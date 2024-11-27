const express = require('express');
const app = express();
const path = require('path');
const mysql = require('mysql');
const moment = require('moment');
const fs = require('fs');
const { url } = require('inspector');
const { arrayBuffer } = require('stream/consumers');
const { monthsShort } = require('moment');
const sql = require('./config.script.js');

const db = sql.connection;

const port = process.env.PORT || 3000;
const server = app.listen(port);
app.use( function(request, result, next){
    result.setHeader("Access-Control-Allow-Origin", '*');
    next();
});
const io = require('socket.io')(server, { cors: { origin: "*", methods: ['GET', 'POST'] }});
var con = mysql.createConnection(db);

const d = new Date();
var datestring = (d.getFullYear() - 1) + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2);

var today = moment(d).format('YYYY-MM-DD');
var months = [];
months[1] = 'Jan';
months[2] = 'Feb';
months[3] = 'Mar';
months[4] = 'Apr';
months[5] = 'May';
months[6] = 'Jun';
months[7] = 'Jul';
months[8] = 'Aug';
months[9] = 'Sep';
months[10] = 'Oct';
months[11] = 'Nov';
months[12] = 'Dec';

let tempResponse = { payroll: 0, acctg: 0, approval: 0 }, payroll, acctg, approval;

io.on('connection', async function(socket) {
    // cash advance notification
    socket.on('getCount', async function(d){
        let _currentAction = d.currentAction;
        var is_fo = _currentAction.indexOf('ca_acctg_fo_notif') !== -1 ? " AND b.company_id = "+d.comp : "";
        
        payroll = await pay_count();
        acctg = await acctg_count(is_fo);
        approval = await approval_count();
        tempResponse = Object.assign({"payroll" : payroll, "acctg": acctg, "approval" : approval});

        io.emit('dataCount', tempResponse);
    });

    socket.on('getData', async function(data){
        var ca = data.type == 'payroll' ? 'Payroll Balance Pending' : data.type == 'acctg' ? 'Accounting Balance Pending' : 'Awaiting Approval';
        var has_comp = data.comp != 0 && data.comp ? "AND company_id = "+data.comp : "";

        payroll = await pay_count();
        acctg = await acctg_count(has_comp);
        approval = await approval_count();
        tempResponse = Object.assign({"payroll" : payroll, "acctg": acctg, "approval" : approval});

        var get_ca = "SELECT a.id as ca_id, UPPER(CONCAT(b.firstname, ' ', b.lastname)) as fullname, a.status as ca_status, b.company_id as comp, UPPER(a.position) as pst, a.reference_no as ca_ref, UPPER(a.department) as dept, FORMAT(a.amt_applied, 2) as amt, a.created_dt as created FROM gcceforms.cash_advance as a left join gccmaster.tblemployees as b on b.id = a.employee WHERE a.status = '"+ca+"' "+has_comp+" AND DATE(a.created_dt) >= '"+datestring+"' AND b.employee_status = 'Active' group by a.id limit 10";
        con.query(get_ca, function(err, result){
            if (err){ throw err;}
            var data = JSON.stringify(result);
            io.emit('data', {'data' : data, tempResponse});
        });
    });
    // cash advance notification

    // hris
    let newResponse = [];
    var sql = "SELECT gccmaster.tblemployees.id,gccmaster.tblemployees.firstname,gccmaster.tblemployees.lastname,gccmaster.tblemployees.suffix,gccmaster.tblemployees.middlename,gccmaster.tblemployees.date_start,gccmaster.tblemployees.pic_filename,gcchris.tblposition.name FROM gccmaster.tblemployees LEFT JOIN gcchris.tblposition ON gcchris.tblposition.id = gccmaster.tblemployees.position WHERE employee_status = 'Active'";

    let getDaysGap = await getDaysGapNearingOnemonth();

    con.query(sql, function(err, result){
        if (err){ throw err;}
        if(result.length > 0){
            for(var i = 0; i < result.length; i++){
                var getDate = new Date(result[i].date_start);
                getDate.setMonth(getDate.getMonth() + 1);
                getDate.setDate(getDate.getDate() - getDaysGap);
                var datetoalert = moment(getDate).format('YYYY-MM-DD');

                var currentImg = 'assets/images/profile/no_image.jpg';
                var userImage = result[i].pic_filename;
                var imagePath = '../../../uploads/files/images/employee_files/empcode_'+result[i].id+'/'+userImage;
                var image = null;

                if (fs.existsSync(imagePath)) {
                    image = 'uploads/files/images/employee_files/empcode_'+result[i].id+'/'+userImage;
                }else{
                    image = currentImg;
                }

                var fullname = displayName(result[i].firstname, result[i].lastname, result[i].middlename, result[i].suffix);
                
                if(datetoalert === today){
                    const data = {id: result[i].id, fullname : fullname.displayName2, position : result[i].name, img: image};
                    newResponse.push(data);
                }
            }

            io.emit('employee_list', newResponse);
        }
    });

    socket.on('getActiveEmployeesOnEachCompany', function(){
        var sql = "SELECT UCASE(IF(company.id IS NULL, `emp`.`company_id`, company.code)) company, COUNT(*) cnt, IF(company.id IS NULL, `emp`.`company_id`, company.code) key_str, (SELECT COUNT(*) FROM gccmaster.tblemployees emp LEFT JOIN `gcchris`.`tblcompanies` `company` ON `emp`.`company_id` = `company`.`id` WHERE IF(company.id IS NULL, emp.company_id, company.code) IS NOT NULL AND emp.work_status IN ('Regular', 'Probationary', 'Service contract', 'No contract') AND (`emp`.`employee_status` = 'Active' AND `emp`.`employee_status` IS NOT NULL)) total, CAST((COUNT(*) / (SELECT COUNT(*) FROM gccmaster.tblemployees emp LEFT JOIN `gcchris`.`tblcompanies` `company` ON `emp`.`company_id` = `company`.`id` WHERE IF(company.id IS NULL, emp.company_id, company.code) IS NOT NULL AND emp.work_status IN ('Regular', 'Probationary', 'Service contract', 'No contract') AND (`emp`.`employee_status` = 'Active' AND `emp`.`employee_status` IS NOT NULL))) * 100 AS DECIMAL(10, 1)) percentage FROM `gccmaster`.`tblemployees` `emp` LEFT JOIN `gcchris`.`tblcompanies` `company` ON `emp`.`company_id` = `company`.`id` WHERE IF(company.id IS NULL, emp.company_id, company.code) IS NOT NULL AND ( emp.employee_status='Active' AND emp.employee_status IS NOT NULL ) GROUP BY IF(company.id IS NULL, `emp`.`company_id`, company.code) ORDER BY COUNT(*) DESC";

        con.query(sql, function(err, result){
            io.emit('getActiveEmp', {data: result});
        });
    });

    socket.on('getRetentionRate', async function(){
        var month = d.getMonth + 1;
        let response = [];
        for(var i = 1; i <= months.length; i++){
            var month_num = months.indexOf(months[i]);

            if(typeof month_num != 'undefined' && month_num > 0){
                if(month_num > month){
                    return false;
                }
                d.setMonth(month_num - 1);
                d.setDate(1);
                var finaldate = moment(d).format('YYYY-MM-DD');

                var _new = await getNewlyHired(month_num);
                var _resign = await getResigned(month_num);
                var totalEmp = await getTotalEmployee();

                const data = {d : months[i], date: finaldate, newly_hired : _new ? parseInt(_new) : 0, resigned : _resign ? parseInt(_resign) : 0};
                response.push(data);
            }
        }
        io.emit('retention_rate', response);
    });

    socket.on('get_gender_demographics', async function(){
        var sql = "SELECT UCASE(emp.gender) gender, emp.gender `key`, COUNT(*) cnt FROM gccmaster.tblemployees emp WHERE employee_status = 'Active' GROUP BY emp.gender";

        con.query(sql, function(err, result){
            var mm = result[0].cnt;
            var fm = result[1].cnt;
            
            io.emit('getGenderDemographics', {data : result, total : mm + fm});
        });
    });

    socket.on('get_personnel_request_summary', async function(){
        var resultset = [];

        var unresolvedPersonnelRequest = await getUnresolvedPersonnelRequest();
        var getUnresolveedPR = {category: "Unresolved Request", value: unresolvedPersonnelRequest, data: {key: 'unresolved'}};
        resultset.push(getUnresolveedPR);

        var overdueUnresolvedPersonnelRequest = await getOverdueUnresolvedPersonnelRequest();
        var getOverdueUnresolvedPR = {category: "Unresolved Overdue", value: overdueUnresolvedPersonnelRequest, data: {key: 'overdue'}};
        resultset.push(getOverdueUnresolvedPR);

        var overdueUnresolvedPersonnelRequest7to30Days = await getOverdueUnresolvedPersonnelRequest7to30Days();
        var overdueUnresolvedPR7to30Days = {category: "Unresolved Overdue, 7 Days", value: overdueUnresolvedPersonnelRequest7to30Days, data: {key: 'overdue-seven-days'}};
        resultset.push(overdueUnresolvedPR7to30Days);

        var overdueUnresolvedPersonnelRequest30to60Days = await getOverdueUnresolvedPersonnelRequest30to60Days();
        var getOverdueUnresolvedPR30to60Days = {category: "Unresolved Overdue, 30 Days", value: overdueUnresolvedPersonnelRequest30to60Days, data: {key: 'overdue-thirty-days'}};
        resultset.push(getOverdueUnresolvedPR30to60Days);

        var overdueUnresolvedPersonnelRequest60to900Days = await getOverdueUnresolvedPersonnelRequest60to900Days();
        var getOverdueUnresolvedPR60to900Days = {category: "Unresolved Overdue, 60 Days", value: overdueUnresolvedPersonnelRequest60to900Days, data: {key: 'overdue-sixty-days'}};
        resultset.push(getOverdueUnresolvedPR60to900Days);

        var overdueUnresolvedPersonnelRequest90Above = await getOverdueUnresolvedPersonnelRequest90Above();
        var getOverdueUnresolvedPR90Above = {category: "Unresolved Overdue, 90 Days", value: overdueUnresolvedPersonnelRequest90Above, data: {key: 'overdue-ninety-days'}};
        resultset.push(getOverdueUnresolvedPR90Above);

        var personnelStillNeeded = await getPersonnelStillNeeded();
        var getPStillNeeded = {category: "Personnel still Needed.", value: personnelStillNeeded, data: {key: 'still-needed'}};
        resultset.push(getPStillNeeded);

        io.emit('getPersonnelRequestSummary', {data: resultset, total : unresolvedPersonnelRequest + overdueUnresolvedPersonnelRequest + overdueUnresolvedPersonnelRequest7to30Days + overdueUnresolvedPersonnelRequest30to60Days + overdueUnresolvedPersonnelRequest60to900Days + overdueUnresolvedPersonnelRequest90Above + personnelStillNeeded});
    });

    socket.on('get_each_employee_status_demographics', async function(){
        let count = await countEmpStatus();
        var sql = "SELECT UCASE(IF(emp.employee_status='Black Listed', 'Blacklisted', emp.employee_status)) , UCASE(IF(emp.employee_status='End of Contract', 'Contract End', emp.employee_status)) employee_status, emp.employee_status `key`, COUNT(*) cnt FROM gccmaster.tblemployees emp WHERE emp.employee_status IS NOT NULL GROUP BY emp.employee_status ORDER BY employee_status DESC";

        con.query(sql, function(err, result){
            io.emit('getEachEmployeeStatusDemographics', {data: result, total:count});
        });
    });
    // hris

    // borrowing
    socket.on('get_all_notif', function(){
        var sql = "SELECT a.id, a.status, b.firstname, b.lastname, b.middlename, b.suffix, c.is_overdue, c.is_returned, c.date_borrowed, IF(pos.`name` IS NULL, a.position, pos.`name`) position, IF(comp.`code` IS NULL, a.company, comp.`code`) company, a.reference_no, a.date_trans, c.asset_name FROM gcceforms.borrowing a LEFT JOIN gccmaster.tblemployees b ON a.borrower = b.id LEFT JOIN gcceforms.borrowing_body c ON a.id = c.borrowing_id LEFT JOIN gcchris.tblcompanies comp ON comp.id = a.company LEFT JOIN gcchris.tblposition pos ON pos.id = a.position WHERE a.status != 'Cancelled' AND c.is_overdue = 0 AND c.is_returned = 0 AND c.date_due <= '"+datestring+"' LIMIT 10";

        con.query(sql, function(err, result){
            io.emit('borrowed', result);
        });
    });
    // borrowing
});

// cash advance 
function pay_count(){
    return new Promise((resolve, reject) => {
        try{
            con.query("SELECT * FROM gcceforms.cash_advance a LEFT JOIN gccmaster.tblemployees b ON b.id = a.employee WHERE a.status = 'Payroll Balance Pending' AND DATE(a.created_dt) >= '"+datestring+"' AND b.employee_status = 'Active' GROUP BY a.id", function(err, result){
                if (err){ throw err;}
                return resolve(result.length);
            });
        }catch(e){
            reject(e);
        }
    });
}

function acctg_count(is_fo = null){
    return new Promise((resolve, reject) => {
        try{
            con.query("SELECT * FROM gcceforms.cash_advance a LEFT JOIN gccmaster.tblemployees b ON b.id = a.employee WHERE a.status = 'Accounting Balance Pending' "+is_fo+" AND DATE(a.created_dt) >= '"+datestring+"' AND b.employee_status = 'Active' GROUP BY a.id", function(err, result){
                if (err){ throw err;}
                return resolve(result.length);
            });
        }catch(e){
            reject(e);
        }
    });
}

function approval_count(){
    return new Promise((resolve, reject) => {
        try{
            con.query("SELECT * FROM gcceforms.cash_advance a LEFT JOIN gccmaster.tblemployees b ON b.id = a.employee WHERE a.status = 'Awaiting Approval' AND DATE(a.created_dt) >= '"+datestring+"' AND b.employee_status = 'Active' GROUP BY a.id", function(err, result){
                if (err){ throw err;}
                return resolve(result.length);
            });
        }catch(e){
            reject(e);
        }
    });
}
// cash advance 

// hris
function countEmpStatus() {
    return new Promise((resolve, reject) => {
        try{
            var sql = "SELECT COUNT(*) cnt FROM gccmaster.tblemployees emp WHERE emp.employee_status IS NOT NULL";

            con.query(sql, function(err, result){
                return resolve(result[0].cnt);
            });
        }catch(e){
            reject(e);
        }
    });
}

function displayName(firstname, lastname, middlename = null, suffix = null){
    var fn = firstname.toUpperCase();
    var ln = lastname.toUpperCase();
    var mn = middlename.toUpperCase();
    var sf = suffix.toUpperCase();

    var nSuffix = '';
    var nMiddleName = '';

    if(sf !== '' && (sf !== 'N/A' && sf !== 'NONE')){
        nSuffix = sf;
    }

    if (mn !== "" && (mn !== "N/A" && mn !== "NONE")) {
        nMiddleName = mn;
    }

    var displayName1 = "";
    var displayName2 = "";

    nMiddleName = nMiddleName.trim();
    nMiddleName = nMiddleName.substr(0, 1);
    nMiddleName = (nMiddleName) ? nMiddleName+"." : "";

    if (nMiddleName && nSuffix) {
        displayName1 = ln + ", " + fn + " " + nMiddleName + " " + nSuffix;
        displayName2 = fn + " " + nMiddleName + " " + ln + " " + nSuffix;
    } else if (nSuffix) {
        displayName1 = ln + ", " + fn + " " + nSuffix;
        displayName2 = fn + " " + ln + " " + nSuffix;
    } else if (nMiddleName) {
        displayName1 = ln + ", " + fn + " " + nMiddleName;
        displayName2 = fn + " " + nMiddleName + " " + ln;
    } else {
        displayName1 = ln + ", " + fn;
        displayName2 = fn + " " + ln;
    }

    displayName1 = displayName1;
    displayName2 = displayName2;

    var data = [];
    data['displayName1'] = displayName1;
    data['displayName2'] = displayName2;

    return data;
}

function getDaysGapNearingOnemonth(){
    return new Promise((resolve, reject) => {
        try{
            con.query("SELECT * FROM gcchris.tbldaysgapsetup ORDER BY id DESC LIMIT 1", function(err, result){
                if (err){ throw err;}
                return resolve(result[0].days);
            });
        }catch(e){
            reject(e);
        }
    });
}

function getTotalEmployee(){
    return new Promise((resolve, reject) => {
        try{
            var sql = "SELECT COUNT(*) total FROM gccmaster.tblemployees emp WHERE YEAR(emp.date_start) < YEAR(CURDATE() - 1) AND emp.date_end = '0000-00-00' AND emp.employee_status='Active'";
            con.query(sql, function(err, result){
                if (err){ throw err;}
                result.forEach( function(element){
                    return resolve(element.total);
                });
            });
        }catch(e){
            reject(e);
        }
    });
}

function getNewlyHired(month){
    return new Promise((resolve, reject) => {
        try{
            var sql = "SELECT COUNT(*) newly_hired, DATE_FORMAT(emp_new.date_start, '%b') `d`, DATE_FORMAT(emp_new.date_start,'%Y-%m-01') `date`, DATE_SUB(DATE_FORMAT(emp_new.date_start,'%Y-%m-01'), INTERVAL 1 MONTH) `previousDate` FROM gccmaster.tblemployees emp_new WHERE emp_new.date_start IS NOT NULL AND emp_new.date_start != '0000-00-00' AND YEAR(emp_new.date_start)=YEAR(CURDATE()) AND emp_new.employee_status='Active' AND MONTH(emp_new.date_start) = "+month+" GROUP BY YEAR(emp_new.date_start), MONTH(emp_new.date_start)";

            con.query(sql, function(err, result){
                if (err){ throw err;}
                if(typeof result[0] !== 'undefined'){
                    const count = result[0].newly_hired;
                    return resolve(count);
                }else{
                    return resolve(false);
                }
            });
        }catch(e){
            reject(e);
        }
    });
}

function getResigned(month){
    return new Promise((resolve, reject) => {
        try{
            var sql = "SELECT COUNT(*) resigned, DATE_FORMAT(emp_resigned.date_end, '%b') `d`, DATE_FORMAT(emp_resigned.date_end,'%Y-%m-01') `date`, DATE_SUB(DATE_FORMAT(emp_resigned.date_end,'%Y-%m-01'), INTERVAL 1 MONTH) `previousDate` FROM gccmaster.tblemployees emp_resigned WHERE emp_resigned.date_end IS NOT NULL AND emp_resigned.date_end != '0000-00-00' AND YEAR(emp_resigned.date_end)=YEAR(CURDATE()) AND ((emp_resigned.employee_status='RESIGN' OR emp_resigned.employee_status='RESIGNED') OR (emp_resigned.work_status = 'RESIGN' OR emp_resigned.work_status = 'RESIGNED')) AND MONTH(emp_resigned.date_end) = "+month+" GROUP BY YEAR(emp_resigned.date_end), MONTH(emp_resigned.date_end)";

            con.query(sql, function(err, result){
                if (err){ throw err;}
                if(typeof result[0] !== 'undefined'){
                    const count = result[0].resigned;
                    return resolve(count);
                }else{
                    return resolve(false);
                }
            });
        }catch(e){
            reject(e);
        }
    });
}

function getUnresolvedPersonnelRequest(){
    return new Promise((resolve, reject) => {
        try{
            var sql = "SELECT * FROM gcchris.tbapplication WHERE status = 'Ongoing' OR status = 'forApproval'";

            con.query(sql, function(err, result){
                return resolve(result.length);
            });
        }catch(e){
            reject(e);
        }
    });
}

function getOverdueUnresolvedPersonnelRequest(){
    return new Promise((resolve, reject) => {
        try{
            var sql = "SELECT * FROM gcchris.tbapplication WHERE status = 'Ongoing' AND is_archived = 0 AND need_dt < CURDATE()";

            con.query(sql, function(err, result){
                return resolve(result.length);
            });
        }catch(e){
            reject(e);
        }
    });
}

function getOverdueUnresolvedPersonnelRequest7to30Days(){
    return new Promise((resolve, reject) => {
        try{
            var sql = "SELECT * FROM gcchris.tbapplication WHERE status = 'Ongoing' AND is_archived = 0 AND DATEDIFF(CURDATE(), need_dt) >= 7 AND DATEDIFF(CURDATE(), need_dt) < 30";

            con.query(sql, function(err, result){
                return resolve(result.length);
            });
        }catch(e){
            reject(e);
        }
    });
}

function getOverdueUnresolvedPersonnelRequest30to60Days(){
    return new Promise((resolve, reject) => {
        try{
            var sql = "SELECT * FROM gcchris.tbapplication WHERE status = 'Ongoing' AND is_archived = 0 AND DATEDIFF(CURDATE(), need_dt) >= 30 AND DATEDIFF(CURDATE(), need_dt) < 60";

            con.query(sql, function(err, result){
                return resolve(result.length);
            });
        }catch(e){
            reject(e);
        }
    })
}

function getOverdueUnresolvedPersonnelRequest60to900Days(){
    return new Promise((resolve, reject) => {
        try{
            var sql = "SELECT * FROM gcchris.tbapplication WHERE status = 'Ongoing' AND is_archived = 0 AND DATEDIFF(CURDATE(), need_dt) >= 60 AND DATEDIFF(CURDATE(), need_dt) < 90";

            con.query(sql, function(err, result){
                return resolve(result.length);
            });
        }catch(e){
            reject(e);
        }
    })
}

function getOverdueUnresolvedPersonnelRequest90Above(){
    return new Promise((resolve, reject) => {
        try{
            var sql = "SELECT * FROM gcchris.tbapplication WHERE status = 'Ongoing' AND is_archived = 0 AND DATEDIFF(CURDATE(), need_dt) > 90";

            con.query(sql, function(err, result){
                return resolve(result.length);
            });
        }catch(e){
            reject(e);
        }
    })
}

function getPersonnelStillNeeded(){
    return new Promise((resolve, reject) => {
        try{
            var sql = "SELECT * FROM gcchris.tbapplication WHERE status = 'Ongoing'";

            con.query(sql, function(err, result){
                return resolve(result.length);
            });
        }catch(e){
            reject(e);
        }
    })
}
// hris
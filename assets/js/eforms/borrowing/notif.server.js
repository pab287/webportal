const express = require('express');
const app = express();
const path = require('path');
const mysql = require('mysql');

const mysqlConfig = {
    host: 'localhost', 
    database: 'gccmaster',
    user: 'root',
    password: 'admin',
};

const port = process.env.PORT || 3000;
const server = app.listen(port);
app.use( function(request, result, next){
    result.setHeader("Access-Control-Allow-Origin", '*');
    next();
});
const io = require('socket.io')(server, { cors: { origin: "*", methods: ['GET', 'POST'] }});
var con = mysql.createConnection(mysqlConfig);

const d = new Date();
var datestring = (d.getFullYear() - 1) + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2);

io.on('connection', socket => {
    socket.on('get_all_notif', function(){
        var sql = "SELECT a.id, a.status, b.firstname, b.lastname, b.middlename, b.suffix, c.is_overdue, c.is_returned, c.date_borrowed, IF(pos.`name` IS NULL, a.position, pos.`name`) position, IF(comp.`code` IS NULL, a.company, comp.`code`) company, a.reference_no, a.date_trans, c.asset_name FROM gcceforms.borrowing a LEFT JOIN gccmaster.tblemployees b ON a.borrower = b.id LEFT JOIN gcceforms.borrowing_body c ON a.id = c.borrowing_id LEFT JOIN gcchris.tblcompanies comp ON comp.id = a.company LEFT JOIN gcchris.tblposition pos ON pos.id = a.position WHERE a.status != 'Cancelled' AND c.is_overdue = 0 AND c.is_returned = 0 AND c.date_due <= '"+datestring+"' LIMIT 10";

        con.query(sql, function(err, result){
            io.emit('borrowed', result);
        });
    });
});
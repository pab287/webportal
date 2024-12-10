let selectedFields = [];
const modalContainer = $('.document-modal-container');
const dropdown = $(".m-dropdown__toggle.export-as");

let template = null;
let templateId = null;
let dtReport = null;
let type ="";

const template_select_option = {
    width: '100%',
    placeholder: "Load Template",
    ajax: {
        url: baseUrl('hris/reports/get_field_templates'),
        dataType: 'JSON',
        processResults: function (data) {
            return data;
        }
    },
    allowClear: true
};

const dbFieldEl = '' +
    '<option value="CAST(emp.idno AS CHAR) idno">ID No</option>' +
    '<option value="emp.biometricno">Biometric No</option>' +
    '<option value="UPPER(IF(company.id IS NULL, emp.company_id, company.code)) _company">Company</option>' +
    '<option value="UPPER(IF(department.id IS NULL, emp.department_id, department.description)) _department">Department Desc</option>' +
    '<option value="UPPER(IF(department.id IS NULL, emp.department_id, department.code)) code">Department Code</option>' +
    '<option value="UPPER(IF(position.id IS NULL, emp.position, position.name)) _position">Position</option>' +
    // '<option value="emp.lastname">Lastname</option>' +
    // '<option value="emp.firstname">Firstname</option>' +
    // '<option value="emp.middlename">Middlename</option>' +
    '<option value="UPPER(emp.lastname) lastname">Lastname</option>' +
    '<option value="UPPER(emp.firstname) firstname">Firstname</option>' +
    '<option value="UPPER(emp.middlename) middlename">Middlename</option>' +
    '<option value="emp.suffix">Suffix</option>' +
    '<option value="TIMESTAMPDIFF(YEAR, emp.bday, CURDATE()) age">Age</option>' +
    '<option value="emp.curr_addr">Current Address</option>' +
    '<option value="emp.prov_addr">Permanent Address</option>' +
    '<option value="emp.citizenship">Citizenship</option>' +
    '<option value="emp.religion">Religion</option>' +
    '<option value="emp.languages">Languages</option>' +
    '<option value="emp.gender">Gender</option>' +
    '<option value="emp.civil_stat">Civil Status</option>' +
    '<option value="emp.bday">Date of Birth</option>' +
    '<option value="emp.birthplace">Place of Birth</option>' +
    '<option value="emp.bloodtype">Blood Type</option>' +
    '<option value="emp.height">Height</option>' +
    '<option value="emp.weight">Weight</option>' +
    '<option value="emp.hair_color">Hair Color</option>' +
    '<option value="emp.complexion">Complexion</option>' +
    '<option value="emp.tel_no">Telephone #</option>' +
    '<option value="emp.mobile_no">Mobile #</option>' +
    '<option value="emp.email">Email Address</option>' +
    '<option value="emp.tin_no">TIN #</option>' +
    '<option value="emp.tax_status">Tax Status</option>' +
    '<option value="emp.phealth_no">Philhealth #</option>' +
    '<option value="emp.pagibig_no">HDMF #</option>' +
    '<option value="emp.sss_no">SSS/UMID #</option>' +
    '<option value="emp.fat_name">Father\'s Name</option>' +
    '<option value="emp.fat_addr">Father\'s Address</option>' +
    '<option value="emp.fat_company">Father\'s Company</option>' +
    '<option value="emp.fat_occupation">Father\'s Occupation</option>' +
    '<option value="emp.fat_contact">Father\'s Contact #</option>' +
    '<option value="emp.mot_name">Mother\'s Name</option>' +
    '<option value="emp.mot_addr">Mother\'s Address</option>' +
    '<option value="emp.mot_company">Mother\'s Company</option>' +
    '<option value="emp.mot_occupation">Mother\'s Occupation</option>' +
    '<option value="emp.mot_contact">Mother\'s Contact #</option>' +
    '<option value="emp.spo_name">Spouse\'s Name</option>' +
    '<option value="emp.spo_addr">Spouse\'s Address</option>' +
    '<option value="emp.spo_company">Spouse\'s Company</option>' +
    '<option value="emp.spo_occupation">Spouse\'s Occupation</option>' +
    '<option value="emp.spo_contact">Spouse\'s Contact #</option>' +
    '<option value="emp.partners_name">Partner\'s Name</option>' +
    '<option value="emp.partners_addr">Partner\'s Address</option>' +
    '<option value="emp.partners_company">Partner\'s Company</option>' +
    '<option value="emp.partners_occupation">Partner\'s Occupation</option>' +
    '<option value="emp.partners_contact">Partner\'s Contact #</option>' +
    '<option value="emp.emer_name">Emergency Contact Person</option>' +
    '<option value="emp.emer_contact">Emergency Contact #</option>' +
    '<option value="emp.emer_addr">Emergency Contact Address</option>' +
    '<option value="emp.work_status">Work Status</option>' +
    '<option value="emp.work_mode">Work Mode</option>' +
    '<option value="emp.payroll_type">Payroll Type</option>' +
    '<option value="emp.atm_info">ATM Account #</option>' +
    '<option value="emp.bank_name">Bank Name</option>' +
    '<option value="emp.date_start">Date Hired</option>' +
    '<option value="emp.date_regular">Date Regularized</option>' +
    '<option value="emp.date_end">Date Separated</option>' +
    '<option value="emp.date_end_prob">Date Probation End</option>' +
    '<option value="emp.latitude">Longitude</option>' +
    '<option value="emp.longitude">Latitude</option>' +
    '<option value="emp.level">Level</option>' +
    '<option value="emp.employee_status">Employee Status</option>' +
    '<option value="educ.educ_degree">Educational Degree</option>' +
    '<option value="LOWER(payout_schedule.name) payout_schedule">Payout Schedule</option>' +
    '<option value="CAST(REPLACE(salaries.sal_rate,TRIM(\',\'),\'\') AS DECIMAL(10,2)) sal_rate">Salary Rate</option>' +
    '<option value="sal_remarks">Salary Remarks</option>' +
    '<option value="sal_date">Salary Effective</option>' +
    '<option value="IF(personnel.id IS NULL OR location.location_name IS NULL, \'No Station Assigned\', GROUP_CONCAT(DISTINCT location.location_name)) station">Station</option>' +
    '';


const dbSortFieldEl = '' +
    '<option value="emp.idno">ID No</option>' +
    '<option value="emp.biometricno">Biometric No</option>' +
    '<option value="IF(company.id IS NULL, emp.company_id, company.code)">Company</option>' +
    '<option value="IF(department.id IS NULL, emp.department_id, department.description)">Department Dsc</option>' +
    '<option value="IF(department.id IS NULL, emp.department_id, department.code)">Department Code</option>' +
    '<option value="IF(position.id IS NULL, emp.position, position.name)">Position</option>' +
    '<option value="emp.lastname">Lastname</option>' +
    '<option value="emp.firstname">Firstname</option>' +
    '<option value="emp.middlename">Middlename</option>' +
    '<option value="emp.suffix">Suffix</option>' +
    '<option value="TIMESTAMPDIFF(YEAR, emp.bday, CURDATE())">Age</option>' +
    '<option value="emp.curr_addr">Current Address</option>' +
    '<option value="emp.prov_addr">Permanent Address</option>' +
    '<option value="emp.citizenship">Citizenship</option>' +
    '<option value="emp.religion">Religion</option>' +
    '<option value="emp.languages">Languages</option>' +
    '<option value="emp.gender">Gender</option>' +
    '<option value="emp.civil_stat">Civil Status</option>' +
    '<option value="emp.bday">Date of Birth</option>' +
    '<option value="emp.birthplace">Place of Birth</option>' +
    '<option value="emp.bloodtype">Blood Type</option>' +
    '<option value="emp.height">Height</option>' +
    '<option value="emp.weight">Weight</option>' +
    '<option value="emp.hair_color">Hair Color</option>' +
    '<option value="emp.complexion">Complexion</option>' +
    '<option value="emp.tel_no">Telephone #</option>' +
    '<option value="emp.mobile_no">Mobile #</option>' +
    '<option value="emp.email">Email Address</option>' +
    '<option value="emp.tin_no">TIN #</option>' +
    '<option value="emp.tax_status">Tax Status</option>' +
    '<option value="emp.phealth_no">Philhealth #</option>' +
    '<option value="emp.pagibig_no">HDMF #</option>' +
    '<option value="emp.sss_no">SSS/UMID #</option>' +
    '<option value="emp.fat_name">Father\'s Name</option>' +
    '<option value="emp.fat_addr">Father\'s Address</option>' +
    '<option value="emp.fat_company">Father\'s Company</option>' +
    '<option value="emp.fat_occupation">Father\'s Occupation</option>' +
    '<option value="emp.fat_contact">Father\'s Contact #</option>' +
    '<option value="emp.mot_name">Mother\'s Name</option>' +
    '<option value="emp.mot_addr">Mother\'s Address</option>' +
    '<option value="emp.mot_company">Mother\'s Company</option>' +
    '<option value="emp.mot_occupation">Mother\'s Occupation</option>' +
    '<option value="emp.mot_contact">Mother\'s Contact #</option>' +
    '<option value="emp.spo_name">Spouse\'s Name</option>' +
    '<option value="emp.spo_addr">Spouse\'s Address</option>' +
    '<option value="emp.spo_company">Spouse\'s Company</option>' +
    '<option value="emp.spo_occupation">Spouse\'s Occupation</option>' +
    '<option value="emp.spo_contact">Spouse\'s Contact #</option>' +
    '<option value="emp.partners_name">Partner\'s Name</option>' +
    '<option value="emp.partners_addr">Partner\'s Address</option>' +
    '<option value="emp.partners_company">Partner\'s Company</option>' +
    '<option value="emp.partners_occupation">Partner\'s Occupation</option>' +
    '<option value="emp.partners_contact">Partner\'s Contact #</option>' +
    '<option value="emp.emer_name">Emergency Contact Person</option>' +
    '<option value="emp.emer_contact">Emergency Contact #</option>' +
    '<option value="emp.emer_addr">Emergency Contact Address</option>' +
    '<option value="emp.work_status">Work Status</option>' +
    '<option value="emp.work_mode">Work Mode</option>' +
    '<option value="emp.payroll_type">Payroll Type</option>' +
    '<option value="emp.atm_info">ATM Account #</option>' +
    '<option value="emp.bank_name">Bank Name</option>' +
    '<option value="emp.date_start">Date Hired</option>' +
    '<option value="emp.date_regular">Date Regularized</option>' +
    '<option value="emp.date_end">Date Separated</option>' +
    '<option value="emp.date_end_prob">Date Probation End</option>' +
    '<option value="emp.latitude">Longitude</option>' +
    '<option value="emp.longitude">Latitude</option>' +
    '<option value="emp.level">Level</option>' +
    '<option value="emp.employee_status">Employee Status</option>' +
    '<option value="educ.educ_degree">Educational Degree</option>' +
    '<option value="LOWER(payout_schedule.name)">Payout Schedule</option>' +
    '<option value="CAST(REPLACE(salaries.sal_rate,TRIM(\',\'),\'\') AS DECIMAL(10,2))">Salary Rate</option>' +
    '<option value="IF(personnel.id IS NULL OR location.location_name IS NULL, \'No Station Assigned\', GROUP_CONCAT(DISTINCT location.location_name))">Station</option>' +
    '';

$('#field')
    .append(dbFieldEl)
    .select2({
        width: '100%'
    })
    .on('select2:select', function (e) {
        const data = e.params.data;
        selectedFields.push({ id: data.id, text: data.text });
        const strId = data.text.replace(' ', '-');

        const el = '' +
            '<div class="m-widget4__item" id="' + strId + '">' +
            '    <div class="m-widget4__info pl-0">' +
            '           <span class="m-widget4__text">' +
            '           ' + (data.text).toUpperCase() +
            '           </span>' +
            '    </div>' +
            '    <div class="m-widget4__ext">' +
            '           <a href="#" onclick="removeField(\'' + strId + '\',\'' + data.id + '\')" ' +
            '              class="m-widget4__icon">' +
            '                   <i class="la la-remove"></i>' +
            '           </a>' +
            '    </div>' +
            '</div>';
        $(".empty-field-selection").html("");
        $(".m-widget4").append(el);

        selectItem(e.target, e.params.data.id);
    })
    .on('select2:unselect', function (e) {
        const data = e.params.data;
        const strId = data.text.replace(' ', '-');

        const index = selectedFields.findIndex((item) => item.id === data.id);
        selectedFields.splice(index, 1);

        $("#" + strId).remove();
        if (selectedFields.length <= 0) {
            $(".empty-field-selection").html("<p class='lead text-muted mt-3'>Please select at least one field.</p>");
        }
    });

$('#order_field')
    .append(dbSortFieldEl)
    .select2({
        width: '100%',
        placeholder: "Select Order Field",
        allowClear: true
    });

$('#order_by').select2({
    width: '100%',
    placeholder: "Order by",
    allowClear: true
});

$('#template')
    .select2(template_select_option)
    .on('select2:select', function (e) {
        templateId = e.params.data.id;
        $.ajax({
            url: baseUrl('hris/reports/get_template_body/' + templateId),
            dataType: 'JSON',
            type: 'GET',
            success: function (response) {
                template = response;
            }
        })
    });

$(document).ready(function () {
    
    $('#query-builder')
        .queryBuilder({
            'bt-tooltip-errors': { delay: 100 },
            filters: [
                { id: 'idno', label: 'ID #', type: 'string' },
                { id: 'emp.biometricno', label: 'Biometric #', type: 'string' },
                { id: 'IF(company.id IS NULL, emp.company_id, company.code)', label: 'Company', type: 'string' },
                {
                    id: 'IF(department.id IS NULL, emp.department_id, department.description)',
                    label: 'Department Desc',
                    type: 'string'
                },
                {
                    id: 'IF(department.id IS NULL, emp.department_id, department.code)',
                    label: 'Department Code',
                    type: 'string'
                },
                { id: 'IF(position.id IS NULL, emp.position, position.name)', label: 'Position', type: 'string' },
                { id: 'lastname', label: 'Lastname', type: 'string' },
                { id: 'firstname', label: 'Firstname', type: 'string' },
                { id: 'middlename', label: 'Middlename', type: 'string' },
                { id: 'suffix', label: 'Suffix', type: 'string' },
                { id: 'curr_addr', label: 'Current Address', type: 'string' },
                { id: 'prov_addr', label: 'Permanent Address', type: 'string' },
                { id: 'citizenship', label: 'Citizenship', type: 'string' },
                { id: 'religion', label: 'Religion', type: 'string' },
                { id: 'languages', label: 'Languages', type: 'string' },
                { id: 'gender', label: 'Gender', type: 'string' },
                { id: 'civil_stat', label: 'Civil Status', type: 'string' },
                { id: 'bday', label: 'Date of Birth', type: 'string' },
                { id: 'birthplace', label: 'Place of Birth', type: 'string' },
                { id: 'bloodtype', label: 'Blood Type', type: 'string' },
                { id: 'height', label: 'Height', type: 'string' },
                { id: 'weight', label: 'Weight', type: 'string' },
                { id: 'hair_color', label: 'Hair Color', type: 'string' },
                { id: 'complexion', label: 'Complexion', type: 'string' },
                { id: 'tel_no', label: 'Telephone #', type: 'string' },
                { id: 'mobile_no', label: 'Mobile #', type: 'string' },
                { id: 'email', label: 'Email Address', type: 'string' },
                { id: 'tin_no', label: 'TIN #', type: 'string' },
                { id: 'tax_status', label: 'Tax Status', type: 'string' },
                { id: 'phealth_no', label: 'Philhealth #', type: 'string' },
                { id: 'pagibig_no', label: 'HDMF #', type: 'string' },
                { id: 'sss_no', label: 'SSS/UMID #', type: 'string' },
                { id: 'fat_name', label: 'Father\'s Name', type: 'string' },
                { id: 'fat_addr', label: 'Father\'s Address', type: 'string' },
                { id: 'fat_company', label: 'Father\'s Company', type: 'string' },
                { id: 'fat_occupation', label: 'Father\'s Occupation', type: 'string' },
                { id: 'fat_contact', label: 'Father\'s Contact #', type: 'string' },
                { id: 'mot_name', label: 'Mother\'s Name', type: 'string' },
                { id: 'mot_addr', label: 'Mother\'s Address', type: 'string' },
                { id: 'mot_company', label: 'Mother\'s Company', type: 'string' },
                { id: 'mot_occupation', label: 'Mother\'s Occupation', type: 'string' },
                { id: 'mot_contact', label: 'Mother\'s Contact #', type: 'string' },
                { id: 'spo_name', label: 'Spouse\'s Name', type: 'string' },
                { id: 'spo_addr', label: 'Spouse\'s Address', type: 'string' },
                { id: 'spo_company', label: 'Spouse\'s Company', type: 'string' },
                { id: 'spo_occupation', label: 'Spouse\'s Occupation', type: 'string' },
                { id: 'spo_contact', label: 'Spouse\'s Contact #', type: 'string' },
                { id: 'partners_name', label: 'Partner\'s Name', type: 'string' },
                { id: 'partners_addr', label: 'Partner\'s Address', type: 'string' },
                { id: 'partners_company', label: 'Partner\'s Company', type: 'string' },
                { id: 'partners_occupation', label: 'Partner\'s Occupation', type: 'string' },
                { id: 'partners_contact', label: 'Partner\'s Contact #', type: 'string' },
                { id: 'emer_name', label: 'Emergency Contact Person', type: 'string' },
                { id: 'emer_contact', label: 'Emergency Contact #', type: 'string' },
                { id: 'emer_addr', label: 'Emergency Contact Address', type: 'string' },
                { id: 'work_status', label: 'Work Status', type: 'string' },
                { id: 'work_mode', label: 'Work Mode', type: 'string' },
                { id: 'payroll_type', label: 'Payroll Type', type: 'string' },
                { id: 'atm_info', label: 'ATM Account #', type: 'string' },
                { id: 'bank_name', label: 'Bank Name', type: 'string' },
                {
                    id: 'date_start',
                    label: 'Date Hired',
                    type: 'date',
                    plugin: 'datepicker',
                    plugin_config: { format: 'yyyy-mm-dd' }
                },
                {
                    id: 'date_regular',
                    label: 'Date Regularized',
                    type: 'date',
                    plugin: 'datepicker',
                    plugin_config: { format: 'yyyy-mm-dd' }
                },
                {
                    id: 'date_end',
                    label: 'Date Separated',
                    type: 'date',
                    plugin: 'datepicker',
                    plugin_config: { format: 'yyyy-mm-dd' }
                },
                {
                    id: 'date_end_prob',
                    label: 'Date Probation End',
                    type: 'date',
                    plugin: 'datepicker',
                    plugin_config: { format: 'yyyy-mm-dd' }
                },
                { id: 'latitude', label: 'Longitude', type: 'string' },
                { id: 'longitude', label: 'Latitude', type: 'string' },
                { id: 'level', label: 'Level', type: 'string' },
                { id: 'employee_status', label: 'Employee Status', type: 'string' },
                { id: 'educ_degree', label: 'Educational Degree', type: 'string' },
                { id: 'LOWER(payout_schedule.name)', label: 'Payout Schedule', type: 'string' },
                {
                    id: 'CAST(REPLACE(salaries.sal_rate,\',\',\'\') AS DECIMAL(10,2))',
                    label: 'Salary Rate',
                    type: 'double'
                },
                {
                    id: "IF(location.id IS NULL, 'No Location Assigned', location.location_name)", label: "Station", type: "string"
                }
            ]
        });

    $("#query-builder").find("button").addClass("mb-1");
    $("#query-builder").find("select2-query-builder_rule_0_filter-1e-container").addClass("col-xs-12 col-sm-12");
    initSelect2OnRules();

    $('#query-builder')
        .on('click', '.btn-success', function () {
            initSelect2OnRules();
        });

    function initSelect2OnRules() {
        $(".rule-filter-container").find('select').select2()
            .on('select2:select', function (e) {
                $(".rule-operator-container").find('select').select2();
            });
    }
});

function generateReport(form) {
    if ($(form).isValid()) {
        $('.empty-table-message').html("");
        let criteria = $("#query-builder").queryBuilder('getSQL');
        criteria = criteria ? criteria.sql : null;
        console.log(criteria);
        const table = $("#table-generated-report");
        if ($.fn.DataTable.isDataTable(table)) {
            table.DataTable().destroy();
        }
        const thead = table.find('thead');
        const tbody = table.find('tbody');
        if (tbody.children().length > 0) {
            tbody.empty();
        }
        let th = "";
        selectedFields.forEach((field) => {
            th += "<th>" + field.text + "</th>";
        });
        const fields = $('select[name="field[]"]').val();
        const order_field = $('select[name=order_field]').val();
        const order_by = $('select[name="order_by"]').val();
        thead.html("").append("<tr>" + th + "</tr>");
        initDatatable(selectedFields, fields, order_field, order_by, criteria);
    }
}

function removeField(title, id) {
    const values = $('#field').val();
    let i;
    if (values) {
        i = values.indexOf(id);
        if (i >= 0) {
            values.splice(i, 1);
            selectedFields.splice(i, 1);
            $('#field').val(values).change();
        }
    }

    if (selectedFields.length <= 0) {
        $(".empty-field-selection")
            .html("<p class='lead text-muted mt-3'>Please select at least one field.</p>");
    }

    $('#' + title).remove();
}

function selectItem(target, id) { // refactored this a bit, don't pay attention to this being a function
    var option = $(target).children('[value="' + id + '"]');
    option.detach();
    $(target).append(option).change();
}

function initDatatable(columns, fields, order_field, order_by, criteria) {
    let _columns = [];
    columns.forEach((column) => {
        let _column = null;
        const _columnIdNo = column.id.indexOf('CAST(emp.idno AS CHAR)') !== -1;
        const _columnCompany = column.id.indexOf('UPPER(IF(company.id IS NULL, emp.company_id, company.code))') !== -1;
        const _columnDepartment = column.id.indexOf('UPPER(IF(department.id IS NULL, emp.department_id, department.description))') !== -1;
        const _columnDepartmentCode = column.id.indexOf('UPPER(IF(department.id IS NULL, emp.department_id, department.code))') !== -1;
        const _columnPosition = column.id.indexOf('UPPER(IF(position.id IS NULL, emp.position, position.name))') !== -1;
        const _columnSalaryRate = column.id.indexOf('CAST(REPLACE(salaries.sal_rate,TRIM(\',\'),\'\') AS DECIMAL(10,2))') !== -1;
        const _columnEducDegree = column.id.indexOf('educ.educ_degree') !== -1;
        const _columnPayoutSchedule = column.id.indexOf('LOWER(payout_schedule.name)') !== -1;
        const _columnFirstname = column.id.indexOf('UPPER(emp.firstname)') !== -1;
        const _columnLastname = column.id.indexOf('UPPER(emp.lastname)') !== -1;
        const _columnMiddlename = column.id.indexOf('UPPER(emp.middlename)') !== -1;
        const _columnStation = column.id.indexOf('IF(personnel.id IS NULL OR location.location_name IS NULL, \'No Station Assigned\', GROUP_CONCAT(DISTINCT location.location_name)) station') !== -1;
        const _columnAge = column.id.indexOf('TIMESTAMPDIFF(YEAR, emp.bday, CURDATE()) age') !== -1;

        if (_columnCompany) {
            _column = column.id.replace('UPPER(IF(company.id IS NULL, emp.company_id, company.code)) ', '');
        } else if (_columnDepartment) {
            _column = column.id.replace('UPPER(IF(department.id IS NULL, emp.department_id, department.description)) ', '');
        } else if (_columnDepartmentCode) {
            _column = column.id.replace('UPPER(IF(department.id IS NULL, emp.department_id, department.code)) ', '');
        }else if (_columnPosition) {
            _column = column.id.replace('UPPER(IF(position.id IS NULL, emp.position, position.name)) ', '');
        } else if (_columnSalaryRate) {
            _column = column.id.replace('CAST(REPLACE(salaries.sal_rate,TRIM(\',\'),\'\') AS DECIMAL(10,2)) ', '');
        } else if (_columnIdNo) {
            _column = column.id.replace('CAST(emp.idno AS CHAR) ', '');
        } else if (_columnEducDegree) {
            _column = column.id.replace("educ.", "");
        }else if (_columnPayoutSchedule) {
            _column = column.id.replace("LOWER(payout_schedule.name) ", "");
        } else if (_columnFirstname){
            _column = column.id.replace('UPPER(emp.firstname) ', '');
        } else if (_columnLastname){
            _column = column.id.replace('UPPER(emp.lastname) ', '');
        } else if (_columnMiddlename){
            _column = column.id.replace('UPPER(emp.middlename) ', '');
        } else if(_columnStation){
            _column = column.id.replace('IF(personnel.id IS NULL OR location.location_name IS NULL, \'No Station Assigned\', GROUP_CONCAT(DISTINCT location.location_name)) station', 'station');
        } else if(_columnAge){
            _column = column.id.replace('TIMESTAMPDIFF(YEAR, emp.bday, CURDATE()) age', 'age');
        } else {
            _column = column.id.replace("emp.", "");
        }

        if (_column === "sal_rate") {
            _columns.push({
                data: _column,
                orderable: false,
                render: function (data) {
                    return data ? data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : null;
                },
                className: 'text-right'
            });
        } else {
            _columns.push({ data: _column, orderable: false });
        }
    });

    dtReport = $('#table-generated-report')
        .DataTable({
            dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 exportDropdown'><'col-xl-6 col-lg-6 col-md-6 col-sm-12'>>" +
                "<'row'<'col-12'rt>>" +
                "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'li><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
            destroy: true,
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'GCC HRIS - EMPLOYEE REPORT',
                    action: function (e, dt, node, config) {
                        const self = this;
                        getExportData(e, dt, node, config, self, baseUrl('hris/reports/generate_employee_report/1'), 'excelHtml5')
                            .then(() => {
                                dropdown.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                            });
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'GCC HRIS - EMPLOYEE REPORT',
                    action: function (e, dt, node, config) {
                        const self = this;
                        getExportData(e, dt, node, config, self, baseUrl('hris/reports/generate_employee_report/1'), 'pdfHtml5')
                            .then(() => {
                                dropdown.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                            });
                    }
                },
                {
                    extend: 'print',
                    title: 'GCC HRIS - EMPLOYEE REPORT',
                    action: function (e, dt, node, config) {
                        const self = this;
                        getExportData(e, dt, node, config, self, baseUrl('hris/reports/generate_employee_report/1'), 'print')
                            .then(() => {
                                dropdown.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                            });
                    }
                }
            ],
            serverSide: true,
            processing: true,
            searching: false,
            ordering: false,
            ajax: {
                url: baseUrl('hris/reports/generate_employee_report'),
                type: 'post',
                dataType: 'json',
                data: function (d) {
                    d.csrf_token = _csrf_hash;
                    d.fields = fields;
                    d.order_field = order_field;
                    d.order_by = order_by;
                    d.criteria = criteria;
                }
            },
            columns: _columns,
            columnDefs: [
                {
                    orderable: false,
                    targets: 0
                }
            ],
            initComplete: function () {
                const dropdown = '' +
                    '       <div class="m-dropdown m-dropdown--inline m-dropdown--align-left" ' +
                    '             data-dropdown-toggle="hover" aria-expanded="true">' +
                    '            <button class="m-dropdown__toggle btn btn-success dropdown-toggle export-as">' +
                    '                EXPORT AS' +
                    '            </button>' +
                    '            <div class="m-dropdown__wrapper">' +
                    '                <div class="m-dropdown__inner">' +
                    '                    <div class="m-dropdown__body">' +
                    '                        <div class="m-dropdown__content">' +
                    '                            <ul class="m-nav">' +
                    '                                <li class="m-nav__item">' +
                    '                                    <a id="export-as-excel" style="cursor:pointer;" ' +
                    '                                       onclick="exportAs(\'excel\'); return false;" class="m-nav__link">' +
                    '                                        <i class="m-nav__link-icon fa fa-file-excel-o m--font-success"></i>' +
                    '                                        <span class="m-nav__link-text" style="text-transform: none;">' +
                    '                                           Excel File' +
                    '                                        </span>' +
                    '                                    </a>' +
                    '                                </li>' +
                    '                                <li class="m-nav__item">' +
                    '                                    <a id="export-as-pdf" style="cursor:pointer;" ' +
                    '                                       onclick="exportAs(\'pdf\'); return false;" class="m-nav__link">' +
                    '                                        <i class="m-nav__link-icon fa fa-file-pdf-o m--font-danger"></i>' +
                    '                                        <span class="m-nav__link-text" style="text-transform: none;">' +
                    '                                          PDF File' +
                    '                                        </span>' +
                    '                                    </a>' +
                    '                                </li>' +
                    '                                <li class="m-nav__item">' +
                    '                                    <a id="export-as-print" style="cursor:pointer;" ' +
                    '                                       onclick="exportAs(\'print\'); return false;" class="m-nav__link">' +
                    '                                        <i class="m-nav__link-icon fa fa-print m--font-info"></i>' +
                    '                                        <span class="m-nav__link-text" style="text-transform: none;">' +
                    '                                          Print' +
                    '                                        </span>' +
                    '                                    </a>' +
                    '                                </li>' +
                    '                            </ul>' +
                    '                        </div>' +
                    '                    </div>' +
                    '                </div>' +
                    '            </div>' +
                    '        </div>';

                $(dropdown).appendTo("#table-generated-report_wrapper .exportDropdown");
            },
            pageLength: 100
        });
}

function exportAs(type) {
    dropdown.addClass("m-btn--custom m-loader m-loader--light m-loader--left");
    let clearHere = false;

    setTimeout(() => {
        switch (type) {
            case "excel":
                dtReport.button(".buttons-excel").trigger();
                break;
            case "pdf":
                dtReport.button(".buttons-pdf").trigger();
                break;
            case "print":
                dtReport.button(".buttons-print").trigger();
                break;
        }

        if (clearHere) {
            dropdown.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
        }
    }, 150);
}

function openAddFieldTemplateDialog() {
    $.ajax({
        url: baseUrl('hris/reports/open_modal'),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: 'hris/masterfile/reports/modals/add_field_template_dialog',
        },
        success: function (response) {
            modalContainer.html(response.html);
            const selectField = modalContainer.find('select[name="field[]"]');
            selectField
                .append(dbFieldEl)
                .select2({
                    width: '100%'
                })
                .on('select2:select', function (e) {
                    selectItem(e.target, e.params.data.id);
                })
            modalContainer.modal('show');
        }
    });
}

function openEditFieldTemplateDialog() {
    if (template.length) {
        const selections = template.map((item) => {
            return item.fieldname;
        });

        $.ajax({
            url: baseUrl('hris/reports/open_modal'),
            type: "POST",
            dataType: "JSON",
            data: {
                csrf_token: _csrf_hash,
                path: 'hris/masterfile/reports/modals/edit_field_template_dialog',
                function_name: 'getTemplateData',
                formData: {
                    id: templateId
                },
                model: 'hris/Reports_model'
            },
            success: function (response) {
                modalContainer.html(response.html);
                const selectField = modalContainer.find('select[name="field[]"]');
                selectField
                    .append(dbFieldEl)
                    .select2({
                        width: '100%'
                    })
                    .on('select2:select', function (e) {
                        selectItem(e.target, e.params.data.id);
                    })
                    .val(selections).change();
                modalContainer.modal('show');
            }
        });
    }
}


function loadTemplate() {
    if (template) { // check if not null
        if (template.length) {
            selectedFields = [];
            $(".m-widget4").html("");
            const selections = template.map((item) => {
                selectedFields.push({ id: item.fieldname, text: item.header });
                const strId = item.header.replace(' ', '-');

                const el = '' +
                    '<div class="m-widget4__item" id="' + strId + '">' +
                    '    <div class="m-widget4__info pl-0">' +
                    '           <span class="m-widget4__text">' +
                    '           ' + (item.header).toUpperCase() +
                    '           </span>' +
                    '    </div>' +
                    '    <div class="m-widget4__ext">' +
                    '           <a href="#" onclick="removeField(\'' + strId + '\',\'' + item.fieldname + '\')" ' +
                    '              class="m-widget4__icon">' +
                    '                   <i class="la la-remove"></i>' +
                    '           </a>' +
                    '    </div>' +
                    '</div>';
                $(".empty-field-selection").html("");
                $(".m-widget4").append(el);

                return item.fieldname;
            });

            $('#field').val(selections).change();
        }
    }
}

$('.m-content')
    .on('submit', '#frm-create-field-template', function (e) {
        e.preventDefault();
        const form = $(this);
        const formData = new FormData(this);
        const url = form.attr('action');

        const select = form.find('select[name="field[]"]');
        let templateItems = [];
        const selected = select.find('option');
        for (let i = 0; i < selected.length; i++) {
            selected[i].selected && templateItems.push({ header: selected[i].text, fieldname: selected[i].value });
        }

        formData.append('templateItems', JSON.stringify(templateItems));

        if (form.isValid()) {
            $.ajax({
                url,
                type: 'POST',
                dataType: 'JSON',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message, 'Template Created.', 10000);
                    } else {
                        toastr.error(response.message, 'Error.', 10000);
                    }

                    modalContainer.modal('hide');
                }
            });
        }
    })
    .on('submit', '#frm-edit-field-template', function (e) {
        e.preventDefault();
        const form = $(this);
        const formData = new FormData(this);
        const url = form.attr('action');

        const select = form.find('select[name="field[]"]');
        let templateItems = [];
        const selected = select.find('option');
        for (let i = 0; i < selected.length; i++) {
            selected[i].selected && templateItems.push({ header: selected[i].text, fieldname: selected[i].value });
        }

        formData.append('templateItems', JSON.stringify(templateItems));

        if (form.isValid()) {
            $.ajax({
                url,
                type: 'POST',
                dataType: 'JSON',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    if (response.success) {
                        const data = response.data;
                        template = data.template_body;

                        $('#template')
                            .empty()
                            .select2(template_select_option);

                        // Set the value, creating a new option if necessary
                        if ($('#template').find("option[value='" + data.id + "']").length) {
                            $('#template').val(data.id).trigger('change');
                        } else {
                            // Create a DOM Option and pre-select by default
                            var newOption = new Option(data.text, data.id, true, true);
                            // Append it to the select
                            $('#template').append(newOption).trigger('change');
                        }

                        toastr.success(response.message, 'Template Updated..', 10000);
                    } else {
                        toastr.error(response.message, 'Error.', 10000);
                    }

                    modalContainer.modal('hide');
                }
            });
        }
    });

async function getExportData(e, dt, node, config, self, url, type) {
    const data = dt.ajax.params();
    data['exportType'] = type;
    const result = await $.ajax({
        url,
        type: "POST",
        dataType: "JSON",
        data,
        success: function (response) {
            dt.rows().remove();
            dt.rows.add(response.data).draw();
            $.fn.dataTable.ext.buttons[type].action.call(self, e, dt, node, config);
        }
    });

    return result;
}
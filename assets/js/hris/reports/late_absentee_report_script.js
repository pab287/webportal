let _years = [];
let _companies = [];
let _departments = [];

const hrisFilterLateReport = $("#frm-filter-hris-late_report");
const dtTableLate = $("#table-late_report");
const modalLatePreview = $("#modalLatePreview");

const hrisFilterAbsenteeReport = $("#frm-filter-hris-absentee_report");
const dtTableAbsentee = $("#table-absentee_report");
const modalAbsenteePreview = $("#modalAbsenteePreview");

let dtTableLateReport, dtTableAbsenteeReport;

let filterOptions = {};
let filterOptionsAbsentee = {};
let globalLoaReference = {};

const months = [
    { id: 1, text: "January" },
    { id: 2, text: "February" },
    { id: 3, text: "March" },
    { id: 4, text: "April" },
    { id: 5, text: "May" },
    { id: 6, text: "June" },
    { id: 7, text: "July" },
    { id: 8, text: "August" },
    { id: 9, text: "September" },
    { id: 10, text: "October" },
    { id: 11, text: "November" },
    { id: 12, text: "December" }
];

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.years !== "undefined" && _tempContentData.years.length > 0){
        _years = _tempContentData.years;
    }
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){
        _companies = _tempContentData.company;
    }

    if(typeof _tempContentData.departments !== "undefined" && _tempContentData.departments.length > 0){
        _departments = _tempContentData.departments;
    }
}

const vmLateReport = new Vue({
    el: "#tempFilterByLateReport",
    data: { filter_by: "date_range" },
    watch: {
        filter_by(value){
            const _this = this;
            const currentElement = this.$el;
            if(value == 'date_range'){
                setTimeout(function () { _this.renderRangeDatePicker(); }, 500);
            }else{
                setTimeout(function () {
                    $(currentElement).find("select[name='filter_month']")
                    .select2({
                        width: '100%',
                        data: months,
                        placeholder: "SELECT MONTH",
                        allowClear: true,
                    });

                    
                    $(currentElement).find("select[name='filter_year']")
                    .select2({
                        width: '100%',
                        data: _years,
                        placeholder: "SELECT YEAR",
                        allowClear: true,
                    });
                }, 250);
            }
        }
    }, methods: {
        renderRangeDatePicker(){
            const currentElement = this.$el;
            const dtPickerElement = $(currentElement).find("#date-picker")
            .daterangepicker({
                buttonClasses: 'm-btn btn',
                applyClass: 'btn-primary',
                cancelClass: 'btn-secondary',
                locale: {
                    format: 'MM/DD/YYYY'
                }
            })
            .on('apply.daterangepicker', function (ev, picker) {
                var tempStartDate = picker.startDate.format('MMM DD, YYYY');
                var tempEndDate = picker.endDate.format('MMM DD, YYYY');
                var tempFormat = tempStartDate + ' - ' + tempEndDate;
                $(currentElement).find("#date-range").val(tempFormat);
            });

            return dtPickerElement;
        }
    },
    mounted(){
        this.renderRangeDatePicker();
    }
});

const vmAbsenteeReport = new Vue({
    el: "#tempFilterByAbsenteeReport",
    data: { filter_by: "date_range" },
    watch: {
        filter_by(value){
            const _this = this;
            const currentElement = this.$el;
            if(value == 'date_range'){
                setTimeout(function () { _this.renderRangeDatePicker(); }, 500);
            }else{
                setTimeout(function () {
                    $(currentElement).find("select[name='filter_month']")
                    .select2({
                        width: '100%',
                        data: months,
                        placeholder: "SELECT MONTH",
                        allowClear: true,
                    });

                    
                    $(currentElement).find("select[name='filter_year']")
                    .select2({
                        width: '100%',
                        data: _years,
                        placeholder: "SELECT YEAR",
                        allowClear: true,
                    });
                }, 250);
            }
        }
    }, methods: {
        renderRangeDatePicker(){
            const currentElement = this.$el;
            const dtPickerElement = $(currentElement).find("#date-picker")
            .daterangepicker({
                buttonClasses: 'm-btn btn',
                applyClass: 'btn-primary',
                cancelClass: 'btn-secondary',
                locale: {
                    format: 'MM/DD/YYYY'
                }
            })
            .on('apply.daterangepicker', function (ev, picker) {
                var tempStartDate = picker.startDate.format('MMM DD, YYYY');
                var tempEndDate = picker.endDate.format('MMM DD, YYYY');
                var tempFormat = tempStartDate + ' - ' + tempEndDate;
                $(currentElement).find("#date-range").val(tempFormat);
            });

            return dtPickerElement;
        }
    },
    mounted(){
        this.renderRangeDatePicker();
    }
});

var vmLatePreview = new Vue({
    el: "#modalLateContainer",
    data: { row: {}, attlogs: [] },
    watch: {
        'row.attendance_logs': function(value){
            let _this = this;
            let arrLogx = [];
            if(typeof value != "undefined" && value){
                const arrLogs = value.split(",");
                arrLogs.forEach(function(log){
                    if(log){
                        const nDate = moment(new Date(log), "YYYY-MM-DD HH:mm:ss").format("LLLL");
                        arrLogx.push(nDate);
                    }
                });
                arrLogx.sort(function(a, b){ return new Date(a) - new Date(b); });
            }
            _this.attlogs = [];
            if(arrLogx.length > 0){
                arrLogx.forEach(function(item){ _this.attlogs.push(item); });
            }

            return _this;
        }
    },
    methods: {
        dateFormatted(date){
            return date ? moment(new Date(date), "YYYY-MM-DD").format("LL"): null;
        }, 
        backgroundClass(date){
            let className = '';
            const meridian = moment(new Date(date), "dddd, MMMM D, YYYY h:m A").format("A");
            if(meridian){
                className = meridian == 'AM' ? 'alert-primary':'alert-danger';
            }
            return className;
        }
    }
});

var vmAbsenteePreview = new Vue({
    el: "#modalAbsenteeContainer",
    data: { row: {}, attlogs: [], reference: {} },
    watch: {
        'row.attendance_logs': function(value){
            let _this = this;
            let arrLogx = [];
            if(typeof value != "undefined" && value){
                const arrLogs = value.split(",");
                arrLogs.forEach(function(log){
                    if(log){
                        const attxLogs = log.split("~");
                        const stDate = moment(new Date(attxLogs[0]), "YYYY-MM-DD HH:mm:ss").format("LLLL");
                        const edDate = moment(new Date(attxLogs[1]), "YYYY-MM-DD HH:mm:ss").format("LLL");
                        const formatted = `${stDate} - ${edDate}`;
                        console.log(formatted);
                        arrLogx.push(formatted);
                    }
                });
                arrLogx.sort(function(a, b){ return new Date(a) - new Date(b); });
            }
            _this.attlogs = [];
            if(arrLogx.length > 0){
                arrLogx.forEach(function(item){ _this.attlogs.push(item); });
            }

            return _this;
        }
    },
    methods: {
        dateFormatted(date){
            return date ? moment(new Date(date), "YYYY-MM-DD").format("LL"): null;
        }, 
        backgroundClass(date){
            let className = '';
            const nDate = date.split(" - ");
            if(nDate.length == 2){
                const meridian = moment(new Date(nDate[0]), "dddd, MMMM D, YYYY h:m A").format("A");
                if(meridian){ className = meridian == 'AM' ? 'alert-primary':'alert-danger'; }
            }
            return className;
        }, getLoaReference(empId, date){
            let referenceNo = null;
            if(typeof empId != "undefined" && typeof date != "undefined"){
                const nDate = date.split(" - ");
                if(nDate.length == 2){
                    const keyDate = moment(new Date(nDate[0]), "dddd, MMMM D, YYYY h:m A").format("YYYY-MM-DD");
                    if(typeof globalLoaReference[empId] != "undefined"){
                        if(typeof globalLoaReference[empId][keyDate] != "undefined"){ referenceNo = globalLoaReference[empId][keyDate]; }
                    }
                }
            }
            return referenceNo;
        }
    }
});


if(typeof hrisFilterLateReport !== "undefined" && hrisFilterLateReport.length == 1){
    hrisFilterLateReport.find("select#company")
    .select2({
        width: '100%',
        data: _companies,
        placeholder: "SELECT AN OPTION",
        allowClear: true,
    }).on("select2:select", function (e) {
        const tempEmployeeSelector = hrisFilterLateReport.find("select#employee");
        const tempPayrollGroupSelector = hrisFilterLateReport.find("select#payroll_group");

        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
            tempEmployeeSelector.val([]).trigger("change");
            tempEmployeeSelector.prop("disabled", false);
        }
        if (typeof tempPayrollGroupSelector !== "undefined" && tempPayrollGroupSelector.length == 1) {
            tempPayrollGroupSelector.val([]).trigger("change");
        }
        
        $(e.target).validate();
    }).on("select2:unselect", function (e) {
        const tempEmployeeSelector = hrisFilterLateReport.find("select#employee");
        const tempPayrollGroupSelector = hrisFilterLateReport.find("select#payroll_group");

        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
            tempEmployeeSelector.val([]).trigger("change");
            tempEmployeeSelector.prop("disabled", false);
        }
        if (typeof tempPayrollGroupSelector !== "undefined" && tempPayrollGroupSelector.length == 1) {
            tempPayrollGroupSelector.val([]).trigger("change");
        }
    });

    hrisFilterLateReport.find("select#department")
    .select2({
        width: '100%',
        placeholder: "SELECT AN OPTION",
        data: _departments,
        allowClear: true,
    });

    hrisFilterLateReport.find("select#employee")
    .select2({
        width: '100%',
        placeholder: "SELECT AN OPTION",
        ajax: {
            url: baseUrl('hris/reports/get_select2_employee_data'),
            dataType: 'json',
            global: false,
            delay: 250,
            data: function (params) {
                params.q = params.term;
                return params;
            },
            processResults: function (data) {
                return data;
            }
        }, language: { errorLoading: function () { return "Searching..." } }
    });

    hrisFilterLateReport.find("select#payroll_group").select2({
        placeholder: 'SELECT AN OPTION',
        width: '100%',
        allowClear: true,
        // minimumInputLength: 1,
        ajax: {
            url: siteUrl("hris/reports/select_payroll_group"),
            dataType: "json",
            type: 'get',
            delay: 250,
            global: false,
            data: function (params) {
                params.company_id = hrisFilterLateReport.find("select#company").val();
                return params;
            }, error: function (xhr, error, code) {
                if (error == "parseerror") { }
            },
            processResults: function (data) {
                return data;
            }
        }
    }).on("select2:select", function (e) {
        const data = e.params.data;
        if (typeof data.employees == "object" && typeof data.employees !== "undefined") {
            const tempEmployeeSelector = hrisFilterLateReport.find("select#employee");
            if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                $.each(data.employees, function (ii, vv) {
                    var tempOption = new Option(vv.text, vv.id, true, true);
                    tempEmployeeSelector.append(tempOption);
                });
                tempEmployeeSelector.prop("disabled", true);
            }
        }
    }).on("select2:unselect", function (e) {
        const tempData = $(this).select2("data");

        const tempEmployeeSelector = hrisFilterLateReport.find("select#employee");
        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
            if(!jQuery.isEmptyObject(tempData)){
                tempEmployeeSelector.empty();
                $.each(tempData[0].employees, function(ii, vv){
                    var tempOption = new Option(vv.text, vv.id, true, true);
                    tempEmployeeSelector.append(tempOption);
                });

                tempEmployeeSelector.prop("disabled", true);
            }else{
                tempEmployeeSelector.empty();
                tempEmployeeSelector.prop("disabled", false);
            }
            
        }
    });
}

if(typeof hrisFilterAbsenteeReport !== "undefined" && hrisFilterAbsenteeReport.length == 1){
    hrisFilterAbsenteeReport.find("select#company")
    .select2({
        width: '100%',
        data: _companies,
        placeholder: "SELECT AN OPTION",
        allowClear: true,
    }).on("select2:select", function (e) {
        const tempEmployeeSelector = hrisFilterAbsenteeReport.find("select#employee");
        const tempPayrollGroupSelector = hrisFilterAbsenteeReport.find("select#payroll_group");

        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
            tempEmployeeSelector.val([]).trigger("change");
            tempEmployeeSelector.prop("disabled", false);
        }
        if (typeof tempPayrollGroupSelector !== "undefined" && tempPayrollGroupSelector.length == 1) {
            tempPayrollGroupSelector.val([]).trigger("change");
        }
        $(e.target).validate();
    }).on("select2:unselect", function (e) {
        const tempEmployeeSelector = hrisFilterAbsenteeReport.find("select#employee");
        const tempPayrollGroupSelector = hrisFilterAbsenteeReport.find("select#payroll_group");

        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
            tempEmployeeSelector.val([]).trigger("change");
            tempEmployeeSelector.prop("disabled", false);
        }
        if (typeof tempPayrollGroupSelector !== "undefined" && tempPayrollGroupSelector.length == 1) {
            tempPayrollGroupSelector.val([]).trigger("change");
        }
    });

    hrisFilterAbsenteeReport.find("select#department")
    .select2({
        width: '100%',
        placeholder: "SELECT AN OPTION",
        data: _departments,
        allowClear: true,
    });

    hrisFilterAbsenteeReport.find("select#employee")
    .select2({
        width: '100%',
        placeholder: "SELECT AN OPTION",
        ajax: {
            url: baseUrl('hris/reports/get_select2_employee_data'),
            dataType: 'json',
            global: false,
            delay: 250,
            data: function (params) {
                params.q = params.term;
                return params;
            },
            processResults: function (data) {
                return data;
            }
        }, language: { errorLoading: function () { return "Searching..." } }
    });

    hrisFilterAbsenteeReport.find("select#payroll_group").select2({
        placeholder: 'SELECT AN OPTION',
        width: '100%',
        allowClear: true,
        // minimumInputLength: 1,
        ajax: {
            url: siteUrl("hris/reports/select_payroll_group"),
            dataType: "json",
            type: 'get',
            delay: 250,
            global: false,
            data: function (params) {
                params.company_id = hrisFilterAbsenteeReport.find("select#company").val();
                return params;
            }, error: function (xhr, error, code) {
                if (error == "parseerror") { }
            },
            processResults: function (data) {
                return data;
            }
        }
    }).on("select2:select", function (e) {
        const data = e.params.data;
        if (typeof data.employees == "object" && typeof data.employees !== "undefined") {
            const tempEmployeeSelector = hrisFilterAbsenteeReport.find("select#employee");
            if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                $.each(data.employees, function (ii, vv) {
                    var tempOption = new Option(vv.text, vv.id, true, true);
                    tempEmployeeSelector.append(tempOption);
                });
                tempEmployeeSelector.prop("disabled", true);
            }
        }
    }).on("select2:unselect", function (e) {
        const tempData = $(this).select2("data");

        const tempEmployeeSelector = hrisFilterAbsenteeReport.find("select#employee");
        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
            if(!jQuery.isEmptyObject(tempData)){
                tempEmployeeSelector.empty();
                $.each(tempData[0].employees, function(ii, vv){
                    var tempOption = new Option(vv.text, vv.id, true, true);
                    tempEmployeeSelector.append(tempOption);
                });

                tempEmployeeSelector.prop("disabled", true);
            }else{
                tempEmployeeSelector.empty();
                tempEmployeeSelector.prop("disabled", false);
            }
            
        }
    });
}

$.validate({
    form: "#frm-filter-hris-late_report",
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        let propDisabled = false;
        const currentForm = form[0];

        const tempEmployeeFilter = $(currentForm).find("select#employee");
        if(typeof tempEmployeeFilter !== "undefined"){
            propDisabled = tempEmployeeFilter.is(":disabled");
            if(propDisabled){ tempEmployeeFilter.prop("disabled", false); }
        }

        var formData = $(currentForm).serialize();
        if(propDisabled){ tempEmployeeFilter.prop("disabled", true); }

        $.ajax({
            url: siteUrl("hris/reports/generate_late_report"),
            type: "post",
            dataType: "json",
            data: formData,
            success: function(json){
                if(json.response){
                    dtTableLateReport.clear();
                    dtTableLateReport.rows.add(json.data);
                    dtTableLateReport.draw(false);
                    filterOptions = Object.assign({}, json.filters);
                }
            }
        });

        return false;
    }
});

$.validate({
    form: "#frm-filter-hris-absentee_report",
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        let propDisabled = false;
        const currentForm = form[0];

        const tempEmployeeFilter = $(currentForm).find("select#employee");
        if(typeof tempEmployeeFilter !== "undefined"){
            propDisabled = tempEmployeeFilter.is(":disabled");
            if(propDisabled){ tempEmployeeFilter.prop("disabled", false); }
        }

        var formData = $(currentForm).serialize();
        if(propDisabled){ tempEmployeeFilter.prop("disabled", true); }

        $.ajax({
            url: siteUrl("hris/reports/generate_absentee_report"),
            type: "post",
            dataType: "json",
            data: formData,
            success: function(json){
                if(json.response){
                    dtTableAbsenteeReport.clear();
                    dtTableAbsenteeReport.rows.add(json.data);
                    dtTableAbsenteeReport.draw(false);
                    filterOptionsAbsentee = Object.assign({}, json.filters);
                    globalLoaReference = Object.assign({}, json.loa_reference);
                }
            }
        });

        return false;
    }
});

if(typeof dtTableLate != "undefined" && dtTableLate.length == 1){
    dtTableLateReport = dtTableLate.DataTable({
        dom: "<'row'<'col-md-9 dtDetails'><'col-md-3 dtActions m--hide'B>>rt",
        ordering: false,
        paging: false,
        columns: [
            { title: "ID Number", data: "idno", width: "12%" },
            { title: "Employee Name", data: "employee_name", width: "25%" },
            { title: "Position", data: "position", width: "*" },
            { title: "Total", data: "late_total", width: "10%", className: "text-right" },
            { title: "", width: "6%", render: function(_data, _type, row){
                    const objResponse = JSON.stringify(row);
                    return `<button class='btn btn-secondary m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill btnView btnLatePreview' data-raw='${objResponse}'>
                        <i class='fa fa-hourglass-half'></i>
                    </button>`;
                }
            }
        ], buttons: [{
            extend: 'excel',
            text: '<i class="fa fa-download"></i><span class="m--font-boldest">EXPORT EXCEL</span>',
            className: "pull-right exportLateReportAction btnExport",
            exportOptions: {
                columns: [0, 1, 2, 3],
                stripHtml: true,
            }
        }, {
            extend: 'print',
            text: '<i class="fa fa-print"></i><span class="m--font-boldest">PRINT</span>',
            className: "pull-right printLateReportAction btnPrint",
            title: function () {
                const filterType = `<div>
                    <div class="m--regular-font-size-sm1 mt-1">FILTER BY: ${filterOptions.filter_by}</div>
                    <div class="m--regular-font-size-sm1 mt-1">FILTER DATE: ${filterOptions.filter_date}</div>
                </div>`;
                const companyCode = typeof filterOptions.company_code != "undefined" ? `<div>
                    <div class="m--regular-font-size-sm1 mt-1">COMPANY: ${filterOptions.company_code}</div>
                </div>`:``;
                const payrollGroup = typeof filterOptions.payroll_group != "undefined" ? `<div>
                    <div class="m--regular-font-size-sm1 mt-1">PAYROLL GROUP: ${filterOptions.payroll_group}</div>
                </div>`:``;

                return `<div class="m--regular-font-size-lg1">ATTENDANCE LATE REPORT</div>
                    <div class='mb-3'>${companyCode}${filterType}${payrollGroup}</div>`;
            }, customize: function (win) {
                var css = `@page { size: portrait; margin: 0.5cm; }
                    table { font-size: 12px; }
                    .print-size-auto{ width: auto }
                    .print-size-8{ width: 8% }
                    .print-size-10{ width: 10% }
                    .print-size-25{ width: 25% }`,
                    head = win.document.head || win.document.getElementsByTagName('head')[0],
                    style = win.document.createElement('style');

                style.type = 'text/css';
                style.media = 'print';

                if (style.styleSheet) { style.styleSheet.cssText = css; } 
                else { style.appendChild(win.document.createTextNode(css)); }

                head.appendChild(style);
                win.document.title = "Late Report Printable Page";
            }, exportOptions: {
                columns: [0, 1, 2, 3],
                stripHtml: true,
            }
        }],
        drawCallback: function(settings){
                $(".btnLatePreview").on("click", function(){
                    const data = $(this).data("raw");
                    vmLatePreview.row = Object.assign({}, data);
                    modalLatePreview.modal();
                });

            var api = this.api();
            var btnPrint = $(settings.nTableWrapper).find(".printLateReportAction");
            var btnExport = $(settings.nTableWrapper).find(".exportLateReportAction");
            var dtActions = $(settings.nTableWrapper).find(".dtActions");
            if (typeof btnPrint !== "undefined" && typeof dtActions !== "undefined") {
                btnPrint.addClass("btn m-btn btn-brand m-btn--icon m--hide animated fadeIn mr-1");
                var tempData = api.data();
                if (tempData.length > 0) {
                    if (btnPrint.hasClass("m--hide") == true) { btnPrint.removeClass("m--hide"); }
                    if (dtActions.hasClass("m--hide") == true) { dtActions.removeClass("m--hide"); }
                } else {
                    if (dtActions.hasClass("m--hide") == false) { dtActions.addClass("m--hide"); }
                    if (btnPrint.hasClass("m--hide") == false) { btnPrint.addClass("m--hide"); }
                }
            }
            if (typeof btnExport !== "undefined" && typeof dtActions !== "undefined") {
                btnExport.addClass("btn m-btn btn-brand m-btn--icon m--hide animated fadeIn");
                var tempData = api.data();
                if (tempData.length > 0) {
                    if (btnExport.hasClass("m--hide") == true) { btnExport.removeClass("m--hide"); }
                    if (dtActions.hasClass("m--hide") == true) { dtActions.removeClass("m--hide"); }
                } else {
                    if (dtActions.hasClass("m--hide") == false) { dtActions.addClass("m--hide"); }
                    if (btnExport.hasClass("m--hide") == false) { btnExport.addClass("m--hide"); }
                }
            }
        }
    });
}

if(typeof dtTableAbsentee !== "undefined" && dtTableAbsentee.length == 1){
    dtTableAbsenteeReport = dtTableAbsentee.DataTable({
        dom: "<'row'<'col-md-9 dtDetails'><'col-md-3 dtActions m--hide'B>>rt",
        ordering: false,
        paging: false,
        columns: [
            { title: "ID Number", data: "idno", width: "12%" },
            { title: "Employee Name", data: "employee_name", width: "25%" },
            { title: "Position", data: "position", width: "*" },
            { title: "Total", data: "absentee_total", width: "10%", className: "text-right" },
            { title: "", width: "6%", render: function(_data, _type, row){
                const objResponse = JSON.stringify(row);
                return `<button class='btn btn-secondary m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill btnView btnAbsenteePreview' data-raw='${objResponse}'>
                    <i class='fa fa-hourglass-half'></i>
                </button>`;
                }
            }
        ], buttons: [{
            extend: 'excel',
            text: '<i class="fa fa-download"></i><span class="m--font-boldest">EXPORT EXCEL</span>',
            className: "pull-right exportAbsenteeReportAction btnExport",
            exportOptions: {
                columns: [0, 1, 2, 3],
                stripHtml: true,
            }
        }, {
            extend: 'print',
            text: '<i class="fa fa-print"></i><span class="m--font-boldest">PRINT</span>',
            className: "pull-right printAbsenteeReportAction btnPrint",
            title: function () {
                const filterType = `<div>
                    <div class="m--regular-font-size-sm1 mt-1">FILTER BY: ${filterOptionsAbsentee.filter_by}</div>
                    <div class="m--regular-font-size-sm1 mt-1">FILTER DATE: ${filterOptionsAbsentee.filter_date}</div>
                </div>`;
                const companyCode = typeof filterOptionsAbsentee.company_code != "undefined" ? `<div>
                    <div class="m--regular-font-size-sm1 mt-1">COMPANY: ${filterOptionsAbsentee.company_code}</div>
                </div>`:``;
                const payrollGroup = typeof filterOptionsAbsentee.payroll_group != "undefined" ? `<div>
                    <div class="m--regular-font-size-sm1 mt-1">PAYROLL GROUP: ${filterOptionsAbsentee.payroll_group}</div>
                </div>`:``;

                return `<div class="m--regular-font-size-lg1">ATTENDANCE ABSENTEE REPORT</div>
                    <div class='mb-3'>${companyCode}${filterType}${payrollGroup}</div>`;
            }, customize: function (win) {
                var css = `@page { size: portrait; margin: 0.5cm; }
                    table { font-size: 12px; }
                    .print-size-auto{ width: auto }
                    .print-size-8{ width: 8% }
                    .print-size-10{ width: 10% }
                    .print-size-25{ width: 25% }`,
                    head = win.document.head || win.document.getElementsByTagName('head')[0],
                    style = win.document.createElement('style');

                style.type = 'text/css';
                style.media = 'print';

                if (style.styleSheet) { style.styleSheet.cssText = css; } 
                else { style.appendChild(win.document.createTextNode(css)); }

                head.appendChild(style);
                win.document.title = "Absentee Report Printable Page";
            }, exportOptions: {
                columns: [0, 1, 2, 3],
                stripHtml: true,
            }
        }],
        drawCallback: function(settings){
            $(".btnAbsenteePreview").on("click", function(){
                const data = $(this).data("raw");
                vmAbsenteePreview.row = Object.assign({}, data);
                modalAbsenteePreview.modal();
            });

        var api = this.api();
        var btnPrint = $(settings.nTableWrapper).find(".printAbsenteeReportAction");
        var btnExport = $(settings.nTableWrapper).find(".exportAbsenteeReportAction");
        var dtActions = $(settings.nTableWrapper).find(".dtActions");
        if (typeof btnPrint !== "undefined" && typeof dtActions !== "undefined") {
            btnPrint.addClass("btn m-btn btn-brand m-btn--icon m--hide animated fadeIn mr-1");
            var tempData = api.data();
            if (tempData.length > 0) {
                if (btnPrint.hasClass("m--hide") == true) { btnPrint.removeClass("m--hide"); }
                if (dtActions.hasClass("m--hide") == true) { dtActions.removeClass("m--hide"); }
            } else {
                if (dtActions.hasClass("m--hide") == false) { dtActions.addClass("m--hide"); }
                if (btnPrint.hasClass("m--hide") == false) { btnPrint.addClass("m--hide"); }
            }
        }
        if (typeof btnExport !== "undefined" && typeof dtActions !== "undefined") {
            btnExport.addClass("btn m-btn btn-brand m-btn--icon m--hide animated fadeIn");
            var tempData = api.data();
            if (tempData.length > 0) {
                if (btnExport.hasClass("m--hide") == true) { btnExport.removeClass("m--hide"); }
                if (dtActions.hasClass("m--hide") == true) { dtActions.removeClass("m--hide"); }
            } else {
                if (dtActions.hasClass("m--hide") == false) { dtActions.addClass("m--hide"); }
                if (btnExport.hasClass("m--hide") == false) { btnExport.addClass("m--hide"); }
            }
        }
    }
    });
}

const resetFilterLateReport = function(event){
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        const select2 = form.find("#employee, #payroll_group, #company");
        if (typeof select2 !== "undefined" && select2.length > 0) {
            $.each(select2, function (i, v) {
                const multi = $(v)[0].multiple;
                if (multi) {
                    $(v).val([])
                        .trigger("change")
                        .prop("disabled", false);
                } else {
                    $(v).val("")
                        .trigger("change");
                }
            });
        }
    }
}

const resetFilterAbsenteeReport = function(event){
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        const select2 = form.find("#employee, #payroll_group, #company");
        if (typeof select2 !== "undefined" && select2.length > 0) {
            $.each(select2, function (i, v) {
                const multi = $(v)[0].multiple;
                if (multi) {
                    $(v).val([])
                        .trigger("change")
                        .prop("disabled", false);
                } else {
                    $(v).val("")
                        .trigger("change");
                }
            });
        }
    }
}

const submitLateFilterForm = function(event){
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        $(form).validate();
        $(form).submit(); 
    }
}

const submitAbsentFilterForm = function(event){
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        $(form).validate();
        $(form).submit(); 
    }
}

const vmLateAction = new Vue({
    el: "#lateReportActions",
    data: { has_actions: false },
});

const vmAbsentAction = new Vue({
    el: "#absentReportActions",
    data: { has_actions: false },
});

if(typeof _currentActions != "undefined" && _currentActions.includes("advance_search")){
    vmLateAction.has_actions = true;
    vmAbsentAction.has_actions = true;
}
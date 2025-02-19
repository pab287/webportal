let _companies = [];
let _departments = [];
let _stations = [];
let filters ={};
if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){ _companies = _tempContentData.company; }
    if(typeof _tempContentData.department !== "undefined" && _tempContentData.department.length > 0){ _departments = _tempContentData.department; }
    if(typeof _tempContentData.station !== "undefined" && _tempContentData.station.length > 0){ _stations = _tempContentData.station; }
}
toastr.options = { positionClass: "toast-bottom-right" };

$("#date-range").val("");
const initDateRangePicker = function(destroy=false){
    if(destroy){ 
        $("#date-picker").daterangepicker("destroy"); 
        $("#date-range").val("");
    }
    $("#date-picker")
    .daterangepicker({
        maxDate: moment().format("MM/DD/YYYY"),
        buttonClasses: 'm-btn btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',
        locale: { format: 'MM/DD/YYYY' }
    }).on('apply.daterangepicker', function (ev, picker) {
        $("#date-range")
        .val(picker.startDate.format('MMM. DD, YYYY') + ' - ' + picker.endDate.format('MMM. DD, YYYY'))
        .validate();
    });
}
initDateRangePicker();

$("#company").select2({
    width: '100%',
    data: _companies,
    placeholder: 'Select an option',
    allowClear: true,
}).on("select2:select", function (e) {
    $(e.target).validate();
    filters.company = $(e.target).val();
}).on("select2:unselect", function (e) {
    globalCompanyId = 0;
    globalDepartmentId = 0;
    filters.company = "";
}).on("change", function (e) {});

$("#department").select2({
    width: '100%',
    data: _departments,
    placeholder: 'Select an option',
    allowClear: true,
}).on("select2:select", function (e) {
    $(e.target).validate();
    filters.department = $(e.target).val();
}).on("select2:unselect", function (e) {
    globalCompanyId = 0;
    globalDepartmentId = 0;
    filters.company ="";
}).on("change", function (e) {});

$("#station").select2({
    width: '100%',
    data: _stations,
    placeholder: 'Select an option',
    allowClear: true,
}).on("select2:select", function (e) {
    $(e.target).validate();
    filters.station = $(e.target).val();
}).on("select2:unselect", function (e) {
    filters.station = "";
}).on("change", function (e) {});

var resetFilter = function (event) {
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        initDateRangePicker(true);
        vmTemporaryFilter.all_filter = "all";
        const select2 = form.find("#company");
        if (typeof select2 !== "undefined" && select2.length > 0) {
            $.each(select2, function (_i, v) {
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

const  vmTemporaryFilter = new Vue({
    el: "#tempFilter",
    data: { all_filter: "all" },
    watch: { 
        all_filter(nValue){
            if(nValue == "date_range"){ setTimeout(function(){ 
                initDateRangePicker(true); 
                toastr.info("Rendering Date Range Picker", "Filter By - Date Range");
            }, 500); }
        }
    }
});

const vmRenderedContent = new Vue({
    el: "#renderedContent",
    data: { headers: [], count: 0, filter: {}, dtInstances: {}, loadingContent: true },
    methods: {
        generateManpowerByCompany: function(){
            const _this = this;
            _this.loadingContent = true;
            const formData = _this.filter;
            if(typeof _this.dtInstances !== "undefined" && Object.keys(_this.dtInstances).length > 0){
                console.log(_this.dtInstances);
            }
            $.ajax({
                url: baseUrl("hris/reports/generate_manpower_by_company_report"),
                type: "post",
                dataType: "json",
                data: formData,
                global: false,
                success: function (json) {
                    let tempInstances = {};
                    if(json.response){
                        tempInstances = Object.assign({});
                        $.fn.dataTable.ext.errMode = 'none';
                        _this.loadingContent = false;
                        const allActiveEmployee = formData.filter_by == "all";
                        const columns = [
                            { data: null, title: "#", width: "8%", render: function(_data, _type, _row, meta){
                                const tempCount = parseInt(meta.row) + 1;
                                return tempCount;
                            } },
                            { data: "idno", title: "ID #",width: "8%" },
                            { data: "lastname", title: "Last Name", width: "8%" },
                            { data: "firstname", title: "First Name", width: "8%" },
                            { data: "middlename", title: "Middle Name", width: "8%" },
                            { data: "position", title: "Position", width: "*" },
                            { data: "department", title: "Department", width: "*", visible: false, },
                            { data: "site_location", title: "Area", width: "*" },
                            { data: "date_start", title: "Date Hired", width: "11%", className: "text-center" },
                            { data: null, title: "Tenure", width: "*", className: "text-center", render: function(_data, _type, row){
                                let tempData = "";
                                if(typeof row.date_start !== "undefined" && row.date_start){
                                    const dateStart = new Date(row.date_start);
                                    tempData =  _this.calculateTenureDate(dateStart);
                                }
                                return tempData;
                            }},
                            { data: "work_status", title: "Status", width: "12%" },
                            { data: "basic_rate", title: "Rate", className: "text-right", width: "10%", render: function(data){
                                return numberFormat(data);
                            } },
                            { data: "allw_rate", title: "Allowance", className: "text-right", width: "7%", render: function(data){
                                return numberFormat(data);
                            } },
                            { data: "salary_date", title: "Salary Adjustment", width: "10%", className: "text-center" },
                        ];

                        const salaryColumns = [
                            { data: null, title: "#", width: "8%", 
                                render: function(_data, _type, _row, meta){
                                    const tempCount = parseInt(meta.row) + 1;
                                    return tempCount;
                                } 
                            },
                            { data: "idno", title: "ID #",width: "8%", visible: false },
                            { data: "lastname", title: "Last Name", width: "8%" },
                            { data: "firstname", title: "First Name", width: "8%" },
                            { data: "middlename", title: "Middle Name", width: "8%" },
                            { data: "position", title: "Position", width: "*" },
                            { data: "department", title: "Department", width: "*" },
                            { data: 'sal_year', title: 'Year', width: '8%' },
                            { data: "sal_rate", title: 'Rate', width: '10%' },
                            { data: 'sal_remarks', title: 'Remarks', width: '20%' },
                            { data: 'sal_date', title: 'Salary Adjustment', width: "10%", className: "text-center" }
                        ];
                        // let salaryColumns = [];

                        $.each(json.data, function(kk, vv){
                            if(vv.regular.count > 0){
                                if(typeof $(`#table-manpower_report_${kk}`) !== "undefined"){
                                    if ( $.fn.DataTable.isDataTable( "#table-manpower_report_"+kk ) ) {
                                        if(typeof _this.dtInstances["regular_"+kk] !== "undefined"){ _this.dtInstances["regular_"+kk].destroy(); }
                                    }
                                    tempInstances["regular_"+kk] = $(`#table-manpower_report_${kk}`).DataTable({
                                        dom: "rtl", paging: false, ordering: false, columns, rowGroup: { 
                                            startRender: function ( _row, group ) {
                                                return group.trim() !=="No group" ? group.trim(): "No Department";
                                            },
                                            dataSrc: [ 'department' ] 
                                        },
                                        buttons: [{
                                            extend: 'print',
                                            text: 'PRINT',
                                            title: function () {
                                                const tempFilter = _this.filter;
                                                const company = vv.company;
                                                const totalEntries = vv.regular.count;
                                                const timeStamp = moment().format("MM/DD/YYYY hh:mm A");
                                                const tempCoverage = allActiveEmployee ? `<div class="m--regular-font-size-sm1 mt-2 text-center">COVERAGE: ALL ACTIVE EMPLOYEES</div>`
                                                : `<div class="m--regular-font-size-sm1 mt-2 text-center">COVERAGE DATE: ${tempFilter.start_date_formatted} - ${tempFilter.end_date_formatted}</div>`;

                                                return `<div class="m--regular-font-size-lg1 text-center">${company}</div>
                                                <div class="m--regular-font-size-lg1 text-center">MANPOWER REPORT</div>${tempCoverage}
                                                <div class="m--regular-font-size-sm1 mt-1 text-center">TOTAL NUMBER OF ENTRIES: ${totalEntries}</div>
                                                <div class="m--regular-font-size-sm2 mt-1 text-center">GENERATED AS OF ${timeStamp}</div>`;
                                            },
                                            exportOptions: { stripHtml: false },
                                            customize: function (win) {
                                                const company = vv.company;
                                                const totalEntries = vv.regular.count;
                                                var css = `@page { size: landscape; margin: 0.5cm; } 
                                                    .dt-print-view table { font-size: 10px; } 
                                                    .dt-print-view table.dataTable tfoot tr:first-child th{ border-top: 1px solid #000000; }
                                                    .dt-print-view table.dataTable tfoot tr:first-child th{ border-bottom: 4px double #000000; }`,
                                                    head = win.document.head || win.document.getElementsByTagName('head')[0],
                                                    style = win.document.createElement('style');
                            
                                                style.type = 'text/css';
                                                style.media = 'print';
                            
                                                if (style.styleSheet) {
                                                    style.styleSheet.cssText = css;
                                                } else {
                                                    style.appendChild(win.document.createTextNode(css));
                                                }
                            
                                                head.appendChild(style);
                                                win.document.title = "HRIS Printable Page";
                    
                                                const tempTable = win.document.getElementsByClassName('dataTable')[0];
                                                $(tempTable).removeClass("table-bordered");
                                                export_log(filters," Manpower DATA Report <strong>"+company+"</strong>", "print",totalEntries); // This is for per company tab
                                            }
                                        }, { extend: 'excel',
                                            customize: function (){
                                                const company = vv.company;
                                                const totalEntries = vv.regular.count;
                                                export_log(filters," Manpower DATA Report <strong>"+company+"</strong>", "excel",totalEntries); // This is for per company tab
                                            }
                                        }]
                                    });
                                    tempInstances["regular_"+kk].clear();
                                    tempInstances["regular_"+kk].rows.add(vv.regular.rows).draw();
                                }
                            }

                            if(vv.weekly.count > 0){
                                if(typeof $(`#table-manpower_report_weekly_${kk}`) !== "undefined"){
                                    if ( $.fn.DataTable.isDataTable( "#table-manpower_report_weekly_"+kk ) ) { 
                                        if(typeof _this.dtInstances["weekly_"+kk] !== "undefined"){ _this.dtInstances["weekly_"+kk].destroy(); }
                                    }
                                    tempInstances["weekly_"+kk] = $(`#table-manpower_report_weekly_${kk}`).DataTable({
                                        dom: "rtl", paging: false, ordering: false, columns, rowGroup: { dataSrc: [ 'department' ] }, 
                                        buttons: [{
                                            extend: 'print',
                                            text: 'PRINT',
                                            title: function () {
                                                const tempFilter = _this.filter;
                                                const company = vv.company;
                                                const totalEntries = vv.weekly.count;
                                                const timeStamp = moment().format("MM/DD/YYYY hh:mm A");
                                                const tempCoverage = allActiveEmployee ? `<div class="m--regular-font-size-sm1 mt-2 text-center">COVERAGE: ALL ACTIVE EMPLOYEES</div>`
                                                : `<div class="m--regular-font-size-sm1 mt-2 text-center">COVERAGE DATE: ${tempFilter.start_date_formatted} - ${tempFilter.end_date_formatted}</div>`;

                                                return `<div class="m--regular-font-size-lg1 text-center">${company}</div>
                                                <div class="m--regular-font-size-lg1 text-center">WEEKLY - MANPOWER REPORT</div>${tempCoverage}
                                                <div class="m--regular-font-size-sm1 mt-1 text-center">TOTAL NUMBER OF ENTRIES: ${totalEntries}</div>
                                                <div class="m--regular-font-size-sm2 mt-1 text-center">GENERATED AS OF ${timeStamp}</div>`;
                                            },
                                            exportOptions: { stripHtml: false },
                                            customize: function (win) {
                                                const company = vv.company;
                                                const totalEntries = vv.regular.count;
                                                var css = `@page { size: landscape; margin: 0.5cm; } 
                                                    .dt-print-view table { font-size: 11px; } 
                                                    .dt-print-view table.dataTable tfoot tr:first-child th{ border-top: 1px solid #000000; }
                                                    .dt-print-view table.dataTable tfoot tr:first-child th{ border-bottom: 4px double #000000; }`,
                                                    head = win.document.head || win.document.getElementsByTagName('head')[0],
                                                    style = win.document.createElement('style');
                            
                                                style.type = 'text/css';
                                                style.media = 'print';
                            
                                                if (style.styleSheet) {
                                                    style.styleSheet.cssText = css;
                                                } else {
                                                    style.appendChild(win.document.createTextNode(css));
                                                }
                            
                                                head.appendChild(style);
                                                win.document.title = "HRIS Printable Page";
                    
                                                const tempTable = win.document.getElementsByClassName('dataTable')[0];
                                                $(tempTable).removeClass("table-bordered");
                                                export_log(filters,"Manpower Data <strong>"+company+"</strong>", "print",totalEntries); // This is for per company tab
                                            }
                                        }, { extend: 'excel',
                                            customize: function (){
                                                const company = vv.company;
                                                const totalEntries = vv.regular.count;
                                                export_log(filters,"Manpower Data <strong>"+company+"</strong>", "excel",totalEntries); // This is for per company tab
                                            }
                                        }]
                                    });
                                    tempInstances["weekly_"+kk].clear();
                                    tempInstances["weekly_"+kk].rows.add(vv.weekly.rows).draw();
                                }
                            }

                            if(vv.salary_history.count > 0){
                                if(typeof $(`#table-manpower_report_salary_history_${kk}`) !== "undefined"){
                                    if ( $.fn.DataTable.isDataTable( "#table-manpower_report_salary_history_"+kk ) ) {
                                        if(typeof _this.dtInstances["salary_history_"+kk] !== "undefined"){ _this.dtInstances["salary_history_"+kk].destroy(); }
                                    }

                                    tempInstances['salary_history_'+kk] = $(`#table-manpower_report_salary_history_${kk}`).DataTable({
                                        dom: "rtl",
                                        paging: false,
                                        orderable: false,
                                        columns: salaryColumns,
                                        rowGroup: { 
                                            startRender: function ( _row, group ) {
                                                return group.trim() !=="No group" ? "<strong>ID NO:</strong> " + group.trim(): "No ID #";
                                            },
                                            dataSrc: [ 'idno' ]
                                        },
                                        buttons: [{
                                            extend: 'print',
                                            text: 'PRINT',
                                            title: function () {
                                                const tempFilter = _this.filter;
                                                const company = vv.company;
                                                const totalEntries = vv.salary_history.count;
                                                const timeStamp = moment().format("MM/DD/YYYY hh:mm A");
                                                const tempCoverage = allActiveEmployee ? `<div class="m--regular-font-size-sm1 mt-2 text-center">COVERAGE: ALL ACTIVE EMPLOYEES</div>`
                                                : `<div class="m--regular-font-size-sm1 mt-2 text-center">COVERAGE DATE: ${tempFilter.start_date_formatted} - ${tempFilter.end_date_formatted}</div>`;

                                                return `<div class="m--regular-font-size-lg1 text-center">${company}</div>
                                                <div class="m--regular-font-size-lg1 text-center">MANPOWER REPORT</div>${tempCoverage}
                                                <div class="m--regular-font-size-sm1 mt-1 text-center">TOTAL NUMBER OF ENTRIES: ${totalEntries}</div>
                                                <div class="m--regular-font-size-sm2 mt-1 text-center">GENERATED AS OF ${timeStamp}</div>`;
                                            },
                                            exportOptions: { stripHtml: false },
                                            customize: function (win) {
                                                const company = vv.company;
                                                const totalEntries = vv.regular.count;
                                                var css = `@page { size: landscape; margin: 0.5cm; } 
                                                    .dt-print-view table { font-size: 10px; } 
                                                    .dt-print-view table.dataTable tfoot tr:first-child th{ border-top: 1px solid #000000; }
                                                    .dt-print-view table.dataTable tfoot tr:first-child th{ border-bottom: 4px double #000000; }`,
                                                    head = win.document.head || win.document.getElementsByTagName('head')[0],
                                                    style = win.document.createElement('style');
                            
                                                style.type = 'text/css';
                                                style.media = 'print';
                            
                                                if (style.styleSheet) {
                                                    style.styleSheet.cssText = css;
                                                } else {
                                                    style.appendChild(win.document.createTextNode(css));
                                                }
                            
                                                head.appendChild(style);
                                                win.document.title = "HRIS Printable Page";
                    
                                                const tempTable = win.document.getElementsByClassName('dataTable')[0];
                                                $(tempTable).removeClass("table-bordered");
                                                export_log(filters,"Salary History Manpower Data <strong>"+company+"</strong>", "print",totalEntries);
                                            }
                                        }, { extend: 'excel' ,
                                            customize: function (){
                                                const company = vv.company;
                                                const totalEntries = vv.regular.count;
                                                export_log(filters,"Salary History Manpower Data <strong>"+company+"</strong>", "excel",totalEntries);
                                            }
                                        }]
                                    });

                                    tempInstances["salary_history_"+kk].clear();
                                    tempInstances["salary_history_"+kk].rows.add(vv.salary_history.rows).draw();
                                }
                            }
                        });
                    }
                    _this.dtInstances = Object.assign({}, tempInstances);
                }
            });
        }, printPayrollSheet: function(e, tempKey){
            const _this = this;
            const dtInstance = _this.dtInstances;
            $("i", e).removeClass();
            $("i", e).addClass("fa fa-spinner fa-spin");
            $("i", e).css({ right: 0, left: 0 });

            setTimeout(() => {
                dtInstance[tempKey].button(".buttons-print").trigger();
                $("i", e).removeClass("fa fa-spinner fa-spin").addClass("fa fa-print");
                $("i", e).css({ top: "50%", left: "50%" });
            }, 250);
        }, exportExcelPayrollSheet: function(e, tempKey) {
            const _this = this;
            const dtInstance = _this.dtInstances;
            $("i", e).removeClass();
            $("i", e).addClass("fa fa-spinner fa-spin");
            $("i", e).css({ right: 0, left: 0 });
        
            setTimeout(() => {
                dtInstance[tempKey].button(".buttons-excel").trigger();
                $("i", e).removeClass("fa fa-spinner fa-spin").addClass("fa fa-print");
                $("i", e).css({ top: "50%", left: "50%" });
            }, 150);
        }, getInstanceExist: function(tempKey){
            const _this = this;
            const dtInstance = _this.dtInstances;
            if(typeof dtInstance[tempKey] !== "undefined" && Object.keys(dtInstance[tempKey]).length > 0){
                return true;
            }else{
                return false;
            }
        }, calculateTenureDate(dateStart) {
            var startDate = new Date(dateStart);
            var diffDate = new Date(new Date() - startDate);
            const tempYear = diffDate.toISOString().slice(0, 4) - 1970;
            const tempMonth = diffDate.getMonth();

            const renderYear = tempYear == 1 ? `${tempYear} YEAR`: tempYear > 1 ? `${tempYear} YEARS`: '';
            const renderMonth = tempMonth == 1 ? `${tempMonth} MONTH`: tempMonth > 1 ? `${tempMonth} MONTHS`: '';
            const renderFormattedDate = `${renderYear} ${renderMonth}`;
            return renderFormattedDate.trim();
        }
    }
});

const vmFilteredContent = new Vue({
    el: "#filteredContent",
    data: { rows: {}, count: 0, filtered_options: {}, dtInstance: {}, loadingContent: false },
    methods: {
        renderDataTable: function(){
            const _this = this;
            const filteredOption = _this.filtered_options;
            const allActiveEmployee = filteredOption.filter_by == "all";
            const columns = [
                { data: "code", title: "COMPANY", width: "*" },
                { data: "total_count", title: "TOTAL COUNT", width: "10%", className: "text-right" },
                { data: "regular_count", title: "REGULAR", width: "10%", className: "text-right" },
                { data: "probi_count", title: "PROBATIONARY", width: "11%", className: "text-right" },
                { data: "retired_count", title: "RETIRED", width: "10%", className: "text-right" },
                { data: "ptime_count", title: "PART-TIME", width: "10%", className: "text-right" },
                { data: "pbase_count", title: "PROJECT BASED", width: "11%", className: "text-right" },
                { data: "ncont_count", title: "NO CONTRACT", width: "10%", className: "text-right" },
            ];
            const dtManpowerReport = $("#table-manpower_report");
            if(typeof dtManpowerReport !== "undefined" && dtManpowerReport.length > 0){
                if ( $.fn.DataTable.isDataTable( '#table-manpower_report' ) ) { 
                    if(typeof _this.dtInstance !== "undefined" && Object.keys(_this.dtInstance).length > 0){
                        _this.dtInstance.destroy();
                    }
                }

                _this.dtInstance = dtManpowerReport.DataTable({
                    dom: "rtl", paging: false, ordering: false, columns,
                    buttons: [{
                        extend: 'print',
                        text: 'PRINT',
                        title: function () {
                            const timeStamp = moment().format("MM/DD/YYYY hh:mm A");
                            const tempCoverage = allActiveEmployee ? `<div class="m--regular-font-size-sm1 mt-2 text-center">COVERAGE: ALL</div>`
                            : `<div class="m--regular-font-size-sm1 mt-2 text-center">COVERAGE DATE: ${filteredOption.start_date_formatted} - ${filteredOption.end_date_formatted}</div>`;
                            
                            return `<div class="m--regular-font-size-lg1 text-center">MANPOWER REPORT</div>${tempCoverage}
                            <div class="m--regular-font-size-sm2 mt-1 text-center">GENERATED AS OF ${timeStamp}`;
                        },
                        exportOptions: { stripHtml: false },
                        customize: function (win) {
                            var css = `@page { size: landscape; margin: 0.5cm; } 
                                .dt-print-view table { font-size: 12px; } 
                                .dt-print-view table.dataTable tfoot tr:first-child th{ border-top: 1px solid #000000; }
                                .dt-print-view table.dataTable tfoot tr:first-child th{ border-bottom: 4px double #000000; }`,
                                head = win.document.head || win.document.getElementsByTagName('head')[0],
                                style = win.document.createElement('style');
        
                            style.type = 'text/css';
                            style.media = 'print';
        
                            if (style.styleSheet) {
                                style.styleSheet.cssText = css;
                            } else {
                                style.appendChild(win.document.createTextNode(css));
                            }
        
                            head.appendChild(style);
                            win.document.title = "HRIS Manpower Count Printable Page";

                            const tempTable = win.document.getElementsByClassName('dataTable')[0];
                            $(tempTable).removeClass("table-bordered");
                            const tempTHead = $(tempTable).find("thead th:not(:first-child)");
                            tempTHead.removeClass("text-right").addClass("text-center");
                            export_log(filters,"Manpower Report Coverage: All", "print",vmFilteredContent.count); // This is for all
                        }
                    }, { extend: 'excel' }],
                    footerCallback: function(){
                        const api = this.api();
                        const intVal = function (i) {
                            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                        };

                        totalCount = api.column(1).data()
                        .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                        totalRegular = api.column(2).data()
                        .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                        totalProbi = api.column(3).data()
                        .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                        totalRetired = api.column(4).data()
                        .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                        totalPtime = api.column(5).data()
                        .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                        totalPbase = api.column(6).data()
                        .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                        totalNcont = api.column(7).data()
                        .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

                        $(api.column(1).footer()).html("<span class='m--font-boldest'>" + totalCount + "</span>");
                        $(api.column(2).footer()).html("<span class='m--font-boldest'>" + totalRegular + "</span>");
                        $(api.column(3).footer()).html("<span class='m--font-boldest'>" + totalProbi + "</span>");
                        $(api.column(4).footer()).html("<span class='m--font-boldest'>" + totalRetired + "</span>");
                        $(api.column(5).footer()).html("<span class='m--font-boldest'>" + totalPtime + "</span>");
                        $(api.column(6).footer()).html("<span class='m--font-boldest'>" + totalPbase + "</span>");
                        $(api.column(7).footer()).html("<span class='m--font-boldest'>" + totalNcont + "</span>");
                    }
                });
            }
        }, printPayrollSheet: function(e){
            const _this = this;
            const dtInstance = _this.dtInstance;
            $("i", e).removeClass();
            $("i", e).addClass("fa fa-spinner fa-spin");
            $("i", e).css({ right: 0, left: 0 });

            setTimeout(() => {
                dtInstance.button(".buttons-print").trigger();
                $("i", e).removeClass("fa fa-spinner fa-spin").addClass("fa fa-print");
                $("i", e).css({ top: "50%", left: "50%" });
            }, 250);
        }, exportExcelPayrollSheet: function(e) {
            const _this = this;
            const dtInstance = _this.dtInstance;
            $("i", e).removeClass();
            $("i", e).addClass("fa fa-spinner fa-spin");
            $("i", e).css({ right: 0, left: 0 });
        
            setTimeout(() => {
                dtInstance.button(".buttons-excel").trigger();
                $("i", e).removeClass("fa fa-spinner fa-spin").addClass("fa fa-print");
                $("i", e).css({ top: "50%", left: "50%" });
            }, 150);
            export_log(filters,"Manpower Report Coverage: All", "excel",vmFilteredContent.count); // This is for all
        }
    }
});

$.validate({
    form: '#frm-filter-hris-manpower',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        var currentForm = form[0];
        var formMethod = currentForm.method;
        var formData = $(currentForm).serialize();

        vmFilteredContent.rows = Object.assign({});
        vmFilteredContent.count = 0;
        vmFilteredContent.filtered_options = Object.assign({});

        vmRenderedContent.headers = [];
        vmRenderedContent.count = 0;
        vmRenderedContent.filter = Object.assign({});

        $.ajax({
            url: baseUrl("hris/reports/generate_manpower_report"),
            type: formMethod,
            dataType: "json",
            data: formData,
            beforeSend: function () {
                vmFilteredContent.loadingContent = true;
                $(currentForm)
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                vmFilteredContent.rows = Object.assign({});
                vmFilteredContent.count = 0;
                if (json.response) {
                    let tempHeaders = [];
                    let companyIds = [];
                    let departmentId = 0;
                    let locationId = 0;

                    if(typeof json.data !== "undefined" && Object.keys(json.data).length > 0){
                        const companyRecords = json.data;
                        $.each(companyRecords, function(_kk, vv){ 
                            tempHeaders.push({ id: vv.id, title: vv.code});
                            companyIds.push(vv.id);
                            departmentId = vv.department;
                            locationId = vv.location ? vv.location : null;
                        });
                        vmFilteredContent.rows = Object.assign({}, companyRecords);
                        vmFilteredContent.count = json.count;
                        vmFilteredContent.filtered_options = json.filtered_options;

                        vmRenderedContent.headers = tempHeaders;
                        vmRenderedContent.count = tempHeaders.length;
                        vmRenderedContent.filter = Object.assign({}, {company_id: companyIds, [_csrf_token]: _csrf_hash, department_id: departmentId, location_name: locationId }, json.filtered_options);
                    }

                    setTimeout(function(){
                        vmFilteredContent.renderDataTable();
                        vmFilteredContent.dtInstance.clear();
                        vmFilteredContent.dtInstance.rows.add(json.data).draw();
                        vmRenderedContent.generateManpowerByCompany();
                    }, 500);
                    toastr.success(json.toastr_msg, "Filtered Options");
                }else{ toastr.error(json.toastr_msg, "Filtered Options"); }
                vmFilteredContent.loadingContent = false;
                $(currentForm)
                    .find(".btn-submit")
                    .removeClass("m-btn--custom m-loader m-loader--light m-loader--right")
                    .prop("disabled", false);
            }
        });
        
        return false;
    }
});

async function export_log(filters, type, name, count) {
    try {
        const response = await $.ajax({
            url: siteUrl("hris/reports/log_export") + '?t=' + new Date().getTime(),
            type: "POST",
            data: { 
                filters,
                type: type,
                name: name,
                count: count,
                csrf_token: _csrf_hash 
            },
            // dataType: 'json'
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            },
        });
        return response;
    } catch (error) {
        console.error('Error exporting log:', error);
        throw error;
    }
}
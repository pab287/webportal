const filterDriversLicense = $("#tempFilterDriversLicense");
const filterTraining = $("#tempFilterTrainings");
const filterHired = $("#tempFilter");
const filterCertificate = $("#tempFilterCertificate");
let globalDepartmentId = 0;
let _companies = [], _departments = [], _positions = [];

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){ _companies = _tempContentData.company; }
    if(typeof _tempContentData.department !== "undefined" && _tempContentData.department.length > 0){ _departments = _tempContentData.department; }
    if(typeof _tempContentData.position !== "undefined" && _tempContentData.position.length > 0){ _positions = _tempContentData.position; }
}

toastr.options = { positionClass: "toast-bottom-right" };

if(typeof filterHired !== "undefined" && filterHired.length == 1){
$("#date-range", filterHired).val("");
    const initDateRangePicker = function(destroy=false){
        if(destroy){ 
            $("#date-picker", filterHired).daterangepicker("destroy"); 
            $("#date-range", filterHired).val("");
        }
        $("#date-picker", filterHired)
        .daterangepicker({
            maxDate: moment().format("MM/DD/YYYY"),
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            locale: { format: 'MM/DD/YYYY' }
        }).on('apply.daterangepicker', function (ev, picker) {
            $("#date-range", filterHired)
            .val(picker.startDate.format('MMM. DD, YYYY') + ' - ' + picker.endDate.format('MMM. DD, YYYY'))
            .validate();
        });
    }
    $("#company", filterHired).select2({
        width: '100%',
        data: _companies,
        placeholder: 'Select an option',
        allowClear: true,
    }).on("select2:select", function (e) {
        $(e.target).validate();
    });

    $("#department", filterHired).select2({
        width: '100%',
        data: _departments,
        placeholder: 'Select an option',
        allowClear: true,
    }).on("select2:select", function (e) {
        $(e.target).validate();
    });

    $("#position", filterHired).select2({
        width: '100%',
        data: _positions,
        placeholder: 'Select an option',
        allowClear: true,
    }).on("select2:select", function (e) {
        $(e.target).validate();
    });

    const temporaryFilterByHired = new Vue({
        el: "#tempFilterBy",
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

    var resetFilter = function (event) {
        const form = $(event).closest("form");
        if (typeof form !== "undefined" && form.length == 1) {
            form.find("input#hired_employees")[0].click();
            form.find("input#hired_sort")[0].click();
            form.find("input#ascending_sort")[0].click();
            initDateRangePicker(true);
            temporaryFilterByHired.all_filter = "all";
            const select2 = form.find("#company, #department, #position");
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

    initDateRangePicker();
}

if(typeof filterTraining !== "undefined"){
    $("#date-range", filterTraining).val("");
    const initDateRangePickerTraining = function(destroy=false){
        if(destroy){ 
            $("#date-picker", filterTraining).daterangepicker("destroy"); 
            $("#date-range", filterTraining).val("");
        }
        $("#date-picker", filterTraining)
        .daterangepicker({
            maxDate: moment().format("MM/DD/YYYY"),
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            locale: { format: 'MM/DD/YYYY' }
        }).on('apply.daterangepicker', function (ev, picker) {
            $("#date-range", filterTraining)
            .val(picker.startDate.format('MMM. DD, YYYY') + ' - ' + picker.endDate.format('MMM. DD, YYYY'))
            .validate();
        });
    }

    $("#company", filterTraining).select2({
        width: '100%',
        data: _companies,
        placeholder: 'Select an option',
        allowClear: true,
    }).on("select2:select", function (e) {
        $(e.target).validate();
    });

    $("#department", filterTraining).select2({
        width: '100%',
        data: _departments,
        placeholder: 'Select an option',
        allowClear: true,
    }).on("select2:select", function (e) {
        $(e.target).validate();
    });

    $("#position", filterTraining).select2({
        width: '100%',
        data: _positions,
        placeholder: 'Select an option',
        allowClear: true,
    }).on("select2:select", function (e) {
        $(e.target).validate();
    });

    const temporaryFilterByTraining = new Vue({
        el: "#tempFilterByTraining",
        data: { all_filter: "all" },
        watch: { 
            all_filter(nValue){
                if(nValue == "date_range"){ setTimeout(function(){ 
                    initDateRangePickerTraining(true); 
                    toastr.info("Rendering Date Range Picker", "Filter By - Date Range");
                }, 500); }
            }
        }
    });

    var resetFilterTraining = function (event) {
        const form = $(event).closest("form");
        if (typeof form !== "undefined" && form.length == 1) {
            initDateRangePickerTraining(true);
            temporaryFilterByTraining.all_filter = "all";
            const select2 = form.find("#company, #department, #position");
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

    initDateRangePickerTraining();
}


if(typeof filterDriversLicense !== "undefined" && filterDriversLicense.length == 1){
    $("#date-range", filterDriversLicense).val("");
    const initDateRangePickerLicenses = function(destroy=false){
        if(destroy){ 
            $("#date-picker", filterDriversLicense).daterangepicker("destroy"); 
            $("#date-range", filterDriversLicense).val("");
        }
        $("#date-picker", filterDriversLicense)
        .daterangepicker({
            maxDate: moment().format("MM/DD/YYYY"),
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            locale: { format: 'MM/DD/YYYY' }
        }).on('apply.daterangepicker', function (ev, picker) {
            $("#date-range", filterDriversLicense)
            .val(picker.startDate.format('MMM. DD, YYYY') + ' - ' + picker.endDate.format('MMM. DD, YYYY'))
            .validate();
        });
    }

    $("#company", filterDriversLicense).select2({
        width: '100%',
        data: _companies,
        placeholder: 'Select an option',
        allowClear: true,
    }).on("select2:select", function (e) {
        $(e.target).validate();
    });

    $("#department", filterDriversLicense).select2({
        width: '100%',
        data: _departments,
        placeholder: 'Select an option',
        allowClear: true,
    }).on("select2:select", function (e) {
        $(e.target).validate();
    });

    $("#position", filterDriversLicense).select2({
        width: '100%',
        data: _positions,
        placeholder: 'Select an option',
        allowClear: true,
    }).on("select2:select", function (e) {
        $(e.target).validate();
    });

    const temporaryFilterByDriversLicense = new Vue({
        el: "#tempFilterByDriversLicense",
        data: { all_filter: "all" },
        watch: { 
            all_filter(nValue){
                if(nValue == "date_range"){ setTimeout(function(){ 
                    initDateRangePickerLicenses(true); 
                    toastr.info("Rendering Date Range Picker", "Filter By - Date Range");
                }, 500); }
            }
        }
    });

    var resetFilterDriversLicense = function (event) {
        const form = $(event).closest("form");
        if (typeof form !== "undefined" && form.length == 1) {
            initDateRangePickerLicenses(true);
            temporaryFilterByDriversLicense.all_filter = "all";
            const select2 = form.find("#company, #department, #position");
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

    initDateRangePickerLicenses();
}

if(typeof filterCertificate !== "undefined"){
    $("#date-range", filterCertificate).val("");

    var _data = [
        { id: "COMPANY SPONSORED - INTERNAL", text: "COMPANY SPONSORED - INTERNAL" },
        { id: "COMPANY SPONSORED - EXTERNAL", text: "COMPANY SPONSORED - EXTERNAL" },
        { id: "PERSONAL", text: "PERSONAL" },
    ];

    const initDateRangePickerCertificate = function(destroy=false){
        if(destroy){ 
            $("#date-picker", filterCertificate).daterangepicker("destroy"); 
            $("#date-range", filterCertificate).val("");
        }
        $("#date-picker", filterCertificate)
        .daterangepicker({
            maxDate: moment().format("MM/DD/YYYY"),
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            locale: { format: 'MM/DD/YYYY' }
        }).on('apply.daterangepicker', function (ev, picker) {
            $("#date-range", filterCertificate)
            .val(picker.startDate.format('MMM. DD, YYYY') + ' - ' + picker.endDate.format('MMM. DD, YYYY'))
            .validate();
        });
    }

    $("#company", filterCertificate).select2({
        width: '100%',
        data: _companies,
        placeholder: 'Select an option',
        allowClear: true,
    }).on("select2:select", function (e) {
        $(e.target).validate();
    });

    $("#department", filterCertificate).select2({
        width: '100%',
        data: _departments,
        placeholder: 'Select an option',
        allowClear: true,
    }).on("select2:select", function (e) {
        $(e.target).validate();
    });

    $("#position", filterCertificate).select2({
        width: '100%',
        data: _positions,
        placeholder: 'Select an option',
        allowClear: true,
    }).on("select2:select", function (e) {
        $(e.target).validate();
    });

    $("#type", filterCertificate).select2({
        width: '100%',
        data: _data,
        placeholder: 'Select an option',
        allowClear: true,
    }).on("select2:select", function (e) {
        $(e.target).validate();
    });

    const temporaryFilterByCertificate = new Vue({
        el: "#tempFilterByCertificate",
        data: { all_filter: "all" },
        watch: { 
            all_filter(nValue){
                if(nValue == "date_range"){ setTimeout(function(){ 
                    initDateRangePickerCertificate(true); 
                    toastr.info("Rendering Date Range Picker", "Filter By - Date Range");
                }, 500); }
            }
        }
    });

    var resetFilterCertificate = function (event) {
        const form = $(event).closest("form");
        if (typeof form !== "undefined" && form.length == 1) {
            initDateRangePickerCertificate(true);
            temporaryFilterByCertificate.all_filter = "all";
            const select2 = form.find("#company, #department, #position, #type");

            $("#licenses").val('');
            
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

    initDateRangePickerCertificate();
}

const vmFilteredContentLicenses = new Vue({
    el: "#filteredContentLicenses",
    data: { rows: {}, count: 0, filtered_options: {}, dtInstance: {} },
    methods: {
        renderDataTable: function(){
            const _this = this;
            const filteredOption = _this.filtered_options;
            const allFilterOption = filteredOption.filter_by === "all";

            const columns = [
                { data: null, title: "#", width: "3%", render: function(_data, _type, row, meta){
                    const expired = parseInt(row.expiration_flag) == 1;
                    const tempCount = parseInt(meta.row) + 1;
                    return expired ? `<span class='m--font-danger m--font-boldest'>${tempCount}</span>`: tempCount;
                } },
                { data: "employee_name", title: "Employee Name", visible: false },
                { data: "company", title: "Company", visible: false },
                { data: "department", title: "Department", visible: false },
                { data: "position", title: "Position" },
                { data: "expiration_date", title: "Expiration Date", width: "12%", className: "text-center" },
                { data: "license_no", title: "License Number", width: "17%", className: "text-left" },
                { data: "restriction", title: "Restriction Code", width: "17%" , className: "text-right" },
            ];

            const dtTrainingReport = $("#table-drivers_license_report");
            if(typeof dtTrainingReport !== "undefined" && dtTrainingReport.length > 0){
                if ( $.fn.DataTable.isDataTable( '#table-drivers_license_report' ) ) { _this.dtInstance.destroy(); }

                _this.dtInstance = dtTrainingReport.DataTable({
                    dom: "rtl", paging: false, ordering: false, columns, 
                    rowGroup: { 
                        startRender: function ( _row, group ) {
                            let html = ``;
                            const arrHeaders = group.split("|");
                            $.each(arrHeaders, function(_kk, vv){
                                if(vv){
                                    html += `<span>${vv}</span>`;
                                }
                            });
                            return html ? html : group;
                        },
                        dataSrc: [ 'employee_header' ] 
                    }, buttons: [{
                        extend: 'print',
                        text: 'PRINT',
                        title: function () {
                            const timeStamp = moment().format("MM/DD/YYYY hh:mm A");
                            const tempCoverage = allFilterOption ? ``: `<div class="m--regular-font-size-sm1 mt-2 text-center">
                                COVERAGE DATE: ${filteredOption.start_date_formatted} - ${filteredOption.end_date_formatted}
                            </div>`;
                            return `<div class="m--regular-font-size-lg1 text-center">DRIVERS LICENSE REPORT</div>${tempCoverage}
                            <div class="m--regular-font-size-sm1 mt-1 text-center">TOTAL NUMBER OF ENTRIES: ${filteredOption.total_entries}</div>
                            <div class="m--regular-font-size-sm2 mt-1 text-center">GENERATED AS OF ${timeStamp}</div>`;
                        },
                        exportOptions: { stripHtml: false },
                        customize: function (win) {
                            var css = `@page { size: portrait; margin: 0.5cm; } 
                                .dt-print-view table { font-size: 10px; } 
                                .dt-print-view table.dataTable tfoot tr:first-child th{ border-top: 1px solid #000000; }
                                .dt-print-view table.dataTable tfoot tr:first-child th{ border-bottom: 4px double #000000; }
                                .dt-print-view table.dataTable .m--font-danger{ color: #FF0000 !important; }`,
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
                            const tempTHead = $(tempTable).find("thead th:not(:first-child):not(:last-child)");
                            tempTHead.removeClass("text-right").addClass("text-left");
                        }
                    }, { extend: 'excel' }],
                    columnDefs: [{
                        targets: "_all",
                        render: function(data, _type, row, meta){
                            let tempData = data;
                            const expired = parseInt(row.expiration_flag) == 1;
                            return expired ? `<span class='m--font-danger m--font-boldest'>${tempData}</span>`: tempData;
                        }
                    }],
                });
            }
        }, printDriversLicense: function(e){
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
        }, exportExcelDriversLicense: function(e) {
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
        }
    }
});

const vmFilteredContentTraining = new Vue({
    el: "#filteredContentTraining",
    data: { rows: {}, count: 0, filtered_options: {}, dtInstance: {} },
    methods: {
        renderDataTable: function(){
            const _this = this;
            const filteredOption = _this.filtered_options;
            const allFilterOption = filteredOption.filter_by === "all";

            const columns = [
                { data: null, title: "#", render: function(_data, _type, _row, meta){
                    const tempCount = parseInt(meta.row) + 1;
                    return tempCount;
                } },
                { data: "employee_name", title: "Employee Name", visible: false },
                { data: "company", title: "Company", visible: false },
                { data: "department", title: "Department", visible: false },
                { data: "position", title: "Position", visible: false },
                { data: "train_from", title: "From", width: "8%" },
                { data: "train_to", title: "To", width: "8%" },
                { data: "training", title: "Title", className: "text-left" },
                { data: "institution", title: "Institution", className: "text-left" },
                { data: "conductor", title: "Conductor", className: "text-left" },
                { data: "venue", title: "Venue", className: "text-left"},
            ];

            const dtTrainingReport = $("#table-training_report");
            if(typeof dtTrainingReport !== "undefined" && dtTrainingReport.length > 0){
                if ( $.fn.DataTable.isDataTable( '#table-training_report' ) ) { _this.dtInstance.destroy(); }

                _this.dtInstance = dtTrainingReport.DataTable({
                    dom: "rtl", paging: false, ordering: false, columns, 
                    rowGroup: { 
                        startRender: function ( _row, group ) {
                            let html = ``;
                            const arrHeaders = group.split("|");
                            $.each(arrHeaders, function(_kk, vv){
                                if(vv){
                                    html += `<span>${vv}</span>`;
                                }
                            });
                            return html ? html : group;
                        },
                        dataSrc: [ 'employee_header' ] 
                    }, buttons: [{
                        extend: 'print',
                        text: 'PRINT',
                        title: function () {
                            const timeStamp = moment().format("MM/DD/YYYY hh:mm A");
                            const tempCoverage = allFilterOption ? ``: `<div class="m--regular-font-size-sm1 mt-2 text-center">
                                COVERAGE DATE: ${filteredOption.start_date_formatted} - ${filteredOption.end_date_formatted}
                            </div>`;
                            return `<div class="m--regular-font-size-lg1 text-center">TRAINING/SEMINARS REPORT</div>${tempCoverage}
                            <div class="m--regular-font-size-sm1 mt-1 text-center">TOTAL NUMBER OF ENTRIES: ${filteredOption.total_entries}</div>
                            <div class="m--regular-font-size-sm2 mt-1 text-center">GENERATED AS OF ${timeStamp}</div>`;
                        },
                        exportOptions: { stripHtml: false },
                        customize: function (win) {
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
                            const tempTHead = $(tempTable).find("thead th:not(:first-child)");
                            tempTHead.removeClass("text-right").addClass("text-left");
                        }
                    }, { extend: 'excel' }]
                });
            }
        }, printTrainingSeminar: function(e){
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
        }, exportExcelTrainingSeminar: function(e) {
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
        }
    }
});

const vmFilteredContent = new Vue({
    el: "#filteredContent",
    data: { rows: {}, count: 0, filtered_options: {}, dtInstance: {} },
    methods: {
        renderDataTable: function(){
            const _this = this;
            const filteredOption = _this.filtered_options;
            const allFilterOption = filteredOption.filter_by === "all";

            const columns = [
                { data: "idno", title: "ID #" },
                { data: "employee_name", title: "Employee Name" },
                { data: "company", title: "Company" },
                { data: "department", title: "Department" },
                { data: "position", title: "Position"},
                { data: "department_head", title: "Department Head" },
            ];

            if(typeof filteredOption.filtered_column !== "undefined" && typeof filteredOption.filtered_by_date !== "undefined"){
                columns.push({ data: filteredOption.filtered_column, title: filteredOption.filtered_by_date, width: "10%" })
            }
            const dtComprehensiveReport = $("#table-comprehensive_report");
            if(typeof dtComprehensiveReport !== "undefined" && dtComprehensiveReport.length > 0){
                if ( $.fn.DataTable.isDataTable( '#table-comprehensive_report' ) ) { _this.dtInstance.destroy(); }

                _this.dtInstance = dtComprehensiveReport.DataTable({
                    dom: "rtl", paging: false, ordering: false, columns,
                    buttons: [{
                        extend: 'print',
                        text: 'PRINT',
                        title: function () {
                            const filteredBy = filteredOption.filtered_by;
                            const timeStamp = moment().format("MM/DD/YYYY hh:mm A");
                            const tempCoverage = allFilterOption ? ``: `<div class="m--regular-font-size-sm1 mt-2 text-center">
                                COVERAGE DATE: ${filteredOption.start_date_formatted} - ${filteredOption.end_date_formatted}
                            </div>`;

                            return `<div class="m--regular-font-size-lg1 text-center">COMPREHENSIVE REPORT</div>
                                <div class="m--regular-font-size-sm1 mt-2 text-center">FILTERED BY: ${filteredBy.toUpperCase()}</div>${tempCoverage}
                                <div class="m--regular-font-size-sm1 mt-1 text-center">TOTAL NUMBER OF ENTRIES: ${filteredOption.total_entries}</div>
                                <div class="m--regular-font-size-sm2 mt-1 text-center">GENERATED AS OF ${timeStamp}</div>`;
                        },
                        exportOptions: { stripHtml: false },
                        customize: function (win) {
                            var css = `@page { size: portrait; margin: 0.5cm; } 
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
                            const tempTHead = $(tempTable).find("thead th:not(:first-child)");
                            tempTHead.removeClass("text-right").addClass("text-center");
                        }
                    }, { extend: 'excel' }]
                });
            }
        }, printHiredSeparated: function(e){
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
        }, exportExcelHiredSeparated: function(e) {
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
        }
    }
});

const vmFilteredContentCertificate = new Vue({
    el: "#filteredContentCertification",
    data: { rows: {}, count: 0, filtered_options: {}, dtInstance: {} },
    methods: {
        renderDataTable: function(){
            const _this = this;
            const filteredOption = _this.filtered_options;
            const allFilterOption = filteredOption.filter_by === "all";

            const columns = [
                { data: null, title: "#", render: function(_data, _type, _row, meta){
                    const expired = _row.expiration_date == 'No Expiry' ? 0 : 1;
                    const tempCount = parseInt(meta.row) + 1;
                    return expired == 1 ? `<span class='m--font-danger m--font-boldest'>${tempCount}</span>`: tempCount;
                } },
                { data: "employee_name", title: "Employee Name", visible: false },
                { data: "company", title: "Company", visible: false },
                { data: "department", title: "Department", visible: false },
                { data: "position", title: "Position", visible: false },
                { data: "license", title: "License/Certificate", width: "8%",},
                { data: "types", title: "Type", width: "10%" },
                { data: "exam_place", title: "Exam Place", width: "8%" },
                { data: "rating", title: "Rating", className: "text-left" },
                { data: "release_date", title: "Released Date", className: "text-left" },
                { data: "exam_date", title: "Exam Date", className: "text-left" },
                { data: "license_no", title: "License Cert. No.", className: "text-left"},
                { data: "expiration_date", title: "Expiry Date", className: "text-left",
                    // render: function(data, type, row, meta){

                        

                    //     // var html = '';
                    //     // console.log(meta.row);
                    //     // if(data < moment().format('YYYY-MM-DD')){
                    //     //     html = '<p style="color: red">' + data + '</p>';
                    //     // }else{
                    //     //     html = data;
                    //     // }
                    //     // return html;
                    // }
                },
            ];

            const dtCertificateReport = $("#table-certificate_report");
            if(typeof dtCertificateReport !== "undefined" && dtCertificateReport.length > 0){
                if ( $.fn.DataTable.isDataTable( '#table-certificate_report' ) ) { _this.dtInstance.destroy(); }

                _this.dtInstance = dtCertificateReport.DataTable({
                    dom: "rtl", paging: false, ordering: false, columns, 
                    rowGroup: { 
                        startRender: function ( _row, group ) {
                            let html = ``;
                            const arrHeaders = group.split("|");
                            $.each(arrHeaders, function(_kk, vv){
                                if(vv){
                                    html += `<span>${vv}</span>`;
                                }
                            });
                            return html ? html : group;
                        },
                        dataSrc: [ 'employee_header' ] 
                    }, buttons: [{
                        extend: 'print',
                        text: 'PRINT',
                        title: function () {
                            const timeStamp = moment().format("MM/DD/YYYY hh:mm A");
                            const tempCoverage = allFilterOption ? ``: `<div class="m--regular-font-size-sm1 mt-2 text-center">
                                COVERAGE DATE: ${filteredOption.start_date_formatted} - ${filteredOption.end_date_formatted}
                            </div>`;
                            return `<div class="m--regular-font-size-lg1 text-center">LICENSES AND CERTIFICATIONS REPORT</div>${tempCoverage}
                            <div class="m--regular-font-size-sm1 mt-1 text-center">TOTAL NUMBER OF ENTRIES: ${filteredOption.total_entries}</div>
                            <div class="m--regular-font-size-sm2 mt-1 text-center">GENERATED AS OF ${timeStamp}</div>`;
                        },
                        exportOptions: { stripHtml: false },
                        customize: function (win) {
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
                            const tempTHead = $(tempTable).find("thead th:not(:first-child)");
                            tempTHead.removeClass("text-right").addClass("text-left");
                        }
                    }, { extend: 'excel' }],
                    columnDefs: [{
                        targets: "_all",
                        render: function(data, _type, row, meta){
                            let tempData = data;
                            const expired = row.expiration_date != 'No Expiry' && row.expiration_date < moment().format('YYYY-MM-DD') ? 1 : 0;
                            return expired ? `<span class='m--font-danger m--font-boldest'>${tempData}</span>`: tempData;
                        }
                    }],
                });
            }
        }, printCertificateSeminar: function(e){
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
        }, exportExcelCertificateSeminar: function(e) {
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
        }
    }
});

$.validate({
    form: '#frm-filter-hris-comprehensive',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        var currentForm = form[0];
        var formMethod = currentForm.method;
        var formData = $(currentForm).serialize();

        $.ajax({
            url: siteUrl("hris/reports/generate_comprehensive_report"),
            type: formMethod,
            dataType: "json",
            data: formData,
            beforeSend: function () {
                $(currentForm)
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                vmFilteredContent.rows = Object.assign({});
                vmFilteredContent.filtered_options = Object.assign({});
                vmFilteredContent.count = 0;

                if (json.response) {
                    vmFilteredContent.rows = Object.assign({}, json.data);
                    vmFilteredContent.filtered_options = Object.assign({}, json.filtered_options);
                    vmFilteredContent.count = json.count;

                    setTimeout(function(){
                        vmFilteredContent.renderDataTable();
                        vmFilteredContent.dtInstance.clear();
                        vmFilteredContent.dtInstance.rows.add(json.data).draw();
                    }, 500);
                    toastr.success(json.toastr_msg, "Filtered Options");
                }else{ toastr.error(json.toastr_msg, "Filtered Options"); }
                
                $(currentForm)
                    .find(".btn-submit")
                    .removeClass("m-btn--custom m-loader m-loader--light m-loader--right")
                    .prop("disabled", false);
            }
        });
        
        return false;
    }
});

$.validate({
    form: '#frm-filter-hris-trainings',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        var currentForm = form[0];
        var formMethod = currentForm.method;
        var formData = $(currentForm).serialize();

        $.ajax({
            url: siteUrl("hris/reports/generate_training_seminars_report"),
            type: formMethod,
            dataType: "json",
            data: formData,
            beforeSend: function () {
                $(currentForm)
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                vmFilteredContentTraining.rows = Object.assign({});
                vmFilteredContentTraining.filtered_options = Object.assign({});
                vmFilteredContentTraining.count = 0;

                if (json.response) {
                    vmFilteredContentTraining.rows = Object.assign({}, json.data);
                    vmFilteredContentTraining.filtered_options = Object.assign({}, json.filtered_options);
                    vmFilteredContentTraining.count = json.count;

                    setTimeout(function(){
                        vmFilteredContentTraining.renderDataTable();
                        vmFilteredContentTraining.dtInstance.clear();
                        vmFilteredContentTraining.dtInstance.rows.add(json.data).draw();
                    }, 500);
                    toastr.success(json.toastr_msg, "Filtered Options");
                }else{ toastr.error(json.toastr_msg, "Filtered Options"); }
                
                $(currentForm)
                    .find(".btn-submit")
                    .removeClass("m-btn--custom m-loader m-loader--light m-loader--right")
                    .prop("disabled", false);
            }
        });

        return false;
    }
});

$.validate({
    form: '#frm-filter-hris-drivers_license',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        var currentForm = form[0];
        var formMethod = currentForm.method;
        var formData = $(currentForm).serialize();

        $.ajax({
            url: siteUrl("hris/reports/generate_drivers_license_report"),
            type: formMethod,
            dataType: "json",
            data: formData,
            beforeSend: function () {
                $(currentForm)
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                vmFilteredContentLicenses.rows = Object.assign({});
                vmFilteredContentLicenses.filtered_options = Object.assign({});
                vmFilteredContentLicenses.count = 0;

                if (json.response) {
                    vmFilteredContentLicenses.rows = Object.assign({}, json.data);
                    vmFilteredContentLicenses.filtered_options = Object.assign({}, json.filtered_options);
                    vmFilteredContentLicenses.count = json.count;

                    setTimeout(function(){
                        vmFilteredContentLicenses.renderDataTable();
                        vmFilteredContentLicenses.dtInstance.clear();
                        vmFilteredContentLicenses.dtInstance.rows.add(json.data).draw();
                    }, 500);
                    toastr.success(json.toastr_msg, "Filtered Options");
                }else{ toastr.error(json.toastr_msg, "Filtered Options"); }
                
                $(currentForm)
                    .find(".btn-submit")
                    .removeClass("m-btn--custom m-loader m-loader--light m-loader--right")
                    .prop("disabled", false);
            }
        });

        return false;
    }
});

$.validate({
    form: '#frm-filter-hris-certificate',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        var currentForm = form[0];
        var formMethod = currentForm.method;
        var formData = $(currentForm).serialize();

        $.ajax({
            url: siteUrl("hris/reports/generate_licenses_certificate_report"),
            type: formMethod,
            dataType: "json",
            data: formData,
            beforeSend: function () {
                $(currentForm)
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                vmFilteredContentCertificate.rows = Object.assign({});
                vmFilteredContentCertificate.filtered_options = Object.assign({});
                vmFilteredContentCertificate.count = 0;

                if (json.response) {
                    vmFilteredContentCertificate.rows = Object.assign({}, json.data);
                    vmFilteredContentCertificate.filtered_options = Object.assign({}, json.filtered_options);
                    vmFilteredContentCertificate.count = json.count;

                    setTimeout(function(){
                        vmFilteredContentCertificate.renderDataTable();
                        vmFilteredContentCertificate.dtInstance.clear();
                        vmFilteredContentCertificate.dtInstance.rows.add(json.data).draw();
                    }, 500);
                    toastr.success(json.toastr_msg, "Filtered Options");
                }else{ toastr.error(json.toastr_msg, "Filtered Options"); }
                
                $(currentForm)
                    .find(".btn-submit")
                    .removeClass("m-btn--custom m-loader m-loader--light m-loader--right")
                    .prop("disabled", false);
            }
        });

        return false;
    }
});
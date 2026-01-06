let search_val = "";
let company = [];

let _companies = [];
let _payoutSchedule = [];

let employee = [];
let date_from = "";
let date_to = "";
let isCollapsedPortlet = true;
let tempRangeDates = {
    min_date: moment().startOf('month').format("MM/DD/YYYY"),
    max_date: moment().endOf('month').format("MM/DD/YYYY"),
};
let showPosted = "_all";
let psEmployeeGroup = [];
let noContAcctNo = [];
let globalPrintableSignatory = [];
let globalWithLoans = null;

// for show modal once
let _show_modal_once = false;
let _show_modal_loans_once = false;
let _md5_key_filter = null;

const _tblPayrollSheet = $("#table-payroll-sheet");
const _tblAbsenteeReport = $("#table-absentee_report");
const _tblContributionReport = $("#table-contribution_report");
const _tblSiteLocationReport = $("#table-site_location_report");

const cbSelectAll = $("#cb-select-all");
const manageCustomAdjustmentsModal = $("#manage-custom-adjustments-modal");
const manageCreatedAdjustmentsModal = $("#manage-created-adjustments-modal");
const manageCustomRateAdjustmentsModal = $("#manage-custom-rate-adjustments-modal");
const confirmDeleteCreatedAdjustmentModal = $("#modal-confirm-delete-created-adjustment");
const confirmDeleteCustomAdjustmentModal = $("#modal-confirm-delete-custom-adjustment");
const confirmApprovalCreatedAdjustmentModal = $("#modal-confirm-approval-created-adjustment");
const psNotificationModal = $("#modal-ps--notification");
const psSignatoryModal = $("#modal-ps--signatory");
const psContributionModal = $("#modal-ps--contribution_report");
const psSiteLocationModal = $("#modal-ps--site_location_report");
const psFilterHistoryModal = $("#modal-ps--filter_history");
const psResetSignatoryModal = $("#modal-ps--reset-signatory");
const confirmPayrollPosting = $("#confirm-payroll-posting");
const confirmUndoPostingModal = $("#confirm-undo-posting");
const viewTimesheetModal = $("#view-timesheet-modal");
const _tblPortletPS = $("#m_portlet_tools-payroll_sheet").mPortlet();

let dtEmployeeTimesheet = null;
let selectedCompany = null;
let dtCreatedAdjustments;
let _globalFooterHtml = null;
let _globalFooterAdjustments = { sss: 0, sss_prov: 0, phic: 0, hdmf: 0, tax: 0, total_loans: 0 };
let _dtRowSSS = [], _dtRowSSS_PROV = [], _dtRowPHIC = [], _dtRowHDMF = [], _dtRowTAX = [], _dtRowLOAN = [];
let _tempLastRow = [];

let globalGrandTotal = {};
const exportOptions = {
    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
};

let globalFilterOptions = {};

const vmPsFilterHistory = new Vue({
    el: "#tempFilterHistory",
    data: { count: 0, rows: [] },
    methods: {
        renderModalHistory: function(){
            const _this = this;
            vmPsFilterHistoryModal.count = _this.count;
            vmPsFilterHistoryModal.rows = _this.rows;
            psFilterHistoryModal.modal("show");
        }
    }
});

let psOccurrence = {};
if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){ _companies = _tempContentData.company; }
if(typeof _tempContentData.payout_schedule !== "undefined" && _tempContentData.payout_schedule.length > 0){ 
    _payoutSchedule = _tempContentData.payout_schedule; 
    $.each(_tempContentData.payout_schedule, function(ii, vv){
        const tempKey = vv.id;
        const occurrence = vv.occurrence;
        let nOccurrance = [];
        for(let i=1; i <= occurrence; i++){
            const _tempOrdinal = moment.localeData().ordinal(i);
            const _tempRange = { id: i, text: _tempOrdinal };
            nOccurrance.push(_tempRange);
        }
        
        psOccurrence = Object.assign({}, psOccurrence, { [tempKey]: nOccurrance });
    });
}
if(typeof _tempContentData.filter_history !== "undefined" && _tempContentData.filter_history.length > 0){ 
    vmPsFilterHistory.rows = _tempContentData.filter_history;
    vmPsFilterHistory.count = _tempContentData.filter_history.length;
}
toastr.options = { newestOnTop: true, positionClass: "toast-bottom-right" };

$('body').tooltip({
    selector: '[data-toggle="m-tooltip"]'
});

$("#ExportExcel").on("click", function () {
    tblPayrollSheet.button('.buttons-excel').trigger();
});

$("#ExportCSV").on("click", function () {
    tblPayrollSheet.button('.buttons-csv').trigger();
});

$("#ExportPDF").on("click", function () {
    tblPayrollSheet.button('.buttons-pdf').trigger();
});

$("#PrintSheet").on("click", function () {
    tblPayrollSheet.button('.buttons-print').trigger();
});


$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblPayrollSheet.ajax.reload();
});

$("#reload_dtTbl").on("click", function () {
    tblPayrollSheet.ajax.reload();
});

const select2Employees = function () {
    $("#employees")
        .select2({
            placeholder: 'Select an option',
            width: '100%',
            ajax: {
                url: baseUrl("payroll/select_employee"),
                dataType: "json",
                delay: 250,
                global: false,
                processResults: function (data) {
                    return data;
                }
            }
        });
}

const resetFilter = function (event) {
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        const select2 = form.find("#employees, #payroll_group");
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
        psEmployeeGroup = [];
    }
}


$("#payroll_group").select2({
    placeholder: 'Select an option',
    width: '100%',
    ajax: {
        url: baseUrl("payroll/select_payroll_group"),
        dataType: "json",
        type: 'get',
        delay: 250,
        global: false,
        data: function (params) {
            params.company_id = $("form#frm-filter select#company").val();
            return params;
        },
        processResults: function (data) {
            return data;
        }
    }
}).on("select2:select", function (e) {
    const _this = this;
    const tempVal = $(_this).val();
    const data = e.params.data;
    let employees = [];
    if (typeof data.employees == "object" && typeof data.employees !== "undefined") { employees = data.employees; }
    if (tempVal.length > 1) {
        $.ajax({
            url: baseUrl("payroll/get_payroll_group_multiple"),
            type: "post",
            dataType: "json",
            data: { group_id: tempVal, [_csrf_token]: _csrf_hash },
            success: function (json) {
                if (json.response) {
                    const tempData = json.data;
                    if (typeof tempData == "object" && typeof tempData !== "undefined") {
                        const tempEmployeeSelector = $("form#frm-filter select#employees");
                        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                            tempEmployeeSelector.empty();
                            $.each(tempData, function (ii, vv) {
                                const tempOption = new Option(vv.text, vv.id, true, true);
                                tempEmployeeSelector.append(tempOption);
                            });
                            tempEmployeeSelector.prop("disabled", true);
                        }
                    }
                }
            }
        });
    } else {
        if (typeof employees == "object" && typeof employees !== "undefined") {
            const tempEmployeeSelector = $("form#frm-filter select#employees");
            if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                tempEmployeeSelector.empty();
                $.each(employees, function (ii, vv) {
                    const tempOption = new Option(vv.text, vv.id, true, true);
                    tempEmployeeSelector.append(tempOption);
                });
                tempEmployeeSelector.prop("disabled", true);
            }
        }
    }
    if (typeof data.text !== "undefined" && data.text) {
        const tempEmpGroup = data.text;
        let tempIsInArray = $.inArray(tempEmpGroup, psEmployeeGroup);
        if (tempIsInArray == -1) {
            psEmployeeGroup.push(tempEmpGroup);
        }
    }

}).on("select2:unselect", function (e) {
    const _this = this;
    const tempValUnselected = $(_this).val();
    const data = e.params.data;
    if (tempValUnselected.length == 0) {
        const tempEmployeeSelector = $("form#frm-filter select#employees");
        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
            tempEmployeeSelector.prop("disabled", false);
        }
    } else {
        $.ajax({
            url: baseUrl("payroll/get_payroll_group_multiple"),
            type: "post",
            dataType: "json",
            data: { group_id: tempValUnselected, [_csrf_token]: _csrf_hash },
            success: function (json) {
                if (json.response) {
                    const tempData = json.data;
                    if (typeof tempData == "object" && typeof tempData !== "undefined") {
                        const tempEmployeeSelector = $("form#frm-filter select#employees");
                        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                            tempEmployeeSelector.empty();
                            $.each(tempData, function (ii, vv) {
                                const tempOption = new Option(vv.text, vv.id, true, true);
                                tempEmployeeSelector.append(tempOption);
                            });
                            tempEmployeeSelector.prop("disabled", true);
                        }
                    }
                }
            }
        });
    }

    if (typeof data.text !== "undefined" && data.text) {
        const tempEmpGroup = data.text;
        let tempIsInArray = $.inArray(tempEmpGroup, psEmployeeGroup);
        if (tempIsInArray !== -1) {
            const index = psEmployeeGroup.indexOf(tempEmpGroup);
            if (index > -1) { psEmployeeGroup.splice(index, 1); }
        }

    }
});

$("#company")
    .select2({
        placeholder: 'Select an option',
        width: '100%',
        data: _companies,
        allowClear: true,
    })
    .on("select2:select", function (data) {
        selectedCompany = data.params.data;
        $(data.target).validate();
    });

const setPayDate = function (tempStartDate = null) {
    $('#pay-date').find("input").val("");
    $('#pay-date')
        .datepicker("destroy")
        .datepicker({
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            },
            autoclose: true,
            format: 'M. dd, yyyy',
        }).on('changeDate', function (e) {
            const currentTarget = $(e.target);
            const currentDate = e.date;
            if (typeof currentDate !== "undefined" && currentDate) {
                const currentDay = moment(currentDate).format("DD");
                if (typeof currentDay !== "undefined") {
                    if (parseInt(currentDay) > 15) {
                        let dateFrom = moment(currentDate)
                            .startOf('month')
                            .format('MM/DD/YYYY');
                        let dateTo = moment(currentDate)
                            .endOf('month')
                            .format('MM/DD/YYYY');
                        tempRangeDates.min_date = dateFrom;
                        tempRangeDates.max_date = dateTo;
                    } else {
                        let dateFrom = moment(currentDate)
                            .subtract(1, 'months')
                            .startOf('month')
                            .format('MM/DD/YYYY');
                        let dateTo = moment(currentDate)
                            .endOf('month')
                            .format('MM/DD/YYYY');
                        tempRangeDates.min_date = dateFrom;
                        tempRangeDates.max_date = dateTo;
                    }
                    generateDateTimePicker(tempRangeDates.min_date, tempRangeDates.max_date)
                }

            }
            currentTarget.find("input").validate();
        });

        if(tempStartDate){ $("#pay-date").datepicker("setDate", tempStartDate); }
}
setPayDate();

const generateDateTimePicker = function (min = null, max = null, start=null, end=null) {
    let options = {
        minDate: min,
        maxDate: max,
        buttonClasses: 'm-btn btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',
        locale: {
            format: 'MM/DD/YYYY'
        }
    };
    if(start && end){ options = Object.assign({}, options, { startDate: start, endDate: end }); }
    $("#date-range").val("");
    $("#date-picker")
        .daterangepicker(options).on('apply.daterangepicker', function (ev, picker) {
            $("#date-range")
                .val(picker.startDate.format('MMM. DD, YYYY') + ' - ' + picker.endDate.format('MMM. DD, YYYY'))
                .validate();
        });
}
generateDateTimePicker(tempRangeDates.min_date, tempRangeDates.max_date);



function search() {
    employee = $("#employees").val();
    Boolean($("#company").val()) ? company.push($("#company").val()) : $("#company").val();
    Boolean($("#company").text()) ? company.push($("#company").text()) : $("#company").text();
    date_from = $("#date-from").val();
    date_to = $("#date-to").val();

    tblPayrollSheet.ajax.reload();
}

function payroll_post() {
    $.validate({
        form: '#frm-add',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("payroll/payroll_sheet/save"),
                type: "POST",
                dataType: "json",
                data: $("#frm-add").find("input, select").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data.state) {
                        toastr.success(data.toastr_msg, "Successfully saved!", 5000);
                        $("#modal-add").modal("hide");
                        $('#frm-add')[0].reset();
                        $('#employee').text("");
                        tblAllowance.ajax.reload();
                    } else {
                        toastr.error(data.toastr_msg, "Error!", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
}

const form = $("#frm-filter");

let dtAbsenteeReport = _tblAbsenteeReport.DataTable({
    dom: 'Brtlp',
    serverSide: false,
    processing: false,
    autoWidth: false,
    ordering: false,
    buttons: [{
        extend: 'print',
        text: '<i class="fa fa-print mr-1"></i> PRINT',
        header: true,
        className: "btnPrint btn btn-primary mb-2 pull-right",
        title: function () {
            const date_range = $("input[name='date_range']").val();

            let coverage = '', tempPayDate = '';
            if (date_range) {
                const date_range_arr = date_range.split("-");
                coverage = moment(date_range_arr[0]).format("MM/DD/YYYY") + " - " + moment(date_range_arr[1]).format("MM/DD/YYYY");
            }

            let tempPayrollGroup = "";
            const pay_date = $("input[name='pay_date']").val();
            if (pay_date) {
                tempPayDate = moment(pay_date).format("MM/DD/YYYY");
            }
            if (typeof psEmployeeGroup !== "undefined" && typeof psEmployeeGroup == "object" && psEmployeeGroup.length > 0) {
                tempPayrollGroup += psEmployeeGroup.join(" | ");
            }

            let newPayrollGroup = "";
            if (tempPayrollGroup) {
                newPayrollGroup = `<div class="m--regular-font-size-sm1 mt-1">PAYROLL GROUP: <span style='font-weight: 600; text-transform: uppercase;'>${tempPayrollGroup}</span></div>`;
            }
            return `<div class="text-center m--regular-font-size-lg2" style="text-transform: uppercase;">${selectedCompany.description}</div>
                    <div class="text-center m--regular-font-size-sm1 text-muted" style="text-transform: uppercase;">${selectedCompany.company_address}</div>
                    <div class="text-center m--regular-font-size-lg1 mt-2">PAYROLL SHEET - ATTENDANCE REPORT</div>
                    <div class="m--regular-font-size-sm1 mt-2">PAY DATE: ${tempPayDate}</div>
                    <div class="m--regular-font-size-sm1 mt-1">PAY COVERAGE: ${coverage}</div>
                    ${newPayrollGroup}`;
        },
        customize: function (win) {
            win.document.title = "Payroll Sheet Printable Page";
            const css = `@page { size: portrait; margin: 0.5cm; } table { font-size: 12px; }`,
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
        }
    }],
    columns: [
        { data: "id", title: "#", width: "3%", className: "text-center" },
        { data: "employee_name", title: "Employee Name", width: "*" },
        { data: "ewd", title: "# Days", width: "10%", className: "text-center" },
        { data: "late_hours", title: "Late", width: "15%" },
        { data: "undertime_hours", title: "Undertime", width: "15%" },
        { data: "absences", title: "Absences (Days)", width: "15%" },
    ]
});

const groupColumn = 0;
let tempFieldCount = 0;
let globalDtTable, globalSiteLocationDtTable;

const tempContributionTable = function(columns = [], fieldCount = 0, dtInstance){
    const dtInit = $.fn.dataTable.isDataTable(dtInstance);
    if(dtInit){ dtInstance.destroy(); }

    return _tblContributionReport.DataTable({
        dom: 'rtlp',
        columns: columns,
        columnDefs: [{ visible: false, targets: groupColumn }],
        order: [[groupColumn, 'asc']],
        data: [],
        drawCallback: function () {
            const api = this.api();
            const rows = api.rows({ page: 'current' }).nodes();
            let last = null;
    
            api.column(groupColumn, { page: 'current' }).data().each(function (group, i) {
                if (last !== group) {
                    $(rows).eq(i)
                        .before('<tr class="group"><td colspan="'+fieldCount+'">' + group + '</td></tr>');
                    last = group;
                }
            });
        }
    });
}

let dtPayrollSheet = _tblPayrollSheet
    .DataTable({
        dom: 'rtlp',
        serverSide: false,
        destroy: true,
        autoWidth: false,
        ordering: false,
        buttons: [
            {
                extend: 'print',
                text: 'PRINT',
                footer: true,
                title: function () {
                    const date_range = $("input[name='date_range']").val();

                    let coverage = '', tempPayDate = '';
                    if (date_range) {
                        const date_range_arr = date_range.split("-");
                        coverage = moment(date_range_arr[0]).format("MM/DD/YYYY") + " - " + moment(date_range_arr[1]).format("MM/DD/YYYY");
                    }

                    let tempPayrollGroup = "";
                    const pay_date = $("input[name='pay_date']").val();
                    if (pay_date) {
                        tempPayDate = moment(pay_date).format("MM/DD/YYYY");
                    }
                    if (typeof psEmployeeGroup !== "undefined" && typeof psEmployeeGroup == "object" && psEmployeeGroup.length > 0) {
                        tempPayrollGroup += psEmployeeGroup.join(" | ");
                    }

                    let newPayrollGroup = "";
                    if (tempPayrollGroup) {
                        newPayrollGroup = `<div class="m--regular-font-size-sm1 mt-1">PAYROLL GROUP: <span style='font-weight: 600; text-transform: uppercase;'>${tempPayrollGroup}</span></div>`;
                    }
                    return `<div class="text-center m--regular-font-size-lg2" style="text-transform: uppercase;">${selectedCompany.description}</div>
                            <div class="text-center m--regular-font-size-sm1 text-muted" style="text-transform: uppercase;">${selectedCompany.company_address}</div>
                            <div class="m--regular-font-size-lg1">PAYROLL SHEET</div>
                            <div class="m--regular-font-size-sm1 mt-2">PAY DATE: ${tempPayDate}</div>
                            <div class="m--regular-font-size-sm1 mt-1">PAY COVERAGE: ${coverage}</div>
                            ${newPayrollGroup}`;
                },
                exportOptions: {
                    stripHtml: false,
                    columns: exportOptions.columns,
                },
                customize: function (win) {
                    const css = `@page { size: landscape; margin: 0.5cm; } 
                        .dt-print-view table { font-size: 12px; } 
                        .dt-print-view table.dataTable tfoot tr:first-child th{ border-top: 1px solid #000000; }
                        .dt-print-view table.dataTable tfoot tr:first-child th{ border-bottom: 4px double #000000; }`,
                        head = win.document.head || win.document.getElementsByTagName('head')[0],
                        body = win.document.body || win.document.getElementsByTagName('body')[0],
                        style = win.document.createElement('style'),
                        tempDiv2 = win.document.createElement('div');

                    style.type = 'text/css';
                    style.media = 'print';

                    if (style.styleSheet) {
                        style.styleSheet.cssText = css;
                    } else {
                        style.appendChild(win.document.createTextNode(css));
                    }

                    head.appendChild(style);
                    win.document.title = "Payroll Sheet Printable Page";

                    const tempTable = win.document.getElementsByClassName('dataTable')[0];

                    $(tempTable).removeClass("table-bordered");
                    const tempHeader = dtPayrollSheet.table().header();
                    $(tempHeader).find("tr:first-child th:last-child").remove();
                    $(tempTable).find("thead").empty().append(tempHeader.innerHTML);
                    $(tempTable).find("thead tr:first-child > th:first-child").empty().text("#");
                    const tempTHead = $(tempTable).find("thead th:not(:first-child)");
                    tempTHead
                        .removeClass("text-right")
                        .addClass("text-center");

                    $(tempTable).find("tfoot th:first-child").addClass("m--font-boldest");
                    const tempTableTfoot = win.document.getElementsByTagName('tfoot')[0];
                    tempTableTfoot.innerHTML = _globalFooterHtml;
                    $(tempTableTfoot).find("tr th:first-child").removeClass("text-center");

                    const tempTable2 = win.document.getElementsByClassName('dataTable')[0];
                    $(tempTable2).find("thead th.last-child").remove();

                    let signatoryCells = ``;
                    if (globalPrintableSignatory.length > 0) {
                        $.each(globalPrintableSignatory, function (i, v) {
                            let tempLabel = v.label;
                            tempLabel = tempLabel.toUpperCase();

                            let tempValue = v.value;
                            tempValue = tempValue ? tempValue.toUpperCase() : tempValue;

                            if (tempLabel && v.is_active === true) {
                                let tempCell = `<div style='display: inline-block; position: relative; width: 20%; margin-top: 30px;'>
                                <p style='font-weight: bold; margin-left: 10px;'>${tempLabel}:</p>
                                <p style='font-weight: 600; margin-left: 10px; margin-right: 50px; margin-top: 50px; padding-top: 10px; border-top: 1px solid #000000;'>${tempValue}</p>
                                </div>`;
                                signatoryCells += tempCell;
                            }
                        });
                    }
                    if (signatoryCells) {
                        tempDiv2.innerHTML = `<table width='100%' style='margin-top: 60px;'>
                        <tbody>
                            <tr>
                                <td width='100%'>${signatoryCells}</td>
                            </tr>
                        </tbody>
                        </table>`;
                        body.appendChild(tempDiv2);
                    }
                    //# change name of column headers when printing
                    $(win.document.body).find("th span#allow").text('ALLOW');
                    $(win.document.body).find("th span#allowance").text('ALLOW');
                    $(win.document.body).find("th span#adjustment").text('ADJ');
                    // end of function
                }
            }, {
                extend: 'excel',
                exportOptions,
                footer: true,
                customize: function (xlsx) {
                    const sheet = xlsx.xl.worksheets['sheet1.xml'];
                    const sheetData = sheet.getElementsByTagName('sheetData')[0];
                    let lastRow = $('row:nth-last-child(2) c', sheet);
                    
                    let tempPre = [];
                    let tempDatax = [];
                    let tempData = [];

                    $.each(lastRow, function (i, v) {
                        let _value = "empty";
                        let tempCell = { key: v, value: _value };
                        tempDatax.push(tempCell);
                    });

                    let tempRowIndex = $('row', sheet).length;
                    tempRowIndex += 1;

                    let tempRowx = Addrow(tempRowIndex, tempDatax);
                    sheetData.appendChild(tempRowx);

                    lastRow = $('row:last c', sheet);

                    let lastRowCols = $('row:nth-last-child(2) c', sheet);
                    $('row:nth-last-child(2)', sheet).remove();
                    $('row:last', sheet).remove();

                    let numrows = $('row', sheet).length;

                    lastRow.each(function (index) {
                        var attr = $(this).attr('r');
                        var pre = attr.substring(0, 1);
                        tempPre.push(pre);
                        var ind = parseInt(attr.substring(1, attr.length));
                        ind = ind + numrows;
                        $(this).attr("r", pre + ind);
                    });

                    let tempRowCols = [];
                    $.each(lastRowCols, function (i, v) {
                        let textContent = v.textContent;
                        if (i > 1) {
                            tempRowCols.push(textContent);
                        }
                    });

                    let tempx = 0;

                    /*** reference 
                     * console.log(tempRowCols, lastRowCols, tempPre);
                    console.log(lastRowCols.length, tempPre.length); 
                    * reference
                    */
                    
                    $.each(tempPre, function (i, v) {
                        let cval = "";
                        if(i == 0) {
                            cval = "GRAND TOTAL";
                        }else if (i > 1 && i !== 11) {
                            cval = tempRowCols[tempx];
                            cval = $.trim(cval);
                            tempx++;
                        }
                        let tempCell = { key: v, value: cval };
                        tempData.push(tempCell);
                    });

                    /*** old code 
                     * $.each(tempPre, function (i, v) {
                        let _value = "";
                        if (i > 2 && i !== 10) {
                            _value = tempRowCols[tempx];
                            _value = $.trim(_value);
                            tempx++;
                        } else if (i == 0) {
                            _value = "GRAND TOTAL";
                        }

                        let tempCell = { key: v, value: _value };
                        tempData.push(tempCell);
                    }); 
                    * old code
                    ***/

                    numrows = $('row', sheet).length;
                    tempRowIndex = numrows > 0 ? numrows + 1 : numrows;

                    let mergeCells = $('mergeCells', sheet);
                    mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                        attr: {
                            ref: 'A' + tempRowIndex + ':' + 'E' + tempRowIndex,
                        },
                    }));

                    const newRowData = Addrow(tempRowIndex, tempData);
                    sheetData.appendChild(newRowData);
                    /*** $('row c:nth-child(10)', sheet).attr("s", "55"); ***/
                    $('row:last c', sheet).attr("s", "2");

                    function Addrow(index, data) {
                        const row = sheet.createElement('row');
                        row.setAttribute("r", index);
                        
                        let i;
                        for (i = 0; i < data.length; i++) {
                            const key = data[i].key;
                            let value = data[i].value;

                            const isNumber = $.isNumeric(value);
                            const tempType = isNumber ? 'n' : 'inlineStr';
                            
                            const c = sheet.createElement('c');
                            c.setAttribute("t", tempType);
                            c.setAttribute("s", "2"); // style index
                            c.setAttribute("r", key + index); // e.g., "A1", "B2"

                            if (isNumber) {
                                value = parseFloat(value.toString().replace(/,/g, '')); // Clean thousands separators if present
                                const v = sheet.createElement('v');
                                v.textContent = value;
                                c.appendChild(v);
                            } else {
                                const is = sheet.createElement('is');
                                const t = sheet.createElement('t');
                                const text = sheet.createTextNode(value);
                                t.appendChild(text);
                                is.appendChild(t);
                                c.appendChild(is);
                            }

                            row.appendChild(c);
                        }

                        /*** let i;
                        for (i = 0; i < data.length; i++) {
                            const key = data[i].key;
                            const value = data[i].value;

                            const tempType = $.isNumeric(value) ? 'n' : 'inlineStr';
                            const c = sheet.createElement('c');
                            c.setAttribute("t", tempType);
                            c.setAttribute("s", "2");
                            c.setAttribute("r", key + index);

                            const is = sheet.createElement('is');
                            const t = sheet.createElement('t');
                            const text = sheet.createTextNode(value);

                            t.appendChild(text);
                            is.appendChild(t);
                            c.appendChild(is);

                            row.appendChild(c);
                        } ***/

                        return row;
                    }

                    function _createNode(doc, nodeName, opts) {
                        const tempNode = doc.createElement(nodeName);
                        if (opts) {
                            if (opts.attr) { $(tempNode).attr(opts.attr); }
                            if (opts.children) {
                                $.each(opts.children, function (key, value) {
                                    tempNode.appendChild(value);
                                });
                            }
                            if (opts.text !== null && opts.text !== undefined) { tempNode.appendChild(doc.createTextNode(opts.text)); }
                        }
                        return tempNode;
                    }
                }
            }
        ],
        ajax: {
            url: baseUrl("payroll/get_payroll_sheet"),
            type: "POST",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.date_range = $("input[name='date_range']", form).val();
                d.employees = $("#employees", form).val();
                d.company = $("#company", form).val();
                d.payout_schedule = $("#payout_schedule", form).val();
                d.payout_sequence = $("#payroll_sequence", form).val();
                d.pay_date = $("input[name='pay_date']", form).val();
                d.show_posted = showPosted;

                return d;
            },
            dataType: "JSON",
        },
        pageLength: 10,
        columns: [
            {
                data: null,
                width: "3%",
                orderable: false,
                className: "text-center",
                render: function (_data, _type, row) {
                    const lockPosting = parseInt(row.printed_payslip) == 1;
                    if (parseInt(row.posted) === 0) {
                        return `<label class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                                <input type="checkbox" name="selected[]" value="${row.id}" checked /><span></span>
                            </label>`;
                    }
                    if (lockPosting) {
                        if (row.has_latest_payroll == 1) {
                            return `<i class="locked-payroll fa fa-lock"></i>`;
                        } else {

                            const hasUndoPrintedPermission = jQuery.inArray("undo_print", _currentActions) !== -1 ? true : false;
                            return `<i class="locked-payroll fa fa-lock ${hasUndoPrintedPermission ? 'pulse' : ''}" ${hasUndoPrintedPermission ? 'onclick="undoPrinted(' + row.id + ', ' + row.emp_id + ')"' : ''} ${hasUndoPrintedPermission ? 'style="cursor: pointer"' : ''} ${hasUndoPrintedPermission ? 'title="Undo Printed Status" data-toggle="m-tooltip" data-original-title="Restore Selected" data-skin="dark"' : ''}></i>`;
                        }
                    }
                    return `<i class="fa fa-check m--font-primary"></i>`;
                }
            }, {
                data: 'id',
                width: "3%",
                orderable: false,
                visible: false,
                className: "text-center",
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: "lastname",
                render: function (data, type, row) {
                    const mi = row.middlename.toLowerCase() !== "n/a" && row.middlename !== "" && row.middlename.toLowerCase() !== "none" ? row.middlename.substring(0, 1) + ". " : "";
                    const suffix = row.suffix.toLowerCase() !== "n/a" && row.suffix !== "" && row.suffix.toLowerCase() !== "none" ? row.suffix : "";
                    let complete_name = data + ", " + row.firstname + " " + suffix + " " + mi;
                    let position = row.position.toUpperCase();

                    complete_name = complete_name.toUpperCase();
                    return `<span class="m--font-bolder">${complete_name}</span><br><small>` + position + `</small>`;
                }
            }, {
                data: "rate", // rate
                width: "5%",
                className: "text-right",
                render: function (data) {
                    return numberFormat(data);
                }
            }, {
                data: "allowance_rate", // allowance rate
                width: "5%",
                className: "text-right",
                render: function (data) {
                    return numberFormat(data);
                }
            }, {
                data: "no_of_days",
                className: "text-center",
                render: function (data) {
                    return numberFormat(data);
                }
            }, {
                data: "ot_amount", // OT
                className: "text-right",
                render: function (data, type, row) {
                    return numberFormat(data);
                }
            }, {
                data: "ot_ndiff_amount", // n_diff
                className: "text-right",
                render: function (data, type, row) {
                    return numberFormat(data);
                }
            }, {
                data: "total_holiday_amount", // holidays
                className: "text-right",
                render: function (data) {
                    return numberFormat(data);
                }
            }, {
                data: "total_ndiff_amount", // regular night diff
                className: "text-right",
                render: function (data) {
                    return numberFormat(data);
                }
            }, {
                data: "basic_rate",
                width: "5%",
                className: "text-right",
                render: function (data) {
                    return numberFormat(data);
                }
            }, {
                data: "total_allowances", // allowances
                width: "5%",
                className: "text-right",
                render: function (data, type, row) {
                    const tempData = numberFormat(data);
                    let template = ``;
                    template = tempData;
                    const tempCreatedAdjustments = row.created_adjustments;
                    if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                        const created_adjustments = tempCreatedAdjustments.split(",");
                        created_adjustments.forEach((row, i) => {
                            const temp_adjustment = row.split("||");
                            const adj_type = parseInt(temp_adjustment[2]);
                            const temp_status = parseInt(temp_adjustment[3]);
                            let allowanceAmount = parseFloat(data);
                            if (adj_type === 1) { allowanceAmount += parseFloat(temp_adjustment[1]); } 
                            else { allowanceAmount -= parseFloat(temp_adjustment[1]); }
                            const formattedAllowanceAmount = numberFormat(allowanceAmount);

                            if (temp_adjustment[0] == "ALLOWANCE" && temp_status === 0) {
                                template = `<div class="mb-0 m--font-bolder m--font-accent">
                                    <span class='fa fa-exclamation-circle'></span>
                                    <span class="m--font-boldest">${tempData}</span>
                                </div>`;
                            }
                            if (temp_adjustment[0] == "ALLOWANCE" && temp_status === 1) {
                                template = `<div class="mb-0 m--font-bolder m--font-primary">
                                    <span class="m--font-boldest">${formattedAllowanceAmount}</span>
                                </div>`;
                            }
                        });
                    }

                    return template;
                }
            }, {
                data: "custom_adjustments", // adjustment
                width: "6%",
                orderable: false,
                className: "text-right",
                render: function (data, type, row) {
                    if (!data) { return `---`; }
                    let template = ``;
                    const custom_adjustments = data.split(",");
                    custom_adjustments.forEach((row, i) => {
                        const custom_adjustment = row.split("||");
                        const marginClass = i > 0 ? "mt-1" : "";
                        const adj_type = parseInt(custom_adjustment[2]);
                        const adjTypeClass = adj_type === 0 ? "m--font-danger" : "";

                        template += `<div class="mb-0 m--regular-font-size-sm1 m--font-bolder ${marginClass}">
                            <span>${custom_adjustment[0]}</span>
                            <span> - </span>
                            <span class="m--font-boldest ${adjTypeClass}">${numberFormat(custom_adjustment[1])}</span>
                        </div>`;
                    });

                    return template;
                }
            }, {
                data: "gross_pay", // gross pay
                width: "5%",
                className: "text-right",
                render: function (data) {
                    return numberFormat(data);
                }
            }, {
                data: "sss", // sss
                className: "text-right",
                render: function (data, type, row) {
                    let approvedAmount = parseFloat(data);
                    const tempData = numberFormat(data);
                    let template = ``;
                    template = tempData;
                    const tempCreatedAdjustments = row.created_adjustments;
                    if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                        let tempAdj = 0;
                        const created_adjustments = tempCreatedAdjustments.split(",");
                        created_adjustments.forEach((row, i) => {
                            const temp_adjustment = row.split("||");
                            const adj_type = parseInt(temp_adjustment[2]);
                            const temp_status = parseInt(temp_adjustment[3]);
                            let adjustedAmount = parseFloat(data);
                            if (adj_type === 1) { adjustedAmount += parseFloat(temp_adjustment[1]); } 
                            else { adjustedAmount -= parseFloat(temp_adjustment[1]); }
                            tempAdj = adjustedAmount;
                            const formattedAdjustedAmount = numberFormat(adjustedAmount);
                            if (temp_adjustment[0] == "SSS" && temp_status === 0) {
                                template = `<div class="mb-0 m--font-bolder m--font-accent">
                                    <span class='fa fa-exclamation-circle'></span>
                                    <span class="m--font-boldest">${tempData}</span>
                                </div>`;
                            }
                            if (temp_adjustment[0] == "SSS" && temp_status === 1) {
                                approvedAmount = tempAdj;
                                template = `<div class="mb-0 m--font-bolder m--font-primary">
                                    <span class="m--font-boldest">${formattedAdjustedAmount}</span>
                                </div>`;

                            }
                        });
                    }
                    if ($.inArray(parseInt(row.id), _dtRowSSS) == -1) {
                        if (typeof _globalFooterAdjustments.sss !== "undefined" && _globalFooterAdjustments.sss !== null) {
                            approvedAmount = parseFloat(_globalFooterAdjustments.sss) + approvedAmount;
                        }
                        _globalFooterAdjustments.sss = approvedAmount;
                        //_globalFooterAdjustments = Object.assign({}, _globalFooterAdjustments, { sss: approvedAmount });
                        _dtRowSSS.push(parseInt(row.id));
                    }
                    return template;
                }
            }, {
                data: "sss_prov", // sss
                className: "text-right",
                render: function (data, type, row) {
                    let approvedAmount = parseFloat(data);
                    const tempData = numberFormat(data);
                    let template = ``;
                    template = tempData;
                    const tempCreatedAdjustments = row.created_adjustments;
                    if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                        let tempAdj = 0;
                        const created_adjustments = tempCreatedAdjustments.split(",");
                        created_adjustments.forEach((row, i) => {
                            const temp_adjustment = row.split("||");
                            const adj_type = parseInt(temp_adjustment[2]);
                            const temp_status = parseInt(temp_adjustment[3]);
                            let adjustedAmount = parseFloat(data);
                            if (adj_type === 1) { adjustedAmount += parseFloat(temp_adjustment[1]); } 
                            else { adjustedAmount -= parseFloat(temp_adjustment[1]); }
                            tempAdj = adjustedAmount;
                            const formattedAdjustedAmount = numberFormat(adjustedAmount);
                            if (temp_adjustment[0] == "SSS_PROV" && temp_status === 0) {
                                template = `<div class="mb-0 m--font-bolder m--font-accent">
                                    <span class='fa fa-exclamation-circle'></span>
                                    <span class="m--font-boldest">${tempData}</span>
                                </div>`;
                            }
                            if (temp_adjustment[0] == "SSS_PROV" && temp_status === 1) {
                                approvedAmount = tempAdj;
                                template = `<div class="mb-0 m--font-bolder m--font-primary">
                                    <span class="m--font-boldest">${formattedAdjustedAmount}</span>
                                </div>`;

                            }
                        });
                    }
                    if ($.inArray(parseInt(row.id), _dtRowSSS_PROV) == -1) {
                        if (typeof _globalFooterAdjustments.sss_prov !== "undefined" && _globalFooterAdjustments.sss_prov !== null) {
                            approvedAmount = parseFloat(_globalFooterAdjustments.sss_prov) + approvedAmount;
                        }
                        _globalFooterAdjustments.sss_prov = approvedAmount;
                        //_globalFooterAdjustments = Object.assign({}, _globalFooterAdjustments, { sss_prov: approvedAmount });
                        _dtRowSSS_PROV.push(parseInt(row.id));
                    }
                    return template;
                }
            }, {
                data: "ph", // phic
                className: "text-right",
                render: function (data, type, row) {
                    let approvedAmount = parseFloat(data);
                    const tempData = numberFormat(data);
                    let template = ``;
                    template = tempData;
                    const tempCreatedAdjustments = row.created_adjustments;
                    if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                        let tempAdj = 0;
                        const created_adjustments = tempCreatedAdjustments.split(",");
                        created_adjustments.forEach((row, i) => {
                            const temp_adjustment = row.split("||");
                            const adj_type = parseInt(temp_adjustment[2]);
                            const temp_status = parseInt(temp_adjustment[3]);
                            let adjustedAmount = parseFloat(data);
                            if (adj_type === 1) { adjustedAmount += parseFloat(temp_adjustment[1]); } 
                            else { adjustedAmount -= parseFloat(temp_adjustment[1]); }
                            tempAdj = adjustedAmount;
                            const formattedAdjustedAmount = numberFormat(adjustedAmount);
                            if (temp_adjustment[0] == "PHIC" && temp_status === 0) {
                                template = `<div class="mb-0 m--font-bolder m--font-accent">
                                    <span class='fa fa-exclamation-circle'></span>
                                    <span class="m--font-boldest">${tempData}</span>
                                </div>`;
                            }
                            if (temp_adjustment[0] == "PHIC" && temp_status === 1) {
                                approvedAmount = tempAdj;
                                template = `<div class="mb-0 m--font-bolder m--font-primary">
                                    <span class="m--font-boldest">${formattedAdjustedAmount}</span>
                                </div>`;
                            }
                        });
                    }
                    if ($.inArray(parseInt(row.id), _dtRowPHIC) == -1) {
                        if (typeof _globalFooterAdjustments.phic !== "undefined" && _globalFooterAdjustments.phic !== null) {
                            approvedAmount = parseFloat(_globalFooterAdjustments.phic) + approvedAmount;
                        }
                        _globalFooterAdjustments.phic = approvedAmount;
                        //_globalFooterAdjustments = Object.assign({}, _globalFooterAdjustments, { phic: approvedAmount });
                        _dtRowPHIC.push(parseInt(row.id));
                    }
                    return template;
                }
            }, {
                data: "hdmf", // hdmf
                className: "text-right",
                render: function (data, type, row) {
                    let approvedAmount = parseFloat(data);
                    const tempData = numberFormat(data);
                    let template = ``;
                    template = tempData;
                    const tempCreatedAdjustments = row.created_adjustments;
                    if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                        let tempAdj = 0;
                        const created_adjustments = tempCreatedAdjustments.split(",");
                        created_adjustments.forEach((row, i) => {
                            const temp_adjustment = row.split("||");
                            const adj_type = parseInt(temp_adjustment[2]);
                            const temp_status = parseInt(temp_adjustment[3]);
                            let adjustedAmount = parseFloat(data);
                            if (adj_type === 1) { adjustedAmount += parseFloat(temp_adjustment[1]); } 
                            else { adjustedAmount -= parseFloat(temp_adjustment[1]); }
                            tempAdj = adjustedAmount;
                            const formattedAdjustedAmount = numberFormat(adjustedAmount);

                            if (temp_adjustment[0] == "HDMF" && temp_status === 0) {
                                template = `<div class="mb-0 m--font-bolder m--font-accent">
                                    <span class='fa fa-exclamation-circle'></span>
                                    <span class="m--font-boldest">${tempData}</span>
                                </div>`;
                            }
                            if (temp_adjustment[0] == "HDMF" && temp_status === 1) {
                                approvedAmount = tempAdj;
                                template = `<div class="mb-0 m--font-bolder m--font-primary">
                                    <span class="m--font-boldest">${formattedAdjustedAmount}</span>
                                </div>`;
                            }
                        });
                    }
                    if ($.inArray(parseInt(row.id), _dtRowHDMF) == -1) {
                        if (typeof _globalFooterAdjustments.hdmf !== "undefined" && _globalFooterAdjustments.hdmf !== null) {
                            approvedAmount = parseFloat(_globalFooterAdjustments.hdmf) + approvedAmount;
                        }
                        _globalFooterAdjustments.hdmf = approvedAmount;
                        // _globalFooterAdjustments = Object.assign({}, _globalFooterAdjustments, { hdmf: approvedAmount });
                        _dtRowHDMF.push(parseInt(row.id));
                    }
                    return template;
                }
            }, {
                data: "tax", // tax
                className: "text-right",
                render: function (data, type, row) {
                    let approvedAmount = parseFloat(data);
                    const tempData = numberFormat(data);
                    let template = ``;
                    template = tempData;
                    const tempCreatedAdjustments = row.created_adjustments;
                    if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                        let tempAdj = 0;
                        const created_adjustments = tempCreatedAdjustments.split(",");
                        created_adjustments.forEach((row, i) => {
                            const temp_adjustment = row.split("||");
                            const adj_type = parseInt(temp_adjustment[2]);
                            const temp_status = parseInt(temp_adjustment[3]);
                            let adjustedAmount = parseFloat(data);
                            if (adj_type === 1) { adjustedAmount += parseFloat(temp_adjustment[1]); } 
                            else { adjustedAmount -= parseFloat(temp_adjustment[1]); }
                            tempAdj = adjustedAmount;
                            const formattedAdjustedAmount = numberFormat(adjustedAmount);

                            if (temp_adjustment[0] == "TAX" && temp_status === 0) {
                                template = `<div class="mb-0 m--font-bolder m--font-accent">
                                    <span class='fa fa-exclamation-circle'></span>
                                    <span class="m--font-boldest">${tempData}</span>
                                </div>`;
                            }
                            if (temp_adjustment[0] == "TAX" && temp_status === 1) {
                                approvedAmount = tempAdj;
                                template = `<div class="mb-0 m--font-bolder m--font-primary">
                                    <span class="m--font-boldest">${formattedAdjustedAmount}</span>
                                </div>`;
                            }
                        });
                    }
                    if ($.inArray(parseInt(row.id), _dtRowTAX) == -1) {
                        if (typeof _globalFooterAdjustments.tax !== "undefined" && _globalFooterAdjustments.tax !== null) {
                            approvedAmount = parseFloat(_globalFooterAdjustments.tax) + approvedAmount;
                        }
                        _globalFooterAdjustments.tax = approvedAmount;
                        // _globalFooterAdjustments = Object.assign({}, _globalFooterAdjustments, { tax: approvedAmount });
                        _dtRowTAX.push(parseInt(row.id));
                    }
                    return template;
                }
            }, {
                data: "total_loans", // loans
                className: "text-right",
                render: function (data, _type, row) {
                    let approvedAmount = parseFloat(data);
                    const tempData = numberFormat(data);
                    let template = ``;
                    const tempCreatedAdjustments = row.created_adjustments;
                    if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                        let tempAdjAmount = 0.00;
                        const created_adjustments = tempCreatedAdjustments.split(",");
                        created_adjustments.forEach((row, i) => {
                            const temp_adjustment = row.split("||");
                            const adj_type = parseInt(temp_adjustment[2]);
                            const temp_status = parseInt(temp_adjustment[3]);
                            let adjustedAmount = parseFloat(data);
                            if (adj_type === 1) { adjustedAmount += parseFloat(temp_adjustment[1]); } 
                            else { adjustedAmount -= parseFloat(temp_adjustment[1]); }
                            const formattedAmount = numberFormat(adjustedAmount);
                            tempAdjAmount = adjustedAmount;
                            if (temp_adjustment[0] == "LOAN" && temp_status === 0) {
                                template = `<div class="mb-0 m--font-bolder m--font-accent">
                                    <span class='fa fa-exclamation-circle'></span>
                                    <span class="m--font-boldest">${tempData}</span>
                                </div>`;
                            }
                            if (temp_adjustment[0] == "LOAN" && temp_status === 1) {
                                approvedAmount = tempAdjAmount;
                                template = `<div class="mb-0 m--font-bolder m--font-primary">
                                    <span class="m--font-boldest">${formattedAmount}</span>
                                </div>`;
                            }
                        });
                    }

                    /*** updates on CA Column ***/
                    const intDeduction = row.total_loans_interest;
                    if (typeof intDeduction !== "undefined" && parseFloat(intDeduction) > 0) {
                        let tempAmountCAInt = parseFloat(intDeduction);

                        template += `<div class="mb-0 m--regular-font-size-sm1 m--font-bolder mt-1">
                            <span>CA/INT</span>
                            <span> - </span>
                            <span class="m--font-boldest">${numberFormat(tempAmountCAInt)}</span>
                        </div>`;

                        approvedAmount = approvedAmount + tempAmountCAInt;
                    }
                    /*** updates on CA Column ***/

                    // deducted charges to total loans
                    const tempDeduction = row.sss_hdmf_loan_deduction;
                    if (typeof tempDeduction !== "undefined" && tempDeduction) {
                        const deductions = tempDeduction.split(",");
                        deductions.forEach((row, i) => {
                            const custom_deduction = row.split("||");
                            const _adj_type = parseInt(custom_deduction[2]);
                            const _adj_details = custom_deduction[0];

                            if ((_adj_type === 0 && _adj_details.toLowerCase() == 'chrge') || (_adj_type === 0 && _adj_details.toLowerCase() == 'ud')) {
                                approvedAmount = approvedAmount - custom_deduction[1];
                            }
                        });

                        template = approvedAmount > 0 ? numberFormat(approvedAmount) : 0;
                    }
                    // deducted charges to total loans

                    if ($.inArray(parseInt(row.id), _dtRowLOAN) == -1) {
                        if (typeof _globalFooterAdjustments.total_loans !== "undefined" && _globalFooterAdjustments.total_loans !== null) {
                            approvedAmount = parseFloat(_globalFooterAdjustments.total_loans) + approvedAmount;
                        }
                        _globalFooterAdjustments.total_loans = approvedAmount;
                        //_globalFooterAdjustments = Object.assign({}, _globalFooterAdjustments, { total_loans: approvedAmount });
                        _dtRowLOAN.push(parseInt(row.id));
                    }

                    const _template = template === "" || template === null || template === " " ? 0.00 : template;
                    return $.isNumeric(_template) === true ? numberFormat(_template): _template;
                }
            }, { 
                data: null, // charges
                width: '5%',
                orderable: false,
                className: 'text-right',
                render: function(data, _type, row){
                    let template = ``;
                    const tempDeduction = row.sss_hdmf_loan_deduction;
                    let charge = 0;
                    if (typeof tempDeduction !== "undefined" && tempDeduction) {
                        const deductions = tempDeduction.split(",");
                        deductions.forEach((row, i) => {
                            const custom_deduction = row.split("||");
                            const marginClass = i > 0 ? "mt-1" : "";
                            const adj_type = parseInt(custom_deduction[2]);
                            const _adj_details = custom_deduction[0];

                            if ((adj_type === 0 && _adj_details.toLowerCase() == 'chrge') || (adj_type === 0 && _adj_details.toLowerCase() == 'ud')) {
                                template += `<div class="mb-0 m--regular-font-size-sm1 m--font-bolder ${marginClass}">
                                    <span class="m--font-boldest">${numberFormat(custom_deduction[1])}</span>
                                </div>`;
                                charge = parseFloat(charge) + parseFloat(custom_deduction[1]);
                            }
                        });
                    }

                    return numberFormat(charge);
                }
            }, {
                data: "sss_loan", // charges
                width: "5%",
                orderable: false,
                className: "text-right",
                render: function (data, _type, row) {
                    let template = ``;
                    const tempDeduction = row.sss_hdmf_loan_deduction;
                    if (typeof tempDeduction !== "undefined" && tempDeduction) {
                        const deductions = tempDeduction.split(",");
                        deductions.forEach((row, i) => {
                            const custom_deduction = row.split("||");
                            const marginClass = i > 0 ? "mt-1" : "";
                            const adj_type = parseInt(custom_deduction[2]);

                            if (adj_type === 1) {
                                const deductionDetails = custom_deduction[0].toUpperCase();
                                template += `<div class="mb-0 m--regular-font-size-sm1 m--font-bolder ${marginClass}">
                                    <span>${deductionDetails}</span>
                                    <span> - </span>
                                    <span class="m--font-boldest">${numberFormat(custom_deduction[1])}</span>
                                </div>`;
                            }
                        });
                    }

                    const _template = template === "" || template === null || template === " " ? 0.00 : template;
                    return $.isNumeric(_template) === true ? numberFormat(_template): _template;
                }
            }, {
                data: "hdmf_loan",
                width: "5%",
                orderable: false,
                className: "text-right",
                render: function (data, type, row) {
                    let template = ``;
                    const tempDeduction = row.sss_hdmf_loan_deduction;
                    if (typeof tempDeduction !== "undefined" && tempDeduction) {
                        const deductions = tempDeduction.split(",");
                        deductions.forEach((row, i) => {
                            const custom_deduction = row.split("||");
                            const marginClass = i > 0 ? "mt-1" : "";
                            const adj_type = parseInt(custom_deduction[2]);

                            if (adj_type === 2) {
                                const deductionDetails = custom_deduction[0].toUpperCase();
                                template += `<div class="mb-0 m--regular-font-size-sm1 m--font-bolder ${marginClass}">
                                    <span>${deductionDetails}</span>
                                    <span> - </span>
                                    <span class="m--font-boldest">${numberFormat(custom_deduction[1])}</span>
                                </div>`;
                            }
                        });
                    }

                    const _template = template === "" || template === null || template === " " ? 0.00 : template;
                    return $.isNumeric(_template) === true ? numberFormat(_template): _template;
                }
            },
            {
                data: "net_pay", // net pay
                className: "text-right",
                render: function (data) {
                    const tempHtml = "&#8369;&nbsp;&nbsp;" + numberFormat(data);
                    return tempHtml;
                }
            },
            {
                data: null,
                width: "4%",
                orderable: false,
                className: "text-center",
                render: function (data, type, row) {
                    const mi = row.middlename.toLowerCase() !== "n/a" && row.middlename !== "" && row.middlename.toLowerCase() !== "none" ? row.middlename.substring(0, 1) + ". " : "";
                    const suffix = row.suffix.toLowerCase() !== "n/a" && row.suffix !== "" && row.suffix.toLowerCase() !== "none" ? row.suffix : "";
                    const complete_name = row.firstname + " " + mi + " " + " " + row.lastname + " " + suffix;
                    const isMonthlyPaidEmployee = typeof row.is_monthly_paid !== "undefined" && parseInt(row.is_monthly_paid) === 1;
                    const lockPosting = parseInt(row.printed_payslip) == 1;

                    let btnUndoPosting = ``;
                    let btnAdjustments = ``;
                    let btnViewTimesheet = ``;

                    if (parseInt(row.is_bonus) === 0 && isMonthlyPaidEmployee === false) {
                        btnViewTimesheet = `<li class="m-nav__item">
                            <a href="javascript:void(0)" class="m-nav__link"
                            onclick="viewTimesheet(${row.emp_id}, '${complete_name}')">
                            <i class="m-nav__link-icon fa fa fa-clock-o"></i>
                            <span class="m-nav__link-text">VIEW TIMESHEET</span>
                            </a>
                        </li>`;
                    }


                    if (_currentActions.includes('undo_posting') && parseInt(row.posted) === 1) {
                        btnUndoPosting = `<li class="m-nav__separator m-nav__separator--fit"></li>
                        <li class="m-nav__item">
                            <a href="javascript:void(0)" class="m-nav__link"
                            onclick="confirmUndoPosting('${complete_name}', ${row.id})">
                            <i class="m-nav__link-icon fa fa-undo"></i>
                            <span class="m-nav__link-text">UNDO POSTING</span>
                            </a>
                        </li>`;
                    }

                    if (parseInt(row.posted) === 0) {
                        if (parseInt(row.is_bonus) === 0) {
                            btnAdjustments += `<li class="m-nav__item">
                                <a href="javascript:void(0)" class="m-nav__link"
                                onclick="openCreatedAdjustmentsModal(${row.id}, ${row.posted})">
                                    <i class="m-nav__link-icon fa fa-file-text-o"></i>
                                    <span class="m-nav__link-text">MANAGE PAYROLL SHEET ADJUSTMENTS</span>
                                </a>
                            </li>`;
                        }
                        btnAdjustments += `<li class="m-nav__item">
                            <a href="javascript:void(0)" class="m-nav__link"
                            onclick="openManageCustomAdjustmentsModal(${row.id}, ${row.posted})">
                                <i class="m-nav__link-icon fa fa-money"></i>
                                <span class="m-nav__link-text">MANAGE CUSTOM ADJUSTMENTS</span>
                            </a>
                        </li>`;
                        /*** btnAdjustments += `<li class="m-nav__item">
                            <a href="javascript:void(0)" class="m-nav__link"
                            onclick="openManageCustomRateAdjustmentsModal(${row.id}, '${complete_name}')">
                                <i class="m-nav__link-icon fa fa-exchange"></i>
                                <span class="m-nav__link-text">MANAGE CUSTOM RATE ADJUSTMENTS</span>
                            </a>
                        </li>`; ***/
                    }

                    let _tempAction = `<div class="m-dropdown m-dropdown--inline m-dropdown--align-right m-dropdown--large"
                            data-dropdown-toggle="click" aria-expanded="true">
                        <a href="#" class="m-dropdown__toggle btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill"
                            data-toggle="m-tooltip" data-original-title="More Options" data-skin="dark"
                            data-delay='{"show": 500}'>
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                        <div class="m-dropdown__wrapper">
                            <span class="m-dropdown__arrow m-dropdown__arrow--right"></span>
                            <div class="m-dropdown__inner">
                                <div class="m-dropdown__body">
                                    <div class="m-dropdown__content">
                                        <ul class="m-nav">
                                            <li class="m-nav__section m-nav__section--first">
                                                <span class="m-nav__section-text">OPTIONS</span>
                                            </li>
                                            ${btnAdjustments}
                                            ${btnViewTimesheet}
                                            ${btnUndoPosting}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;

                    if (lockPosting) {
                        if (parseInt(row.is_bonus) === 0 && isMonthlyPaidEmployee === false) {
                            _tempAction = `<a href="javascript:void(0)" 
                                class="m-portlet__nav-link btn m-btn--icon m-btn--icon-only btn-sm" 
                                data-toggle="m-tooltip" data-original-title="View Time Sheet" data-skin="dark"
                                data-delay='{"show": 500}'
                                onclick="viewTimesheet(${row.emp_id}, '${complete_name}')">
                                <i class="fa fa-clock-o"></i>
                            </a>`;
                        } else {
                            _tempAction = ``;
                        }
                    }

                    return _tempAction;
                }
            }
        ],
        order: [[1, "asc"]],
        createdRow: function (row, data, dataIndex) {
            const lockPosting = parseInt(data.printed_payslip) == 1;
            if (lockPosting) {
                $(row).addClass("m--lock_posting");
            }
        },
        drawCallback: function (settings) {
            let tempFooter = $(settings.nTableWrapper).find("tfoot");
            if (typeof tempFooter !== "undefined") { _globalFooterHtml = tempFooter[0].innerHTML; }
        },
        footerCallback: function (row, data, start, end, display) {
            globalGrandTotal = {};
            const api = this.api();
            // Remove the formatting to get integer data for summation
            const intVal = function (value) {
                return typeof value === 'string'
                ? parseFloat(value.replace(/[^\d.-]/g, ''), 10)
                : typeof value === 'number' ? value : 0;
            }

            const totalOT = api.column(6).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            const totalNDOT = api.column(7).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            const totalHoliday = api.column(8).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            const totalRegND = api.column(9).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            const totalBasic = api.column(10).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            const totalAllowance = api.column(11).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            const totalGross = api.column(13).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

            const totalCharges = api
                .column(20)
                .data()
                .reduce(function (a, b) {
                    const tempDeduction = b.sss_hdmf_loan_deduction;
                    let totalChargeDeduction = 0;

                    if (typeof tempDeduction !== "undefined" && tempDeduction) {
                        const deductions = tempDeduction.split(",");
                        deductions.forEach((row, i) => {
                            const custom_deduction = row.split("||");
                            const _adj_type = parseInt(custom_deduction[2]);
                            const _adj_details = custom_deduction[0];

                            if (_adj_type === 0 && _adj_details.toLowerCase() == 'chrge' || (_adj_type === 0 && _adj_details.toLowerCase() == 'ud')) {
                                totalChargeDeduction = parseFloat(totalChargeDeduction) + parseFloat(custom_deduction[1]);
                            }
                        });
                    
                    }

                    return intVal(a) + intVal(totalChargeDeduction);
                }, 0);

            const totalSSSLoans = api.column(21).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            const totalHDMFLoans = api.column(22).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            const totalNetpay = api.column(23).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            
            let totalSSS = api.column(14).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            let totalSSS_PROV = api.column(15).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            let totalPH = api.column(16).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            let totalHDMF = api.column(17).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            let totalTAX = api.column(18).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            let totalLoans = api.column(19).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            
            totalSSS = _globalFooterAdjustments.sss;
            totalSSS_PROV = _globalFooterAdjustments.sss_prov;
            totalPH = _globalFooterAdjustments.phic;
            totalHDMF = _globalFooterAdjustments.hdmf;
            totalTAX = _globalFooterAdjustments.tax;
            totalLoans = _globalFooterAdjustments.total_loans;

            /*** ut: numberFormat(totalUT), ***/
            const _tempFooterData = {
                ot: numberFormat(totalOT),
                ndot: numberFormat(totalNDOT),
                holiday: numberFormat(totalHoliday),
                basic: numberFormat(totalBasic),
                allowance: numberFormat(totalAllowance),
                gross: numberFormat(totalGross),
                sss: numberFormat(totalSSS),
                sss_prov: numberFormat(totalSSS_PROV),
                phic: numberFormat(totalPH),
                hdmf: numberFormat(totalHDMF),
                tax: numberFormat(totalTAX),
                loans: numberFormat(totalLoans),
                charges: numberFormat(totalCharges),
                sss_loan: numberFormat(totalSSSLoans),
                hdmf_loan: numberFormat(totalHDMFLoans),
                net: numberFormat(totalNetpay),
            }

            globalGrandTotal = { ..._tempFooterData }

            /*** $(api.column(6).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalUT) + "</span>"); ***/
            $(api.column(6).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalOT) + "</span>");
            $(api.column(7).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalNDOT) + "</span>");
            $(api.column(8).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalHoliday) + "</span>");
            $(api.column(9).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalRegND) + "</span>");

            $(api.column(10).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalBasic) + "</span>");
            $(api.column(11).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalAllowance) + "</span>");
            $(api.column(13).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalGross) + "</span>");
            $(api.column(14).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalSSS) + "</span>");
            $(api.column(15).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalSSS_PROV) + "</span>");
            $(api.column(16).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalPH) + "</span>");
            $(api.column(17).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalHDMF) + "</span>");
            $(api.column(18).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalTAX) + "</span>");
            $(api.column(19).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalLoans) + "</span>");
            $(api.column(20).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalCharges) + "</span>");
            $(api.column(21).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalSSSLoans) + "</span>");
            $(api.column(22).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalHDMFLoans) + "</span>");
            $(api.column(23).footer()).html("<span class='m--font-boldest'>&#8369;&nbsp;&nbsp;" + numberFormat(totalNetpay) + "</span>");
        }
    });

function confirmUndoPosting(emp_name, payroll_sheet_id) {
    $(".modal-body p", confirmUndoPostingModal).html(`Are you sure to undone posting the payroll sheet of
                                                        <span class="m--font-boldest">${emp_name}</span>?`);
    let url = $("form", confirmUndoPostingModal).data("url");
    url += "/" + payroll_sheet_id;
    $("form").attr("data-employee", emp_name);
    $("form", confirmUndoPostingModal).attr("action", url);

    confirmUndoPostingModal.modal("show");
}

$.validate({
    form: '#frm-undo-posting',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const url = form.attr("action");
        const employee = form.attr("data-employee");
        $.ajax({
            url,
            type: "POST",
            dataType: "JSON",
            data: {
                employee,
                csrf_token: _csrf_hash
            },
            success: function (response) {
                toastr[response.toast](response.message, response.title, { timeOut: 10000 });
                dtPayrollSheet.ajax.reload(null, false);
                confirmUndoPostingModal.modal("hide");
            }
        });
        return false;
    }
});

function formatNumber(value, decimals = 2) {
    return parseFloat(value).toLocaleString("en-US", { maximumFractionDigits: decimals });
}

$.validate({
    form: '#frm-filter',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const date_range = $("input[name='date_range']", form).val();
        const employees = $("#employees", form).val();
        const company = $("#company", form).val();
        const payout_schedule = $("#payout_schedule", form).val();
        const payout_sequence = $("#payroll_sequence", form).val();
        const pay_date = $("input[name='pay_date']", form).val();

        checkPayrollSheetData(date_range, employees, company, payout_schedule, payout_sequence, pay_date);

        /*** for getting generated employees that have available cash advance loans ***/
        /*** if(jQuery.inArray("activate_loan", _currentActions) !== -1){
            toastr.info("Please wait, The system is fetching available employee loan/s.", "Fetching Employee Loan/s");
            $.ajax({
                url : baseUrl('payroll/get_emp_with_loans'),
                type: "GET",
                data: {
                    employees,
                    company,
                    date_range,
                    payout_schedule,
                    pay_date
                },
                dataType: "json",
                success: function (json){
                    if(json.state == true){
                        const _md5KeyFilterReset = _md5_key_filter !== json.md5_key_filter;
                        if(_show_modal_loans_once === false && json.count > 0 && json.show_modal === true && _md5KeyFilterReset){
                            vmEmpLoans.tempLoansCount = json.count;
                            vmEmpLoans.tempLoansRows = Object.assign({}, json.results);
                            $("#modal-ps--with-loans").modal();
                            _md5_key_filter = json.md5_key_filter;
                        }else{ generate_ps(date_range, employees, company, payout_schedule, payout_sequence, pay_date); }
                    }else{ generate_ps(date_range, employees, company, payout_schedule, payout_sequence, pay_date); }
                }
            });
        }else{ generate_ps(date_range, employees, company, payout_schedule, payout_sequence, pay_date); } ***/

        // original source code location
        /*** $.ajax({
            url: baseUrl("payroll/generate_payroll_sheet"),
            type: "POST",
            data: {
                csrf_token: _csrf_hash,
                date_range,
                employees,
                company,
                payout_schedule,
                payout_sequence,
                pay_date,
            },
            dataType: "JSON",
            success: function (response) {
                showPosted = "_all";
                _globalFooterAdjustments = { sss: 0, sss_prov: 0, phic: 0, hdmf: 0, tax: 0, total_loans: 0 };
                _dtRowSSS = []; _dtRowSSS_PROV = []; _dtRowPHIC = []; _dtRowHDMF = []; _dtRowTAX = []; _dtRowLOAN = [];
                $("input.show_posted_record[value='_all']").prop("checked", true);

                let absenteeCounter = 0;
                let tempAbsenteeData = [];
                if (typeof response.undertime_records !== "undefined" && response.undertime_records.length > 0) {
                    absenteeCounter = response.undertime_records.length;
                    tempAbsenteeData = response.undertime_records;
                }

                dtPayrollSheet.ajax.reload(function () {
                    if (typeof response.data !== "undefined" && response.data.length > 0) {
                        noContAcctNo = [];
                        $.each(response.data, function (i, v) {
                            if (typeof v.no_cont_acctno !== "undefined" && v.no_cont_acctno.length > 0) {
                                const tempData = { id: v.id, no_acct: v.no_cont_acctno };
                                noContAcctNo.push(tempData);
                            }
                        });
                        if (noContAcctNo.length > 0) {
                            $.ajax({
                                url: siteUrl("payroll/generate_employees_no_cont_acct"),
                                dataType: "json",
                                type: "post",
                                data: { [_csrf_token]: _csrf_hash, employee_account: noContAcctNo },
                                success: function (json) {
                                    let tempCount = 0;
                                    let tempRows = {};
                                    if (json.response) {
                                        tempCount = json.count;
                                        tempRows = Object.assign({}, json.data);
                                    }
                                    vmPsNotification.count = tempCount;
                                    if (vmPsNotification.notification_clicked == true) {
                                        vmPsNotification.notification_clicked = false;
                                    }
                                    vmPsNotificationModal.count = tempCount;
                                    vmPsNotificationModal.rows = Object.assign({}, tempRows);
                                }
                            });
                        }
                    }
                    setTimeout(function () {
                        const rowCount = dtPayrollSheet.rows().count();
                        if (rowCount > 0 && isCollapsedPortlet == true) { isCollapsedPortlet = _tblPortletPS.expand(); }
                    }, 500);

                    if (typeof selectedCompany.id !== "undefined" && selectedCompany.id !== null && selectedCompany.id) {
                        const currentSelectCompanyId = selectedCompany.id;
                        $.ajax({
                            url: siteUrl("payroll/get_current_signatory_by_company_and_type/" + currentSelectCompanyId + "/1"),
                            dataType: "json",
                            success: function (json) {
                                let tempRow = Object.assign({});
                                let ctr = 0;
                                if (json.response) {
                                    tempRow = Object.assign({}, json.data);
                                    ctr = json.count;
                                }
                                vmTempSignatory.row = Object.assign({}, tempRow);
                                vmTempSignatory.count = ctr;
                                vmTempSignatory.$mount();

                                vmPortletSignatories.row = Object.assign({}, tempRow);
                                vmPortletSignatories.count = ctr;

                                vmResetSignatories.row = Object.assign({}, tempRow);
                                vmResetSignatories.count = ctr;
                            }
                        });
                    }
                }, true);

                vmPsNotification.absentee_count = absenteeCounter;
                if (absenteeCounter > 0) {
                    dtAbsenteeReport.clear();
                    $.each(tempAbsenteeData, function (k, v) {
                        v.id = k + 1;
                        dtAbsenteeReport.row.add(v);
                    });
                    dtAbsenteeReport.draw();
                }
            }
        }); ***/
        // original source code location
        return false;
    }
});

$("#form-emp-loans").submit( function(e){
    e.preventDefault();
    globalWithLoans = $(this).serialize();
    const date_range = $("input[name='date_range']", '#frm-filter').val();
    const employees = $("#employees", '#frm-filter').val();
    const company = $("#company", '#frm-filter').val();
    const payout_schedule = $("#payout_schedule", '#frm-filter').val();
    const payout_sequence = $("#payroll_sequence", '#frm-filter').val();
    const pay_date = $("input[name='pay_date']", '#frm-filter').val();
    const payrollGroup = $("#payroll_group", '#frm-filter').val();

    generate_ps(date_range, employees, company, payout_schedule, payout_sequence, pay_date, payrollGroup, globalWithLoans);
    $("#modal-ps--with-loans").modal('hide');
    return $(this).trigger('reset')[0];
});

$("#modal-ps--with-loans").on("shown.bs.modal", function(){
    const cancelAction = $(this).find("#cancel-emp-loans");
    if(typeof cancelAction !== "undefined" && cancelAction.length == 1){
        cancelAction.on("click", function(){
            const date_range = $("input[name='date_range']", '#frm-filter').val();
            const employees = $("#employees", '#frm-filter').val();
            const company = $("#company", '#frm-filter').val();
            const payout_schedule = $("#payout_schedule", '#frm-filter').val();
            const payout_sequence = $("#payroll_sequence", '#frm-filter').val();
            const pay_date = $("input[name='pay_date']", '#frm-filter').val();
            const payrollGroup = $("#payroll_group", "#frm-filter").val();
        
            setTimeout(function(){
                generate_ps(date_range, employees, company, payout_schedule, payout_sequence, pay_date, payrollGroup);
            }, 250);

            return $("#form-emp-loans").trigger('reset')[0];
        });
    }
});


// for generation of payroll sheet
function generate_ps(date_range, employees, company, payout_schedule, payout_sequence, pay_date, payroll_group, emp_with_loans = null){
    // original source code
    toastr.info("Please wait, The system is generating payroll sheet data!", "Generating Payroll Sheet Data");
    $.ajax({
        url: baseUrl("payroll/generate_payroll_sheet"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            date_range,
            employees,
            company,
            payout_schedule,
            payout_sequence,
            pay_date,
            payroll_group,
            emp_with_loans
        },
        dataType: "JSON",
        success: function (response) {
            showPosted = "_all";
            _globalFooterAdjustments = { sss: 0, sss_prov: 0, phic: 0, hdmf: 0, tax: 0, total_loans: 0 };
            _dtRowSSS = []; _dtRowSSS_PROV = []; _dtRowPHIC = []; _dtRowHDMF = []; _dtRowTAX = []; _dtRowLOAN = [];
            $("input.show_posted_record[value='_all']").prop("checked", true);

            let absenteeCounter = 0;
            let tempAbsenteeData = [];
            if (typeof response.undertime_records !== "undefined" && response.undertime_records.length > 0) {
                absenteeCounter = response.undertime_records.length;
                tempAbsenteeData = response.undertime_records;
            }
            
            if(typeof response.filtered_history !== "undefined" && response.filtered_history.length > 0){
                vmPsFilterHistory.rows = response.filtered_history;
                vmPsFilterHistory.count = response.filtered_history.length;
            }

            let contributionCounter = 0;
            let tempContributions = [];
            let tempContributionsFields = [];
            let unpostedCounter = 0;

            //only display printed payslip check if user has the action/permission
            if (jQuery.inArray("undo_print", _currentActions) !== -1) {
                checkPrintedPayslip(date_range, employees, company, payout_schedule, payout_sequence, pay_date, payroll_group);
            }
            //only display printed payslip check if user has the action/permission

            const generatedData = response.data;
            dtPayrollSheet.ajax.reload(function (_e) {
                try{
                    if (typeof generatedData !== "undefined" && generatedData.length > 0) {                    
                        let noContAcctNo = [];
                        let employeeLocation = [];

                        vmPsNotification.contributions = tempContributions;
                        vmPsNotification.contribution_fields = tempContributionsFields;
                        
                        $.each(generatedData, function (_i, v) {
                            let includeContribution = false;
                            if (typeof v.no_cont_acctno !== "undefined" && v.no_cont_acctno.length > 0) {
                                const tempData = { id: v.id, no_acct: v.no_cont_acctno };
                                noContAcctNo.push(tempData);
                            }
    
                            if(v.payroll_sheet_row !== "undefined" && Object.keys(v.payroll_sheet_row).length > 0){
                                if((v.days_worked > 0 && v.earnings > 0) && v.payroll_sheet_row.posted == 0){ 
                                    includeContribution = true;
                                    unpostedCounter++;
                                }
                            }
    
                            if(typeof v.contributions !== "undefined" && Object.keys(v.contributions).length > 0){
                                if(typeof v.contributions.data !== "undefined" && v.contributions.data.length > 0 && includeContribution){
                                    $.each(v.contributions.data, function(ck, cv){
                                        const rowDetails = Object.assign({}, cv, {employee_name: v.fullname });
                                        tempContributions.push(rowDetails);
                                    });
                                }
                                if(typeof v.contributions.column_fields !== "undefined" && v.contributions.column_fields.length > 0){
                                    tempContributionsFields = v.contributions.column_fields;
                                }
                            }

                            if(typeof v.earnings !== "undefined" && parseFloat(v.earnings) > 0){
                                const tempRowData = { employee_name: v.fullname, position: v.position, location: v.site_location };
                                employeeLocation.push(tempRowData);
                            }
                        });

                        vmPsNotification.location = [];
                        if(employeeLocation.length > 0){
                            const tempCtr = employeeLocation.length;
                            vmPsNotification.locations = employeeLocation;
                            vmPsNotification.location_count = employeeLocation.length;
                            vmPsNotification.renderSiteLocationDataTable();
                            toastr.info(`A total of ${tempCtr} employee(s) has been generated.`,"Employee Location Notification");
                        }

                        if (noContAcctNo.length > 0) {
                            $.ajax({
                                url: siteUrl("payroll/generate_employees_no_cont_acct"),
                                dataType: "json",
                                type: "post",
                                data: { [_csrf_token]: _csrf_hash, employee_account: noContAcctNo },
                                success: function (json) {
                                    let tempCount = 0;
                                    let tempRows = {};
                                    if (json.response) {
                                        tempCount = json.count;
                                        tempRows = Object.assign({}, json.data);
                                    }
                                    vmPsNotification.count = tempCount;
                                    if (vmPsNotification.notification_clicked === true) {
                                        vmPsNotification.notification_clicked = false;
                                    }
                                    vmPsNotificationModal.count = tempCount;
                                    vmPsNotificationModal.rows = Object.assign({}, tempRows);
                                    if(tempCount > 0){
                                        toastr.warning(`A total of ${tempCount} employee(s) without mandatory gov't number has been detected.`,"Employee Gov't Number Notification");
                                    }
                                }
                            });
                        }
    
                        if(tempContributionsFields.length > 0){
                            vmPsNotification.contribution_fields = tempContributionsFields;
                        }
    
                        vmPsNotification.contribution_count = contributionCounter;
                        if(tempContributions.length > 0 && unpostedCounter > 0){
                            contributionCounter = tempContributions.length;
                            vmPsNotification.contribution_count = contributionCounter;
                            vmPsNotification.contributions = tempContributions;
                            vmPsNotification.renderDataTable();
                        }
                    }
                }catch(error){ toastr.error(error, "Payroll Sheet - Data Rendering"); }

                setTimeout(function () {
                    const rowCount = dtPayrollSheet.rows().count();
                    if (rowCount > 0 && isCollapsedPortlet === true) { isCollapsedPortlet = _tblPortletPS.expand(); }
                }, 500);

                if (typeof selectedCompany.id !== "undefined" && selectedCompany.id !== null && selectedCompany.id) {
                    const currentSelectCompanyId = selectedCompany.id;
                    $.ajax({
                        url: siteUrl("payroll/get_current_signatory_by_company_and_type/" + currentSelectCompanyId + "/1"),
                        dataType: "json",
                        success: function (json) {
                            let tempRow = Object.assign({});
                            let ctr = 0;
                            if (json.response) {
                                tempRow = Object.assign({}, json.data);
                                ctr = json.count;
                            }
                            vmTempSignatory.row = Object.assign({}, tempRow);
                            vmTempSignatory.count = ctr;
                            vmTempSignatory.$mount();

                            vmPortletSignatories.row = Object.assign({}, tempRow);
                            vmPortletSignatories.count = ctr;

                            vmResetSignatories.row = Object.assign({}, tempRow);
                            vmResetSignatories.count = ctr;
                        }
                    });
                }

                if(contributionCounter > 0){ psContributionModal.modal("show"); }
            }, true);

            vmPsNotification.absentee_count = absenteeCounter;
            if (absenteeCounter > 0) {
                dtAbsenteeReport.clear();
                $.each(tempAbsenteeData, function (k, v) {
                    v.id = k + 1;
                    dtAbsenteeReport.row.add(v);
                });
                dtAbsenteeReport.draw();
            }


        }
    }).done(function(response){
        _show_modal_once = false;
    });
    // original source code
}
// for generation of payroll sheet
$("#payout_schedule")
    .select2({
        placeholder: "Select an option",
        width: "100%",
        data: _payoutSchedule,
    })
    .on("select2:select", function (option) {
        const data = option.params.data;
        const id = data.id;

        $("#payroll_sequence").empty();
        if(typeof psOccurrence[id] !== "undefined" && psOccurrence[id].length > 0){
            $.each(psOccurrence[id], function(_kk, _vv){
                const newOption = new Option(_vv.text, _vv.id, false, false);
                $("#payroll_sequence").append(newOption);
            });
            $("#payroll_sequence").trigger("change");
        }
        payroll_sequence_select2();

        /*** $.ajax({
            url: baseUrl(`payroll/get_payout_schedule_occurrence/${id}`),
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                $("#payroll_sequence").empty();

                response.forEach(item => {
                    const newOption = new Option(item.text, item.id, false, false);
                    $("#payroll_sequence")
                        .append(newOption)
                        .trigger("change");
                });

                payroll_sequence_select2();
            }
        }); ***/

        $(option.target).validate();
    });

var payroll_sequence_select2 = function () {
    $("#payroll_sequence")
        .select2({
            placeholder: "SELECT",
            width: "100%"
        }).on("select2:select", function (option) {
            $(option.target).validate();
        }).on("change", function (option) {
            $(option.target).validate();
        });
}
payroll_sequence_select2();

var vmPayrollParameterSettings = new Vue({
    el: "#payroll_parameter-container",
    data: {
        active_tax_status: false,
        fixed_tax_monthly_income: 0,
        fixed_tax_monthly_income_switch: 0,
        enable_zero_netpay: 0,
        ftmi_prop_switch: false,
        sss_contribution_basis: "basic_rate",
        admin_access: false,
    },
    watch: {
        fixed_tax_monthly_income_switch: function (value) {
            const _this = this;
            _this.ftmi_prop_switch = parseInt(value) == 1;
        }, 
    },
    methods: {
        propSwitch: function (e) {
            const _this = this;
            let isChecked = e.target.checked;
            _this.ftmi_prop_switch = isChecked;
            return _this;
        },
        validateTaxSettings: function () {
            $.validate({
                form: "#frm-payroll-settings",
                lang: "en",
                scrollToTopOnError: false,
                onSuccess: function (form) {
                    const tempUrl = form[0].action;
                    const formData = new FormData(form[0]);
                    formData.append('csrf_token', _csrf_hash);

                    $.ajax({
                        url: tempUrl,
                        type: "POST",
                        dataType: "JSON",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (json) {
                            if (json.response) {
                                toastr.success(json.taostr_msg, "Payroll Parameters", { timeOut: 5000 });
                            }
                        }
                    });
                    return false;
                }
            });
        },
        validateSssContributionSettings: function () {
            $.validate({
                form: "#frm-update-sss_contribution_basis",
                lang: "en",
                scrollToTopOnError: false,
                onSuccess: function (form) {
                    const tempUrl = form[0].action;
                    const formData = new FormData(form[0]);
                    formData.append('csrf_token', _csrf_hash);
                    $.ajax({
                        url: tempUrl,
                        type: "POST",
                        dataType: "JSON",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (json) {
                            if (json.response) {
                                toastr.success(json.taostr_msg, "Payroll Parameters", { timeOut: 5000 });
                            }
                        }
                    });
                    return false;
                }
            });
        }, validateZeroNetpaySettings: function () {
            $.validate({
                form: "#frm-update-zero_netpay",
                lang: "en",
                scrollToTopOnError: false,
                onSuccess: function (form) {
                    const tempUrl = form[0].action;
                    const formData = new FormData(form[0]);
                    formData.append('csrf_token', _csrf_hash);
                    $.ajax({
                        url: tempUrl,
                        type: "POST",
                        dataType: "JSON",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (json) {
                            if (json.response) {
                                toastr.success(json.taostr_msg, "Payroll Parameters", { timeOut: 5000 });
                            }
                        }
                    });
                    return false;
                }
            });
        }
    }
});

var vmPsNotification = new Vue({
    el: "#ps--notification",
    data: { 
        notification_clicked: false, 
        contribution_clicked: false, 
        contributions: [], 
        contribution_fields: [],
        locations: [],
        count: 0, 
        absentee_count: 0, 
        contribution_count: 0,
        location_count: 0,
    },
    methods: {
        showModalNotification: function () {
            const _this = this;
            if (_this.notification_clicked == false) {
                _this.notification_clicked = true;
            }
            return _this;
        }, tickContributionNotification: function (e) {
            const _this = this;
            if (_this.contribution_clicked == false) {
                _this.contribution_clicked = true;
            }
            return _this;
        }, tickSiteLocationNotification: function (e) {
            const _this = this;
            if (_this.location_clicked == false) {
                _this.location_clicked = true;
            }
            return _this;
        }, 
        renderDataTable: function(){
            const _this = this;
            tempFieldCount = _this.contribution_fields.length;
            const tempData = _this.contributions;
            let tempColumns = [];
            $.each(_this.contribution_fields, function(kkk, vvv){
                const tempTitle = vvv === 'payroll_seq' ? 'seq': (vvv === 'basic_rate' ? 'basic pay': vvv.replace(/_/g, ' '));
                const tempAlign = kkk > 4 ? 'text-right': (kkk == 4 ? 'text-center': '');
                const tempHeaderAlign = kkk > 4 ? 'text-right': (kkk == 4 ? 'text-center': '');

                const tempRow = { data: vvv, title: tempTitle, class: tempHeaderAlign, orderable: false,
                    createdCell: function(td){ if(tempAlign){ $(td).addClass(tempAlign); } } 
                };
                tempColumns.push(tempRow);
            });

            globalDtTable = tempContributionTable(tempColumns, tempFieldCount, globalDtTable);
            globalDtTable.clear();
            globalDtTable.rows.add(tempData);
            globalDtTable.draw();
        },
        renderSiteLocationDataTable: function(){
            const _this = this;
            const tempData = _this.locations;
            let tempColumns = [
                { data: null, title: "#", width: "3%", className: "text-center", render: function(_data, _display, _row, meta){
                    const tempIndex = parseInt(meta.row) + 1;
                    return tempIndex;
                } },
                { title: "Employee Name", data: "employee_name", width: "*" },
                { title: "Position", data: "position", width: "35%" },
                { title: "Site Location", data: "location", width: "35%" },
            ];

            const dtInit = $.fn.dataTable.isDataTable(globalSiteLocationDtTable);
            if(dtInit){ globalSiteLocationDtTable.destroy(); }

            globalSiteLocationDtTable = _tblSiteLocationReport.DataTable({
                dom: 'Brtlp',
                columns: tempColumns,
                data: [],
                ordering: false,
                columnDefs: [{ targets: ["_all"], defaultContent: "---" }],
                buttons: [{
                    extend: 'print',
                    text: '<i class="fa fa-print mr-1"></i> PRINT',
                    header: true,
                    className: "btnPrint btn btn-primary mb-2 pull-right",
                    title: function () {
                        const date_range = $("input[name='date_range']").val();
                        let coverage = '', tempPayDate = '';
                        if (date_range) {
                            const date_range_arr = date_range.split("-");
                            coverage = moment(date_range_arr[0]).format("MM/DD/YYYY") + " - " + moment(date_range_arr[1]).format("MM/DD/YYYY");
                        }
            
                        let tempPayrollGroup = "";
                        const pay_date = $("input[name='pay_date']").val();
                        if (pay_date) {
                            tempPayDate = moment(pay_date).format("MM/DD/YYYY");
                        }
                        if (typeof psEmployeeGroup !== "undefined" && typeof psEmployeeGroup == "object" && psEmployeeGroup.length > 0) {
                            tempPayrollGroup += psEmployeeGroup.join(" | ");
                        }
            
                        let newPayrollGroup = "";
                        if (tempPayrollGroup) {
                            tempPayrollGroup.toUpperCase();
                            newPayrollGroup = `<div class="m--regular-font-size-sm1 mt-1">PAYROLL GROUP: <span style='font-weight: 600; text-transform: uppercase;'>${tempPayrollGroup}</span></div>`;
                        }
                        let tempCompanyDesc = selectedCompany.description;
                        tempCompanyDesc.toUpperCase();
                        return `<div class="text-center m--regular-font-size-lg2" style="text-transform: uppercase;">${tempCompanyDesc}</div>
                                <div class="text-center m--regular-font-size-sm1 text-muted" style="text-transform: uppercase;">${selectedCompany.company_address}</div>
                                <div class="text-center m--regular-font-size-lg1 mt-2">PAYROLL SHEET - EMPLOYEE LOCATION REPORT</div>
                                <div class="m--regular-font-size-sm1 mt-2">PAY DATE: ${tempPayDate}</div>
                                <div class="m--regular-font-size-sm1 mt-1">PAY COVERAGE: ${coverage}</div>
                                ${newPayrollGroup}`;
                    },
                    customize: function (win) {
                        win.document.title = "Payroll Sheet Printable Page";
                        var css = `@page { size: portrait; margin: 0.5cm; } table { font-size: 12px; }`,
                            head = win.document.head || win.document.getElementsByTagName('head')[0],
                            style = win.document.createElement('style');
            
                        style.type = 'text/css';
                        style.media = 'print';
            
                        if (style.styleSheet) {
                            style.styleSheet.cssText = css;
                        } else { style.appendChild(win.document.createTextNode(css)); }
                        head.appendChild(style);

                        var tempTable = win.document.getElementsByClassName('dataTable')[0];
                        $(tempTable).removeClass("table-bordered");
                    }
                }],
            });

            globalSiteLocationDtTable.clear();
            globalSiteLocationDtTable.rows.add(tempData);
            globalSiteLocationDtTable.draw();
        },
    }
});

var vmPsNotificationModal = new Vue({
    el: "#tempPsNotificationContent",
    data: { count: 0, rows: {} },
});

var vmPsFilterHistoryModal = new Vue({
    el: "#filterHistoryContent",
    data: { count: 0, rows: {} },
    methods: {
        generateFilterHistory: function(data){
            $("#frm-filter select#company").val(data.company).trigger("change");
            $("#frm-filter select#payout_schedule").val(data.payout_schedule).trigger("change");

            selectedCompany = Object.assign({}, data.company_collection);
            const tempId = data.payout_schedule;
            if(typeof psOccurrence[tempId] !== "undefined" && psOccurrence[tempId].length > 0){
                $("#payroll_sequence").empty();
                $.each(psOccurrence[tempId], function(_kk, _vv){
                    const newOption = new Option(_vv.text, _vv.id, false, false);
                    $("#payroll_sequence").append(newOption);
                });
                $("#frm-filter #payroll_sequence").val(data.payout_sequence).trigger("change");
            }
            payroll_sequence_select2();

            setPayDate(data.pay_date);
            $("#frm-filter input[name='pay_date']").val(data.pay_date);
            
            const tempStart = moment(data.date_start, "YYYY-MM-DD").format("MM/DD/YYYY");
            const tempEnd = moment(data.date_end, "YYYY-MM-DD").format("MM/DD/YYYY");
            generateDateTimePicker(tempRangeDates.min_date, tempRangeDates.max_date, tempStart, tempEnd);
            $("#frm-filter #date-range").val(data.date_range);

            if(data.group_count > 0){
                $.each(data.group_collection, function(_kx, vx){
                    const newGroupOption = new Option(vx.text, vx.id, false, false);
                    $("#frm-filter #payroll_group").append(newGroupOption);
                });
                $("#frm-filter #payroll_group").val(data.payroll_group).trigger("change");
            }

            $("#frm-filter #employees").prop("disabled", false);
            if(data.employees_count > 0){
                const hasPayrollGroup = data.group_count > 0;
                $("#frm-filter #employees").empty();
                $.each(data.employees_collection, function(_kxx, vxx){
                    const newGroupOption = new Option(vxx.text, vxx.id, false, false);
                    $("#frm-filter #employees").append(newGroupOption);
                });
                $("#frm-filter #employees")
                .val(data.employees)
                .trigger("change")
                .prop("disabled", hasPayrollGroup);
            }

            $("#company, #payout_schedule, #date-range").validate();
            psFilterHistoryModal.modal("hide");
            toastr.info("Payroll Sheet, history filter has been applied.", "Payroll Sheet - Filter History", { timeOut: 5000 });
        }
    }
});

$("#payroll-parameters-modal")
    .on("show.bs.modal", function () {
        const modal = $(this);
        $.ajax({
            url: baseUrl(`payroll/get_remittance_parameters`),
            dataType: "JSON",
            type: "GET",
            beforeSend: function () {
            },
            success: function (response) {
                const remittancesContainer = $("#remittances-container", modal);
                let isActiveTaxStatus = false;
                remittancesContainer.empty();

                response.forEach((item) => {
                    if (item.remittance_code == "TAX" && item.status == "1") { isActiveTaxStatus = true; }
                    const template = `
                        <div class="form-group row">
                            <input type="hidden" name="id[]" value="${item.id}">
                            <label class="col col-form-label">
                                <span class="m--regular-font-size-lg1 m--font-boldest">
                                    ${item.remittance_name}
                                </span>
                            </label>
                            <div class="col-3 text-right">
                                <span class="m-switch m-switch--outline m-switch--icon m-switch--success">
                                    <label class="mb-0">
                                        <input type="checkbox" name="status_${item.id}"  value="${item.status}"
                                               ${parseInt(item.status) === 1 ? "checked" : ""}>
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>`;
                    remittancesContainer.append(template);
                });

                vmPayrollParameterSettings.active_tax_status = isActiveTaxStatus;
                setTimeout(function () {
                    vmPayrollParameterSettings.validateTaxSettings();
                }, 500);
            }
        });

        $.ajax({
            url: baseUrl(`payroll/get_payroll_settings`),
            dataType: "JSON",
            type: "GET",
            beforeSend: function () {
            },
            success: function (json) {
                vmPayrollParameterSettings.admin_access = typeof json.admin_access !== "undefined" ? json.admin_access : false;
                if (json.response) {
                    const settingsProp = ["fixed_tax_monthly_income", "fixed_tax_monthly_income_switch", "sss_contribution_basis", "enable_zero_netpay"];
                    const tempData = json.data;
                    $.each(tempData, function (i, v) {
                        if (settingsProp.includes(v.setting_name)) {
                            vmPayrollParameterSettings[v.setting_name] = v.setting_value;
                        }
                    });
                    setTimeout(function () {
                        vmPayrollParameterSettings.validateTaxSettings();
                        vmPayrollParameterSettings.validateSssContributionSettings();
                        vmPayrollParameterSettings.validateZeroNetpaySettings();
                    }, 500);
                }

            },
        });

        $.ajax({
            url: baseUrl(`payroll/get_pay_rate_settings`),
            dataType: "JSON",
            type: "GET",
            beforeSend: function () {
            },
            success: function (response) {
                const payRateSettingTable = $("#pay-rate-setting-container table tbody", modal);
                payRateSettingTable.empty();

                response.forEach((item, idx) => {
                    const template = `<tr>
                                            <td>${item.particulars}</td>
                                            <td width="15%" class="text-right" style="vertical-align: top;">
                                                <input type="text" size="3" value="${item.regular_rate}"
                                                       name="regular_rate_${idx}"
                                                       class="text-right" disabled>
                                            </td>
                                            <td width="15%" class="text-right" style="vertical-align: top;">
                                                <input type="text" size="3" value="${item.night_diff_rate}"
                                                       name="night_diff_rate_${idx}"
                                                       class="text-right" disabled>
                                            </td>
                                            <td width="15%" class="text-right" style="vertical-align: top;">
                                                <input type="text" size="3" value="${item.ot_rate}"
                                                       name="ot_rate_${idx}"
                                                       class="text-right" disabled>
                                            </td>
                                            <td width="15%" class="text-right" style="vertical-align: top;">
                                                <input type="text" size="3" value="${item.ot_night_diff_rate}"
                                                       name="ot_night_diff_rate_${idx}"
                                                       class="text-right" disabled>
                                            </td>
                                            <td width="8%" class="text-center">
                                                <button type="button" class="btn btn-default m-btn m-btn--icon m-btn--hover-primary
                                                                             m-btn--icon-only m-btn--pill btn-sm edit"
                                                        onclick="editRow(${item.id}, ${idx})"
                                                        data-toggle="m-tooltip" data-original-title="Edit"
                                                        data-skin="dark">
                                                    <i class="fa fa-pencil"></i>
                                                </button>

                                                <button type="button" class="btn btn-default m-btn m-btn--icon m-btn--hover-success
                                                                             m-btn--icon-only m-btn--pill btn-sm save m--hide"
                                                        onclick="saveChanges(${item.id}, ${idx})"
                                                        data-toggle="m-tooltip" data-original-title="Save Changes"
                                                        data-skin="dark">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            </td>
                                      </tr>`;
                    payRateSettingTable.append(template);
                });
            }
        });
    });

function editRow(id, index) {
    const row = $("#pay-rate-setting-container table tbody").find(`tr:eq(${index})`);
    const firstEl = $('input[type="text"]', row).eq(0);
    setTimeout(function () {
        firstEl.focus();
    }, 10);
    $('input[type="text"]', row).prop("disabled", false);
    $(".edit", row).addClass("m--hide");
    $(".save", row).removeClass("m--hide");
}

function saveChanges(id, index) {
    const regular_rate = $("input[name='regular_rate" + "_" + index + "']").val();
    const night_diff_rate = $("input[name='night_diff_rate" + "_" + index + "']").val();
    const ot_rate = $("input[name='ot_rate" + "_" + index + "']").val();
    const ot_night_diff_rate = $("input[name='ot_night_diff_rate" + "_" + index + "']").val();

    const row = $("#pay-rate-setting-container table tbody").find(`tr:eq(${index})`);

    $.ajax({
        url: baseUrl(`payroll/update_pay_rate_setting`),
        type: "POST",
        dataType: "JSON",
        global: false,
        data: {
            csrf_token: _csrf_hash,
            id,
            regular_rate,
            night_diff_rate,
            ot_rate,
            ot_night_diff_rate,
        },
        success: function (response) {
            $('input[type="text"]', row).prop("disabled", true);
            $(".edit", row).removeClass("m--hide");
            $(".save", row).addClass("m--hide");

            if (response) {
                toastr.success("Pay rate successfully updated.", "Changes was saved.", { timeOut: 10000 });
            }
        }
    });
}

$.validate({
    form: $("#frm-update-remittance-parameters"),
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const formData = new FormData(form[0]);
        formData.append('csrf_token', _csrf_hash);

        $.ajax({
            url: baseUrl('payroll/update_remittance_parameters'),
            type: "POST",
            dataType: "JSON",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                let isActiveTaxStatus = vmPayrollParameterSettings.active_tax_status;
                if (response) {
                    toastr[response.toast](response.message, response.title, { timeOut: 10000 });
                    if (typeof response.updated_count !== "undefined" && parseInt(response.updated_count) > 0) {
                        $.each(response.updated_parameters, function (i, v) {
                            if (v.remittance_code == "TAX") { isActiveTaxStatus = parseInt(v.status) == 1; }
                        });
                    }
                }
                vmPayrollParameterSettings.active_tax_status = isActiveTaxStatus;
                setTimeout(function () {
                    vmPayrollParameterSettings.validateTaxSettings();
                }, 500);
            }
        });

        return false;
    }
});

cbSelectAll.on('change', function (e) {
    const checkedValue = e.target.checked;
    // select all in current page only
    $('tbody input[type=\'checkbox\']', _tblPayrollSheet).prop('checked', checkedValue);
    dtPayrollSheet.draw();
});

_tblPayrollSheet.on('change', 'tbody input[type=\'checkbox\']', function () { checkCbSelectAll(); });
_tblPayrollSheet.on('draw.dt', function () { checkCbSelectAll(); });

function checkCbSelectAll() {
    const cbCount = $('tbody input[type=\'checkbox\']', _tblPayrollSheet).length;
    const checkedCbCount = $('tbody input[type=\'checkbox\']:checked', _tblPayrollSheet).length;

    if (parseInt(checkedCbCount) >= 1) {
        $('#btn-verify').removeAttr('disabled');
    } else {
        $('#btn-verify').attr('disabled', 'true');
    }

    cbSelectAll.prop('checked', (parseInt(cbCount) === parseInt(checkedCbCount) && parseInt(checkedCbCount) >= 1));
}

function openManageCustomRateAdjustmentsModal(payroll_sheet_id, complete_name) {
    $("form", manageCustomRateAdjustmentsModal).attr("data-ps_id", payroll_sheet_id);
    $("form #display_name", manageCustomRateAdjustmentsModal).text(complete_name);
    manageCustomRateAdjustmentsModal.modal("show");
}

manageCustomRateAdjustmentsModal.on("show.bs.modal", function () {
    const payroll_sheet_id = $(this).attr("data-ps_id");
    dtRateAdjustments = $("table", this).DataTable({
        dom: "rtlp",
        serverSide: false,
        autoWidth: false,
        destroy: true,
        ajax: {
            url: baseUrl(`payroll/get_payroll_sheet_custom_rate_adjustments/${payroll_sheet_id}`),
            type: "GET",
            dataType: "JSON"
        },
    });
});

function openManageCustomAdjustmentsModal(payroll_sheet_id, posted) {
    $("form", manageCustomAdjustmentsModal).attr("data-posted", posted);
    $("form :input", manageCustomAdjustmentsModal).prop("disabled", (parseInt(posted) === 1));

    $("input[name='payroll_sheet_id']", manageCustomAdjustmentsModal).val(payroll_sheet_id);
    $("input[name='amount']").maskMoney({ thousands: ',', decimal: '.', allowNegative: true });
    manageCustomAdjustmentsModal.modal("show");
}

function openCreatedAdjustmentsModal(payroll_sheet_id, posted) {
    $("form", manageCreatedAdjustmentsModal).attr("data-posted", posted);
    $("form", manageCreatedAdjustmentsModal).attr("data-mode", "add");
    $("form :input", manageCreatedAdjustmentsModal).prop("disabled", (parseInt(posted) === 1));

    $("input[name='payroll_sheet_id']", manageCreatedAdjustmentsModal).val(payroll_sheet_id);
    $("input[name='amount']").maskMoney({ thousands: ',', decimal: '.', allowNegative: true });
    manageCreatedAdjustmentsModal.modal("show");
}

init();
let dtCustomAdjustments = null;

manageCustomAdjustmentsModal.on("show.bs.modal", function () {
    const form = $("form", this);
    const payroll_sheet_id = $("input[name='payroll_sheet_id']", form).val();
    const posted = form.attr("data-posted");

    dtCustomAdjustments = $("table", this).DataTable({
        dom: "rtlp",
        serverSide: false,
        autoWidth: false,
        destroy: true,
        ajax: {
            url: baseUrl(`payroll/get_payroll_sheet_custom_adjustments/${payroll_sheet_id}`),
            type: "GET",
            dataType: "JSON"
        },
        columns: [
            {
                data: "particulars",
                render: function (data, type, row) {
                    return `<p class="m--font-bolder mb-0">${data}</p>
                            <p class="m--regular-font-size-sm1 mb-0 mt-1 text-muted">${row.description}</p>`;
                }
            },
            {
                width: "20%",
                className: "text-right pr-5",
                data: "amount",
                render: function (data, type, row) {
                    const style = parseInt(row.cadj_type) === 0 ? "m--font-danger" : "";
                    return `<span class="${style} m--font-boldest">${parseFloat(data).toLocaleString("en-US", { maximumFractionDigits: 2 })}</span>`;
                }
            },
            {
                width: "28%",
                data: "created_at",
                render: function (data, type, row) {
                    return `<p class="mb-0 m--font-bolder">${row._created_by}</p>
                            <p class="mb-0 text-muted">${data}</p>`;
                }
            },
            {
                width: "15%",
                className: "text-center",
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    let buttons = ``;

                    if (parseInt(posted) === 1) {
                        return `---`;
                    }

                    if (_currentActions.includes("edit")) {
                        buttons += ` <button type="button"
                                            class="btn btn-default btn-sm m-btn m-btn--icon m-btn--icon-only
                                                   m-btn--hover-primary
                                                   m-btn--pill"
                                            onclick="editCustomAdjustment(${row.id})">
                                        <i class="fa fa-pencil"></i>
                                     </button>`;
                    }

                    if (_currentActions.includes("delete")) {
                        buttons += ` <button type="button"
                                            class="btn btn-default btn-sm m-btn m-btn--icon m-btn--icon-only
                                                   m-btn--hover-danger
                                                   m-btn--pill"
                                            onclick="openDeleteCustomAdjustmentConfirmation(${row.id})">
                                        <i class="fa fa-trash-o"></i>
                                     </button>`;
                    }
                    return buttons;
                }
            },
        ],
        order: [[2, "DESC"]]
    });
});

manageCreatedAdjustmentsModal.on("show.bs.modal", function () {
    const form = $("form", this);
    const payroll_sheet_id = $("input[name='payroll_sheet_id']", form).val();
    const posted = form.attr("data-posted");

    var select2Particular = form.find("#temp_particulars");
    if (typeof select2Particular !== "undefined" && select2Particular.length == 1) {
        select2Particular.select2({
            width: "100%",
            placeholder: "Select an option",
        });
    }

    dtCreatedAdjustments = $("table", this).DataTable({
        dom: "rtlp",
        serverSide: false,
        autoWidth: false,
        destroy: true,
        ajax: {
            url: baseUrl(`payroll/get_payroll_sheet_created_adjustments/${payroll_sheet_id}`),
            type: "GET",
            dataType: "JSON"
        },
        columns: [{
            data: "particulars",
            render: function (data, type, row) {
                return `<p class="m--font-bolder mb-0">${data}</p>
                        <p class="m--regular-font-size-sm1 mb-0 mt-1 text-muted">${row.description}</p>`;
            }
        }, {
            width: "20%",
            className: "text-right pr-5",
            data: "amount",
            render: function (data, type, row) {
                const style = parseInt(row.cadj_type) === 0 ? "m--font-danger" : "";
                return `<span class="${style} m--font-boldest">${parseFloat(data).toLocaleString("en-US", { maximumFractionDigits: 2 })}</span>`;
            }
        }, {
            width: "28%",
            data: "created_at",
            render: function (data, type, row) {
                return `<p class="mb-0 m--font-bolder">${row._created_by}</p>
                            <p class="mb-0 text-muted">${data}</p>`;
            }
        }, {
            width: "15%",
            className: "text-center",
            data: null,
            orderable: false,
            render: function (data, type, row) {
                var ctrButtons = 0;
                var listButtons = [];
                let buttons = ``;
                var tempStatus = parseInt(row.status);
                if (parseInt(posted) === 1) { return `---`; }
                if (_currentActions.includes("edit") && tempStatus == 0) {
                    buttons += ` <button type="button"
                        class="btn btn-default btn-sm m-btn m-btn--icon m-btn--icon-only
                                m-btn--hover-primary
                                m-btn--pill"
                        onclick="editCreatedAdjustment(${row.id})">
                    <i class="fa fa-pencil"></i>
                    </button>`;
                    listButtons.push(`<li class="m-nav__item">
                        <a href="javascript:void(0)" class="m-nav__link"
                        onclick="editCreatedAdjustment(${row.id})">
                        <i class="m-nav__link-icon fa fa-pencil"></i>
                        <span class="m-nav__link-text">Edit</span>
                        </a>
                    </li>`);
                    ctrButtons++;
                }

                if (_currentActions.includes("delete") && tempStatus == 0) {
                    buttons += ` <button type="button"
                    class="btn btn-default btn-sm m-btn m-btn--icon m-btn--icon-only
                    m-btn--hover-danger
                    m-btn--pill"
                    onclick="openDeleteCreatedAdjustmentConfirmation(${row.id})">
                    <i class="fa fa-trash-o"></i>
                    </button>`;
                    listButtons.push(`<li class="m-nav__item">
                        <a href="javascript:void(0)" class="m-nav__link"
                        onclick="openDeleteCreatedAdjustmentConfirmation(${row.id})">
                        <i class="m-nav__link-icon fa fa-trash-o"></i>
                        <span class="m-nav__link-text">Remove</span>
                        </a>
                    </li>`);
                    ctrButtons++;
                }
                if ((_currentActions.includes("approve_action") || _currentActions.includes("approving_authority")) && tempStatus == 0) {
                    buttons += ` <button type="button"
                    class="btn btn-default btn-sm m-btn m-btn--icon m-btn--icon-only
                    m-btn--hover-success
                    m-btn--pill"
                    onclick="openApprovalCreatedAdjustmentConfirmation(${row.id}, 'approve')">
                    <i class="fa fa-thumbs-up"></i>
                    </button>`;
                    listButtons.push(`<li class="m-nav__separator m-nav__separator--fit"></li>
                    <li class="m-nav__item">
                        <a href="javascript:void(0)" class="m-nav__link"
                        onclick="openApprovalCreatedAdjustmentConfirmation(${row.id}, 'approve')">
                        <i class="m-nav__link-icon fa fa-thumbs-up"></i>
                        <span class="m-nav__link-text">Approve</span>
                        </a>
                    </li>`);
                    ctrButtons++;
                }

                if ((_currentActions.includes("disapprove_action") || _currentActions.includes("approving_authority")) && tempStatus == 0) {
                    buttons += ` <button type="button"
                    class="btn btn-default btn-sm m-btn m-btn--icon m-btn--icon-only
                    m-btn--hover-danger
                    m-btn--pill"
                    onclick="openApprovalCreatedAdjustmentConfirmation(${row.id}, 'disapprove')">
                    <i class="fa fa-thumbs-down"></i>
                    </button>`;
                    listButtons.push(`<li class="m-nav__item">
                        <a href="javascript:void(0)" class="m-nav__link"
                        onclick="openApprovalCreatedAdjustmentConfirmation(${row.id}, 'disapprove')">
                        <i class="m-nav__link-icon fa fa-thumbs-down"></i>
                        <span class="m-nav__link-text">Disapprove</span>
                        </a>
                    </li>`);
                    ctrButtons++;
                }

                if ((_currentActions.includes("undo_approval") || _currentActions.includes("approving_authority")) && tempStatus == 1) {
                    buttons += ` <button type="button"
                    class="btn btn-default btn-sm m-btn m-btn--icon m-btn--icon-only
                    m-btn--hover-warning
                    m-btn--pill"
                    onclick="openApprovalCreatedAdjustmentConfirmation(${row.id}, 'undo_approve')">
                    <i class="fa fa-undo"></i>
                    </button>`;
                    listButtons.push(`<li class="m-nav__item">
                        <a href="javascript:void(0)" class="m-nav__link"
                        onclick="openApprovalCreatedAdjustmentConfirmation(${row.id}, 'undo_approve')">
                        <i class="m-nav__link-icon fa fa-undo"></i>
                        <span class="m-nav__link-text">Undo Approve</span>
                        </a>
                    </li>`);
                    ctrButtons++;
                }

                if ((_currentActions.includes("undo_disapproval") || _currentActions.includes("approving_authority")) && tempStatus == 2) {
                    buttons += ` <button type="button"
                    class="btn btn-default btn-sm m-btn m-btn--icon m-btn--icon-only
                    m-btn--hover-warning
                    m-btn--pill"
                    onclick="openApprovalCreatedAdjustmentConfirmation(${row.id}, 'undo_disapprove')">
                    <i class="fa fa-undo"></i>
                    </button>`;
                    listButtons.push(`<li class="m-nav__item">
                        <a href="javascript:void(0)" class="m-nav__link"
                        onclick="openApprovalCreatedAdjustmentConfirmation(${row.id}, 'undo_disapprove')">
                        <i class="m-nav__link-icon fa fa-undo"></i>
                        <span class="m-nav__link-text">Undo Disapprove</span>
                        </a>
                    </li>`);
                    ctrButtons++;
                }

                if (ctrButtons > 2) {
                    var tempButtons = '';
                    $.each(listButtons, function (i, v) {
                        tempButtons += v;
                    });

                    buttons = `<div class="m-dropdown m-dropdown--inline m-dropdown--align-right" data-dropdown-toggle="click" aria-expanded="true">
                        <a href="#" class="m-dropdown__toggle btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill"
                            data-toggle="m-tooltip" data-original-title="More Options" data-skin="dark"
                            data-delay='{"show": 500}'>
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                        <div class="m-dropdown__wrapper">
                            <span class="m-dropdown__arrow m-dropdown__arrow--right"></span>
                            <div class="m-dropdown__inner">
                                <div class="m-dropdown__body">
                                    <div class="m-dropdown__content">
                                        <ul class="m-nav">
                                            <li class="m-nav__section m-nav__section--first">
                                                <span class="m-nav__section-text">OPTIONS</span>
                                            </li>
                                            ${tempButtons}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                }
                return buttons;
            }
        }],
        order: [[2, "DESC"]]
    });
});

function editCreatedAdjustment(adj_id) {
    $.ajax({
        url: baseUrl(`payroll/get_created_adjustment/${adj_id}`),
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function (response) {
            var tempParticular = response.particulars;
            tempParticular = tempParticular.toLowerCase();

            $("input[name='id']", manageCreatedAdjustmentsModal).val(adj_id);
            $("select[name='particulars']", manageCreatedAdjustmentsModal).val(tempParticular).trigger("change");
            $("input[name='amount']", manageCreatedAdjustmentsModal).val((parseInt(response.adj_id) === 0 ? "-" : "") + response.amount);
            $("textarea[name='description']", manageCreatedAdjustmentsModal).val(response.description);
            $("form", manageCreatedAdjustmentsModal).attr("data-mode", "edit");
            $(".btnSave", manageCreatedAdjustmentsModal).html("Save Changes");
        }
    });
}

function editCustomAdjustment(cadj_id) {
    $.ajax({
        url: baseUrl(`payroll/get_custom_adjustment/${cadj_id}`),
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function (response) {
            $("input[name='id']", manageCustomAdjustmentsModal).val(cadj_id);
            $("input[name='particulars']", manageCustomAdjustmentsModal).val(response.particulars);
            $("input[name='amount']", manageCustomAdjustmentsModal).val((parseInt(response.cadj_type) === 0 ? "-" : "") + response.amount);
            $("textarea[name='description']", manageCustomAdjustmentsModal).val(response.description);
            $("form", manageCustomAdjustmentsModal).attr("data-mode", "edit");
            $(".btnSave", manageCustomAdjustmentsModal).html("Save Changes");
        }
    });
}

function openApprovalCreatedAdjustmentConfirmation(adj_id, type = 'approve') {
    var tempStatus = 0;
    switch (type) {
        case 'approve': tempStatus = 1; break;
        case 'disapprove': tempStatus = 2; break;
        case 'undo_approve': type = 'undo approve'; tempStatus = 4; break;
        case 'undo_disapprove': type = 'undo disapprove'; tempStatus = 3; break;
        default: tempStatus = 0; break;
    }

    $("form #temp_status", confirmApprovalCreatedAdjustmentModal).text(type);
    $("form #adj_id", confirmApprovalCreatedAdjustmentModal).val(adj_id);
    $("form #adj_status", confirmApprovalCreatedAdjustmentModal).val(tempStatus);
    confirmApprovalCreatedAdjustmentModal.modal("show");
}

function openDeleteCreatedAdjustmentConfirmation(adj_id) {
    $("form", confirmDeleteCreatedAdjustmentModal).attr('data-id', adj_id);
    confirmDeleteCreatedAdjustmentModal.modal("show");
}

function openDeleteCustomAdjustmentConfirmation(cadj_id) {
    $("form", confirmDeleteCustomAdjustmentModal).attr('data-id', cadj_id);
    confirmDeleteCustomAdjustmentModal.modal("show");
}

function init() {
    $('input[name="particulars"]')
        .autocomplete('dispose')
        .autocomplete({
            serviceUrl: baseUrl(`payroll/get_custom_adjustment_particulars`),
            ajaxSettings: {
                global: false
            },
            showNoSuggestionNotice: true,
        });
}

$.validate({
    form: $("#frm-approval-created-adjustments"),
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const type = $(form).attr("data-type");
        $.ajax({
            url: baseUrl(`payroll/created_approval_adjustments`),
            type: "POST",
            data: $(form).serialize(),
            dataType: "JSON",
            success: function (response) {
                init();
                if (response.success == true) {
                    resetManageCreatedAdjustmentsForm('add');
                    dtCreatedAdjustments.ajax.reload();
                    /*** dtPayrollSheet.ajax.reload(); ***/
                    _show_modal_once = true; // show only loan modal when generating
                    setTimeout(function () {
                        tempRegeneratePayroll();
                    }, 500);
                    confirmApprovalCreatedAdjustmentModal.modal("hide");
                }
                if (response) {
                    toastr[response.toast](response.message, response.title, { timeOut: 10000 });
                }
            }
        });

        return false;
    }
});

$.validate({
    form: $("#frm-manage-custom-adjustments"),
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const mode = $(form).attr("data-mode");
        $.ajax({
            url: baseUrl(`payroll/manage_custom_adjustments/${mode}`),
            type: "POST",
            data: $(form).serialize(),
            dataType: "JSON",
            success: function (response) {
                init();
                resetManageCustomAdjustmentsForm(mode);
                dtCustomAdjustments.ajax.reload();
                /*** dtPayrollSheet.ajax.reload(); ***/
                setTimeout(function () {
                    _show_modal_once = true; // show only loan modal when generating
                    tempRegeneratePayroll();
                }, 500);
                if (response) {
                    toastr[response.toast](response.message, response.title, { timeOut: 10000 });
                }
            }
        });

        return false;
    }
});

$.validate({
    form: $("#frm-manage-created-adjustments"),
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const mode = $(form).attr("data-mode");
        $.ajax({
            url: baseUrl(`payroll/manage_created_adjustments/${mode}`),
            type: "POST",
            data: $(form).serialize(),
            dataType: "JSON",
            success: function (response) {
                init();
                const temp_mode = (response.success == true) ? "add" : "edit";
                if (response.success == true) {
                    resetManageCreatedAdjustmentsForm(temp_mode);
                    dtCreatedAdjustments.ajax.reload();
                    dtPayrollSheet.ajax.reload();
                }

                if (response) {
                    toastr[response.toast](response.message, response.title, { timeOut: 10000 });
                }
            }
        });

        return false;
    }
});

$.validate({
    form: $("#frm-confirm-delete-created-adjustment"),
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const id = $(form).attr("data-id");
        $.ajax({
            url: baseUrl(`payroll/delete_created_adjustments/${id}`),
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                if (response) {
                    toastr[response.toast](response.message, response.title, { timeOut: 10000 });
                }
                dtCreatedAdjustments.ajax.reload();
                /*** dtPayrollSheet.ajax.reload(); ***/
                _show_modal_once = true; // show only loan modal when generating
                setTimeout(function () {
                    tempRegeneratePayroll();
                }, 500);
                $("#modal-confirm-delete-created-adjustment").modal("hide");
            }
        });
        return false;
    }
});

$.validate({
    form: $("#frm-confirm-delete-custom-adjustment"),
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const id = $(form).attr("data-id");
        $.ajax({
            url: baseUrl(`payroll/delete_custom_adjustments/${id}`),
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                if (response) {
                    toastr[response.toast](response.message, response.title, { timeOut: 10000 });
                }

                dtCustomAdjustments.ajax.reload();
                /*** dtPayrollSheet.ajax.reload(); ***/
                _show_modal_once = true; // show only loan modal when generating
                setTimeout(function () {
                    tempRegeneratePayroll();
                }, 500);
                $("#modal-confirm-delete-custom-adjustment").modal("hide");
            }
        });
        return false;
    }
});

function resetManageCustomAdjustmentsForm(mode = 'edit') {
    if (mode === 'edit') {
        $("input[name='id']").val("");
    }

    $("input[name='particulars']").val("");
    $("input[name='amount']").val("");
    $("textarea[name='description']").val("");
    $(".btnSave", manageCustomAdjustmentsModal).html("Save");
}

function resetManageCreatedAdjustmentsForm(mode = 'edit') {
    if (mode === 'edit') {
        $("input[name='id']").val("");
    }

    $("select[name='particulars']").val("").trigger("change");
    $("input[name='amount']").val("");
    $("textarea[name='description']").val("");
    $(".btnSave", manageCreatedAdjustmentsModal).html("Save");
}

$(document)
    .on('show.bs.modal', '.modal', function () {
        /*var zIndex = Math.max.apply(null, Array.prototype.map.call(document.querySelectorAll('*'), function (el) {
            return +el.style.zIndex;
        })) + 200;*/

        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex - 1);

        setTimeout(function () {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 2).addClass('modal-stack');
        }, 0);
    });

function viewTimesheet(emp_id, employee_name) {
    viewTimesheetModal.attr("data-emp_id", emp_id);
    viewTimesheetModal.attr("data-employee_name", employee_name);
    viewTimesheetModal.modal("show");
}

let global_print_key = [];

viewTimesheetModal.on("show.bs.modal", function () {
    const emp_id = $(this).attr("data-emp_id");
    dtEmployeeTimesheet = $("table", this).DataTable({
        dom: 'rtlp',
        serverSide: false,
        autoWidth: false,
        paging: false,
        pagination: false,
        destroy: true,
        ajax: {
            url: baseUrl(`payroll/get_employee_timesheet`),
            type: "POST",
            dataType: "JSON",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.emp_id = emp_id;
                d.date_range = $('input[name="date_range"]', form).val();
            }
        },
        // createdRow: function(row, data, dataIndex){
        //     // console.log(dataIndex +'/'+data.id+'-'+data.is_holiday+'-'+data.weekday+'-'+data.date);
        //     if(data.id == null && data.is_holiday == null && data.weekday != 'sunday'){
        //         global_print_key.push(data.date); 
        //     }
        // },
        columns: [
            {
                data: 'date',
                render: function (data, type, row, meta) {
                    //display indication if employee is absent, if date is holiday and if employee has overtime
                    let if_holiday;
                    let overtime = " <em class='fa fa-clock-o ml-1' style='color:#5867dd;'></em>";
                    let holiday = " <em class='fa fa-flag ml-1' style='color:#ffb822;'>";
                    let absent = " <em class='fa fa-times-rectangle ml-1' style='color:#5c5d62;'>";
                    if (row.id == null && row.is_holiday == null && row.weekday != 'sunday') {
                        if_holiday = moment(data).format("MM/DD/YYYY") + absent
                    } else {
                        if (row.is_holiday == 1 && row.total_accredited_ot_hrs > 0) {
                            if_holiday = moment(data).format("MM/DD/YYYY") + holiday + overtime;
                        } else if (row.is_holiday == 1) {
                            if_holiday = moment(data).format("MM/DD/YYYY") + holiday;
                        } else if (row.total_accredited_ot_hrs > 0) {
                            if_holiday = moment(data).format("MM/DD/YYYY") + overtime;
                        } else {
                            if_holiday = moment(data).format("MM/DD/YYYY");
                        }
                    }

                    return if_holiday;
                },
                width: "12%",
            },
            {
                data: 'weekday',
                orderable: false,
                render: function (data) {
                    return data ? data.substring(0, 3) : null;
                },

                width: "8%",
            },
            {
                data: "am_in",

                width: "10%",
                render: function (data) {
                    if (!data) {
                        return `<style='background-color: green;'>`;
                    }

                    return moment(data, "hh:mm:ss").format("hh:mm A");
                }
            },
            {
                data: "am_out",

                width: "10%",
                render: function (data) {
                    if (!data) {
                        return ``;
                    }

                    return moment(data, "hh:mm:ss").format("hh:mm A");
                }
            },
            {
                data: "pm_in",

                width: "10%",
                render: function (data) {
                    if (!data) {
                        return ``;
                    }

                    return moment(data, "hh:mm:ss").format("hh:mm A");
                }
            },
            {
                data: "pm_out",

                width: "10%",
                render: function (data) {
                    if (!data) {
                        return ``;
                    }

                    return moment(data, "hh:mm:ss").format("hh:mm A");
                }
            },
            {
                data: "total_late",

                width: "10%",
                render: function (data, type, row) {
                    if (parseFloat(row.total_time_rendered) <= 0) {
                        return 0;
                    }

                    if (parseInt(data) > 0) {
                        return `<span class="m--font-danger m--font-boldest">${data}</span>`;
                    }

                    return data;
                }
            },
            {
                data: "total_ut",

                width: "10%",
                render: function (data, type, row) {
                    if (parseFloat(row.total_time_rendered) <= 0) {
                        return 0;
                    }

                    if (parseInt(data) > 0) {
                        return `<span class="m--font-danger m--font-boldest">${data}</span>`;
                    }

                    return data;
                }
            },
            {
                data: "total_time_rendered",

                width: "12%",
                render: function (data, type, row) {
                    if (data && parseFloat(data) > 0) {
                        const hrs = parseFloat(data) / 60;
                        return hrs.toLocaleString("en-US", { maximumFractionDigits: 2 });
                    }

                    return data;
                }
            },
            {
                data: "total_accredited_ot_hrs",

                width: "8%",
                render: function (data, type, row) {
                    if (data && parseFloat(data) > 0) {
                        const total_accredited_ot_hrs = parseFloat(data);
                        return total_accredited_ot_hrs.toFixed(2);
                    }
                    return data;
                }
            },
            {
                data: "total_accredited_ndiff_ot_hrs",

                width: "8%",
                render: function (data, type, row) {
                    if (data && parseFloat(data) > 0) {
                        const total_accredited_ndiff_ot_hrs = parseFloat(data);
                        return total_accredited_ndiff_ot_hrs.toFixed(2);
                    }
                    return data;
                }
            },
        ],
        buttons: [
            {
                extend: 'print',
                text: 'PRINT',
                title: function () {
                    const date_range = ($("input[name='date_range']").val()).split("-");
                    return `<div class="m--regular-font-size-lg4">
                                <span class="m--font-boldest" style='text-transform: uppercase;'>${viewTimesheetModal.attr("data-employee_name")}</span> |
                                DATED: <span class="m--font-boldest">${date_range[0]}</span> - <span class="m--font-boldest">${date_range[1]}</span><br>
                                <p><span class="m--font-bolder">LEGEND</span></p>
                                <p><em class='fa fa-flag' style='color:#ffb822;'></em><span class="m--font-bold"> Holiday</span>&nbsp;&nbsp;&nbsp;
                                <em class='fa fa-times-rectangle' style='color:#5c5d62;'></em><span class="m--font-bold"> Absent</span>&nbsp;&nbsp;&nbsp;
                                <em class='fa fa-clock-o' style='color:#5867dd;'></em><span class="m--font-bold"> Overtime</span></p>
                            </div>`;
                },
                exportOptions: {
                    stripHtml: false,
                },
                // customize: function(win){
                //     var css = `table.dataTable tbody > tr.absent{ color: #ff2312; background-color: #ff2312;}
                //     table.dataTable tbody > tr > td.absent{ background-color: inherit;}`, 
                //         head = win.document.head || win.document.getElementsByTagName('head')[0],
                //         body = win.document.body || win.document.getElementsByTagName('body')[0],
                //         style = win.document.createElement('style'),
                //         tempDiv = win.document.createElement('div'),
                //         tempDiv2 = win.document.createElement('div');

                //     style.type = 'text/css';
                //     style.media = 'print';


                //     if (style.styleSheet) {
                //         style.styleSheet.cssText = css;
                //     } else {
                //         style.appendChild(win.document.createTextNode(css));
                //     }


                //     head.appendChild(style);

                //     const rows = $(win.document.body).find('tbody > tr');
                //     $.each(rows, function(i, v){
                //         var temp = $(v).find("td:first-child").text();
                //         var _dateX = moment(temp).format("YYYY_MM_DD");
                //         if($.inArray(_dateX, global_print_key) !== -1){
                //            $(v).addClass("alert-metal");
                //         }
                //     });

                //     /*** $.each(global_print_key, function(i, v){
                //         $(win.document.body).find('tbody>tr :nth('+v+')').css('color','blue');
                //     }) ***/


                // }

            }
        ],
        lengthMenu: [[15, 30, 50, -1], [15, 30, 50, "All"]]
    });
});

let selectedPayrollSheet = [];

function confirmPosting() {
    var checkedCount = 0;
    selectedPayrollSheet = [];
    dtPayrollSheet.column(0).nodes().to$().each(function (index) {
        const cb = $("input[type='checkbox']", this);
        const checked = cb.prop("checked");
        if (checked == true) { selectedPayrollSheet.push(cb.val()); checkedCount++; }
    });

    if (checkedCount > 0) {
        confirmPayrollPosting.modal("show");
    } else {
        toastr.warning("No confirmed payroll sheet data available!", "Payroll Sheet Posting");
    }
}

function postPayrollSheet() {
    $.ajax({
        url: baseUrl(`payroll/post_payroll_sheet`),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            selected: selectedPayrollSheet
        },
        success: function (response) {
            if (response) {
                toastr[response.toast](response.message, response.title, { timeOut: 10000 });
            }

            dtPayrollSheet.ajax.reload(null, false);
            confirmPayrollPosting.modal("hide");
        }
    });
}

function printTimesheet(el) {
    $("i", el).removeClass();
    $("i", el).addClass("fa fa-spinner fa-spin");
    $("i", el).css({ right: 0, left: 0 });

    setTimeout(() => {
        dtEmployeeTimesheet.button(".buttons-print").trigger();
        $("i", el).removeClass("fa fa-spinner fa-spin").addClass("fa fa-print");
        $("i", el).css({ top: "50%", left: "50%" });
    }, 150);
}

function printPayrollSheet(el) {
    $("i", el).removeClass();
    $("i", el).addClass("fa fa-spinner fa-spin");
    $("i", el).css({ right: 0, left: 0 });

    setTimeout(() => {
        dtPayrollSheet.button(".buttons-print").trigger();
        $("i", el).removeClass("fa fa-spinner fa-spin").addClass("fa fa-print");
        $("i", el).css({ top: "50%", left: "50%" });
    }, 150);
}

function exportExcelPayrollSheet(el) {
    $("i", el).removeClass();
    $("i", el).addClass("fa fa-spinner fa-spin");
    $("i", el).css({ right: 0, left: 0 });

    setTimeout(() => {
        dtPayrollSheet.button(".buttons-excel").trigger();
        $("i", el).removeClass("fa fa-spinner fa-spin").addClass("fa fa-print");
        $("i", el).css({ top: "50%", left: "50%" });
    }, 150);
}

// dri
function tempRegeneratePayroll() {
    $("#frm-filter").submit();
}

_tblPortletPS.on('afterExpand', function (portlet) {
    setTimeout(function () { isCollapsedPortlet = true; }, 500);
});
_tblPortletPS.on('afterCollapse', function (portlet) {
    setTimeout(function () { isCollapsedPortlet = false; }, 500);
});

function toggleParameterModal() {
    setTimeout(function () { $("#payroll-parameters-modal").modal("show"); }, 500);
}

$(document).on("change", "input.show_posted_record", function (e) {
    showPosted = e.target.value;
    dtPayrollSheet.ajax.reload();
    /*** const dtRecord = dtPayrollSheet.rows().data().length;
    if (dtRecord > 0) {} ***/
});

$(document).ready(function (e) {
    select2Employees();
});

var vmPortletSignatories = new Vue({
    el: "#portlet--signatories",
    data: { row: {}, count: 0 },
    methods: {
        openModalSignatory: function () {
            return psSignatoryModal.modal("show");
        },
        resetModalSignatory: function () {
            return psResetSignatoryModal.modal("show");
        }
    }
});

var vmResetSignatories = new Vue({
    el: "#reset-signatory--content",
    data: { row: {}, count: 0 },
    methods: {
        validateFields: function () {
            const _this = this;
            const currentElement = _this.$el;
            const tempForm = $(currentElement).find("form#resetPrintableSignatories");
            if (typeof tempForm !== "undefined") {
                $.validate({
                    form: tempForm,
                    lang: 'en',
                    onSuccess: function (form) {
                        const tempUrl = form[0].action;
                        const tempType = form[0].method;
                        const formData = $(form[0]).serialize();

                        $.ajax({
                            url: tempUrl,
                            type: tempType,
                            dataType: "json",
                            data: formData,
                            beforeSend: function () {
                                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            },
                            success: function (json) {
                                let tempRow = Object.assign({});
                                let ctr = 0;

                                if (json.response) {
                                    tempRow = Object.assign({}, json.data);
                                    ctr = json.count;
                                }
                                vmTempSignatory.row = Object.assign({}, tempRow);
                                vmTempSignatory.count = ctr;
                                vmTempSignatory.$mount();

                                vmPortletSignatories.row = Object.assign({}, tempRow);
                                vmPortletSignatories.count = ctr;

                                _this.row = Object.assign({}, tempRow);
                                _this.count = ctr;
                                const currentModal = $(currentElement).closest(".modal");
                                currentModal.modal("hide");

                                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            }
                        });
                        return false;
                    },

                });
            }
        }
    }, mounted: function () {
        const _this = this;
        _this.validateFields();
    }
});

var vmTempSignatory = new Vue({
    el: "#signatory--content",
    data: { row: {}, count: 0 },
    methods: {
        setGlobalSignatories: function () {
            const _this = this;
            const currentRow = _this.row;
            globalPrintableSignatory = [];
            if (typeof currentRow.meta_field !== "undefined" && typeof currentRow.meta_field == "object") {
                $.each(currentRow.meta_field, function (i, v) {
                    const tempData = { label: v.label, value: v.value, is_active: v.is_active };
                    globalPrintableSignatory.push(tempData);
                });
            }
            return globalPrintableSignatory;
        },
        activeSignatory: function (e) {
            const currentTarget = e.target;
            const formGroup = $(currentTarget).closest(".form-group.m-form__group.row");
            if (typeof formGroup !== "undefined" && formGroup.length == 1) {
                let isChecked = $(currentTarget).is(":checked");
                const select2Container = formGroup.find(".select2--value");
                if (typeof select2Container !== "undefined" && select2Container.length == 1) {
                    if (isChecked) {
                        if (select2Container.is(":disabled") == true) {
                            select2Container.prop("disabled", false);
                        }
                    } else {
                        if (select2Container.is(":disabled") == false) {
                            select2Container.prop("disabled", true);
                        }
                    }
                }
            }
        }, setModalSelect2: function () {
            const _this = this;
            const _currentElement = _this.$el;
            const psModalSignatory = $(_currentElement)
                .closest("#modal-ps--signatory");
            if (typeof psModalSignatory !== "undefined" && psModalSignatory.length == 1) {
                initSelect2Employee(psModalSignatory);
            }
        }, validateFields: function () {
            const _this = this;
            const currentElement = _this.$el;
            const tempForm = $(currentElement).find("form#updatePrintableSignatories");
            if (typeof tempForm !== "undefined") {
                $.validate({
                    form: tempForm,
                    lang: 'en',
                    onSuccess: function (form) {
                        const tempUrl = form[0].action;
                        const tempType = form[0].method;
                        const formData = $(form[0]).serialize();

                        $.ajax({
                            url: tempUrl,
                            type: tempType,
                            dataType: "json",
                            data: formData,
                            beforeSend: function () {
                                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            },
                            success: function (json) {
                                const currentModal = $(currentElement).closest(".modal");
                                if (json.response) {
                                    const currentData = json.data;
                                    if (Object.keys(currentData).length > 0) {
                                        const metaFields = currentData.meta_field;
                                        const ctr = metaFields.length;

                                        _this.row = Object.assign({}, currentData);
                                        _this.count = ctr;
                                        _this.setGlobalSignatories();

                                        vmPortletSignatories.row = Object.assign({}, currentData);
                                        vmPortletSignatories.count = ctr;

                                        vmResetSignatories.row = Object.assign({}, currentData);
                                        vmResetSignatories.count = ctr;

                                        if (typeof currentModal !== "undefined" && currentModal.length == 1) {
                                            currentModal.modal("hide");
                                        }
                                    }
                                } else {
                                    toastr.error("Payroll Signatory", json.toastr_msg);
                                }
                                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            }
                        });
                        return false;
                    },

                });
            }
        }
    }, mounted: function () {
        const _this = this;
        setTimeout(function () {
            _this.setModalSelect2();
            _this.setGlobalSignatories();
            _this.validateFields();
        }, 500);
    }
});

var initSelect2Employee = function (tempModal, portlet) {
    if (typeof tempModal !== "undefined" && tempModal.length == 1) {
        let tempSelector = tempModal.find("select.select2--value");
        if (typeof portlet !== "undefined") { tempSelector = portlet.find("select.select2--value"); }
        if (typeof tempSelector !== "undefined") {
            tempSelector.select2({
                tags: true,
                allowClear: true,
                placeholder: 'Select an option',
                width: '100%',
                dropdownParent: tempModal,
                ajax: {
                    url: baseUrl("payroll/select_employee"),
                    dataType: "json",
                    delay: 250,
                    global: false,
                    processResults: function (data) {
                        let tempData = [];
                        $.each(data.results, function (i, v) {
                            const dd = { id: v.text, text: v.text };
                            tempData.push(dd);
                        });
                        return { results: tempData };
                    }
                }
            });
        }
    }
}

var vmEmpLoans = new Vue({
    el: "#tempCashAdvanceLoans",
    data: { 
        tempLoansCount: 0, 
        tempLoansRows: {}, 
        filter_option: {},
    }, methods: {
        mergeEvent: function(employee_name, rawData, prevData){
            const instance = this;
            const filterOption = instance.filter_option;
            let mergeIds = [], tempAmount = 0, loanId = 0;

            if(typeof rawData !== "undefined" && typeof rawData.loan_id !== "undefined" && (typeof rawData.amount !== "undefined" && rawData.amount)){
                loanId = rawData.loan_id ? rawData.loan_id: loanId;
                tempAmount += parseFloat(rawData.amount);
                mergeIds.push(rawData.loan_id);
            }

            if(typeof prevData !== "undefined" && typeof prevData.loan_id !== "undefined" && (typeof prevData.balance_amt !== "undefined" && prevData.balance_amt)){
                tempAmount += parseFloat(prevData.balance_amt);
                mergeIds.push(prevData.loan_id);
            }

            if(mergeIds.length == 2){
                Swal.fire({
                    title: 'Merge Loan?',
                    text: "Are you sure you want to merge `"+employee_name+"` loans?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Merge it!'
                  }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: siteUrl("payroll/merge_employee_ca_loan"),
                            type: "post",
                            data: {
                                loan_id: mergeIds, 
                                loan_type_id: loanId,
                                amount: tempAmount, 
                                filter_option: filterOption,
                                [_csrf_token]: _csrf_hash
                            },
                            dataType: "json",
                            success: function(json){
                                if(json.response){
                                    const tempData = json.filtered_data;
                                    instance.tempLoansCount = tempData.count;
                                    instance.tempLoansRows = Object.assign({}, tempData.results);
                                    Swal.fire({
                                        title: 'Merged!',
                                        text: "Loan/s has been merged successfully.",
                                        icon: 'success',
                                    });
                                }else{
                                    Swal.fire({
                                        title: 'Failed!',
                                        text: "Loan/s merging failed!",
                                        icon: 'error',
                                    });
                                }
                            }
                        });
                    }
                  });
            }
        }
    }
});

let dtTableExistingPsData = $("#tbl--existing_ps_data").DataTable({
    dom: '<"row"<"col-5 col-md-5 col-lg-5 col-sm-12" l><"col-7 col-md-7 col-lg-7 col-sm-12 text-right" i>>rtp',
    ordering: false,
    searching: false,
    columns: [
        { data: "id", width: "2%", className: "text-center", render: function(data, _type, row){
            if (data) {
                const isPosted = parseInt(row.posted) === 1;
                var _checkButton = isPosted ? "<input type='checkbox' disabled />" : "<input type='checkbox' class='existing_ps' name='existing_id[]'  value=" + data + ">";
                return _checkButton;
            } else { return false; }
        } },
        { data: "employee_name", title: "Employee Name", width: "*"  },
        { data: "pay_date", title: "Pay Date", width: "15%", className: "text-center", render: function(data, _type, row){
            const tempTag = row.tag;
            const nTag = tempTag.length > 0 && tempTag.includes('pay_date') ? `<span data-toggle="m-tooltip" 
                data-placement="top" data-original-title="Conflict" data-skin="dark">
                <i class="fa fa-exclamation-circle m--font-info ml-2"></i>
            </span>`: '';

            const paydate = moment(new Date(data), "YYYY-MM-DD").format("MMM. DD, YYYY");
            return paydate + nTag;
        } },
        { data: "date_start", title: "Coverage Date / Cut-Off",  width: "22%", render: function(data, _type, row){
            const tempTag = row.tag;
            const nTag = tempTag.length > 0 && tempTag.includes('coverage_date') ? `<span data-toggle="m-tooltip" 
                data-placement="top" data-original-title="Conflict" data-skin="dark">
                <i class="fa fa-exclamation-circle m--font-info ml-2"></i>
            </span>`: '';

            const dStart = moment(new Date(data), "YYYY-MM-DD").format("MMM. DD, YYYY");
            const dEnd = moment(new Date(row.date_end), "YYYY-MM-DD").format("MMM. DD, YYYY");
            const coverage = `${dStart} - ${dEnd}`;
            return coverage + nTag;
        } },
        { data: "payroll_schedule", title: "Classification",  className: "text-center", width: "15%", },
        { data: "payroll_seq", title: "Sequence", className: "text-center",  width: "10%", render: function(data, type, row){
            const tempValue = moment.localeData().ordinal(data);
            const tempTag = row.tag;
            const nTag = tempTag.length > 0 && tempTag.includes('payout_sequence') ? `<span data-toggle="m-tooltip" 
                data-placement="top" data-original-title="Conflict" data-skin="dark">
                <i class="fa fa-exclamation-circle m--font-info ml-2"></i>
            </span>`: '';

            return tempValue + nTag;
        } },
        { data: "posted", title: "Posted", className: "text-center",  width: "10%", render: function(data){
            const tempClass = parseInt(data) == 1 ? 'fa fa-check-circle m--font-success':'fa fa-times-circle m--font-danger';
            return `<i class="m--icon-font-size-lg1 ${tempClass}"></i>`;
        } },
    ], 
    columnDefs: [
        { targets: 0, checkboxes: { selectRow: true } }
    ], 
    createdRow: function( row, data ){
        const isPosted = parseInt(data.posted) === 1;
        if(isPosted){ $(row).addClass("m--bg-ps_existing--posted"); }
    },
    select: { style: 'multi' },
});

const checkPayrollSheetData = function(date_range, employees, company, payout_schedule, payout_sequence, pay_date){
    toastr.info("Please Wait, The system is checking for existing payroll sheet data!", "Checking Existing Payroll Sheet Data");
    $.ajax({
        url: baseUrl("payroll/checking_payroll_sheet_data"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            date_range,
            employees,
            company,
            payout_schedule,
            payout_sequence,
            pay_date,
        },
        dataType: "JSON",
        success: function(json){
            if(json.response){
                toastr.warning("A total of ("+json.count+") existing payroll sheet data found!", "Existing Payroll Sheet Data");
                vmExistingPayrollData.rows = json.data;
                vmExistingPayrollData.conflict_payroll_sheet = json.conflict_payroll_sheet;
                vmExistingPayrollData.count = json.count;
                vmExistingPayrollData.filter = Object.assign({}, json.filter);
                globalFilterOptions = Object.assign({}, json.filter);
                
                const globalPsConflict = parseInt(json.conflict_payroll_sheet) == 2;
                vmToUpdateAction.show_action = globalPsConflict;
                
                const toUpdatePS = $("#modal-ps--existing-payroll_sheet #toUpdatePayrollSheet");
                if(globalPsConflict){ 
                    if(toUpdatePS.hasClass("m--hide")){ toUpdatePS.removeClass("m--hide"); }
                }else{
                    if(!toUpdatePS.hasClass("m--hide")){ toUpdatePS.addClass("m--hide"); }
                }

                dtTableExistingPsData.clear();
                dtTableExistingPsData.rows.add(json.data);
                dtTableExistingPsData.draw();

                $("#modal-ps--existing-payroll_sheet").modal();
            }else{
                if(jQuery.inArray("activate_loan", _currentActions) !== -1){
                    /*** for getting generated employees that have available cash advance loans ***/
                    toastr.info("Please wait, The system is fetching available employee loan/s.", "Fetching Employee Loan/s");
                    $.ajax({
                        url : baseUrl('payroll/get_emp_with_loans'),
                        type: "GET",
                        data: {
                            employees,
                            company,
                            date_range,
                            payout_schedule,
                            pay_date
                        },
                        dataType: "json",
                        success: function (json){
                            if(json.state == true){
                                const _md5KeyFilterReset = _md5_key_filter !== json.md5_key_filter;
                                if(_show_modal_loans_once === false && json.count > 0 && json.show_modal === true && _md5KeyFilterReset){
                                    vmEmpLoans.tempLoansCount = json.count;
                                    vmEmpLoans.tempLoansRows = Object.assign({}, json.results);
                                    $("#modal-ps--with-loans").modal();
                                    _md5_key_filter = json.md5_key_filter;
                                }else{ generate_ps(date_range, employees, company, payout_schedule, payout_sequence, pay_date); }
                            }else{ generate_ps(date_range, employees, company, payout_schedule, payout_sequence, pay_date); }
                        }
                    });
                }else{ generate_ps(date_range, employees, company, payout_schedule, payout_sequence, pay_date); }
            }
        }
    });
}

const vmExistingPayrollData = new Vue({
    el: "#tempExistingPayrollSheet",
    data: { rows: [], count: 0, filter: {}, conflict_payroll_sheet: 0 },
    methods: {
        getTaggedFilter: function(tag, type){
            let tempTag = '';
            if(tag.length > 0 && tag.includes(type)){
                tempTag = `<i class="fa fa-exclamation-circle m--font-info ml-2"></i>`;
            }
            return tempTag;
        }, renderNumberOrdinal: function (index){
            return moment.localeData().ordinal(index);
        }, isPostedIcon: function(value){
            const tempClass = parseInt(value) == 1 ? 'fa fa-check-circle m--font-success':'fa fa-times-circle m--font-danger';
            return `<i class="m--icon-font-size-lg1 ${tempClass}"></i>`;
        }, dateFormatted: function (date){
            return moment(new Date(date), "YYYY-MM-DD").format("MMM. DD, YYYY");
        }
    }
});

$.validate({
    form: '#form-existing_ps',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const chkOption = $("input.existing_ps", form);
        let allowUpdate = false;
        if(typeof chkOption !== "undefined" && chkOption.length > 0){
            chkOption.each(function(_kk, vv){
                const isChecked = $(vv).is(":checked");
                if(isChecked){ allowUpdate = true; }
            });
        }

        if(allowUpdate){
            let formData = $("input", form).serialize();
            const stringify = JSON.stringify(globalFilterOptions);
            formData += "&filter_option="+stringify;

            Swal.fire({
                title: 'Update Payroll Data?',
                text: "Are you sure you want to update this payroll sheet data?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Update it!'
              }).then((result) => {
                if (result.isConfirmed) {
                    
                    $.ajax({
                        url: baseUrl("payroll/update_existing_payroll_sheet_data"),
                        type: "POST",
                        data: formData,
                        dataType: "JSON",
                        success: function(json){
                            if(json.response){
                                Swal.fire({
                                    title: 'Updated!',
                                    text: "Payroll sheet data has been updated successfully.",
                                    icon: 'success',
                                });

                                if(typeof json.payroll_sheet_data != "undefined"){
                                    const psData = json.payroll_sheet_data;
                                    if(psData.response){
                                        const globalPsConflict = parseInt(psData.conflict_payroll_sheet) == 2;
                                        if(globalPsConflict && psData.count > 0){
                                            dtTableExistingPsData.clear();
                                            dtTableExistingPsData.rows.add(psData.data);
                                            dtTableExistingPsData.draw();
                                        }else{ $("#modal-ps--existing-payroll_sheet").modal("hide"); }
                                    }else{ $("#modal-ps--existing-payroll_sheet").modal("hide"); }
                                }
                            }else{
                                Swal.fire({
                                    title: 'Update Failed!',
                                    text: "Failed to update payroll sheet data!",
                                    icon: 'error',
                                });
                            }
                        }
                    });
                }
              });

        }else{
            Swal.fire({
                title: 'Update Error!',
                text: "Nothing to update, please tick the checkbox for updates.",
                icon: 'error',
            });
        }
        return false;
    }
});

const vmToUpdateAction = new Vue({
    el: "#toUpdateAction",
    data: { show_action: false }
});


function undoPrinted(ps_id, emp_id){
    Swal.fire({
        title: 'Undo Printed Payroll Sheet?',
        text: "Are you sure you want to undo the printed payroll sheet?",
        icon: 'question',
        input: "textarea",
        inputLabel: "Reason for undoing printed payroll sheet",
        inputValidator: (result) => {
            return !result && "Reason is required!";
        },
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Undo it!'
    }).then((result) => {
        if (result.isConfirmed && typeof result.value !== "undefined" && result.value) {
            $.ajax({
                url: baseUrl("payroll/undo_printed_payroll_sheet"),
                type: "POST",
                data: { 
                    id: ps_id,
                    emp_id: emp_id,
                    csrf_token: _csrf_hash,
                    reason: result.value
                },
                dataType: "JSON",
                success: function(json){
                    if(json.response){
                        Swal.fire({
                            title: 'Undo Printed Payroll sheet',
                            text: "Printed Payroll sheet has been undone successfully.",
                            icon: 'success',
                        }).then(() => {
                            dtPayrollSheet.ajax.reload(null, false);
                        });
                    }else{
                        Swal.fire({
                            title: 'Undo Failed!',
                            text: "Failed to undo printed payroll sheet!",
                            icon: 'error',
                        });
                    }
                }
            });
        }
    });
}

function checkPrintedPayslip(date_range, employees, company, payout_schedule, payout_sequence, pay_date, payroll_group){
    $.ajax({
        url: baseUrl("payroll/check_printed_payslip"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            date_range,
            employees,
            company,
            payout_schedule,
            payout_sequence,
            pay_date,
            payroll_group
        },
        dataType: "JSON",
        success: function(json){
            if(json.response){
                if(parseInt(json.count_printed) > 0){
                    Swal.fire({
                        title: 'Printed Payslip Found',
                        html: "A total of <strong>("+json.count_printed+")</strong> printed payslip found after generating payroll sheet records.<br/>"+
                            "Print reversal is allowed only for the most recent posted payroll.",
                        icon: 'info',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                }
            }
        }
    });
}
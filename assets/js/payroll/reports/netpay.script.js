let globalPrintableSignatory = [];
const psSignatoryModal = $("#modal-ps--signatory");
const psResetSignatoryModal = $("#modal-ps--reset-signatory");
const tableNetpay = $("#tbl-neypay_report");

let _tempIds = [];
let _years = [];
let _companies = [];
let _payoutSchedule = [];
let _globalNetPay = 0;
let filteredCompany = '';
let filteredGroup = '';
let filtered = [];

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
    if(typeof _tempContentData.payout_schedule !== "undefined" && _tempContentData.payout_schedule.length > 0){ 
        _payoutSchedule = _tempContentData.payout_schedule;
    }
}

$(document).ready(function () {
    $("input[name=group]").change(function () {
        var value = $('input[name=group]:checked').val();
        if (value == 1) {
            $("#paydate-filter").removeClass('m--hide');
            $("#filter-by-month-year").addClass('m--hide');
            $("#payout_schedule").val("").trigger("change");
            $("#company").val("").trigger("change");
            $("#payroll_group").empty();
            $("#employees").empty().attr('disabled', false);
        } else {
            $("#filter-by-month-year").removeClass('m--hide');
            $("#paydate-filter").addClass('m--hide');
            $("#payout_schedule").val("").trigger("change");
            $("#company").val("").trigger("change");
            $("#payroll_group").empty();
            $("#employees").empty().attr('disabled', false);
        }
    });
});

let tempRangeDates = {
    min_date: moment().startOf('month').format("MM/DD/YYYY"),
    max_date: moment().endOf('month').format("MM/DD/YYYY"),
};

$("#filter_month")
    .select2({
        width: '100%',
        data: months,
        placeholder: "SELECT MONTH",
        allowClear: true,
    });

$("#filter_year")
    .select2({
        width: '100%',
        data: _years,
        placeholder: "SELECT YEAR",
        allowClear: true,
    });

$("#payout_schedule")
    .select2({
        width: "100%",
        data: _payoutSchedule,
        placeholder: "SELECT AN OPTION",
        allowClear: true,
    });

$("#company").select2({
    width: '100%',
    data: _companies,
    placeholder: 'Select an option',
    allowClear: true,
}).on('select2:select', function(e){
    var self = $(e.target);
    // self.validate();

    $("#payroll_group").empty();
    $("#employees").empty().attr('disabled', false);
}).on('select2:unselect', function(){
    $("#payroll_group").empty();
    $("#employees").empty().attr('disabled', false);
});

$('#pay-date').datepicker({
    todayHighlight: true,
    orientation: "bottom left",
    templates: {
        leftArrow: '<i class="la la-angle-left"></i>',
        rightArrow: '<i class="la la-angle-right"></i>'
    },
    autoclose: true,
    format: 'M.dd, yyyy',
}).on('changeDate', function (e) {
    const currentTarget = $(e.target);
    const currentDate = e.date;
    if (typeof currentDate !== "undefined" && currentDate) {
        const currentDay = moment(currentDate).format("DD");
        const cMomentDate = moment(currentDate, 'YYYY-MM-DD');
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

var generateDateTimePicker = function (min = null, max = null) {
    $("#date-range").val("");
    $("#date-picker")
        .daterangepicker({
            /*** minDate: min,
            maxDate: max, ***/
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            locale: {
                format: 'MM/DD/YYYY'
            }
        }).on('apply.daterangepicker', function (ev, picker) {
            $("#date-range")
                .val(picker.startDate.format('MMM. DD, YYYY') + ' - ' + picker.endDate.format('MMM. DD, YYYY'))
                .validate();
        });
}

generateDateTimePicker(tempRangeDates.min_date, tempRangeDates.max_date);

$.validate({
    form: '#frm-filter-payroll-neypay_report',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        var currentForm = form[0];
        var formMethod = currentForm.method;
        var formUrl = currentForm.action;
        var formData = $(currentForm).serialize();

        /** added for payroll group */
        var emptyEmployeeList = $(currentForm).find("#employees").serialize() ? true : false;
        if (emptyEmployeeList == false && $(currentForm).find("#employees").val().length > 0) {
            formData += '&serialized_employees=' + $(currentForm).find("#employees").val().toString();
        }
        var payrollGroup = $(currentForm).find("#payroll_group").text();
        if(payrollGroup){ formData += '&payroll_group='+payrollGroup; }
        /** added for payroll group */

        $.ajax({
            url: formUrl,
            type: formMethod,
            dataType: "json",
            data: formData,
            beforeSend: function () {
                $(form[0])
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                vmNavigation.set_printable = false;
                vmNavigation.printable_content = null;
            },
            success: function (json) {
                dtNetPayReport.clear();
                dtNetPayReport.rows.add(json.data);
                dtNetPayReport.draw();

                filtered = Object.assign({}, { 'payroll_group' : payrollGroup, 'company': json.filter.company_description ? removeSpecials(json.filter.company_description) : 'All Company', 'total': json.grand_total_decimal });

                if (json.response) {
                    vmNavigation.set_printable = true;
                    vmNavigation.printable_content = json.printable_content;
                }
                $(form[0])
                    .find(".btn-submit")
                    .removeClass("m-btn--custom m-loader m-loader--light m-loader--right")
                    .prop("disabled", false);
            }
        });
        return false;
    }
});

const dtNetPayReport = tableNetpay.DataTable({
    dom: 'rt',
    ordering: false,
    pageLength: -1,
    buttons: [{
        extend: 'print',
        footer: false,
        title: function(){
            return `<div class="text-center m--regular-font-size-lg2">PAYROLL NET PAY SUMMARY REPORT</div>`;
        },
        exportOptions: { stripHtml: false },
        customize: function (win) {
            var css = `@page { size: portrait; margin: 0.5cm; } 
                .print-size-25{ width: 25% }
                .dt-print-view table { font-size: 12px; } 
                .dt-print-view table.dataTable tfoot tr:first-child th{ border-top: 1px solid #000000; }
                .dt-print-view table.dataTable tfoot tr:first-child th{ border-bottom: 4px double #000000; }`,
                head = win.document.head || win.document.getElementsByTagName('head')[0],
                body = win.document.body || win.document.getElementsByTagName('body')[0]
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
            win.document.title = "Netpay Report Printable Page";

            var tempTable = win.document.getElementsByClassName('dataTable')[0];
            $(tempTable).removeClass("table-bordered");

            tempDiv2.innerHTML = `<div class="row mt-5 printable-row_content">
                <div class="col-md-9 col-lg-9 col-sm-12">&nbsp;</div>
                <div class="col-md-3 col-lg-3 col-sm-12 text-right">
                    <h3 style="font-family: 'Lucida Console';">${_globalNetPay}</h3>
                    <h5 class="print-size-25" style="border-top: 5px double; font-weight: bold; padding-top: 10px; float: right;">GRAND TOTAL</h5>
                </div>
            </div>`;
            body.appendChild(tempDiv2);
        }
    },{
        extend: 'excelHtml5',
        footer: false,
        filename: function(){
            return 'PAYROLL NET PAY SUMMARY REPORT';
        },
        title: function(){
            return ``;
        },
        footer: false,
        exportOptions: { 
            stripHtml: false,
            columns: [9, 7, 8, 1]
        },
        customize: function (xlsx) {
            var sheet = xlsx.xl.worksheets['sheet1.xml'];
            var sheetData = sheet.getElementsByTagName('sheetData')[0];
            var downrows = filtered.payroll_group != '' ? 3 : 2;
            let mergeCells = $('mergeCells', sheet);

            // footer
            let numrows = $('row', sheet).length;
            var tempRowIndex = numrows > 0 ? numrows + 1 : numrows;

            let tempRowx = addRowFooter(tempRowIndex, [{ key: 'A', value: 'GRAND TOTAL' }, { key: 'B', value: filtered.total }, { key: 'C', value: '' }, { key: 'D', value: '' }]);
            sheetData.appendChild(tempRowx);
            // footer

            $('row', sheet).each(function () {
                var attr = $(this).attr('r');
                var ind = parseInt(attr);
                ind = ind + downrows;
                $(this).attr("r",ind);
            });

            $('row c ', sheet).each(function () {
                var attr = $(this).attr('r');
                var pre = attr.substring(0, 1);
                var ind = parseInt(attr.substring(1, attr.length));
                ind = ind + downrows;

                if(pre == 'B' || pre == 'C'){
                    $(this).attr("r", pre + ind).attr('s', '51');
                }else{
                    $(this).attr("r", pre + ind).attr('s', '50');
                }
            });

            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                attr: {
                    ref: 'A1' + ':' + 'D1',
                },
            }));

            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                attr: {
                    ref: 'B2' + ':' + 'C2',
                },
            }));
            
            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                attr: {
                    ref: 'B3' + ':' + 'D3',
                },
            }));

            var r1 = addRowTitle(1, [{ k: 'A', v: 'PAYROLL NET PAY SUMMARY REPORT' }, { k: 'B', v: '' }, { k: 'C', v: '' }, { k: 'D', v: '' }]);
            var r2 = addRowMessage(2, [{ k: 'A', v: 'COMPANY: ' }, { k: 'B', v: filtered.company }, { k: 'C', v: '' }, { k: 'D', v: '' }]);
            var r3 = addRowMessage(3, [{ k: 'A', v: 'PAYROLL GROUP: ' }, { k: 'B', v: filtered.payroll_group }, { k: 'C', v: '' }, { k: 'D', v: '' }]);

            if(filtered.payroll_group){
                sheetData.insertBefore(r3, sheetData.childNodes[0]);
            }

            sheetData.insertBefore(r2, sheetData.childNodes[0]);
            sheetData.insertBefore(r1, sheetData.childNodes[0]);

            function _createNode(doc, nodeName, opts) {
                var tempNode = doc.createElement(nodeName);
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

            function addRowTitle(index, data){
                var row = sheet.createElement('row');

                row.setAttribute("r", index);              
                for (i = 0; i < data.length; i++) {
                    var key = data[i].k;
                    var value = data[i].v;

                    var c  = sheet.createElement('c');
                    c.setAttribute("t", "inlineStr");
                    c.setAttribute("s", '51');
                    c.setAttribute("r", key + index);

                    var is = sheet.createElement('is');
                    var t = sheet.createElement('t');
                    var text = sheet.createTextNode(value)

                    t.appendChild(text);                                      
                    is.appendChild(t);
                    c.appendChild(is);

                    row.appendChild(c);                                                                                                                         
                }

                return row;
            }
            
            function addRowMessage(index, data){
                var row = sheet.createElement('row');

                row.setAttribute("r", index);              
                for (i = 0; i < data.length; i++) {
                    var key = data[i].k;
                    var value = data[i].v;

                    var c  = sheet.createElement('c');
                    c.setAttribute("t", "inlineStr");
                    c.setAttribute("s", '50');
                    c.setAttribute("r", key + index);

                    var is = sheet.createElement('is');
                    var t = sheet.createElement('t');
                    var text = sheet.createTextNode(value)

                    t.appendChild(text);                                      
                    is.appendChild(t);
                    c.appendChild(is);

                    row.appendChild(c);                                                                                                                         
                }

                return row;
            }

            function addRowFooter(index, data) {
                var row = sheet.createElement('row');
                row.setAttribute("r", index);
                for (i = 0; i < data.length; i++) {
                    var key = data[i].key;
                    var value = data[i].value;

                    var c = sheet.createElement('c');
                    c.setAttribute("t", "inlineStr");
                    c.setAttribute("s", "2");
                    c.setAttribute("r", key + index);

                    var is = sheet.createElement('is');
                    var t = sheet.createElement('t');
                    var text = sheet.createTextNode(value)

                    t.appendChild(text);
                    is.appendChild(t);
                    c.appendChild(is);
                    row.appendChild(c);
                }
                return row;
            }
        }
    }],
    columns: [
        {
            width: "7%",
            render: function (data, type, row, meta) {
                return meta.row + 1;
            }
        },
        { data: "employee_name", width: "*" },
        { data: "company_description", width: "15%" },
        { data: "position", width: "16%" },
        { data: "work_status", width: "14%" },
        { data: "date_start", width: "12%" },
        { data: "atm_info", width: "10%",
            render: function(data){
                return data ? data : ' --- ';
            }
        },
        { orderable: false, data: "net_pay_decimal", className: "text-right m--font-bolder", width: "14%" },
        { data: null, visible: false,
            render: function(data, type, row, meta){
                return 'PAYROLL';
            }
        },
        { data: 'atm_info', visible: false }
        
    ],
    footerCallback: function (row, data, start, end, display) {
        _globalNetPay = 0;
        var api = this.api();

        var intVal = function (i) {
            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
        };

        totalNetPay = api
            .column(7)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        _globalNetPay = numberFormat(totalNetPay);
        $(api.column(7).footer()).html("<span class='m--font-boldest'>" + _globalNetPay + "</span>");
    }
});

var vmNavigation = new Vue({
    el: "#tempActions",
    data: { set_printable: false, printable_content: null },
    methods: {
        printReport: function () {
            const _this = this;
            const tempHtml = _this.printable_content;
            setPrintableWindow(tempHtml);
        }
    }
});

var setPrintableWindow = function (html) {
    if (typeof html !== "undefined" && html) {
        var printWindow = window.open(siteUrl('payroll/reports/printable_form'), '_blank');
        printWindow.focus();
        printWindow.onload = function(){
            setTimeout(function () {
                const appendContainer = printWindow.document.getElementById('append_printable-container');
                if (typeof appendContainer !== "undefined" && appendContainer !== null) {
                    appendContainer.innerHTML = html;
                    setTimeout(function () {
                        printWindow.print();
                        printWindow.close();
                    }, 500);
                } else {
                    toastr.info("Print detail(s) is still in progress!", "Contribution / Deduction");
                    printWindow.close();
                }
            }, 500);
        }
    }
    return false;
}

function printNetpayReport(el) {
    $("i", el).removeClass();
    $("i", el).addClass("fa fa-spinner fa-spin");
    $("i", el).css({ right: 0, left: 0 });

    setTimeout(() => {
        dtNetPayReport.button(".buttons-print").trigger();
        $("i", el).removeClass("fa fa-spinner fa-spin").addClass("fa fa-print");
        $("i", el).css({ top: "50%", left: "50%" });
    }, 150);
}

/** added for payroll group */
$("#employees").select2({
    placeholder: 'Select',
    width: '100%',
    ajax: {
        url: baseUrl("payroll/reports/select_employee"),
        dataType: "json",
        delay: 250,
        global: false,
        processResults: function (data) {
            return data;
        }
    }
});

$("#payroll_group").select2({
    placeholder: 'Select',
    width: '100%',
    ajax: {
        url: baseUrl("payroll/select_payroll_group"),
        dataType: "json",
        type: 'get',
        delay: 250,
        global: false,
        data: function (params) {
            params.company_id = $("form#frm-filter-payroll-neypay_report select#company").val();
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
                        const tempEmployeeSelector = $("form#frm-filter-payroll-neypay_report select#employees");
                        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                            tempEmployeeSelector.empty();
                            $.each(tempData, function (ii, vv) {
                                var tempOption = new Option(vv.text, vv.id, true, true);
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
            const tempEmployeeSelector = $("form#frm-filter-payroll-neypay_report select#employees");
            if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                tempEmployeeSelector.empty();
                $.each(employees, function (ii, vv) {
                    var tempOption = new Option(vv.text, vv.id, true, true);
                    tempEmployeeSelector.append(tempOption);
                });
                tempEmployeeSelector.prop("disabled", true);
            }
        }
    }
}).on("select2:unselect", function (e) {
    const _this = this;
    const tempValUnselected = $(_this).val();
    if (tempValUnselected.length == 0) {
        const tempEmployeeSelector = $("form#frm-filter-payroll-neypay_report select#employees");
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
                        const tempEmployeeSelector = $("form#frm-filter-payroll-neypay_report select#employees");
                        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                            tempEmployeeSelector.empty();
                            $.each(tempData, function (ii, vv) {
                                var tempOption = new Option(vv.text, vv.id, true, true);
                                tempEmployeeSelector.append(tempOption);
                            });
                            tempEmployeeSelector.prop("disabled", true);
                        }
                    }
                }
            }
        });

    }

});
/** added for payroll group */

function exportNetpayReport(el){
    $("i", el).removeClass();
    $("i", el).addClass("fa fa-spinner fa-spin");
    $("i", el).css({ right: 0, left: 0 });

    setTimeout(() => {
        dtNetPayReport.button(".buttons-excel").trigger();
        $("i", el).removeClass("fa fa-spinner fa-spin").addClass("fa fa-print");
        $("i", el).css({ top: "50%", left: "50%" });
    }, 150);
}

function removeSpecials(str){
    return str.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
}
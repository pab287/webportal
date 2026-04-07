let globalPrintableSignatory = [];
const psSignatoryModal = $("#modal-ps--signatory");
const psResetSignatoryModal = $("#modal-ps--reset-signatory");
const tableNetpay = $("#tbl-neypay_report");

let _tempIds = [];
let _years = [];
let _companies = [];
let _payoutSchedule = [];
let _globalNetPay = 0;
let _globalGrossPay = 0;
let filteredCompany = '';
let filteredGroup = '';
let payroll_option = "";
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
        const value = $('input[name=group]:checked').val();
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

const generateDateTimePicker = function (min = null, max = null) {
    $("#date-range").val("");
    $("#date-picker")
        .daterangepicker({
            minDate: min,
            maxDate: max,
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
        const currentForm = form[0];
        const formMethod = currentForm.method;
        const formUrl = currentForm.action;
        let formData = $(currentForm).serialize();

        /** added for payroll group */
        const emptyEmployeeList = $(currentForm).find("#employees").serialize() !== "";
        if (emptyEmployeeList === false && $(currentForm).find("#employees").val().length > 0) {
            formData += '&serialized_employees=' + $(currentForm).find("#employees").val().toString();
        }
        const payrollGroup = $(currentForm).find("#payroll_group").text();
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

                const filteredBy = $("input[name=group]:checked").val();
                const filteredDate = filteredBy == 1 ? 'DATE RANGE ' + $("#date-range").val() : getMonthTextById($("#filter_month").val()).toUpperCase() + ' - ' + $("#filter_year").val();

                filtered = Object.assign({}, { 
                    'payroll_group' : payrollGroup, 
                    'company': json.filter.company_description ? removeSpecials(json.filter.company_description) : 'All Company', 
                    'gross': json.gross_total_decimal,
                    'total': json.grand_total_decimal,
                    'option': json.payroll_option,
                    'generated': filteredDate
                });

                if (json.response) {
                    vmNavigation.set_printable = true;
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
    scrollX: true,
    buttons: [{
        extend: 'print',
        footer: false,
        title: function(){
            const option = filtered.option != 'all' ? filtered.option.toUpperCase() : '';

            return `<div class="text-center m--regular-font-size-lg2">${option} PAYROLL NET PAY SUMMARY REPORT</div>`;
        },
        exportOptions: { stripHtml: false, columns: ':visible:not(:eq(0)):not(.actions)' },
        customize: function (win) {
            const css = `@page { size: portrait; margin: 0.5cm; } 
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
            const option = filtered.option != 'all' ? filtered.option.toUpperCase() : '';
            win.document.title = option + " Netpay Report Printable Page";

            const tempTable = win.document.getElementsByClassName('dataTable')[0];
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
            const option = filtered.option != 'all' ? filtered.option.toUpperCase()+' ' : '';
            return `${option}CUSTOM PAYROLL SHEET REPORT ${filtered.generated}`;
        },
        title: function(){
            return ``;
        },
        exportOptions: { 
            stripHtml: false,
            columns: ':visible:not(:eq(0)):not(.actions)'
        },
        customize: function (xlsx) {
            const option = filtered.option != 'all' ? filtered.option.toUpperCase()+' ' : '';
            const sheet = xlsx.xl.worksheets['sheet1.xml'];
            const sheetData = sheet.getElementsByTagName('sheetData')[0];
            const downrows = filtered.payroll_group != '' ? 3 : 2;
            let mergeCells = $('mergeCells', sheet);
            const columnCount = tableNetpay.DataTable().columns(':visible').count();

            // footer
            let numrows = $('row', sheet).length;
            const tempRowIndex = numrows > 0 ? numrows + 1 : numrows;

            const lastColIndex = columnCount; // already 1-based
            const lastColLetter = getExcelColumnLetter(lastColIndex - 1);
            const nextColLetter = getExcelColumnLetter(lastColIndex - 2);
            const secondLastColLetter = getExcelColumnLetter(lastColIndex - 3); //for grand total
            let tempRowx = addRowFooter(tempRowIndex, [
                { key: secondLastColLetter, value: 'GRAND TOTAL' },
                { key: nextColLetter, value: filtered.gross },
                { key: lastColLetter, value: filtered.total }
            ]);
            sheetData.appendChild(tempRowx);
            // footer

            $('row', sheet).each(function () {
                const attr = $(this).attr('r');
                let ind = parseInt(attr);
                ind = ind + downrows;
                $(this).attr("r", ind);
            });

            $('row c ', sheet).each(function () {
                const attr = $(this).attr('r');
                const pre = attr.substring(0, 1);
                let ind = parseInt(attr.substring(1, attr.length));
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
            
            if (filtered.payroll_group != '') {
                mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                    attr: {
                        ref: 'B3' + ':' + 'D3',
                    },
                }));
            }

            const r1 = addRowTitle(1, [{ k: 'A', v: `${option}CUSTOM PAYROLL SHEET REPORT ${filtered.generated}` }, { k: 'B', v: '' }, { k: 'C', v: '' }, { k: 'D', v: '' }]);
            const r2 = addRowMessage(2, [{ k: 'A', v: 'COMPANY: ' }, { k: 'B', v: filtered.company }, { k: 'C', v: '' }, { k: 'D', v: '' }]);
            const r3 = addRowMessage(3, [{ k: 'A', v: 'PAYROLL GROUP: ' }, { k: 'B', v: filtered.payroll_group }, { k: 'C', v: '' }, { k: 'D', v: '' }]);

            if(filtered.payroll_group){
                sheetData.insertBefore(r3, sheetData.childNodes[0]);
            }

            sheetData.insertBefore(r2, sheetData.childNodes[0]);
            sheetData.insertBefore(r1, sheetData.childNodes[0]);

            function _createNode(doc, nodeName, opts) {
                const tempNode = doc.createElement(nodeName);
                if (opts) {
                    if (opts.attr) { $(tempNode).attr(opts.attr); }
                    if (opts.children) {
                        $.each(opts.children, function (_key, value) {
                            tempNode.appendChild(value);
                        });
                    }
                    if (opts.text !== null && opts.text !== undefined) { tempNode.appendChild(doc.createTextNode(opts.text)); }
                }
                return tempNode;
            }

            function addRowTitle(index, data){
                const row = sheet.createElement('row');
                row.setAttribute("r", index);
                let i;             
                for (i = 0; i < data.length; i++) {
                    const key = data[i].k;
                    const value = data[i].v;

                    const c  = sheet.createElement('c');
                    c.setAttribute("t", "inlineStr");
                    c.setAttribute("s", '51');
                    c.setAttribute("r", key + index);

                    const is = sheet.createElement('is');
                    const t = sheet.createElement('t');
                    const text = sheet.createTextNode(value)
                    t.appendChild(text);                                      
                    is.appendChild(t);
                    c.appendChild(is);
                    row.appendChild(c);                                                                                                                         
                }

                return row;
            }
            
            function addRowMessage(index, data){
                const row = sheet.createElement('row');
                row.setAttribute("r", index);
                let i;              
                for (i = 0; i < data.length; i++) {
                    const key = data[i].k;
                    const value = data[i].v;

                    const c  = sheet.createElement('c');
                    c.setAttribute("t", "inlineStr");
                    c.setAttribute("s", '50');
                    c.setAttribute("r", key + index);

                    const is = sheet.createElement('is');
                    const t = sheet.createElement('t');
                    const text = sheet.createTextNode(value)
                    t.appendChild(text);                                      
                    is.appendChild(t);
                    c.appendChild(is);
                    row.appendChild(c);                                                                                                                         
                }

                return row;
            }

            function addRowFooter(index, data) {
                const row = sheet.createElement('row');
                row.setAttribute("r", index);
                let i;
                for (i = 0; i < data.length; i++) {
                    const key = data[i].key;
                    const value = data[i].value;

                    const c = sheet.createElement('c');
                    c.setAttribute("t", "inlineStr");
                    c.setAttribute("s", "2");
                    c.setAttribute("r", key + index);

                    const is = sheet.createElement('is');
                    const t = sheet.createElement('t');
                    const text = sheet.createTextNode(value)
                    t.appendChild(text);
                    is.appendChild(t);
                    c.appendChild(is);
                    row.appendChild(c);
                }
                return row;
            }

            function getExcelColumnLetter(colIndex) {
                let letter = '';
                while (colIndex > 0) {
                    let temp = (colIndex - 1) % 26;
                    letter = String.fromCharCode(temp + 65) + letter;
                    colIndex = Math.floor((colIndex - 1) / 26);
                }
                return letter;
            }
        }
    }],
    columns: [
        { width: "7%",
            render: function (data, type, row, meta) {
                return meta.row + 1;
            }
        },
        { data: "employee_name", width: "*" },
        { data: "company_description", width: "10%" },
        { data: "department_description", width: "10%" },
        { data: "position", width: "10%" },
        { data: "work_status", visible: false },
        { data: "payroll_group", width: '14%',
            render: function (data) {
                return data != null ? data : ' No group assigned ';
            }
        },
        {
            data: "station", width: "10%",
            render: function (data) {
                return data ? data : 'No assigned Station';
            }
        },
        { data: "rate", width: "*", visible: false,
            render: function(data) {
                return numberFormat(data);
            }
        },
        { data: "no_of_days", width: "*", visible: false,
            render: function (data) {
                return numberFormat(data);
            }
        },
        { data: "basic_rate", width: "*", visible: false,
            render: function(data) {
                return numberFormat(data);
            }
        },
        { data: "total_undertime_amount", width: "*", visible: false,
            render: function(data) {
                return numberFormat(data);
            }
        },
        { data: "ot_amount", width: "5%",
            render: function (data) {
                return numberFormat(data);
            }
        },
        { data: "total_ndiff_amount", width: "5%",
            render: function (data, type, row, meta) {
                const total_ndiff = parseFloat(row.total_ndiff_amount) + parseFloat(row.ot_ndiff_amount);
                return numberFormat(total_ndiff);
            }
        },
        { data: "total_holiday_amount", width: "*", visible: false,
            render: function (data) {
                return numberFormat(data);
            }
        },
        { data: "allowance_rate", className:'text-right', width: "10%", visible: false,
            render: function (data) {
                return numberFormat(data);
            }
        },
        { data: "total_allowances", className:'text-right', width: "10%",
            render: function (data) {
                return numberFormat(data);
            }
        },
        { data: "gross_pay", className:'text-right', width: "10%",
            render: function (data) {
                return numberFormat(data);
            }
        },
        { data: "net_pay", className:'text-right', width: "10%",
            render: function (data) {
                return numberFormat(data);
            }
        },
        
    ],
    footerCallback: function (row, data, start, end, display) {
        _globalNetPay = 0;
        _globalGrossPay = 0;
        const api = this.api();
        const intVal = function (i) { return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0; };

        const totalGrossPay = api
            .column(16)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        const totalNetPay = api
            .column(17)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);
        

        _globalNetPay = numberFormat(totalNetPay);
        _globalGrossPay = numberFormat(totalGrossPay);
        $(api.column(16).footer()).html("<span class='m--font-boldest'>" + _globalGrossPay + "</span>");
        $(api.column(17).footer()).html("<span class='m--font-boldest'>" + _globalNetPay + "</span>");
    }
});

function showOrHideColumn(index, el) {
    const column = dtNetPayReport.column(index);
    column.visible($(el)[0].checked);
}

const vmNavigation = new Vue({
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

function getMonthTextById(id) {
    const month = months.find(m => m.id === parseInt(id));
    return month ? month.text : "Invalid month";
}
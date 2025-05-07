let search_val = "", date_from = "", date_to = "", selectedCompany = null, _globalFooterHtml = null;
let psEmployeeGroup = [], globalPrintableSignatory = [], company = [], _companies = [], _payoutSchedule = [], employee = [];
let _globalFooterAdjustments = { sss: 0, sss_prov: 0, phic: 0, hdmf: 0, tax: 0, total_loans: 0 };
let _dtRowSSS = [], _dtRowSSS_PROV = [], _dtRowPHIC = [], _dtRowHDMF = [], _dtRowTAX = [], _dtRowLOAN = [], _tempLastRow = [];
let globalGrandTotal = {};
const exportOptions = {
    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21],
};

let globalFilterOptions = {};
let isCollapsedPortlet = true;
let tempRangeDates = {
    min_date: moment().startOf('month').format("MM/DD/YYYY"),
    max_date: moment().endOf('month').format("MM/DD/YYYY"),
};

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
        psOccurrence[tempKey] = nOccurrance;
    });
}

toastr.options = { newestOnTop: true, positionClass: "toast-bottom-right" };
$('body').tooltip({ selector: '[data-toggle="m-tooltip"]' });

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
    if (start && end) {
        options = { ...options, startDate: start, endDate: end };
    }
    $("#date-range").val("");
    $("#date-picker")
        .daterangepicker(options).on('apply.daterangepicker', function (ev, picker) {
            $("#date-range")
                .val(picker.startDate.format('MMM. DD, YYYY') + ' - ' + picker.endDate.format('MMM. DD, YYYY'))
                .validate();
        });
}

generateDateTimePicker(tempRangeDates.min_date, tempRangeDates.max_date);
const payroll_sequence_select2 = function () {
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
    $(option.target).validate();
});

$("#payroll_group").select2({
    placeholder: 'Select an option',
    width: '100%',
    ajax: {
        url: baseUrl("hris/reports/select_ps_payroll_group"),
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
            url: baseUrl("hris/reports/get_payroll_group_multiple"),
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
            url: baseUrl("hris/reports/get_payroll_group_multiple"),
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

const drawCallbackRequestAction = function (btnAction, dtActions, tempData) {
    if (!btnAction || !dtActions) return;
    btnAction.addClass("btn m-btn btn-brand m-btn--icon m--hide animated fadeIn mr-1");

    const showElements = () => {
        btnAction.removeClass('m--hide');
        dtActions.removeClass('m--hide');
    };

    const hideElements = () => {
        btnAction.addClass('m--hide');
        dtActions.addClass('m--hide');
    };

    if (tempData.length > 0) { showElements(); } 
    else { hideElements(); }       
}

const dtTable = $("#table-payroll-sheet").DataTable({
    dom: "<'row'<'col-md-9 dtDetails'><'col-md-3 dtActions m--hide'B>>rtlp",
    serverSide: false,
    destroy: true,
    autoWidth: false,
    ordering: false,
    pageLength: 10,
    buttons: [{
        extend: 'excel',
        text: '<i class="fa fa-download"></i><span class="m--font-boldest">EXPORT EXCEL</span>',
        className: "pull-right exportTempReportAction btnExport",
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

            lastRow.each(function () {
                const attr = $(this).attr('r');
                const pre = attr.substring(0, 1);
                tempPre.push(pre);
                let ind = parseInt(attr.substring(1, attr.length));
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
            $.each(tempPre, function (i, v) {
                let _value = "";
                if (i > 1 && i !== 9) {
                    _value = tempRowCols[tempx];
                    _value = $.trim(_value);
                    tempx++;
                } else if (i == 0) {
                    _value = "GRAND TOTAL";
                }

                let tempCell = { key: v, value: _value };
                tempData.push(tempCell);
            });

            numrows = $('row', sheet).length;
            tempRowIndex = numrows > 0 ? numrows + 1 : numrows;

            let mergeCells = $('mergeCells', sheet);
            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                attr: { ref: 'A' + tempRowIndex + ':' + 'D' + tempRowIndex },
            }));

            const newRowData = Addrow(tempRowIndex, tempData);
            sheetData.appendChild(newRowData);
            $('row:last c', sheet).attr("s", "2");

            function Addrow(index, data) {
                const row = sheet.createElement('row');
                row.setAttribute("r", index);

                for (let i = 0; i < data.length; i++) {
                    const key = data[i].key;
                    const value = data[i].value;

                    const c = sheet.createElement('c');
                    c.setAttribute("t", "inlineStr");
                    c.setAttribute("s", "2");
                    c.setAttribute("r", key + index);

                    let is = sheet.createElement('is');
                    let t = sheet.createElement('t');
                    let text = sheet.createTextNode(value)

                    t.appendChild(text);
                    is.appendChild(t);
                    c.appendChild(is);

                    row.appendChild(c);
                }

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
    }, {
        extend: 'print',
        text: '<i class="fa fa-print"></i><span class="m--font-boldest">PRINT</span>',
        className: "pull-right printTempReportAction btnPrint",
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
                    <div class="m--regular-font-size-lg1">PAYROLL SHEET NO EARNERS DATA</div>
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
                style = win.document.createElement('style');

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
            const tempHeader = dtTable.table().header();
            $(tempTable).find("thead").empty().append(tempHeader.innerHTML);
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

            //# change name of column headers when printing
            $(win.document.body).find("th span#allow").text('ALLOW');
            $(win.document.body).find("th span#allowance").text('ALLOW');
            $(win.document.body).find("th span#adjustment").text('ADJ');
            // end of function
        }
    }], 
    columns: [
        {
            data: 'id',
            width: "3%",
            orderable: false,
            visible: false,
            className: "text-center",
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        {
            data: "lastname",
            render: function (data, type, row) {
                const mi = row.middlename.toLowerCase() !== "n/a" && row.middlename !== "" && row.middlename.toLowerCase() !== "none" ? row.middlename.substring(0, 1) + ". " : "";
                const suffix = row.suffix.toLowerCase() !== "n/a" && row.suffix !== "" && row.suffix.toLowerCase() !== "none" ? row.suffix : "";
                let complete_name = data + ", " + row.firstname + " " + suffix + " " + mi;
                let position = row.position.toUpperCase();
                complete_name = complete_name.toUpperCase();
                return `<span class="m--font-bolder">${complete_name}</span><br><small>` + position + `</small>`;
            }
        },
        {
            data: "rate",
            width: "5%",
            className: "text-right",
            render: function (data) {
                return numberFormat(data);
            }
        },
        {
            data: "allowance_rate",
            width: "5%",
            className: "text-right",
            render: function (data) {
                return numberFormat(data);
            }
        },
        {
            data: "no_of_days",
            className: "text-center",
            render: function (data) {
                return numberFormat(data);
            }
        },
        {
            data: "ot_amount",
            className: "text-right",
            render: function (data, type, row) {
                return numberFormat(data);
            }
        },
        {
            data: "ot_ndiff_amount",
            className: "text-right",
            render: function (data, type, row) {
                return numberFormat(data);
            }
        },
        {
            data: "total_holiday_amount",
            className: "text-right",
            render: function (data) {
                return numberFormat(data);
            }
        },
        {
            data: "basic_rate",
            width: "5%",
            className: "text-right",
            render: function (data) {
                return numberFormat(data);
            }
        },
        {
            data: "total_allowances",
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
                        let temp_amount = parseFloat(data);
                        if (adj_type == 1) {
                            temp_amount = parseFloat(data) + parseFloat(temp_adjustment[1]);
                        } else {
                            temp_amount = parseFloat(data) - parseFloat(temp_adjustment[1]);
                        }
                        temp_amount = numberFormat(temp_amount);

                        if (temp_adjustment[0] == "ALLOWANCE" && temp_status === 0) {
                            template = `<div class="mb-0 m--font-bolder m--font-accent">
                                <span class='fa fa-exclamation-circle'></span>
                                <span class="m--font-boldest">${tempData}</span>
                            </div>`;
                        }
                        if (temp_adjustment[0] == "ALLOWANCE" && temp_status === 1) {
                            template = `<div class="mb-0 m--font-bolder m--font-primary">
                                <span class="m--font-boldest">${temp_amount}</span>
                            </div>`;
                        }
                    });
                }

                return template;
            }
        },
        {
            data: "custom_adjustments",
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
        },
        {
            data: "gross_pay",
            width: "5%",
            className: "text-right",
            render: function (data) {
                return numberFormat(data);
            }
        },
        {
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
                        let temp_amount = parseFloat(data);
                        if (adj_type == 1) {
                            temp_amount = parseFloat(data) + parseFloat(temp_adjustment[1]);
                        } else {
                            temp_amount = parseFloat(data) - parseFloat(temp_adjustment[1]);
                        }
                        tempAdj = temp_amount;
                        temp_amount = numberFormat(temp_amount);
                        if (temp_adjustment[0] == "SSS" && temp_status === 0) {
                            template = `<div class="mb-0 m--font-bolder m--font-accent">
                                <span class='fa fa-exclamation-circle'></span>
                                <span class="m--font-boldest">${tempData}</span>
                            </div>`;
                        }
                        if (temp_adjustment[0] == "SSS" && temp_status === 1) {
                            approvedAmount = tempAdj;
                            template = `<div class="mb-0 m--font-bolder m--font-primary">
                                <span class="m--font-boldest">${temp_amount}</span>
                            </div>`;

                        }
                    });
                }
                if ($.inArray(parseInt(row.id), _dtRowSSS) == -1) {
                    if (typeof _globalFooterAdjustments.sss !== "undefined") {
                        approvedAmount = parseFloat(_globalFooterAdjustments.sss) + approvedAmount;
                    }
                    _globalFooterAdjustments = Object.assign({}, _globalFooterAdjustments, { sss: approvedAmount });
                    _dtRowSSS.push(parseInt(row.id));
                }
                return template;
            }
        },
        {
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
                        let temp_amount = parseFloat(data);
                        if (adj_type == 1) {
                            temp_amount = parseFloat(data) + parseFloat(temp_adjustment[1]);
                        } else {
                            temp_amount = parseFloat(data) - parseFloat(temp_adjustment[1]);
                        }
                        tempAdj = temp_amount;
                        temp_amount = numberFormat(temp_amount);
                        if (temp_adjustment[0] == "SSS_PROV" && temp_status === 0) {
                            template = `<div class="mb-0 m--font-bolder m--font-accent">
                                <span class='fa fa-exclamation-circle'></span>
                                <span class="m--font-boldest">${tempData}</span>
                            </div>`;
                        }
                        if (temp_adjustment[0] == "SSS_PROV" && temp_status === 1) {
                            approvedAmount = tempAdj;
                            template = `<div class="mb-0 m--font-bolder m--font-primary">
                                <span class="m--font-boldest">${temp_amount}</span>
                            </div>`;

                        }
                    });
                }
                if ($.inArray(parseInt(row.id), _dtRowSSS_PROV) == -1) {
                    if (typeof _globalFooterAdjustments.sss_prov !== "undefined") {
                        approvedAmount = parseFloat(_globalFooterAdjustments.sss_prov) + approvedAmount;
                    }
                    _globalFooterAdjustments = Object.assign({}, _globalFooterAdjustments, { sss_prov: approvedAmount });
                    _dtRowSSS_PROV.push(parseInt(row.id));
                }
                return template;
            }
        },
        {
            data: "ph", // phic
            className: "text-right",
            render: function (data, type, row) {
                let approvedAmount = parseFloat(data);
                const tempData = numberFormat(data);
                let template = ``;
                template = tempData;
                const tempCreatedAdjustments = row.created_adjustments;
                if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                    var tempAdj = 0;
                    const created_adjustments = tempCreatedAdjustments.split(",");
                    created_adjustments.forEach((row, i) => {
                        const temp_adjustment = row.split("||");
                        const adj_type = parseInt(temp_adjustment[2]);
                        const temp_status = parseInt(temp_adjustment[3]);
                        let temp_amount = parseFloat(data);
                        if (adj_type == 1) {
                            temp_amount = parseFloat(data) + parseFloat(temp_adjustment[1]);
                        } else {
                            temp_amount = parseFloat(data) - parseFloat(temp_adjustment[1]);
                        }
                        tempAdj = temp_amount;
                        temp_amount = numberFormat(temp_amount);

                        if (temp_adjustment[0] == "PHIC" && temp_status === 0) {
                            template = `<div class="mb-0 m--font-bolder m--font-accent">
                                <span class='fa fa-exclamation-circle'></span>
                                <span class="m--font-boldest">${tempData}</span>
                            </div>`;
                        }
                        if (temp_adjustment[0] == "PHIC" && temp_status === 1) {
                            approvedAmount = tempAdj;
                            template = `<div class="mb-0 m--font-bolder m--font-primary">
                                <span class="m--font-boldest">${temp_amount}</span>
                            </div>`;
                        }
                    });
                }
                if ($.inArray(parseInt(row.id), _dtRowPHIC) == -1) {
                    if (typeof _globalFooterAdjustments.phic !== "undefined") {
                        approvedAmount = parseFloat(_globalFooterAdjustments.phic) + approvedAmount;
                    }
                    _globalFooterAdjustments = Object.assign({}, _globalFooterAdjustments, { phic: approvedAmount });
                    _dtRowPHIC.push(parseInt(row.id));
                }
                return template;
            }
        },
        {
            data: "hdmf", // hdmf
            className: "text-right",
            render: function (data, type, row) {
                let approvedAmount = parseFloat(data);
                const tempData = numberFormat(data);
                let template = ``;
                template = tempData;
                const tempCreatedAdjustments = row.created_adjustments;
                if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                    var tempAdj = 0;
                    const created_adjustments = tempCreatedAdjustments.split(",");
                    created_adjustments.forEach((row, i) => {
                        const temp_adjustment = row.split("||");
                        const adj_type = parseInt(temp_adjustment[2]);
                        const temp_status = parseInt(temp_adjustment[3]);
                        let temp_amount = parseFloat(data);
                        if (adj_type == 1) {
                            temp_amount = parseFloat(data) + parseFloat(temp_adjustment[1]);
                        } else {
                            temp_amount = parseFloat(data) - parseFloat(temp_adjustment[1]);
                        }
                        tempAdj = temp_amount;
                        temp_amount = numberFormat(temp_amount);

                        if (temp_adjustment[0] == "HDMF" && temp_status === 0) {
                            template = `<div class="mb-0 m--font-bolder m--font-accent">
                                <span class='fa fa-exclamation-circle'></span>
                                <span class="m--font-boldest">${tempData}</span>
                            </div>`;
                        }
                        if (temp_adjustment[0] == "HDMF" && temp_status === 1) {
                            approvedAmount = tempAdj;
                            template = `<div class="mb-0 m--font-bolder m--font-primary">
                                <span class="m--font-boldest">${temp_amount}</span>
                            </div>`;
                        }
                    });
                }
                if ($.inArray(parseInt(row.id), _dtRowHDMF) == -1) {
                    if (typeof _globalFooterAdjustments.hdmf !== "undefined") {
                        approvedAmount = parseFloat(_globalFooterAdjustments.hdmf) + approvedAmount;
                    }
                    _globalFooterAdjustments = Object.assign({}, _globalFooterAdjustments, { hdmf: approvedAmount });
                    _dtRowHDMF.push(parseInt(row.id));
                }
                return template;
            }
        },
        {
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
                        let temp_amount = parseFloat(data);
                        if (adj_type == 1) {
                            temp_amount = parseFloat(data) + parseFloat(temp_adjustment[1]);
                        } else {
                            temp_amount = parseFloat(data) - parseFloat(temp_adjustment[1]);
                        }
                        tempAdj = temp_amount;
                        temp_amount = numberFormat(temp_amount);

                        if (temp_adjustment[0] == "TAX" && temp_status === 0) {
                            template = `<div class="mb-0 m--font-bolder m--font-accent">
                                <span class='fa fa-exclamation-circle'></span>
                                <span class="m--font-boldest">${tempData}</span>
                            </div>`;
                        }
                        if (temp_adjustment[0] == "TAX" && temp_status === 1) {
                            approvedAmount = tempAdj;
                            template = `<div class="mb-0 m--font-bolder m--font-primary">
                                <span class="m--font-boldest">${temp_amount}</span>
                            </div>`;
                        }
                    });
                }
                if ($.inArray(parseInt(row.id), _dtRowTAX) == -1) {
                    if (typeof _globalFooterAdjustments.tax !== "undefined") {
                        approvedAmount = parseFloat(_globalFooterAdjustments.tax) + approvedAmount;
                    }
                    _globalFooterAdjustments = Object.assign({}, _globalFooterAdjustments, { tax: approvedAmount });
                    _dtRowTAX.push(parseInt(row.id));
                }
                return template;
            }
        },
        {
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
                    if (typeof _globalFooterAdjustments.total_loans !== "undefined") {
                        approvedAmount = parseFloat(_globalFooterAdjustments.total_loans) + approvedAmount;
                    }
                    _globalFooterAdjustments = Object.assign({}, _globalFooterAdjustments, { total_loans: approvedAmount });
                    _dtRowLOAN.push(parseInt(row.id));
                }

                if($.isNumeric(template)){
                    return numberFormat(template);
                }else{
                    return template == '' ? '0.00' : template;
                }
                
            }
        }, { 
            data: null, 
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
            data: "sss_loan",
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

                return template ? template : numberFormat(data);
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

                return template ? template : numberFormat(data);
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
    ],
    order: [[1, "asc"]],
    drawCallback: function (settings) {
        const api = this.api();
        const tempData = api.data();
        const btnPrint = $(settings.nTableWrapper).find(".printTempReportAction");
        const btnExport = $(settings.nTableWrapper).find(".exportTempReportAction");
        const dtActions = $(settings.nTableWrapper).find(".dtActions");

        drawCallbackRequestAction(btnPrint, dtActions, tempData);
        drawCallbackRequestAction(btnExport, dtActions, tempData);

        const tempFooter = $(settings.nTableWrapper).find("tfoot");
        if (typeof tempFooter !== "undefined") { _globalFooterHtml = tempFooter[0].innerHTML; }
    }, footerCallback: function (row, data, start, end, display) {
        globalGrandTotal = {};
        const api = this.api();
        // Remove the formatting to get integer data for summation
        const intVal = function (i) {
            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
        };

        let totalOT = api
            .column(5)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalNDOT = api
            .column(6)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalHoliday = api
            .column(7)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalBasic = api
            .column(8)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalAllowance = api
            .column(9)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalGross = api
            .column(11)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalSSS = api
            .column(12)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalSSS_PROV = api
            .column(13)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalPH = api
            .column(14)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalHDMF = api
            .column(15)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalTAX = api
            .column(16)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalLoans = api
            .column(17)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);
        
        let totalCharges = api
            .column(18)
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

        let totalSSSLoans = api
            .column(19)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalHDMFLoans = api
            .column(20)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalNetpay = api
            .column(21)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

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

        globalGrandTotal = { ..._tempFooterData };

        /*** $(api.column(6).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalUT) + "</span>"); ***/
        $(api.column(5).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalOT) + "</span>");
        $(api.column(6).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalNDOT) + "</span>");
        $(api.column(7).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalHoliday) + "</span>");
        $(api.column(8).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalBasic) + "</span>");
        $(api.column(9).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalAllowance) + "</span>");
        $(api.column(11).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalGross) + "</span>");
        $(api.column(12).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalSSS) + "</span>");
        $(api.column(13).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalSSS_PROV) + "</span>");
        $(api.column(14).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalPH) + "</span>");
        $(api.column(15).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalHDMF) + "</span>");
        $(api.column(16).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalTAX) + "</span>");
        $(api.column(17).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalLoans) + "</span>");
        $(api.column(18).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalCharges) + "</span>");
        $(api.column(19).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalSSSLoans) + "</span>");
        $(api.column(20).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalHDMFLoans) + "</span>");
        $(api.column(21).footer()).html("<span class='m--font-boldest'>&#8369;&nbsp;&nbsp;" + numberFormat(totalNetpay) + "</span>");
    }
});

const dtTableRequest = function (formData) {
    return $.ajax({
        url: siteUrl("hris/reports/no_earners_report_filtered_data"),
        type: "post",
        dataType: "json",
        data: formData,
        success: function (json) {
            if(json.response){
                dtTable.clear();
                dtTable.rows.add(json.data).draw(false);
            }else{
                Swal.fire({
                    title: 'Search filter, not found!',
                    text: "No data found based on the search filter you provided!",
                    icon: 'error',
                });
                dtTable.clear();
                dtTable.rows.add([]).draw(false);
            }
        }, error: function (xhr, error, code) {
            if (error == "parsererror") {
                toastr.warning(code, "Re-loading Content", 5000);
                setTimeout(() => dtTableRequest(formData), 250);
            }
        }
    });
}

$.validate({
    form: '#frm-filter',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        let formData = $(form).serialize();
        dtTableRequest(formData);
        return false;
    }
});
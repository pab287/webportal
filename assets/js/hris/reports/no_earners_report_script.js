let search_val = "", date_from = "", date_to = "", selectedCompany = null;
let psEmployeeGroup = [], globalPrintableSignatory = [], company = [], _companies = [], _payoutSchedule = [], employee = [];
let _globalFooterAdjustments = { sss: 0, sss_prov: 0, phic: 0, hdmf: 0, tax: 0, total_loans: 0 };
let _dtRowSSS = [], _dtRowSSS_PROV = [], _dtRowPHIC = [], _dtRowHDMF = [], _dtRowTAX = [], _dtRowLOAN = [], _tempLastRow = [];
let globalGrandTotal = {};
const exportOptions = {
    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22],
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

const dtTable = $("#table-payroll-sheet").DataTable({
    dom: 'rtlp',
    serverSide: false,
    destroy: true,
    autoWidth: false,
    ordering: false,
    pageLength: 10,
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

                    console.log(data, template);
                    return $.isNumeric(template) ? numberFormat(template) : template || 0.00;
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
});
$.validate({
    form: '#frm-filter',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        let formData = $(form).serialize();
        $.ajax({
            url: siteUrl("hris/reports/no_earners_report_filtered_data"),
            type: "post",
            dataType: "json",
            data: formData,
            success: function (json) {
                if(json.response){
                    dtTable.clear();
                    dtTable.rows.add(json.data).draw(false);
                }
            }
        });
        return false;
    }
});
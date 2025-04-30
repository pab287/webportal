let search_val = "", date_from = "", date_to = "", selectedCompany = null;
let psEmployeeGroup = [], globalPrintableSignatory = [], company = [], _companies = [], _payoutSchedule = [], employee = [];
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
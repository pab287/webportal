let globalPrintableSignatory = [];
const psSignatoryModal = $("#modal-ps--signatory");
const psResetSignatoryModal = $("#modal-ps--reset-signatory");
const _tblPortletPS = $("#m_portlet_tools-contribution_deduction").mPortlet();
let total_amount = 0;
let _filter = [];

let _tempIds = [];
let _years = [];
let _companies = [];
let _payout_mode = [];
let _payoutSchedule = [];
let _station = 0;
let isCollapsedPortlet = true;

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
    
    if(typeof _tempContentData.payout_mode !== "undefined" && _tempContentData.payout_mode.length > 0){
        _payout_mode = _tempContentData.payout_mode;
    }

    if(typeof _tempContentData.payout_schedule !== "undefined" && _tempContentData.payout_schedule.length > 0){ 
        _payoutSchedule = _tempContentData.payout_schedule;
    }
}

_tblPortletPS.on('afterExpand', function (portlet) {
    setTimeout(function () { isCollapsedPortlet = true; }, 500);
});
_tblPortletPS.on('afterCollapse', function (portlet) {
    setTimeout(function () { isCollapsedPortlet = false; }, 500);
});

let tempRangeDates = {
    min_date: moment().startOf('month').format("MM/DD/YYYY"),
    max_date: moment().endOf('month').format("MM/DD/YYYY"),
};

$(document).ready(function () {
    $("input[name=group]").change(function () {
        var value = $('input[name=group]:checked').val();
        if (value == 1) {
            $("#paydate-filter").removeClass('m--hide');
            $("#filter-by-month-year").addClass('m--hide');
            $("#company").val('').trigger('change');
            $("#payroll_group").empty();
            $("#payout_mode").val('').trigger('change');
            $("#station").val('').trigger('change');
            $("#payout_schedule").val('').trigger('change');
            $("#employees").empty().prop("disabled", false);
        } else {
            $("#filter-by-month-year").removeClass('m--hide');
            $("#paydate-filter").addClass('m--hide');
            $("#company").val('').trigger('change');
            $("#payroll_group").empty();
            $("#payout_mode").val('').trigger('change');
            $("#station").val('').trigger('change');
            $("#payout_schedule").val('').trigger('change');
            $("#employees").empty().prop("disabled", false);
        }
    });
});

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

$("#employees").select2({
    placeholder: 'Select',
    width: '100%',
    ajax: {
        url: baseUrl("payroll/reports/select_employee"),
        dataType: "json",
        delay: 250,
        global: false,
        data: function (params) {
            params.q = params.term;
            params.payout_mode = $("form#frm-filter-payroll-contribution select#payout_mode").val();
            return params;
        },
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
            params.company_id = $("form#frm-filter-payroll-contribution select#company").val();
            params.payout_mode = $("form#frm-filter-payroll-contribution select#payout_mode").val();
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
                        const tempEmployeeSelector = $("form#frm-filter-payroll-contribution select#employees");
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
            const tempEmployeeSelector = $("form#frm-filter-payroll-contribution select#employees");
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
        const tempEmployeeSelector = $("form#frm-filter-payroll-contribution select#employees");
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
                        const tempEmployeeSelector = $("form#frm-filter-payroll-contribution select#employees");
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

$("#company").select2({
        width: '100%',
        data: _companies,
        placeholder: 'Select an option',
        allowClear: true,
    }).on("select2:select", function (data) {
        $(data.target).validate();
    }).on("select2:unselect", function (e) {
        $("#frm-filter-payroll-contribution")
            .find("select#employees")
            .val([])
            .trigger("change")
            .prop("disabled", false);
    }).on("change", function (e) {
        $("#frm-filter-payroll-contribution")
            .find("select#payroll_group")
            .val([])
            .trigger("change");
        $("#frm-filter-payroll-contribution")
            .find("select#employees")
            .val([])
            .trigger("change")
            .prop("disabled", false);
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


var resetFilter = function (event) {
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        form.find("input[name=group]")[0].click(); // added for reseting the filter by
        const select2 = form.find("#employees, #payroll_group, #company");
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

var vmPrintArea = new Vue({
    el: "#printArea, #printArea_monthly",
    data: { rows: {}, count: 0, filter: {}, signatures: {}, signature_count: 0, row_columns: [], column_count: 0, grand_total_footer: {}, grand_total: {}, print_counter: { count: 0, last_printed: null, last_printed_at: null } },
    methods: {
        rowFormatNumber: function (number) {
            return numberFormat(number);
        },
        printContent: function (printdivname) {
            const _this = this;
            if (_this.count > 0) { printdiv(printdivname); }
            else { toastr.error("Nothing to print!", "Contribution / Deduction"); }
            return this;
        }, ctrCount: function(number){
            return parseInt(number) + 1;
        }, renderColumnLabel: function(label){
            let tempLabel = label.toUpperCase();
            switch(label){
                case 'cal.': tempLabel = 'SSS CAL'; break;
                case 'cal': tempLabel = 'HDMF CAL'; break;
                case 'chrge': tempLabel = 'CHARGES'; break;
                case 'medloan': tempLabel = 'MED LOAN'; break;
                default: tempLabel = label.toUpperCase(); break;
            }
            return tempLabel;
        }, getStationGroups: function () {
            const rawRows = Array.isArray(this.rows) ? this.rows : Object.values(this.rows || {});
            const grouped = {};
            rawRows.forEach(function (item) {
                const station = (item && item.station && String(item.station).trim()) ? String(item.station).trim().toUpperCase() : 'NO ASSIGNED PROJECT';
                if (!grouped[station]) { grouped[station] = []; }
                grouped[station].push(item);
            });

            return Object.keys(grouped)
                .sort()
                .map(function (station) {
                    return { station: station, rows: grouped[station] };
                });
        }, getDynamicColumnValue: function (item, header) {
            if (!item) { return 0; }
            const key = String(header || '').toLowerCase();
            const value = item[key];
            return value ? parseFloat(value) : 0;
        }, getStationSubtotal: function (rows, key) {
            const _this = this;
            const tempRows = Array.isArray(rows) ? rows : [];
            return tempRows.reduce(function (acc, row) {
                if (!row) { return acc; }
                if (['sss', 'sss_prov', 'ph', 'hdmf', 'tax'].indexOf(key) >= 0) {
                    const val = row[key] ? parseFloat(row[key]) : 0;
                    return acc + (Number.isNaN(val) ? 0 : val);
                }
                return acc + _this.getDynamicColumnValue(row, key);
            }, 0);
        }, getStationSummaryColumns: function () {
            const dynamicCols = Array.isArray(this.row_columns) ? this.row_columns.map(function (col) {
                return String(col || '').toLowerCase();
            }) : [];
            return ['sss', 'sss_prov', 'ph', 'hdmf', 'tax'].concat(dynamicCols);
        }, getStationSummary: function () {
            const _this = this;
            const columns = _this.getStationSummaryColumns();
            const groups = this.getStationGroups();
            return groups.map(function (group) {
                const uniq = {};
                const totals = {};
                columns.forEach(function (col) { totals[col] = 0; });
                (group.rows || []).forEach(function (row) {
                    const empId = row && row.emp_id ? parseInt(row.emp_id, 10) : 0;
                    if (empId > 0) { uniq[empId] = true; }
                });

                columns.forEach(function (col) {
                    totals[col] = _this.getStationSubtotal(group.rows, col);
                });

                return {
                    station: group.station,
                    employee_count: Object.keys(uniq).length,
                    totals: totals
                };
            });
        }, getStationSummaryGrandTotal: function () {
            const columns = this.getStationSummaryColumns();
            const summaries = this.getStationSummary();
            return summaries.reduce(function (acc, row) {
                acc.employee_count += row.employee_count || 0;
                columns.forEach(function (col) {
                    acc.totals[col] += row.totals && row.totals[col] ? row.totals[col] : 0;
                });
                return acc;
            }, (function () {
                const initTotals = {};
                columns.forEach(function (col) { initTotals[col] = 0; });
                return { employee_count: 0, totals: initTotals };
            })());
        }
    }
});

var vmPrintAreaMonthly = new Vue({
    el: "#printArea_monthly",
    data: { count: 0, filter: {}, signatures: {}, signature_count: 0, print_counter: { count: 0, last_printed: null, last_printed_at: null } },
    methods: {
        rowFormatNumber: function (number) {
            return numberFormat(number);
        },
        printContent: function (printdivname) {
            const _this = this;
            if (_this.count > 0) { printDivMonthly(printdivname); }
            else { toastr.error("Nothing to print!", "Contribution / Deduction"); }
            return this;
        }
    }
});

function printdiv(printdivname) {
    const parseSerializedFilter = function (serialized) {
        const parsed = {};
        if (!serialized) { return parsed; }

        $.each(serialized.split("&"), function (_i, pair) {
            if (!pair) { return; }

            const parts = pair.split("=");
            const rawKey = decodeURIComponent((parts.shift() || "").replace(/\+/g, " "));
            const rawValue = decodeURIComponent((parts.join("=") || "").replace(/\+/g, " "));
            if (!rawKey) { return; }

            if (rawKey.slice(-2) === "[]") {
                const arrayKey = rawKey.slice(0, -2);
                if (!Array.isArray(parsed[arrayKey])) { parsed[arrayKey] = []; }
                parsed[arrayKey].push(rawValue);
            } else {
                parsed[rawKey] = rawValue;
            }
        });

        return parsed;
    };

    const payload = parseSerializedFilter(_filter);
    delete payload.csrf_token;

    payload[_csrf_token] = _csrf_hash;
    payload.amount = total_amount;
    payload.module = "contribution_report";

    $.ajax({
        url: baseUrl('payroll/reports/count_print'),
        data: payload,
        dataType: "json",
        type: 'post',
        success: function (response) {
            const data = response.data;
            const isDisplayValue = function (value) {
                if (value === null || typeof value === "undefined") { return false; }
                const tempVal = String(value).trim();
                if (!tempVal) { return false; }
                if (tempVal.toLowerCase() === "null") { return false; }
                if (tempVal.toLowerCase() === "no assigned name") { return false; }
                return true;
            };

            vmPrintArea.print_counter.count = (data && typeof data.count !== "undefined") ? data.count : 0;
            vmPrintArea.print_counter.last_printed = (data && isDisplayValue(data.last_printed_by)) ? data.last_printed_by : null;
            vmPrintArea.print_counter.last_printed_at = (data && isDisplayValue(data.last_printed_at)) ? moment(data.last_printed_at).format('lll').toUpperCase() : null;

            Vue.nextTick(function () {
                window.requestAnimationFrame(function () {
                    window.requestAnimationFrame(function () {
                        var newstr = document.getElementById(printdivname).innerHTML;
                        var printWindow = window.open(siteUrl('payroll/reports/printable_form'), '_blank');
                        printWindow.focus();
                        printWindow.onload = function(){
                            const appendContainer = printWindow.document.getElementById('append_printable-container');
                            if (typeof appendContainer !== "undefined" && appendContainer !== null) {
                                appendContainer.innerHTML = newstr;
                                const footerSignature = printWindow.document.getElementById('footer-signature');
                                const headerCompanyTitle = printWindow.document.getElementById('header--company_title--center');
                                const printAction = printWindow.document.getElementById('printAction');
            
                                if (footerSignature) { footerSignature.classList.remove('m--hide'); }
                                if (headerCompanyTitle) { headerCompanyTitle.classList.remove('m--hide'); }
                                if (printAction) { printAction.classList.add('m--hide'); }
                                setTimeout(function () {
                                    printWindow.print();
                                    printWindow.close();
                                }, 200);
                            } else {
                                toastr.info("Print detail(s) is still in progress!", "Contribution / Deduction");
                                printWindow.close();
                            }
                        }
                    });
                });
            });
        }
    });

    
    return false;
}

function printDivMonthly(printdivname) {
    const parseSerializedFilter = function (serialized) {
        const parsed = {};
        if (!serialized) { return parsed; }

        $.each(serialized.split("&"), function (_i, pair) {
            if (!pair) { return; }

            const parts = pair.split("=");
            const rawKey = decodeURIComponent((parts.shift() || "").replace(/\+/g, " "));
            const rawValue = decodeURIComponent((parts.join("=") || "").replace(/\+/g, " "));
            if (!rawKey) { return; }

            if (rawKey.slice(-2) === "[]") {
                const arrayKey = rawKey.slice(0, -2);
                if (!Array.isArray(parsed[arrayKey])) { parsed[arrayKey] = []; }
                parsed[arrayKey].push(rawValue);
            } else {
                parsed[rawKey] = rawValue;
            }
        });

        return parsed;
    };

    const payload = parseSerializedFilter(_filter);
    delete payload.csrf_token;

    payload[_csrf_token] = _csrf_hash;
    payload.amount = total_amount;
    payload.module = "contribution_report";

    $.ajax({
        url: baseUrl('payroll/reports/count_print'),
        data: payload,
        dataType: "json",
        type: 'post',
        success: function (response) {
            const data = response.data;
            const isDisplayValue = function (value) {
                if (value === null || typeof value === "undefined") { return false; }
                const tempVal = String(value).trim();
                if (!tempVal) { return false; }
                if (tempVal.toLowerCase() === "null") { return false; }
                if (tempVal.toLowerCase() === "no assigned name") { return false; }
                return true;
            };

            vmPrintAreaMonthly.print_counter.count = (data && typeof data.count !== "undefined") ? data.count : 0;
            vmPrintAreaMonthly.print_counter.last_printed = (data && isDisplayValue(data.last_printed_by)) ? data.last_printed_by : null;
            vmPrintAreaMonthly.print_counter.last_printed_at = (data && isDisplayValue(data.last_printed_at)) ? moment(data.last_printed_at).format('lll').toUpperCase() : null;

            Vue.nextTick(function () {
                window.requestAnimationFrame(function () {
                    window.requestAnimationFrame(function () {
                        var newstr = document.getElementById(printdivname).innerHTML;
                        var printWindow = window.open(siteUrl('payroll/reports/printable_form'), '_blank');
                        printWindow.focus();
                        printWindow.onload = function(){
                            setTimeout(function(){
                                const appendContainer = printWindow.document.getElementById('append_printable-container');
                                if (typeof appendContainer !== "undefined" && appendContainer !== null) {
                                    appendContainer.innerHTML = newstr;
                                    const footerSignature = printWindow.document.getElementById('footer-signature');
                                    const headerCompanyTitle = printWindow.document.getElementById('header--company_title--center');
                                    const printAction = printWindow.document.getElementById('printAction');

                                    if (footerSignature) { footerSignature.classList.remove('m--hide'); }
                                    if (headerCompanyTitle) { headerCompanyTitle.classList.remove('m--hide'); }
                                    if (printAction) { printAction.classList.add('m--hide'); }
                                    setTimeout(function () {
                                        printWindow.print();
                                        printWindow.close();
                                    }, 200);
                                } else {
                                    toastr.info("Print detail(s) is still in progress!", "Contribution / Deduction");
                                    printWindow.close();
                                }
                            }, 200);
                        }
                    });
                });
            });
        }
    });

    return false;
}

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

                                        vmPrintArea.signatures = Object.assign({}, currentData);
                                        vmPrintArea.signature_count = ctr;

                                        vmPrintAreaMonthly.signatures = Object.assign({}, currentData);
                                        vmPrintAreaMonthly.signature_count = ctr;

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

                                vmPrintArea.signatures = Object.assign({}, tempRow);
                                vmPrintArea.signature_count = ctr;

                                vmPrintAreaMonthly.signatures = Object.assign({}, tempRow);
                                vmPrintAreaMonthly.signature_count = ctr;

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
                    url: baseUrl("payroll/reports/select_employee"),
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
var _clearTable = true;


$.validate({
    form: '#frm-filter-payroll-contribution',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        var currentForm = form[0];
        var formMethod = currentForm.method;
        var formUrl = currentForm.action;
        var formData = $(currentForm).serialize();

        _filter = formData;

        var emptyEmployeeList = $(currentForm).find("#employees").serialize() ? true : false;
        if (emptyEmployeeList == false && $(currentForm).find("#employees").val().length > 0) {
            formData += '&serialized_employees=' + $(currentForm).find("#employees").val().toString();
        }
        var currentSelectCompanyId = $(currentForm).find("#company").val();
        var payrollGroup = $(currentForm).find("#payroll_group").text();
        if(payrollGroup){ formData += '&payroll_group='+payrollGroup; }

        if ($('input[name=group]:checked').val() == 1) {
            $("#pay_date_data").removeClass('m--hide');
            $("#monthly_data").addClass('m--hide');
            $.ajax({
                url: baseUrl("payroll/reports/generate_contribution_deduction"),
                type: formMethod,
                dataType: "json",
                data: formData,
                beforeSend: function () {
                    $(form[0])
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (json) {
                    vmPrintArea.rows = Object.assign({});
                    vmPrintArea.grand_total_footer = Object.assign({});
                    vmPrintArea.count = 0;
                    if (json.response) {
                        vmPrintArea.rows = Object.assign({}, json.data);
                        vmPrintArea.filter = Object.assign({}, json.filter);
                        vmPrintArea.count = json.count;

                        vmPrintArea.row_columns = json.row_columns;
                        vmPrintArea.column_count = json.column_count;
                        vmPrintArea.grand_total_footer = Object.assign({}, json.grand_total_footer);
                        vmPrintArea.grand_total = Object.assign({}, json.grand_total);
                    }
                    $(form[0])
                        .find(".btn-submit")
                        .removeClass("m-btn--custom m-loader m-loader--light m-loader--right")
                        .prop("disabled", false);
                }
            });
        } else {
            $("#monthly_data").removeClass('m--hide');
            $("#pay_date_data").addClass('m--hide');
            const url = baseUrl("payroll/reports/generate_taxable_income_report_month");
            getScriptRendering(url, formData, currentForm);

            $(form[0])
                .find(".btn-submit")
                .removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }

        if (typeof currentSelectCompanyId !== "undefined" && parseInt(currentSelectCompanyId) > 0) {
            $.ajax({
                url: siteUrl("payroll/reports/get_current_signatory_by_company_and_type/" + currentSelectCompanyId + "/2"),
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

                    vmPrintArea.signatures = Object.assign({}, tempRow);
                    vmPrintArea.signature_count = ctr;

                    vmPrintAreaMonthly.signatures = Object.assign({}, tempRow);
                    vmPrintAreaMonthly.signature_count = ctr;

                    vmResetSignatories.row = Object.assign({}, tempRow);
                    vmResetSignatories.count = ctr;
                }
            });
        }

        return false;
    }
});


var getScriptRendering = function (formUrl, formData, currentForm) {
    $.ajax({
        url: formUrl,
        type: "post",
        dataType: "json",
        data: formData,
        beforeSend: function () {
            $(currentForm)
                .find(".btn-submit")
                .addClass("m-btn--custom m-loader m-loader--light m-loader--right")
                .prop("disabled", true);
        },
        success: function (response) {
            vmPrintAreaMonthly.filter = Object.assign({});
            vmPrintAreaMonthly.count = 0;
            if (response.response) {
                _clearTable = false;
                _tempIds = [];
                _tempIds = response.data;
                vmPrintAreaMonthly.filter = Object.assign({}, response.filters);
                $.ajax({
                    url: siteUrl("payroll/reports/generate_contribution_deduction_list"),
                    type: "post",
                    dataType: "json",
                    data: { ps_id: _tempIds, [_csrf_token]: _csrf_hash },
                    success: function (json) {
                        if (json.response) {
                            vmPrintAreaMonthly.count = json.count;

                            const tempTable = json.html;
                            $("#company_name").html($("#company option:selected").text());
                            $("#month_date").empty().html(response.filters.month);
                            $("#append--table_content").empty().html(tempTable);
                            $("#monthly_contrib_deduct").DataTable({
                                dom: 'rtlip', processing: true, destroy: true, paging: false, searching: false, bInfo: false, ordering: false,
                                buttons: [
                                    {
                                        extend: 'print',
                                        text: '<i class="fa fa-print"></i><span class="m--font-boldest">PRINT</span>',
                                        className: "pull-right printRemittanceAction btnPrint",
                                        exportOptions: {
                                            columns: ':visible',
                                            stripHtml: false,
                                        }
                                    }
                                ],
                            });
                        }else{
                            $("#append--table_content").empty().html("<h6 class='text-center mt-3 m--font-danger text-uppercase'>No Loan(s) Contribution/Deduction found!</h6>");
                        }
                    }
                });
                toastr.success(response.toastr_msg, "Filtered Taxable Income");
            } else {
                _clearTable = true;
                toastr.error(response.toastr_msg, "Filtered Taxable Income");
                $("#append--table_content").empty().html("<h6 class='text-center mt-3 m--font-danger text-uppercase'>No Loan(s) Contribution/Deduction found!</h6>");
            }

            $(currentForm)
                .find(".btn-submit")
                .removeClass(
                    "m-btn--custom m-loader m-loader--light m-loader--right"
                ).prop("disabled", false);
        }, error: function (xhr, error, code) {
            if (error == "parsererror") {
                getScriptRendering(formUrl, formData, currentForm);
            }
        }
    });
}

$("#payout_mode").select2({
    allowClear: true,
    width: '100%',
    data: _payout_mode,
    placeholder: "SELECT AN OPTION",
});

$("#station").select2({
    allowClear: true,
    width: '100%',
    ajax: {
        url: baseUrl('payroll/reports/select2_station'),
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
    }, language: { errorLoading: function () { return "Searching..." } },
    placeholder: "SELECT AN OPTION",
});

$("#payout_schedule").select2({
    width: "100%",
    data: _payoutSchedule,
    placeholder: "SELECT AN OPTION",
    allowClear: true,
});

function exportContent(element) {
    if (!vmPrintArea || !vmPrintArea.count) {
        toastr.error("Nothing to export!", "Contribution / Deduction");
        return;
    }

    const reportTitle = "PAYROLL SHEET - CONTRIBUTION/DEDUCTION REPORT";
    const dynamicColumns = Array.isArray(vmPrintArea.row_columns) ? vmPrintArea.row_columns : [];
    const sourceRows = Array.isArray(vmPrintArea.rows) ? vmPrintArea.rows : Object.values(vmPrintArea.rows || {});
    const rows = sourceRows
        .slice()
        .sort(function (a, b) {
            const nameA = ((a && a.employee_name) ? String(a.employee_name) : "").toLowerCase().trim();
            const nameB = ((b && b.employee_name) ? String(b.employee_name) : "").toLowerCase().trim();
            return nameA.localeCompare(nameB);
        });

    const formatNumber = function (value) {
        const parsed = parseFloat(value);
        return Number.isNaN(parsed) ? "0.00" : parsed.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    const renderColumnLabel = function (label) {
        let tempLabel = String(label || "").toUpperCase();
        switch (String(label || "").toLowerCase()) {
            case "cal.": tempLabel = "SSS CAL"; break;
            case "cal": tempLabel = "HDMF CAL"; break;
            case "chrge": tempLabel = "CHARGES"; break;
            case "medloan": tempLabel = "MED LOAN"; break;
            default: tempLabel = String(label || "").toUpperCase(); break;
        }
        return tempLabel;
    };

    const parseNumber = function (value) {
        const parsed = parseFloat(value);
        return Number.isNaN(parsed) ? 0 : parsed;
    };

    const headers = ["#", "EMPLOYEE NAME", "SSS", "SSS PROV", "PHIC", "HDMF", "TAX"];
    $.each(dynamicColumns, function (_i, header) {
        headers.push(renderColumnLabel(header));
    });
    const lastColIndex = headers.length - 1;

    const totals = {
        sss: 0, sss_prov: 0, ph: 0, hdmf: 0, tax: 0
    };
    const dynamicTotals = {};
    $.each(dynamicColumns, function (_i, header) {
        dynamicTotals[String(header || "").toLowerCase()] = 0;
    });

    const titleRow1 = new Array(headers.length).fill("");
    const titleRow2 = new Array(headers.length).fill("");
    titleRow1[0] = reportTitle;

    const exportRows = [];
    exportRows.push(titleRow1);
    exportRows.push(titleRow2);
    exportRows.push(headers);

    $.each(rows, function (index, item) {
        const rowData = [];
        const sss = parseNumber(item && item.sss);
        const sssProv = parseNumber(item && item.sss_prov);
        const ph = parseNumber(item && item.ph);
        const hdmf = parseNumber(item && item.hdmf);
        const tax = parseNumber(item && item.tax);

        totals.sss += sss;
        totals.sss_prov += sssProv;
        totals.ph += ph;
        totals.hdmf += hdmf;
        totals.tax += tax;

        rowData.push(index + 1);
        rowData.push(item && item.employee_name ? item.employee_name : "");
        rowData.push(formatNumber(sss));
        rowData.push(formatNumber(sssProv));
        rowData.push(formatNumber(ph));
        rowData.push(formatNumber(hdmf));
        rowData.push(formatNumber(tax));

        $.each(dynamicColumns, function (_ii, header) {
            const key = String(header || "").toLowerCase();
            const val = parseNumber(item && item[key]);
            dynamicTotals[key] += val;
            rowData.push(formatNumber(val));
        });

        exportRows.push(rowData);
    });

    const grandTotalRow = ["", "GRAND TOTAL", formatNumber(totals.sss), formatNumber(totals.sss_prov), formatNumber(totals.ph), formatNumber(totals.hdmf), formatNumber(totals.tax)];
    $.each(dynamicColumns, function (_i, header) {
        const key = String(header || "").toLowerCase();
        grandTotalRow.push(formatNumber(dynamicTotals[key]));
    });
    exportRows.push(grandTotalRow);

    const xlsxFileName = "contribution_deduction_report.xlsx";
    const endColIndex = lastColIndex + 1; // ExcelJS is 1-based

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Contribution Report");

    worksheet.getColumn(1).width = 5;
    worksheet.getColumn(2).width = 28;
    for (let i = 3; i <= endColIndex; i++) {
        worksheet.getColumn(i).width = 14;
    }

    worksheet.mergeCells(1, 1, 2, endColIndex);
    const titleCell = worksheet.getCell("A1");
    titleCell.value = reportTitle;
    titleCell.font = { bold: true, size: 16, name: "Arial" };
    titleCell.alignment = { horizontal: "center", vertical: "middle", wrapText: true };
    worksheet.getRow(1).height = 25;
    worksheet.getRow(2).height = 25;

    const headerRow = worksheet.getRow(3);
    headerRow.height = 18;
    headers.forEach(function (h, i) {
        const cell = headerRow.getCell(i + 1);
        cell.value = h;
        cell.font = { bold: true, name: "Arial" };
        cell.alignment = { horizontal: "center", vertical: "middle" };
    });

    $.each(rows, function (index, item) {
        const sss     = parseNumber(item && item.sss);
        const sssProv = parseNumber(item && item.sss_prov);
        const ph      = parseNumber(item && item.ph);
        const hdmf    = parseNumber(item && item.hdmf);
        const tax     = parseNumber(item && item.tax);

        totals.sss      += sss;
        totals.sss_prov += sssProv;
        totals.ph       += ph;
        totals.hdmf     += hdmf;
        totals.tax      += tax;

        const rowValues = [
            index + 1,
            item && item.employee_name ? item.employee_name : "",
            formatNumber(sss), formatNumber(sssProv),
            formatNumber(ph), formatNumber(hdmf), formatNumber(tax)
        ];
        $.each(dynamicColumns, function (_ii, header) {
            const key = String(header || "").toLowerCase();
            const val = parseNumber(item && item[key]);
            dynamicTotals[key] += val;
            rowValues.push(formatNumber(val));
        });

        const dataRow = worksheet.addRow(rowValues);
        dataRow.eachCell(function (cell) {
            cell.alignment = { horizontal: "center", vertical: "middle" };
        });
        dataRow.getCell(2).alignment = { horizontal: "left", vertical: "middle" };
    });

    const totalValues = ["", "GRAND TOTAL",
        formatNumber(totals.sss), formatNumber(totals.sss_prov),
        formatNumber(totals.ph), formatNumber(totals.hdmf), formatNumber(totals.tax)
    ];
    $.each(dynamicColumns, function (_i, header) {
        totalValues.push(formatNumber(dynamicTotals[String(header || "").toLowerCase()]));
    });
    const totalRow = worksheet.addRow(totalValues);
    totalRow.eachCell(function (cell) {
        cell.font = { bold: true, name: "Arial" };
        cell.alignment = { horizontal: "center", vertical: "middle" };
    });

    workbook.xlsx.writeBuffer().then(function (buffer) {
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = xlsxFileName;
        a.click();
        URL.revokeObjectURL(url);
    });
}

function exportDivMonthly(element) {
    if (!vmPrintAreaMonthly || !vmPrintAreaMonthly.count) {
        toastr.error("Nothing to export!", "Contribution / Deduction");
        return;
    }

    const $table = $("#monthly_contrib_deduct");
    if (!$table.length) {
        toastr.error("Monthly report table is not available.", "Contribution / Deduction");
        return;
    }

    const reportTitle = "CONTRIBUTION/DEDUCTION REPORT";
    const headers = [];
    $table.find("thead tr:first th").each(function () {
        headers.push($(this).text().trim());
    });

    const bodyRows = [];
    $table.find("tbody tr").each(function () {
        const row = [];
        $(this).find("td, th").each(function () {
            row.push($(this).text().trim());
        });
        if (row.length) { bodyRows.push(row); }
    });

    const footerRows = [];
    $table.find("tfoot tr").each(function () {
        const row = [];
        $(this).find("td, th").each(function () {
            row.push($(this).text().trim());
        });
        if (row.length) { footerRows.push(row); }
    });

    const maxCols = Math.max(
        headers.length,
        bodyRows.reduce(function (max, row) { return Math.max(max, row.length); }, 0),
        footerRows.reduce(function (max, row) { return Math.max(max, row.length); }, 0)
    );

    if (!maxCols) {
        toastr.error("No monthly rows found for export.", "Contribution / Deduction");
        return;
    }

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Monthly Contribution");
    const endColIndex = maxCols;

    for (let i = 1; i <= endColIndex; i++) {
        worksheet.getColumn(i).width = (i === 2) ? 28 : 14;
    }

    worksheet.mergeCells(1, 1, 2, endColIndex);
    const titleCell = worksheet.getCell("A1");
    titleCell.value = reportTitle;
    titleCell.font = { bold: true, size: 16, name: "Arial" };
    titleCell.alignment = { horizontal: "center", vertical: "middle", wrapText: true };
    worksheet.getRow(1).height = 25;
    worksheet.getRow(2).height = 25;

    const headerRow = worksheet.getRow(3);
    for (let i = 0; i < endColIndex; i++) {
        const cell = headerRow.getCell(i + 1);
        cell.value = headers[i] || "";
        cell.font = { bold: true, name: "Arial" };
        cell.alignment = { horizontal: "center", vertical: "middle" };
    }

    const normalizedHeaders = headers.map(function (h) {
        return String(h || "").toUpperCase().trim();
    });
    let employeeNameIndex = normalizedHeaders.findIndex(function (h) {
        return h === "EMPLOYEE NAME";
    });
    if (employeeNameIndex < 0) {
        employeeNameIndex = normalizedHeaders.findIndex(function (h) {
            return h.indexOf("EMPLOYEE NAME") >= 0;
        });
    }
    if (employeeNameIndex < 0) {
        employeeNameIndex = normalizedHeaders.findIndex(function (h) {
            return h.indexOf("EMPLOYEE") >= 0 && h.indexOf("#") === -1;
        });
    }
    const numberIndex = headers.findIndex(function (h) {
        return String(h || "").trim() === "#";
    });

    const employeeRows = bodyRows
        .filter(function (row) {
            if (employeeNameIndex < 0 || !row[employeeNameIndex]) { return false; }
            const name = String(row[employeeNameIndex] || "").trim().toUpperCase();
            if (!name) { return false; }
            if (name.indexOf("PROJECT #:") >= 0) { return false; }
            if (name.indexOf("SUB TOTAL") >= 0) { return false; }
            if (name.indexOf("GRAND TOTAL") >= 0) { return false; }
            return true;
        })
        .sort(function (a, b) {
            const nameA = String(a[employeeNameIndex] || "").toLowerCase().trim();
            const nameB = String(b[employeeNameIndex] || "").toLowerCase().trim();
            return nameA.localeCompare(nameB);
        });

    employeeRows.forEach(function (rowValues, idx) {
        const padded = rowValues.slice();
        while (padded.length < endColIndex) { padded.push(""); }
        if (numberIndex >= 0) { padded[numberIndex] = String(idx + 1); }
        const dataRow = worksheet.addRow(padded);
        dataRow.eachCell(function (cell) {
            cell.alignment = { horizontal: "center", vertical: "middle" };
        });
    });

    const parseNumber = function (value) {
        const clean = String(value == null ? "" : value).replace(/,/g, "");
        const parsed = parseFloat(clean);
        return Number.isNaN(parsed) ? null : parsed;
    };
    const formatNumber = function (value) {
        return Number(value || 0).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    const grandTotal = new Array(endColIndex).fill("");
    if (employeeNameIndex >= 0) { grandTotal[employeeNameIndex] = "GRAND TOTAL"; }
    for (let i = 0; i < endColIndex; i++) {
        if (i === numberIndex || i === employeeNameIndex) { continue; }
        let sum = 0;
        let hasNumber = false;
        employeeRows.forEach(function (row) {
            const val = parseNumber(row[i]);
            if (val !== null) {
                sum += val;
                hasNumber = true;
            }
        });
        if (hasNumber) { grandTotal[i] = formatNumber(sum); }
    }

    const totalRow = worksheet.addRow(grandTotal);
    totalRow.eachCell(function (cell) {
        cell.font = { bold: true, name: "Arial" };
        cell.alignment = { horizontal: "center", vertical: "middle" };
    });

    const xlsxFileName = "contribution_deduction_monthly.xlsx";
    workbook.xlsx.writeBuffer().then(function (buffer) {
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = xlsxFileName;
        a.click();
        URL.revokeObjectURL(url);
    });
}

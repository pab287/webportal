let globalPrintableSignatory = [];
const psSignatoryModal = $("#modal-ps--signatory");
const psResetSignatoryModal = $("#modal-ps--reset-signatory");
const _tblPortletPS = $("#m_portlet_tools-contribution_deduction").mPortlet();

let _tempIds = [];
let _years = [];
let _companies = [];
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
            $("#employees").empty().prop("disabled", false);
        } else {
            $("#filter-by-month-year").removeClass('m--hide');
            $("#paydate-filter").addClass('m--hide');
            $("#company").val('').trigger('change');
            $("#payroll_group").empty();
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
    data: { rows: {}, count: 0, filter: {}, signatures: {}, signature_count: 0, row_columns: [], column_count: 0, grand_total_footer: {}, grand_total: {} },
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
        }
    }
});

var vmPrintAreaMonthly = new Vue({
    el: "#printArea_monthly",
    data: { count: 0, filter: {}, signatures: {}, signature_count: 0 },
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
    var newstr = document.getElementById(printdivname).innerHTML;
    var printWindow = window.open(siteUrl('payroll/reports/printable_form'), '_blank');
    printWindow.focus();
    printWindow.onload = function(){
        setTimeout(function(){
            const appendContainer = printWindow.document.getElementById('append_printable-container');
            if (typeof appendContainer !== "undefined" && appendContainer !== null) {
                appendContainer.innerHTML = newstr;
                printWindow.document.getElementById('footer-signature').classList.remove('m--hide');
                printWindow.document.getElementById('header--company_title--center').classList.remove('m--hide');
                printWindow.document.getElementById('printAction').classList.add('m--hide');
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
    return false;
}

function printDivMonthly(printdivname) {
    var newstr = document.getElementById(printdivname).innerHTML;
    var printWindow = window.open(siteUrl('payroll/reports/printable_form'), '_blank');
    printWindow.focus();
    printWindow.onload = function(){
        setTimeout(function(){
            const appendContainer = printWindow.document.getElementById('append_printable-container');
            if (typeof appendContainer !== "undefined" && appendContainer !== null) {
                appendContainer.innerHTML = newstr;
                printWindow.document.getElementById('footer-signature').classList.remove('m--hide');
                printWindow.document.getElementById('header--company_title--center').classList.remove('m--hide');
                printWindow.document.getElementById('printAction').classList.add('m--hide');
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
            var url = baseUrl("payroll/reports/generate_taxable_income_report_month");
            getScriptRendering(url, formData, currentForm);

            $(form[0])
                .find(".btn-submit")
                .removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }

        if (typeof currentSelectCompanyId !== undefined && parseInt(currentSelectCompanyId) > 0) {
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
                        }
                    }
                });
                toastr.success(response.toastr_msg, "Filtered Taxable Income");
            } else {
                _clearTable = true;
                toastr.error(response.toastr_msg, "Filtered Taxable Income");
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






let _tempIds = [];
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

var _tempFields = { sss: 9, hdmf: 10, ph: 11, taxable: 12, tax: 13, total: 14 };
var _visibleFields = [];
var _clearTable = true;
var _tempFilter = {};
var _globalFooterHtml = null;
let _totalTaxable = 0;
var _tempData = {
    show_by_date: true, show_picker: false,
    year_picker: false, hide_fields: [],
    visible_fields: [], company_ids: [],
    adjustment_fields: [],
};

var vmGenerateRemittance = new Vue({
    el: "#generate-remittance_content",
    data: _tempData,
    methods: {
        updateVisibleFields: function () {
            var _this = this;
            var currentElement = _this.$el;
            var tempVisibleFields = $(currentElement).find("input.temp-visible_fields:checked");
            if (typeof tempVisibleFields !== "undefined" && tempVisibleFields.length > 0) {
                var tempVF = [];
                $.each(tempVisibleFields, function (i, v) {
                    var tempVal = $(v).val();
                    tempVF.push(tempVal);
                });
                _this.visible_fields = tempVF;
            }
        },
        tempShowByDates: function (id) {
            var _this = this;
            var currentElement = _this.$el;
            _this.show_by_date = (id == 1) ? true : false;
            _this.year_picker = (id == 3) ? true : false;
            _this.show_picker = false;

            if (id == 1) {
                var filterDateRange = $(currentElement).find("#filter_date_range");
                if (typeof filterDateRange !== "undefined" && filterDateRange.length == 1) {
                    filterDateRange.on("change", function () {
                        var thisFilter = this;
                        if (thisFilter.checked) {
                            $("#filter-by-date-range").removeClass('m--hide');
                            $("#filter-by-month-year").addClass('m--hide');
                        } else {
                            $("#filter-by-date-range").addClass('m--hide');
                            $("#filter-by-month-year").removeClass('m--hide');
                        }
                    });
                }
            }

            _this.renderSelect2Picker();
            return _this;
        }, tempShowPicker: function () {
            var _this = this;
            var currentElement = _this.$el;
            _this.show_picker = (_this.show_picker == true) ? false : true;
            if (_this.show_picker === true) {
                setTimeout(function () {
                    $(currentElement).find("#date-picker")
                        .daterangepicker({
                            buttonClasses: 'm-btn btn',
                            applyClass: 'btn-primary',
                            cancelClass: 'btn-secondary',
                            locale: {
                                format: 'MM/DD/YYYY'
                            }
                        })
                        .on('apply.daterangepicker', function (ev, picker) {
                            var tempStartDate = picker.startDate.format('MMM DD, YYYY');
                            var tempEndDate = picker.endDate.format('MMM DD, YYYY');
                            var tempFormat = tempStartDate + ' - ' + tempEndDate;
                            $(currentElement).find("#date-range").val(tempFormat);
                        });
                }, 500);
            } else {
                _this.renderSelect2Picker();
            }

            return _this;
        }, renderSelect2Picker: function () {
            var _this = this;
            var currentElement = _this.$el;
            var tempModal = $(currentElement).closest(".modal");
            setTimeout(function () {
                $(currentElement).find("select[name='filter_month']")
                    .select2({
                        width: '100%',
                        data: months,
                        placeholder: "SELECT MONTH",
                        allowClear: true,
                        dropdownParent: tempModal,
                    });

                $(currentElement).find("select[name='filter_year']")
                    .select2({
                        width: '100%',
                        placeholder: "SELECT YEAR",
                        allowClear: true,
                        dropdownParent: tempModal,
                        ajax: {
                            url: baseUrl('payroll/get_posted_payroll_sheet_years'),
                            dataType: 'JSON',
                            type: 'GET',
                            global: false,
                        }, language: { errorLoading: function () { return "Searching..." } }
                    });

                $(currentElement).find("select#employee")
                    .select2({
                        width: '100%',
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                        ajax: {
                            url: baseUrl('payroll/select_employee'),
                            dataType: 'json',
                            global: false,
                            delay: 250,
                            data: function (params) {
                                params.company_ids = _this.company_ids;
                                params.q = params.term;
                                return params;
                            },
                            processResults: function (data) {
                                return data;
                            }
                        }, language: { errorLoading: function () { return "Searching..." } }
                    });

                $(currentElement).find("select#company")
                    .select2({
                        width: '100%',
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                        ajax: {
                            url: baseUrl('payroll/select_company'),
                            dataType: 'json',
                            global: false,
                            delay: 250,
                            processResults: function (data) {
                                return data;
                            },
                        }, language: { errorLoading: function () { return "Searching..." } }
                    }).on("select2:select", function (e) {
                        var _thisSelect2 = this;
                        var selectedValues = $(_thisSelect2).select2("val");
                        _this.company_ids = selectedValues;
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");
                    }).on("select2:unselect", function (e) {
                        var _thisSelect2 = this;
                        var selectedValues = $(_thisSelect2).select2("val");
                        _this.company_ids = selectedValues;
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");
                    });
            }, 200);
        }, toggleCheckbox: function (e) {
            var _this = this;
            _this.hide_fields = [];
            var checkboxInline = $(e.target).closest(".m-checkbox-inline");
            if (typeof checkboxInline !== "undefined") {
                var tempChecked = checkboxInline.find("input[type='checkbox']:checked");
                if (tempChecked.length == 1) {
                    tempChecked.prop("disabled", true);
                } else if (tempChecked.length > 1) {
                    tempChecked.prop("disabled", false);
                }
                var tempFields = [];
                $.each(tempChecked, function (i, v) {
                    var tempValue = $(v).val();
                    var tempIndex = _tempFields[tempValue];
                    tempFields.push(tempIndex);
                });
                $.each(_tempFields, function (ii, vv) {
                    if (jQuery.inArray(vv, tempFields) == -1) { _this.hide_fields.push(vv); }
                });
                _this.updateVisibleFields();
                return _this;
            }
        }, resetFields: function () {
            var _this = this;
            var currentForm = $(_this.$el).find("#frm-remittance-report");
            if (typeof currentForm !== "undefined") {
                currentForm.find("input[name=group]")[0].click();
                /*** currentForm.find("select").val("").trigger("change"); ***/
                currentForm.find("select[multiple]").val([]).trigger("change");
                currentForm[0].reset();
            }
        },
    }, mounted: function () {
        var _this = this;
        _this.renderSelect2Picker();
        _this.updateVisibleFields();
    }
});

let tempColumns = [
    { data: 'employee_name', width: "25%", className: "print-size-25" },
    { data: 'company_description', width: "*" },
    {
        data: 'basic_rate', width: "8%", className: "text-right print-size-10", defaultContent: "0.00", render: function (data) {
            return numberFormat(data);
        }
    }, {
        data: 'gross_pay', width: "8%", className: "text-right print-size-10", defaultContent: "0.00", render: function (data) {
            return numberFormat(data);
        }
    },
    { data: 'adjustment_1_description', width: "8%", className: "text-right print-size-10", defaultContent: "0.00", visible: false },
    { data: 'adjustment_2_description', width: "8%", className: "text-right print-size-10", defaultContent: "0.00", visible: false },
    { data: 'adjustment_3_description', width: "8%", className: "text-right print-size-10", defaultContent: "0.00", visible: false },
    { data: 'adjustment_4_description', width: "8%", className: "text-right print-size-10", defaultContent: "0.00", visible: false },
    { data: 'adjustment_5_description', width: "8%", className: "text-right print-size-10", defaultContent: "0.00", visible: false },
    { data: 'sss', width: "8%", className: "text-right print-size-10" },
    { data: 'hdmf', width: "8%", className: "text-right print-size-10" },
    { data: 'ph', width: "8%", className: "text-right print-size-10" },
    {
        data: null, width: "8%", className: "text-right print-size-10", render: function (data, type, row, meta) {
            let basicRate = parseFloat(row.basic_rate);
            var tempTotal = 0;
            var tempFields = row.visible_fields;
            if (tempFields.length > 0) {
                var arrAdjustmentTotal = [];
                $.each(tempFields, function (i, v) {
                    if (v !== "tax") {
                        var tempNumber = toNumber(row[v]);
                        tempNumber = parseFloat(tempNumber);
                        tempTotal += tempNumber;
                        if ($.inArray("adjustment", tempFields) !== -1) {
                            var tempAdjustmentTotal = 0;
                            for (ii = 1; ii <= parseInt(row.adjustment_columns); ii++) {
                                var tempValue = row["adjustment_" + ii + "_value"];
                                tempAdjustmentTotal += tempValue;
                            }
                            if ($.inArray(tempAdjustmentTotal, arrAdjustmentTotal) == -1) {
                                arrAdjustmentTotal.push(tempAdjustmentTotal);
                                tempTotal += tempAdjustmentTotal;
                            }
                        }
                    }
                });
            }
            tempTotal = basicRate - tempTotal;
            _totalTaxable += tempTotal;
            tempTotal = numberFormat(tempTotal);
            return "<span class='m--font-boldest'>" + tempTotal + "</span>";
        }
    },
    { data: 'tax', width: "8%", className: "text-right print-size-10" },
    {
        data: null, width: "10%", className: "text-right print-size-10", defaultContent: "0.00",
        render: function (data, type, row, meta) {
            var tempTotal = 0;
            var tempFields = row.visible_fields;
            if (tempFields.length > 0) {
                var arrAdjustmentTotal = [];
                $.each(tempFields, function (i, v) {
                    var tempNumber = toNumber(row[v]);
                    tempNumber = parseFloat(tempNumber);
                    tempTotal += tempNumber;
                    if ($.inArray("adjustment", tempFields) !== -1) {
                        var tempAdjustmentTotal = 0;
                        for (ii = 1; ii <= parseInt(row.adjustment_columns); ii++) {
                            var tempValue = row["adjustment_" + ii + "_value"];
                            tempAdjustmentTotal += tempValue;
                        }
                        if ($.inArray(tempAdjustmentTotal, arrAdjustmentTotal) == -1) {
                            arrAdjustmentTotal.push(tempAdjustmentTotal);
                            tempTotal += tempAdjustmentTotal;
                        }
                    }
                });
            }
            tempTotal = numberFormat(tempTotal);
            return "<span class='m--font-boldest'>" + tempTotal + "</span>";
        }
    },
];

let dtRemittances = $('#tbl-remittances').DataTable({
    dom: "<'row'<'col-md-10 dtDetails'><'col-md-2 dtActions m--hide'B>>rt",
    serverSide: true,
    processing: true,
    destroy: true,
    ordering: false,
    paging: false,
    buttons: [
        {
            extend: 'print',
            footer: true,
            text: '<i class="fa fa-print"></i><span class="m--font-boldest">PRINT</span>',
            className: "pull-right printRemittanceAction btnPrint",
            title: function () {
                let _comp = ``;
                if (typeof _tempFilter.companies == "object" && _tempFilter.companies.length > 0) {
                    var _tempComp = _tempFilter.companies;
                    _tempComp = _tempComp.join(" | ");
                    _comp += `<div class="m--regular-font-size-sm1 mt-1">COMPANY : ${_tempComp} </div>`;
                }

                return `<div class="m--regular-font-size-lg1">CONTRIBUTION / DEDUCTION REPORT</div>
                        <div class="m--regular-font-size-sm1 mt-2">FILTERED BY : ${_tempFilter.filter_by}</div>
                        <div class="m--regular-font-size-sm1 mt-1">PAY COVERAGE : ${_tempFilter.coverage_date}</div>
                        ${_comp}`;
            }, customize: function (win) {
                var last = null;
                var current = null;
                var bod = [];

                var css = `@page { size: landscape; margin: 0.5cm; }
                    table { font-size: 10px; }
                    .print-size-10{ width: 10% }
                    .print-size-25{ width: 25% }`,
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
                win.document.title = "Remittances Printable Page";
                $(win.document.body).find('tfoot')[0].innerHTML = _globalFooterHtml;
            }, exportOptions: {
                columns: ':visible',
                stripHtml: false,
            }
        },
    ], ajax: {
        url: baseUrl('payroll/reports/get_remittance_report_request'),
        type: 'POST',
        dataType: 'JSON',
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.ids = _tempIds;
            d.visible_fields = _visibleFields;
            d.clear_table = _clearTable;
        }, error: function (xhr, error, code) {
            if (error == "parsererror") { dtRemittances.ajax.reload(); }
        },
    }, columns: tempColumns,
    drawCallback: function (settings) {
        var api = this.api();
        var btnPrint = $(settings.nTableWrapper).find(".printRemittanceAction");
        var dtActions = $(settings.nTableWrapper).find(".dtActions");
        var dtDetails = $(settings.nTableWrapper).find(".dtDetails");
        if (typeof btnPrint !== "undefined" && typeof dtActions !== "undefined") {
            btnPrint.addClass("btn m-btn btn-brand m-btn--icon m--hide animated fadeIn");
            var tempData = api.data();
            if (tempData.length > 0) {
                if (btnPrint.hasClass("m--hide") == true) { btnPrint.removeClass("m--hide"); }
                if (dtActions.hasClass("m--hide") == true) { dtActions.removeClass("m--hide"); }
            } else {
                if (dtActions.hasClass("m--hide") == false) { dtActions.addClass("m--hide"); }
                if (btnPrint.hasClass("m--hide") == false) { btnPrint.addClass("m--hide"); }
            }
        }
        if (typeof dtDetails !== "undefined") {
            dtDetails.empty();
            if (typeof _tempFilter == "object" && Object.keys(_tempFilter).length > 0) {
                var tempHtmlCompany = "";
                if (typeof _tempFilter.companies == "object" && _tempFilter.companies.length > 0) {
                    var _arrCompanies = "";
                    $.each(_tempFilter.companies, function (i, v) {
                        _arrCompanies += `<span class="m-badge m-badge--metal m-badge--wide m-badge--rounded m--margin-right-5 m--font-bolder">${v}</span>`;
                    });
                    tempHtmlCompany += `<div class='row'>
                        <div class='col-12 col-md-2'>
                            <span class='m--font-bolder'>COMPANY :</span>
                        </div>
                        <div class='col-12 col-md-10'>${_arrCompanies}</div>
                    </div>`;
                } else {
                    tempHtmlCompany += "&nbsp;";
                }

                var tempHtml = `<div class='row'>
                    <div class='col-12 col-md-5'>
                        <div class='row'>
                            <div class='col-12 col-md-4'>
                            <span class='m--font-bolder'>FILTERED BY :</span>
                            </div>
                            <div class='col-12 col-md-8'>
                                <span class='m--font-bolder'>${_tempFilter.filter_by}</span>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-12 col-md-4'>
                            <span class='m--font-bolder'>COVERAGE DATE :</span>
                            </div>
                            <div class='col-12 col-md-8'>
                                <span class='m--font-bolder'>${_tempFilter.coverage_date}</span>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-md-7'>${tempHtmlCompany}</div>
                </div>`;
                dtDetails.html(tempHtml);
            }
        }

        var tempFooter = $(settings.nTableWrapper).find("tfoot");
        if (typeof tempFooter !== "undefined") { _globalFooterHtml = tempFooter[0].innerHTML; }
    },
    footerCallback: function (row, data, start, end, display) {
        var api = this.api(), data;
        // Remove the formatting to get integer data for summation
        var intVal = function (i) {
            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
        };

        let totalRegPay = api
            .column(2)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalGrossPay = api
            .column(3)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let tempData = api.rows().data();
        let totalAdj1 = 0;
        $.each(tempData, function (i, v) {
            totalAdj1 += v.adjustment_1_value;
        });

        let totalAdj2 = 0;
        $.each(tempData, function (i, v) {
            totalAdj2 += v.adjustment_2_value;
        });

        let totalAdj3 = 0;
        $.each(tempData, function (i, v) {
            totalAdj3 += v.adjustment_3_value;
        });

        let totalAdj4 = 0;
        $.each(tempData, function (i, v) {
            totalAdj4 += v.adjustment_4_value;
        });

        let totalAdj5 = 0;
        $.each(tempData, function (i, v) {
            totalAdj5 += v.adjustment_5_value;
        });

        let totalSSS = api
            .column(9)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalHDMF = api
            .column(10)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalPH = api
            .column(11)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalTaxable = 0;
        $.each(tempData, function (i, vv) {
            var tempTotal = 0;
            var tempFields = vv.visible_fields;
            if (tempFields.length > 0) {
                var arrAdjustmentTotal = [];
                $.each(tempFields, function (i, v) {
                    var tempNumber = toNumber(vv[v]);
                    tempNumber = parseFloat(tempNumber);
                    tempTotal += tempNumber;
                    if ($.inArray("adjustment", tempFields) !== -1) {
                        var tempAdjustmentTotal = 0;
                        for (ii = 1; ii <= parseInt(vv.adjustment_columns); ii++) {
                            var tempValue = vv["adjustment_" + ii + "_value"];
                            tempAdjustmentTotal += tempValue;
                        }
                        if ($.inArray(tempAdjustmentTotal, arrAdjustmentTotal) == -1) {
                            arrAdjustmentTotal.push(tempAdjustmentTotal);
                            tempTotal += tempAdjustmentTotal;
                        }
                    }
                });
            }
            let tempTaxable = vv.basic_rate - tempTotal;
            totalTaxable += tempTaxable;
        });

        let totalTAX = api
            .column(13)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        $(api.column(2).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalRegPay) + "</span>");
        $(api.column(3).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalGrossPay) + "</span>");
        $(api.column(4).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalAdj1) + "</span>");
        $(api.column(5).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalAdj2) + "</span>");
        $(api.column(6).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalAdj3) + "</span>");
        $(api.column(7).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalAdj4) + "</span>");
        $(api.column(8).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalAdj5) + "</span>");
        $(api.column(9).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalSSS) + "</span>");
        $(api.column(10).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalHDMF) + "</span>");
        $(api.column(11).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalPH) + "</span>");
        $(api.column(12).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalTaxable) + "</span>");
        $(api.column(13).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalTAX) + "</span>");
    }
});

$.validate({
    form: "#frm-remittance-report",
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        var currentForm = form[0];
        var formUrl = currentForm.action;
        var formData = $(currentForm).serialize();
        getScriptRendering(formUrl, formData, currentForm);
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
        success: function (json) {
            _tempFilter = Object.assign({});
            var tempHideFields = [];
            if (json.response) {
                dtRemittances.columns().visible(true);
                _clearTable = false;
                _tempIds = json.data;
                _visibleFields = json.visible_fields;
                _tempFilter = Object.assign({}, json.filters);
                tempHideFields = vmGenerateRemittance.hide_fields;
                if (tempHideFields.length > 0) {
                    dtRemittances.columns(tempHideFields).visible(false, false);
                }
                toastr.success(json.toastr_msg, "Filtered Remittances");
            } else {
                _clearTable = true;
                toastr.error(json.toastr_msg, "Filtered Remittances");
            }

            dtRemittances.columns.adjust();
            dtRemittances.ajax.reload();
            setTimeout(function () {
                const indexes = [4, 5, 6, 7, 8];
                const tempJson = dtRemittances.settings().ajax.json();
                if (typeof tempJson.show_adjustments !== "undefined" && tempJson.show_adjustments == true) {
                    const tempCounter = parseInt(tempJson.adjustment_columns);
                    if (tempCounter > 0) {
                        for (ii = tempCounter; ii < indexes.length; ii++) {
                            if ($.inArray(indexes[ii], tempHideFields) == -1) {
                                tempHideFields.push(indexes[ii]);
                            }
                        }
                    }
                } else {
                    for (ii = 0; ii < indexes.length; ii++) {
                        if ($.inArray(indexes[ii], tempHideFields) == -1) {
                            tempHideFields.push(indexes[ii]);
                        }
                    }
                }
                dtRemittances.columns().visible(true);
                dtRemittances.columns(tempHideFields).visible(false, false);
                dtRemittances.columns.adjust();
                dtRemittances.ajax.reload();
            }, 500);

            $(currentForm)
                .find(".btn-submit")
                .removeClass(
                    "m-btn--custom m-loader m-loader--light m-loader--right"
                ).prop("disabled", true);
        }, error: function (xhr, error, code) {
            if (error == "parsererror") {
                getScriptRendering(formUrl, formData, currentForm);
            }
        }
    });
}
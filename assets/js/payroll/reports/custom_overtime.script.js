let globalPrintableSignatory = [];
const psSignatoryModal = $("#modal-ps--signatory");
const psResetSignatoryModal = $("#modal-ps--reset-signatory");
const modalGenerateReport = $("#generate-report-modal");

let _years = [];
let _companies = [];
let psEmployeeGroup = [];

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

let _clearTable = true;
let _tempFilter = {};
let _globalFooterHtml = null;
let _totalTaxable = 0;
let _tempData = {
    show_by_date: true, show_picker: false,
    year_picker: false, month_picker: false, company_ids: 0,
};
let dtOTSummary = null;

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.years !== "undefined" && _tempContentData.years.length > 0){
        _years = _tempContentData.years;
    }
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){
        _companies = _tempContentData.company;
    }
}

const vmGeneratejournal = new Vue({
    el: "#generate-journal_content",
    data: _tempData,
    methods: {
        tempShowByDates: function (id) {
            const _this = this;
            let currentElement = _this.$el;
            _this.show_by_date = (id == 1);
            _this.month_picker = (id == 2);
            _this.year_picker = (id == 3);
            _this.show_picker = false;
            if (id == 1) {
                const filterDateRange = $(currentElement).find("#filter_date_range");
                if (typeof filterDateRange !== "undefined" && filterDateRange.length == 1) {
                    filterDateRange.on("change", function () {
                        const thisFilter = this;
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
            const _this = this;
            const currentElement = _this.$el;
            _this.show_picker = !_this.show_picker;
            if (_this.show_picker === true) {
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
                        const tempStartDate = picker.startDate.format('MMM DD, YYYY');
                        const tempEndDate = picker.endDate.format('MMM DD, YYYY');
                        const tempFormat = tempStartDate + ' - ' + tempEndDate;
                        $(currentElement).find("#date-range").val(tempFormat);
                    });
            } else {
                _this.renderSelect2Picker();
            }

            return _this;
        }, renderSelect2Picker: function () {
            const _this = this;
            const currentElement = _this.$el;
            const tempModal = $(currentElement).closest(".modal");
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
                        data: _years,
                        width: '100%',
                        placeholder: "SELECT YEAR",
                        allowClear: true,
                        dropdownParent: tempModal,
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
                                /*** params.company_ids = _this.company_ids; ***/
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
                        allowClear: true,
                        width: '100%',
                        data: _companies,
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                    }).on("select2:select", function (e) {
                        const _thisSelect2 = this;
                        const selectedValues = $(_thisSelect2).select2("val");
                        _this.company_ids = selectedValues;
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");
                    }).on("select2:unselect", function (e) {
                        const _thisSelect2 = this;
                        const selectedValues = $(_thisSelect2).select2("val");
                        _this.company_ids = selectedValues;
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");
                    });
            }, 200);
        }, resetFields: function () {
            const _this = this;
            const currentForm = $(_this.$el).find("#frm-journal-report");
            if (typeof currentForm !== "undefined") {
                _this.company_ids = 0;
                psEmployeeGroup = [];

                currentForm.find("input[name=group]")[0].click();
                currentForm.find("select").val("").trigger("change");
                const select2Multiple = currentForm.find("select[multiple]");
                if(typeof select2Multiple != "undefined" && select2Multiple.length > 0){
                    select2Multiple.prop("disabled", false);
                    setTimeout(function(){ 
                        currentForm[0].reset(); 
                        setTimeout(function(){
                            select2Multiple.val([]);
                            select2Multiple.trigger("change");
                        }, 250);
                    }, 750);
                }
            }
        },
    }, mounted: function () {
        const _this = this;
        _this.renderSelect2Picker();
    }
});


const vmReportHeaders = new Vue({
    el: "#report-header",
    data: { show_header: false, filters: {} }
});

const vmActionSignatories = new Vue({
    el: "#actionSignatories",
    data: { show_signatories: false, signatories: {} },
    methods: {
        editSignatories: function () {
            return psSignatoryModal.modal("show");
        }, resetSignatories: function () {
            return psResetSignatoryModal.modal("show");
        }
    }
});

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
            params.company_id = $("form#frm-journal-report select#company").val();
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
                        const tempEmployeeSelector = $("form#frm-journal-report select#employee");
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
    } else if (typeof employees == "object" && typeof employees !== "undefined") {
        const tempEmployeeSelector = $("form#frm-journal-report select#employee");
        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
            tempEmployeeSelector.empty();
            $.each(employees, function (ii, vv) {
                const tempOption = new Option(vv.text, vv.id, true, true);
                tempEmployeeSelector.append(tempOption);
            });
            tempEmployeeSelector.prop("disabled", true);
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
        const tempEmployeeSelector = $("form#frm-journal-report select#employee");
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
                        const tempEmployeeSelector = $("form#frm-journal-report select#employee");
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

$(document).ready(function(){
    dtOTSummary = $('#tbl-overtime-summary').DataTable({
        dom: "rt",
        serverSide: true,
        processing: true,
        destroy: true,
        paging: false,
        searching: false,
        ordering: false,
        footer: true,
        ajax: {
            url: baseUrl('payroll/reports/get_custom_overtime_summary'),
            type: 'POST',
            dataType: 'JSON',
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.ids = _tempIds;
                d.clear_table = _clearTable;
                d.filters = _tempFilter;
            }, 
        }, buttons: [{
            extend: 'excel',
            footer: true,
            customize: function (xlsx) {
                const sheet = xlsx.xl.worksheets['sheet1.xml'];

                let numrows = $('row', sheet).length;
                let mergeCells = $('mergeCells', sheet);
                mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                    attr: { ref: 'A' + numrows + ':' + 'F' + numrows },
                }));

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
            }
        }], columns: [
            { visible: false, data: 'employee_name' },
            { data: 'overtime_in', width: '10%' },
            { data: 'day', width: '8%', className: "text-center" },
            { data: 'daily_rate', width: '6%', className: "text-right",
                render: function(data, type, row){
                    return '₱ '+data;
                }
            }, { data: 'allowance', width: '9%', className: "text-right", 
                render: function (data) {
                    if (data && parseFloat(data) > 0) {
                        const allw = parseFloat(data);
                        return '₱ '+ allw.toFixed(2);
                    } else { return '-'; }
                }
            }, { data: 'ot_hrs', width: '5%', className: "text-right",
                render: function (data, type, row) {
                    if (data && parseFloat(data) > 0) {
                        const ot_hrs = parseFloat(data);
                        return ot_hrs.toFixed(2);
                    }
                    return data;
                }
            }, { data: 'ot_pay', width: '8%', className: "text-right",
                render: function (data) {
                    if (data && parseFloat(data) > 0) {
                        let ot_pay = parseFloat(data);
                        return '₱ '+ot_pay.toFixed(2);
                    }else{ return '-'; }
                }
            }, { data: 'ot_pay_20', className: "text-right", width: '8%', 
                render: function (data) {
                    if (data && parseFloat(data) > 0) {
                        let ot_pay_20 = parseFloat(data);
                        return '₱ '+ot_pay_20.toFixed(2);
                    }else{ return '-'; }
                }
            }, { data: 'ot_pay_30', className: "text-right", width: '8%', 
                render: function (data) {
                    if (data && parseFloat(data) > 0) {
                        let ot_pay_30 = parseFloat(data);
                        return '₱ '+ot_pay_30.toFixed(2);
                    }else{ return '-'; }
                }
            }, { data: 'ot_ndiff_hrs', width: '8%', className: "text-right",
                render: function (data) {
                    if (data && parseFloat(data) > 0) {
                        const ot_ndiff_hrs = parseFloat(data);
                        return ot_ndiff_hrs.toFixed(2);
                    }
                    return data;
                }
            }, { data: 'night_diff', className: "text-right", width: '8%', 
                render: function (data) {
                    if (data && parseFloat(data) > 0) {
                        const ot_ndiff_pay = parseFloat(data);
                        return '₱ '+ot_ndiff_pay.toFixed(2);
                    } else { return '-'; }
                }
            },
            { data: 'ot_allowance', className: "text-right", width: '8%', 
                render: function (data) {
                    if (data && parseFloat(data) > 0) {
                        const ot_allowance = parseFloat(data);
                        return '₱ '+ot_allowance.toFixed(2);
                    } else { return '-'; }
                }
            },
            { data: null, className: "text-right", width: '5%', 
                render: function () { return '-'; }
            }, 
            { data: 'amount', className: "text-right pr-3", width: '10%', 
                render: function (data, type, row) {
                    if (data && parseFloat(data) > 0) {
                        const amount = parseFloat(data);
                        return '₱ '+amount.toFixed(2);
                    }
                    return data;
                }
            },
            { data: 'ot_details', width: '5%', className: "text-center", 
                render: function (data) { 
                    let html = '';

                    let status = 'fa-times-circle text-danger';
                    let title = 'No Overtime Request Found.';

                    if (data) {
                        status = data.status.toLowerCase() == 'approved' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger';
                        title = `Status: ${data.status.toUpperCase()}&#013;Ref #: ${data.reference_no}&#013;Created at: ${moment(data.created_at).format('LL')}`;
                    }

                    html = `<span class="fa ${status}" style="font-size: 18px" title="${title}"></span>`;
                    return html;
                }
            },
            { data: 'is_paid', width: '5%', className: "text-center",
                render: function (data) {
                    let html = '';
                    const status = data && parseInt(data) == 1 ? 'fa-check-circle text-success' : 'fa-times-circle text-danger';
                    const title = data && parseInt(data) == 1 ? 'Paid' : 'Unpaid';
                    html = `<span class="fa ${status}" style="font-size: 18px" title="${title}"></span>`;
                    return html;
                }
            },
        ], rowGroup: {
            startRender: function ( _rows, group ) {
                return $('<tr><td colspan="15" class="bg-secondary"><span class="m--font-boldest">' + group + '</span></td></tr>');
            },
            endRender: function ( rows, _group ) {
                let OTadj = rows
                    .data()
                    .pluck('ot_adj')
                    .reduce( function (a, b) {
                        return b ? toNumber(b) : 0.00;
                    }, 0);
                
                let totalAmount = rows
                .data()
                .pluck('amount')
                .reduce( function (a, b) {
                    let totalOTAmount = parseFloat(a) + parseFloat(b);
                    return toNumber(totalOTAmount);
                }, 0);
                    
                let total = parseFloat(totalAmount) + parseFloat(OTadj);
                const uiAdjustmentAmount = parseFloat(OTadj) > 0 ? `<span class="m--font-boldest">₱ ${numberFormat(OTadj)}</span>`: `-`;
                const uiTotal = `<strong>₱ ${numberFormat(total)}</strong>`;

                const tempContainer = `<tr class="bg-secondary">
                    <td colspan="11" class="text-right">&nbsp;</td>
                    <td class="text-right">${uiAdjustmentAmount}</td>
                    <td class="text-right pr-3">${uiTotal}</td>
                    <td colspan="2">&nbsp;</td>
                    </tr>`;

                return $(tempContainer);
            },
            dataSrc: [ 'employee_name' ],
            
        }, drawCallback: function () {
            const api = this.api();
            const tempData = api.data();
            const _dtActions = $("#table-actions");
            const hasRowData = tempData.length > 0;
            if (_dtActions.hasClass("m--hide") === false) { _dtActions.addClass("m--hide"); }
            if (hasRowData && typeof _dtActions !== "undefined" && _dtActions.length == 1 && _dtActions.hasClass("m--hide") === true) {
                _dtActions.removeClass("m--hide");
            }
            vmReportHeaders.show_header = hasRowData;

        }, footerCallback: function () {
            const api = this.api();
            const tempData = api.data();
            let arrAdjustments = {};
            if(tempData.length > 0) {
                $.each(tempData, function (_i, row) {
                    if (row.ot_adj && parseFloat(row.ot_adj) > 0) {
                        if(jQuery.isEmptyObject(arrAdjustments[row.emp_id])) { arrAdjustments[row.emp_id] = []; }
                        if(jQuery.inArray(row.ot_adj, arrAdjustments[row.emp_id]) == -1) { arrAdjustments[row.emp_id].push(row.ot_adj); }
                    }
                });
            }

            let totalAdjustmentAmount = 0;
            if(Object.keys(arrAdjustments).length > 0) {
                $.each(arrAdjustments, function (_i, adjAmount) {
                    totalAdjustmentAmount += parseFloat(adjAmount);
                });
            }
            
            const intVal = function (i) {
                if (typeof i === 'string') {
                    return parseFloat(i.replace(/[^0-9.-]/g, '').trim()) || 0;
                }
                return typeof i === 'number' ? i : 0;
            };

            const otPayTotalIndex = 6;
            const otPay20TotalIndex = 7;
            const otPay30TotalIndex = 8;
            const nDiffTotalIndex = 10;
            const otAllowanceIndex = 11;
            const adjustmentIndex = 12;
            const grandTotalIndex = 13;

            let otPayTotalAmount = api.column(otPayTotalIndex).data().reduce(function (a, b) { return parseFloat(intVal(a).toFixed(2)) + parseFloat(intVal(b).toFixed(2)); }, 0);
            let otPay20TotalAmount = api.column(otPay20TotalIndex).data().reduce(function (a, b) { return parseFloat(intVal(a).toFixed(2)) + parseFloat(intVal(b).toFixed(2)); }, 0);
            let otPay30TotalAmount = api.column(otPay30TotalIndex).data().reduce(function (a, b) { return parseFloat(intVal(a).toFixed(2)) + parseFloat(intVal(b).toFixed(2)); }, 0);
            let nDiffTotalAmount = api.column(nDiffTotalIndex).data().reduce(function (a, b) { return parseFloat(intVal(a).toFixed(2)) + parseFloat(intVal(b).toFixed(2)); }, 0);
            let otAllowanceAmount = api.column(otAllowanceIndex).data().reduce(function (a, b) { return parseFloat(intVal(a).toFixed(2)) + parseFloat(intVal(b).toFixed(2)); }, 0);
            let totalAmount = api.column(grandTotalIndex).data().reduce(function (a, b) { return parseFloat(intVal(a).toFixed(2)) + parseFloat(intVal(b).toFixed(2)); }, 0);

            const grandTotalAmount = parseFloat(totalAmount) + parseFloat(totalAdjustmentAmount);
            const footerLabelTotal = $(api.column(5).footer());
            footerLabelTotal.removeClass("text-center");
            footerLabelTotal.html(`<span class="m--font-boldest mr-3">GRAND TOTAL</span>`);

            $(api.column(otPayTotalIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(otPayTotalAmount) + "</span>");
            $(api.column(otPay20TotalIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(otPay20TotalAmount) + "</span>");
            $(api.column(otPay30TotalIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(otPay30TotalAmount) + "</span>");
            $(api.column(nDiffTotalIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(nDiffTotalAmount) + "</span>");
            $(api.column(otAllowanceIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(otAllowanceAmount) + "</span>");
            $(api.column(adjustmentIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalAdjustmentAmount) + "</span>");
            $(api.column(grandTotalIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(grandTotalAmount) + "</span>");
        }, createdRow: function (rowEl, rowData, _index) {
            const isPaid = rowData.is_paid && parseInt(rowData.is_paid) == 1 ? true : false;

            if (!isPaid) { $(rowEl).addClass('unpaid-ot'); }
        }
    });

    $.validate({
        form: "#frm-journal-report",
        lang: "en",
        scrollToTopOnError: false,
        onSuccess: function (form) {
            let propDisabled = false;
            
            const currentForm = form[0];
            const formUrl = currentForm.action;

            const tempEmployeeFilter = $(currentForm).find("select#employee");
            if(typeof tempEmployeeFilter !== "undefined"){
                propDisabled = tempEmployeeFilter.is(":disabled");
                if(propDisabled){ tempEmployeeFilter.prop("disabled", false); }
            }
            
            let formData = $(currentForm).serialize();
            if(propDisabled){ tempEmployeeFilter.prop("disabled", true); }
            
            getScriptRendering(formUrl, formData, currentForm);
            return false;
        }
    });
    
    const getScriptRendering = function (formUrl, formData, currentForm) {
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
                _tempFilter = {};
                vmActionSignatories.show_signatories = false;
                
                if (json.response) {
                    _clearTable = false;
                    _tempIds = json.data;
                    _tempFilter = { ...json.filters };
                    vmReportHeaders.filters = { ...json.filters };
                    vmActionSignatories.show_signatories = true;
                    toastr.success(json.toastr_msg, "Filtered Overtime Summary Report");

                    setTimeout( function () { modalGenerateReport.modal("hide"); }, 750);
                } else {
                    _clearTable = true;
                    toastr.error(json.toastr_msg, "Filtered Overtime Summary Report");
                }
    
                dtOTSummary.ajax.reload();
                const currentSelectCompanyId = $(currentForm).find("#company").val();
                if (typeof currentSelectCompanyId !== "undefined" && parseInt(currentSelectCompanyId) > 0) {
                    $.ajax({
                        url: siteUrl("payroll/reports/get_current_signatory_by_company_and_type/" + currentSelectCompanyId + "/2"),
                        global: false,
                        dataType: "json",
                        success: function (json) {
                            let tempRow = {};
                            let ctr = json.count ? json.count : 0;
                            if (json.response) { tempRow = { ...json.data }; }
                            
                            vmActionSignatories.signatories = { ...tempRow };
                            vmTempSignatory.row = { ...tempRow };
                            vmTempSignatory.count = ctr;
                            vmTempSignatory.$mount();

                            vmPortletSignatories.row = { ...tempRow };
                            vmPortletSignatories.count = ctr;
                        }
                    });
                }
            }
        });
    }
});

const exportExcel = function(){
    dtOTSummary.button(".buttons-excel").trigger();
}

const vmPortletSignatories = new Vue({
    el: "#portlet--signatories",
    data: { row: {}, count: 0 }
});

const vmTempSignatory = new Vue({
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
                        if (select2Container.is(":disabled") === true) {
                            select2Container.prop("disabled", false);
                        }
                    } else {
                        if (select2Container.is(":disabled") === false) {
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

                                        _this.row = { ...currentData };
                                        _this.count = ctr;
                                        _this.setGlobalSignatories();

                                        vmPortletSignatories.row = { ...currentData };
                                        vmPortletSignatories.count = ctr;

                                        vmResetSignatories.row = { ...currentData };
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

const vmResetSignatories = new Vue({
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
                                let tempRow = {};
                                let ctr = 0;

                                if (json.response) {
                                    tempRow = { ...json.data };
                                    ctr = json.count;
                                }
                                vmTempSignatory.row = { ...tempRow };
                                vmTempSignatory.count = ctr;
                                vmTempSignatory.$mount();

                                vmPortletSignatories.row = { ...tempRow };
                                vmPortletSignatories.count = ctr;

                                _this.row = { ...tempRow };
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

const initSelect2Employee = function (tempModal, portlet) {
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
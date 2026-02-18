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
let dtPayrollTable = null;

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

                        const self = $(currentElement).find("#date-range");
                        self.validate();
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
                    }).on("select2:select", function (e) {
                        const self = $(this);
                        self.validate();
                    });

                $(currentElement).find("select[name='filter_year']")
                    .select2({
                        data: _years,
                        width: '100%',
                        placeholder: "SELECT YEAR",
                        allowClear: true,
                        dropdownParent: tempModal,
                    }).on("select2:select", function (e) {
                        const self = $(this);
                        self.validate();
                    });

                $(currentElement).find("select#employee")
                    .select2({
                        width: '100%',
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                        ajax: {
                            url: baseUrl('payroll/select_employee_by_company'),
                            dataType: 'json',
                            global: false,
                            delay: 250,
                            data: function (params) {
                                params.company_ids = [$(currentElement).find("select#company").val()] || 0;
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
                        
                        const self = $(this);
                        self.validate();
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
    dtPayrollTable = $('#tbl-payrollsheet-summary').DataTable({
        dom: "rt",
        serverSide: false,
        processing: false,
        destroy: true,
        paging: false,
        searching: true,
        ordering: false,
        footer: true,
        buttons: [{
            extend: 'excel',
            footer: true,
            customize: function (xlsx) {
                const sheet = xlsx.xl.worksheets['sheet1.xml'];
                let styles = xlsx.xl['styles.xml'];

                let fonts = $('fonts', styles);
                let boldFontIndex = $('font', fonts).length;

                fonts.append(`
                    <font>
                        <b/>
                        <sz val="11"/>
                        <name val="Calibri"/>
                    </font>
                `);

                fonts.attr('count', boldFontIndex + 1);

                let cellXfs = $('cellXfs', styles);
                let newStyleIndex = $('xf', cellXfs).length;

                cellXfs.append(`
                    <xf numFmtId="0" fontId="${boldFontIndex}" fillId="0" borderId="0" xfId="0" applyAlignment="1">
                        <alignment horizontal="right"/>
                    </xf>
                `);
                cellXfs.attr('count', newStyleIndex + 1);

                let numrows = $('row', sheet).length;
                let mergeCells = $('mergeCells', sheet);
                mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                    attr: { ref: 'A' + numrows + ':' + 'I' + numrows },
                }));

                $('row c[r^="A' + numrows + '"]', sheet).attr('s', newStyleIndex);

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
            { data: 'date', width: '10%', className: "text-center" },
            { data: 'day', width: '8%', className: "text-center" },
            { data: 'daily_rate', width: '6%', className: "text-center",
                render: function(data, type, row){
                    return '₱ '+numberFormat(data);
                }
            },
            { data: 'hourly_rate', width: '6%', className: "text-center",
                render: function(data, type, row){
                    return '₱ '+numberFormat(data);
                }
            },
            { data: 'total_no_of_hrs_worked', width: '6%', className: "text-center",
                render: function(data, type, row){
                    return data;
                }
            },
            { data: 'regular_total_no_of_hrs_worked', width: '6%', className: "text-center",
                render: function(data, type, row){
                    return data;
                }
            },
            { data: 'overtime_total_no_of_hrs_worked', width: '6%', className: "text-center",
                render: function(data, type, row){
                    return data;
                }
            },
            { data: 'ot_rate', width: '6%', className: "text-center",
                render: function(data, type, row){
                    return '₱ '+numberFormat(data);
                }
            },
            { data: 'ot_pay_25', width: '6%', className: "text-center",
                render: function(data, type, row){
                    return '₱ '+numberFormat(data);
                }
            },
            { data: 'regular_nd_hrs_worked', width: '6%', className: "text-center",
                render: function(data, type, row){
                    return data;
                }
            },
            { data: 'ot_ndiff_hrs_worked', width: '6%', className: "text-center",
                render: function(data, type, row){
                    return data;
                }
            },
            { data: 'regular_night_diff', width: '6%', className: "text-center",
                render: function(data, type, row){
                    return '₱ '+numberFormat(data);
                }
            },
            { data: 'ot_night_diff', width: '6%', className: "text-center",
                render: function(data, type, row){
                    return '₱ '+data;
                }
            },
            { data: 'amount_paid', width: '6%', className: "text-center",
                render: function(data, type, row){
                    return '₱ '+data;
                }
            },
            { data: 'total_pay', width: '6%', className: "text-center",
                render: function(data, type, row){
                    return '₱ '+data;
                }
            },
        ], rowGroup: {
            startRender: function ( _rows, group ) {
                return $('<tr><td colspan="15" class="bg-secondary"><span class="m--font-boldest">' + group + '</span></td></tr>');
            },
            endRender: function ( rows, _group ) {
                let totalAmount = rows
                .data()
                .pluck('total_pay')
                .reduce( function (a, b) {
                    let total_amount = parseFloat(a) + parseFloat(b);
                    return toNumber(total_amount);
                }, 0);
                    
                let total = parseFloat(totalAmount);
                const uiTotal = `<strong>₱ ${numberFormat(total)}</strong>`;

                const tempContainer = `<tr class="bg-secondary">
                    <td colspan="14" class="text-center">&nbsp;</td>
                    <td class="text-center pr-3">${uiTotal}</td>
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

            const _filter = $("#filter-table");
            if (_filter.hasClass("m--hide") === false) { _filter.addClass("m--hide"); }
            if (hasRowData && typeof _filter !== "undefined" && _filter.length == 1 && _filter.hasClass("m--hide") === true) {
                _filter.removeClass("m--hide");
            }

        }, footerCallback: function () {
            const api = this.api();
            const tempData = api.data();
            
            const intVal = function (i) {
                if (typeof i === 'string') {
                    return parseFloat(i.replace(/[^0-9.-]/g, '').trim()) || 0;
                }
                return typeof i === 'number' ? i : 0;
            };

            const otRateIndex = 8;
            const ot25RateIndex = 9;
            const regularNdHrsIndex = 10;
            const OtNdHrsIndex = 11;
            const regularNdIndex = 12;
            const otNdIndex = 13;
            const amountIndex = 14;
            const totalIndex = 15;

            const filteredRows = api.rows({ filter: 'applied' }).data();

            const sumColumn = (index) => filteredRows.reduce((a, b) => a + intVal(b[api.column(index).dataSrc()]), 0);

            let ot25RateAmount = sumColumn(ot25RateIndex);
            let regularNdHrsAmount = sumColumn(regularNdHrsIndex);
            let OtNdHrsAmount = sumColumn(OtNdHrsIndex);
            let regularNdAmount = sumColumn(regularNdIndex);
            let otNdAmount = sumColumn(otNdIndex);
            let amountAmount = sumColumn(amountIndex);

            const grandTotalAmount = sumColumn(parseFloat(totalIndex));
            const footerLabelTotal = $(api.column(8).footer());
            footerLabelTotal.removeClass("text-center");
            footerLabelTotal.html(`<span class="m--font-boldest mr-3">GRAND TOTAL</span>`);

            $(api.column(ot25RateIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(ot25RateAmount) + "</span>");
            $(api.column(regularNdHrsIndex).footer()).html("<span class='m--font-boldest'> "+ regularNdHrsAmount +" </span>");
            $(api.column(OtNdHrsIndex).footer()).html("<span class='m--font-boldest'> "+ OtNdHrsAmount +" </span>");
            $(api.column(regularNdIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(regularNdAmount) + "</span>");
            $(api.column(otNdIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(otNdAmount) + "</span>");
            $(api.column(amountIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(amountAmount) + "</span>");
            $(api.column(totalIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(grandTotalAmount) + "</span>");
        }
    });

    // for filtering by column without create new request or altering array
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'tbl-nightdiff-summary') {
            return true;
        }

        let checked = $('input[name="filter"]:checked')
            .map(function () { return this.value; })
            .get();

        if (!checked.length || checked.includes('all')) {
            return true;
        }

        let row = settings.aoData[dataIndex]._aData;
        let paid = parseInt(row.posted);

        return checked.some(val =>
            (val == 1      && paid === 1) ||
            (val == 0     && paid === 0)
        );
    });
    // for filtering by column without create new request or altering array

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

                if (json.data.length > 0) {
                    vmActionSignatories.show_signatories = true;
                    vmReportHeaders.filters = { ...json.filters };
                    toastr.success('Payroll Sheet entries found!', "Filtered Payroll Sheet Summary Report");

                    dtPayrollTable.clear().rows.add(json.data).draw();

                    setTimeout( function () { 
                        modalGenerateReport.modal("hide"); 
                    }, 750);
                } else {
                    toastr.error('No Payroll Sheet entries available!', "Filtered Payroll Sheet Summary Report");
                }
    
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

    $('input[name="filter"]').on('change', function () {
        dtPayrollTable.draw();
    });
});

const exportExcel = function(){
    dtPayrollTable.button(".buttons-excel").trigger();
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
let _tempIds = [];
let _years = [];
let _companies = [];

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

let _tempData = {
    month_picker: true,
    year_picker: true, 
    company_ids: [],
    visible_fields: [],
    include13th_month: false,
};

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.years !== "undefined" && _tempContentData.years.length > 0){
        _years = _tempContentData.years;
    }
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){
        _companies = _tempContentData.company;
    }
}

const vmGenerateRemittance = new Vue({
    el: "#generate-remittance_content",
    data: _tempData,
    methods: {
        tempShowByDates: function (id) {
            const _this = this;
            if(id === 1){
                _this.month_picker = true;
                _this.year_picker = true;
            }else{
                _this.month_picker = false;
            }

            _this.renderSelect2Picker();
            return _this;
        }, tempShowPicker: function () {
            const _this = this;
            const currentElement = _this.$el;
            _this.show_picker = (_this.show_picker === true) ? false : true;
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
                            const tempStartDate = picker.startDate.format('MMM DD, YYYY');
                            const tempEndDate = picker.endDate.format('MMM DD, YYYY');
                            const tempFormat = tempStartDate + ' - ' + tempEndDate;
                            $(currentElement).find("#date-range").val(tempFormat);
                        });
                }, 500);
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
                    }).on("select2:select, change", function(e){
                        const currentTarget = e.target;
                        if(typeof currentTarget !== "undefined"){ $(currentTarget).validate(); }
                    });

                $(currentElement).find("select[name='filter_year']")
                    .select2({
                        width: '100%',
                        placeholder: "SELECT YEAR",
                        allowClear: true,
                        dropdownParent: tempModal,
                        data: _years,
                        /*** ajax: {
                            url: baseUrl('payroll/get_posted_payroll_sheet_years'),
                            dataType: 'JSON',
                            type: 'GET',
                            global: false,
                        },  ***/
                        language: { errorLoading: function () { return "Searching..." } }
                    }).on("select2:select, change", function(e){
                        const currentTarget = e.target;
                        if(typeof currentTarget !== "undefined"){ $(currentTarget).validate(); }
                    });

                $(currentElement).find("select#employee")
                    .select2({
                        width: '100%',
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                        ajax: {
                            url: baseUrl('payroll/reports/select_employee/all'),
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
                        width: '100%',
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                        data: _companies,
                        /*** ajax: {
                            url: baseUrl('payroll/select_company'),
                            dataType: 'json',
                            global: false,
                            delay: 250,
                            processResults: function (data) {
                                return data;
                            },
                        }, ***/
                        language: { errorLoading: function () { return "Searching..." } }
                    }).on("select2:select, change", function (e) {
                        const _thisSelect2 = this;
                        const selectedValues = $(_thisSelect2).select2("val");
                        _this.company_ids = selectedValues;
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");

                        const currentTarget = e.target;
                        if(typeof currentTarget !== "undefined"){ $(currentTarget).validate(); }
                    }).on("select2:unselect", function (e) {
                        const _thisSelect2 = this;
                        const selectedValues = $(_thisSelect2).select2("val");
                        _this.company_ids = selectedValues;
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");

                        const currentTarget = e.target;
                        if(typeof currentTarget !== "undefined"){ $(currentTarget).validate(); }
                    });
            }, 200);
        }, resetFields: function () {
            var _this = this;
            var currentForm = $(_this.$el).find("#frm-remittance-report");
            if (typeof currentForm !== "undefined") {
                //currentForm.find("input[name=group]")[0].click();
                currentForm.find("select").val("").trigger("change");
                currentForm.find("select[multiple]").val([]).trigger("change");
                currentForm[0].reset();
            }
        }, toggleCheckbox : function(e){
            var _this = this;
            _this.hide_fields = [];
            var checkboxInline = $(e.target).closest(".m-checkbox-inline");
            if (typeof checkboxInline !== "undefined") {
                _this.updateVisibleFields();
                return _this;
            }
        }, updateVisibleFields: function () {
            var _this = this;
            _this.visible_fields = [];
            
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
            const include13th_month = _this.include13th_month;
            if(include13th_month === true){ _this.visible_fields.push('13th_month'); }
            return _this;
        }, toggle13thMonthFilter(e){
            var _this = this;
            var isChecked = $(e.target).is(":checked");
            _this.include13th_month = isChecked;
            _this.updateVisibleFields();
            return _this;
        }
    }, mounted: function () {
        var _this = this;
        _this.renderSelect2Picker();
    }
});

let tempColumns = [
    { data: "phealth_no", width: "15%", className: "print-size-10" },
    { data: "idno", width: "15%", className: "print-size-10" },
    { data: "employee_name", width: "*", className: "print-size-auto" },
    {
        data: "ph_ee", width: "15%", className: "print-size-8 text-right", render: function (data, meta, row) {
            return numberFormat(data);
        }
    },
    {
        data: "ph_er", width: "15%", className: "print-size-8 text-right", render: function (data, meta, row) {
            return numberFormat(data);
        }
    },
    {
        data: null, width: "15%", className: "print-size-8 text-right", render: function (data, meta, row) {
            let tempTotal = 0;
            tempTotal = parseFloat(row.ph_ee) + parseFloat(row.ph_er);
            tempTotal = numberFormat(tempTotal);
            return tempTotal;
        }
    },
];

let dtRemittances = $('#tbl-phic_remittances').DataTable({
    dom: "<'row'<'col-md-9 dtDetails'><'col-md-3 dtActions m--hide'B>>rt",
    serverSide: true,
    processing: true,
    destroy: true,
    ordering: false,
    paging: false,
    buttons: [
        {
            extend: 'excel',
            footer: true,
            text: '<i class="fa fa-download"></i><span class="m--font-boldest">EXPORT EXCEL</span>',
            className: "pull-right exportRemittanceAction btnExport",
            customize: function (xlsx) {
                const sheet = xlsx.xl.worksheets['sheet1.xml'];

                let numrows = $('row', sheet).length;
                numrows = $('row', sheet).length;
                let mergeCells = $('mergeCells', sheet);
                mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                    attr: {
                        ref: 'A' + numrows + ':' + 'C' + numrows,
                    },
                }));

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
            },
            exportOptions: {
                columns: ':visible',
                stripHtml: false,
            },
        },
        {
            extend: 'print',
            text: '<i class="fa fa-print"></i><span class="m--font-boldest">PRINT</span>',
            className: "pull-right printRemittanceAction btnPrint",
            footer: true,
            title: function () {
                let _comp = ``;
                if (typeof _tempFilter.companies == "object" && _tempFilter.companies.length > 0) {
                    var _tempComp = _tempFilter.companies;
                    _tempComp = _tempComp.join(" | ");
                    _comp += `<div class="m--regular-font-size-sm1 mt-1">COMPANY : ${_tempComp} </div>`;
                }

                return `<div class="m--regular-font-size-lg1">PHIC CONTRIBUTION</div>
                        <div class="m--regular-font-size-sm1 mt-1">PAY COVERAGE : ${_tempFilter.coverage_date}</div>
                        ${_comp}`;
            }, customize: function (win) {
                var last = null;
                var current = null;
                var bod = [];

                var css = `@page { size: landscape; margin: 0.5cm; }
                    table { font-size: 10px; }
                    .print-size-auto{ width: auto }
                    .print-size-8{ width: 8% }
                    .print-size-10{ width: 10% }
                    .print-size-25{ width: 25% }`,
                    head = win.document.head || win.document.getElementsByTagName('head')[0],
                    body = win.document.body || win.document.getElementsByTagName('body')[0],
                    style = win.document.createElement('style'),
                    tempDiv = win.document.createElement('div');

                style.type = 'text/css';
                style.media = 'print';

                if (style.styleSheet) {
                    style.styleSheet.cssText = css;
                } else {
                    style.appendChild(win.document.createTextNode(css));
                }

                head.appendChild(style);
                win.document.title = "SSS Contribution Printable Page";
                tempDiv.innerHTML = `<table width='100%' style='margin-top: 30px;'>
                <thead>
                    <tr>
                        <th width='33.33%'><p style='padding: 0 50px;'>Prepared By:</p></th>
                        <th width='33.33%'><p style='padding: 0 50px;'>Noted By:</p></th>
                        <th width='33.33%'><p style='padding: 0 50px;'>Approved By:</p></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width='33.33%'><p style='margin-left: 50px; margin-right: 50px; margin-top: 20px; border-top: 1px solid #000000;'>MARY GRACE B. CORTEZ</p></td>
                        <td width='33.33%'><p style='margin-left: 50px; margin-right: 50px; margin-top: 20px; border-top: 1px solid #000000;'>KAREN S. RAMIREZ</p></td>
                        <td width='33.33%'><p style='margin-left: 50px; margin-right: 50px; margin-top: 20px; border-top: 1px solid #000000;'>GYD / CMD</p></td>
                    </tr>
                <tbody>
            </table>`;
                body.appendChild(tempDiv);
            }, exportOptions: {
                columns: ':visible',
                stripHtml: false,
            }
        },
    ], ajax: {
        url: baseUrl('payroll/reports/get_phic_remittance_report_request'),
        type: 'POST',
        dataType: 'JSON',
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.ids = _tempIds;
            d.clear_table = _clearTable;
        }, error: function (xhr, error, code) {
            if (error == "parsererror") { dtRemittances.ajax.reload(); }
        },
    }, columns: tempColumns,
    drawCallback: function (settings) {
        const api = this.api();
        const btnPrint = $(settings.nTableWrapper).find(".printRemittanceAction");
        const btnExport = $(settings.nTableWrapper).find(".exportRemittanceAction");
        const dtActions = $(settings.nTableWrapper).find(".dtActions");
        const dtDetails = $(settings.nTableWrapper).find(".dtDetails");
        if (typeof btnPrint !== "undefined" && typeof dtActions !== "undefined") {
            btnPrint.addClass("btn m-btn btn-brand m-btn--icon m--hide animated fadeIn mr-1");
            const tempData = api.data();
            if (tempData.length > 0) {
                if (btnPrint.hasClass("m--hide") == true) { btnPrint.removeClass("m--hide"); }
                if (dtActions.hasClass("m--hide") == true) { dtActions.removeClass("m--hide"); }
            } else {
                if (dtActions.hasClass("m--hide") == false) { dtActions.addClass("m--hide"); }
                if (btnPrint.hasClass("m--hide") == false) { btnPrint.addClass("m--hide"); }
            }
        }
        if (typeof btnExport !== "undefined" && typeof dtActions !== "undefined") {
            btnExport.addClass("btn m-btn btn-brand m-btn--icon m--hide animated fadeIn");
            const tempData = api.data();
            if (tempData.length > 0) {
                if (btnExport.hasClass("m--hide") == true) { btnExport.removeClass("m--hide"); }
                if (dtActions.hasClass("m--hide") == true) { dtActions.removeClass("m--hide"); }
            } else {
                if (dtActions.hasClass("m--hide") == false) { dtActions.addClass("m--hide"); }
                if (btnExport.hasClass("m--hide") == false) { btnExport.addClass("m--hide"); }
            }
        }
        if (typeof dtDetails !== "undefined") {
            dtDetails.empty();
            if (typeof _tempFilter == "object" && Object.keys(_tempFilter).length > 0) {
                let tempHtmlCompany = "";
                if (typeof _tempFilter.companies == "object" && _tempFilter.companies.length > 0) {
                    let _arrCompanies = "";
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

                const tempHtml = `<div class='row'>
                    <div class='col-12 col-md-5'>
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
    }, footerCallback: function (row, data, start, end, display) {
        const api = this.api();
        // Remove the formatting to get integer data for summation
        const intVal = function (i) {
            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
        };

        let totalEE = api
            .column(3)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalER = api
            .column(4)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let totalER_EE = intVal(totalEE) + intVal(totalER);

        $(api.column(3).footer()).html(numberFormat(totalEE));
        $(api.column(4).footer()).html(numberFormat(totalER));
        $(api.column(5).footer()).html(numberFormat(totalER_EE));
    }
});

const generateFormValidate = $.validate({
    form: "#frm-remittance-report",
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const currentForm = form[0];
        const formUrl = currentForm.action;
        const formData = $(currentForm).serialize();
        getScriptRendering(formUrl, formData, currentForm);
        return false;
    }
});

function printDivMonthly(printdivname){
    const newstr = document.getElementById(printdivname).innerHTML;
    const printWindow = window.open(siteUrl('payroll/reports/printable_form'), '_blank');
    setTimeout(function () {
        const appendContainer = printWindow.document.getElementById('append_printable-container');
        if (typeof appendContainer !== "undefined" && appendContainer !== null) {
            appendContainer.innerHTML = newstr;
            setTimeout(function () {
                printWindow.print();
                printWindow.close();
            }, 500);
        } else {
            toastr.info("Print detail(s) is still in progress!", "Contribution / Deduction");
            printWindow.close();
        }
    }, 500);
    return false;
}

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
        success: function (response) {
            _tempFilter = Object.assign({});
            if (response.response) {
                _clearTable = false;
                _tempIds = response.data;
                _tempFilter = Object.assign({}, response.filters);
                const params = Object.assign({}, response.params);
                const visibleFields = vmGenerateRemittance.$data.visible_fields;

                $.ajax({
                    url: siteUrl("payroll/reports/generate_taxable_list"),
                    type: "post",
                    dataType: "json",
                    /*** data: { ps_id: _tempIds, [_csrf_token]: _csrf_hash, visible_fields: visibleFields }, ***/
                    data: { [_csrf_token]: _csrf_hash, visible_fields: visibleFields, params },
                    success: function (json) {
                        if (json.response) {
                            const tempTable = json.html;
                            $("#company").empty().html(response.filters.companies);
                            $("#month_year").empty().html(response.filters.month_year);
                            $("#append--table_content").empty().html(tempTable);
                            let tblTaxableIncome = $("#tbl_taxable_income").DataTable({
                                processing: true,
                                destroy: true,
                                ordering: false,
                                paging: false,
                                searching: false,
                                bInfo: false,
                                buttons: [
                                    {
                                        extend: 'excelHtml5',
                                        text: '<i class="fa fa-download"></i><span class="m--font-boldest">Export Excel</span>',
                                        title: "Taxable Income Reports -" + response.filters.companies + " - " + response.filters.month_year,
                                        exportOptions: {
                                            columns: ':visible',
                                            stripHtml: true,
                                        },
                                        footer: true,
                                    }
                                ]
                            });

                            $("#generate-report-modal").modal('hide');

                            $("#excel_btn").on("click", function(e) {
                                e.preventDefault();
                                tblTaxableIncome.button( '.buttons-excel' ).trigger();
                            });
                        }
                    }
                });
                toastr.success(response.toastr_msg, "Filtered Taxable Income");
            } else {
                _clearTable = true;
                toastr.error(response.toastr_msg, "Filtered Taxable Income");
            }
            dtRemittances.ajax.reload();

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
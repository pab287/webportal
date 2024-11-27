const psSignatoryModal = $("#modal-ps--signatory");
const psDataModal = $("#modal-ps--data");
const psResetSignatoryModal = $("#modal-ps--reset-signatory");
const psGenerateReportModal = $("#generate-report-modal");

let globalPrintableSignatory = [];
let _tempIds = [];
let _years = [];
let _companies = [];
let psEmployeeGroup = [];

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

var _clearTable = true;
var _tempFilter = {};
var _globalFooterHtml = null;

var _tempData = {
    show_by_date: true, show_picker: false,
    year_picker: false, company_ids: 0,
};
let selectedCompany = null;

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.years !== "undefined" && _tempContentData.years.length > 0){
        _years = _tempContentData.years;
    }
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){
        _companies = _tempContentData.company;
    }
}
var vmGenerateRemittance = new Vue({
    el: "#generate-remittance_content",
    data: _tempData,
    methods: {
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

                /*** $(currentElement).find("select[name='filter_year']")
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
                    }); ***/

                $(currentElement).find("select[name='filter_year']")
                    .select2({
                        width: '100%',
                        data: _years,
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
                            url: baseUrl('payroll/reports/select_employee'),
                            dataType: 'json',
                            global: false,
                            delay: 250,
                            data: function (params) {
                                /*** params.company_ids = selectedCompany.id; ***/
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
                        data: _companies,
                        allowClear: true,
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                    }).on("select2:select", function (e) {
                        selectedCompany = e.params.data;
                        _this.company_id = selectedCompany.id;
                        $(e.target).validate();
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");
                    }).on("select2:unselect", function (e) {
                        selectedCompany = e.params.data;
                        _this.company_id = 0;
                        $(e.target).validate();
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");
                    });
                /*** $(currentElement).find("select#company")
                    .select2({
                        width: '100%',
                        allowClear: true,
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
                        selectedCompany = e.params.data;
                        _this.company_id = selectedCompany.id;
                        $(e.target).validate();
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");
                    }).on("select2:unselect", function (e) {
                        selectedCompany = e.params.data;
                        _this.company_id = 0;
                        $(e.target).validate();
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");
                    }); ***/
            }, 200);
        }, resetFields: function () {
            var _this = this;
            var currentForm = $(_this.$el).find("#frm-remittance-report");
            if (typeof currentForm !== "undefined") {
                psEmployeeGroup = [];
                currentForm.find("input[name=group]")[0].click();
                currentForm.find("select").val("").trigger("change");
                currentForm.find("select[multiple]").prop("disabled", false);
                setTimeout(function(){
                    currentForm.find("select[multiple]").val([]).trigger("change");
                    currentForm[0].reset();
                }, 500);
            }
        },
    }, mounted: function () {
        var _this = this;
        _this.renderSelect2Picker();
    }
});

var vmPsData = new Vue({
    el: "#ps-data--content",
    data: { record: {}, count: 0, sss_table: {} },
    methods: {
        formattedDate: function(date){
            let tempDate = date;
            if(date){
                tempDate = moment(new Date(date), 'MMMM DD, YYYY').format('MMMM DD, YYYY');
            }
            return tempDate;
        }, formattedAdjustments(adjustment, type='sss'){
            let tempAdjustment = 0;
            if(adjustment && type){
                const adjx = adjustment.split(',');
                console.log(adjx);
                adjx.forEach(function(vv){
                    const adjcol = vv.split('|');
                    if(adjcol.length === 3 && type === adjcol[0].toLowerCase()){
                        if(parseInt(adjcol[2]) === 1){
                            tempAdjustment = parseFloat(tempAdjustment) + parseFloat(adjcol[1]);
                        }else{
                            tempAdjustment = parseFloat(tempAdjustment) - parseFloat(adjcol[1]);
                        }
                    }
                });
            }
            return numberFormat(tempAdjustment);
        }, formattedAmount: function(amount){
            return numberFormat(amount);
        }
    }
});

let tempColumns = [
    { data: "sss_no", width: "8%", className: "print-size-8" },
    { data: "idno", width: "8%", className: "print-size-8" },
    { data: "employee_name", width: "*", className: "print-size-auto", render: function(data, meta, row){
        const tempData = { name: data, 
            rows: row.ps_data, 
            contribution_basis: row.contribution_basis, 
            amount_basis: row.amount_basis, 
            range: row.sss, 
        };
        const psData = JSON.stringify(tempData);
        return `<a href='javascript:void(0);' style='text-decoration: none;' onClick='getCurrentPsData(${psData})' class='text-dark'>${data}</a>`;
    } },
    {
        data: "basic_rate", width: "8%", className: "print-size-8 text-right", render: function (data, meta, row) {
            return numberFormat(data);
        }
    },
    {
        data: "gross_pay", width: "8%", className: "print-size-8 text-right", render: function (data, meta, row) {
            return numberFormat(data);
        }
    },
    {
        data: "sss_ee", width: "5%", className: "print-size-5 text-right", render: function (data, meta, row) {
            return numberFormat(data);
        }
    },
    {
        data: "sss_er", width: "5%", className: "print-size-5 text-right", render: function (data, meta, row) {
            return numberFormat(data);
        }
    },
    {
        data: "ec_er", width: "5%", className: "print-size-5 text-right", render: function (data, meta, row) {
            return numberFormat(data);
        }
    },
    {
        data: "sss_prov_ee", width: "5%", className: "print-size-5 text-right", render: function (data, meta, row) {
            return numberFormat(data);
        }
    },
    {
        data: "sss_prov_er", width: "5%", className: "print-size-5 text-right", render: function (data, meta, row) {
            return numberFormat(data);
        }
    },
    {
        data: null, width: "8%", className: "print-size-8 text-right", render: function (data, meta, row) {
            let tempTotal = 0;
            tempTotal = parseFloat(row.sss_prov_ee) + parseFloat(row.sss_prov_er);
            tempTotal = numberFormat(tempTotal);
            return tempTotal;
        }
    },
    {
        data: null, width: "8%", className: "print-size-8 text-right", render: function (data, meta, row) {
            let tempTotal = 0;
            tempTotal = parseFloat(row.sss_er) + parseFloat(row.ec_er) + parseFloat(row.sss_prov_er);
            tempTotal = numberFormat(tempTotal);
            return tempTotal;
        }
    },
    {
        data: null, width: "10%", className: "print-size-8 text-right", render: function (data, meta, row) {
            let tempTotal = 0;
            tempTotal = parseFloat(row.sss_ee) + parseFloat(row.sss_er) + parseFloat(row.ec_er) + parseFloat(row.sss_prov_ee) + parseFloat(row.sss_prov_er);
            tempTotal = numberFormat(tempTotal);
            return tempTotal;
        }
    },
];

let dtRemittances = $('#tbl-sss_remittances').DataTable({
    dom: "<'row'<'col-md-8 dtDetails'><'col-md-4 dtActions m--hide'B>>rt",
    /*** serverSide: true,
    processing: true, ***/
    destroy: true,
    ordering: false,
    paging: false,
    buttons: [
        {
            extend: 'print',
            text: '<i class="fa fa-print"></i><span class="m--font-boldest">PRINT</span>',
            className: "pull-right printRemittanceAction btnPrint",
            footer: true,
            title: function () {
                let _comp = ``;
                var _tempComp = _tempFilter.companies;
                _comp += `<div class="m--regular-font-size-sm1 mt-1">COMPANY : ${_tempComp} </div>`;

                return `<div class="m--regular-font-size-lg1">SSS CONTRIBUTION</div>
                        <div class="m--regular-font-size-sm1 mt-2">FILTERED BY : ${_tempFilter.filter_by}</div>
                        <div class="m--regular-font-size-sm1 mt-1">PAY COVERAGE : ${_tempFilter.coverage_date}</div>
                        ${_comp}`;
            }, customize: function (win) {
                var last = null;
                var current = null;
                var bod = [];

                var css = `@page { size: landscape; margin: 0.5cm; }
                    table { font-size: 8px; }
                    table.dataTable tfoot tr:first-child th{ border-top: 1px solid #000000; }
                    table.dataTable tfoot tr:first-child th{ border-bottom: 4px double #000000; }
                    .print-size-auto{ width: auto }
                    .print-size-5{ width: 5% }
                    .print-size-8{ width: 8% }
                    .print-size-10{ width: 10% }
                    .print-size-25{ width: 25% }`,
                    head = win.document.head || win.document.getElementsByTagName('head')[0],
                    body = win.document.body || win.document.getElementsByTagName('body')[0],
                    style = win.document.createElement('style'),
                    tempDiv = win.document.createElement('div'),
                    tempDiv2 = win.document.createElement('div');

                style.type = 'text/css';
                style.media = 'print';

                if (style.styleSheet) {
                    style.styleSheet.cssText = css;
                } else {
                    style.appendChild(win.document.createTextNode(css));
                }

                head.appendChild(style);
                win.document.title = "SSS Contribution Printable Page";
                var tempTable = win.document.getElementsByClassName('dataTable')[0];
                $(tempTable).removeClass("table-bordered");
                $(tempTable).find("tfoot th:first-child").addClass("m--font-boldest");
                var tempTableTfoot = win.document.getElementsByTagName('tfoot')[0];
                tempTableTfoot.innerHTML = _globalFooterHtml;
                $(tempTableTfoot).find("tr th:first-child").removeClass("text-center");

                let signatoryCells = ``;
                if (globalPrintableSignatory.length > 0) {
                    $.each(globalPrintableSignatory, function (i, v) {
                        let tempLabel = v.label;
                        tempLabel = tempLabel.toUpperCase();

                        let tempValue = v.value;
                        tempValue = tempValue ? tempValue.toUpperCase() : tempValue;

                        if (tempLabel && v.is_active == true) {
                            let tempCell = `<div style='display: inline-block; position: relative; width: 30%; margin-top: 30px;'>
                                <p style='font-weight: bold;'>${tempLabel}:</p>
                                <p style='font-weight: 600; margin-left: 40px; margin-right: 40px; margin-top: 50px; padding-top: 10px; border-top: 1px solid #000000;'>${tempValue}</p>
                                </div>`;
                            signatoryCells += tempCell;
                        }
                    });
                }
                if (signatoryCells) {
                    tempDiv2.innerHTML = `<table width='100%' style='margin-top: 60px; page-break-inside: avoid; text-align: center; font-size: 10px;'>
                        <tbody>
                            <tr>
                                <td width='100%'>${signatoryCells}</td>
                            </tr>
                        </tbody>
                        </table>`;
                    body.appendChild(tempDiv2);
                }
            }, exportOptions: {
                columns: ':visible',
                stripHtml: false,
            }
        }
    ], 
    /*** ajax: {
        url: baseUrl('payroll/reports/get_sss_remittance_report_request'),
        type: 'POST',
        dataType: 'JSON',
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.ids = _tempIds;
            d.clear_table = _clearTable;
        }, error: function (xhr, error, code) {
            if (error == "parsererror") { dtRemittances.ajax.reload(); }
        },
    }, ***/
    columns: tempColumns,
    drawCallback: function (settings) {
        var api = this.api();
        var tempFooter = $(settings.nTableWrapper).find("tfoot");
        if (typeof tempFooter !== "undefined") { _globalFooterHtml = tempFooter[0].innerHTML; }

        var btnPrint = $(settings.nTableWrapper).find(".printRemittanceAction");
        var dtActions = $(settings.nTableWrapper).find(".dtActions");
        var dtDetails = $(settings.nTableWrapper).find(".dtDetails");
        if (typeof btnPrint !== "undefined" && typeof dtActions !== "undefined") {
            btnPrint.addClass("btn m-btn btn-brand m-btn--icon m--hide animated fadeIn ml-1");
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
                tempHtmlCompany += `<div class='row'>
                        <div class='col-12 col-md-4'>
                            <span class='m--font-bolder'>COMPANY :</span>
                        </div>
                        <div class='col-12 col-md-8'>
                            <span class='m--font-bolder'>${_tempFilter.companies}</span>
                        </div>
                    </div>`;

                var tempHtml = `<div class='row'>
                    <div class='col-12 col-md-6'>
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
                    <div class='col-12 col-md-6'>${tempHtmlCompany}</div>
                </div>`;
                dtDetails.html(tempHtml);
            }
        }

        if(tempData.length > 0){
            if (typeof selectedCompany.id !== "undefined" && selectedCompany.id !== null && selectedCompany.id) {
                const currentSelectCompanyId = selectedCompany.id;
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
    
                        vmResetSignatories.row = Object.assign({}, tempRow);
                        vmResetSignatories.count = ctr;
                    }
                });
            }
        }
    }, footerCallback: function (row, data, start, end, display) {
        globalGrandTotal = Object.assign({});

        var api = this.api(), data;
        // Remove the formatting to get integer data for summation
        var intVal = function (i) {
            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
        };

        totalBasic = api
            .column(3)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        totalGross = api
            .column(4)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        totalEESSS = api
            .column(5)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        totalERSSS = api
            .column(6)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        totalEREC = api
            .column(7)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        totalEEPROV = api
            .column(8)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        totalERPROV = api
            .column(9)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        totalPROVTOTAL = parseFloat(totalEEPROV) + parseFloat(totalERPROV);
        totalERSSSTOTAL = parseFloat(totalERSSS) + parseFloat(totalEREC) + parseFloat(totalERPROV);
        totalSSSTOTAL = parseFloat(totalEESSS) + parseFloat(totalERSSS) + parseFloat(totalEREC) + parseFloat(totalEEPROV) + parseFloat(totalERPROV);

        totalPROVTOTAL = numberFormat(totalPROVTOTAL);

        $(api.column(3).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalBasic) + "</span>");
        $(api.column(4).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalGross) + "</span>");
        $(api.column(5).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalEESSS) + "</span>");
        $(api.column(6).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalERSSS) + "</span>");
        $(api.column(7).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalEREC) + "</span>");
        $(api.column(8).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalEEPROV) + "</span>");
        $(api.column(9).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalERPROV) + "</span>");
        $(api.column(10).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalPROVTOTAL) + "</span>");
        $(api.column(11).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalERSSSTOTAL) + "</span>");
        $(api.column(12).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalSSSTOTAL) + "</span>");
    }
});

$.validate({
    form: "#frm-remittance-report",
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        let propDisabled = false;

        var currentForm = form[0];
        var formUrl = currentForm.action;

        const tempEmployeeFilter = $(currentForm).find("select#employee");
        if(typeof tempEmployeeFilter !== "undefined"){
            propDisabled = tempEmployeeFilter.is(":disabled");
            if(propDisabled){ tempEmployeeFilter.prop("disabled", false); }
        }

        var formData = $(currentForm).serialize();
        if(propDisabled){ tempEmployeeFilter.prop("disabled", true); }

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
            if (json.response) {
                _clearTable = false;
                /*** _tempIds = json.data; ***/

                dtRemittances.clear();
                dtRemittances.rows.add(json.data).draw(false);

                _tempFilter = Object.assign({}, json.filters);
                psGenerateReportModal.modal("hide");
                toastr.success(json.toastr_msg, "Filtered SSS Contribution");
            } else {
                _clearTable = true;
                toastr.error(json.toastr_msg, "Filtered SSS Contribution");
            }
            /*** dtRemittances.ajax.reload(function () {
                if (typeof selectedCompany.id !== "undefined" && selectedCompany.id !== null && selectedCompany.id) {
                    const currentSelectCompanyId = selectedCompany.id;
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

                            vmResetSignatories.row = Object.assign({}, tempRow);
                            vmResetSignatories.count = ctr;
                        }
                    });
                }
            }, false); ***/

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
            params.company_id = $("form#frm-remittance-report select#company").val();
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
                        const tempEmployeeSelector = $("form#frm-remittance-report select#employee");
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
            const tempEmployeeSelector = $("form#frm-remittance-report select#employee");
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
        const tempEmployeeSelector = $("form#frm-remittance-report select#employee");
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
                        const tempEmployeeSelector = $("form#frm-remittance-report select#employee");
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

var getCurrentPsData = function(data){
    if(typeof data !== "undefined" && Object.keys(data).length > 0){
        vmPsData.record = Object.assign({}, data);
        vmPsData.sss_table = Object.assign({}, data.range);
        vmPsData.count = data.rows.length;
    }
    
    psDataModal.modal("show");
}
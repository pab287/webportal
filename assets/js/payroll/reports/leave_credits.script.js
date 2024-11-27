const psSignatoryModal = $("#modal-ps--signatory");
const psResetSignatoryModal = $("#modal-ps--reset-signatory");


const leaveCreditsTable = $("#tbl-leave_credits");
const generateModal = $("#generate-report-modal");

let dtLeaveCreditsTable, selectedCompany = null;

let _companies = [];
let psEmployeeGroup = [];
let globalPrintableSignatory = [];
let _tempFilter = {};

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
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){
        _companies = _tempContentData.company;
    }
}

const vmGenerateReport = new Vue({
    el: "#generate-leave_credits_content",
    data: { filter_by: 1 },
    watch: {
        filter_by(value){
            const _this = this;
            const currentElement = _this.$el;
            if(parseInt(value) == 2){
                setTimeout(function(){
                    $("select#month", currentElement)
                    .select2({
                        width: '100%',
                        data: months,
                        placeholder: "SELECT MONTH",
                        allowClear: true,
                    });
                    $("#month_filter_option", currentElement).removeClass("m--hide").addClass("m-animate-fade-in");
                }, 250);
            }
        }
    }
});

if(typeof generateModal != "undefined" && generateModal.length == 1){
    $("#payroll_group", generateModal).select2({
        placeholder: 'Select an option',
        width: '100%',
        dropdownParent: generateModal,
        ajax: {
            url: baseUrl("payroll/select_payroll_group"),
            dataType: "json",
            type: 'get',
            delay: 250,
            global: false,
            data: function (params) {
                params.company_id = $("form#frm-leave_credits-report select#company").val();
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
                            const tempEmployeeSelector = $("form#frm-leave_credits-report select#employee");
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
                const tempEmployeeSelector = $("form#frm-leave_credits-report select#employee");
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
            const tempEmployeeSelector = $("form#frm-leave_credits-report select#employee");
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
                            const tempEmployeeSelector = $("form#frm-leave_credits-report select#employee");
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

    $("select#employee", generateModal)
    .select2({
        width: '100%',
        placeholder: "SELECT AN OPTION",
        dropdownParent: generateModal,
        ajax: {
            url: baseUrl('payroll/select_employee'),
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
        }, language: { errorLoading: function () { return "Searching..." } }
    });

    $("select#company", generateModal)
    .select2({
        allowClear: true,
        width: '100%',
        data: _companies,
        placeholder: "SELECT AN OPTION",
        dropdownParent: generateModal,
    }).on("select2:select", function (e) {
        selectedCompany = e.params.data;
        $(e.target).validate();
        $("form#frm-leave_credits-report", generateModal)
            .find("select#employee")
            .val([])
            .trigger("change");
    }).on("select2:unselect", function (e) {
        selectedCompany = e.params.data;
        $(e.target).validate();
        $("form#frm-leave_credits-report", generateModal)
            .find("select#employee")
            .val([])
            .trigger("change");
    });
}

if(typeof leaveCreditsTable !== "undefined" && leaveCreditsTable.length == 1){
    dtLeaveCreditsTable = leaveCreditsTable.DataTable({
        dom: "<'row'<'col-md-9 dtDetails'><'col-md-3 dtActions m--hide'B>>rt",
        serverSide: false,
        processing: false,
        destroy: true,
        ordering: false,
        paging: false,
        buttons: [{
                extend: 'excel',
                footer: true,
                text: '<i class="fa fa-download"></i><span class="m--font-boldest">EXPORT EXCEL</span>',
                className: "pull-right exportLeaveCreditsAction btnExport",
                exportOptions: {
                    columns: ':visible',
                    stripHtml: false,
                },
            }, {
                extend: 'print',
                text: '<i class="fa fa-print"></i><span class="m--font-boldest">PRINT</span>',
                className: "pull-right printLeaveCreditsAction btnPrint",
                footer: true,
                title: function () {
                    let tempPayrollGroup = "";
                    if (typeof psEmployeeGroup !== "undefined" && typeof psEmployeeGroup == "object" && psEmployeeGroup.length > 0) {
                        tempPayrollGroup += psEmployeeGroup.join(" | ");
                    }

                    let newPayrollGroup = "";
                    if (tempPayrollGroup) {
                        newPayrollGroup = `<div class="m--regular-font-size-sm1 mt-3">PAYROLL GROUP: <span style='font-weight: 600; text-transform: uppercase;'>${tempPayrollGroup}</span></div>`;
                    }
                    
                    return `<div class='mb-2'><div class="text-center m--regular-font-size-lg2" style="text-transform: uppercase;">${selectedCompany.description}</div>
                        <div class="text-center m--regular-font-size-sm1 text-muted" style="text-transform: uppercase;">${selectedCompany.company_address}</div>
                        <div class="text-center m--regular-font-size-lg1 mt-2">LEAVE CREDITS REPORT</div>
                        ${newPayrollGroup}</div>`;
                }, customize: function (win) {
                    var css = `@page { size: portrait; margin: 0.5cm; }
                        .printable-row_content{
                            page-break-after: always;
                            page-break-inside: avoid;
                        }
                        table { font-size: 10px; }
                        .print-size-auto{ width: auto }
                        .print-size-8{ width: 8% }
                        .print-size-10{ width: 10% }
                        .print-size-25{ width: 25% }`,
                        head = win.document.head || win.document.getElementsByTagName('head')[0],
                        body = win.document.body || win.document.getElementsByTagName('body')[0],
                        style = win.document.createElement('style'),
                        tempDiv2 = win.document.createElement('div');
    
                    tempDiv2.className = "printable-row_content";
    
                    style.type = 'text/css';
                    style.media = 'print';
    
                    if (style.styleSheet) {
                        style.styleSheet.cssText = css;
                    } else {
                        style.appendChild(win.document.createTextNode(css));
                    }
    
                    head.appendChild(style);
                    win.document.title = "Leave Credits Printable Page";

                    let signatoryCells = ``;
                    if (globalPrintableSignatory.length > 0) {
                        $.each(globalPrintableSignatory, function (i, v) {
                            let tempLabel = v.label;
                            tempLabel = tempLabel.toUpperCase();

                            let tempValue = v.value;
                            tempValue = tempValue ? tempValue.toUpperCase() : tempValue;

                            if (tempLabel && v.is_active == true) {
                                let tempCell = `<div style='display: inline-block; position: relative; width: 33.33%; margin-top: 30px; text-align: center'>
                                    <p style='font-weight: bold; text-align: center; margin: 0 auto'>${tempLabel}:</p>
                                    <p style='font-weight: 600; margin-left: 40px; margin-right: 40px; margin-top: 50px; padding-top: 10px; border-top: 1px solid #000000;'>${tempValue}</p>
                                    </div>`;
                                signatoryCells += tempCell;
                            }
                        });
                    }
                    if (signatoryCells) {
                        tempDiv2.innerHTML = `<table width='100%' style='margin-top: 60px; text-align: center; font-size: 10px;'>
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
            },
        ], columns: [
            { data: "lastname", width: "14%" },
            { data: "firstname", width: "*" },
            { data: "date_start", width: "8%", className: "text-center", render: function(data){
                return moment(new Date(data), "YYYY-MM-DD").format("DD-MMM-YYYY");
            } },
            { data: "sil", width: "4%", className: "text-center", },
            { data: "basic_rate", width: "8%", className: "text-right", render: function(data){
                return numberFormat(data);
            } },
            { data: "allowance_rate", width: "8%", className: "text-right", render: function(data){
                return numberFormat(data);
            } },
            { className: "text-right", width: "8%", render: function(_data, _type, row){
                    const { basic_rate } = row;
                    const totalAmount = parseFloat(basic_rate) / 12;
                    return numberFormat(totalAmount.toFixed(2));
                }
            }, { className: "text-right", width: "8%", render: function(_data, _type, row){
                    const { basic_rate, allowance_rate } = row;
                    const halfBasicRate = parseFloat(basic_rate) / 12;
                    const totalAmount = parseFloat(basic_rate) + parseFloat(allowance_rate) + halfBasicRate;
                    return numberFormat(totalAmount.toFixed(2));
                }
            }, { className: "text-right", width: "8%", render: function(_data, _type, row){
                    const { sil, basic_rate, allowance_rate } = row;
                    const halfBasicRate = parseFloat(basic_rate) / 12;
                    const totalAmount = parseFloat(basic_rate) + parseFloat(allowance_rate) + halfBasicRate;
                    const ntotalAmount = totalAmount.toFixed(2);
                    const totalCredit = ntotalAmount * parseFloat(sil);
                    const nTotalCredits = numberFormat(totalCredit.toFixed(2));
                    return nTotalCredits;
                }
            }, { data: "charges", width: "8%", className: "text-right", render: function(data){
                    const nCharges = numberFormat(data.toFixed(2));
                    return nCharges; 
                } 
            }, { className: "text-right", width: "10%", render: function(_data, _type, row){
                    const { sil, basic_rate, allowance_rate, charges } = row;
                    const halfBasicRate = parseFloat(basic_rate) / 12;
                    const totalAmount = parseFloat(basic_rate) + parseFloat(allowance_rate) + halfBasicRate;
                    const ntotalAmount = totalAmount.toFixed(2);
                    const net_totalCredits = (ntotalAmount * parseFloat(sil)) - parseFloat(charges.toFixed(2));

                    const netTotalCredits = net_totalCredits > 0 ? numberFormat(net_totalCredits.toFixed(2)): numberFormat(0.00);
                    return netTotalCredits;
                }
            }, 
        ], drawCallback: function (settings) {
            var api = this.api();
            var btnPrint = $(settings.nTableWrapper).find(".printLeaveCreditsAction");
            var btnExport = $(settings.nTableWrapper).find(".exportLeaveCreditsAction");
            var dtActions = $(settings.nTableWrapper).find(".dtActions");
            if (typeof btnPrint !== "undefined" && typeof dtActions !== "undefined") {
                btnPrint.addClass("btn m-btn btn-brand m-btn--icon m--hide animated fadeIn mr-1");
                var tempData = api.data();
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
                var tempData = api.data();
                if (tempData.length > 0) {
                    if (btnExport.hasClass("m--hide") == true) { btnExport.removeClass("m--hide"); }
                    if (dtActions.hasClass("m--hide") == true) { dtActions.removeClass("m--hide"); }
                } else {
                    if (dtActions.hasClass("m--hide") == false) { dtActions.addClass("m--hide"); }
                    if (btnExport.hasClass("m--hide") == false) { btnExport.addClass("m--hide"); }
                }
            }
        }
    });
}

$.validate({
    form: "#frm-leave_credits-report",
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
                _tempIds = json.data;
                _tempFilter = Object.assign({}, json.filters);

                dtLeaveCreditsTable.clear();
                dtLeaveCreditsTable.rows.add(json.data);
                dtLeaveCreditsTable.draw(false);
                
                if(typeof selectedCompany != "undefined" && Object.keys(selectedCompany).length > 0){
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

                generateModal.modal("hide");
                toastr.success(json.toastr_msg, "Leave Credits");
            } else {
                _clearTable = true;
                toastr.error(json.toastr_msg, "Filtered PHIC Contribution");
            }
            


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

var resetFields = function(_this){
    const currentForm = $(_this).closest("form");
    if(typeof currentForm != "undefined" && currentForm.length == 1){
        psEmployeeGroup = [];
        currentForm.find("select").val("").trigger("change");
        currentForm.find("select[multiple]").prop("disabled", false);
        setTimeout(function(){
            currentForm.find("select[multiple]").val([]).trigger("change");
            currentForm[0].reset();
        }, 500);
    }
}

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
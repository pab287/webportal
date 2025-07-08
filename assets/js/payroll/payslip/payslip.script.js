const cbSelectAll = $("#cb-select-all");
const modalGeneratePayslip = $("#payroll-payslip-modal");
const dtPayrollPayslip = $("#table-payroll-payslip");
const viewTimesheetModal = $("#view-timesheet-modal");
const viewPayrollPayslipModal = $("#view-payroll-payslip-modal");

const exportOptions = {
    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
};

let dtEmployeeTimesheet;
let dtPayslipTable;
let dtPayrollIds = [];
let _company = [];
let _companyId = 0;
let dropdownCompany = [];

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0) {
    if (jQuery.inArray("view_by_company", _currentActions) !== -1) {
        if(typeof _tempContentData.company !== "undefined" && _tempContentData.company){ _company = _tempContentData.company; _companyId = _tempContentData.company.id; }
    }
    if(typeof _tempContentData.dropdown_company !== "undefined" && _tempContentData.dropdown_company){ dropdownCompany = _tempContentData.dropdown_company; }
}

const vmPayslip = new Vue({
    el: "#generated-payslip",
    data: { request: {}, has_request: false, rows: {}, row_count: 0 }
});


const vmPayslipContent = new Vue({
    el: "#temp-payslip_content",
    data: { row: {}, ot_computation: {}, 
    total_ot_hrs: 0, ot_hrs: 0, ot_ndiff_hrs: 0, ot_ndiff_computation: 0,
    total_ndiff_hrs: 0, total_ndiff_computation: 0, raw_tl: 0, raw_tod: 0, raw_tli: 0 },
    methods: {
        printCurrentPayslip: function (id) {
            if (parseInt(id) > 0) {
                let tempId = [];
                tempId.push(id);
                return triggerPrintable(tempId);
            } else {
                return false;
            }
        },
        adjustments: function (loan, amount, adj = []) {
            return loan + "||" + amount + "||" + adj;
        }
    }
});

if (typeof modalGeneratePayslip !== "undefined" && modalGeneratePayslip.length == 1) {
    modalGeneratePayslip.on("show.bs.modal", function () {
        const modal = $(this);
        const tempForm = modal.find("form");
        if (typeof tempForm !== "undefined") {
            tempForm[0].reset();
            let tempSelections = tempForm.find("select");
            if (typeof tempSelections !== "undefined" && tempSelections.length > 0) {
                $.each(tempSelections, function (index, object) {
                    $(object).val("").trigger("change");
                });
            }
        }

        if (jQuery.inArray("view_by_company", _currentActions) !== -1) {
            if (_company) {
                const companySelect2 = modalGeneratePayslip.find("#company");
                if(typeof companySelect2 !== "undefined" && companySelect2.length == 1) {
                    companySelect2.val(_company.id).trigger("change");
                    companySelect2.next().prop("hidden", true);
                }
                modalGeneratePayslip.find("#has_privi_company-text").text(_company.text);
            }
        } else {
            modalGeneratePayslip.find("#has_privi_company-text").prop("hidden", true);
        }
    });

    modalGeneratePayslip.find("#employees").select2({
        placeholder: 'Select',
        width: '100%',
        dropdownParent: modalGeneratePayslip,
        ajax: {
            url: baseUrl("payroll/select_employee_by_privileges"), //original controller is select_employee and changed to select_employee_by_privileges as the first controller is global
            dataType: "json",
            delay: 250,
            global: false,
            data: function (params) {
                var query = {
                    q: params.term,
                    company_id: _companyId
                }

                return query;
            },
            processResults: function (data, params) {
                if (jQuery.inArray("view_by_company", _currentActions) !== -1) {
                    if (typeof params.term == "undefined") {
                        if (data.results.length === 0) {
                            toastr.warning("No Assigned Payroll Group found!", "Payroll Group");
                        }
                    }
                }
                
                return data;
            }
        }
    });

    modalGeneratePayslip.find("#company").select2({
        placeholder: 'Select',
        width: '100%',
        allowClear: true,
        dropdownParent: modalGeneratePayslip,
        data: dropdownCompany,
        /*** ajax: {
            url: baseUrl("payroll/select_company"),
            dataType: "json",
            delay: 250,
            global: false,
            processResults: function (data) {
                return data;
            }
        } ***/
    }).on("select2:select", function (data) {
        selectedCompany = data.params.data;
    });


    //=====================END JV==============================

    var resetFilter = function (event) {
        const form = $(event).closest("form");
        if (typeof form !== "undefined" && form.length == 1) {
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


    $("#payroll_group").select2({
        placeholder: 'Select',
        width: '100%',
        ajax: {
            url: baseUrl("payroll/select_payroll_group_payslip"), //original controller is select_payroll_group and changed to select_payroll_group_payslip as the first controller is global
            dataType: "json",
            type: 'get',
            delay: 250,
            global: false,
            data: function (params) {
                params.company_id = _companyId || $("form#frm-payroll-posted select#company").val();
                return params;
            },
            processResults: function (data, params) {
                if (jQuery.inArray("view_by_company", _currentActions) !== -1) {
                    if (typeof params.term == "undefined") {
                        if (data.results.length === 0) {
                            toastr.warning("No Assigned Payroll Group found!", "Payroll Group");
                        }
                    }
                }

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
                            const tempEmployeeSelector = $("form#frm-payroll-posted select#employees");
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
                const tempEmployeeSelector = $("form#frm-payroll-posted select#employees");
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
            const tempEmployeeSelector = $("form#frm-payroll-posted select#employees");
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
                            const tempEmployeeSelector = $("form#frm-payroll-posted select#employees");
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


    //=====================END JV==============================



    modalGeneratePayslip.find('#pay-date').datepicker({
        todayHighlight: true,
        orientation: "bottom left",
        templates: {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        },
        autoclose: true,
        format: 'M.dd, yyyy'
    });

    modalGeneratePayslip.find("#date-picker").daterangepicker({
        buttonClasses: 'm-btn btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',
        locale: {
            format: 'MM/DD/YYYY'
        }
    });

    modalGeneratePayslip.find('#date-picker').on('apply.daterangepicker', function (ev, picker) {
        $("#date-range").val(picker.startDate.format('MMM. DD, YYYY') + ' - ' + picker.endDate.format('MMM. DD, YYYY'));
    });

    modalGeneratePayslip.find("#payout_schedule").select2({
        placeholder: "SELECT",
        width: "100%",
        dropdownParent: modalGeneratePayslip,
        ajax: {
            url: baseUrl(`payroll/get_payout_schedule`),
            dataType: "JSON",
            global: false,
            delay: 500
        }
    }).on("select2:select", function (option) {
        const data = option.params.data;
        const id = data.id;

        $.ajax({
            url: baseUrl(`payroll/get_payout_schedule_occurrence/${id}`),
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                $("#payroll_sequence").empty();

                response.forEach(item => {
                    const newOption = new Option(item.text, item.id, false, false);
                    $("#payroll_sequence").append(newOption);
                });
            }
        });
    });

    modalGeneratePayslip.find("#payroll_sequence").select2({
        placeholder: "SELECT",
        width: "100%",
        dropdownParent: modalGeneratePayslip,
    });

    $.validate({
        form: modalGeneratePayslip.find("form"),
        lang: "en",
        scrollToTopOnError: false,
        onSuccess: function (form) {
            var currentForm = form[0];
            var formUrl = currentForm.action;
            var formData = $(currentForm).serialize();

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
                    dtPayrollIds = [];
                    vmPayslip.request = Object.assign({});
                    vmPayslip.has_request = false;
                    if (json.response) {
                        vmPayslip.request = Object.assign({}, json.post_data);
                        vmPayslip.has_request = true;

                        toastr.success(
                            json.toastr_msg,
                            "Generation payslip, Successful",
                            5000
                        );
                        if (typeof modalGeneratePayslip !== "undefined") {
                            modalGeneratePayslip.modal("hide");
                        }
                        if (parseInt(json.count) > 0) {
                            dtPayrollIds = json.ps_id;
                            dtPayslipTable.ajax.reload();
                        }
                    } else {
                        toastr.error(
                            json.toastr_msg,
                            "Error generating payslip data!",
                            5000
                        );
                    }

                    $(currentForm)
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        ).prop("disabled", true);
                }
            });
            return false;
        }
    });
}

if (typeof dtPayrollPayslip !== "undefined" && dtPayrollPayslip.length == 1) {
    dtPayslipTable = dtPayrollPayslip.DataTable({
        dom: 'Brtlp',
        serverSide: false,
        destroy: true,
        autoWidth: false,
        ordering: false,
        buttons: [{
            text: '<i class="fa fa-print"></i><span class="m--font-boldest">PRINT REPORT</span>',
            className: "pull-right printPayslipAction btn-warning btnPrint m--margin-left-25",
            action: function (_e, dt, _node, _conf) {
                const temp = vmPayslip.request;
                const tempData = dt.data();
                if (typeof tempData !== "undefined" && tempData.length > 0) {
                    let ids = [];
                    $.each(tempData, function (i, v) { ids.push(v.id); });
                    triggerPrintableNetPay(ids, temp);
                }
            }
        }, {
            text: '<i class="fa fa-print"></i><span class="m--font-boldest">PRINT ACKNOWLEDGEMENT</span>',
            className: "pull-right printPayslipAction btnPrint",
            action: function (_e, dt, _node, _conf) {
                const tempData = dt.data();
                if (typeof tempData !== "undefined" && tempData.length > 0) {
                    let ids = [];
                    $.each(tempData, function (i, v) { ids.push(v.id); });
                    triggerPrintableAknowledgement(ids);
                }
            }
        }, {
            text: '<i class="fa fa-print"></i><span class="m--font-boldest">PRINT ALL</span>',
            className: "pull-right printPayslipAction btnPrint",
            action: function (_e, dt, _node, _conf) {
                const tempData = dt.data();
                if (typeof tempData !== "undefined" && tempData.length > 0) {
                    let ids = [];
                    $.each(tempData, function (i, v) { ids.push(v.id); });
                    triggerPrintable(ids);
                }
            }
        }, {
            text: '<i class="fa fa-print"></i><span class="m--font-boldest">PRINT SELECTED</span>',
            className: "pull-right printPayslipSelectedAction btnPrint",
            action: function (_e, dt, _node, _conf) {
                const tempCheckbox = $(dt.body()).find("input[type='checkbox']:checked");
                if (typeof tempCheckbox !== "undefined" && tempCheckbox.length > 0) {
                    let ids = [];
                    $.each(tempCheckbox, function (i, v) {
                        const checkedValue = $(v).val();
                        ids.push(checkedValue);
                    });
                    triggerPrintable(ids);
                }
            }
        }, {
            text: '<i class="fa fa-print"></i><span class="m--font-boldest">PRINT OPTION</span>',
            titleAttr: 'Print Option for Retiree, Local Hires and Pavers',
            className: "pull-right printPayslipOptionAction btnPrint",
            action: function (_e, dt, _node, _conf) {
                const tempCheckbox = $(dt.body()).find("input[type='checkbox']:checked");
                if (typeof tempCheckbox !== "undefined" && tempCheckbox.length > 0) {
                    let ids = [];
                    $.each(tempCheckbox, function (i, v) {
                        const checkedValue = $(v).val();
                        ids.push(checkedValue);
                    });
                    triggerPrintableOption(ids);
                }
            }
        }],
        ajax: {
            url: baseUrl("payroll/get_payroll_payslip_table_request"),
            type: "POST",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.ps_ids = dtPayrollIds;
                return d;
            },
            dataType: "JSON",
        },
        pageLength: 10,
        columns: [
            {
                data: null,
                width: "3%",
                orderable: false,
                className: "text-center",
                render: function (data, type, row) {
                    return `<label class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                            <input type="checkbox" name="selected[]" value="${row.id}"><span></span>
                        </label>`;
                }
            },
            {
                data: "lastname",
                render: function (data, type, row) {
                    const mi = row.middlename.toLowerCase() !== "n/a" && row.middlename !== "" && row.middlename.toLowerCase() !== "none" ? row.middlename.substring(0, 1) + ". " : "";
                    const suffix = row.suffix.toLowerCase() !== "n/a" && row.suffix !== "" && row.suffix.toLowerCase() !== "none" ? row.suffix : "";
                    const complete_name = data + ", " + row.firstname + " " + suffix + " " + mi;
                    return `<span class="m--font-bolder">${complete_name}</span>`;
                }
            },
            {
                data: "rate", // rate
                className: "text-right",
                render: function (data) {
                    return numberFormat(data);
                }
            },
            {
                data: "no_of_days",
                className: "text-center",
                render: function (data) {
                    return formatNumber(data);
                }
            },
            /*** {
                data: "total_minutes_worked",
                className: "text-right",
                render: function (data) {
                    data = data / 60;
                    return formatNumber(data);
                }
            }, ***/
            {
                data: "basic_rate",
                className: "text-right",
                render: function (data) {
                    return numberFormat(data);
                }
            },
            /*** data: "total_undertime_amount", // undertime, // unrendered ***/
            {
                data: "total_unrendered_amount",
                className: "text-right",
                render: function (data) {
                    return numberFormat(data);
                }
            },
            {
                data: "ot_amount", // OT
                className: "text-right",
                render: function (data, type, row) {
                    return numberFormat(data);
                }
            },
            {
                data: "ot_ndiff_amount", // n_diff
                className: "text-right",
                render: function (data, type, row) {
                    return numberFormat(data);
                }
            },
            {
                data: "total_holiday_amount", // holidays
                className: "text-right",
                render: function (data) {
                    return numberFormat(data);
                }
            },
            {
                data: "total_ndiff_amount", // reg_n_diff
                className: "text-right",
                render: function (data, type, row) {
                    return numberFormat(data);
                }
            },
            {
                data: "custom_adjustments", // adjustment
                width: "8%",
                orderable: false,
                className: "text-right",
                render: function (data, type, row) {
                    if (!data) {
                        return `---`;
                    }

                    let template = ``;
                    let total_custom_adjustments = 0;
                    const custom_adjustments = data.split(",");
                    custom_adjustments.forEach((row, i) => {
                        const custom_adjustment = row.split("||");
                        const marginClass = i > 0 ? "mt-1" : "";
                        const dividerClass = custom_adjustments.length === (i + 1) ? "custom-adjustment-total-divider" : "";
                        const adj_type = parseInt(custom_adjustment[2]);
                        const adjTypeClass = adj_type === 0 ? "m--font-danger" : "";

                        template += `<div class="mb-0 m--regular-font-size-sm1 m--font-bolder ${marginClass}">
                                        <span>${custom_adjustment[0]}</span>
                                        <span> - </span>                                        
                                        <span class="m--font-boldest ${adjTypeClass}">${parseFloat(custom_adjustment[1]).toLocaleString('en-US', { maximumFractionDigits: 2 })}</span>
                                     </div>`;
                    });

                    return template;
                }
            },
            {
                data: "total_allowances", // allowances
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
                data: "gross_pay", // gross pay
                className: "text-right",
                render: function (data) {
                    return numberFormat(data);
                }
            },
            {
                data: "sss", // sss
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

                            if (temp_adjustment[0] == "SSS" && temp_status === 0) {
                                template = `<div class="mb-0 m--font-bolder m--font-accent">
                                    <span class='fa fa-exclamation-circle'></span>                                       
                                    <span class="m--font-boldest">${tempData}</span>
                                </div>`;
                            }
                            if (temp_adjustment[0] == "SSS" && temp_status === 1) {
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
                data: "sss_prov", // sss
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

                            if (temp_adjustment[0] == "SSS_PROV" && temp_status === 0) {
                                template = `<div class="mb-0 m--font-bolder m--font-accent">
                                    <span class='fa fa-exclamation-circle'></span>                                       
                                    <span class="m--font-boldest">${tempData}</span>
                                </div>`;
                            }
                            if (temp_adjustment[0] == "SSS_PROV" && temp_status === 1) {
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
                data: "ph", // phic
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

                            if (temp_adjustment[0] == "PHIC" && temp_status === 0) {
                                template = `<div class="mb-0 m--font-bolder m--font-accent">
                                    <span class='fa fa-exclamation-circle'></span>                                       
                                    <span class="m--font-boldest">${tempData}</span>
                                </div>`;
                            }
                            if (temp_adjustment[0] == "PHIC" && temp_status === 1) {
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
                data: "hdmf", // hdmf
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

                            if (temp_adjustment[0] == "HDMF" && temp_status === 0) {
                                template = `<div class="mb-0 m--font-bolder m--font-accent">
                                    <span class='fa fa-exclamation-circle'></span>                                       
                                    <span class="m--font-boldest">${tempData}</span>
                                </div>`;
                            }
                            if (temp_adjustment[0] == "HDMF" && temp_status === 1) {
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
                data: "tax", // tax
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

                            if (temp_adjustment[0] == "TAX" && temp_status === 0) {
                                template = `<div class="mb-0 m--font-bolder m--font-accent">
                                    <span class='fa fa-exclamation-circle'></span>                                       
                                    <span class="m--font-boldest">${tempData}</span>
                                </div>`;
                            }
                            if (temp_adjustment[0] == "TAX" && temp_status === 1) {
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
                data: "total_loans", // cash advance
                className: "text-right",
                render: function (data, type, row) {
                    const tempData = numberFormat(data);
                    let template = ``;
                    template = tempData;
                    let approvedAmount = parseFloat(data);

                    // added deduction of loan interest
                    const intDeduction = row.total_loans_interest;
                    if(typeof intDeduction !== "undefined" && parseFloat(intDeduction) > 0){
                        let tempAmountCAInt = parseFloat(intDeduction);

                        approvedAmount = approvedAmount + tempAmountCAInt;
                    }
                    // added deduction of loan interest

                    const tempCreatedAdjustments = row.created_adjustments;
                    if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                        const created_adjustments = tempCreatedAdjustments.split(",");
                        var tempAdj = 0;
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

                            if (temp_adjustment[0] == "LOAN" && temp_status === 0) {
                                template = `<div class="mb-0 m--font-bolder m--font-accent">
                                    <span class='fa fa-exclamation-circle'></span>                                       
                                    <span class="m--font-boldest">${tempData}</span>
                                </div>`;
                            }
                            if (temp_adjustment[0] == "LOAN" && temp_status === 1) {
                                approvedAmount = tempAdj;
                                template = `<div class="mb-0 m--font-bolder m--font-primary">                                     
                                    <span class="m--font-boldest">${temp_amount}</span>
                                </div>`;
                            }
                        });
                    }

                    // deducted charges to total loans as this column is for cash advance only
                    const tempDeduction = row.sss_hdmf_loan_deduction;
                    if (typeof tempDeduction !== "undefined" && tempDeduction) {
                        const deductions = tempDeduction.split(",");
                        deductions.forEach((row, i) => {
                            const custom_deduction = row.split("||");
                            const marginClass = i > 0 ? "mt-1" : "";
                            const _adj_type = parseInt(custom_deduction[2]);
                            const _adj_details = custom_deduction[0];

                            if ((_adj_type === 0 && _adj_details.toLowerCase() == 'chrge') || (_adj_type === 0 && _adj_details.toLowerCase() == 'ud')) {
                                approvedAmount = approvedAmount - custom_deduction[1];
                            }
                            template = approvedAmount <= 0 ? 0 : numberFormat(approvedAmount);
                        });
                    }
                    // deducted charges to total loans as this column is for cash advance only

                    return template;
                }
            },
            {
                data: null, // loans
                className: "text-right",
                render: function (data, type, row) {
                    let template = ``;
                    const tempDeduction = row.sss_hdmf_loan_deduction;
                    let charge = 0;
                    if (typeof tempDeduction !== "undefined" && tempDeduction) {
                        const deductions = tempDeduction.split(",");
                        deductions.forEach((row, i) => {
                            const custom_deduction = row.split("||");
                            const adj_type = parseInt(custom_deduction[2]);
                            const _adj_details = custom_deduction[0];
                            if ((adj_type === 0 && _adj_details.toLowerCase() == 'chrge') || (adj_type === 0 && _adj_details.toLowerCase() == 'ud')) {
                                const deductionDetails = custom_deduction[0].toUpperCase();
                                charge = parseFloat(charge) + parseFloat(custom_deduction[1]);
                            }
                        });
                    }

                    return numberFormat(charge);
                }
            },
            {
                data: "sss_loan", // sss loan
                className: "text-right",
                render: function (data) {
                    return numberFormat(data);
                }
            },
            {
                data: "hdmf_loan", // hdmf loan
                className: "text-right",
                render: function (data) {
                    return numberFormat(data);
                }
            },
            {
                data: "net_pay", // net pay
                className: "text-right",
                render: function (data) {
                    return numberFormat(data);
                }
            },
            {
                data: null,
                width: "4%",
                orderable: false,
                className: "text-center",
                render: function (data, type, row) {
                    const mi = row.middlename.toLowerCase() !== "n/a" && row.middlename !== "" && row.middlename.toLowerCase() !== "none" ? row.middlename.substring(0, 1) + ". " : "";
                    const suffix = row.suffix.toLowerCase() !== "n/a" && row.suffix !== "" && row.suffix.toLowerCase() !== "none" ? row.suffix : "";
                    const complete_name = row.firstname + " " + mi + " " + " " + row.lastname + " " + suffix;
                    const dateRange = row.date_start + "_" + row.date_end;
                    const isBonus = parseInt(row.is_bonus) == 1;

                    let viewTimesheet = ``;
                    if (parseInt(row.is_bonus) == 0) {
                        viewTimesheet = `<li class="m-nav__item">
                            <a href="javascript:void(0)" class="m-nav__link"
                                onclick="viewTimesheet(${row.emp_id}, '${complete_name}', '${dateRange}')">
                                <i class="m-nav__link-icon fa fa-clock-o"></i>
                                <span class="m-nav__link-text">VIEW TIMESHEET</span>
                            </a>
                        </li>`;
                    }

                    return `<div class="m-dropdown m-dropdown--inline m-dropdown--align-right m-dropdown--large" 
                            data-dropdown-toggle="click" aria-expanded="true">
                        <a href="#" class="m-dropdown__toggle btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill"
                            data-toggle="m-tooltip" data-original-title="More Options" data-skin="dark"
                            data-delay='{"show": 500}'>
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                        <div class="m-dropdown__wrapper">
                            <span class="m-dropdown__arrow m-dropdown__arrow--right"></span>
                            <div class="m-dropdown__inner">
                                <div class="m-dropdown__body">
                                    <div class="m-dropdown__content">
                                        <ul class="m-nav">
                                            <li class="m-nav__section m-nav__section--first">
                                                <span class="m-nav__section-text">OPTIONS</span>
                                            </li>
                                            ${viewTimesheet}
                                            <li class="m-nav__item">
                                                <a href="javascript:void(0)" class="m-nav__link"
                                                    onclick="viewPayslip(${row.id})">
                                                    <i class="m-nav__link-icon flaticon-coins"></i>
                                                    <span class="m-nav__link-text">VIEW PAYSLIP</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                }
            }
        ],
        order: [[1, "asc"]],
        drawCallback: function (settings) {
            var api = this.api();
            var btnPrintSelected = $(settings.nTableWrapper).find(".printPayslipSelectedAction");
            var btnPrint = $(settings.nTableWrapper).find(".printPayslipAction");
            var btnPrintOption = $(settings.nTableWrapper).find(".printPayslipOptionAction");
            if (typeof btnPrint !== "undefined") {
                btnPrint.addClass("btn m-btn btn-brand m-btn--icon m--hide animated fadeIn m--margin-left-5");
                var tempData = api.data();
                if (tempData.length > 0) {
                    if (btnPrint.hasClass("m--hide") == true) { btnPrint.removeClass("m--hide"); }
                } else {
                    if (btnPrint.hasClass("m--hide") == false) { btnPrint.addClass("m--hide"); }
                }
            }
            if (typeof btnPrintSelected !== "undefined") {
                btnPrintSelected.addClass("btn m-btn btn-success m-btn--icon m--hide animated fadeIn m--margin-left-5");
                var tempData = api.data();
                if (tempData.length > 0) {
                    if (btnPrintSelected.hasClass("m--hide") == true) { btnPrintSelected.removeClass("m--hide"); }
                } else {
                    if (btnPrintSelected.hasClass("m--hide") == false) { btnPrintSelected.addClass("m--hide"); }
                }
                var checkedItems = $(settings.nTBody).find("input[type='checkbox']:checked");
                if (checkedItems.length > 0) { btnPrintSelected.prop("disabled", false); }
                else { btnPrintSelected.prop("disabled", true); }
            }

            var checkBoxes = $(settings.nTBody).find("input[type='checkbox']");
            if (typeof checkBoxes !== "undefined" && checkBoxes.length > 0) {
                checkBoxes.on("click", function (e) {
                    redrawPayslipTable();
                });
            }

            if (typeof btnPrintOption !== "undefined") {
                btnPrintOption.addClass("btn m-btn btn-metal text-white m-btn--icon m--hide animated fadeIn");
                var tempData = api.data();
                if (tempData.length > 0) {
                    if (btnPrintOption.hasClass("m--hide") == true) { btnPrintOption.removeClass("m--hide"); }
                } else {
                    if (btnPrintOption.hasClass("m--hide") == false) { btnPrintOption.addClass("m--hide"); }
                }
                var checkedItems = $(settings.nTBody).find("input[type='checkbox']:checked");
                if (checkedItems.length > 0) { btnPrintOption.prop("disabled", false); }
                else { btnPrintOption.prop("disabled", true); }
            }

        }, footerCallback: function (row, data, start, end, display) {
            var api = this.api(), data;
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };

            let totalNet = api
                .column(22)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            $(api.column(1).footer()).html("<span class='m--font-boldest'>&nbsp;&nbsp;GRANDTOTAL</span>");
            $(api.column(22).footer()).html("<span class='m--font-boldest'>&#8369;&nbsp;&nbsp;" + numberFormat(totalNet) + "</span>");
        }
    });

    cbSelectAll.on('change', function (e) {
        let checkedValue = e.target.checked;
        $('tbody input[type=\'checkbox\']', dtPayrollPayslip).prop('checked', checkedValue);
        dtPayslipTable.draw();
    });
}

function redrawPayslipTable() { return dtPayslipTable.draw(); }
function viewPayslip(rowId) {
    if (rowId) {
        $.ajax({
            url: siteUrl("payroll/get_current_payroll_payslip/" + rowId),
            dataType: "json",
            success: function (json) {
                vmPayslipContent.row = Object.assign({});
                vmPayslipContent.ot_computation = Object.assign({});
                if (json.response) {
                    vmPayslipContent.row = Object.assign({}, json.data);
                    var data = json.data;
                    var totalOT = parseFloat(vmPayslipContent.row.ot_amount) + parseFloat(vmPayslipContent.row.ot_ndiff_amount);
                    var totalOTHrs = (parseFloat(vmPayslipContent.row.ot_minutes) + parseFloat(vmPayslipContent.row.ot_ndiff_minutes)) / 60;

                    vmPayslipContent.total_ot_hrs = numberFormat(totalOTHrs);
                    vmPayslipContent.ot_hrs = numberFormat(parseFloat(vmPayslipContent.row.ot_minutes)/60);
                    vmPayslipContent.ot_computation = numberFormat(totalOT);
                    vmPayslipContent.ot_ndiff_hrs = numberFormat(parseFloat(vmPayslipContent.row.ot_ndiff_minutes) / 60);
                    vmPayslipContent.ot_ndiff_computation = numberFormat(parseFloat(vmPayslipContent.row.ot_ndiff_amount));
                    
                    vmPayslipContent.total_ndiff_hrs = numberFormat(parseFloat(vmPayslipContent.row.total_ndiff_minutes) / 60);
                    vmPayslipContent.total_ndiff_computation = numberFormat(parseFloat(vmPayslipContent.row.total_ndiff_amount));

                    let tempLoan = [];
                    let totalLoan = parseFloat(vmPayslipContent.row.totalLoan.replace(/,/g, ''));
                    let totalDeduction = 0;
                    let totalOthersDeductions = 0;
                    let overAllTotal = 0;
                    const tempCreatedAdjustments = data.created_adjustments;

                    if (data.sss && parseFloat(data.sss) > 0) {
                        totalDeduction = totalDeduction + parseFloat(data.sss.replace(/,/g, ''));
                    }

                    if (data.sss_prov && parseFloat(data.sss_prov) > 0) {
                        totalDeduction = totalDeduction + parseFloat(data.sss_prov.replace(/,/g, ''));
                    }
                    
                    if (data.ph && parseFloat(data.ph) > 0) {
                        totalDeduction = totalDeduction + parseFloat(data.ph.replace(/,/g, ''));
                    }
                    
                    if (data.hdmf && parseFloat(data.hdmf) > 0) {
                        totalDeduction = totalDeduction + parseFloat(data.hdmf.replace(/,/g, ''));
                    }
                    
                    if (data.tax && parseFloat(data.tax) > 0) {
                        totalDeduction = totalDeduction + parseFloat(data.tax.replace(/,/g, ''));
                    }

                    if (json.data.loans.length > 0) {
                        $.each(json.data.loans, function (index, item) {
                            if (item.loan_name.toLowerCase() != 'charges' && item.loan_name.toLowerCase() != 'under deduction' && item.loan_name.toLowerCase() != 'medical loan') {
                                var temp_amount = parseFloat(item.amount_due.replace(/,/g, ''));
    
                                // for adding cash advance with loan adjustments
                                if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                                    const created_adjustments = tempCreatedAdjustments.split(",");
                                    var tempAdj = 0;
                                    created_adjustments.forEach((row, i) => {
                                        const temp_adjustment = row.split("||");
                                        const adj_type = parseInt(temp_adjustment[2]);
                                        const temp_status = parseInt(temp_adjustment[3]);
                                        let _temp = parseFloat(item.amount_due);
                                        if (adj_type == 1) {
                                            _temp = parseFloat(temp_amount) + parseFloat(temp_adjustment[1]);
                                        } else {
                                            _temp = parseFloat(temp_amount) - parseFloat(temp_adjustment[1]);
                                        }

                                        tempAdj = _temp;
                                        _temp = _temp;

                                        if (typeof item.loan_name !== "undefined" && item.loan_name.toLowerCase() == 'cash advance') {
                                            if (temp_adjustment[0] == "LOAN" && temp_status === 1) {
                                                temp_amount = _temp;
                                            }
                                        } else {
                                            // includes loan adjustments when employee has no cash advance
                                            if (temp_adjustment[0] == "LOAN" && temp_status === 1) {
                                                if (!tempLoan.some(el => el.loan_name === 'CASH ADVANCE')) {
                                                    tempLoan.push({
                                                        'loan_name' : 'CASH ADVANCE',
                                                        'amount_due' : temp_adjustment[1],
                                                        'loan_type' : adj_type
                                                    });
                                                }
                                            }
                                        }
                                    });
                                }
    
                                tempLoan.push({
                                    'loan_name' : item.loan_name,
                                    'amount_due' : numberFormat(temp_amount),
                                    'loan_type' : item.loan_type
                                });
                            }
    
                            // for adding the charges to Other Deductions
                            if (item.loan_name.toLowerCase() == 'charges' || item.loan_name.toLowerCase() == 'under deduction' || item.loan_name.toLowerCase() == 'medical loan') {
                                vmPayslipContent.row.adjustment_deductions.push({
                                    'label' : item.loan_name,
                                    'display_value' : item.amount_due,
                                    'value' : item.amount_due,
                                    'adj_type' : 0
                                });
    
                                totalLoan = totalLoan - parseFloat(item.amount_due.replace(/,/g, ''));
                            }
                        });
                    } else {
                        if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                            const created_adjustments = tempCreatedAdjustments.split(",");
                            var tempAdj = 0;
                            created_adjustments.forEach((row, i) => {
                                const temp_adjustment = row.split("||");
                                const adj_type = parseInt(temp_adjustment[2]);
                                const temp_status = parseInt(temp_adjustment[3]);
                                let _temp = parseFloat(temp_adjustment[1]);
    
                                tempAdj = _temp;
                                _temp = formatNumber(_temp);
    
                                if (temp_adjustment[0] == "LOAN" && temp_status === 1) {
                                    tempLoan.push({
                                        'loan_name' : 'cash advance',
                                        'amount_due' : _temp,
                                        'loan_type' : adj_type
                                    });
                                }
                            });
                        }
                    }

                    $.each(vmPayslipContent.row.adjustment_deductions, function (index, item) {
                        totalOthersDeductions = totalOthersDeductions + parseFloat(item.display_value.replace(/,/g, ''));
                    });

                    overAllTotal = parseFloat(totalDeduction) + parseFloat(totalLoan) + parseFloat(totalOthersDeductions) + parseFloat(vmPayslipContent.row.total_loans_interest);

                    vmPayslipContent.row.loans = tempLoan;
                    vmPayslipContent.row.totalLoan = numberFormat(totalLoan);
                    vmPayslipContent.row.total_allowances = numberFormat(vmPayslipContent.row.total_allowances);
                    vmPayslipContent.row.deductions = numberFormat(totalDeduction);
                    vmPayslipContent.row.total_others_deductions = numberFormat(totalOthersDeductions);
                    vmPayslipContent.row.overall_total_deductions = numberFormat(overAllTotal);
                    vmPayslipContent.row.adjustment_d_count = vmPayslipContent.row.adjustment_deductions.length;

                    vmPayslipContent.raw_tl = parseFloat(totalLoan);
                    vmPayslipContent.raw_tod = parseFloat(totalOthersDeductions);
                    vmPayslipContent.raw_tli = parseFloat(vmPayslipContent.row.total_loans_interest);
                    viewPayrollPayslipModal.modal("show");
                }
            }
        });
    }
}

function viewTimesheet(emp_id, employee_name, date_range) {
    viewTimesheetModal.attr("data-emp_id", emp_id);
    viewTimesheetModal.attr("data-employee_name", employee_name);
    viewTimesheetModal.attr("data-date_range", date_range);
    viewTimesheetModal.modal("show");
}

viewTimesheetModal.on("show.bs.modal", function () {
    const emp_id = $(this).attr("data-emp_id");
    let tempDateRange = $(this).attr("data-date_range");
    tempDateRange = tempDateRange.replace(/-/g, "/");
    tempDateRange = tempDateRange.replace(/_/g, "-");

    dtEmployeeTimesheet = $("table", this).DataTable({
        dom: 'rtlp',
        serverSide: false,
        autoWidth: false,
        pageLength: 15,
        destroy: true,
        buttons: [
            {
                extend: 'print',
                text: 'PRINT',
                title: function () {
                    const date_range = tempDateRange.split("-");
                    return `<div class="m--regular-font-size-lg4">
                                <span class="m--font-boldest">${viewTimesheetModal.attr("data-employee_name")}</span> | 
                                DATED: <span class="m--font-boldest">${date_range[0]}</span> - <span class="m--font-boldest">${date_range[1]}</span>
                            </div>`;
                },
            }
        ],
        ajax: {
            url: baseUrl(`payroll/get_employee_timesheet`),
            type: "POST",
            dataType: "JSON",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.emp_id = emp_id;
                /*** d.date_range = $('input[name="date_range"]', form).val(); ***/
                d.date_range = tempDateRange;
            }
        },
        columns: [
            {
                data: 'date',
                render: function (data, type, row, meta) {
                    let if_holiday;
                    let overtime = " <em class='fa fa-clock-o ml-1' style='color:#5867dd;'></em>";
                    let holiday = " <em class='fa fa-flag ml-1' style='color:#ffb822;'>";
                    let absent = " <em class='fa fa-times-rectangle ml-1' style='color:#5c5d62;'>";
                    if (row.id == null && row.is_holiday == null && row.weekday != 'sunday') {
                        if_holiday = moment(data).format("MM/DD/YYYY") + absent;
                    } else {
                        if (row.is_holiday == 1 && row.total_accredited_ot_hrs > 0) {
                            if_holiday = moment(data).format("MM/DD/YYYY") + holiday + overtime;
                        } else if (row.is_holiday == 1) {
                            if_holiday = moment(data).format("MM/DD/YYYY") + holiday;
                        } else if (row.total_accredited_ot_hrs > 0) {
                            if_holiday = moment(data).format("MM/DD/YYYY") + overtime;
                        } else {
                            if_holiday = moment(data).format("MM/DD/YYYY");
                        }
                    }

                    return if_holiday;
                },
                width: "12%",
            },
            {
                data: 'weekday',
                orderable: false,
                render: function (data) {
                    return data.substring(0, 3);
                },
                className: "text-center",
                width: "8%",
            },
            {
                data: "am_in",
                className: "text-center",
                width: "10%",
                render: function (data) {
                    if (!data) {
                        return ``;
                    }

                    return moment(data, "hh:mm:ss").format("hh:mm A");
                }
            },
            {
                data: "am_out",
                className: "text-center",
                width: "10%",
                render: function (data) {
                    if (!data) {
                        return ``;
                    }

                    return moment(data, "hh:mm:ss").format("hh:mm A");
                }
            },
            {
                data: "pm_in",
                className: "text-center",
                width: "10%",
                render: function (data) {
                    if (!data) {
                        return ``;
                    }

                    return moment(data, "hh:mm:ss").format("hh:mm A");
                }
            },
            {
                data: "pm_out",
                className: "text-center",
                width: "10%",
                render: function (data) {
                    if (!data) {
                        return ``;
                    }

                    return moment(data, "hh:mm:ss").format("hh:mm A");
                }
            },
            {
                data: "total_late",
                className: "text-center",
                width: "10%",
                render: function (data, type, row) {
                    if (parseFloat(row.total_time_rendered) <= 0) {
                        return 0;
                    }

                    if (parseInt(data) > 0) {
                        return `<span class="m--font-danger m--font-boldest">${data}</span>`;
                    }

                    return data;
                }
            },
            {
                data: "total_ut",
                className: "text-center",
                width: "10%",
                render: function (data, type, row) {
                    if (parseFloat(row.total_time_rendered) <= 0) {
                        return 0;
                    }

                    if (parseInt(data) > 0) {
                        return `<span class="m--font-danger m--font-boldest">${data}</span>`;
                    }

                    return data;
                }
            },
            {
                data: "total_time_rendered",
                className: "text-center",
                width: "12%",
                render: function (data, type, row) {
                    if (data && parseFloat(data) > 0) {
                        const hrs = parseFloat(data) / 60;
                        return hrs.toLocaleString("en-US", { maximumFractionDigits: 2 });
                    }

                    return data;
                }
            },
            {
                data: "total_accredited_ot_hrs",
                className: "text-center",
                width: "8%",
            },
            {
                data: "total_accredited_ndiff_ot_hrs",
                className: "text-center",
                width: "8%",
            },
        ],
        lengthMenu: [[15, 30, 50, -1], [15, 30, 50, "All"]]
    });
});

function formatNumber(value, decimals = 2) {
    return parseFloat(value).toLocaleString("en-US", { maximumFractionDigits: decimals });
}

function printTimesheet(el) {
    $("i", el).removeClass();
    $("i", el).addClass("fa fa-spinner fa-spin");
    $("i", el).css({ right: 0, left: 0 });

    setTimeout(() => {
        dtEmployeeTimesheet.button(".buttons-print").trigger();
        $("i", el).removeClass("fa fa-spinner fa-spin").addClass("fa fa-print");
        $("i", el).css({ top: "50%", left: "50%" });
    }, 150);
}

function triggerPrintableNetPay(ids = [], temp = {}) {
    if (ids.length > 0) {
        $.ajax({
            url: baseUrl("payroll/set_printable_payslip_netpay"),
            type: "post",
            dataType: "json",
            data: { csrf_token: _csrf_hash, ids: ids, paramaters: temp },
            success: function (json) {
                if (json.response) {
                    const tempHtml = json.html;
                    const printWindow = window.open(siteUrl('payroll/reports/printable_form'), '_blank');
                    printWindow.focus();
                    printWindow.onload = function(){
                        const printableContainer = printWindow.document.getElementById('append_printable-container');
                        if (printableContainer) {
                            printableContainer.innerHTML = tempHtml;
                            setTimeout(() => {
                                printWindow.print();
                                printWindow.close();
                            }, 250);
                        } else {
                            toastr.info('Print detail(s) is still in progress!', 'Payroll / Payslip Neypay');
                            printWindow.close();
                        }
                    }
                }
            }
        });
    } else {
        return false;
    }
}

function triggerPrintableAknowledgement(ids = []) {
    if (ids.length > 0) {
        $.ajax({
            url: baseUrl("payroll/set_printable_payslip_aknowledgement"),
            type: "post",
            dataType: "json",
            data: { csrf_token: _csrf_hash, ids: ids },
            success: function (json) {
                if (json.response) {
                    const tempHtml = json.html;
                    const printWindow = window.open(siteUrl('payroll/reports/printable_form'), '_blank');
                    printWindow.focus();
                    printWindow.onload = function(){
                        const printableContainer = printWindow.document.getElementById('append_printable-container');
                        if (printableContainer) {
                            printableContainer.innerHTML = tempHtml;
                            setTimeout(() => {
                                printWindow.print();
                                printWindow.close();
                            }, 250);
                        } else {
                            toastr.info('Print detail(s) is still in progress!', 'Payroll / Payslip Aknowledgement');
                            printWindow.close();
                        }
                    }
                }
            }
        });
    } else {
        return false;
    }
}

function triggerPrintable(ids = []) {
    if (ids.length > 0) {
        $.ajax({
            url: baseUrl("payroll/set_printable_payslip"),
            type: "post",
            dataType: "json",
            data: { csrf_token: _csrf_hash, ids: ids },
            success: function (json) {
                let setPrintIds = [];
                if (json.response) {
                    const tempHtml = json.html;
                    const printWindow = window.open(siteUrl('payroll/reports/printable_form'), '_blank');
                    printWindow.focus();
                    printWindow.onload = function(){
                        const printableContainer = printWindow.document.getElementById('append_printable-container');
                        if (printableContainer) {
                            printableContainer.innerHTML = tempHtml;
                            setTimeout(() => {
                                printWindow.print();
                                printWindow.close();
                            }, 250);
                        } else {
                            toastr.info('Print detail(s) is still in progress!', 'Payroll / Payslip');
                            printWindow.close();
                        }
                    }

                   printWindow.onbeforeprint = function (e) { setPrintIds = ids; }
                    printWindow.onafterprint = function () {
                        $.ajax({ 
                            url: siteUrl("payroll/update_payrollsheet_printed_status"),
                            type: "post",
                            dataType: "json",
                            data: { csrf_token: _csrf_hash, printed_id: setPrintIds },
                            success: function (json) {
                              if (json.response) {
                                    const { printed_id } = json.data;
                                    swalAlertNotification(printed_id);
                                }
                            }
                        });
                    }
                }
            }
        });
    } else {
        return false;
    }
}


const swalAlertNotification = function (printIds = []) {
    if(printIds.length > 0){
        Swal.fire({
            title: 'Send Payslip via Telegram/Email?',
            text: 'Do you want to send the payslip via Telegram/Email?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, send it!',
            cancelButtonText: 'No, cancel',
        }).then((result) => {
        if (result.isConfirmed) {
            $.when(
                $.ajax({
                    url: siteUrl("payroll/send_telegram"),
                    type: 'post',
                    data: {
                        csrf_token: _csrf_hash,
                        payslipId: printIds,
                    }
                }),
                $.ajax({
                    url: siteUrl("payroll/send_email"),
                    type: 'post',
                    data: {
                        csrf_token: _csrf_hash,
                        payslipId: printIds,
                    }
                })
            ).done(function() {
                Swal.fire(
                    'Sent!',
                    'Payslip has been sent via Telegram and Email.',
                    'success'
                );
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire(
                    'Cancelled',
                    'Payslip sending via Telegram and Email was cancelled.',
                    'error'
                );
            }
        });
    }
}

function triggerPrintableOption(ids = []) {
    if (ids.length > 0) {
        Swal.fire({
            title: 'Print Payslip?',
            html: 'You are about to print a different payslip layout for <strong>Retirees</strong>, <strong>Local Hires</strong> and <strong>Pavers</strong>. Would you like to proceed?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, print it!',
            cancelButtonText: 'No, cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: baseUrl("payroll/set_printable_payslip_option"),
                    type: "post",
                    dataType: "json",
                    data: { csrf_token: _csrf_hash, ids: ids },
                    success: function (json) {
                        let setPrintIds = [];
                        if (json.response) {
                            const tempHtml = json.html;
                            const printWindow = window.open(siteUrl('payroll/reports/printable_form'), '_blank');
                            printWindow.focus();
                            printWindow.onload = function(){
                                const printableContainer = printWindow.document.getElementById('append_printable-container');
                                if (printableContainer) {
                                    printableContainer.innerHTML = tempHtml;
                                    setTimeout(() => {
                                        printWindow.print();
                                        printWindow.close();
                                    }, 200);
                                } else {
                                    toastr.info('Print detail(s) is still in progress!', 'Payroll / Payslip Option');
                                    printWindow.close();
                                }
                            }

                            printWindow.onbeforeprint = function (e) { setPrintIds = ids; }
                            printWindow.onafterprint = function () {
                                $.ajax({ 
                                    url: siteUrl("payroll/update_payrollsheet_printed_status"),
                                    type: "post",
                                    dataType: "json",
                                    data: { csrf_token: _csrf_hash, printed_id: setPrintIds },
                                    success: function (json) {
                                        if (json.response) {
                                            const { printed_id } = json.data;
                                            swalAlertNotification(printed_id);
                                        }else{
                                            toastr.warning(json.toastr_msg, 'Payroll / Payslip Option');
                                        }
                                    }
                                });
                            }
                        }
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire(
                    'Cancelled',
                    'Payslip printing was cancelled.',
                    'error'
                );
            }
        });
    } else {
        return false;
    }
}
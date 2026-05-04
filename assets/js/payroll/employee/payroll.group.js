const tablePayrollGroup = $("#table-payroll_group");
let dtPayrollGroup, search_val;
const btnNewEmployeeGroup = $("#btnNewEmployeeGroup");
const documentModal = $("#documentModal");
let propAllFilter = false;
const modalTransferGroup = $("#modalTransferGroup");
const formTransferGroup = $("#formTransferGroup");
const tableTransferApproval = $("#tableTransferApproval");
const tableTransferHistory = $("#tableTransferHistory");

let dtTableApproval, dtTableHistory;

let _forApproval = [];
let _transferHistory = [];
let _companies = [];
let _globalLockedEmployees = { id: [], employees: {} };
let _payoutSched = {};
let _payment_mode = {};

const notificationCounter = new Vue({
    el: "#notificationCounter",
    data: { count: 0, notification_clicked: false },
    methods: {
        toggleClicked: function () {
            if (this.notification_clicked === false) { this.notification_clicked = true; }
            toastr.clear();
            return this.notification_clicked;
        }
    }
});

if(_tempContentData !== undefined && Object.keys(_tempContentData).length > 0){
    if(_tempContentData.company !== undefined && _tempContentData.company.length > 0){
        _companies = _tempContentData.company;
    }

    if (_tempContentData.payroll_sched !== undefined && Object.keys(_tempContentData.payroll_sched).length > 0) {
        _payoutSched = _tempContentData.payroll_sched;
    }

    if (_tempContentData.payment_mode !== undefined && _tempContentData.payment_mode.length > 0) {
        _payment_mode = _tempContentData.payment_mode;
    }
}

if(tableTransferApproval.length === 1){
    dtTableApproval = tableTransferApproval.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: false,
        processing: true,
        searching: false,
        ordering: false,
        destroy: true,
        columns: [
            { data: 'company_code', className: 'text-left', width: "10%", render: function(data){
                return `<p class='m--marginless' style='line-height: 28px; height: auto;'>
                    <span class='mr-3'>${data ?? "ALL COMPANIES" }</span>
                </p>`;
            } },
            { data: 'employees', className: 'text-left', width: "10%", render: function(data){
                let tempHtml = `<p class='m--marginless' style='line-height: 28px; height: auto;'>`;
                $.each(data, function(i, v){
                    tempHtml += `<span class='m-badge m-badge--metal m-badge--wide m-badge--rounded mr-1'>${v.employee_name}</span>`;
                });
                tempHtml += `</p>`;
                return tempHtml;
                
            } },
            { data: 'payroll_group', className: 'text-left', width: "*"  },
            { data: 'reason', className: 'text-left', width: "10%"  },
            { data: "id", width: "8%", className: 'text-center', render: function(data, _type, row){
                if(_currentActions != undefined && _currentActions.length > 0 && _currentActions.includes("approve_action")){
                    const rawData = JSON.stringify(row);
                    return `<button 
                        class="btn btn-sm btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnApprove_action btnApproveTransfer"
                        data-placement="bottom"
                        data-toggle="m-tooltip"
                        title="Approve Employee Group Transfer" data-original-title="Approve Employee Group Transfer"
                        data-row='${rawData}'><i class="fa fa-thumbs-up"></i>
                        </button> <button class="btn btn-sm btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDisapprove_action btnDisapproveTransfer"
                        data-placement="bottom"
                        data-toggle="m-tooltip"
                        title="Disapprove Employee Group Transfer" data-original-title="Disapprove Employee Group Transfer"
                        data-row='${rawData}'><i class="fa fa-thumbs-down"></i></button>`;
                }else{
                    return "---";
                }
            }}
        ]
    });

    if(_tempContentData !== undefined && Object.keys(_tempContentData).length > 0){
        if(_tempContentData.for_approval !== undefined && _tempContentData.for_approval.length > 0){
            _forApproval = _tempContentData.for_approval;
            notificationCounter.count = _forApproval.length;
            setTimeout(function () {
                dtTableApproval.clear();
                dtTableApproval.rows.add(_forApproval).draw(false);
            }, 1000);
        }
    }

    $(document).on("click", ".btnApproveTransfer", function(){
        const btnThis = $(this);
        const dataRow = btnThis.data("row");
        Swal.fire({
            icon: "question",
            title: "Employee Transfer?",
            text: "Are you sure you want to approve this employee group transfer?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Approve It!",
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: baseUrl("payroll/employee/approve_employee_group_transfer"),
                    type: "post",
                    dataType: "json",
                    data: { id: dataRow.id,
                        employee_id: dataRow.employee_id,
                        group_id: dataRow.group_id,
                        csrf_token: _csrf_hash
                    },
                    beforeSend: function () {
                        btnThis.addClass("m-loader m-loader--light m-loader--right");
                    },
                    success: function (json) {
                        if (json.response) {
                            dtTableApproval.row(btnThis.parents("tr")).remove().draw(false);
                            if(notificationCounter.count > 0) notificationCounter.count--;
                        }
                        toastr[json.response ? "success" : "error"](json.message, "Approve Employee Transfer");
                    },
                    complete: function () {
                        btnThis.removeClass("m-loader m-loader--light m-loader--right");
                    }
                });
            }
        });
    });

    $(document).on("click", ".btnDisapproveTransfer", function(){
        const btnThis = $(this);
        const dataRow = $(this).data("row");
        Swal.fire({
            icon: "question",
            title: "Employee Transfer?",
            text: "Are you sure you want to disapprove this employee group transfer?",
            input: "textarea",
            inputPlaceholder: "Reason is Required *",
            inputLabel: "Reason for Disapproval",
            customClass: { inputLabel: 'm--font-bolder required' },
            inputValidator: (result) => { return !result && "Reason for disapproval is required!"; },
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Disapprove It!",
            allowOutsideClick: false,
            allowEscapeKey: false,
            focusConfirm: false,
            target: document.querySelector('.modal.show') || document.body,
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.ajax({
                    url: baseUrl("payroll/employee/disapprove_employee_group_transfer"),
                    type: "post",
                    dataType: "json",
                    data: { id: dataRow.id,
                        employee_id: dataRow.employee_id,
                        group_id: dataRow.group_id,
                        reason: result.value,
                        csrf_token: _csrf_hash
                    },
                    beforeSend: function () {
                        btnThis.addClass("m-loader m-loader--light m-loader--right");
                    },
                    success: function (json) {
                        if (json.response) {
                            dtTableApproval.row(btnThis.parents("tr")).remove().draw(false);
                            if(notificationCounter.count > 0) notificationCounter.count--;
                        }
                        toastr[json.response ? "success" : "error"](json.message, "Disapprove Employee Transfer");
                    },
                    complete: function () {
                        btnThis.removeClass("m-loader m-loader--light m-loader--right");
                    }
                });
            }
        });
    });
}

if(tableTransferHistory.length === 1){
    dtTableHistory = $("#tableTransferHistory").DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: false,
        processing: true,
        searching: false,
        ordering: false,
        destroy: true,
        columns: [
            { data: 'company_code', className: 'text-left', width: "10%", render: function(data, _type, row){
                const tempClass = row.status_name === "Approved" ? "m-badge--success" : "m-badge--danger";
                return `<p class='m--marginless' style='line-height: 28px; height: auto;'>
                    <span class='mr-3'>${data ?? "ALL COMPANIES" }</span>
                    <span class='m-badge m-badge--wide m-badge--rounded ${tempClass}'>${row.status_name}</span></p>`;
            }},
            { data: 'employees', className: 'text-left', width: "10%", render: function(data){
                let tempHtml = `<p class='m--marginless' style='line-height: 28px; height: auto;'>`;
                $.each(data, function(i, v){
                    tempHtml += `<span class='m-badge m-badge--metal m-badge--wide m-badge--rounded mr-1'>${v.employee_name}</span>`;
                });
                tempHtml += `</p>`;
                return tempHtml;
                
            } },
            { data: 'payroll_group', className: 'text-left', width: "*", render: function(data, _type, row){
                const actionStatusAt = moment(row.action_status_at).format("LLL");
                let tempHtml = `<p class='m--marginless' style='line-height: 18px; height: auto;'>${data}</p>`;
                tempHtml += `<p class='m--marginless'><small class='m--font-boldest'>Last Updated By: ${row.action_status_by} on ${actionStatusAt}</small></p>`;
                return tempHtml;
            } },
            { data: 'reason', className: 'text-left', width: "10%"  }
        ],
    });

        if(_tempContentData !== undefined && Object.keys(_tempContentData).length > 0){
        if(_tempContentData.transfer_history !== undefined && _tempContentData.transfer_history.length > 0){
            _transferHistory = _tempContentData.transfer_history;
    
            setTimeout(function () {
                dtTableHistory.clear();
                dtTableHistory.rows.add(_transferHistory).draw(false);
            }, 1000);
        }
    }
}


if (tablePayrollGroup !== undefined) {
    dtPayrollGroup = tablePayrollGroup.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        destroy: true,
        ajax: {
            url: baseUrl("payroll/employee/get_employee_group"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                return d;
            }, error: function (xhr, error, code) {
                if (error == "parsererror") {
                    dtPayrollGroup.ajax.reload(null, false);
                }
            },
            global: false,
        },
        columns: [
            { data: "company", title: "Company", width: "12%" },
            {
                data: "employees", title: "Employee(s)", render: function (data, meta, row) {
                    let tempHtml = `<p class='m--marginless' style='line-height: 28px; height: auto;'>`;
                    $.each(data, function (i, v) {
                        tempHtml += `<span class='m-badge m-badge--metal m-badge--wide m-badge--rounded mr-1'>${v}</span>`;
                    });
                    tempHtml += `</p>`;
                    return tempHtml;
                }
            },
            { data: "description", title: "Description", width: "25%" },
            { 
                data: 'assigned_employees', title: "User Restriction", width: "20%", render: function (data, meta, row) {
                    let tempHtml = ``;

                    if (typeof data !== "undefined" && data.length > 0) {
                        tempHtml += `<p class='m--marginless' style='line-height: 28px; height: auto;'>`;
                        tempHtml += '<span class="fa fa-exclamation-circle mr-2" style="font-size: 18px; color: #36a3f7 !important;" ata-skin="dark" data-toggle="m-tooltip" data-placement="top" title="User can only view this payroll group!" data-original-title="User can only view this payroll group!"></span>';
                        $.each(data, function (i, v) {
                            tempHtml += `<span class='m-badge m-badge--metal m-badge--wide m-badge--rounded mr-1'>${v}</span>`;
                        });
                        tempHtml += `</p>`;
                    } else {
                        tempHtml += `<span class='m-badge m-badge--warning m-badge--wide m-badge--rounded m--font-boldest'>No Restriction</span>`;
                    }

                    return tempHtml;
                }
            },
            {
                data: null, title: "Status", width: "5%", className: "text-center", render: function (data, meta, row) {
                    let tempClass = typeof row.status !== "undefined" && parseInt(row.status) == 1 ? "btn-success" : "btn-danger";
                    let tempHtml = `<span class='btn ${tempClass} m-btn m-btn--icon m-btn--icon-only btn-sm'>
                        <i class='flaticon-users'></i>
                    </span>`;
                    return tempHtml;
                }
            },
            {
                data: null, title: "Action", width: "8%", className: "text-center", render: function (data, meta, row) {
                    let tempHtml = ``;
                    if (_currentActions.includes("edit")) {
                        tempHtml += `<button type="button"
                                class="btn btn-sm btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditGroup"
                                data-placement="bottom"
                                data-toggle="m-tooltip"
                                title="" data-original-title="Edit Employee Group"
                                data-url="${row.edit_url}"><i class="la la-edit"></i></button>`;
                    }
                    if (_currentActions.includes("archive")) {
                        tempHtml += `<button type="button"
                                class="btn btn-sm btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnArchive btnDeleteGroup"
                                data-placement="bottom"
                                data-toggle="m-tooltip"
                                title="" data-original-title="Archive Employee Group"
                                data-url="${row.archive_url}"><i class="la la-archive"></i></button>`;
                    }
                    return tempHtml;
                }
            },
        ], columnDefs: [{
            targets: "all",
            defaultContent: "",
        }]
    });
}

$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    dtPayrollGroup.ajax.reload(null, false);
});

btnNewEmployeeGroup.on("click", function () {
    const currentTarget = this;
    const currentUrl = $(currentTarget).data("modal");
    $.ajax({
        url: currentUrl,
        dataType: "json",
        global: false,
        beforeSend: function(){
            $(currentTarget).addClass("m-btn--custom m-loader m-loader--light m-loader--right").prop("disabled", true);
        },
        success: function (json) {
            if (json.response) {
                propAllFilter = false;
                documentModal.empty().html(json.html);
                const employeeSelect2 = documentModal.find("select#employee_id");
                const companySelect2 = documentModal.find("select#company_id");
                const companyAllFilter = documentModal.find("input#all_company_filter");
                const allowView = documentModal.find("input#allow_view");
                const assignSelect = documentModal.find('select#assign_employee_id');
                const payoutSchedSelect = documentModal.find('select#payroll_sched');
                const payoutMode = documentModal.find('select#payment_mode');
                const activeEmployeeFilter = documentModal.find("input#active_employees");

                if (typeof companyAllFilter !== "undefined" && companyAllFilter.length == 1) {
                    companyAllFilter.on("change", function (e) {
                        let isChecked = e.target.checked;
                        propAllFilter = isChecked;
                        companySelect2.prop("disabled", isChecked);
                        if (isChecked) { companySelect2.val("").trigger("change"); }
                        if (isChecked == false) { employeeSelect2.val([]).trigger("change"); }

                        allowView.prop('disabled', isChecked);
                        if (allowView.is(":checked")) { 
                            allowView.prop('checked', false); 
                            assignSelect.val([]).trigger("change"); 
                            documentModal.find('#assign_employee_div').prop("hidden", true);
                        }
                    });
                }

                if (typeof companySelect2 !== "undefined" && companySelect2.length == 1) {
                    companySelect2.select2({
                        width: "100%",
                        data: _companies,
                        placeholder: "select an option",
                        dropdownParent: documentModal,
                        allowClear: true,
                    }).on("select2:select", function () {
                        employeeSelect2.val([]).trigger("change");
                    }).on("select2:unselect", function () {
                        employeeSelect2.val([]).trigger("change");
                    });
                }

                if (typeof payoutMode !== "undefined" && payoutMode.length == 1) {
                    if (payoutMode.hasClass("select2-hidden-accessible")) {
                        payoutMode.select2("destroy");
                    }

                    payoutMode.select2({
                        width: "100%",
                        placeholder: "select an option",
                        dropdownParent: documentModal,
                        data: _payment_mode
                    })
                }

                if (typeof payoutSchedSelect !== "undefined" && payoutSchedSelect.length == 1) {
                    if (payoutSchedSelect.hasClass("select2-hidden-accessible")) {
                        payoutSchedSelect.select2("destroy");
                    }

                    payoutSchedSelect.select2({
                        width: "100%",
                        placeholder: "select an option",
                        dropdownParent: documentModal,
                        data: _payoutSched,
                        allowClear: true
                    })
                }

                if (typeof employeeSelect2 !== "undefined" && employeeSelect2.length == 1) {
                    employeeSelect2.select2({
                        width: "100%",
                        placeholder: "select an option",
                        dropdownParent: documentModal,
                        ajax: {
                            delay: 1000,
                            global: false,
                            url: baseUrl('payroll/employee/get_employee_group_for_filter'),
                            dataType: 'json',
                            type: 'get',
                            data: function (params) {
                                params.company_id = companySelect2.val();
                                params.all_filter = propAllFilter;
                                params.payout_sched = payoutSchedSelect.val();
                                params.employee_status = activeEmployeeFilter.is(":checked") ? 1 : 0;
                                return params;
                            }
                        },
                        escapeMarkup: function (markup) {
                            return markup;
                        },
                        templateResult: function (data) {
                            return data.html;
                        },
                        templateSelection: function (data) {
                            return data.text;
                        }
                    });
                }

                if (typeof allowView !== "undefined" && allowView.length == 1) {
                    allowView.on("change", function (e) {
                        let isChecked = e.target.checked;
                        documentModal.find('#assign_employee_div').prop("hidden", !isChecked);
                        if (isChecked == false) { assignSelect.val([]).trigger("change"); }
                    })
                }

                if (typeof assignSelect !== "undefined" && assignSelect.length == 1) {
                    assignSelect.select2({
                        width: "100%",
                        placeholder: "select an option",
                        dropdownParent: documentModal,
                        ajax: {
                            delay: 1000,
                            global: false,
                            url: baseUrl('payroll/employee/get_employee_list'),
                            dataType: 'json',
                            type: 'get',
                            data: function (params) {
                                params.q = params.term;
                                params.company_id = companySelect2.val();
                                return params;
                            }
                        }
                    });
                }

                if(typeof activeEmployeeFilter !== "undefined" && activeEmployeeFilter.length == 1){
                    activeEmployeeFilter.on("change", function () {
                        employeeSelect2.val([]).trigger("change");
                    });
                }

                $.validate({
                    form: documentModal.find("form"),
                    lang: "en",
                    onSuccess: function (form) {
                        var currentForm = form[0];
                        var formUrl = currentForm.action;
                        var formData = $(currentForm).serialize();
                        const isActiveFilter = activeEmployeeFilter.is(":checked") ? 1 : 0;
                        formData += "&active_only="+isActiveFilter;

                        $.ajax({
                            url: formUrl,
                            type: "POST",
                            dataType: "json",
                            data: formData,
                            beforeSend: function () {
                                $(form[0])
                                    .find(".btn-submit")
                                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            },
                            success: function (data) {
                                if (data.response) {
                                    documentModal.modal("hide");
                                    dtPayrollGroup.ajax.reload(null, false);
                                }
                                $(form[0])
                                    .find(".btn-submit")
                                    .removeClass(
                                        "m-btn--custom m-loader m-loader--light m-loader--right"
                                    );
                            }
                        });
                        return false;
                    }
                });
                documentModal.modal("show");
            }
            $(currentTarget).removeClass("m-btn--custom m-loader m-loader--light m-loader--right").prop("disabled", false);
        }
    });
});

$(document).on("click", "button.btnEditGroup", function () {
    const _this = this;
    const rowUrl = $(_this).data("url");
    $.ajax({
        url: rowUrl,
        dataType: "json",
        success: function (json) {
            if (json.response) {
                propAllFilter = false;
                const tempRow = json.row;
                let _employees = {};
                tempRow.employees.forEach(value => {
                    _employees[value.id] = value.text;
                });

                _globalLockedEmployees = { employees: {}, id: [] };
                if(tempRow.allow_transfer !== undefined && Number.parseInt(tempRow.allow_transfer) === 0) {
                    _globalLockedEmployees.employees = { ..._employees };
                    $.each(tempRow.employee_id, (i, v) => {
                        _globalLockedEmployees.id.push(v);
                    });
                }

                documentModal.empty().html(json.html);
                const employeeSelect2 = documentModal.find("select#employee_id");
                const companySelect2 = documentModal.find("select#company_id");
                const companyAllFilter = documentModal.find("input#all_company_filter");
                const allowView = documentModal.find("input#allow_view");
                const assignSelect = documentModal.find('select#assign_employee_id');
                const payoutSchedSelect = documentModal.find('select#payroll_sched');
                const payoutMode = documentModal.find('select#payment_mode');
                const activeEmployeeFilter = documentModal.find("input#active_employees");
                let idsToEnable = tempRow.employee_id ?? [];

                if (companyAllFilter !== undefined && companyAllFilter.length == 1) {
                    companyAllFilter.on("change", function (e) {
                        let isChecked = e.target.checked;
                        propAllFilter = isChecked;
                        companySelect2.prop("disabled", isChecked);
                        if (isChecked) { companySelect2.val("").trigger("change"); }
                        if (isChecked === false) { employeeSelect2.val([]).trigger("change"); }

                        allowView.prop('disabled', isChecked);
                        if (allowView.is(":checked")) { 
                            allowView.prop('checked', false); 
                            assignSelect.val([]).trigger("change"); 
                            documentModal.find('#assign_employee_div').prop("hidden", true);
                        }
                    });
                }

                if (companySelect2 !== undefined && companySelect2.length == 1) {
                    companySelect2.select2({
                        width: "100%",
                        data: _companies,
                        placeholder: "select an option",
                        dropdownParent: documentModal,
                        allowClear: true,
                    }).on("select2:select", function () {
                        employeeSelect2.val([]).trigger("change");
                    }).on("select2:unselect", function () {
                        employeeSelect2.val([]).trigger("change");
                    });
                }

                if (typeof payoutMode !== "undefined" && payoutMode.length == 1) {
                    if (payoutMode.hasClass("select2-hidden-accessible")) {
                        payoutMode.select2("destroy");
                    }

                    payoutMode.select2({
                        width: "100%",
                        placeholder: "select an option",
                        dropdownParent: documentModal,
                        data: _payment_mode
                    }).val(tempRow.payout_mode).trigger("change");
                }

                if (typeof payoutSchedSelect !== "undefined" && payoutSchedSelect.length == 1) {
                    if (payoutSchedSelect.hasClass("select2-hidden-accessible")) {
                        payoutSchedSelect.select2("destroy");
                    }

                    payoutSchedSelect.select2({
                        width: "100%",
                        placeholder: "select an option",
                        dropdownParent: documentModal,
                        data: _payoutSched,
                        allowClear: true
                    }).val(tempRow.payout_sched).trigger("change");
                }

                if (employeeSelect2 !== undefined && employeeSelect2.length == 1) {
                    employeeSelect2.select2({
                        width: "100%",
                        placeholder: "select an option",
                        dropdownParent: documentModal,
                        ajax: {
                            delay: 1000,
                            global: false,
                            url: baseUrl('payroll/employee/get_employee_group_for_filter'),
                            dataType: 'json',
                            type: 'get',
                            data: function (params) {
                                params.company_id = companySelect2.val();
                                params.all_filter = propAllFilter;
                                params.employee_status = activeEmployeeFilter.is(":checked") ? 1 : 0;
                                return params;
                            }, processResults: function (data) {
                                const { results } = data;
                                results.forEach(item => {
                                    if (idsToEnable.includes(String(item.id))) {
                                        item.disabled = false;
                                    }
                                });
                                return { results: results };
                            }
                        },
                        escapeMarkup: function (markup) {
                            return markup;

                        },
                        templateResult: function (data) {
                            return data.html || data.text;
                        },
                        templateSelection: function (data) {
                            return data.text || data.id;
                        }
                    });

                    if (typeof tempRow.employees == "object" && tempRow.employees !== undefined) {
                        employeeSelect2.empty();
                        $.each(tempRow.employees, function (ii, vv) {
                            const tempOption = new Option(vv.text, vv.id, true, true);
                            employeeSelect2.append(tempOption);
                        });
                    }
                }

                if (typeof allowView !== "undefined" && allowView.length == 1) {
                    const _isCheked = tempRow.is_allow_view == 1 ? true : false;
                    allowView.prop("checked", _isCheked);
                    documentModal.find('#assign_employee_div').prop("hidden", !_isCheked);

                    allowView.on("change", function (e) {
                        let isChecked = e.target.checked;
                        documentModal.find('#assign_employee_div').prop("hidden", !isChecked);
                        if (isChecked == false) { assignSelect.val([]).trigger("change"); }
                    });
                }

                if (typeof assignSelect !== "undefined" && assignSelect.length == 1) {
                    assignSelect.select2({
                        width: "100%",
                        placeholder: "select an option",
                        dropdownParent: documentModal,
                        ajax: {
                            delay: 1000,
                            global: false,
                            url: baseUrl('payroll/employee/get_employee_list'),
                            dataType: 'json',
                            type: 'get',
                            data: function (params) {
                                params.q = params.term;
                                params.company_id = companySelect2.val();
                                return params;
                            }
                        }
                    });

                    if (typeof tempRow.allowed == "object" && typeof tempRow.allowed !== "undefined") {
                        assignSelect.empty();
                        $.each(tempRow.allowed, function (ii, vv) {
                            var tempOption = new Option(vv.text, vv.id, true, true);
                            assignSelect.append(tempOption);
                        });
                    }
                }
                
                if (typeof activeEmployeeFilter !== "undefined" && activeEmployeeFilter.length === 1) {
                    activeEmployeeFilter.on("change", function (e) {
                        const current = $(this);
                        const oldChecked = current.prop("checked");
                        
                        const hasValue = employeeSelect2.val() && employeeSelect2.val().length > 0;
                        if (!hasValue) { return; }
                        e.preventDefault();

                        Swal.fire({
                            title: "Active/All Filter Search",
                            text: "This will clear the current employee(s) on the payroll group employees. Would you like to proceed?",
                            icon: "question",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes, Proceed",
                            cancelButtonText: "No",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                employeeSelect2.val([]).trigger("change");
                            }else{
                                current.prop("checked", !oldChecked);
                            }
                        });
                    });
                }
                
                $.validate({
                    form: documentModal.find("form"),
                    lang: "en",
                    onSuccess: function (form) {
                        const { id, employees } = _globalLockedEmployees;
                        let tempState = false;
                        if(id !== undefined && id.length > 0){
                            let employeeNames = [];
                            let restoreValues = [];
                            const currentIds = employeeSelect2.val();
                            id.forEach(value => {
                                if(currentIds.includes(value) === false){
                                    const empName = employees[value];
                                    employeeNames.push(empName);
                                    restoreValues.push({ id: value, text: empName });
                                }
                            });

                            if(employeeNames.length > 0){
                                Swal.fire({
                                    title: "Remove Employee(s)",
                                    html: "Unable to remove employee(s) on this payroll group. Employee Transfer is required!<br>" + employeeNames.join("<br>"),
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#3085d6",
                                    cancelButtonColor: "#d33",
                                    confirmButtonText: "OK",
                                    cancelButtonText: "Proceed Anyway!",
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                }).then((result) => {
                                    if(result.isConfirmed){
                                        restoreValues.forEach(value => {
                                            employeeSelect2.append(new Option(value.text, value.id, true, true));
                                        });
                                    }

                                    if (result.dismiss === Swal.DismissReason.cancel) {
                                        Swal.fire({
                                            title: "Warning!",
                                            html: "You are about to remove employee(s) on this payroll group!<br>" + employeeNames.join("<br>"),
                                            icon: "warning",
                                            input: "textarea",
                                            inputPlaceholder: "Reason is Required *",
                                            inputLabel: "Reason for employee payroll group removal",
                                            customClass: { inputLabel: 'm--font-bolder required' },
                                            inputValidator: (result) => { return !result && "Reason for employee payroll group removal is required!"; },
                                            showCancelButton: true,
                                            confirmButtonColor: "#3085d6",
                                            cancelButtonColor: "#d33",
                                            confirmButtonText: "OK",
                                            cancelButtonText: "Cancel",
                                            allowOutsideClick: false,
                                            allowEscapeKey: false,
                                            target: document.querySelector('.modal.show') || document.body
                                        }).then((resultNext) => {
                                            if(resultNext.dismiss === Swal.DismissReason.cancel){
                                                restoreValues.forEach(value => {
                                                    employeeSelect2.append(new Option(value.text, value.id, true, true));
                                                });
                                            }

                                            if(resultNext.isConfirmed && resultNext.value){
                                                let tempNames = restoreValues.map(value => value.text);
                                                postPayrollGroup(form, { reason: resultNext.value, employees: tempNames });
                                            }
                                        });
                                    }
                                });
                                tempState = true;
                            }
                        }

                        if(tempState){ return false; }

                        postPayrollGroup(form);
                        return false;
                    }
                });

                documentModal.modal("show");
            }
        }
    });
});

const postPayrollGroup = function (form, data) {
    const currentForm = form[0];
    const formUrl = currentForm.action;
    let formData = $(currentForm).serialize();
    if(data !== undefined && Object.keys(data).length > 0){
        formData+="&reason="+data.reason;
        data.employees.forEach(value => {
            formData+="&employees[]="+encodeURIComponent(value);
        });
    }

    const isActiveFilter = $(currentForm).find("input#active_employees").is(":checked") ? 1 : 0;
    formData += "&active_only="+isActiveFilter;

    $.ajax({
        url: formUrl,
        type: "POST",
        dataType: "json",
        data: formData,
        beforeSend: function () {
            $(form[0])
                .find(".btn-submit")
                .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function (data) {
            if (data.response) {
                documentModal.modal("hide");
                dtPayrollGroup.ajax.reload(null, false);
            }
            $(form[0])
                .find(".btn-submit")
                .removeClass(
                    "m-btn--custom m-loader m-loader--light m-loader--right"
                );
        }
    });
}

$(document).on("click", "button.btnDeleteGroup", function () {
    const _this = this;
    const rowUrl = $(_this).data("url");
    $.ajax({
        url: rowUrl,
        dataType: "json",
        success: function (json) {
            if (json.response) {
                documentModal
                    .empty()
                    .html(json.html);
                documentModal.modal("show");

                documentModal.find("form").on("submit", function (e) {
                    const tempForm = e.target;
                    e.preventDefault();

                    $.ajax({
                        url: tempForm.action,
                        type: "post",
                        dataType: "json",
                        data: $(e.target).serialize(),
                        beforeSend: function () {
                            $(tempForm)
                                .find(".btn-submit")
                                .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        },
                        success: function (json) {
                            if (json.response) {
                                documentModal.modal("hide");
                                dtPayrollGroup.ajax.reload(null, false);
                            }
                            $(tempForm)
                                .find(".btn-submit")
                                .removeClass(
                                    "m-btn--custom m-loader m-loader--light m-loader--right"
                                );
                        }
                    })
                });
            }
        }
    });
});

const renderNotificationRecords = function () {
    $.ajax({
        url: siteUrl("payroll/employee/get_duplicate_payroll_group"),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                const { duplicate_count, data } = json;
                vmNotification.count = duplicate_count;
                vmModalEntries.count = duplicate_count;
                vmModalEntries.data = { ...data };
                if(json.duplicate_count > 0){
                    toastr.info("A total of ("+json.duplicate_count+") duplicate payroll group found!", "Duplicate Payroll Group", { timeOut: 0, extendedTimeOut: 0 });
                }
            } else {
                vmNotification.count = 0;
                vmModalEntries.count = 0;
                vmModalEntries.data = {};
            }
        }
    })
}

const vmNotification = new Vue({
    el: "#group_notification",
    data: { count: 0, notification_clicked: false, no_payroll_group: false },
    methods: {
        toggleClicked: function () {
            let _this = this;
            if (_this.notification_clicked === false) { _this.notification_clicked = true; }
            toastr.clear();
            return _this.notification_clicked;
        }, 
        addPayrollGroup: function () {
            getEmployeesWithoutPayrollGroup(true);
        }
    }
});

const vmModalEntries = new Vue({
    el: "#modal-duplicate-entries",
    data: { count: 0, data: {} }
});

let _propAllFilter = false;
const _companySelect2 = formTransferGroup.find("#company_id");
const _employeeSelect2 = formTransferGroup.find("#employee_id");
const _payrollGroupSelect2 = formTransferGroup.find("#payroll_group_id");
const _companyAllFilter = formTransferGroup.find("input#all_company_filter");

if (_companyAllFilter !== undefined && _companyAllFilter.length == 1) {
    _companyAllFilter.on("change", function (e) {
        let isChecked = e.target.checked;
        _propAllFilter = isChecked;
        _companySelect2.prop("disabled", isChecked);
        if (isChecked) { _companySelect2.val("").trigger("change"); }
        if (isChecked === false) { _employeeSelect2.val([]).trigger("change"); }
    });
}

if (_companySelect2.length === 1) {
    _companySelect2.select2({
        width: "100%",
        placeholder: "Select Company",
        data: _companies,
        dropdownParent: modalTransferGroup,
        allowClear: true,
    }).on("select2:select", function () {
        _employeeSelect2.val([]).trigger("change");
    }).on("select2:unselect", function () {
        _employeeSelect2.val([]).trigger("change");
    });
}

if(_employeeSelect2.length === 1){
    _employeeSelect2.select2({
        width: "100%",
        placeholder: "Select Employee",
        data: [],
        dropdownParent: modalTransferGroup,
        ajax: {
            delay: 750,
            global: false,
            url: baseUrl('payroll/employee/get_transferable_employee_groups'),
            dataType: 'json',
            type: 'get',
            data: function (params) {
                params.company_id = _companySelect2.val();
                params.all_filter = _propAllFilter;
                return params;
            }
        }
    });
}

if(_payrollGroupSelect2.length === 1){
    _payrollGroupSelect2.select2({
        width: "100%",
        placeholder: "Select Payroll Group",
        data: [],
        dropdownParent: modalTransferGroup,
        ajax: {
            delay: 750,
            global: false,
            url: baseUrl('payroll/employee/get_payroll_groups'),
            dataType: 'json',
            type: 'get',
            data: function (params) {
                params.company_id = _companySelect2.val();
                params.all_filter = _companyAllFilter.prop("checked");
                return params;
            }
        }
    });
}

$.validate({
    form: formTransferGroup,
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const currentForm = form[0];
        const formUrl = currentForm.action;
        const formData = $(currentForm).serialize();

        $.ajax({
            url: formUrl,
            type: "POST",
            dataType: "json",
            data: formData,
            beforeSend: function () {
                $(form[0])
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.response) {
                    modalTransferGroup.modal("hide");
                    currentForm.reset();
                    dtPayrollGroup.ajax.reload(null, false);
                    getEmployeeTransferState();
                }
                
                $(form[0])
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            },
        });
        return false;
    }
});

const getEmployeeTransferState = () => {
    $.ajax({
        url: baseUrl("payroll/employee/get_employee_transfer_state"),
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            const { for_approval, transfer_history } = data;
            notificationCounter.count = 0;
            if(for_approval.length > 0){
                notificationCounter.count = for_approval.length;
            }
            setTimeout(function () {
                if(for_approval.length > 0){
                    dtTableApproval.clear();
                    dtTableApproval.rows.add(for_approval).draw(false);
                }

                if(transfer_history.length > 0){
                    dtTableHistory.clear();
                    dtTableHistory.rows.add(transfer_history).draw(false);
                }
            }, 1000);

            vmModalEntries.count = data.count;
            vmModalEntries.data = data.data;
        },
    });
}
function normalizeKey(key) {
  return key.replace(/_/g, " ").replace(/\w\S*/g, txt =>
      txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase()
    );
}

function normalizeArrayKeys(data) {
  return data.map(row => {
    const newRow = {};
    Object.keys(row).forEach(key => {
      newRow[normalizeKey(key)] = row[key];
    });
    return newRow;
  });
}

const getEmployeesWithoutPayrollGroup = function (isClicked = false) {
    $.ajax({
        url: siteUrl("payroll/employee/get_employees_without_payroll_group"),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                const { data: arrEmpRecord } = json;

                const ctr = arrEmpRecord.length;
                let tempHtml = `<div class='row swal--custom-list'>`;
                arrEmpRecord.forEach((row, _index) => {
                    tempHtml += `<div class='col-6 col-md-6 col-lg-6 col-sm-12'><span class='m--font-bolder text-left ml-1'>${row.employee_name} - ${row.company_code}</span></div>`;
                });
                tempHtml += `</div>`;
                
                let swalFireOption = {
                    title: 'EMPLOYEES WITHOUT PAYROLL GROUP!',
                    html: `A TOTAL OF <b>${ctr}</b> EMPLOYEES WITHOUT PAYROLL GROUP FOUND!<br>${tempHtml}`,
                    icon: 'warning',
                    width: '1024px',
                    confirmButtonText: 'Export Excel',
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                    allowOutsideClick: () => !Swal.isLoading(),
                    allowEscapeKey: () => !Swal.isLoading()
                };

                if (isClicked === false) {
                    vmNotification.no_payroll_group = true;
                    swalFireOption.timer = 10000;
                    swalFireOption.timerProgressBar = true;
                    let isManualClose = false;
                    swalFireOption.didOpen = () => {
                        const popup = Swal.getPopup();
                        // Fade before timer ends
                        setTimeout(() => {
                            if (!isManualClose && popup) {
                                popup.classList.add('swal2-fade-out');
                            }
                        }, 9800);
                    };
                    swalFireOption.willClose = () => {
                        return new Promise(resolve => setTimeout(resolve, 500));
                    };
                }

                swalFireOption.preConfirm = async () => {
                    try {
                        Swal.showLoading();
                        Swal.stopTimer();
                        await new Promise(r => setTimeout(r, 50));
                        const formattedData = normalizeArrayKeys(arrEmpRecord);
                        const CHUNK_SIZE = 5000;
                        let worksheet = XLSX.utils.json_to_sheet([]);
                        for (let i = 0; i < formattedData.length; i += CHUNK_SIZE) {
                            const chunk = formattedData.slice(i, i + CHUNK_SIZE);
                            XLSX.utils.sheet_add_json(worksheet, chunk);
                            await new Promise(r => setTimeout(r, 0));
                        }

                        const workbook = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(
                            workbook,
                            worksheet,
                            "WITHOUT PAYROLL GROUP"
                        );

                        XLSX.writeFile(
                            workbook,
                            "EMPLOYEES WITHOUT PAYROLL GROUP.xlsx",
                            { compression: true }
                        );

                        return true;

                    } catch (err) {
                        Swal.showValidationMessage(
                            `Export failed: ${err.message || err}`
                        );
                        return false;
                    }
                };
                Swal.fire(swalFireOption);
            }else{
                vmNotification.no_payroll_group = false;
            }
        }
    });
}

$(document).ready(function () {
    getEmployeesWithoutPayrollGroup();
    renderNotificationRecords();
});

// Restore Payroll Group
const archived_payroll_group_table = $("#table-archived_payroll_group");

// prevent reinit
if ($.fn.DataTable.isDataTable(archived_payroll_group_table)) {
    archived_payroll_group_table.DataTable().destroy();
    archived_payroll_group_table.find("tbody").empty();

}

archived_payroll_group_table.DataTable({
    dom: '<"toolbar">rtlip',
    processing: true,
    searching: true,
    ordering: false,
    bAutoWidth: false,
    data: [], // Initially empty, will be populated on modal archive_payroll_group_modal show 
    columns: [
        { data: "company", width: "200px" },
        {
            data: "employees", width: "500px",
            render: function (data) {
                let html = "<p style='line-height:28px;'>";
                $.each(data, function (i, v) {
                    html += `<span class='m-badge m-badge--metal m-badge--wide m-badge--rounded mr-1'>${v}</span>`;
                });
                return html + "</p>";
            }
        },
        { data: "description", width: "200px" },
        {
            data: null,
            width: "100px",
            className: "text-center",
            render: function (data, meta, row) {
                return `<button title="Restore" class="btn btn-sm btn-default m-btn--pill btnRestore btnRestoreGroup" data-url="${row.restore_url}">
                            <i class="la la-undo" style="font-size: 10px;"></i>
                        </button>`;
            }
        }
    ]
});

// custom search input
$("#payroll_archived_search").on("keyup change", function () {
    archived_payroll_group_table.DataTable().search(this.value).draw();
});

$("#archive_payroll_group_modal").on("shown.bs.modal", async () => {
    try {
        const res = await $.ajax({
            url: baseUrl("payroll/employee/get_archived_employee_group"),
            type: "POST",
            dataType: "json",
            data: { csrf_token : _csrf_hash },
        });

        archived_payroll_group_table.DataTable().clear().rows.add(res).draw();
    } catch (err) {
        console.error("Error fetching archived payroll groups:", err);
    }
});

$("#archive_payroll_group_modal").on("hidden.bs.modal", function () {
    archived_payroll_group_table.DataTable().search('');
    archived_payroll_group_table.DataTable().clear().draw();
    $("#payroll_archived_search").val("");
});

$(document).on("click", "button.btnRestoreGroup", function () {
    const _this = this;
    const rowUrl = $(_this).data("url");
    $.ajax({
        url: rowUrl,
        dataType: "json",
        success: function (json) {
            if (json.response) {
                $("#archive_payroll_group_modal").modal("hide");

                documentModal.empty().html(json.html);
                documentModal.modal("show");

                documentModal.find("form").on("submit", function (e) {
                    const tempForm = e.target;
                    e.preventDefault();

                    $.ajax({
                        url: tempForm.action,
                        type: "post",
                        dataType: "json",
                        data: $(e.target).serialize(),
                        beforeSend: function () {
                            $(tempForm).find(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        },
                        success: function (json) {
                            if (json.response) {
                                documentModal.modal("hide");
                                dtPayrollGroup.ajax.reload(null, false);
                            }
                            $(tempForm).find(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        }
                    })
                });
            }
        }
    });
});
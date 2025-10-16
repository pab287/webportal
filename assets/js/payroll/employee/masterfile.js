var tableEmployeeList = $("#table-employee");
var tblSalaryHistory = $("#tbl-salary-history");
var tableCashAdvance = $("#tbl-cash_advance_list");

var modalTempContent = $("#modalTempContent");

var mcompany = $("#m--input-company_id");
var dtEmployee = null;
let dtPerformanceRating = null;

var companyExceptCurrent;

var modalTempContentLg = modalTempContent.clone().prop("id", "modalTempContentLg").appendTo(".m-content");
modalTempContentLg.find(".modal-dialog").addClass("modal-lg");

const changeEmployeeCompanyDialog = $("#change-employee-company-dialog");

const classificationDropdown = $('select[name="employee_status"]');
const status = $('select[name="work_status"]');

loadEmployees("Active");

$("body").tooltip({
    selector: "[data-toggle='m-tooltip']"
});

function filterEmployees(el) {
    const employee_status = $(el).val();
    loadEmployees(employee_status);
}

function loadEmployees(employee_status) {
    if (typeof tableEmployeeList !== "undefined") {
        var search_val = "";
        dtEmployee = tableEmployeeList.DataTable({
            dom: '<"toolbar">frtlip',
            serverSide: true,
            processing: true,
            searching: false,
            ordering: false,
            destroy: true,
            ajax: {
                url: baseUrl("payroll/employee/employee_masterfile/" + employee_status),
                type: "post",
                dataType: "json",
                data: function (d) {
                    d.csrf_token = _csrf_hash;
                    d.search['value'] = search_val;
                    return d;
                },
                global: false,
            },
            columns: [
                {
                    data: "image",
                    width: "5%",
                    className: "text-center"
                },
                {data: "name"},
                {
                    data: "status_201",
                    width: "10%",
                    render: function (data) {
                        const badgeClass = data.toLowerCase() === "incomplete" ? "m-badge--danger" : "m-badge--success";
                        return `<span class="m-badge m-badge--wide m--font-boldest ${badgeClass}">${data}</span>`;
                    }
                },
                {
                    data: "work_status",
                    width: "10%",
                    render: function (data) {
                        return `<span class="m--font-boldest">${data}</span>`;
                    }
                },
                {data: null, width: "10%", className: "text-center"}
            ],
            columnDefs: [
                {
                    data: "image",
                    targets: 0,
                    render: function (data, type, row, meta) {
                        var _html =
                            "<div clas='m-card-profile__pic-wrapper'><img class='m--img-rounded m--marginless m--img-centered user__pic' src='" +
                            data +
                            "' /></div>";
                        return _html;
                    }
                },
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return employeeDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
        });

        function employeeDataTableActions($id) {
            if ($id) {
                var _actionButton = "";
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditEmployee' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='Edit Employee Record' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";
                return _actionButton;
            } else {
                return false;
            }
        }

        $('#generalSearch').donetyping(function (callback) {
            search_val = $(this).val();
            dtEmployee.ajax.reload();
        });

        $(document).on("click", ".btnEditEmployee", function () {
            var dataId = $(this).data("id");
            window.location.href = baseUrl("payroll/employee/edit_employee_masterfile/" + dataId);
        });

        $(document).on("click", ".btnViewEmployee201", function () {
            var dataId = $(this).data("id");
            window.location.href = baseUrl("payroll/employee/view_employee_masterfile/" + dataId);
        });
    }
}

if (typeof _tempContentData !== "undefined") {
    var tempData = _tempContentData.data;
    var tempDropdownData = _tempContentData.dropdown_data;
    var displayEmail = tempData.display_email ? tempData.display_email : "";
    var displayName = tempData.display_name
        ? tempData.display_name
        : "No Employee Name";
    var displayAvatar = tempData.pic_filename;

    var leftPanel = new Vue({
        el: "#left_pane-card",
        data: {
            left_pane: {
                display_name: displayName,
                display_email: displayEmail,
                display_avatar: displayAvatar
            }
        }
    });

    var vmPayInfo = new Vue({
        el: "#frmEditPayrollData-container",
        data: {
            vmpayinfo: tempData, 
            edited_content: {}, 
            psInfoCtr: 0, 
            forApprovalCtr: 0,
        },
        methods: {
            scrollToBottom(){ $('html, body').animate({ scrollTop: $('#payroll_info-history').offset().top}, 1000); },
            forApprovalModal(){
                if(typeof modalForApproval !== "undefined" && modalForApproval.length == 1){
                    modalForApproval.modal("show");
                }
            }
        },
        mounted: function () {
            $("#payroll_type")
                .select2({
                    width: "100%",
                    placeholder: "SELECT..."
                })
                .on("select2:select", function (e) {
                    const data = e.params.data;
                    vmPayInfo.vmpayinfo = Object.assign({}, vmPayInfo.vmpayinfo, {payroll_type: data.id});
                    vmPayInfo.edited_content = Object.assign({}, vmPayInfo.edited_content, {payroll_type: data.id});
                });

            $("#payout_sched")
                .select2({
                    width: "100%",
                    placeholder: "SELECT..."
                })
                .on("select2:select", function (e) {
                    const data = e.params.data;
                    vmPayInfo.vmpayinfo = Object.assign({}, vmPayInfo.vmpayinfo, {payout_sched: data.id});
                    vmPayInfo.edited_content = Object.assign({}, vmPayInfo.edited_content, {payout_sched: data.text});
                });
        }
    });

    var vmTab3 = new Vue({
        el: "#frmEditEmploymentData",
        data: {vm_tab3: tempData},
        mounted: function () {
            var vmData = this.vm_tab3;

            //additional info
            var sssNo = vmData.sss_no;

            var self = $(this.$el);
            var umidSwitch = self.find("#umid_no");
            umidSwitch.prop("checked", false);

            if (typeof sssNo !== "undefined" && sssNo !== null && sssNo !== "") {
                var tempSplitSssNo = sssNo.split("-", 3);
                var firstSplit = tempSplitSssNo[0].length;
                if (firstSplit > 2) {
                    umidSwitch.prop("checked", true);
                }
            }

            var tinNumberMask = self.find("#tin_no");
            var philhealthMask = self.find("#phealth_no");
            var pagibigMask = self.find("#pagibig_no");
            var sssNoMask = self.find("#sss_no");

            tinNumberMask.inputmask("mask", {"mask": "999-999-999"});
            philhealthMask.inputmask("mask", {"mask": "99-999999999-9"});
            pagibigMask.inputmask("mask", {"mask": "9999-9999-9999"});
            sssNoMask.inputmask("mask", {"mask": "99-9999999-9"});

            setTimeout(function () {
                umidSwitch.trigger("change");
            }, 500);

            const _formPersonalInformation = $("form#frmEditEmployeeData");
            const _formAdditionalInformation = $("form#frmEditAdditionalData");
            const radioPtSingle = _formAdditionalInformation.find("#pt_single");
            const radioPtMarried = _formAdditionalInformation.find("#pt_married");
            const radioPtPartner = _formAdditionalInformation.find("#pt_partner");

            //employment data
            const employee_status = vmData.employee_status ? vmData.employee_status.toLowerCase() : "";
            const work_status = vmData.work_status ? vmData.work_status.toLowerCase() : "";

            const activateRehireStatuses = ["inactive", "resign", "terminated"];
            if (activateRehireStatuses.includes(employee_status) ||
                activateRehireStatuses.includes(work_status)) {
                $("#rehire-button-container").removeClass("m--hide");
            } else {
                $("#rehire-button-container").addClass("m--hide");
            }

            const activeStatusOptions = '' +
                '<option value=""></option>' +
                '<option ' + (vmData.work_status === 'REGULAR' ? 'selected' : '') + ' value="REGULAR">REGULAR</option>' +
                '<option ' + (vmData.work_status === 'PROBATIONARY' ? 'selected' : '') + '  value="PROBATIONARY">PROBATIONARY</option>' +
                '<option ' + (vmData.work_status === 'NO CONTRACT' ? 'selected' : '') + '  value="NO CONTRACT">NO CONTRACT</option>' +
                '<option ' + (vmData.work_status === 'RETIRED' ? 'selected' : '') + '  value="RETIRED">RETIREE</option>' +
                '<option ' + (vmData.work_status === 'CONSULTANT' ? 'selected' : '') + '  value="CONSULTANT">CONSULTANT/RETAINER</option>' +
                '<option ' + (vmData.work_status === 'PROJECT BASED' ? 'selected' : '') + '  value="PROJECT BASED">PROJECT BASED</option>';

            const inactiveStatusOptions = '' +
                '<option value=""></option>' +
                '<option ' + (vmData.work_status === 'RESIGN' ? 'selected' : '') + ' value="RESIGN">RESIGNED</option>' +
                '<option ' + (vmData.work_status === 'TERMINATED' ? 'selected' : '') + ' value="TERMINATED">TERMINATED</option>' +
                '<option ' + (vmData.work_status === 'BLACKLISTED' ? 'selected' : '') + ' value="BLACKLISTED">BLACKLISTED</option>' +
                '<option ' + (vmData.work_status === 'END OF CONTRACT' ? 'selected' : '') + ' value="END OF CONTRACT">END OF CONTRACT</option>' +
                '<option ' + (vmData.work_status === 'INDEFINITE LEAVE' ? 'selected' : '') + ' value="INDEFINITE LEAVE">INDEFINITE LEAVE</option>';

            const contractorStatusOption = '' +
                '<option value=""></option>' +
                '<option ' + (vmData.work_status === '"N/A' ? 'selected' : '') + ' value="N/A" selected>N/A</option>';

            const status = $('#status');
            status.find('option').remove();

            // OLD FUNCTION
            // if (vmData.employee_status.toLowerCase() === 'active') {
            //     status.append(activeStatusOptions);
            // } else if (vmData.employee_status.toLowerCase() === 'inactive') {
            //     status.append(inactiveStatusOptions);
            // } else {
            //     status.append(contractorStatusOption);
            //     status.attr('readonly');
            // }

            // NEW FUNCTION
            if(vmData.employee_status) {
                if (vmData.employee_status.toLowerCase() === 'active') {
                    status.append(activeStatusOptions);
                } else if (vmData.employee_status.toLowerCase() === 'inactive') {
                    status.append(inactiveStatusOptions);
                } else {
                    status.append(contractorStatusOption);
                    status.attr('readonly');
                }
            }
            else {
                status.append(contractorStatusOption);
                status.attr('readonly');
            }

            $("#m--input-company_id")
                .select2({
                    data: tempDropdownData.dropdown_company,
                    placeholder: {
                        id: "-1",
                        text: "Select an option"
                    },
                    width: '100%'
                })
                .val(vmData.company_id)
                .trigger("change")
                .on("select2:select", function (e) {
                    const data = e.params.data;
                    vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, {company_id: data.id});
                });

            $('#change-company-company_id')
                .select2({
                    data: tempDropdownData.dropdown_company,
                    placeholder: {
                        id: "-1",
                        text: "Select a Company"
                    },
                    width: '100%',
                    dropdownParent: $("#change-employee-company-dialog")
                })
                .val(-1)
                .trigger("change");

            $("#m--input-department_id")
                .select2({
                    data: tempDropdownData.dropdown_department,
                    placeholder: {
                        id: "-1",
                        text: "Select an option"
                    },
                    width: '100%'
                })
                .val(vmData.department_id)
                .trigger("change")
                .on("select2:select", function (e) {
                    const data = e.params.data;
                    vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, {department_id: data.id});
                });

            $('#change-company-department_id')
                .select2({
                    data: tempDropdownData.dropdown_department,
                    placeholder: {
                        id: "-1",
                        text: "Select a Department"
                    },
                    width: '100%',
                    dropdownParent: $("#change-employee-company-dialog")
                })
                .val(-1)
                .trigger("change");

            $("#m--input-position_id")
                .select2({
                    data: tempDropdownData.dropdown_position,
                    placeholder: {
                        id: "-1",
                        text: "Select an option"
                    },
                    width: '100%'
                })
                .val(vmData.position)
                .trigger("change")
                .on("select2:select", function (e) {
                    const data = e.params.data;
                    vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, {position: data.id});
                });

            $('#change-company-position')
                .select2({
                    data: tempDropdownData.dropdown_position,
                    placeholder: {
                        id: "-1",
                        text: "Select a Position"
                    },
                    width: '100%',
                    dropdownParent: $("#change-employee-company-dialog")
                })
                .val(-1)
                .trigger("change");

            $("#m--input-payroll_type_id")
                .select2({
                    data: tempDropdownData.dropdown_payroll_type,
                    placeholder: {
                        id: "-1",
                        text: "Select an option"
                    },
                    width: '100%'
                })
                .val(vmData.payroll_type)
                .trigger("change")
                .on("select2:select", function (e) {
                    const data = e.params.data;
                    vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, {payroll_type: data.id});
            });
            console.log(tempData.auto_overtime);
        }
    });    

    if (typeof tableCashAdvance !== "undefined") {
        var dtCashAdvance = tableCashAdvance.DataTable({
            dom: '<"toolbar dt-toolbar_cash_advance">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("payroll/employee/get_employee_cash_advance"),
                type: "post",
                dataType: "json",
                data: {csrf_token: _csrf_hash, emp_id: tempData.id}
            },
            columns: [
                {data: "reference_no", title: "Reference No"},
                {data: "amt_applied", title: "Amount Applied"},
                {data: "purpose", title: "Purpose"},
                {data: "amt_approved", title: "Amount Approved"},
                {data: "created_dt", title: "Date Applied"},
                {data: "status", title: "Status"},
                {data: null, title: "Action", width: "8%", className: "text-center"}
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return cashAdvanceDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                let search_thread = null;
                $("#tbl-cash_advance_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableCashAdvance.dataTable().api();
                            const elem = $("#tbl-cash_advance_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });

        function cashAdvanceDataTableActions($id) {
            if ($id) {
                var _actionButton = "";
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditCashAdvance' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                if (jQuery.inArray("archive", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveCashAdvance' data-id='" +
                        $id +
                        "'><i class='la la-file-archive-o'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";
                return _actionButton;
            } else {
                return false;
            }
        }
    }
}

$(document).ready(function () {
    validatePersonalEmployeeData();
    setTimeout(function () {
        updateUmidTypeSwitch();
    }, 500);
});

var updateUmidTypeSwitch = function () {
    var _formAdditionalInformation = $("form#frmEditAdditionalData");
    if (typeof _formAdditionalInformation !== "undefined") {
        var umidNoSwitch = _formAdditionalInformation.find("#umid_no");
        var sssNoMask = _formAdditionalInformation.find("#sss_no");
        umidNoSwitch.on("change", function () {
            var radioThis = $(this);
            var isPropChecked = radioThis.prop("checked");
            if (isPropChecked == true) {
                sssNoMask.inputmask("mask", {"mask": "9999-9999999-9"});
            } else {
                sssNoMask.inputmask("mask", {"mask": "99-9999999-9"});
            }
        });
    }
}

var validatePersonalEmployeeData = function () {
    $.validate({
        form: "#frmEditEmploymentData",
        lang: "en",
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
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(
                            json.toastr_msg,
                            "Employee data has been updated.",
                            5000
                        );
                        var _respData = json.data;
                        var _newData = Object.assign(
                            {},
                            {
                                display_name: _respData.display_name,
                                display_email: _respData.display_email,
                                display_avatar: _respData.pic_filename
                            }
                        );
                        leftPanel.left_pane = _newData;
                        _tempContentData.data = Object.assign({}, json.data);
                        vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, json.data);
                    } else {
                        toastr.error(
                            json.toastr_msg,
                            "Error updating employee data!",
                            5000
                        );
                    }

                    $(currentForm)
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                }
            });
            return false;
        }
    });
};

function openChangeCompanyDialog() {
    $('input[name="current_company"]').val(vmTab3.vm_tab3.company);
    $('input[name="current_position"]').val(vmTab3.vm_tab3._position);
    $('input[name="current_department"]').val(vmTab3.vm_tab3.department);
    $('input[name="current_status"]').val(vmTab3.vm_tab3._status);
    $('input[name="emp_id"]').val(vmTab3.vm_tab3.id);

    const date_start = new Date(vmTab3.vm_tab3.date_start);

    $('input[name="work_from"]').val(date_start.getFullYear());

    changeEmployeeCompanyDialog.modal("show");
}

function changeEmployeeCompany(_form) {
    const form = $(_form);
    const url = form.attr("action");
    const formData = new FormData(_form);
    formData.append("csrf_token", _csrf_hash);

    if (form.isValid()) {
        $.ajax({
            url,
            dataType: "JSON",
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                if (response.success) {
                    _tempContentData.data = Object.assign({}, response.data);
                    vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, response.data);
                    changeEmployeeCompanyDialog.modal("hide");
                    $("#m--input-position_id").val(vmTab3.vm_tab3.position).trigger('change');
                    $("#m--input-department_id").val(vmTab3.vm_tab3.department_id).trigger('change');

                    $("#change-company-company_id").val('').trigger('change');
                    $("#change-company-department_id").val('').trigger('change');
                    $("#change-company-position").val('').trigger('change');

                    toastr.success(response.message, "Transfer Company", 10000);
                } else {
                    toastr.error(response.message, "Error", 10000);
                }
            }
        });
    }
}

/* --START-- SALARY HISTORY */
console.log(tblSalaryHistory);
if (typeof tblSalaryHistory !== "undefined" && tblSalaryHistory.length != 0) {
    var dtSalaryHistory = tblSalaryHistory.DataTable({
        dom: '<"toolbar dt-toolbar-salary-history">frtlip',
        serverSide: true,
        processing: true,
        ordering: false,
        autoWidth: false,
        ajax: {
            url: baseUrl("payroll/employee/get_employee_salary_history"),
            type: "post",
            dataType: "json",
            data: {csrf_token: _csrf_hash, emp_id: tempData.id}
        },
        columns: [
            {
                data: "sal_date",
                title: "Date",
                width: "10%"
            },
            {
                data: "sal_rate",
                title: "Rate",
                width: "10%"
            },
            {
                data: "position",
                title: "Position"
            },
            {
                data: "sal_remarks",
                title: "Remarks"
            },
            {
                data: null,
                title: "Action",
                width: "15%",
                className: "text-center"
            }
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return historySalaryAction(row.id, row.position_id);
                }
            },
            {
                targets: "_all",
                defaultContent: ""
            }
        ],
        initComplete: function () {
            $(".dt-toolbar-salary-history")
                .append(
                    "<button type='button' " +
                    "        class='btn btn-sm btn-success btnNew btnAddSalaryHistory'>" +
                    "           <i class='fa fa-plus'></i>" +
                    "           <span>New</span>" +
                    "</button>"
                );

            let search_thread = null;
            $("#tbl-salary-history_filter input")
                .unbind()
                .bind("input", function (e) {
                    clearTimeout(search_thread);
                    search_thread = setTimeout(function () {
                        const dtTableApi = tblSalaryHistory.dataTable().api();
                        const elem = $("#tbl-salary-history_filter input");
                        return dtTableApi.search($(elem).val()).draw();
                    }, 1000);
                });
        },
    });

    function historySalaryAction($id, position_id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' " +
                "         class='btn btn-default m-btn m-btn--hover-accent m-btn--icon " +
                "                m-btn--icon-only m-btn--pill btnEditSalaryHistory btnEdit' " +
                "         data-id='" + $id + "' data-position-id='" + position_id + "'>" +
                "           <i class='la la-edit'></i>" +
                " </button>";
            _actionButton +=
                " <button type='button' " +
                "         class='btn btn-default m-btn m-btn--hover-warning " +
                "                m-btn--icon m-btn--icon-only m-btn--pill btnRemoveSalaryHistory btnArchive' " +
                "         data-id='" + $id + "'>" +
                "           <i class='la la-file-archive-o'></i>" +
                " </button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $(document)
        .on("click", ".btnAddSalaryHistory", function () {
            $("#add-salary-emp-id").val(tempData.id);
            $("#add-salary-history-modal").modal("show");
        });

    $(".money").maskMoney({thousands: ',', decimal: '.', allowZero: false});

    $(".sal_date_container")
        .datepicker({
            todayHighlight: true,
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            },
            autoclose: true
        })
        .on('changeDate show', function (e) {
            $(".sal_date_container").datepicker("update", moment(e.date).format("MM/DD/YYYY"));
        });


    $("#frm-add-salary-history [name='sal_position']")
        .select2({
            placeholder: "Select Position",
            width: "100%",
            ajax: {
                url: baseUrl("payroll/employee/get_position_select2_data"),
                dataType: "JSON",
                delay: 500
            },
            dropdownParent: $("#add-salary-history-modal")
        });

    $("#frm-edit-salary-history [name='sal_position']")
        .select2({
            placeholder: "Select Position",
            width: "100%",
            ajax: {
                url: baseUrl("payroll/employee/get_position_select2_data"),
                dataType: "JSON",
                delay: 500
            },
            dropdownParent: $("#edit-salary-history-modal")
        });

    tblSalaryHistory
        .on("click", ".btnEditSalaryHistory", function () {
            const id = $(this).attr('data-id');
            const position_id = $(this).attr("data-position-id");
            const row = $(this).closest('tr');
            $("#frm-edit-salary-history [name='sal_position']").val("").trigger("change");

            let sal_date = row.find("td:eq(0)").text();
            sal_date = sal_date ? moment(sal_date).format("MM/DD/YYYY") : "";

            const position = row.find("td:eq(2)").text();

            const form = $("#frm-edit-salary-history");

            form.find("[name='id']").val(id);
            form.find("[name='sal_date']").val(sal_date);
            form.find(".sal_date_container").datepicker("update", sal_date);

            let rate = row.find("td:eq(1)").text();
            rate = rate ? parseFloat(rate).toLocaleString(undefined, {minimumFractionDigits: 2}) : "";
            form.find("[name='sal_rate']").val(rate);
            form.find("[name='sal_remarks']").val(row.find("td:eq(3)").text());

            if (!isNaN(position_id)) {
                const option = new Option(position, position_id, false, true);
                $("#frm-edit-salary-history [name='sal_position']").append(option).trigger("change");
            }

            $("#edit-salary-history-modal").modal("show");
        });

    $.validate({
        form: "#frm-add-salary-history",
        lang: "en",
        scrollToTopOnError: false,
        onSuccess: function (form) {
            const currentForm = $(form);
            var formData = currentForm.serialize();

            $.ajax({
                url: baseUrl("payroll/employee/add_salary_history"),
                type: "post",
                dataType: "json",
                data: formData,
                beforeSend: function () {
                    currentForm
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (json) {
                    if (json) {
                        toastr.success("Salary saved.", "Salary successfully saved.", 10000);
                        currentForm.resetForm();
                        $("[name='sal_position']").val('').trigger('change');
                        $("#add-salary-history-modal").modal("hide");
                        dtSalaryHistory.ajax.reload();
                    } else {
                        toastr.error("Error", "Error saving new salary!", 10000);
                    }

                    currentForm
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                }
            });
            return false;
        }
    });

    $.validate({
        form: "#frm-edit-salary-history",
        lang: "en",
        scrollToTopOnError: false,
        onSuccess: function (form) {
            const currentForm = $(form);
            var formData = currentForm.serialize();

            $.ajax({
                url: baseUrl("payroll/employee/edit_salary_history"),
                type: "post",
                dataType: "json",
                data: formData,
                beforeSend: function () {
                    currentForm
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (json) {
                    if (json) {
                        toastr.success("Salary Updated.", "Salary successfully updated.", 10000);
                        currentForm.resetForm();
                        currentForm.find("[name='sal_position']").val('').trigger('change');
                        $("#edit-salary-history-modal").modal("hide");
                        dtSalaryHistory.ajax.reload();
                    } else {
                        toastr.error("Error", "Error updating salary!", 10000);
                    }

                    currentForm
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                }
            });
            return false;
        }
    });
}
/* --END-- SALARY HISTORY */

var vmBankInfo = new Vue({
    el: "#frmEditBankData-container",
    data: { row: tempData }
});

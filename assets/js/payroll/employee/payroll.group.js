const tablePayrollGroup = $("#table-payroll_group");
let dtPayrollGroup, search_val;
const btnNewEmployeeGroup = $("#btnNewEmployeeGroup");
const documentModal = $("#documentModal");
let propAllFilter = false;

let _companies = [];
if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){
        _companies = _tempContentData.company;
    }
}

if (typeof tablePayrollGroup !== "undefined") {
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

                $.validate({
                    form: documentModal.find("form"),
                    lang: "en",
                    onSuccess: function (form) {
                        var currentForm = form[0];
                        var formUrl = currentForm.action;
                        var formData = $(currentForm).serialize();
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
                documentModal
                    .empty()
                    .html(json.html);
                const employeeSelect2 = documentModal.find("select#employee_id");
                const companySelect2 = documentModal.find("select#company_id");
                const companyAllFilter = documentModal.find("input#all_company_filter");
                const allowView = documentModal.find("input#allow_view");
                const assignSelect = documentModal.find('select#assign_employee_id');

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

                    if (typeof tempRow.employees == "object" && typeof tempRow.employees !== "undefined") {
                        employeeSelect2.empty();
                        $.each(tempRow.employees, function (ii, vv) {
                            var tempOption = new Option(vv.text, vv.id, true, true);
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

                $.validate({
                    form: documentModal.find("form"),
                    lang: "en",
                    onSuccess: function (form) {
                        var currentForm = form[0];
                        var formUrl = currentForm.action;
                        var formData = $(currentForm).serialize();
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
        }
    });
});

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

var renderNotificationRecords = function () {
    $.ajax({
        url: siteUrl("payroll/employee/get_duplicate_payroll_group"),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                vmNotification.count = json.duplicate_count;
                vmModalEntries.count = json.duplicate_count;
                vmModalEntries.data = Object.assign({}, json.data);
                if(json.duplicate_count > 0){
                    toastr.info("A total of ("+json.duplicate_count+") duplicate payroll group found!", "Duplicate Payroll Group", { timeOut: 0, extendedTimeOut: 0 });
                }
            } else {
                vmNotification.count = 0;
                vmModalEntries.count = 0;
                vmModalEntries.data = Object.assign({});
            }
        }
    })
}

$(document).ready(function () {
    renderNotificationRecords();
});

var vmNotification = new Vue({
    el: "#group_notification",
    data: { count: 0, notification_clicked: false },
    methods: {
        toggleClicked: function () {
            let _this = this;
            if (_this.notification_clicked === false) { _this.notification_clicked = true; }
            toastr.clear();
            return _this.notification_clicked;
        }
    }
});

var vmModalEntries = new Vue({
    el: "#modal-duplicate-entries",
    data: { count: 0, data: {} }
});
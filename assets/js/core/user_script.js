var search_val = "";

$("#select2_group").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
        url: baseUrl("users/get_group"),
        delay: 250,
        processResults: function (data) {
            return data;
        }

    }
});
$("#select2_employee").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    dropdownParent: $('#modal_form_user'),
    ajax: {
        url: baseUrl("users/get_employee"),
        dataType: "json",
        delay: 250,
        global: false,
        processResults: function (data) {
            return data;
        }
    }
});
var _modalAssignRole = $("#modal-user_role-assign");
var _dtUsers = $("#table-users").DataTable({
    dom: '<"toolbar">frtlip',
    paging: false,
    serverSide: true,
    processing: true,
    searching: false,
    ajax: {
        url: baseUrl("core/users/get_user_list"),
        type: "POST",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
            return d;
        }, error: function (xhr, error, code) {
            if (error == "parsererror") { 
                _dtUsers.ajax.reload(null, false); 
                toastr.warning("Re-loading, error in rendering list data!", "MANAGE USERS LIST");
            }
        }, global: false,
    },
    columns: [
        { data: "biometricno", width: "10%" },
        { data: "lastname", width: "15%" },
        { data: "firstname", width: "15%" },
        { data: "middlename", width: "15%" },
        { data: "email", width: "15%" },
        { data: "description", width: "17%" },
        { data: "employee_status", width: "5%" },
        { data: null, width: "8%" }
    ],
    columnDefs: [{
        data: "biometricno",
        defaultContent: "---",
        targets: 0,
        render: function (data, type, row, meta) {
            return (typeof data !== "undefined" && data !== null && data !== "") ? data : "---";
        }
    }, {
        data: "middlename",
        defaultContent: "N/A",
        targets: 3,
        render: function (data, type, row, meta) {
            return (typeof data !== "undefined" && data !== null && data !== "") ? data : "N/A";
        }
    }, {
        data: "email",
        defaultContent: "none",
        targets: 4,
        render: function (data, type, row, meta) {
            return (typeof data !== "undefined" && data !== null && data !== "") ? data : "NO EMAIL ADDRESS";
        }
    }, {
        data: null,
        defaultContent: "",
        targets: -1,
        orderable: false,
        render: function (data, type, row, meta) {
            return userDatatableActions(row.id);
        }
    }, {
        data: "employee_status",
        defaultContent: "",
        targets: 6,
        orderable: false,
        className: "dt-column-center",
        render: function (data, type, row, meta) {
            return userDatatableStatus(row.employee_status);
        }
    }, {
        targets: "_all",
        defaultContent: ""
    }
    ],
    scrollY: "55vh",
    scrollCollapse: true,
    initComplete: function (settings, json) {
        if (typeof aclActionUpdate == "function") {
            aclActionUpdate();
        }
    }
});

function userDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        if (
            typeof _currentActions !== "undefined" &&
            jQuery.inArray("edit", _currentActions) !== -1
        ) {
            _actionButton +=
                "<button type='button' title='Click to Assign User Role' class='btn btn-default m-btn m-btn--hover-accent btn-sm m-btn--icon m-btn--icon-only m-btn--pill btnAssignUser btnAssign' data-id='" +
                $id +
                "'><i class='la la-edit'></i></button>";
            _actionButton += " <button type='button' title='Click to Edit User' class='btn btn-default m-btn m-btn--hover-accent btn-sm m-btn--icon m-btn--icon-only m-btn--pill btnEditItem btnEdit' onclick='edit_user(" + $id + ")'><i class='la la-eyedropper'></i></button>";
            _actionButton += " <button type='button' title='Click to Suspend User'" +
                "                      class='btn btn-default m-btn m-btn--hover-warning btn-sm m-btn--icon m-btn--icon-only m-btn--pill btnSuspend_action' " +
                "                      onclick='open_suspend_user_confirmation(" + $id + ")'><i class='la la-warning'></i></button>";
        }
        return _actionButton;
    } else {
        return false;
    }
}

function userDatatableStatus($status) {
    var _html = "";
    if ($status == "Active") {
        _html =
            "<span class='btn btn-success m-btn m-btn--icon m-btn--icon-only btn-sm' title='ACTIVE'><i class='la la-user'></i></span>";
    } else {
        $status = $status == null ? "DEVELOPMENT" : $status.toUpperCase();
        _html =
            "<span class='btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm' title='" +
            $status +
            "'><i class='la la-user'></i></span>";
    }
    return _html;
}

jQuery(document).on("click", "#table-users .btnAssignUser", function () {
    var dataId = $(this).data("id");
    $.ajax({
        url: baseUrl("core/users/get_user_data"),
        type: "POST",
        dataType: "json",
        data: { id: dataId, csrf_token: _csrf_hash },
        success: function (json) {
            if (json.response) {
                var _modalContent = _modalAssignRole.find(".modal-content");
                if (typeof _modalContent !== "undefined") {
                    _modalContent.empty().append(json.html);
                    $(_modalAssignRole).modal("show");
                }
            }
        }
    });
});

jQuery(document).on(
    "click",
    "#modal-user_role-assign #form-users-assign .btn-submit",
    function () {
        var _self = $(this);
        var _form = _self.parent(".modal-footer").parent("#form-users-assign");
        if (typeof _form !== "undefined") {
            $.ajax({
                url: _form.attr("action"),
                type: "POST",
                dataType: "json",
                data: _form.serialize(),
                success: function (json) {
                    if (json.response) {
                        _dtUsers.ajax.reload();
                        toastr.success(json.message, "Assign User Role", 5000);
                        $(_modalAssignRole).modal("hide");
                    } else {
                        toastr.error(json.message, "Assign User Role", 5000);
                    }
                }
            });
        }
    }
);

function open_user() {
    save_method = 'add';
    document.getElementById('employee').style.removeProperty('display');
    $('#form_user')[0].reset();
    $('#modal_form_user').modal('show'); // show bootstrap modal
    $('.modal-title').text('New User'); // Set Title to Bootstrap modal title

}

function open_suspend_user_confirmation(id) {
    const _modal = $("#modal-confirm-suspend-user");
    _modal.find("form").attr("action", baseUrl("users/process_suspend_account/" + id));
    _modal.modal("show");
}

function process_suspend_account(el) {
    const url = $(el).attr("action");
    $.ajax({
        url,
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            if (response.success) {
                $(el).attr("action", "");
                $("#modal-confirm-suspend-user").modal("hide");
                toastr.success(response.message, "Success", 10000);
                _dtUsers.ajax.reload();
            } else {
                toastr.error(response.message, "Error", 10000);
            }
        }
    });
}

function edit_user(id) {
    document.getElementById('employee').style.display = 'none';
    save_method = 'update';
    $('#form_user')[0].reset();
    $.ajax({
        url: baseUrl("users/edit_user/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            var newOption = new Option(data.group_name, data.group_id, true, true);
            $('#select2_group').append(newOption).trigger('change');
            $('[name="username"]').val(data.username);
            $('[name="password"]').val(data.password);
            $('[name="email"]').val(data.email);
            $('[name="telegram_chat_id"]').val(data.telegram_chat_id);
            $('#modal_form_user').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit User'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function save_user() {
    var url;

    if (save_method == 'add') {
        url = baseUrl("users/add_user/");
    } else {
        url = baseUrl("users/update_user/");
    }


    $.validate({
        form: '#form_user',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form_user').serialize(),
                dataType: "JSON",

                success: function (data) {
                    if (data.status) {

                        _dtUsers.ajax.reload();
                        $("#modal_form_user").modal("hide");
                        toastr.success("User data updated!", "Success", 10000);
                    } else {
                        toastr.error("Failed updating data!", "Failed", 10000);
                    }

                }
            });
            return false;
        },
    });
}

$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    _dtUsers.ajax.reload();
});
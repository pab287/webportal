let search_val = "";
let save_method = "add";

let userRoles = [];
if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.user_role !== "undefined" && _tempContentData.user_role.length > 0){
        userRoles = _tempContentData.user_role;
    }
}

$("select#user_role").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    data: userRoles,
    dropdownParent: $('#modal_form_user')
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
const _modalAssignRole = $("#modal-user_role-assign");
const _dtUsers = $("#table-users").DataTable({
    dom: '<"toolbar">frtlip',
    paging: false,
    serverSide: true,
    processing: true,
    searching: false,
    order: [0, "desc"],
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
        { data: "id", visible: false, searchable: false },
        { data: "biometricno", width: "8%", defaultContent: "",
            render: function (data, type, row, meta) {
                return (typeof data !== "undefined" && data !== null && data !== "") ? data : "---";
            }
        }, { data: "firstname", width: "18%", render: function (_data, _type, row) {
            return row.employee_name;
        }},
        { data: "username", width: "18%",
            render: function (data, type, row, meta) {
                let tempHtml = `<p class='mb-0'>${data}</p>`;
                tempHtml += `<p><small class='m--font-bolder'>${row.email ? row.email : "NO EMAIL ADDRESS"}</small></p>`;
                return tempHtml;
        }},
        { data: "user_role", width: "*", render: function (data, _type, row) {
            let tempHtml = `<p class='mb-0'>${data}</p>`;
            tempHtml += `<p><small class='m--font-bolder'>PS.ID: ${row.telegram_chat_id ? row.telegram_chat_id : "---"}</small></p>`;
            return tempHtml;
        }},
        { data: "is_important", width: "10%", orderable: false, className: "text-center",
            render: function (data) {
                return parseInt(data) === 1 ? "<span class='m-badge m-badge--success m-badge--wide m--font-boldest'>YES</span>"
                : "<span class='m-badge m-badge--danger m-badge--wide m--font-boldest'>NO</span>";
        }},
        { data: "employee_status", width: "5%", orderable: false, className: "text-center",
            render: function (data, type, row, meta) {
                return userDatatableStatus(row.employee_status);
        }},
        { data: null, width: "8%", orderable: false, className: "text-center",
            render: function (data, type, row, meta) {
                return userDatatableActions(row.id);
        }}
    ],
    columnDefs: [{defaultContent: "---", targets: "_all"}],
    scrollY: "55vh",
    scrollCollapse: true
});

function userDatatableActions($id) {
    if ($id) {
        let _actionButton = "";
        if (typeof _currentActions !== "undefined" && jQuery.inArray("edit", _currentActions) !== -1) {
            _actionButton +=
                "<button type='button' title='Click to Assign User Role' class='btn btn-default m-btn m-btn--hover-accent btn-sm m-btn--icon m-btn--icon-only m-btn--pill btnAssignUser btnAssign' data-id='" +
                $id +
                "'><i class='la la-edit'></i></button>";
            _actionButton += " <button type='button' title='Click to Edit User' class='btn btn-default m-btn m-btn--hover-accent btn-sm m-btn--icon m-btn--icon-only m-btn--pill btnEditItem btnEdit' onclick='edit_user(" + $id + ")'><i class='la la-eyedropper'></i></button>";
            _actionButton += " <button type='button' title='Click to Suspend User'" +
                " class='btn btn-default m-btn m-btn--hover-warning btn-sm m-btn--icon m-btn--icon-only m-btn--pill btnSuspend_action' " +
                " onclick='open_suspend_user_confirmation(" + $id + ")'><i class='la la-warning'></i></button>";
        }
        return _actionButton;
    } else {
        return false;
    }
}

function userDatatableStatus($status) {
    let _html = "";
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
    const dataId = $(this).data("id");
    $.ajax({
        url: baseUrl("core/users/get_user_data"),
        type: "POST",
        dataType: "json",
        data: { id: dataId, csrf_token: _csrf_hash },
        success: function (json) {
            if (json.response) {
                const _modalContent = _modalAssignRole.find(".modal-content");
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
        const _self = $(this);
        const _form = _self.parent(".modal-footer").parent("#form-users-assign");
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
    $('#form_user').find("select#user_role").val("").trigger("change");
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
            $('select#user_role').val(data.role_id).trigger('change');
            $('[name="username"]').val(data.username);
            $('[name="password"]').val(data.password);
            $('[name="email"]').val(data.email);
            $('[name="telegram_chat_id"]').val(data.telegram_chat_id);
            const propChecked = parseInt(data.is_important) === 1;
            $('[name="is_important"]').prop('checked', propChecked);
            $('#modal_form_user').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit User'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    _dtUsers.ajax.reload();
});

$.validate({
    form: '#form_user',
    lang: 'en',
    onSuccess: function (form) {
        const url = save_method == 'add' ? siteUrl("users/add_user") : siteUrl("users/update_user");
        const currentForm = $(form);
        const formData = currentForm.serialize();
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                if (data.status) {
                    _dtUsers.ajax.reload(null, false);
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
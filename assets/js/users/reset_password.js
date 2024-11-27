let search = "";

let tblResetPassword = $("#table-reset-password")
    .DataTable({
        dom: 'frtlip',
        serverSide: true,
        processing: true,
        autoWidth: false,
        order: [2, "asc"],
        ajax: {
            url: baseUrl("users/get_active_user_list"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search;
            }, error: function (xhr, error, code) {
                if (error == "parsererror") { 
                    tblResetPassword.ajax.reload(null, false); 
                    toastr.warning("Re-loading, error in rendering list data!", "ACTIVE USERS LIST");
                }
            }, global: false,
        },
        searching: false,
        columns: [
            {
                data: "email",
                width: "23.75%"
            },
            {
                data: "username",
                width: "23.75%"
            },
            {
                data: "firstname",
                width: "23.75%"
            },
            {
                data: "lastname",
                width: "23.75%"
            },
            {
                data: "",
                width: "5%",
                orderable: false,
                render: function (data, type, row) {
                    return "<button class='btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill' " +
                        "           title='Click to reset password.' onclick='openConfirmModal(" + row.id + ")'>" +
                        "   <i class='fa fa-unlock'></i>" +
                        "   </button>";
                },
                className: "text-center"
            }
        ]
    });

$("#generalSearch")
    .donetyping(function () {
        search = $(this).val();
        tblResetPassword.ajax.reload();
    });

$(".m-content")
    .on("submit", "#confirmation-dialog",
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");

            $.ajax({
                url,
                type: "GET",
                dataType: "JSON",
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message, "Success", 10000);
                        $(".document-modal-container").find("form").attr("action", "");
                    } else {
                        toastr.error(response.message, "Error", 10000);
                    }

                    closeDialog();
                }
            });
        });

function clearSearch() {
    search = "";
    tblResetPassword.ajax.reload();
    $("#generalSearch").val("");
}

function openConfirmModal(id) {
    $.ajax({
        url: baseUrl("users/open_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {
                title: "Reset Password",
                message: "Are you sure to reset this users password?",
                action: "users/reset_selected_user_password/?id=" + id,
            },
            path: "ams/confirmation_dialog",
            function_name: "passDataToDialog"
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}

function closeDialog() {
    $(".document-modal-container").modal("hide");
}
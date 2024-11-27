let search = "";
const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];

let tblSuspendedUsers = $("#table-suspended-users")
    .DataTable({
        dom: 'frtlip',
        serverSide: true,
        processing: true,
        autoWidth: false,
        order: [1, "asc"],
        ajax: {
            url: baseUrl("users/get_suspended_users_list"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search;
            }, error: function (xhr, error, code) {
                if (error == "parsererror") { 
                    tblSuspendedUsers.ajax.reload(null, false); 
                    toastr.warning("Re-loading, error in rendering list data!", "SUSPENDED USERS LIST");
                }
            }, global: false,
        },
        searching: false,
        columns: [
            {
                data: "email",
                render: function (data) {
                    return data ? data : "NO EMAIL ADDRESS";
                }
            },
            {
                data: "lastname",
            },
            {
                data: "firstname",
            },
            {
                data: "middlename",
            },
            {
                data: "suspended_by",
                render: function (data) {
                    return data ? data : "--";
                }
            },
            {
                data: "suspended_dt",
                render: function (data) {
                    if (data) {
                        const date = new Date(data);
                        return months[date.getMonth()] + " " + date.getDate() + ", " + date.getFullYear();
                    }

                    return "--";
                }
            },
            {
                data: "",
                width: "5%",
                orderable: false,
                render: function (data, type, row) {
                    return "<button onclick='openRemoveSuspensionConfirmation(" + row.id + ")' " +
                        "           title='Click to remove suspension.' " +
                        "           class='btn btn-default m-btn m-btn--hover-primary m-btn--icon m-btn--icon-only m-btn--pill btnNew'>" +
                        "       <i class='la la-reply'></i>" +
                        "   </button>";
                },
                className: 'text-center'
            }
        ]
    });

$("#generalSearch")
    .donetyping(function () {
        search = $(this).val();
        tblSuspendedUsers.ajax.reload();
    });

function clearSearch() {
    search = "";
    tblSuspendedUsers.ajax.reload();
    $("#generalSearch").val("");
}

function openRemoveSuspensionConfirmation(id) {
    const _modal = $("#modal-confirm-remove-suspension");
    _modal.find("form").attr("action", baseUrl("users/process_unsuspend_account/" + id));
    _modal.modal("show");
}

function process_unsuspend_account(el) {
    const url = $(el).attr("action");
    $.ajax({
        url,
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            if (response.success) {
                $(el).attr("action", "");
                $("#modal-confirm-remove-suspension").modal("hide");
                toastr.success(response.message, "Success", 10000);
                tblSuspendedUsers.ajax.reload();
            } else {
                toastr.error(response.message, "Error", 10000);
            }
        }
    });
}
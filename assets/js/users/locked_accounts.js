let search_val = "";
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblLockedUsers.ajax.reload();
});

let tblLockedUsers = $("#table-locked-users").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    searching: false,
    ajax: {
        url: baseUrl("users/get_locked_accounts"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
            return d;
        }, error: function (xhr, error, code) {
                if (error == "parsererror") { 
                    tblLockedUsers.ajax.reload(null, false); 
                    toastr.warning("Re-loading, error in rendering list data!", "LOCKED ACCOUNTS LIST");
                }
            }, global: false,
    },
    order: [0, "desc"],
    columns: [
        { data: "lockout_dt", visible: false, searchable: false },
        { data: "lockout_dt", width: "12%" },
        { data: "lastname", width: "18%", render: function (_data, _type, row) {
            return row.employee_name != null ? row.employee_name : "No Account Name";
        }},
        { data: "username", render: function (data, _type, row) {
                const trimmedEmail = $.trim(row.email);
                const tempEmail = trimmedEmail !== "" && trimmedEmail !== null ? trimmedEmail : "NO EMAIL";
                return `<p class='mb-0'>${data}</p><p><small class='m--font-bolder'>${tempEmail}</small></p>`;
            }
        },
        { data: "null", width: "6%", className: "text-center", orderable: false },
    ],
    columnDefs: [{
        data: null,
        defaultContent: "",
        targets: -1,
        orderable: false,
        render: function(data, type, row, meta) {
            return `
                <button type="button" class="btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRestore" onclick="unlockAccount(${row.id})"
                        data-toggle="m-tooltip" 
                        data-placement="bottom" 
                        data-skin="dark" 
                        data-original-title="Unlock Account" 
                        data-delay="{\"show\": 300}">
                    <i class="la la-reply"></i>
                </button>
            `;
        }
    }]

});

function unlockAccount(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to unlock this account.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, unlock it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        position: 'top',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: siteUrl("users/unlock_account/"),
                type: "POST",
                dataType: "json",
                data: {
                    csrf_token: _csrf_hash, 
                    id:id,
                },
                success: function (json) {
                    if (json.status) {
                        toastr.success(json.message, "Locked Accounts", 5000);
                        tblLockedUsers.ajax.reload();
                    } else {
                        toastr.error(json.message, "Locked Accounts", 5000);
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error("An error occurred while restoring Account.", "Locked Accounts", 5000);
                }
            });
        }
    });
}
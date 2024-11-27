var dtProtocol = $("#table-email-protocol");
var dtTableProtocol;
var search_val = "";
var modalNewProtocol = $("#new_modal");
var modalEditProtocol = $("#edit_modal");
var modalRemoveProtocol = $("#delete_modal");

$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    dtTableProtocol.ajax.reload();
});

if (typeof dtProtocol !== "undefined" && dtProtocol.length == 1) {
    dtTableProtocol = dtProtocol.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("configuration/email_protocol_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                return d;
            }
        },
        searching: false,
        columns: [
            { data: "server_name" },
            { data: "unique_code", width: "10%" },
            { data: "protocol", width: "8%" },
            { data: "smtp_host" },
            { data: "smtp_user" },
            { data: "smtp_port", width: "5%" },
            { data: "smtp_crypto", width: "5%" },
            { data: null, width: "10%", className: "text-center" },
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) { return itemDatatableActions(row.id); },
            }
        ]
    });
}

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += "<button type='button' onclick='edit_protocol(" + $id + ")' data-toggle='modal' data-target='#edit_modal' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-pencil-square'></i></button>";
        _actionButton += "<button type='button' onclick='delete_protocol(" + $id + ")' data-toggle='modal' data-target='#delete_modal' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-trash'></i></button>";
        return _actionButton;
    } else { return false; }
}

$.validate({
    form: "#new_form",
    lang: 'en',
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
            success: function (json) {
                if (json.response) {
                    currentForm.reset();
                    dtTableProtocol.ajax.reload();
                    modalNewProtocol.modal("hide");
                    toastr.success(json.toastr_msg, "Protocol Settings", 5000);
                } else {
                    toastr.error(json.toastr_msg, "Protocol Settings", 5000);
                }
                $(form[0])
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            }
        });
        return false;
    },
});

$.validate({
    form: "#edit_form",
    lang: 'en',
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
            success: function (json) {
                if (json.response) {
                    currentForm.reset();
                    dtTableProtocol.ajax.reload();
                    modalEditProtocol.modal("hide");
                    toastr.success(json.toastr_msg, "Protocol Settings", 5000);
                } else {
                    toastr.error(json.toastr_msg, "Protocol Settings", 5000);
                }
                $(form[0])
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            }
        });
        return false;
    },
});

$(document).on("submit", "#remove_form", function (e) {
    e.preventDefault();
    $.ajax({
        url: e.target.action,
        dataType: "json",
        type: e.target.method,
        data: $(e.target).serialize(),
        beforeSend: function () {
            $(e.target)
                .find(".btn-submit")
                .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }, success: function (json) {
            if (json.response) {
                dtTableProtocol.ajax.reload();
                modalRemoveProtocol.modal("hide");
            }

            $(e.target)
                .find(".btn-submit")
                .removeClass(
                    "m-btn--custom m-loader m-loader--light m-loader--right"
                );
        }
    });
    console.log(e.target);
    console.log(e.target.action);
});
var delete_protocol = function (id) {
    if (id) {
        return $.ajax({
            url: siteUrl("configuration/get_email_protocol_by_id/" + id),
            dataType: "json",
            success: function (json) {
                if (json.response) {
                    vmDeleteModal.row = Object.assign({}, json.row);
                }
            }
        });
    } else {
        return false;
    }
}

var edit_protocol = function (id) {
    if (id) {
        return $.ajax({
            url: siteUrl("configuration/get_email_protocol_by_id/" + id),
            dataType: "json",
            success: function (json) {
                if (json.response) {
                    vmEditModal.row = Object.assign({}, json.row);
                }
            }
        });
    } else {
        return false;
    }
}

var vmEditModal = new Vue({
    el: "#edit-modal_body",
    data: { row: {} }
});

var vmDeleteModal = new Vue({
    el: "#delete-modal_body",
    data: { row: {} }
});
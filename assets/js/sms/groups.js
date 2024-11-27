var search_val = "";
var tblGroup = $("#table-groups").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("sms/get_groups_collection/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.search['value'] = search_val
        }
    },
    searching: true,
    columns: [

        {data: "group_name", width: "90%"},
        {data: null, width: "10%", className: "text-center"},
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,

            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id);
            },
        }


    ]
});

$("#delete_modal").hide();

$("#select2_contact").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    allowClear: true,
    ajax: {
        url: baseUrl("sms/get_contact_select"),
        delay: 500,
        processResults: function (data) {
            return data;
        }

    }
});

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += " " +
            "<button " +
            "   type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
            "   onclick='edit_group(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Edit Group.'" +
            "   data-skin='dark'>" +
            "   <i class='la la-pencil-square'></i>" +
            "</button>";
        _actionButton += " <button " +
            "   type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' " +
            "   onclick='delete_group(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Delete Group.'" +
            "   data-skin='dark'>" +
            "<i class='la la-trash'></i>" +
            "</button>";
        _actionButton += " <button " +
            "   type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill btnListItem' " +
            "   onclick='open_contacts(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Manage Members.'" +
            "   data-skin='dark'>" +
            "   <i class='la la-list-ul'></i>" +
            "</button>";
        return _actionButton;
    } else {
        return false;
    }
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblGroup.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblGroup.ajax.reload();
});

function open_group() {
    save_method = 'add';
    $('#form_groups')[0].reset();
    $('#modal_form_groups').modal('show'); // show bootstrap modal
    $('.modal-title').text('New Group'); // Set Title to Bootstrap modal title
}

function edit_group(id) {
    save_method = 'update';
    $('#form_groups')[0].reset();
    $.ajax({
        url: baseUrl("sms/edit_group/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            $('[name="group_name"]').val(data.group_name);
            $('#modal_form_groups').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit Group'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function delete_group($id) {
    $('#delete_modal').modal("show");

    $.validate({
        form: '#delete_form',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("sms/delete_group/") + $id,
                type: "POST",
                dataType: "json",
                data: $("#delete_form").find("input,textarea").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data) {
                        toastr.success(data.toastr_msg, "Notification: Successfully saved", 5000);
                        $('#delete_modal').modal('hide');
                        $('#delete_form')[0].reset();
                        tblGroup.ajax.reload();
                    } else {
                        toastr.error(data.toastr_msg, "Notification: Error", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
}

function save_group() {
    var url;

    if (save_method == 'add') {
        url = baseUrl("sms/add_group/");
    } else {
        url = baseUrl("sms/update_group/");
    }

    $.validate({
        form: '#form_groups',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form_groups').serialize(),
                dataType: "JSON",
                success: function (data) {
                    if (data.status) {
                        tblGroup.ajax.reload();
                        $("#modal_form_groups").modal("hide");
                        toastr.success("Contact was successfully save.", "Success", 1000);
                    } else {
                        toastr.warning(data.msg, "", 1000);
                    }
                }
            });
            return false;
        },
    });
}

function open_contacts(id) {
    $('[name="group_id"]').val(id);
    $('#modal_form_contacts').modal('show'); // show bootstrap modal
    $('.modal-title').text('Contact List'); // Set Title to Bootstrap modal title
    edit_group2(id)
}

function edit_group2(id) {
    $('#form_contacts')[0].reset();
    $.ajax({
        url: baseUrl("sms/edit_group/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="group_name2"]').val(data.group_name);
            contactlist(id);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function contactlist(id) {
    // $('#table-content').dataTable().fnClearTable();
    $('#table-content').dataTable().fnDestroy();
    var tblContent = $("#table-content").DataTable({
        dom: '<"toolbar">rtlp',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("sms/get_groups_contact/") + id,
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
            }
        },
        searching: true,
        columns: [
            {data: "name", width: "50%"},
            {data: "cp_no", width: "40%"},
            {data: null, width: "10%", className: "text-center"},
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,

                render: function (data, type, row, meta) {
                    return itemDatatableActions2(row);
                },
            }
        ]
    });
}

function itemDatatableActions2(row) {
    if (row) {
        var _actionButton = "";

        _actionButton += "" +
            "<button type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' " +
            "   onclick='delete_contact(" + row.id + ",\"" + row.name+"\")'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Remove Contatct.'" +
            "   data-skin='dark'>" +
            "   <i class='la la-trash'></i>" +
            "</button>";

        return _actionButton;
    } else {
        return false;
    }
}

function delete_contact(id,name) {
    $("#delete_contact_from_members_modal").modal("show");
    
    var group_name = $('[name="group_name2"]').val();
    $("#delete_contact_from_members_modal #name").val(name);
    $("#delete_contact_from_members_modal #group_name").val(group_name);

    $.validate({
        form: '#frm-delete-contact-confirmation',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("sms/delete_group_contact/") + id,
                type: "POST",
                dataType: "json",
                data: $("#frm-delete-contact-confirmation").find("input,textarea").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data) {
                        var check = $('[name="group_id"]').val();
                        contactlist(check);
                        $("#delete_contact_from_members_modal").modal("hide");
                    } else {
                        toastr.error(data.msg, "Notification: Error", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
}

function save_contact() {
    var check = $('[name="group_id"]').val();
    const form = $("#form_contacts");

    if (form.isValid()) {
        $.ajax({
            url: baseUrl("sms/save_group_contact/") + check,
            type: "POST",
            dataType: "JSON",
            data: {csrf_token: _csrf_hash, contact: $('[name="contact"]').val()},
            success: function (data) {
                contactlist(check);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error: "save"');
            }
        });
    }
}

$(document).on('show.bs.modal', '.modal', function () {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function () {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

$(document).on('hidden.bs.modal', '.modal', function () {
    $('.modal:visible').length && $(document.body).addClass('modal-open');
});
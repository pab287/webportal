var search_val = "";
var tblTemplates = $("#table-templates").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("sms/get_templates_collection/"),
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
        {data: "template_name", width: "15%"},
        {data: "message", width: "75%"},
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

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += " <button " +
            "   type='button'" +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'" +
            "   onclick='edit_template(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Edit Template'" +
            "   data-skin='dark'>" +
            "   <i class='la la-pencil-square'></i>" +
            "</button>";
        _actionButton += " <button " +
            "   type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' " +
            "   onclick='delete_template(" + $id + ")' " +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Delete Template'" +
            "   data-skin='dark'>" +
            "   <i class='la la-trash'></i>" +
            "</button>";
        return _actionButton;
    } else {
        return false;
    }
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblTemplates.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblTemplates.ajax.reload();
});

$.validate({
    form: '#frm_status_new',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("sms/add_template"),
            type: "POST",
            dataType: "json",
            data: $("#frm_status_new").find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.status) {
                    toastr.success(data.msg, "Notification: Successfully saved", 5000);
                    $('#add_template_modal').modal('hide');
                    $('#frm_status_new')[0].reset();
                    tblTemplates.ajax.reload();
                } else {
                    toastr.warning(data.msg, "Notification: Error", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

var vmTab1 = new Vue({
    el: "#frm_status_edit",
    data: {vm_tab1: {}}
});

function edit_template($id) {
    $.ajax({
        type: "GET",
        url: baseUrl("sms/edit_template/") + $id,
        dataType: "json",
        success: function (data) {
            vmTab1.vm_tab1 = Object.assign({}, vmTab1.vm_tab1, data);
            $("#frm_status_edit").attr('action', baseUrl("sms/update_template/" + $id));
            $("#edit_template_modal").modal("show");
        }
    });
}

function process_edit_template(element) {
    const form = $(element);
    const url = form.attr('action');
    
    if (form.isValid()) {
        $.ajax({
            url,
            type: "POST",
            dataType: "json",
            data: form.serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    toastr.success(data.msg, "Notification: Successfully saved", 5000);
                    $('#edit_template_modal').modal('hide');
                    $('#frm_status_edit')[0].reset();
                    tblTemplates.ajax.reload();
                } else {
                    toastr.error(data.msg, "Notification: Error", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
    }
}

function delete_template($id) {
    $("#delete_form").attr("action", baseUrl("sms/delete_template/" + $id));
    $("#delete_modal").modal("show");
}

function processDeleteTemplate(element) {
    const form = $(element);
    const url = form.attr('action');
    $.ajax({
        url,
        type: "POST",
        dataType: "json",
        data: form.serialize(),
        beforeSend: function () {
            $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function (data) {
            if (data) {
                toastr.success("Template was removed successfully.", "Template Removed.", 5000);
                $('#delete_modal').modal('hide');
                $('#delete_form')[0].reset();
                tblTemplates.ajax.reload();
            } else {
                toastr.error(data.toastr_msg, "Error", 5000);
            }
            $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }
    });
}

$("#delete_modal").hide();
jQuery(document).ready(function () {

    fileUploadPhoto();

});

var search_val = "";
var tblContact = $("#table-contacts").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("sms/get_contacts_collection/"),
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
                return itemDatatableActions(row);
            },
        }
    ]
});


function itemDatatableActions(row) {
    if (row) {
        var _actionButton = "";
        _actionButton += " <button " +
            "   type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
            "   onclick='edit_contact(" + row.id + ")' " +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Edit Template'" +
            "   data-skin='dark'>" +
            "   <i class='la la-pencil-square'></i>" +
            "</button>";
        _actionButton += " <button " +
            "   type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' " +
            "   onclick='delete_contact(" + row.id +", \""+row.name+"\")' " +
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
    tblContact.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblContact.ajax.reload();
});

function open_contact() {
    save_method = 'add';
    $('#form_contacts')[0].reset();
    $('#modal_form_contacts').modal('show'); // show bootstrap modal
    $('#modal_form_contacts .modal-title').text('New Contact'); // Set Title to Bootstrap modal title
}

function open_import() {
    $('#upload_csv')[0].reset();
    $('#import_modal').modal('show'); // show bootstrap modal
}

function edit_contact(id) {
    save_method = 'update';
    $('#form_contacts')[0].reset();
    $.ajax({
        url: baseUrl("sms/edit_contact/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            $('[name="firstname"]').val(data.firstname);
            $('[name="lastname"]').val(data.lastname);
            $('[name="cp_no"]').val(data.cp_no);
            $('#modal_form_contacts').modal('show'); // show bootstrap modal
            $('#modal_form_contacts .modal-title').text('Edit Contact'); // Set Title to Bootstrap modal title
            $("#modal_form_contacts").modal("show");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function delete_contact(id, name) {
    $("#delete_form").attr('action', baseUrl("sms/delete_contact/" + id));
    $('#delete_modal #name').val(name);
    $("#delete_modal").modal("show");
}

function processDeleteContact(element) {
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
                toastr.success("Contact was removed successfully.", "Remove Contact.", 5000);
                $('#delete_modal').modal('hide');
                $('#delete_form')[0].reset();
                tblContact.ajax.reload();
            } else {
                toastr.error(data.msg, "Error", 5000);
            }
            $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }
    });
}

$("#delete_modal").hide();


function save_contact(event) {

    var url;

    if (save_method == 'add') {
        url = baseUrl("sms/add_contact/");
    } else {
        url = baseUrl("sms/update_contact/");
    }

    $.validate({
        form: '#form_contacts',
        lang: 'en',
        scrollToTopOnError: false,
        onSuccess: function (form) {
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form_contacts').serialize(),
                dataType: "JSON",
                success: function (data) {
                    if (data.status) {
                        tblContact.ajax.reload();
                        $("#modal_form_contacts").modal("hide");
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

function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode > 31 
    && (charCode < 48 || charCode > 57))
    return false;
    return true;
}

var fileUploadPhoto = function () {
    var url = baseUrl("sms/upload_recipients_contacts");
    $("#fileupload").fileupload({
            url: url,
            dataType: "json",
            formData: {csrf_token: _csrf_hash},
            done: function (e, data) {
                var result = data.result;
                if (result.response) {
                    var filePath = result.added_file;
                    var renderFile = result.render_file;
                    $("#file_append").text(renderFile);
                    $("#upload").on("click", function () {
                        if(filePath){
                            $.ajax({
                                url: baseUrl("sms/import_uploads_contacts/"),
                                formData: {csrf_token: _csrf_hash},
                                data: {filePath},
                                dataType: "json",
                                beforeSend: function data(){
                                    filePath = "";
                                },
                                success: function data(data) {
                                    if(data.response == "Done"){
                                        filePath = "";
                                        tblContact.ajax.reload();
                                        $("#fileupload").val(null);
                                        $("#file_append").text("");
                                        $("#file_append").val(null);
                                        $('#import_modal').modal('hide');
                                        toastr.success(result.toastr_msg, "Upload Done", {timeOut: 5000});
                                        if (data.duplicates && data.duplicates.length > 0) {
                                          var duplicatesCount = data.duplicates.length;
                                          //wait before firing toast
                                          toastr.warning('There are ' + duplicatesCount + ' duplicates.', "Duplicate Contacts", {timeOut: 5000});
                                        }
                                        if (data.missing && data.missing.length > 0) {
                                          var missingCount = data.missing.length;
                                          //wait before firing toast
                                          toastr.warning('There are ' + missingCount + ' missing items.', "Missing Data", {timeOut: 5000});
                                        }

                                    }else{
                                        toastr.error(data.response, "Error", 5000);
                                        $('#import_modal').modal('hide');
                                    }
                                }
                            });
                        }
                    });
                } else {
                    toastr.error(result.toastr_msg, "File error", 5000);
                }
            }
        });
}
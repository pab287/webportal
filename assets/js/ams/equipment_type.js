$("#select2_category").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
        url: baseUrl("ams/maintenance/get_equipment_category"),
        processResults: function (data) {
            return data;
        }
    }
});

var search_val = "";
var tblType = $("#table-equipment_type").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("ams/maintenance/get_equipment_type_collection"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
        }
    },
    searching: true,
    columns: [

        {data: "desc", width: "40%"},
        {data: "description", width: "40%"},
        {data: "code", width: "10%"},
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
        _actionButton += " <button type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
            "   onclick='edit_equipment_type(" + $id + ")'" +
            "   data-toggle='m-tooltip'" +
            "   data-placement='bottom'" +
            "   data-skin='dark'" +
            "   data-delay='{\"show\": 300}'" +
            "   data-original-title='Edit'>" +
            "   <i class='la la-pencil-square'></i>" +
            "</button>";
        _actionButton += " <button type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' " +
            "   onclick='open_delete(" + $id + ")'" +
            "   data-toggle='m-tooltip'" +
            "   data-placement='bottom'" +
            "   data-skin='dark'" +
            "   data-delay='{\"show\": 300}'" +
            "   data-original-title='Delete'>" +
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
    tblType.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblType.ajax.reload();
});

function open_equipment_type() {
    save_method = 'add';
    $('#form_equipment_type')[0].reset();
    $('#modal_form_equipment_type').modal('show'); // show bootstrap modal
    $('.modal-title').text('New Equipment Category'); // Set Title to Bootstrap modal title
}

function open_delete($id) {
    $('[name="delete_id"]').val($id);
    $('#modal_form_delete').modal('show'); // show bootstrap modal
    $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}

function edit_equipment_type(id) {

    save_method = 'update';
    $('#form_equipment_type')[0].reset();
    $.ajax({
        url: baseUrl("ams/maintenance/edit_equipment_type/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            var newOption = new Option(data.desc, data.ec_id, false, true);
            $('#select2_category').append(newOption).trigger('change');
            $('[name="code"]').val(data.code);
            $('[name="description"]').val(data.description);
            $('#modal_form_equipment_type').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit Equipment Category'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function delete_equipment_type() {
    $temp = $('[name="delete_id"]').val();
    // ajax delete data to database
    $.ajax({
        url: baseUrl("ams/maintenance/delete_equipment_type/") + $temp,
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash},
        success: function (data) {
            //if success reload ajax table
            tblType.ajax.reload();
            $("#modal_form_delete").modal("hide");
            toastr.success("Equipment Type was successfully deleted.", "Equipment Type Deleted.", 10000);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            toastr.error("A problem occurred.", "Error", 10000);
        }
    });


}

function save_equipment_type() {
    var url;

    let msg = null;
    if (save_method == 'add') {
        url = baseUrl("ams/maintenance/add_equipment_type/");
        msg = {
            title: "New Equipment Type",
            message: "New Equipment Type was successfully saved."
        };
    } else {
        url = baseUrl("ams/maintenance/update_equipment_type/");
        msg = {
            title: "Equipment Type Updated",
            message: "Equipment Type was updated."
        };
    }

    $.validate({
        form: '#form_equipment_type',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form_equipment_type').serialize(),
                dataType: "JSON",

                success: function (data) {
                    if (data.status) {
                        tblType.ajax.reload();
                        $("#modal_form_equipment_type").modal("hide");
                        toastr.success(msg.message, msg.title, 10000);
                    } else {
                        toastr.error("A problem occurred.", "Error", 10000);
                    }

                }
            });
            return false;
        },
    });
}
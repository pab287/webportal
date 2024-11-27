$("#select2_category").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
        url: baseUrl("ams/maintenance/get_category"),
        processResults: function (data) {
            return data;
        }

    }
});
var search_val = "";
var tblSubCategory = $("#table-sub_category").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("ams/maintenance/get_sub_category_collection"),
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

        {data: "description", width: "40%"},
        {data: "sub_cat_desc", width: "40%"},
        {data: "sub_cat_code", width: "10%"},
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
            "   onclick='edit_sub_category(" + $id + ")'" +
            "   data-toggle='m-tooltip'" +
            "   data-original-title='Edit'" +
            "   data-skin='dark'" +
            "   data-delay='{\"show\": 300}'" +
            "   data-placement='bottom'>" +
            "   <i class='la la-pencil-square'></i>" +
            "</button>";
        _actionButton += " <button type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' " +
            "   onclick='open_delete(" + $id + ")'" +
            "   data-toggle='m-tooltip'" +
            "   data-original-title='Delete'" +
            "   data-skin='dark'" +
            "   data-delay='{\"show\": 300}'" +
            "   data-placement='bottom'>" +
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
    tblSubCategory.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblSubCategory.ajax.reload();
});

function open_sub_category() {
    save_method = 'add';
    $('#form_sub_category')[0].reset();
    $('#modal_form_sub_category').modal('show'); // show bootstrap modal
    $('.modal-title').text('New Sub Category'); // Set Title to Bootstrap modal title
}

function open_delete($id) {
    $('[name="delete_id"]').val($id);
    $('#modal_form_delete').modal('show'); // show bootstrap modal
    $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}

function edit_sub_category(id) {
    save_method = 'update';
    $('#form_sub_category')[0].reset();
    $.ajax({
        url: baseUrl("ams/maintenance/edit_sub_category/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            var newOption = new Option(data.description, data.cat_id, true, true);
            $('#select2_category').append(newOption).trigger('change');
            $('[name="code"]').val(data.sub_cat_code);
            $('[name="description"]').val(data.sub_cat_desc);
            $('#modal_form_sub_category').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit Sub Category'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function delete_sub_category() {

    $temp = $('[name="delete_id"]').val();
    // ajax delete data to database
    $.ajax({
        url: baseUrl("ams/maintenance/delete_sub_category/") + $temp,
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash},
        success: function (data) {
            //if success reload ajax table
            tblSubCategory.ajax.reload();
            toastr.success("Sub Category was successfully deleted.", "Sub Category Deleted.", 10000);
            $("#modal_form_delete").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            toastr.error("A problem occurred.", "Error", 10000);
        }
    });


}

function save_sub_category() {
    var url;

    let msg = null;
    if (save_method == 'add') {
        url = baseUrl("ams/maintenance/add_sub_category/");
        msg = {
            title: "New Sub Category",
            message: "New Sub Category was successfully saved."
        };
    } else {
        url = baseUrl("ams/maintenance/update_sub_category/");
        msg = {
            title: "Sub Category Updated",
            message: "Sub Category was successfully update."
        };
    }


    $.validate({
        form: '#form_sub_category',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form_sub_category').serialize(),
                dataType: "JSON",

                success: function (data) {
                    if (data.status) {
                        tblSubCategory.ajax.reload();
                        toastr.success(msg.message, msg.title, 10000);
                        $("#modal_form_sub_category").modal("hide");
                    } else {
                        toastr.error("A problem occurred.", "Error", 10000);
                    }

                }
            });
            return false;
        },
    });
}
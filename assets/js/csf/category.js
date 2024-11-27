var search_val = "";
var tblCategory = $("#table-category").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("csf/get_category_collection/"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.search['value'] = search_val
        }
    },
    searching: true,
    columns: [

        {data: "name", width: "90%"},
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
            "   onclick='edit_category(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom' " +
            "   data-original-title='Edit Category' data-skin='dark'" +
            "   data-delay='{\"show\": 300}'>" +
            "   <i class='la la-pencil-square'></i>" +
            "</button>";
        _actionButton += " <button type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' " +
            "   onclick='open_category(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom' " +
            "   data-original-title='Delete Category' data-skin='dark'" +
            "   data-delay='{\"show\": 300}'>" +
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
    tblCategory.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblCategory.ajax.reload();
});

function open_category() {
    save_method = 'add';
    $('#form_category')[0].reset();
    $('#modal_form_category').modal('show'); // show bootstrap modal
    $('.modal-title').text('New Category'); // Set Title to Bootstrap modal title
}

function open_delete($id) {
    $('[name="delete_id"]').val($id);
    $('#modal_form_delete').modal('show'); // show bootstrap modal
    $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}

function edit_category(id) {

    save_method = 'update';
    $('#form_category')[0].reset();
    $.ajax({
        url: baseUrl("csf/edit_category/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            $('[name="name"]').val(data.name);
            $('#modal_form_category').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit Category'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function delete_category() {
    $temp = $('[name="delete_id"]').val();
    // ajax delete data to database
    $.ajax({
        url: baseUrl("csf/delete_category/") + $temp,
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash},
        success: function (data) {
            //if success reload ajax table
            tblCategory.ajax.reload();
            $("#modal_form_delete").modal("hide");
            toastr.success("Category was successfully deleted.", "Category was deleted.", 10000);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            toastr.error("An error occurred.", "Error", 10000);
        }
    });


}

function save_category() {
    var url;
    let toast;

    if (save_method == 'add') {
        url = baseUrl("csf/add_category/");
        toast = {title: "Category was saved.", message: "New Category was successfully saved."};
    } else {
        url = baseUrl("csf/update_category/");
        toast = {title: "Category was updated.", message: "Category was successfully updated."};
    }


    $.validate({
        form: '#form_category',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form_category').serialize(),
                dataType: "JSON",

                success: function (data) {
                    if (data.status) {
                        tblCategory.ajax.reload();
                        $("#modal_form_category").modal("hide");
                        toastr.success(toast.message, toast.title, 10000);
                    } else {
                        toastr.error("An error occurred.", "Error", 10000);
                    }

                }
            });
            return false;
        },
    });
}
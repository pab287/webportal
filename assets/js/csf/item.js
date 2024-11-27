$("#select2_category").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
      url: baseUrl("csf/get_category"),
      processResults: function (data) {
        return data;
      }
      
    }
  });
var search_val = "";
var tblItem = $("#table-item").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("csf/get_item_collection/"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.search['value'] = search_val
        }
    },
    searching: true,
    columns: [

        {data: "name", width: "50%"},
        {data: "category", width: "40%"},
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
            "   onclick='edit_item(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom' " +
            "   data-original-title='Edit Item' data-skin='dark'" +
            "   data-delay='{\"show\": 300}'>" +
            "   <i class='la la-pencil-square'></i>" +
            "</button>";
        _actionButton += " <button type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' " +
            "   onclick='open_item(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom' " +
            "   data-original-title='Delete Item' data-skin='dark'" +
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
    tblItem.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblItem.ajax.reload();
});

function open_item() {
    save_method = 'add';
    $('#form_item')[0].reset();
    $('#modal_form_item').modal('show'); // show bootstrap modal
    $('.modal-title').text('New Item'); // Set Title to Bootstrap modal title
}

function open_delete($id) {
    $('[name="delete_id"]').val($id);
    $('#modal_form_delete').modal('show'); // show bootstrap modal
    $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}

function edit_item(id) {

    save_method = 'update';
    $('#form_item')[0].reset();
    $.ajax({
        url: baseUrl("csf/edit_item/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            $('[name="name"]').val(data.name);
            newOption = new Option(data.category, data.category_id, true, true);
          $('#select2_category').append(newOption).trigger('change');
            $('#modal_form_item').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit Item'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function delete_item() {
    $temp = $('[name="delete_id"]').val();
    // ajax delete data to database
    $.ajax({
        url: baseUrl("csf/delete_item/") + $temp,
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash},
        success: function (data) {
            //if success reload ajax table
            tblItem.ajax.reload();
            $("#modal_form_delete").modal("hide");
            toastr.success("Item was successfully deleted.", "Item was deleted.", 10000);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            toastr.error("An error occurred.", "Error", 10000);
        }
    });


}

function save_item() {
    var url;
    let toast;

    if (save_method == 'add') {
        url = baseUrl("csf/add_item/");
        toast = {title: "Item was saved.", message: "New Item was successfully saved."};
    } else {
        url = baseUrl("csf/update_item/");
        toast = {title: "Item was updated.", message: "Item was successfully updated."};
    }


    $.validate({
        form: '#form_item',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form_item').serialize(),
                dataType: "JSON",

                success: function (data) {
                    if (data.status) {
                        tblItem.ajax.reload();
                        $("#modal_form_item").modal("hide");
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
var search_val = "";
var tblTag = $("#table-tag").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("archiving/get_tag_collection/"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.search['value'] = search_val
        }
    },
    searching: true,
    columns: [

        {data: "tag", width: "90%"},
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
            "   onclick='edit_tag(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom' " +
            "   data-original-title='Edit Tag' data-skin='dark'" +
            "   data-delay='{\"show\": 300}'>" +
            "   <i class='la la-pencil-square'></i>" +
            "</button>";
        _actionButton += " <button type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' " +
            "   onclick='open_delete(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom' " +
            "   data-original-title='Delete Tag' data-skin='dark'" +
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
    tblTag.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblTag.ajax.reload();
});

function open_tag() {
    save_method = 'add';
    $('#form_tag')[0].reset();
    $('#modal_form_tag').modal('show'); // show bootstrap modal
    $('.modal-title').text('New Tag'); // Set Title to Bootstrap modal title
}

function open_delete($id) {
    $('[name="delete_id"]').val($id);
    $('#modal_form_delete').modal('show'); // show bootstrap modal
    $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}

function edit_tag(id) {

    save_method = 'update';
    $('#form_tag')[0].reset();
    $.ajax({
        url: baseUrl("archiving/edit_tag/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            $('[name="tag"]').val(data.tag);
            $('#modal_form_tag').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit Tag'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function delete_tag() {
    $temp = $('[name="delete_id"]').val();
    // ajax delete data to database
    $.ajax({
        url: baseUrl("archiving/delete_tag/") + $temp,
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash},
        success: function (data) {
            //if success reload ajax table
            tblTag.ajax.reload();
            $("#modal_form_delete").modal("hide");
            toastr.success("Tag was successfully deleted.", "Tag was deleted.", 10000);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            toastr.error("An error occurred.", "Error", 10000);
        }
    });


}

function save_tag() {
    var url;
    let toast;

    if (save_method == 'add') {
        url = baseUrl("archiving/add_tag/");
        toast = {title: "Tag was saved.", message: "New tag was successfully saved."};
    } else {
        url = baseUrl("archiving/update_tag/");
        toast = {title: "Tag was updated.", message: "Tag was successfully updated."};
    }


    $.validate({
        form: '#form_tag',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form_tag').serialize(),
                dataType: "JSON",

                success: function (data) {
                    if (data.status) {
                        tblTag.ajax.reload();
                        $("#modal_form_tag").modal("hide");
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
var search_val = "";
var tblClassification = $("#table-classification").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("archiving/get_classification_collection/"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.search['value'] = search_val
        }
    },
    searching: true,
    columns: [

        {data: "description", width: "50%"},
        {data: "type", width: "40%"},
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
            "   onclick='edit_classification(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom'" +
            "   data-skin='dark' data-original-title='Edit Classification'" +
            "   data-delay='{\"show\": 300}'>" +
            "   <i class='la la-pencil-square'></i>" +
            "</button>";
        _actionButton += " <button type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' " +
            "   onclick='open_delete(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom'" +
            "   data-skin='dark' data-original-title='Delete Classification'" +
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
    tblClassification.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblClassification.ajax.reload();
});

function open_classification() {
    save_method = 'add';
    $('#form_classification')[0].reset();
    $('#modal_form_classification').modal('show'); // show bootstrap modal
    $('.modal-title').text('New Classification'); // Set Title to Bootstrap modal title
}

function open_delete($id) {
    $('[name="delete_id"]').val($id);
    $('#modal_form_delete').modal('show'); // show bootstrap modal
    $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}

function edit_classification(id) {

    save_method = 'update';
    $('#form_classification')[0].reset();
    $.ajax({
        url: baseUrl("archiving/edit_classification/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            $('[name="description"]').val(data.description);
            $('[name="type"]').val(data.type).trigger("change");
            $('#modal_form_classification').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit Classification'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function delete_classification() {
    $temp = $('[name="delete_id"]').val();
    // ajax delete data to database
    $.ajax({
        url: baseUrl("archiving/delete_classification/") + $temp,
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash},
        success: function (data) {
            //if success reload ajax table
            tblClassification.ajax.reload();
            $("#modal_form_delete").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error adding / update data');
        }
    });


}

function save_classification() {
    var url;

    if (save_method == 'add') {
        url = baseUrl("archiving/add_classification/");
    } else {
        url = baseUrl("archiving/update_classification/");
    }


    $.validate({
        form: '#form_classification',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form_classification').serialize(),
                dataType: "JSON",

                success: function (data) {
                    if (data.status) {

                        tblClassification.ajax.reload();
                        $("#modal_form_classification").modal("hide");
                    } else {
                        alert('Error get data from ajax');
                    }

                }
            });
            return false;
        },
    });
}
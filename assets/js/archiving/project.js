var search_val = "";
var tblProject = $("#table-project").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("archiving/get_project_collection/"),
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
            "   onclick='edit_project(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-original-title='Edit Project'" +
            "   data-placement='bottom' data-skin='dark'" +
            "   data-delay='{\"show\": 300}'>" +
            "   <i class='la la-pencil-square'></i>" +
            "</button>";
        _actionButton += " <button type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' " +
            "   onclick='open_delete(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-original-title='Delete Project'" +
            "   data-placement='bottom' data-skin='dark'" +
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
    tblProject.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblProject.ajax.reload();
});

function open_project() {
    save_method = 'add';
    $('#form_project')[0].reset();
    $('#modal_form_project').modal('show'); // show bootstrap modal
    $('.modal-title').text('New Project'); // Set Title to Bootstrap modal title
}

function open_delete($id) {
    $('[name="delete_id"]').val($id);
    $('#modal_form_delete').modal('show'); // show bootstrap modal
    $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}

function edit_project(id) {

    save_method = 'update';
    $('#form_project')[0].reset();
    $.ajax({
        url: baseUrl("archiving/edit_project/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            $('[name="name"]').val(data.name);
            $('#modal_form_project').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit Project'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function delete_project() {
    $temp = $('[name="delete_id"]').val();
    // ajax delete data to database
    $.ajax({
        url: baseUrl("archiving/delete_project/") + $temp,
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash},
        success: function (data) {
            //if success reload ajax table
            tblProject.ajax.reload();
            $("#modal_form_delete").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error adding / update data');
        }
    });


}

function save_project() {
    var url;

    if (save_method == 'add') {
        url = baseUrl("archiving/add_project/");
    } else {
        url = baseUrl("archiving/update_project/");
    }


    $.validate({
        form: '#form_project',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form_project').serialize(),
                dataType: "JSON",

                success: function (data) {
                    if (data.status) {

                        tblProject.ajax.reload();
                        $("#modal_form_project").modal("hide");
                    } else {
                        alert('Error get data from ajax');
                    }

                }
            });
            return false;
        },
    });
}
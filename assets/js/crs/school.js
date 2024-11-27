var search_val = "";
var tblSchool = $("#table-school").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("crs/get_school_collection/"),
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

        {data: "school", width: "90%"},
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
            "   type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
            "   onclick='edit_school(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom' " +
            "   data-original-title='Edit School' data-skin='dark'" +
            "   data-delay='{\"show\": 300}'>" +
            "   <i class='la la-pencil-square'></i>" +
            "</button>";
        _actionButton += " <button " +
            "   type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' " +
            "   onclick='open_delete(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom' " +
            "   data-original-title='Delete School' data-skin='dark'" +
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
    tblSchool.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblSchool.ajax.reload();
});

function open_school() {
    save_method = 'add';
    $('#form_school')[0].reset();
    $('#modal_form_school').modal('show'); // show bootstrap modal
    $('.modal-title').text('New School'); // Set Title to Bootstrap modal title
}

function open_delete($id) {
    $('[name="delete_id"]').val($id);
    $('#modal_form_delete').modal('show'); // show bootstrap modal
    $('.modal-title').text('Delete Confirmation'); // Set Title to Bootstrap modal title
}

function edit_school(id) {

    save_method = 'update';
    $('#form_school')[0].reset();
    $.ajax({
        url: baseUrl("crs/edit_school/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            $('[name="school"]').val(data.school);
            $('#modal_form_school').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit School'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function delete_school() {
    $temp = $('[name="delete_id"]').val();
    $.ajax({
        url: baseUrl("crs/delete_school"),
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash,id:$temp},
        success: function (data) {
            if(data.success){            
            $("#generalSearch").val("");
            search_val = $("#generalSearch").val();
            tblSchool.ajax.reload();
            $("#modal_form_delete").modal("hide");
            toastr.success(data.msg, "School Deleted.", 10000);
        }

        },
        error: function (jqXHR, textStatus, errorThrown) {
            toastr.error("An error occurred.", "Error", 10000);
        }
    });


}

function save_school() {
    var url;
    let toast = null;

    if (save_method == 'add') {
        url = baseUrl("crs/add_school/");
        toast = {title: "New School Saved.", message: "New school was successfully saved."};
    } else {
        url = baseUrl("crs/update_school/");
        toast = {title: "School Updated.", message: "School was successfully updated."};
    }

    const formData = serializeArrayToObject($('#form_school'));

    $.validate({
        form: '#form_school',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                dataType: "JSON",
                success: function (data) {
                    if (data.success) {
                        search_val = formData.school;
                        $("#generalSearch").val(search_val);
                        tblSchool.ajax.reload();
                        $("#modal_form_school").modal("hide");
                        toastr.success(data.msg, "Success", 10000);
                    } else {
                        toastr.error("An error occurred.", "Error", 10000);
                    }

                }
            });
            return false;
        },
    });
}
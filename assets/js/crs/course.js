var search_val = "";
var tblCourse = $("#table-course").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("crs/get_course_collection/"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
        }
    },
    searching: true,
    columns: [
        {data: "course", width: "90%"},
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
            "   onclick='edit_course(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-original-title='Edit Course' data-skin='dark'" +
            "   data-placement='bottom'" +
            "   data-delay='{\"show\": 300}'" +
            "   ><i class='la la-pencil-square'></i></button>";
        _actionButton += " <button type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' " +
            "   onclick='open_delete(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-original-title='Delete Course' data-skin='dark'" +
            "   data-placement='bottom'" +
            "   data-delay='{\"show\": 300}'" +
            "><i class='la la-trash'></i></button>";
        return _actionButton;
    } else {
        return false;
    }
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblCourse.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblCourse.ajax.reload();
});

function open_course() {
    save_method = 'add';
    $('#form_course')[0].reset();
    $('#modal_form_course').modal('show'); // show bootstrap modal
    $('.modal-title').text('New Course'); // Set Title to Bootstrap modal title
}

function open_delete($id) {
    $('[name="delete_id"]').val($id);
    $('#modal_form_delete').modal('show'); // show bootstrap modal
    $('.modal-title').text('Delete Confirmation'); // Set Title to Bootstrap modal title
}

function edit_course(id) {

    save_method = 'update';
    $('#form_course')[0].reset();
    $.ajax({
        url: baseUrl("crs/edit_course/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            $('[name="course"]').val(data.course);
            $('#modal_form_course').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit Course'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function delete_course() {
    $temp = $('[name="delete_id"]').val();
    $.ajax({
        url: baseUrl("crs/delete_course"),
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash,id:$temp},
        success: function (data) {
            if (data.success){
                $("#generalSearch").val("");
                search_val = $("#generalSearch").val();
                tblCourse.ajax.reload();
                $("#modal_form_delete").modal("hide");
                toastr.success(data.msg, "Course Deleted.", 10000);
            }
            else{
                toastr.success(data.msg, "Failed.", 10000);
            }

        },
        error: function (jqXHR, textStatus, errorThrown) {
            toastr.error("An error occurred while saving.", "Error.", 10000);
        }
    });


}

function save_course() {
    var url;
    let toast = null;

    if (save_method == 'add') {
        url = baseUrl("crs/add_course/");
    } else {
        url = baseUrl("crs/update_course/");
    }

    const form = $('#form_course');
    const formData = serializeArrayToObject(form);
    if (form.isValid()) {
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                if (data.success) {
                    search_val = formData.course;
                    $("#generalSearch").val(search_val);
                    tblCourse.ajax.reload();
                    $("#modal_form_course").modal("hide");
                    toastr.success(data.msg, "Success", 10000);
                } else {
                    toastr.error("An error occurred while saving.", "Error.", 10000);
                }
            }
        });
    }
}
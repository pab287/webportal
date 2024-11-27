var search_val = "";
var tblMemo = $("#table-memo").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("archiving/get_memo_collection/"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
        }
    },
    searching: true,
    columns: [

        {data: "subject", width: "30%"},
        {data: "number", width: "20%"},
        {data: "year", width: "10%"},
        {
            data: "tag1", width: "35%", render: function (data) {
                return formatTag(data)
            }
        },
        {data: null, width: "5%", className: "text-center"},
    ],
    columnDefs: [

        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,

            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id, row.is_fav);
            },
        }


    ]
});
$("#tag_id").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
        url: baseUrl("archiving/get_tag"),
        delay: 500,
        processResults: function (data) {
            return data;
        }

    }
});

function formatTag(data) {
    var s1 = data;
    var s2 = s1.substr(1);
    return s2;


}


function itemDatatableActions($id, $fav) {
    if ($id) {


        var _actionButton = "";
        _actionButton += "<span style='overflow: visible; width: 110px;'>";
        _actionButton += "<div class='dropdown'>";
        _actionButton += "<a href='#' class='btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' data-toggle='dropdown'>";
        _actionButton += "<i class='la la-ellipsis-h'></i>";
        _actionButton += "</a>";
        _actionButton += "<div class='dropdown-menu dropdown-menu-right'>";
        _actionButton += "<a class='dropdown-item' onclick='view_memo(" + $id + ")'><i class='la la-download'></i>Download</a>";
        if ($fav == '0') {
            _actionButton += "<a class='dropdown-item' onclick='fav_memo(" + $id + ")'><i class='la la-star-o'></i>Favorite</a>";
        }
        if ($fav == '1') {
            _actionButton += "<a class='dropdown-item' onclick='unfav_memo(" + $id + ")'><i class='la la-ban'></i>Unfavorite</a>";
        }
        _actionButton += "<a class='dropdown-item' onclick='edit_memo(" + $id + ")'><i class='la la-pencil-square'></i>Edit</a>";
        _actionButton += "<a class='dropdown-item' onclick='supersede(" + $id + ")'><i class='la la-file-archive-o'></i>Supersede</a>";
        _actionButton += "<a class='dropdown-item' onclick='open_delete(" + $id + ")'><i class='la la-trash'></i>Delete</a>";
        _actionButton += " </div>";
        _actionButton += "</div>";
        _actionButton += "</span>";

        return _actionButton;
    } else {
        return false;
    }
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblMemo.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblMemo.ajax.reload();
});

function fav_memo($id) {

    $.ajax({
        url: baseUrl("archiving/favorite_memo/") + $id,
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash},
        success: function (data) {
            tblMemo.ajax.reload();

        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error adding / update data');
        }
    });


}

function unfav_memo($id) {

    $.ajax({
        url: baseUrl("archiving/unfavorite_memo/") + $id,
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash},
        success: function (data) {
            tblMemo.ajax.reload();

        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error adding / update data');
        }
    });


}

function supersede($id) {

    $.ajax({
        url: baseUrl("archiving/supersede_memo/") + $id,
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash},
        success: function (data) {
            tblMemo.ajax.reload();

        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error adding / update data');
        }
    });


}

function open_delete($id) {
    $('[name="delete_id"]').val($id);
    $('#modal_form_delete').modal('show'); // show bootstrap modal
    $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}

function open_memo() {
    save_method = 'add';
    $("#tag_id").empty();
    $('#form_document')[0].reset(); // reset form on modals
    $('[name="filename"]').val("");
    $('[name="doc_filename"]').val("");

    $('#modal_form_document').modal('show'); // show bootstrap modal
    $('.modal-title').text('Add New Memo'); // Set Title to Bootstrap modal title
}

function edit_memo(id) {
    save_method = 'update';
    $('#form_document')[0].reset();
    $.ajax({
        url: baseUrl("archiving/edit_memo/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            $('[name="number"]').val(data.number);
            $('[name="year"]').val(data.year);
            $('[name="subject"]').val(data.subject);
            $('[name="tag_temp"]').val(data.tag1);
            $('[name="filename"]').val(data.filename);
            $('[name="doc_filename"]').val("*" + data.filename);
            var x = document.getElementById("tag_id");
            x.remove(x.selectedIndex);

            document.getElementById('tag_text').style.removeProperty('display')

            $('#modal_form_document').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit Memo'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function delete_memo() {
    $temp = $('[name="delete_id"]').val();
    // ajax delete data to database
    $.ajax({
        url: baseUrl("archiving/delete_memo/") + $temp,
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash},
        success: function (data) {
            tblMemo.ajax.reload();
            $('#modal_form_delete').modal('hide');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error adding / update data');
        }
    });
}

function view_memo($id) {

    //Ajax Load data from ajax
    $.ajax({
        url: baseUrl("archiving/edit_memo/") + $id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {

            window.location.replace(baseUrl("uploads/module/archiving/memo/files/") + data.filename);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });

}


function save_memo(formEl) {
    var url;
    if (save_method == "add") {
        url = baseUrl("archiving/add_memo/");
    } else {
        url = baseUrl("archiving/update_memo/" + $('[name="id"]').val());
    }

    const form = $(formEl);
    const formData = new FormData(formEl);

    if (form.isValid()) {
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                $('#modal_form_document').modal('hide');
                tblMemo.ajax.reload();
            }
        });
    }
}

function getFilename(fileElement) {
    const fileEl = $(fileElement)[0];
    if (fileEl && fileEl.files.length) {
        const file = fileEl.files[0];
        $("#filename").val(file.name);
    } else {
        $("#filename").val("");
    }
}
            
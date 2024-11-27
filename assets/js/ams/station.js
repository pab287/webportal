var search_val = "";
var tblStation = $("#table-station").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("ams/maintenance/get_station_collection"),
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


        {data: "station", width: "90%"},
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
            "   onclick='edit_station(" + $id + ")'" +
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
    tblStation.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblStation.ajax.reload();
});

function open_station() {
    save_method = 'add';
    $('#form_station')[0].reset();
    $('#modal_form_station').modal('show'); // show bootstrap modal
    $('.modal-title').text('New Station'); // Set Title to Bootstrap modal title
}

function open_delete($id) {
    $('[name="delete_id"]').val($id);
    $('#modal_form_delete').modal('show'); // show bootstrap modal
    $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}

function edit_station(id) {

    save_method = 'update';
    $('#form_station')[0].reset();
    $.ajax({
        url: baseUrl("ams/maintenance/edit_station/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            $('[name="station"]').val(data.station);
            $('#modal_form_station').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit Station'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function delete_station() {
    $temp = $('[name="delete_id"]').val();
    // ajax delete data to database
    $.ajax({
        url: baseUrl("ams/maintenance/delete_station/") + $temp,
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash},
        success: function (data) {
            //if success reload ajax table
            tblStation.ajax.reload();
            $("#modal_form_delete").modal("hide");
            toastr.success("Station was successfully deleted.", "Station Deleted.", 10000);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            toastr.error("A problem occurred.", "Error", 10000);
        }
    });


}

function save_station() {
    var url;

    let msg = null;
    if (save_method == 'add') {
        url = baseUrl("ams/maintenance/add_station/");
        msg = {
            title: "New Station Saved",
            message: "New Station was successfully saved."
        };
    } else {
        url = baseUrl("ams/maintenance/update_station/");
        msg = {
            title: "Station Updated",
            message: "Station was successfully updated."
        };
    }


    $.validate({
        form: '#form_station',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form_station').serialize(),
                dataType: "JSON",

                success: function (data) {
                    if (data.status) {
                        tblStation.ajax.reload();
                        $("#modal_form_station").modal("hide");
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
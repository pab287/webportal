let eventVue = new Vue({
    el: "#m_portlet",
    data: {
        optionSelected: {},
    },
});
let archive = 0;


let tblEventsSettings = $('#table-events_settings').DataTable({
    dom: 'rtlip',
    rowId: 'id',
    search: false,
    serverSide: true,
    processing: true,
    global: false,   
    ajax: {
        url: baseUrl('events/get_events_settings'),
        type: 'post',
        dataType: 'json',
        global: false,
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.is_archived = archive;
        },
    },
    columns: [
        { data: 'id', visible: false },
        { data: 'name' },
        { data: 'type' },
        { data: 'fullname' },
        { data: 'created_at' },
        { data: null, title: 'Actions', className: "text-left", orderable: false, defaultContent: '',
            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id);
            }
        }
    ],
    
});

$.validate({
    form: "#new_option_form",
    lang: "en",
    onSuccess: function (form) {
        var currentForm = form;
        var formData = $(currentForm).serializeArray();
        formData.push({ name: "csrf_token", value: _csrf_hash });
        $.ajax({
            url: baseUrl('events/new_events_settings'),
            type: "post",
            dataType: "json",
            global: false,
            data: formData,
            beforeSend: function () {
                $(currentForm).find(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                if(json.success){
                    $('#table-events_settings').DataTable().ajax.reload(null, false);
                    $('#new_option_form')[0].reset();
                    $('#new_option').modal('hide');
                    toastr.success(json.message);
                }else{
                    toastr.error(json.message);
                }
                $(currentForm).find(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false; 
    }
});

function itemDatatableActions(id) {
    let _actionButton = "";

    if (archive) {

        _actionButton += `
            <a href="javascript:void(0)" 
                class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnRestore" 
                onclick="restoreOption(${id})" 
                title="Restore Event">
                <i class="la la-undo"></i>
            </a>`;

    } else {

        _actionButton += `
            <a href="javascript:void(0)" 
                class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnEdit" 
                onclick="onEditEvent(${id})" 
                title="Edit Settings">
                <i class="la la-edit"></i>
            </a>`;

        _actionButton += `
            <a href="javascript:void(0)" 
                class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnArchive" 
                onclick="archiveOption(${id})" 
                title="Archive Settings">
                <i class="la la-file-archive-o"></i>
            </a>`;
    }

    return _actionButton;
}


function onEditEvent(id) {
    let rowData = tblEventsSettings.row('#'+id).data();
    selectedData = rowData; 
    eventVue.optionSelected = JSON.parse(JSON.stringify(rowData));
    $("#edit_options").modal("show");
}



$.validate({
    form: "#update_option_form",
    lang: "en",
    onSuccess: function (form) {
        var currentForm = form;
        var formData = $(currentForm).serializeArray();
        formData.push({ name: "csrf_token", value: _csrf_hash });
        $.ajax({
            url: baseUrl('events/update_events_settings'),
            type: "post",
            dataType: "json",
            global: false,
            data: formData,
            beforeSend: function () {
                $(currentForm).find(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                if(json.success){
                    $('#table-events_settings').DataTable().ajax.reload(null, false);
                    $('#update_option_form')[0].reset();
                    $('#edit_options').modal('hide');
                    toastr.success(json.message);
                }else{
                    toastr.error(json.message);
                }
                $(currentForm).find(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false; 
    }
});


function archiveOption(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "This event will be archived. You can restore it later if needed.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, archive it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl("events/archive_event_settings"),
                type: "POST",
                dataType: "json",
                global: false,
                data: {
                    csrf_token: _csrf_hash,
                    id: id,
                    action: 1
                },
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (res) {    
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    if (res.success) {
                        tblEventsSettings.ajax.reload(null, false);
                        toastr.success(res.message);
                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function () {
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    toastr.error("Something went wrong while archiving the event.");
                }
            });
        }
    });
}

function restoreOption(id) {
    Swal.fire({
        title: "Restore this event?",
        text: "This event will be restored and will appear again in the active list.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, restore it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl("events/archive_event_settings"),
                type: "POST",
                dataType: "json",
                global: false,
                data: {
                    csrf_token: _csrf_hash,
                    id: id,
                    action: 0
                },
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (res) {    
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    if (res.success) {
                        tblEventsSettings.ajax.reload(null, false);
                        toastr.success(res.message);
                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function () {
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    toastr.error("Something went wrong while restoring the event.");
                }
            });
        }
    });
}


function openArchive() {
    archive = archive === 0 ? 1 : 0;
    if (archive === 1) {
        $('#page_title').text('ARCHIVED EVENTS');
        $('#archive_text').text('Back to Active');
    } else {
        $('#page_title').text('EVENTS PAGE SETTINGS');
        $('#archive_text').text('Archive');
    }
    tblEventsSettings.ajax.reload();
}
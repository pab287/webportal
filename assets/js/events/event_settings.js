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
        { data: 'hex_code',
            render: function (data, type, row) {
                if (!data) return 'NOT SET';
                return `
                    <div style="display:flex; flex-direction:column; align-items:center; gap:4px;">
                        <div style="font-weight:600;">${data}</div>
                        <div style="width: 32px; height: 16px; border: 1px solid #ccc; background-color: ${data}; border-radius: 3px;"></div>
                    </div>
                `;
            }
        },
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
        $('#newOption').hide();
    } else {
        $('#page_title').text('EVENTS PAGE SETTINGS');
        $('#archive_text').text('Archive');
        $('#newOption').show();
    }
    tblEventsSettings.ajax.reload();
}

const $colorPicker = $("#option_color");
const $colorText   = $("#option_color_text");

if (!$colorText.val() || $colorText.val().charAt(0) !== '#') {
    $colorText.val("#000000");
}
$colorPicker.val($colorText.val());

$colorPicker.on("input change", function () {
    $colorText.val($(this).val().toUpperCase());
});

$colorText.on("input", function () {
    let val = $(this).val().toUpperCase();
    val = val.replace(/[^0-9A-F#]/g, "");
    val = "#" + val.replace(/#/g, "");
    val = val.substring(0, 7);

    $(this).val(val);
    if (/^#[0-9A-F]{6}$/.test(val)) {
        $colorPicker.val(val);
    }
});
$colorText.on("keydown", function (e) {
    const pos = this.selectionStart;
    if ((pos === 0 || pos === 1) && e.key === "Backspace") {
        e.preventDefault();
    }
});
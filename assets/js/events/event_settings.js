let eventVue = new Vue({
    el: "#m_portlet",
    data: {

    },
    mounted() {
        
    },
    methods: {
        
    }
});



let tblEventsSettings = $('#table-events_settings').DataTable({
    dom: 'rtlip',
    rowId: 'id',
    search: false,
    serverSide: true,
    processing: false,
    global: false,   
    ajax: {
        url: baseUrl('events/get_events_settings'),
        type: 'post',
        dataType: 'json',
        data: function (d) {
            d.csrf_token = _csrf_hash;
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
            data: formData,
            beforeSend: function () {
                $(currentForm).find(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                if(json.success){
                    $('#table-events_settings').DataTable().ajax.reload(null, false);
                    $('#new_option_form')[0].reset();
                    $('#addNewOptionModal').modal('hide');
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

        _actionButton += `
            <a href="javascript:void(0)" 
                class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnEdit" 
                onclick="onEditEvent(${id})" 
                title="Edit Participant">
                <i class="la la-eye"></i>
            </a>`;

        _actionButton += `
            <a href="javascript:void(0)" 
                class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnArchive" 
                onclick="archiveParticipant(${id})" 
                title="Archive Participant">
                <i class="la la-file-archive-o"></i>
            </a>`;

    return _actionButton;
}

function onEditEvent(id) {
    let rowData = tblEventsSettings.row('#'+id).data();
    selectedData = rowData; 
    eventVue.participantDataSelected = JSON.parse(JSON.stringify(rowData));
    $("#edit_options").modal("show");
}


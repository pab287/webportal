let holidayCalendar;
let speakerIndex = 1;
let tblCalendarOfHolidays = $("#table-calendar-of-holidays")
    .DataTable({
        dom: 'frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: true,
        // order: [[0, 'asc']],
        ajax: {
            url: baseUrl('hris/calendar/get_events_tabular'),
            type: 'post',
            dataType: 'json',
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = $("#search-holidays").val();
                d.search['filter_year'] = $('#filter-year').val();
            },
            // success: function(res) {
            //     console.log('DataTables AJAX response:', res);
            // }
        },
        columns: [
            { data: 'id', name: 'id', visible: false },
            { data: 'event_title' },
            { data: 'description' },
            { data: 'event_venue', name: 'event_venue',},
            { 
                data: null, 
                render: function(data, type, row) {
                    if (!row.speakers || row.speakers.length === 0) {
                        return '<span class="text-muted">No speakers</span>';
                    }
        
                    return row.speakers.map(function(s) {
                        return `<div>
                            <strong>${s.speaker_name}</strong> - ${s.position}<br>
                            <small>${s.company}</small>
                        </div>`;
                    }).join(""); // separator between speakers

                }
            }, 
            {
                data: null,
                orderable: false,
                render: function (data, type, row, meta) {
                    return itemDatatableActions(row.id, row.status);
                }
            },            
        ]
    });

$('#addNewEvent').on('shown.bs.modal', function () {
    console.log('modal opened');
    if ($('#event_date').data('daterangepicker')) {
        $('#event_date').data('daterangepicker').remove();
    }
    $('#event_date').daterangepicker({
        showDropdowns: true,
        autoUpdateInput: false,
        locale: {
            format: 'MMM DD, YYYY',
        }
    });

    $('#event_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MMM DD, YYYY') + ' - ' + picker.endDate.format('MMM DD, YYYY'));
    });

    $('#event_date').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
});

let eventVue = new Vue({
    el: "#new_event_form",
    data: {speakers: [{name: '', position: '', company: ''}]},
    mounted: function () {
    },
    methods:{
        addNewSpeaker() {
            console.log('Adding new speaker');
            this.speakers.push({
                name: '',
                position: '',
                company: ''
            });
        },
        removeSpeaker(index) {
            console.log('Removing speaker at index:', index);
            if (this.speakers.length > 1) {
                this.speakers.splice(index, 1);
            }
        },
    },
});

$.validate({
    form : '#new_event_form',
    lang: 'en',
    onSuccess : function(form) {
        console.log('Validation successful');

        $.ajax({
            url: baseUrl('hris/calendar/save_event'),
            type: "POST",
            dataType: "json",
            data: $(form).serialize(),
            beforeSend: function() {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function(res) {
                if(res.success){
                    $(form).trigger("reset");
                    $("#addNewEvent").modal('hide');
                    toastr.success(res.message, 'Success', 5000);
                    // holidayCalendar.ajax.reload();
                    // $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }else{
                    toastr.error(res.message, 'Error', 5000);
                    // $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            }
        });
        return false;
    }
});

function itemDatatableActions($id, $status) {
    let _actionButton = "";

    _actionButton += " <a style='text-decoration: none;' " +
        "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit' " +
        "   data-toggle='modal' data-target='#edit-events-modal' " +   
        "   data-ticket-id='" + $id + "' " +
        "   data-skin='dark' " +
        "   title='Edit Ticket'>" +
        "   <i class='la la-pencil-square'></i>" +
        "</a>";

    _actionButton += " <a style='text-decoration: none;' " +
        "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnView' " +
        "   data-toggle='modal' data-target='#view-ticket-modal' " +
        "   data-ticket-id='" + $id + "' " +
        "   data-skin='dark' " +
        "   title='View Ticket'>" +
        "   <i class='la la-eye'></i>" +
        "</a>";

    _actionButton += " <button " +
        "   type='button' " +
        "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnArchive' " +
        "   onclick='deleteR(" + $id + ")' " +
        "   data-toggle='m-tooltip' data-placement='bottom' title='Archive Ticket' " +
        "   data-skin='dark'>" +
        "   <i class='la la-file-archive-o'></i>" +
        "</button>";

    return _actionButton;
}

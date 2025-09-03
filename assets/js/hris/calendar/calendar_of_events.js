let holidayCalendar;
let speakerIndex = 1;
let selectedEventData = null;
let tblCalendarOfHolidays = $("#table-calendar-of-holidays")
    .DataTable({
        dom: 'frtlip',
        rowId: 'id',
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
            { 
                data: null, 
                name: 'event_venue',
                render: function(data, type, row) {
                    let venue = row.event_venue ? row.event_venue : "No Venue";
                    let schedule = "";
                    if (row.event_from && row.event_to) {
                        let fromDate = moment(row.event_from).format("MMM DD, YYYY");
                        let toDate   = moment(row.event_to).format("MMM DD, YYYY");
            
                        if (fromDate === toDate) {
                            schedule = fromDate; // same day
                        } else {
                            schedule = fromDate + " - " + toDate;
                        }
                    }
            
                    return `<div>
                                <strong>${venue}</strong><br>
                                <small>${schedule}</small>
                            </div>`;
                }
            },
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

    $('#search-holidays')
    .donetyping(function () {
        tblCalendarOfHolidays.ajax.reload();
    });

    $('#filter-year')
        .on('change', function () {
            tblCalendarOfHolidays.ajax.reload();
        });

    $('#filter-year')
        .datepicker({
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            },
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });

$('#addNewEvent').on('shown.bs.modal', function () {
    if ($('#event_date').data('daterangepicker')) {
        $('#event_date').data('daterangepicker').remove();
    }
    $('#event_date').daterangepicker({
        showDropdowns: true,
        autoUpdateInput: false,
        locale: {
            format: 'MMM DD, YYYY',
            cancelLabel: 'Clear'
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
    data: {speakers: [{name: '', position: '', company: ''}], edit_speakers:[]},
    mounted: function () {
    },
    methods:{
        addNewSpeaker() {
            this.speakers.push({
                name: '',
                position: '',
                company: ''
            });
        },
        removeSpeaker(index) {
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
                    tblCalendarOfHolidays.ajax.reload();
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

function itemDatatableActions(id, status) {
    let _actionButton = "";
    _actionButton += " <a style='text-decoration: none;' " +
        "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit' " +
        "   onclick='onEditEvent(" + id + ")' " +  
        "   data-toggle='m-tooltip' data-placement='bottom' title='View Ticket' " +
        "   data-skin='dark' " +
        "   title='View Event'>" +
        "   <i class='la la-eye'></i>" +
        "</a>";

    _actionButton += " <button " +
        "   type='button' " +
        "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnArchive' " +
        "   onclick='deleteArchive(" + id + ")' " +   
        "   data-toggle='m-tooltip' data-placement='bottom' title='Archive Ticket' " +
        "   data-skin='dark'>" +
        "   <i class='la la-file-archive-o'></i>" +
        "</button>";

        _actionButton += " <a " +
        "   href='" + baseUrl('hris/calendar/add_participants/') + id + "' " +
        "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' " +
        "   data-toggle='m-tooltip' data-placement='bottom' title='Add Participants' " +
        "   data-skin='dark'>" +
        "   <i class='la la-user-plus'></i>" +
        "</a>";

    return _actionButton;
}

let editEventVue = new Vue({
    el: "#edit-events-modal",
    data: {
        eventsData:{

        },
        disabled:true,
    },
    mounted: function () {
    },
    methods:{
        addNewSpeaker() {
            this.eventsData.speakers.push({
                name: '',
                position: '',
                company: ''
            });
        },
        removeSpeaker(index) {
            if (this.eventsData.speakers.length > 1) {
                this.eventsData.speakers.splice(index, 1);
            }
        },
        formatSchedule(dateFrom, dateTo) {
            if (dateFrom && dateTo) {
                let start = moment(dateFrom).format('MMM DD, YYYY');
                let end = moment(dateTo).format('MMM DD, YYYY');
                return `${start} - ${end}`;
            }
            return '';
        }
    },
});

function onEditEvent(id) {
    let rowData = tblCalendarOfHolidays.row('#'+id).data();
    selectedEventData = JSON.parse(JSON.stringify(rowData));
    selectedEventData.date = moment(rowData.event_from).format("MMM DD, YYYY") 
    + " - " + moment(rowData.event_to).format("MMM DD, YYYY");
    editEventVue.eventsData = JSON.parse(JSON.stringify(rowData));
    $("#edit-events-modal").modal("show");
}

$.validate({
    form : '#edit_event_form',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess : function(form) {
        let formData =  $(form).serialize();
        let eventData = JSON.parse(JSON.stringify(editEventVue.eventsData));
        if(!checkChanges(eventData, selectedEventData)){
            toastr.info('No changes detected.', 'Info', 5000);
            return false;
        };
        $.ajax({
            url: baseUrl('hris/calendar/update_event'),
            type: "POST",
            dataType: "json",
            data: formData,
            beforeSend: function() {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function(res) {
                if(res.status){
                    $(form).trigger("reset");
                    $("#edit-events-modal").modal('hide');
                    toastr.success(res.message, 'Success', 5000);
                    tblCalendarOfHolidays.ajax.reload(null, false);
                    // $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }else{
                    toastr.error(res.message, 'Error', 5000);
                    // $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
                $('#edit_event_form')[0].reset();
            }
        });
        return false;
    }
});

function checkChanges(newData, oldData) {
    return JSON.stringify(newData) !== JSON.stringify(oldData);
}

$('#edit-events-modal').on('shown.bs.modal', function () {
    if ($('#edit_event_date').data('daterangepicker')) {
        $('#edit_event_date').data('daterangepicker').remove();
    }
    $('#edit_event_date').daterangepicker({
        showDropdowns: true,
        autoUpdateInput: false,
        locale: {
            format: 'MMM DD, YYYY',
            cancelLabel: 'Clear'
        }
    });

    $('#edit_event_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MMM DD, YYYY') + ' - ' + picker.endDate.format('MMM DD, YYYY'));
        editEventVue.eventsData.date = $(this).val();
    });

    $('#edit_event_date').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
});

$('#edit-events-modal').on('hidden.bs.modal', function () {
    $('#edit_event_form')[0].reset();
    editEventVue.eventsData = {};
    editEventVue.disabled = true;
});

function deleteArchive(id){
    Swal.fire({
        title: 'Are you sure?',
        text: "This event will be archived and cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, archive it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl('hris/calendar/archive_event'),
                type: "POST",
                dataType: "json",
                data: {
                    csrf_token : _csrf_hash,
                    id:id
                },
                beforeSend: function() {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(res) {
                    if(res.status){
                        Swal.fire('Archived!', res.message, 'success');
                        tblCalendarOfHolidays.ajax.reload(null, false);
                    }else{
                        Swal.fire('Error!', res.message, 'error');
                    }
                }
            });
        }
    });
}



const CalendarBasic = function () {
    let calendarInitialized = false;
    
    return {
        init: function () {
            if (calendarInitialized) {
                $('#m_calendar').fullCalendar('render');
                return;
            }
            
            const todayDate = moment().startOf('day');
            const YM = todayDate.format('YYYY-MM');
            const YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
            const TODAY = todayDate.format('YYYY-MM-DD');
            const TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');

            holidayCalendar = $('#m_calendar')
                .fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaDay,listYear'
                    },
                    // Hide time display in month view
                    displayEventTime: false,
                    
                    events: _tempContentData.events,

                    dayClick: function (date, jsEvent, view) {
                        openAddHolidayModal(date.format());
                    },

                    eventClick: function (calEvent, jsEvent, view) {
                        openEditHolidayModal(calEvent);
                    },

                    eventDrop: function (info) {
                        updateOnDragDone(info);
                    },

                    eventRender: function(event, element) {
                        // Remove default time display
                        element.find('.fc-time').remove();
                        
                        // Get speakers info
                        const speakers = event.speakers || [];
                        const speakerNames = speakers.map(speaker => speaker.speaker_name).join(', ');
                        
                        // Create custom event content using Metronic 5 classes
                        const customContent = `
                            <div class="m-widget4__item-wrapper">
                                <div class="m-widget4__item-title m--font-boldest" style="color: white; font-size: 11px;">
                                    ${event.title}
                                </div>
                                <div class="m-widget4__item-desc" style="color: rgba(255,255,255,0.8); font-size: 9px;">
                                    <span class="m-badge m-badge--white m-badge--pill m-badge--xs m--margin-right-5">
                                        <i class="la la-map-marker"></i>
                                    </span>
                                    ${event.venue}
                                </div>
                                ${speakers.length > 0 ? `
                                <div class="m-widget4__item-desc" style="color: rgba(255,255,255,0.7); font-size: 8px;">
                                    <span class="m-badge m-badge--white m-badge--pill m-badge--xs m--margin-right-5">
                                        <i class="la la-user"></i>
                                    </span>
                                    ${speakerNames}
                                </div>` : ''}
                            </div>
                        `;
                        
                        // Replace the event content
                        element.find('.fc-content').html(customContent);
                        
                        // Add custom tooltip with full speaker details
                        let tooltipText = `${event.description}\nVenue: ${event.event_venue}`;
                        if (speakers.length > 0) {
                            tooltipText += '\nSpeakers:\n';
                            speakers.forEach(speaker => {
                                tooltipText += `• ${speaker.speaker_name} - ${speaker.position}, ${speaker.company}\n`;
                            });
                        }
                        element.attr('title', tooltipText);
                        
                        // Add Metronic classes and styling
                        element.addClass('m-portlet__body m--padding-5');
                        element.css({
                            'border-radius': '4px',
                            'border': 'none'
                        });
                        
                        if (speakers.length > 0) {
                            element.css('background', 'linear-gradient(135deg, #6c7ae0 0%, #9baaf3 100%)');
                            element.addClass('m-badge--brand');
                        } else {
                            element.css('background', 'linear-gradient(135deg, #fd397a 0%, #fb5581 100%)');
                            element.addClass('m-badge--danger');
                        }
                    },
                });
                
            calendarInitialized = true;
        }
    };
}();

$('a[data-toggle="tab"][href="#calender-view-tab"]').on('shown.bs.tab', function () {
    if ($('#m_calendar').data('fullCalendar')) {
        $('#m_calendar').fullCalendar('render');
    } else {
        CalendarBasic.init();
    }
});
let holidayCalendar;
let speakerIndex = 1;
let selectedEventData = null;
let selectedCompanies = null;
let selectedDepartments = null;
let selectedCompaniesEdit = null;
let selectedDepartmentsEdit = null;
let select2Training = null;
let select2InitType = null;
let Select2Category = null;
if (_currentActions.includes("view_own_request")) {
    $(".btnNew").hide();
}

let tblCalendarOfHolidays = $("#table-calendar-of-holidays")
    .DataTable({
        dom: 'frtlip',
        rowId: 'id',
        serverSide: true,
        processing: false,
        global: false,
        searching: false,
        ordering: true,
        order: [[0, 'desc']],
        ajax: {
            url: baseUrl('events/get_events_tabular'),
            type: 'post',
            dataType: 'json',
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = $("#search-holidays").val();
                d.search['filter_year'] = $('#filter-year').val();
            },
        },
        columns: [
            { data: 'id', name: 'id', visible: false },
            { data: 'event_title',
                render: function(data, type, row) {
                    return `
                        <div>
                            <div class="fw-bold">${row.event_title}</div>
                            <div class="small text-muted">BY: ${row.events_by}</div>
                        </div>
                    `;
                }
            },
            // { data: 'description' },
            { 
                data: "event_venue", 
                name: 'event_venue',
                render: function(data, type, row) {
                    let venue = row.event_venue ? row.event_venue : "No Venue";
                    let schedule = "";
                    let statusTag = "";
            
                    if (row.event_from && row.event_to) {
                        let fromDate = moment(row.event_from);
                        let toDate   = moment(row.event_to);
                        let today    = moment();
            
                        let displayFrom = fromDate.format("MMM DD, YYYY");
                        let displayTo   = toDate.format("MMM DD, YYYY");
            
                        if (displayFrom === displayTo) {
                            schedule = displayFrom; // same day
                        } else {
                            schedule = displayFrom + " - " + displayTo;
                        }
            
                        if (today.isBefore(fromDate, 'day')) {
                            statusTag = `<span class="badge badge-info">Upcoming</span>`;
                        } else if (today.isBetween(fromDate, toDate, 'day', '[]')) {
                            statusTag = `<span class="badge badge-warning">Ongoing</span>`;
                        } else if (today.isAfter(toDate, 'day')) {
                            statusTag = `<span class="badge badge-success">Done</span>`;
                        }
                    }
            
                    return `
                        <div>
                            <strong>${venue}</strong><br> 
                            <small>${schedule}</small><br>
                            ${statusTag}
                        </div>`;
                }
            },
            { 
                data: null, orderable: false,
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
                width: "10%",
                render: function (data, type, row, meta) {
                    return itemDatatableActions(row.id, row.participant_status, row.event_from, row.event_to);
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
        maxDate: moment().add(365, 'days'),
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
    data: {speakers: [{id: Date.now(), name: '', position: '', company: ''}], edit_speakers:[]},
    mounted: function () {
        this.initSelect2();
    },
    methods:{
        addNewSpeaker() {
            this.speakers.push({
                id: Date.now(),
                name: '',
                position: '',
                company: ''
            });
        },
        removeSpeaker(speakerId) {
            if (this.speakers.length > 1) {
                const index = this.speakers.findIndex(speaker => speaker.id === speakerId);
                if (index > -1) {
                    this.speakers.splice(index, 1);
                }
            }
        },
        initSelect2(){
            $("#company").select2({
                dropdownParent: $('#new_event_form'),
                data: _tempContentData.company,
                allowClear: false,
                placeholder: "Select an option",
                width: '100%'
            }).on("change", function () {
                let data = $(this).select2('data');
                selectedCompanies = data.map(item => item.text); 
            });

            $("#department").select2({
                dropdownParent: $('#new_event_form'),
                data: _tempContentData.department,
                allowClear: false,
                placeholder: "Select an option",
                width: '100%'
            }).on("change", function () {
                let data = $(this).select2('data');
                selectedDepartments = data.map(item => item.text); 
            });

        }
    },
});

$.validate({
    form : '#new_event_form',
    lang: 'en',
    scrollToTopOnError : false,
    onSuccess : function(form) {
        let formData = $(form).serializeArray();
        formData.push({name: "company_array", value: JSON.stringify(selectedCompanies)});
        formData.push({name: "department_array", value: JSON.stringify(selectedDepartments)});
        $.ajax({
            url: baseUrl('events/save_event'),
            type: "POST",
            dataType: "json",
            data: formData,
            beforeSend: function() {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function(res) {
                if(res.success){
                    $(form).trigger("reset");
                    $("#addNewEvent").modal('hide');
                    selectedCompanies = null;
                    selectedDepartments = null;
                    toastr.success(res.message, 'Success', 5000);
                    tblCalendarOfHolidays.ajax.reload();
                    editEventVue.events = res.events;
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

function itemDatatableActions(id, status, from, to) {
    let _actionButton = "<span class='action-buttons'>";

    if (_currentActions.includes("view_own_request")) {
        if (status !== undefined && status === "pending") {
            _actionButton += `
                <a href="javascript:void(0)" 
                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnSave" 
                    onclick="confirmParticipant(${id})" 
                    title="Confirm Attendance">
                    <i class="la la-check-circle text-success"></i>
                </a>
                <a href="javascript:void(0)" 
                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnSave" 
                    onclick="declineParticipant(${id})" 
                    title="Decline Attendance">
                    <i class="la la-times-circle text-danger"></i>
                </a>`;
        } else {
            _actionButton += `<span class="text-success">Attendance Confirmed</span>`;
        }

        _actionButton += "</span>";
        return _actionButton;
    }

    _actionButton += `
        <a style="text-decoration: none;" 
            class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit" 
            onclick="onEditEvent(${id})" 
            data-toggle="m-tooltip" data-placement="bottom" 
            data-skin="dark" 
            title="View Event">
            <i class="la la-eye"></i>
        </a>
        <button 
            type="button" 
            class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnArchive" 
            onclick="deleteArchive(${id})" 
            data-toggle="m-tooltip" data-placement="bottom" title="Archive Event" 
            data-skin="dark">
            <i class="la la-file-archive-o"></i>
        </button>
        <a 
            href="${baseUrl('events/add_participants/') + id}" 
            class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnNew" 
            data-toggle="m-tooltip" data-placement="bottom" title="Manage Training" 
            data-skin="dark">
            <i class="la la-user"></i>
        </a>
    `;

    _actionButton += "</span>";

    return _actionButton;
}


let editEventVue = new Vue({
    el: "#edit-events-modal",
    data: {
        events:_tempContentData.events,
        eventsData:{

        },
        disabled:true,
    },
    watch: {
        events: {
            handler(newEvents) {
                $('#m_calendar').fullCalendar('removeEvents');
                $('#m_calendar').fullCalendar('addEventSource', newEvents);
                $('#m_calendar').fullCalendar('rerenderEvents');
            },
            deep: true
        }
    },
    mounted: function () {
        this.initSelect2();
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
        },
        initSelect2(){
            $("#company_edit").select2({
                dropdownParent: $('#edit_event_form'),
                data: _tempContentData.company,
                allowClear: false,
                placeholder: "Select an option",
                width: '100%'
            }).on('change', function () {
                let data = $(this).select2('data');
                editEventVue.eventsData.company_ids = JSON.parse(JSON.stringify(data.map(item => item.id)));
                editEventVue.eventsData.company_array = JSON.parse(JSON.stringify(data.map(item => item.text)));
                selectedCompaniesEdit = JSON.parse(JSON.stringify(data.map(item => item.text)));
            });

            $("#department_edit").select2({
                dropdownParent: $('#edit_event_form'),
                data: _tempContentData.department,
                allowClear: false,
                placeholder: "Select an option",
                width: '100%'
            }).on('change', function () {
                let data = $(this).select2('data');
                editEventVue.eventsData.department_ids = JSON.parse(JSON.stringify(data.map(item => item.id)));
                editEventVue.eventsData.department_array = JSON.parse(JSON.stringify(data.map(item => item.text)));
                selectedDepartmentsEdit = JSON.parse(JSON.stringify(data.map(item => item.text)));
            });

            $('#edit_training_type').select2({
                placeholder: "Select an Option",
                dropdownParent: $('#edit_event_form'),
                allowClear: false,
                width: '100%',
                data: _tempContentData.options.training_type
            }).on('change', function () {
                let selectedId = $(this).val();
                editEventVue.eventsData.training_type = selectedId;
                select2Training = selectedId;
            });
            
            // $('#edit_init_type').select2({
            //     placeholder: "Select an Option",
            //     dropdownParent: $('#edit_event_form'),
            //     allowClear: false,
            //     width: '100%',
            //     data: _tempContentData.options.initiation_type
            // }).on('change', function () {
            //     let selectedId = $(this).val();
            //     editEventVue.eventsData.init_type = selectedId;
            //     select2Init = selectedId;   
            // });
            
            $('#edit_training_category').select2({
                placeholder: "Select an Option",
                dropdownParent: $('#edit_event_form'),
                allowClear: false,
                width: '100%',
                data: _tempContentData.options.training_category
            }).on('change', function () {
                let selectedId = $(this).val();
                editEventVue.eventsData.training_category = selectedId;
                select2Category = selectedId;
            })

        }
    },
});

function onEditEvent(id) {
    let rowData = tblCalendarOfHolidays.row('#'+id).data();
    selectedEventData = JSON.parse(JSON.stringify(rowData));
    selectedEventData.date = moment(rowData.event_from).format("MMM DD, YYYY") 
    + " - " + moment(rowData.event_to).format("MMM DD, YYYY");
    editEventVue.eventsData = JSON.parse(JSON.stringify(rowData));
    $("#btnEdit").show();
    $("#company_edit").val(rowData.company_ids).trigger('change');
    $("#department_edit").val(rowData.department_ids).trigger('change');
    $("#edit_training_type").val(rowData.training_type).trigger('change');
    // $("#edit_init_type").val(rowData.init_type).trigger('change');
    $("#edit_training_category").val(rowData.training_category).trigger('change');
    $("#edit-events-modal").modal("show");
}

$.validate({
    form : '#edit_event_form',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess : function(form) {
        let formData =  $(form).serialize();
        formData += "&company_array=" + encodeURIComponent(JSON.stringify(selectedCompaniesEdit));
        formData += "&department_array=" + encodeURIComponent(JSON.stringify(selectedDepartmentsEdit));    
        let eventData = JSON.parse(JSON.stringify(editEventVue.eventsData));
        if(!checkChanges(eventData, selectedEventData)){
            toastr.info('No changes detected.', 'Info', 5000);
            return false;
        };
        $.ajax({
            url: baseUrl('events/update_event'),
            type: "POST",
            dataType: "json",
            data: formData,
            beforeSend: function() {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function(res) {
                if(res.status){
                    $(form).trigger("reset");
                    $("#company_edit").val(null).trigger("change");
                    $("#department_edit").val(null).trigger("change");
                    $("#edit_training_type").val(null).trigger('change');
                    // $("#edit_init_type").val(null).trigger('change');
                    $("#edit_training_category").val(null).trigger('change');
                    selectedCompaniesEdit = null;
                    selectedDepartmentsEdit = null;
                    $("#edit-events-modal").modal('hide');
                    toastr.success(res.message, 'Success', 5000);
                    tblCalendarOfHolidays.ajax.reload(null, false);
                    editEventVue.events = res.events;
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

function normalize(obj) {
    if (Array.isArray(obj)) {
        return obj.map(v => typeof v === "string" ? v.trim() : normalize(v));
    } else if (obj !== null && typeof obj === "object") {
        return Object.fromEntries(
            Object.entries(obj).map(([k, v]) => [k, normalize(v)])
        );
    }
    return typeof obj === "string" ? obj.trim() : obj;
}

function checkChanges(newData, oldData) {
    const normNew = normalize(newData);
    const normOld = normalize(oldData);
    return JSON.stringify(normNew) !== JSON.stringify(normOld);
}

$('#edit-events-modal').on('shown.bs.modal', function () {
    if ($('#edit_event_date').data('daterangepicker')) {
        $('#edit_event_date').data('daterangepicker').remove();
    }
    $('#edit_event_date').daterangepicker({
        showDropdowns: true,
        autoUpdateInput: false,
        maxDate: moment().add(365, 'days'),
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
                url: baseUrl('events/archive_event'),
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
            holidayCalendar = $('#m_calendar')
                .fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month'
                    },
                    displayEventTime: false,
                    
                    events: editEventVue.events,

                    eventClick: function (calEvent, jsEvent, view) {
                        openEditHolidayModal(calEvent,jsEvent);
                    },

                    eventRender: function(event, element) {
                        element.find('.fc-time').remove();
                        const speakers = event.speakers || [];
                        let title = event.title.length > 20 ? event.title.slice(0, 20) + '...' : event.title;
                        const customContent = `
                            <div class="m-widget4__item-wrapper">
                                <div class="m-widget4__item-title m--font-boldest mb-1" style="color: black; font-size: 1.2em;">
                                    ${title}
                                </div>
                            </div>
                        `;
                        
                        element.find('.fc-content').html(customContent);
                        const totalParticipants = event.total_participants || 0;

                        let tooltipText = '';

                        if (speakers.length > 0) {
                            tooltipText += '\nResource Person(s):\n';
                            speakers.forEach(speaker => {
                                tooltipText += `• ${speaker.speaker_name}`;
                                // if (speaker.position) tooltipText += ` - ${speaker.position}`;
                                // if (speaker.company) tooltipText += `, ${speaker.company}`;
                                tooltipText += '\n';
                            });
                        }

                        tooltipText += `Venue: ${event.venue}\n`;
                        tooltipText += `Number of Trainees: ${totalParticipants}\n`;
                        

                        
                        element.attr('title', tooltipText.trim());
                        element.addClass('m-portlet__body m--padding-5');
                        element.css({
                            'border-radius': '4px',
                            'border': 'none'
                        });
                        let background = event.hex_code && event.hex_code.trim() !== '' ? event.hex_code: '#c4c4c4';
                        element.css({
                            'background-color': background,
                            'border-color': background
                        });
                    },
                });
            calendarInitialized = true;
        }
    };
}();

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    var target = $(e.target).attr("href");
    $('.tab-pane').removeClass('active show');
    $(target).addClass('active show');
});

$('a[data-toggle="tab"][href="#calender-view-tab"]').on('shown.bs.tab', function () {
    if ($('#m_calendar').data('fullCalendar')) {
        $('#m_calendar').fullCalendar('render');
    } else {
        CalendarBasic.init();
    }
});

function openEditHolidayModal(event) {
    const data = {
        id: event.id,
        events_by: event.events_by,
        company_ids: event.company_ids,
        department_ids: event.department_ids,
        event_title: event.title,
        event_from: event.start ? event.start.format() : null,
        event_to: event.end ? event.end.format() : event.start.format(),
        description: event.description || "",
        event_venue: event.venue || "",
        speakers: event.speakers || [],
        training_type: event.training_type || null,
        init_type: event.init_type || null,
        training_category: event.training_category || null
    };

    editEventVue.eventsData = JSON.parse(JSON.stringify(data));
    $("#company_edit").val(data.company_ids).trigger('change');
    $("#department_edit").val(data.department_ids).trigger('change');
    $("#edit_training_type").val(data.training_type).trigger('change');
    // $("#edit_init_type").val(data.init_type).trigger('change');
    $("#edit_training_category").val(data.training_category).trigger('change');
    $("#edit-events-modal").modal("show");
    $("#btnEdit").hide();
}
 
function confirmParticipant(id) {
    let rowData = tblCalendarOfHolidays.row('#'+id).data();
    let fullname = rowData.firstname + ' ' + rowData.middlename + ' ' + rowData.lastname;
    Swal.fire({
        title: 'Confirm Attendance?',
        text: "Do you want to mark this participant as attending?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Confirm',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl('events/confirm_participant'),
                type: "POST",
                data: { 
                    csrf_token: $("#csrf_token").val(),
                    event_id: id,
                    fullname: fullname,
                    event_title: rowData.event_title,
                    id: rowData.id 
                },
                dataType: "json",
                success: function(res) {
                    if(res.success){
                        toastr.success(res.message, 'Success', 5000);
                        tblCalendarOfHolidays.ajax.reload();
                    }else{
                        toastr.error(res.message, 'Error', 5000);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Something went wrong while updating participant.', 'Error');
                }
            });
        }
    });
}


function declineParticipant(id) {
    let rowData = tblCalendarOfHolidays.row('#'+id).data();
    let fullname = rowData.firstname + ' ' + rowData.middlename + ' ' + rowData.lastname;
    Swal.fire({
        title: 'Decline Attendance?',
        text: "Do you want to mark this participant as not attending?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Decline',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl('events/decline_participant'),
                type: "POST",
                data: { 
                    csrf_token: $("#csrf_token").val(),
                    event_id: id,
                    fullname: fullname,
                    event_title: rowData.event_title,
                    id: rowData.id 
                },
                dataType: "json",
                success: function(res) {
                    if(res.success){
                        toastr.success(res.message, 'Success', 5000);
                        tblCalendarOfHolidays.ajax.reload();
                    }else{
                        toastr.error(res.message, 'Error', 5000);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Something went wrong while declining participant.', 'Error');
                }
            });
        }
    });
}

$('#addNewEvent').on('hidden.bs.modal', function () {
    $("#new_event_form").trigger("reset");
    eventVue.speakers = [{id: Date.now(), name: '', position: '', company: '' }];
    $("#company").val([]).trigger("change");
    $("#department").val([]).trigger("change");
    $("#training_type").val(null).trigger("change");
    $("#init_type").val(null).trigger("change");
    $("#training_category").val(null).trigger("change");
});

$('#training_type').select2({
    placeholder: "Select an Option",
    dropdownParent: $('#addNewEvent'),
    allowClear: true,
    width: '100%',
    data: _tempContentData.options.training_type
});

$('#init_type').select2({
    placeholder: "Select an Option",
    dropdownParent: $('#addNewEvent'),
    allowClear: true,
    width: '100%',
    data: _tempContentData.options.initiation_type
});

$('#training_category').select2({
    placeholder: "Select an Option",
    dropdownParent: $('#addNewEvent'),
    allowClear: true,
    width: '100%',
    data: _tempContentData.options.training_category
});

CalendarBasic.init();
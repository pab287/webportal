let tblCalendarOfHolidays = $("#table-calendar-of-holidays-archive")
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
            global: false,
            dataType: 'json',
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.is_archived = 1;
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
                className: "text-center",
                // width: "10%",
                render: function (data, type, row, meta) {
                    return itemDatatableActions(row.id, row.participant_status, row.event_from, row.event_to);
                }
            },            
        ]
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
            <button 
                type="button" 
                class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnRestore" 
                onclick="restoreEvent(${id})" 
                data-toggle="m-tooltip" data-placement="bottom" title="Restore Event" 
                data-skin="dark">
                <i class="la la-reply"></i>
            </button>
        `;
    
        _actionButton += "</span>";
    
        return _actionButton;
    }
    

    function restoreEvent(id){
        Swal.fire({
            title: 'Are you sure?',
            text: "Are you sure you want to restore this event?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, restore it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: baseUrl('events/restore_event'),
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
                            Swal.fire('Restored!', res.message, 'success');
                            tblCalendarOfHolidays.ajax.reload(null, false);
                        }else{
                            Swal.fire('Error!', res.message, 'error');
                        }
                    }
                });
            }
        });
    }

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

    $('#search-holidays').donetyping(function () {
        tblCalendarOfHolidays.ajax.reload();
    });
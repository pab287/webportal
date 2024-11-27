const modalContainer = $('.document-modal-container');
const modalConfirmContainer = $('.document-modal-confirm-container');
let holidayCalendar;

const CalendarBasic = function () {
    return {
        //main function to initiate the module
        init: function () {
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
                    eventStartEditable: true, // editable through dragging option
                    eventDurationEditable: false,  // editable through resizing option
                    eventLimit: true, // allow "more" link when too many events
                    navLinks: true,
                    events: {
                        url: baseUrl("hris/calendar/get_holidays"),
                        method: "GET"
                    },

                    dayClick: function (date, jsEvent, view) {
                        openAddHolidayModal(date.format());
                        // $(this).css('background-color', '#f7f7f7');
                    },

                    eventClick: function (calEvent, jsEvent, view) {
                        openEditHolidayModal(calEvent);
                    },

                    eventDrop: function (info) {
                        updateOnDragDone(info);
                    },

                    eventRender: function (event, element) {
                        if (element.hasClass('fc-day-grid-event')) {
                            element.data('content', event.description);
                            element.data('placement', 'top');
                            mApp.initPopover(element);
                        } else if (element.hasClass('fc-time-grid-event')) {
                            element.find('.fc-title').append('<div class="fc-description">' + event.description + '</div>');
                        } else if (element.find('.fc-list-item-title').lenght !== 0) {
                            element.find('.fc-list-item-title').append('<div class="fc-description">' + event.description + '</div>');
                        }
                        
                        if(event.company == 'all'){
                            element.find('.fc-content').append('<div class="mt-3 mb-2 text-white"><span style="font-weight: 900">Tagged Companies</span>: All Companies</div>');
                        }else{
                            let obj = '';
                            const companies = event.company.length;
                            $.each(event.company, function(index, value){
                                const lastItem = index === companies - 1;
                                const separator = lastItem ? '' : ', ';
                                obj += value.text.split(' ')[0] + separator;
                            });

                            element.find('.fc-content').append('<div class="mt-3 mb-2 text-white"><span style="font-weight: 900">Tagged Companies</span>: '+obj+'</div>');
                        }
                    }
                });
        }
    };
}();

let tblCalendarOfHolidays = $("#table-calendar-of-holidays")
    .DataTable({
        dom: 'frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: true,
        order: [[0, 'asc']],
        ajax: {
            url: baseUrl('hris/calendar/get_holidays_tabular'),
            type: 'post',
            dataType: 'json',
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = $("#search-holidays").val();
                d.search['filter_year'] = $('#filter-year').val();
            }
        },
        columns: [
            {data: 'date_from'},
            {data: 'date_to'},
            {data: 'description'},
            {
                data: 'company',
                orderable: false,
                className: "companies",
                width: '15%',
                render: function(data, type, row){
                    if(data == 'all'){
                        return data.toUpperCase() + ' Companies';
                    }else{
                        let obj = '';
                        const margin = data.length > 1 ? 'mr-1' : '';
                        $.each(data, function(index, value){
                            obj += "<span class='badge badge-pill badge-success text-white py-1 "+margin+"'>"+value.text.split(' ')[0]+"</span>";
                        });
                        return obj;
                    }
                }
            },
            {
                data: 'classification',
                render: function (data, type, row) {
                    return data.toLowerCase().includes("holiday") ? data : data + " Holiday";
                }
            },
            {
                data: null,
                width: '8%',
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    const _data = JSON.stringify({id: row.id, description: row.classification, company: row.company, department: row.department});

                    let actions = "" +
                        "<button type='button' " +
                        "   class='btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-primary btnEdit'" +
                        "   onclick='openEditHolidayModal(" + _data + ", true)'" +
                        "   data-toggle='m-tooltip' data-placement='bottom' data-skin='dark' data-delay='{\"show\": 300}' " +
                        "   data-original-title='Edit Holiday'>" +
                        "   <i class='la la-edit'></i>" +
                        "</button> ";

                    actions += '' +
                        '<button type="button" ' +
                        '   class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-danger btnEdit" ' +
                        '   data-toggle="m-tooltip" data-placement="bottom" data-skin="dark" data-delay=\'{"show": 300}\' ' +
                        '   data-original-title="Delete Holiday"' +
                        '   onclick="confirmDeleteHoliday(' + row.id + ')">' +
                        '   <i class="la la-trash-o"></i>' +
                        '</button>';


                    return actions;
                }
            }
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

function reloadTable() {
    tblCalendarOfHolidays.ajax.reload();
}

function openAddHolidayModal(date, fromTabular = false) {
    $.ajax({
        url: baseUrl("hris/calendar/open_edit_modal"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: "hris/masterfile/calendar/calendar_of_holidays/add_holiday_dialog",
            function_name: "passDataToDialog",
            formData: {date, fromTabular}
        },
        success: function (response) {
            modalContainer.html(response.html);
            modalContainer.find('#classification')
                .select2({
                    placeholder: 'Select Classification',
                    dropdownParent: modalContainer,
                    width: '100%',
                    allowClear: true,
                    ajax: {
                        url: baseUrl(`hris/calendar/get_holiday_classification`),
                        dataType: "JSON",
                        delay: 500
                    }
                });

            modalContainer.find('#company')
                .select2({
                    placeholder: 'Select Company',
                    dropdownParent: modalContainer,
                    width: '100%',
                    allowClear: true,
                    ajax: {
                        url: baseUrl(`hris/calendar/get_company`),
                        dataType: "JSON",
                        delay: 500,
                        beforeSend:() => {
                            mApp.unblock();
                        },
                        success:() => {
                            mApp.unblock();
                        }
                    }
                }).on("select2:select", function (e) {
                    const _this = this;
                    const tempVal = $(_this).val();
                    const data = e.params.data;
                    const tempData = $(_this).select2("data");

                    modalContainer.find('#department').prop('disabled', false);
                }).on("select2:unselect", function(e){
                    const _this = this;
                    const tempData = $(_this).select2("data");

                    if(tempData.length == 0){
                        modalContainer.find('#department').prop('disabled', true);
                        modalContainer.find('#department').val("").trigger('change');
                    }
                    
                }).on("select2:clear", function(e){
                    modalContainer.find('#department').prop('disabled', true);
                    modalContainer.find('#department').val("").trigger('change');
                });
                
            modalContainer.find('#department')
                .select2({
                    placeholder: 'Select Department',
                    dropdownParent: modalContainer,
                    width: '100%',
                    allowClear: true,
                    ajax: {
                        url: baseUrl(`hris/calendar/get_department`),
                        dataType: "JSON",
                        delay: 500,
                        beforeSend:() => {
                            mApp.unblock();
                        },
                        success:() => {
                            mApp.unblock();
                        }
                    }
                });

            if (fromTabular) {
                modalContainer.find('.date')
                    .datetimepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "MM dd,yyyy",
                        autoclose: true,
                    });
            }

            modalContainer.modal('show');
        }
    });
}

function openEditHolidayModal(data, fromTabular = false) {
    $.ajax({
        url: baseUrl("hris/calendar/open_edit_modal"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: "hris/masterfile/calendar/calendar_of_holidays/edit_holiday_dialog",
            function_name: "getHolidayItem",
            model: "hris/Holiday_model",
            formData: {
                id: data.id,
                fromTabular
            }
        },
        success: function (response) {
            const holiday = data.description.toLowerCase();
            const holiday_classification = holiday.includes("holiday") ? holiday : (holiday + " holiday");

            modalContainer.html(response.html);
            modalContainer.find('#classification')
                .select2({
                    placeholder: 'Select Classification',
                    dropdownParent: modalContainer,
                    width: '100%',
                    allowClear: true,
                    ajax: {
                        url: baseUrl(`hris/calendar/get_holiday_classification`),
                        dataType: "JSON",
                        delay: 500
                    }
                });

            const option = new Option(holiday_classification, holiday_classification, false, true);
            $("#classification", modalContainer).append(option);

            modalContainer.find('#company')
                .select2({
                    placeholder: 'Select Company',
                    dropdownParent: modalContainer,
                    width: '100%',
                    allowClear: true,
                    ajax: {
                        url: baseUrl(`hris/calendar/get_company`),
                        dataType: "JSON",
                        delay: 500,
                        beforeSend:() => {
                            mApp.unblock();
                        },
                        success:() => {
                            mApp.unblock();
                        }
                    }
                }).on("select2:select", function (e) {
                    const _this = this;
                    const tempVal = $(_this).val();
                    const data = e.params.data;
                    const tempData = $(_this).select2("data");

                    modalContainer.find('#department').prop('disabled', false);
                }).on("select2:unselect", function(e){
                    const _this = this;
                    const tempData = $(_this).select2("data");

                    if(tempData.length == 0){
                        modalContainer.find('#department').prop('disabled', true);
                        modalContainer.find('#department').val("").trigger('change');
                    }
                    
                }).on("select2:clear", function(e){
                    modalContainer.find('#department').prop('disabled', true);
                    modalContainer.find('#department').val("").trigger('change');
                });

            if(data.company != 'all'){
                let comp_option;
                $.each(data.company, function(index, value) {
                    comp_option = new Option(value.text, value.id, false, true);
                    $("#company", modalContainer).append(comp_option);
                });
                modalContainer.find('#department').prop('disabled', false);
            }else{
                modalContainer.find('#department').prop('disabled', true);
            }

            modalContainer.find('#department')
                .select2({
                    placeholder: 'Select Department',
                    dropdownParent: modalContainer,
                    width: '100%',
                    allowClear: true,
                    ajax: {
                        url: baseUrl(`hris/calendar/get_department`),
                        dataType: "JSON",
                        delay: 500,
                        beforeSend:() => {
                            mApp.unblock();
                        },
                        success:() => {
                            mApp.unblock();
                        }
                    }
                });
            
            if(data.department != 'all'){
                let comp_option;
                $.each(data.department, function(index, value) {
                    comp_option = new Option(value.text, value.id, false, true);
                    $("#department", modalContainer).append(comp_option);
                });
            }

            if (fromTabular) {
                modalContainer.find('.date')
                    .datetimepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "MM dd,yyyy",
                        autoclose: true
                    });
            }

            modalContainer.modal('show');
        }
    });
}

function confirmDeleteHoliday(id) {
    $.ajax({
        url: baseUrl("hris/masterfile/open_confirm_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {
                title: "<i class='la la-trash mr-2'></i>Confirm Delete",
                message: "Are you sure to delete this holiday?",
                action: "hris/calendar/delete_holiday/" + id,
                color: "btn-danger"
            },
            path: "ams/confirmation_dialog",
            function_name: "passDataToDialog"
        },
        success: function (modal) {
            modalConfirmContainer.html(modal);
            modalConfirmContainer.modal('show');
        }
    });
}

function updateOnDragDone(data) {
    var date_end = (data.end) ? data.end : data.start;
    $.ajax({
        url: baseUrl('hris/calendar/update_holiday'),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            id: data.id,
            description: data.title,
            classification: data.description,
            date_from: data.start.format(),
            date_to: date_end.format(),
            drag: true
        },
        success: function (response) {
            _toaster(response, "Holiday Moved.", 10000);
        }
    });
}

function _toaster(response, title, duration) {
    const type = response.success ? 'success' : 'error';
    const _title = response.success ? title : 'Error';
    toastr[type](response.message, _title, duration);
}

function closeEntryDialog() {
    modalContainer.modal("hide");
}

function closeConfirmDialog() {
    modalConfirmContainer.modal("hide");
}

function clearCalender() {
    $("#m_calendar").fullCalendar("destroy");
    tblCalendarOfHolidays.ajax.reload();
}

$(document)
    .on('show.bs.modal', '.modal', function (event) {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function () {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });

jQuery(document)
    .ready(function () {
        $('.m-content')
            .on('submit', '#calendar-save-holiday',
                function (e) {
                    e.preventDefault();
                    const form = $(this);
                    const url = form.attr('action');
                    const formData = new FormData(this);

                    if (form.isValid()) {
                        $.ajax({
                            url,
                            type: "POST",
                            dataType: "JSON",
                            processData: false,
                            contentType: false,
                            data: formData,
                            success: function (response) {
                                _toaster(response, 'Holiday Saved.', 10000);
                                if (holidayCalendar) {
                                    holidayCalendar.fullCalendar('refetchEvents');
                                }
                                tblCalendarOfHolidays.ajax.reload();
                                closeEntryDialog();
                            }
                        });
                    }
                })
            .on('submit', '#calendar-edit-holiday',
                function (e) {
                    e.preventDefault();
                    const form = $(this);
                    const url = form.attr('action');
                    const formData = new FormData(this);

                    if (form.isValid()) {
                        $.ajax({
                            url,
                            type: "POST",
                            dataType: "JSON",
                            processData: false,
                            contentType: false,
                            data: formData,
                            success: function (response) {
                                _toaster(response, 'Holiday Updated.', 10000);
                                if (holidayCalendar) {
                                    holidayCalendar.fullCalendar('refetchEvents');
                                }

                                tblCalendarOfHolidays.ajax.reload();
                                closeEntryDialog();
                            }
                        });
                    }
                })
            .on('submit', '#confirmation-dialog',
                function (e) {
                    e.preventDefault();
                    const url = $(this).attr('action');

                    $.ajax({
                        url: baseUrl(url),
                        type: "GET",
                        dataType: "JSON",
                        success: function (response) {
                            _toaster(response, 'Holiday Deleted.', 10000);

                            // close both dialogs
                            closeConfirmDialog();
                            closeEntryDialog();
                            if (holidayCalendar) {
                                holidayCalendar.fullCalendar('refetchEvents');
                            }

                            tblCalendarOfHolidays.ajax.reload();
                        }
                    });
                });
    });

function departments(container, company = {}){
    container.find('#department')
    .select2({
        placeholder: 'Select Department',
        dropdownParent: container,
        width: '100%',
        allowClear: true,
        ajax: {
            url: baseUrl(`hris/calendar/get_department`),
            dataType: "JSON",
            data: {
                company: company
            },
            delay: 500
        }
    }).on("select2:select", function (e) {
        console.log('test');
    });
}
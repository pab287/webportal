const modalContainer = $('.document-modal-container');
let holidayCalendar;

function openEvaluationDialog(data) {
    $.ajax({
        url: baseUrl("hris/calendar/open_edit_modal"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: "hris/masterfile/calendar/calendar_of_probationary_employees/modals/evaluation_dialog",
            function_name: "passDataToDialog",
            formData: data
        },
        success: function (response) {
            modalContainer.html(response.html);
            modalContainer.modal('show');
        }
    });
}

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
                        right: 'month,listYear'
                    },
                    editable: false,
                    eventLimit: true, // allow "more" link when too many events
                    navLinks: true,
                    events: {
                        url: baseUrl("hris/calendar/get_calendar_of_probationary_employees"),
                        method: "GET"
                    },

                    dayClick: function (date, jsEvent, view) {
                    },

                    eventClick: function (data, jsEvent, view) {
                        const isClickable = parseInt(data.clickable) === 1;
                        const _start = data.start.format();
                        delete data.source;
                        delete data.start;
                        data.start = _start;

                        // console.log(data);

                        if (isClickable) {
                            openEvaluationDialog(data);
                        }
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
                    }
                });
        }
    };
}();

jQuery(document)
    .ready(function () {
        CalendarBasic.init();
    });

function addProbeeEvaluation(probee_cal_id, emp_id, status, field) {
    $.ajax({
        url: baseUrl('hris/calendar/add_probee_evaluation'),
        type: "post",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            probee_cal_id,
            emp_id,
            status,
            field
        },
        success: function (response) {
            if (response.success) {
                toastr.success(response.message, "Successfully Evaluated.", 10000);
            } else {
                toastr.error(response.message, "Error.", 10000);
            }
            modalContainer.modal("hide");
            holidayCalendar.fullCalendar('refetchEvents');
        }
    });
}
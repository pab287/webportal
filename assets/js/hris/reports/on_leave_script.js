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
                    height: 500,
                    defaultView: "listMonth",
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay,listMonth'
                    },
                    eventStartEditable: false, // editable through dragging option
                    eventDurationEditable: false,  // editable through resizing option
                    eventLimit: false, // allow "more" link when too many events
                    navLinks: true,
                    displayEventTime: false,
                    events: {
                        url: baseUrl('hris/reports/get_employee_leaves'),
                        type: 'GET'
                    },

                    dayClick: function (date, jsEvent, view) {
                    },

                    eventClick: function (calEvent, jsEvent, view) {
                    },

                    eventDrop: function (info) {
                    },

                    eventRender: function (event, element) {
                        
                        element.find('.fc-title').css('word-wrap','break-word');
                        if (element.hasClass('fc-day-grid-event')) {
                            element.data('content', event.reason);
                            element.data('placement', 'top');
                            mApp.initPopover(element);
                        } else if (element.hasClass('fc-time-grid-event')) {
                            element.data('content', event.reason);
                            element.data('placement', 'top');
                            mApp.initPopover(element);
                        } else if (element.find('.fc-list-item-title').lenght !== 0) {
                            element.find('.fc-list-item-title').addClass('reports-text-black');
                            
                            element.find('.fc-list-item-title').append('<div class="fc-description">' + event.reason + '</div>');
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
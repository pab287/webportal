const modalContainer = $('.document-modal-container');
const modalConfirmContainer = $('.document-modal-confirm-container');
let holidayCalendar;

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
            
        ],
    });
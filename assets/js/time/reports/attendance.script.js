var devices = [
    { device_id: 'all', name: "All Devices", location: "All" },
];

if(typeof _tempContentData !== "undefined" && _tempContentData){
    devices = (typeof _tempContentData.devices !== "undefined" && _tempContentData.devices.length > 0) ? devices.concat(_tempContentData.devices) : [];
}

var vmData = new Vue({
    el: "#attendance_logs",
    lang: 'en',
    data : {
        devices: devices,
        table: {},
        device_id: 'all',
        search_val: '',
        loc_name: 'all',
        filterFrom: '',
        filterTo: '',
    },
    mounted: function(){
        const instance = this;

        var tbl = $("#table-logs");

        instance.table = tbl.DataTable({
            searching: false,
            dom: '<"toolbar">frtlip',
            serverSide: true,
            processing: true,
            destroy: true,
            retrieve: true,
            order: [[0, 'desc']],
            ajax: {
                url: baseUrl(`gcctime/reports/get_attendance_logs_datatable_request`),
                type: "POST",
                dataType: "JSON",
                data: function (d) {
                    d.device = instance.device_id;
                    d.csrf_token = _csrf_hash;
                    d.search['value'] = instance.search_val;
                    d.filterFrom = instance.filterFrom;
                    d.filterTo = instance.filterTo;
                }
            },
            columns: [
                { data: 'datetime', visible: false, orderable: false },
                { data: "image", width: "8%", className: "text-center", orderable: false,
                render: function (data) {
                    return `<img class="m--img-rounded m--marginless m--img-centered user__pic" src="${data}" width="60" height="60">`;
                }
                },
                { data: "firstname", width: '50%',
                    render: function (data, type, row, meta) {
                        var html = ``;

                        html += `${row.employee_name}`;

                        html += `<p class="m-0"><small><strong>DEVICE NAME:</strong> ${ row.device_name }</small></p>`;
                        html += `<p class="m-0"><small><strong>LOCATION NAME:</strong> ${ row.location_name }</small></p>`;

                        return html;
                    }
                },
                { data: "date", width: '15%', className: 'text-center' },
                { data: "time", width: '15%', className: 'text-center' },
                { data: 'type', width: '15%', className: 'text-center', orderable: false },
            ],
        });

        $("#generalSearch").donetyping(function (callback) {
            instance.search_val = $(this).val();
            instance.table.ajax.reload();
        });

        const initAttendanceReportStartDate = moment();
        const initAttendanceReportEndDate = moment();
        let selectedAttendanceReportStartDate = moment();
        let selectedAttendanceReportEndDate = moment();

        $('#attendance-report-date-range-picker').daterangepicker({
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',

            startDate: initAttendanceReportStartDate,
            endDate: initAttendanceReportEndDate,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            format: "MMM. DD, YYYY"
        }, function (start, end, label) {
            selectedAttendanceReportStartDate = start;
            selectedAttendanceReportEndDate = end;

            let _label = label;
            filterLateReport = label;
            if (label === "Custom Range") {
                _label += ": <strong>" + start.format("MMM. DD, YYYY") + "</strong> to <strong>" + end.format("MMM. DD, YYYY") + "</strong>";
            }

            $(".selected-filter", $('#attendance-report-date-range-picker')).html(_label);

            instance.filterFrom = moment(selectedAttendanceReportStartDate).format("YYYY-MM-DD");
            instance.filterTo = moment(selectedAttendanceReportEndDate).format("YYYY-MM-DD");
            instance.table.ajax.reload();
        });
    },
    methods: {
        filterByDevice: function (device = 'all', location = 'all') {
            const instance = this;
            
            instance.device_id = device;
            instance.loc_name = location;
            instance.table.ajax.reload();
        },
        isEmpty: function (value) {
            return $.isEmptyObject(value);
        }, toUpperCase: function (value) {
            return value.toUpperCase();
        }, dateTimeFormat: function (value) {
            return moment(value).format('YYYY-MM-DD HH:mm:ss');
        }, clearFilter: function () {
            const instance = this;

            instance.filterFrom = "";
            instance.filterTo = "";

            instance.table.ajax.reload();
            $(".selected-filter", $('#attendance-report-date-range-picker')).html('Today');

            $('#attendance-report-date-range-picker').data('daterangepicker').setStartDate(moment());
            $('#attendance-report-date-range-picker').data('daterangepicker').setEndDate(moment());
        }
    }
});
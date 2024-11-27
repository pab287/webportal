<style>
    .selected-filter {
        text-transform: none;
    }

    .m-widget1__title__time {
        font-size: 32px;
        font-family: sans-serif;
    }

    table#table-attendance-logs.dataTable tr.group td {
        background-color: #ececec;
        font-weight: 500;
        font-size: 16px;
    }
</style>
<div class="fade modal" tabindex="-1" role="dialog"
     id="attendance-log">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="m--font-boldest mb-0 text-muted">ATTENDANCE LOG</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <button class="btn btn-brand m-btn m-btn--icon"
                                id="attendance-date-range-picker">
                            <span>
                                <i class="fa fa-calendar"></i>
                                <span class="selected-filter pl-3 pr-2">Today</span>
                            </span>
                        </button>
                    </div>
                </div>

                <div class="table-responsive mt-4">
                    <table class="table table-bordered"
                           id="table-attendance-logs" width="100%">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Device</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let dt;
    const initStartDate = moment();
    const initEndDate = moment();
    var selectedStartDate = moment();
    var selectedEndDate = moment();
    let filter = "Today";

    $("#attendance-log").on("show.bs.modal", function () {
        dt = $("#table-attendance-logs")
            .DataTable({
                dom: 'rtlp',
                serverSide: false,
                destroy: true,
                ajax: {
                    url: baseUrl(`gcctime/timesheet_cron/get_attendance_log`),
                    dataType: "JSON",
                    type: "POST",
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
                        d.selectedStartDate = selectedStartDate.format("YYYY-MM-DD");
                        d.selectedEndDate = selectedEndDate.format("YYYY-MM-DD");
                    }
                },
                columns: [
                    {
                        data: 'header',
                        visible: false
                    },
                    {
                        data: 'date',
                        width: "60%",
                        render: function (data, row, type, meta) {
                            return `<div class="" style="font-size: 18px;">${moment(data).format("hh:mm:ss A")}</div>`;
                        }
                    },
                    {
                        data: "device",
                        width: "40%",
                        render: function (data, row, type, meta) {
                            return `<div style="text-transform: none;" class="text-muted">${data ? data : `---`}</div>`;
                        }
                    }
                ],
                autoWidth: false,
                drawCallback: function (settings) {
                    var api = this.api();
                    var rows = api.rows({page: 'current'}).nodes();
                    var last = null;
                    const pages = api.page.info().pages;
                    const paging = $(api.table().container()).find('div.dataTables_paginate');
                    const length = $(api.table().container()).find('div.dataTables_length');

                    api.column(0, {page: 'current'}).data().each(function (group, i) {
                        if (last !== group) {
                            $(rows).eq(i).before(
                                '<tr class="group"><td colspan="5">' + moment(group).format("MMM.DD, YYYY") + '</td></tr>'
                            );

                            last = group;
                        }
                    });

                    if (pages <= 1) {
                        paging.css('visibility', 'hidden');
                        length.css('visibility', 'hidden');
                    } else {
                        paging.css('visibility', '');
                        length.css('visibility', '');
                    }
                }
            });
    });

    $('#attendance-date-range-picker')
        .daterangepicker({
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',

            startDate: initStartDate,
            endDate: initEndDate,
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
            selectedStartDate = start;
            selectedEndDate = end;

            let _label = label;
            filter = label;
            if (label === "Custom Range") {
                _label += ": <strong>" + start.format("MMM. DD, YYYY") + "</strong> to <strong>" + end.format("MMM. DD, YYYY") + "</strong>";
            }

            $(".selected-filter").html(_label);
            dt.ajax.reload();
        });
</script>
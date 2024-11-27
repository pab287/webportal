let searchLoaForToday = '';
let searchLoaForTheWeek = '';
const loaType = ['Undertime', 'Half Day', 'Whole Day', 'Others'];
const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
const colors = [
    '#f44336',
    '#e91e63',
    '#9c27b0',
    '#673ab7',
    '#3f51b5',
    '#2196f3',
    '#03a9f4',
    '#009688',
    '#ff5722',
    '#795548',
    '#607d8b',
];

get_loa_for_today();
get_loa_for_the_week();

function getColor(status) {
    if ((status === null || status === '')) {
        return '#ffffff';
    }
    return colors[(status.charCodeAt(0)) % colors.length];
}

function get_loa_for_today() {
    $('#table-loa-today').DataTable({
        dom: '<"toolbar">rtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl('eforms/Loa/get_loa_for_today'),
            type: 'post',
            dataType: 'json',
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search.value = searchLoaForToday;
            }
        },
        searching: true,
        columns: [
            {
                data: 'employee_name',
                render: function (data, type, row) {
                    return '<small>' + row.company + ' - ' + row.position + '</small>' +
                        '<br />' +
                        '<div class="m--margin-top-5 m--font-boldest">' + row.employee_name + '</div>' +
                        '<div><small>' + row.position + '</small></div>';
                }
            },
            {
                data: 'reason',
                width: '55%',
                render: function (data, type, row) {
                    const _loaType = loaType[parseInt(row.type) - 1];
                    return '<small> Type: <span class="m--font-bolder">' + _loaType + '</span></small><br />' + '<div class="m--margin-top-5">' + row.reason + '</div>';
                }
            },
        ],
        order: [
            ['0', 'asc']
        ]
    });
}

function get_loa_for_the_week() {
    $('#table-loa-weekly').DataTable({
        dom: '<"toolbar">rtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl('eforms/Loa/get_loa_for_the_week'),
            type: 'post',
            dataType: 'json',
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search.value = searchLoaForTheWeek;
            }
        },
        searching: true,
        columns: [
            {
                data: 'employee_name',
                render: function (data, type, row) {
                    return '<small>' + row.company + ' - ' + row.position + '</small>' +
                        '<br />' +
                        '<div class="m--margin-top-5 m--font-boldest">' + row.employee_name + '</div>' +
                        '<div><small>' + row.position + '</small></div>';
                }
            },
            {
                data: 'reason',
                width: '55%',
                render: function (data, type, row) {
                    const _loaType = loaType[parseInt(row.type) - 1];
                    return '<small> Type: <span class="m--font-bolder">' + _loaType + '</span></small><br />' + '<div class="m--margin-top-5">' + row.reason + '</div>';
                },
                width: '40%'
            },
            {
                data: 'date_from',
                render: function (data, type, row) {
                    const _date = new Date(data);
                    const _m = months[_date.getMonth()];
                    return _m !== undefined ? _m + ' ' + _date.getDate() + ', ' + _date.getFullYear() : 'N/A';
                }
            },
            {
                data: 'date_to',
                render: function (data, type, row) {
                    const _date = new Date(data);
                    const _m = months[_date.getMonth()];
                    return _m !== undefined ? _m + ' ' + _date.getDate() + ', ' + _date.getFullYear() : 'N/A';
                }
            },
        ],
    });
}

am4core.ready(function () {
    get_loa_for_analytics();
});

function get_loa_for_analytics() {


    $.ajax({
        type: "GET",
        url: baseUrl('eforms/Loa/get_loa_analytics_for_dashboard'),
        dataType: "JSON",
        success: function (result) {

            am4core.ready(function () {
                am4core.useTheme(am4themes_animated);
                result.map((item) => {
                    item.color = getColor(item.status);
                });

                var chart = am4core.create("chartdiv", am4charts.XYChart3D);

                var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
                categoryAxis.dataFields.category = "status";
                categoryAxis.renderer.inversed = true;

                var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());

                var series = chart.series.push(new am4charts.ColumnSeries3D());
                series.dataFields.valueX = "count";
                series.dataFields.categoryY = "status";
                series.name = "Count";
                series.columns.template.propertyFields.fill = "color";
                series.columns.template.tooltipText = "{valueX}";
                series.columns.template.column3D.stroke = am4core.color("#fff");
                series.columns.template.column3D.strokeOpacity = 0.2;

                chart.data = result;
            });
        }
    });
}
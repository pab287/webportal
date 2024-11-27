const colors = [
    '#f44336',
    '#868e96',
    '#dc3545',
    '#ffc107',
    '#673ab7',
    '#3f51b5',
    '#2196f3',
    '#03a9f4',
    '#009688',
    '#ff5722',
    '#28a745'
    //'#9c27b0',
    //'#e91e63',
    //'#795548',
    //'#607d8b',
];

function getColor(status) {
    if ((status === null || status === '')) {
        return '#ffffff';
    }
    return colors[(status.charCodeAt(0)) % colors.length];
}

am4core.ready(function () {
    get_cash_for_analytics();
});

function get_cash_for_analytics() {
    am4core.useTheme(am4themes_animated);

    $.ajax({
        type: "GET",
        url: baseUrl('eforms/overtime/get_analytics_for_dashboard'),
        dataType: "JSON",
        success: function (result) {

            result.map((item) => {
                item.color = getColor(item.status);
            });

            am4core.useTheme(am4themes_animated);

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
        }
    });
}

var tblOTToday = $("#table-ot-today").DataTable({
    dom: '<"toolbar">tl',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("eforms/overtime/get_daily/"),
		type: "post",
        dataType: "json",
       data: function(d){
            d.csrf_token = _csrf_hash
        }
    },
    searching: false,
    columns: [
        { data: "status", render: function (data) {return statusBg(data)}},
        { data: "firstname", render: function ( data, type, row, meta ) { return row.display_employee; }},
        { data: "date_from", render: function (data) {return formatTime(data)}},
        { data: "date_to", render: function (data) {return formatTime(data)}},
    ],
});

var tblOTWeekly = $("#table-ot-weekly").DataTable({
    dom: '<"toolbar">tl',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("eforms/overtime/get_weekly/"),
		type: "post",
        dataType: "json",
        data: function(d){
                d.csrf_token = _csrf_hash
        }
    },
    searching: false,
    columns: [
        { data: "status", render: function (data) {return statusBg(data)}},
        { data: "firstname", render: function ( data, type, row, meta ) { return row.display_employee; }},
        { data: "date_from", render: function (data) {return formatTime(data)}},
        { data: "date_to", render: function (data) {return formatTime(data)}},
    ],
});

function statusBg(status){
    switch(status){
        case "Pending":
            return '<div class="m-badge m-badge--warning text-white m-badge--wide" role="alert"><strong>Pending</strong></div>';
        break;
        case "Approved":
            return '<div class="m-badge m-badge--success text-white m-badge--wide" role="alert"><strong>Approved</strong></div>';
        break;
        case "Disapproved":
            return '<div class="m-badge m-badge--danger text-white m-badge--wide" role="alert"><strong>Disapproved</strong></div>';
        break;
        default:
            return '<div class="m-badge m-badge--metal text-white m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
        break;
    }
}

function formatTime(time){
    return (time=="0000-00-00 00:00:00") ? "" : moment(time).format("LLL");
}


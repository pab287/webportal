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


get_loa_for_analytics();

function get_loa_for_analytics() {
    $.ajax({
        type: "GET",
        url: baseUrl('eforms/accountability/chart_data/'),
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

function getColor(status) {
    if ((status === null || status === '')) {
        return '#ffffff';
    }
    return colors[(status.charCodeAt(0)) % colors.length];
}

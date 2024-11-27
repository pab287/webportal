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
function getColor(status) {
    if ((status === null || status === '')) {
        return '#ffffff';
    }
    return colors[(status.charCodeAt(0)) % colors.length];
}
var search_val = "";
var tblCash = $("#table-cash-today").DataTable({
    dom: '<"toolbar">tl',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("eforms/cash_advance/get_daily/"),
		type: "post",
        dataType: "json",
       data: function(d){
            d.csrf_token = _csrf_hash,
            d.search['value'] = search_val
        }
    },
    searching: true,
    columns: [
        
        { data: "reference_no"},
        { data: "firstname", render: function (data, type, row, meta) {return displayName(row.display_name)}},
        { data: "amt_applied", className: "text-right", width: "10%"},
        { data: "purpose"}
        
    ],
    
});


function displayName($displayName){
    return $displayName;
}

var tblCash2 = $("#table-cash-weekly").DataTable({
    dom: '<"toolbar">tl',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("eforms/cash_advance/get_weekly/"),
		type: "post",
        dataType: "json",
       data: function(d){
            d.csrf_token = _csrf_hash,
            d.search['value'] = search_val
        }
    },
    searching: true,
    columns: [
        
        { data: "reference_no"},
        { data: "firstname", render: function (data, type, row, meta) {return displayName(row.display_name)}},
        { data: "amt_applied", className: "text-right", width: "10%"},
        { data: "purpose"}
        
    ],
    
});
am4core.ready(function () {
    get_cash_for_analytics();
});

function get_cash_for_analytics() {
    am4core.useTheme(am4themes_animated);

    $.ajax({
        type: "GET",
        url: baseUrl('eforms/cash_advance/get_cash_analytics_for_dashboard'),
        dataType: "JSON",
        success: function (result) {
            
            result.map((item) => {
                item.color = getColor(item.status);
            });

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("chartdiv", am4charts.XYChart3D);

            // Create axes
            var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "status";
            categoryAxis.renderer.inversed = true;

            var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());

            // Create series
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
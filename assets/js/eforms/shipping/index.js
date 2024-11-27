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
var tblShipping = $("#table-shipping-today").DataTable({
    dom: '<"toolbar">tl',
	serverSide: true,
    processing: true,
    ajax: {
		url: baseUrl("eforms/shipping/get_daily/"),
		type: "post",
        dataType: "json",
       data: function(d){
            d.csrf_token = _csrf_hash,
            d.search['value'] = search_val
        }
    },
    searching: true,
    columns: [
        
        { data: "reference_no" },
        { data: "file_under"},
        { data: "firstname", render: function (data, type, row, meta) {return displayName(row.display_name)}},
        { data: "description"},
        
    ],
    
});
var tblShipping2 = $("#table-shipping-weekly").DataTable({
    dom: '<"toolbar">tl',
	serverSide: true,
    processing: true,
    ajax: {
		url: baseUrl("eforms/shipping/get_weekly/"),
		type: "post",
        dataType: "json",
       data: function(d){
            d.csrf_token = _csrf_hash,
            d.search['value'] = search_val
        }
    },
    searching: true,
    columns: [
        
        { data: "reference_no" },
        { data: "file_under"},
        { data: "firstname", render: function (data, type, row, meta) {return displayName(row.display_name)}},
        { data: "description"},
        
    ],
    
});

function displayName($displayName){
    return $displayName;
}
am4core.ready(function () {
    get_shipping_for_analytics();
});

function get_shipping_for_analytics() {
    am4core.useTheme(am4themes_animated);

    $.ajax({
        type: "GET",
        url: baseUrl('eforms/shipping/get_shipping_analytics_for_dashboard'),
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


$.ajax({
    url : baseUrl("eforms/shipping/most_shippingloc_details/"),
    type: "GET",
    dataType: "JSON",
    success: function(data)
    {
        $("#shiptoloc").append(data.ship_to_address);
        $("#countsl").append(data.c + "x");
        var ave = (data.c / data.sum) * 100;
        $("#p1").css("width", ave);
    }
  });

$.ajax({
    url : baseUrl("eforms/shipping/most_shippingto_details/"),
    type: "GET",
    dataType: "JSON",
    success: function(data)
    {
        $("#shipto").append(data.ship_to);
        $("#countst").append(data.c + "x");
        var ave = (data.c / data.sum) * 100;
        $("#p2").css("width", ave);
    }
  });

  $.ajax({
    url : baseUrl("eforms/shipping/most_shippingit_details/"),
    type: "GET",
    dataType: "JSON",
    success: function(data)
    {
        $("#shipitid").append(data.stock_code+" -");
        $("#shipitdes").append(data.description);
        $("#countsi").append(data.c + "x");
        var ave = (data.c / data.sum) * 100;
        $("#p3").css("width", ave);
    }
  });
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
var tblTravel = $("#table-travel-today").DataTable({
    dom: '<"toolbar">tl',
	serverSide: true,
    processing: true,
    ajax: {
		url: baseUrl("eforms/travel_order/get_daily/"),
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
        { data: "company" },
        { data: null },
        { data: null, width: "15%", },
        { data: null, width: "25%", },
        
    ],
    columnDefs: [{
        data: null,
        defaultContent: "",
        targets: 2,
        orderable: false,
        render: function (data, type, row, meta) {
            var tempHtml = "<div class='custom-details driver--details'><p>No Assigned Driver</p></div>";
            if (typeof row.is_service !== "undefined" && row.is_service == "1" && row.driver !== "") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>" + row.driver + "</p>";
                if (typeof row.vehicle_plate !== "undefined" && row.vehicle_description !== "undefined") {
                    tempHtml += "<p>" + row.vehicle_plate + "</p>";
                    tempHtml += "<p>" + row.vehicle_description + "</p>";
                }
                tempHtml += "</div>";
            }
            if (typeof row.is_commute !== "undefined" && row.is_commute == "1") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>Commute</p>";
                tempHtml += "</div>";
            }
            if (typeof row.is_personal !== "undefined" && row.is_personal == "1") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>Personal</p>";
                tempHtml += "<p>Vehicle</p>";
                tempHtml += "</div>";
            }
            if (typeof row.is_others !== "undefined" && row.is_others == "1" && $.trim(row.others_remarks) !== "") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>" + row.others_remarks + "</p>";
                tempHtml += "</div>";
            }

            return tempHtml;
        },
    }, {
        data: null,
        defaultContent: "",
        targets: 3,
        orderable: false,
        render: function (data, type, row, meta) {
            var tempHtml = "<div class='custom-details driver--details'><ul class='custom_list--dot'><li>No Assigned Personnel</li></ul></div>";
            if (typeof row.personnels !== "undefined" && row.personnels.length > 0) {
                tempHtml = "<div class='custom-details'>";
                tempHtml += "<ul class='custom_list--dot'>";
                jQuery.each(row.personnels, function (i, v) {
                    tempHtml += "<li>" + v + "</li>";
                });
                tempHtml += "</ul>";
                tempHtml += "</div>";
            }

            return tempHtml;
        },
    }, {
        data: null,
        defaultContent: "",
        targets: 4,
        orderable: false,
        render: function (data, type, row, meta) {
            var tempHtml = "<div class='custom-details driver--details'><p>No Assigned Destination</p></div>";
            if (typeof row.destination !== "undefined" && row.destination.length > 0) {
                tempHtml = "<div class='custom-details'>";
                tempHtml += "<ul class='custom_list--dot'>";
                jQuery.each(row.destination, function (i, v) {
                    tempHtml += "<li>" + v + "</li>";
                });
                tempHtml += "</ul>";
                tempHtml += "</div>";
            }

            return tempHtml;
        },
    }]
});
var tblTravel2 = $("#table-travel-weekly").DataTable({
    dom: '<"toolbar">tl',
	serverSide: true,
    processing: true,
    ajax: {
		url: baseUrl("eforms/travel_order/get_weekly/"),
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
        { data: "company" },
        { data: null },
        { data: null, width: "15%", },
        { data: null, width: "25%", },
        
    ],
    columnDefs: [{
        data: null,
        defaultContent: "",
        targets: 2,
        orderable: false,
        render: function (data, type, row, meta) {
            var tempHtml = "<div class='custom-details driver--details'><p>No Assigned Driver</p></div>";
            if (typeof row.is_service !== "undefined" && row.is_service == "1" && row.driver !== "") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>" + row.driver + "</p>";
                if (typeof row.vehicle_plate !== "undefined" && row.vehicle_description !== "undefined") {
                    tempHtml += "<p>" + row.vehicle_plate + "</p>";
                    tempHtml += "<p>" + row.vehicle_description + "</p>";
                }
                tempHtml += "</div>";
            }
            if (typeof row.is_commute !== "undefined" && row.is_commute == "1") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>Commute</p>";
                tempHtml += "</div>";
            }
            if (typeof row.is_personal !== "undefined" && row.is_personal == "1") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>Personal</p>";
                tempHtml += "<p>Vehicle</p>";
                tempHtml += "</div>";
            }
            if (typeof row.is_others !== "undefined" && row.is_others == "1" && $.trim(row.others_remarks) !== "") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>" + row.others_remarks + "</p>";
                tempHtml += "</div>";
            }

            return tempHtml;
        },
    }, {
        data: null,
        defaultContent: "",
        targets: 3,
        orderable: false,
        render: function (data, type, row, meta) {
            var tempHtml = "<div class='custom-details driver--details'><ul class='custom_list--dot'><li>No Assigned Personnel</li></ul></div>";
            if (typeof row.personnels !== "undefined" && row.personnels.length > 0) {
                tempHtml = "<div class='custom-details'>";
                tempHtml += "<ul class='custom_list--dot'>";
                jQuery.each(row.personnels, function (i, v) {
                    tempHtml += "<li>" + v + "</li>";
                });
                tempHtml += "</ul>";
                tempHtml += "</div>";
            }

            return tempHtml;
        },
    }, {
        data: null,
        defaultContent: "",
        targets: 4,
        orderable: false,
        render: function (data, type, row, meta) {
            var tempHtml = "<div class='custom-details driver--details'><p>No Assigned Destination</p></div>";
            if (typeof row.destination !== "undefined" && row.destination.length > 0) {
                tempHtml = "<div class='custom-details'>";
                tempHtml += "<ul class='custom_list--dot'>";
                jQuery.each(row.destination, function (i, v) {
                    tempHtml += "<li>" + v + "</li>";
                });
                tempHtml += "</ul>";
                tempHtml += "</div>";
            }

            return tempHtml;
        },
    }]
    
});
am4core.ready(function () {
    get_travel_for_analytics();
});

function get_travel_for_analytics() {
    am4core.useTheme(am4themes_animated);

    $.ajax({
        type: "GET",
        url: baseUrl('eforms/travel_order/get_travel_analytics_for_dashboard'),
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
            series.columns.template.events.on("hit", function (e) {
                const data = e.target.dataItem.dataContext;
                const key = data.status;
                window.open(baseUrl('eforms/travel_order/masterfile?status=' + key), "_blank");
            }, this);
            chart.data = result;
        }
    });
}

$.ajax({
    url : baseUrl("eforms/travel_order/most_traveled_person/"),
    type: "GET",
    dataType: "JSON",
    success: function(data)
    {
        $("#shipp").append(data.display_name);
        $("#countsl").append(data.c + "x");
        var ave = (data.c / data.sum) * 100;
        $("#p1").css("width", ave);
    }
  });

$.ajax({
    url : baseUrl("eforms/travel_order/most_traveled_vehicle/"),
    type: "GET",
    dataType: "JSON",
    success: function(data)
    {
        $("#shipv").append(data.vehicle_name + " ("+data.vehicle+")");
        $("#countst").append(data.c + "x");
        var ave = (data.c / data.sum) * 100;
        $("#p2").css("width", ave);
    }
  });

  $.ajax({
    url : baseUrl("eforms/travel_order/most_traveled_destination/"),
    type: "GET",
    dataType: "JSON",
    success: function(data)
    {
        $("#shipd").append(data.destination);
        $("#countsi").append(data.c + "x");
        var ave = (data.c / data.sum) * 100;
        $("#p3").css("width", ave);
    }
  });
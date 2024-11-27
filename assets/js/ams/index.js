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
var type;
var view_type;
function getColor(status) {
    if ((status === null || status === '')) {
        return '#ffffff';
    }
    return colors[(status.charCodeAt(0)) % colors.length];
}


am4core.ready(function () {
    /* START EMPLOYEE STATUS CHART */
    var assetPercentageSummaryContainer = am4core.create("asset-percentage-summary-container", am4core.Container);
    assetPercentageSummaryContainer.width = am4core.percent(100);
    assetPercentageSummaryContainer.height = am4core.percent(100);

    am4core.useTheme(am4themes_animated);

    let chart = new am4charts.XYChart3D();
    chart.parent = assetPercentageSummaryContainer;

    assetDemographics
        .then((done) => {
            chart.data = done.merged;
        })
        .catch((err) => {
            console.log("some error occured " + err);
        });

    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "code";
    categoryAxis.renderer.minGridDistance = 10;
    categoryAxis.renderer.inversed = true;
    categoryAxis.renderer.grid.template.disabled = false;

    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.min = 0;
    valueAxis.extraMax = 0.1;

    var series = chart.series.push(new am4charts.ColumnSeries3D());
    series.dataFields.categoryX = "code";
    series.dataFields.valueY = "value";
    series.columns.template.propertyFields.fill = "color";
    series.columns.template.tooltipText = "{categoryX} : {valueY}";
    series.columns.template.adapter.add("fill", function (fill, target) {
        return chart.colors.getIndex(target.dataItem.index);
    });   

    $(window).resize(function(){
        if(screen.width <= 1400){
            categoryAxis.renderer.labels.template.horizontalCenter = "right";
            categoryAxis.renderer.labels.template.verticalCenter = "middle";
            categoryAxis.renderer.labels.template.rotation = 300;
        }else{
            categoryAxis.renderer.labels.template.rotation = 0;
        }
    });
    if(screen.width <= 1400){
        categoryAxis.renderer.labels.template.horizontalCenter = "right";
        categoryAxis.renderer.labels.template.verticalCenter = "middle";
        categoryAxis.renderer.labels.template.rotation = 300;
    }else{
        categoryAxis.renderer.labels.template.rotation = 0;
    }
    // var labelBullet = series.bullets.push(new am4charts.LabelBullet());
    // labelBullet.label.verticalCenter = "bottom";
    // labelBullet.label.dy = -10;
    // labelBullet.label.text = "{values.valueY.workingValue.formatNumber('#.')}";
    chart.zoomOutButton.disabled = true;
    categoryAxis.sortBySeries = series;
    $("#loader").remove();
}); // end am4core.ready()

let assetDemographics = new Promise((resolve, reject) => {
    $.ajax({
        url: baseUrl("ams/dashboard/get_asset_demographics"),
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            resolve(response);
        },
        error: function (response) {
            reject(response);
        }
    });
});

$(document).ready(function(){
    get_recently_added_assets();
    get_accounted_unaccounted_assets();
    get_asset_per_location(type);
    get_asset_per_status(view_type);
    get_asset_incomplete_details();
    
});

$("#asset_type a").click(function(e){
    e.preventDefault();
    var text = $(this).attr("id");
    $("#per_loc").text(text);
});
$("#asset_status a").click(function(e){
    e.preventDefault();
    var text = $(this).attr("id");
    $("#per_status").text(text);
});


function get_recently_added_assets(recently = 'month') {
    am4core.useTheme(am4themes_animated);

    $.ajax({
        type: "GET",
        url: baseUrl('ams/dashboard/get_recently_added_assets/' + recently),
        dataType: "JSON",
        success: function (result) {

            am4core.useTheme(am4themes_animated);

            var chart = am4core.create("recently-added-assets-container", am4charts.XYChart3D);

            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "status";
            categoryAxis.renderer.inversed = true;
            categoryAxis.renderer.minGridDistance = 2;
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.grid.template.disabled = false;

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.min = 0;
            valueAxis.extraMax = 0.1;
            
            var series = chart.series.push(new am4charts.ColumnSeries3D());
            series.dataFields.categoryX = "status";
            series.dataFields.valueY = "count";
            series.name = "Count";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.tooltipText = "{categoryX} : {valueY}";
            series.columns.template.adapter.add("fill", function (fill, target) {
                return chart.colors.getIndex(target.dataItem.index);
            });

            $(window).resize(function(){
                if(screen.width <= 450){
                    categoryAxis.renderer.labels.template.horizontalCenter = "right";
                    categoryAxis.renderer.labels.template.verticalCenter = "middle";
                    categoryAxis.renderer.labels.template.rotation = 300;
                }
            });
            if(screen.width <= 450){
                categoryAxis.renderer.labels.template.horizontalCenter = "right";
                categoryAxis.renderer.labels.template.verticalCenter = "middle";
                categoryAxis.renderer.labels.template.rotation = 300;
            }
            
            chart.data = result;
        }
    });
}

function get_asset_per_location(type = 'asset') {
    am4core.useTheme(am4themes_animated);
    $.ajax({
        type: "GET",
        url: baseUrl('ams/dashboard/get_asset_per_location/'  + type),
        dataType: "JSON",
        success: function (result) {
            
            am4core.useTheme(am4themes_animated);
            var chart = am4core.create("asset-per-location-container", am4charts.XYChart3D);

            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "location";
            categoryAxis.renderer.inversed = true;
            categoryAxis.renderer.labels.template.rotation = 310;
            categoryAxis.renderer.labels.template.truncate = true;
            categoryAxis.renderer.labels.template.maxWidth = 120;
            categoryAxis.renderer.labels.template.horizontalCenter = "right";
            categoryAxis.renderer.labels.template.verticalCenter = "middle";
            categoryAxis.renderer.minGridDistance = 2;

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.min = 1;

            var series = chart.series.push(new am4charts.ColumnSeries3D());
            series.dataFields.categoryX = "location";
            series.dataFields.valueY = "count";
            series.name = "location";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.tooltipText = "{categoryX} : {valueY}";
            series.columns.template.width = am4core.percent(50);
            // columnTemplate.width = am4core.percent(100)
            series.startLocation = 10;
            series.columns.template.adapter.add("fill", function(fill, target){
                return chart.colors.getIndex(target.dataItem.index);
              });   

            chart.data = result;
        }
    });
}

function get_asset_per_status(type = 'asset') {
    am4core.useTheme(am4themes_animated);

    $.ajax({
        type: "GET",
        url: baseUrl('ams/dashboard/get_asset_per_status/' + type),
        dataType: "JSON",
        success: function (result) {

            am4core.useTheme(am4themes_animated);

            var chart = am4core.create("asset-per-status-container", am4charts.XYChart3D);

            var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "status";
            categoryAxis.renderer.inversed = true;
            categoryAxis.renderer.minGridDistance = 2;
            categoryAxis.renderer.grid.template.location = 0;

            var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
            valueAxis.min = 0;
            valueAxis.extraMax = 0.1;

            var series = chart.series.push(new am4charts.ColumnSeries3D());
            series.dataFields.categoryY = "status";
            series.dataFields.valueX = "count";
            series.name = "Count";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.tooltipText = "{categoryY} : {valueX}";
            series.columns.template.width = am4core.percent(50);
            series.columns.template.column.cornerRadiusTopRight = 10;
            series.columns.template.column.cornerRadiusBottomRight = 10;
            series.columns.template.adapter.add("fill", function(fill, target){
                return chart.colors.getIndex(target.dataItem.index);
              }); 
              
            chart.data = result;
        }
    });
}

function get_asset_incomplete_details() {
    am4core.useTheme(am4themes_animated);

    $.ajax({
        type: "GET",
        url: baseUrl('ams/dashboard/get_asset_incomplete_details'),
        dataType: "JSON",
        success: function (result) {

            am4core.useTheme(am4themes_animated);

            var chart = am4core.create("asset-incomplete-details-container", am4charts.XYChart3D);

            var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "status";
            categoryAxis.renderer.inversed = true;
            categoryAxis.renderer.minGridDistance = 2;
            categoryAxis.renderer.grid.template.location = 0;

            var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
            valueAxis.min = 0;
            valueAxis.extraMax = 0.1;

            var series = chart.series.push(new am4charts.ColumnSeries3D());
            series.dataFields.categoryY = "status";
            series.dataFields.valueX = "count";
            series.name = "Count";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.tooltipText = "{categoryY} : {valueX}";
            series.columns.template.width = am4core.percent(50);
            series.columns.template.column.cornerRadiusTopRight = 10;
            series.columns.template.column.cornerRadiusBottomRight = 10;
            series.columns.template.adapter.add("fill", function(fill, target){
                return chart.colors.getIndex(target.dataItem.index);
            });
        
            chart.data = result;
        }
    });
}

function get_accounted_unaccounted_assets(type = 'accounted_asset') {
    am4core.useTheme(am4themes_animated);

    $.ajax({
        type: "GET",
        url: baseUrl('ams/dashboard/get_accounted_unaccounted_assets/' + type),
        dataType: "JSON",
        success: function (result) {

            am4core.useTheme(am4themes_animated);

            var chart = am4core.create("accounted-unaccounted-assets-container", am4charts.XYChart3D);

            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "status";
            categoryAxis.renderer.inversed = true;
            categoryAxis.renderer.minGridDistance = 2;
            categoryAxis.renderer.grid.template.location = 0;

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.min = 0;
            valueAxis.extraMax = 0.1;

            var series = chart.series.push(new am4charts.ColumnSeries3D());
            series.dataFields.categoryX = "status";
            series.dataFields.valueY = "count";
            series.name = "Count";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.tooltipText = "{categoryX} : {valueY}";
            
            series.columns.template.adapter.add("fill", function (fill, target) {
                return chart.colors.getIndex(target.dataItem.index);
            });
            $(window).resize(function(){
                if(screen.width <= 1400){
                    categoryAxis.renderer.labels.template.horizontalCenter = "right";
                    categoryAxis.renderer.labels.template.verticalCenter = "middle";
                    categoryAxis.renderer.labels.template.rotation = 300;
                }else{
                    categoryAxis.renderer.labels.template.rotation = 0;
                }
            });
            if(screen.width <= 1400){
                categoryAxis.renderer.labels.template.horizontalCenter = "right";
                categoryAxis.renderer.labels.template.verticalCenter = "middle";
                categoryAxis.renderer.labels.template.rotation = 300;
            }else{
                categoryAxis.renderer.labels.template.rotation = 0;
            }
           
            chart.data = result;
        }
    });
}
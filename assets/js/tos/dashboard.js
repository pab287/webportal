
chart_data();

function chart_data(){
    $.ajax({
      url : baseUrl("tos/training/chart_data/"),
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {
        console.log(data);
        am4core.ready(function() {
            am4core.useTheme(am4themes_animated);
            var chart = am4core.create("chart-div", am4charts.XYChart);
            chart.scrollbarX = new am4core.Scrollbar();
            chart.data = data;
            
            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "description";
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.minGridDistance = 20;
            categoryAxis.renderer.labels.template.horizontalCenter = "right";
            categoryAxis.renderer.labels.template.verticalCenter = "middle";
            categoryAxis.renderer.labels.template.rotation = 270;
            categoryAxis.tooltip.disabled = true;
            categoryAxis.renderer.minHeight = 110;
            
            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.renderer.minWidth = 50;
            valueAxis.title.text = "FILES";
            
            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.sequencedInterpolation = true;
            series.dataFields.valueY = "topic";
            series.dataFields.categoryX = "description";
            series.tooltipText = "[{categoryX}: bold]{valueY}[/]";
            series.columns.template.strokeWidth = 0;
            
            series.tooltip.pointerOrientation = "vertical";
            
            series.columns.template.column.cornerRadiusTopLeft = 10;
            series.columns.template.column.cornerRadiusTopRight = 10;
            series.columns.template.column.fillOpacity = 0.8;
            
            // on hover, make corner radiuses bigger
            var hoverState = series.columns.template.column.states.create("hover");
            hoverState.properties.cornerRadiusTopLeft = 0;
            hoverState.properties.cornerRadiusTopRight = 0;
            hoverState.properties.fillOpacity = 1;
            
            series.columns.template.adapter.add("fill", function(fill, target) {
            return chart.colors.getIndex(target.dataItem.index);
            });
            
            // Cursor
            chart.cursor = new am4charts.XYCursor();
            
            }); // end am4core.ready()
        }
    });
}
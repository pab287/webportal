var search_val = "";

var tblResume = $("#table-resume")
    .DataTable({
        dom: '<"toolbar">rt',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("crs/get_week_collection/"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
            }
        },
        searching: true,
        columns: [
            {
                data: "name",
                width: "20%",
            },
            {
                data: "school", width: "20%", render: function (data) {
                    return formatTag(data)
                }
            },
            {
                data: "course", width: "20%", render: function (data) {
                    return formatTag(data)
                }
            },
            {
                data: "position", width: "20%", render: function (data) {
                    return formatTag(data)
                }
            },
            {data: "recruitment", width: "20%"},
        ],
    });

/*$("#autocomplete").autocomplete({
    source: baseUrl("crs/ajax_get_names"), // path to the get_birds method
    messages: {
        noResults: '',
        results: function () {
        }
    }
}).data("ui-autocomplete")._renderItem = function (ul, item) {
    var tempUrl = baseUrl("uploads/module/crs/files/" + item.filename);
    var inner_html = '<a href="' + tempUrl + '"><div class="list_item_container"><div class="autocomplete-label">' + item.label.toUpperCase() + '</div></div></a>';
    return $("<li></li>")
        .data("item.autocomplete", item)
        .append(inner_html)
        .appendTo(ul);
};*/

var tblSupervisory = $("#table-supervisory")
    .DataTable({
        dom: '<"toolbar">rtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("crs/get_supervisory/"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
            }
        },
        searching: true,
        columns: [
            {data: "name", width: "70%", className: 'pl-3 pr-3'},
            {data: "total", width: "30%", className: 'pl-3 pr-3', orderable: false},
        ],
        autoWidth: false
    });

var tblManagerial = $("#table-managerial")
    .DataTable({
        dom: '<"toolbar">rtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("crs/get_managerial/"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
            }
        },
        searching: true,
        columns: [
            {data: "name", width: "70%", className: 'pl-3 pr-3'},
            {data: "total", width: "30%", className: 'pl-3 pr-3', orderable: false},
        ],
        autoWidth: false
    });

var tblSkilled = $("#table-skilled")
    .DataTable({
        dom: '<"toolbar">rtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("crs/get_skilled/"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
            }
        },
        searching: true,
        columns: [
            {data: "name", width: "70%", className: 'pl-3 pr-3'},
            {data: "total", width: "30%", className: 'pl-3 pr-3', orderable: false},
        ],
        autoWidth: false
    });

var tblRank = $("#table-rank")
    .DataTable({
        dom: '<"toolbar">rtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("crs/get_rank/"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        searching: true,
        columns: [
            {data: "name", width: "70%", className: 'pl-3 pr-3'},
            {data: "total", width: "30%", className: 'pl-3 pr-3', orderable: false},
        ],
        autoWidth: false
    });

function formatTag(data) {
    var s1 = data;
    var s2 = s1.substr(1);
    return s2;
}


//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblResume.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblResume.ajax.reload();
});
$.ajax({
    url: baseUrl("crs/count_resume/"),
    type: "GET",
    dataType: "JSON",
    success: function (data) {
        var percent = parseInt((data.count / data.total) * 100);
        $("#resume").append(data.count);
        $("#progress_resume").css("width", percent + "%");
        $("#percent_resume").append(percent + "%");
    }
});

$.ajax({
    url: baseUrl("crs/count_archive/"),
    type: "GET",
    dataType: "JSON",
    success: function (data) {
        var percent = parseInt((data.count / data.total) * 100);
        $("#archive").append(data.count);
        $("#progress_archive").css("width", percent + "%");
        $("#percent_archive").append(percent + "%");
    }
});

am4core.ready(function () {
    var mynimo = 0;
    var jobstreet = 0;
    var walk = 0;
    var referral = 0;
    $.ajax({
        url: baseUrl("crs/count_recruitment/"),
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            mynimo = data.mynimo;
            jobstreet = data.jobstreet;
            walk = data.walk;
            referral = data.referral;

            am4core.useTheme(am4themes_animated);
            var chart = am4core.create("chartdiv", am4charts.XYChart);
            chart.exporting.menu = new am4core.ExportMenu();
            var data = [{
                "year": "MYNIMO",

                "resumes": mynimo
            }, {
                "year": "JOBSTREET",

                "resumes": jobstreet
            }, {
                "year": "WALK-IN",

                "resumes": walk
            }, {
                "year": "REFERRAL",

                "resumes": referral

            }];

            /* Create axes */
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "year";
            categoryAxis.renderer.minGridDistance = 20;

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

            var lineSeries = chart.series.push(new am4charts.LineSeries());
            lineSeries.name = "Resumes";
            lineSeries.dataFields.valueY = "resumes";
            lineSeries.dataFields.categoryX = "year";

            lineSeries.stroke = am4core.color("#fdd400");
            lineSeries.strokeWidth = 3;
            lineSeries.propertyFields.strokeDasharray = "lineDash";
            lineSeries.tooltip.label.textAlign = "middle";

            var bullet = lineSeries.bullets.push(new am4charts.Bullet());
            bullet.fill = am4core.color("#fdd400"); // tooltips grab fill from parent by default
            bullet.tooltipText = "[#fff font-size: 15px]{name} from {categoryX}:\n[/][#fff font-size: 20px]{valueY}[/] [#fff]{additional}[/]"
            var circle = bullet.createChild(am4core.Circle);
            circle.radius = 4;
            circle.fill = am4core.color("#fff");
            circle.strokeWidth = 3;

            chart.data = data;
        }
    });


});
am4core.ready(function () {
    $.ajax({
        url: baseUrl("crs/count_supervisory/"),
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            am4core.useTheme(am4themes_animated);

            var chart = am4core.create("chartdiv2", am4charts.XYChart);
            // chart.scrollbarX = new am4core.Scrollbar();
            chart.data = data;

            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "name";
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.minGridDistance = 30;
            categoryAxis.renderer.minWidth = 10;
            categoryAxis.fontSize = 10;

            var label = categoryAxis.renderer.labels.template;
            label.wrap = true;
            label.maxWidth = 90;

            /*categoryAxis.renderer.labels.template.adapter.add("dy", function (dy, target) {
                if (target.dataItem && target.dataItem.index & 2 == 2) {
                    return dy + 25;
                }
                return dy;
            });*/

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "total";
            series.dataFields.categoryX = "name";

            series.name = "Total";
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/]";
            series.columns.template.fillOpacity = .8;
            series.columns.template.adapter.add("fill", function (fill, target) {
                return chart.colors.getIndex(target.dataItem.index);
            });

            var columnTemplate = series.columns.template;
            columnTemplate.strokeWidth = 0;
            columnTemplate.strokeOpacity = 1;

        }
    });


});
am4core.ready(function () {
    $.ajax({
        url: baseUrl("crs/count_managerial/"),
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("chartdiv3", am4charts.XYChart);
            // chart.scrollbarX = new am4core.Scrollbar();
            // Add data

            chart.data = data;

            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "name";
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.minGridDistance = 30;
            categoryAxis.renderer.minWidth = 10;
            categoryAxis.fontSize = 10;

            var label = categoryAxis.renderer.labels.template;
            label.wrap = true;
            label.maxWidth = 90;

            /*categoryAxis.renderer.labels.template.adapter.add("dy", function (dy, target) {
                if (target.dataItem && target.dataItem.index & 2 == 2) {
                    return dy + 25;
                }
                return dy;
            });*/

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "total";
            series.dataFields.categoryX = "name";
            series.name = "Total";
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/]";
            series.columns.template.fillOpacity = .8;
            series.columns.template.adapter.add("fill", function (fill, target) {
                return chart.colors.getIndex(target.dataItem.index);
            });

            var columnTemplate = series.columns.template;
            columnTemplate.strokeWidth = 0;
            columnTemplate.strokeOpacity = 1;
        }
    });
});

am4core.ready(function () {
    $.ajax({
        url: baseUrl("crs/count_skilled/"),
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            am4core.useTheme(am4themes_animated);

            var chart = am4core.create("chartdiv4", am4charts.XYChart);
            // chart.scrollbarX = new am4core.Scrollbar();
            // Add data

            chart.data = data;

            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "name";
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.minGridDistance = 30;
            categoryAxis.renderer.minWidth = 10;
            categoryAxis.fontSize = 10;

            var label = categoryAxis.renderer.labels.template;
            label.wrap = true;
            label.maxWidth = 90;

            /*categoryAxis.renderer.labels.template.adapter.add("dy", function (dy, target) {
                if (target.dataItem && target.dataItem.index & 2 == 2) {
                    return dy + 25;
                }
                return dy;
            });*/

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "total";
            series.dataFields.categoryX = "name";
            series.name = "Total";
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/]";
            series.columns.template.fillOpacity = .8;
            series.columns.template.adapter.add("fill", function (fill, target) {
                return chart.colors.getIndex(target.dataItem.index);
            });

            var columnTemplate = series.columns.template;
            columnTemplate.strokeWidth = 0;
            columnTemplate.strokeOpacity = 1;
        }
    });
});

am4core.ready(function () {
    $.ajax({
        url: baseUrl("crs/count_rank/"),
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            am4core.useTheme(am4themes_animated);

            var chart = am4core.create("chartdiv5", am4charts.XYChart);
            // chart.scrollbarX = new am4core.Scrollbar();

            chart.data = data;

            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "name";
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.minGridDistance = 30;
            categoryAxis.renderer.minWidth = 10;
            categoryAxis.fontSize = 10;

            var label = categoryAxis.renderer.labels.template;
            label.wrap = true;
            label.maxWidth = 90;

            /*categoryAxis.renderer.labels.template.adapter.add("dy", function (dy, target) {
                if (target.dataItem && target.dataItem.index & 2 == 2) {
                    return dy + 25;
                }
                return dy;
            });*/

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "total";
            series.dataFields.categoryX = "name";
            series.name = "Total";
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/]";
            series.columns.template.fillOpacity = .8;
            series.columns.template.adapter.add("fill", function (fill, target) {
                return chart.colors.getIndex(target.dataItem.index);
            });

            var columnTemplate = series.columns.template;
            columnTemplate.strokeWidth = 0;
            columnTemplate.strokeOpacity = 1;
        }
    });
});

function loadTab(tab) {
    $(".tab-pane").removeClass("show active");
    $(tab).addClass("show active");
}
       
    

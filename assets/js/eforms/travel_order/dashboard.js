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
const formatted = moment().format('MMMM D, YYYY dddd');
let approvedChart = null;
let analyticsChart = null;
let analyticsLabel= $('#analyticsLabel');
let tableDate = null;
let createdToLabel = $('#createdToLabel');
createdToLabel.text(formatted);
let departingToLabel = $('#departingToLabel');
departingToLabel.text(formatted);
function getColor(status) {
    if ((status === null || status === '')) {
        return '#ffffff';
    }
    return colors[(status.charCodeAt(0)) % colors.length];
}
var search_val = "";
var tblTravel = $("#table-travel-today").DataTable({
    dom: 'rtlp',
	serverSide: true,
    processing: true,
    scrollY: "450px", 
    scrollCollapse: true,
    ajax: {
		url: baseUrl("eforms/travel_order/get_created"),
		type: "post",
        dataType: "json",
        global: false,
       data: function(d){
            d.csrf_token = _csrf_hash,
            d.date = tableDate;
        }
    },
    order: [0, "desc"],
    searching: true,
    columns: [
        {data: "id", visible: false},
        { data: "reference_no",width: "*%" },
        { data: "company"},
        { data: null, width: "*%", },
        { data: null, width: "15%", },
        { data: null, width: "25%", },
        
    ],
    columnDefs: [{
        data: null,
        defaultContent: "",
        targets: 3,
        orderable: false,
        render: function (data, type, row, meta) {
            var tempHtml = "<div class='custom-details driver--details'><p>No Assigned Driver</p></div>";
            if (typeof row.is_service !== "undefined" && row.is_service == "1" && row.driver !== "") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>" + row.driver + "</p>";
                if (typeof row.vehicle_plate !== "undefined" && row.vehicle_description !== "undefined") {
                    tempHtml += "<p>" + row.vehicle_plate + "</p>";
                    // tempHtml += "<p>" + row.vehicle_description + "</p>";
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
        targets: 4,
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
        targets: 5,
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
    dom: 'rtlp',
	serverSide: true,
    processing: true,
    scrollY: "450px", 
    scrollCollapse: true,
    ajax: {
		url: baseUrl("eforms/travel_order/get_departing"),
		type: "post",
        dataType: "json",
        global: false,
        data: function(d){
            d.csrf_token = _csrf_hash,
            d.date = tableDate;
        }
    },
    searching: true,
    columns: [
        { data: "id", visible: false},
        { data: "reference_no", width: "*%" },
        { data: "company" },
        { data: null, width: "*%", },
        { data: null, width: "15%", },
        { data: null, width: "25%", },
        
    ],
    columnDefs: [{
        data: null,
        defaultContent: "",
        targets: 3,
        orderable: false,
        render: function (data, type, row, meta) {
            var tempHtml = "<div class='custom-details driver--details'><p>No Assigned Driver</p></div>";
            if (typeof row.is_service !== "undefined" && row.is_service == "1" && row.driver !== "") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>" + row.driver + "</p>";
                if (typeof row.vehicle_plate !== "undefined" && row.vehicle_description !== "undefined") {
                    tempHtml += "<p>" + row.vehicle_plate + "</p>";
                    // tempHtml += "<p>" + row.vehicle_description + "</p>";
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
        targets: 4,
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
        targets: 5,
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
    // get_approved_travel_order();
});

function get_travel_for_analytics(date = null) {
    $.ajax({
        type: "POST",
        url: baseUrl('eforms/travel_order/get_travel_analytics_for_dashboard'),
        dataType: "JSON",
        global: false,
        data: {
            csrf_token: _csrf_hash,
            date: date,
          },
        success: function (result) {
            if (analyticsChart != null) {
                analyticsChart.dispose();
            }

            if(result.length == 0){
                $("#chartdiv").html(`
                    <div class="form-control-label d-flex justify-content-center align-items-center h-100">
                      <h3>No matching records found</h3>
                    </div>
                  `);
                return;
            }

            result.map((item) => {
                item.color = getColor(item.status);
            });

            am4core.useTheme(am4themes_animated);
            var chart = am4core.create("chartdiv", am4charts.XYChart3D);
            var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "status";
            categoryAxis.renderer.inversed = true;
            categoryAxis.renderer.labels.template.adapter.add("textOutput", function(text) {
                if (text === "Recommend_Approved") {
                    return "PENDING APPROVAL";
                }
                if (text === "Pending") {
                    return "FOR RECOMMENDATION";
                }
                if (typeof text === 'string') {
                    return text.replace(/_/g, " ").toUpperCase();
                }
                return text;
            });
            var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());

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
    global: false,
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
    global: false,
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
    global: false,
    success: function(data)
    {
        $("#shipd").append(data.destination);
        $("#countsi").append(data.c + "x");
        var ave = (data.c / data.sum) * 100;
        $("#p3").css("width", ave);
    }
  });

  const dateRangeConfig = {
    startDate: moment("2016-01-01"),           // set to match All Time
    endDate: moment(), 
    singleDatePicker: false,
    showDropdowns: true,
    autoUpdateInput: false,
    opens: 'left', 
    minDate: moment("2016-01-01"),
    maxDate: moment(),
    locale: {
        format: 'YYYY-MM-DD',
        cancelLabel: 'Show All'
    },
    ranges: {
        'All Time': [moment("2016-01-01"), moment()],
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()]
    }
  };

  const tableDateRangeConfig = {
    singleDatePicker: false,
    showDropdowns: true,
    autoUpdateInput: false,
    opens: 'left', 
    maxDate: moment(),
    locale: {
        format: 'YYYY-MM-DD',
        cancelLabel: 'Show All'
    },
    ranges: {
        'All Time': [moment("2016-01-01"), moment()],
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()]
    }
  };

  const HandleAnalyticsApply = (ev, picker) => {
    const startDate = picker.startDate.format('YYYY-MM-DD');
    const endDate = picker.endDate.format('YYYY-MM-DD');
    
    if (startDate === moment().format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        analyticsLabel.text("TODAY"); 
    } else if (startDate === moment().subtract(6, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        analyticsLabel.text("LAST 7 DAYS"); 
    } else if (startDate === moment().subtract(29, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        analyticsLabel.text("LAST 30 DAYS");  
    } else if (startDate === moment().subtract(1, 'days').format('YYYY-MM-DD') && endDate === moment().subtract(1, 'days').format('YYYY-MM-DD')) {
        analyticsLabel.text("YESTERDAY"); 
    } else if (startDate === moment("2016-01-01").format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        analyticsLabel.text("ALL TIME");
    }
    else {
        analyticsLabel.text(`FROM: ${picker.startDate.format('MMM D, YYYY')} - TO: ${picker.endDate.format('MMM D, YYYY')}`);
    }
    get_travel_for_analytics({ start: startDate, end: endDate });
  };

  const handleAnalyticsCancel  = () => {
    analyticsLabel.text("ALL TIME");
    get_travel_for_analytics({  start: moment("2016-01-01").format('YYYY-MM-DD'), end: moment().format('YYYY-MM-DD') });
  };

  $('#toAnalyticsPicker').daterangepicker(dateRangeConfig)
  .on('cancel.daterangepicker', handleAnalyticsCancel)
  .on('apply.daterangepicker', HandleAnalyticsApply);


  const HandleTableApply = (ev, picker) => {
    const startDate = picker.startDate.format('YYYY-MM-DD');
    const endDate = picker.endDate.format('YYYY-MM-DD');
    
    if (startDate === moment().format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        createdToLabel.text(formatted); 
    } else if (startDate === moment().subtract(6, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        createdToLabel.text("LAST 7 DAYS"); 
    } else if (startDate === moment().subtract(29, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        createdToLabel.text("LAST 30 DAYS");  
    } else if (startDate === moment().subtract(1, 'days').format('YYYY-MM-DD') && endDate === moment().subtract(1, 'days').format('YYYY-MM-DD')) {
        createdToLabel.text("YESTERDAY"); 
    } 
    else if (startDate === moment("2016-01-01").format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        createdToLabel.text("ALL TIME");
    }
    else {
        createdToLabel.text(`FROM: ${picker.startDate.format('MMM D, YYYY')} - TO: ${picker.endDate.format('MMM D, YYYY')}`);
    }
    tableDate = {start: startDate, end: endDate};
    tblTravel.ajax.reload();
  };

  const handleTableCancel  = () => {
    analyticsLabel.text("ALL TIME");
    tableDate = {start:moment("2016-01-01").format('YYYY-MM-DD'), end : moment().format('YYYY-MM-DD')};
    tblTravel.ajax.reload();
  };

  $('#createdTablePicker').daterangepicker(tableDateRangeConfig)
  .on('cancel.daterangepicker', handleTableCancel)
  .on('apply.daterangepicker', HandleTableApply);

  const HandleDepartTableApply = (ev, picker) => {
    const startDate = picker.startDate.format('YYYY-MM-DD');
    const endDate = picker.endDate.format('YYYY-MM-DD');
    
    if (startDate === moment().format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        departingToLabel.text(formatted); 
    } else if (startDate === moment().subtract(6, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        departingToLabel.text("LAST 7 DAYS"); 
    } else if (startDate === moment().subtract(29, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        departingToLabel.text("LAST 30 DAYS");  
    } else if (startDate === moment().subtract(1, 'days').format('YYYY-MM-DD') && endDate === moment().subtract(1, 'days').format('YYYY-MM-DD')) {
        departingToLabel.text("YESTERDAY"); 
    } 
    else if (startDate === moment("2016-01-01").format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        departingToLabel.text("ALL TIME");
    }
    else {
        departingToLabel.text(`FROM: ${picker.startDate.format('MMM D, YYYY')} - TO: ${picker.endDate.format('MMM D, YYYY')}`);
    }
    tableDate = {start: startDate, end: endDate};
    tblTravel2.ajax.reload();
  };

  const handleDepartTableCancel  = () => {
    departingToLabel.text("ALL TIME");
    tableDate = {start:moment("2016-01-01").format('YYYY-MM-DD'), end : moment().format('YYYY-MM-DD')};
    tblTravel2.ajax.reload();
  };

  $('#departingTablePicker').daterangepicker(tableDateRangeConfig)
  .on('cancel.daterangepicker', handleDepartTableCancel)
  .on('apply.daterangepicker', HandleDepartTableApply);

  let travelOderDataSheet = new Vue({
    el: "#m-content",
    data: {
        approved:{},
        pieChartLabel: "All Time",
    },
    mounted() {
        this.get_approved_travel_order();
    },
    methods: {
        get_approved_travel_order: function() {
            vm = this;
            $.ajax({
                url : baseUrl("eforms/travel_order/get_approved_chart"),
                type: "POST",
                dataType: "JSON",
                global: false,
                data: {
                    csrf_token: _csrf_hash,
                    start: moment("2016-01-01").format('YYYY-MM-DD'),
                    end:   moment().format('YYYY-MM-DD')
                  },
                success: function(data){
                    vm.approved = Object.assign({}, data.approved);
                    vm.loadGraphApproved(vm.approved);   
                }
            });
        },
        loadGraphApproved(data){
            if (approvedChart != null) {
                approvedChart.dispose();
            }
            let approvedData = Object.entries(data).map(([key, value]) => ({
                approved: key.charAt(0).toUpperCase() + key.slice(1),
                value: parseInt(value),
                color: am4core.color(
                    key == 'Accomplished' ? '#34BFA3' : 
                    key == 'Overdue' ? '#F4516C' : 
                    key == 'Ongoing' ? '#FAC35D' : '#36A3F7'
                ),
                url: key == 'Accomplished' ? '&accomplished=1' : 
                     key == 'Overdue' ? '&overdue=1' : 
                     key == 'Ongoing' ? '&ongoing=1' : ''
            }));
        
              approvedChart = am4core.create("approvedPieChart", am4charts.PieChart);

              const totalValue = approvedData.reduce((sum, item) => sum + item.value, 0);
              if (totalValue === 0) {
                  approvedChart.data = [{
                      approved: "No Data",
                      value: 1000,
                      disabled: true,
                      color: am4core.color("#dadada"),
                      opacity: 0.3,
                      strokeDasharray: "4,4",
                      tooltip: "",
                      url: ""
                  }];
              } else {
                  approvedChart.data = approvedData;
              }

              let pieSeries = approvedChart.series.push(new am4charts.PieSeries());
              pieSeries.dataFields.value = "value";
              pieSeries.dataFields.category = "approved";
              pieSeries.slices.template.propertyFields.fill = "color";
              approvedChart.legend = new am4charts.Legend();

        }
    }
  });

  const handleToApprovedResponse = (data) => {
    const totalValue = Object.values(data.approved).reduce((sum, value) => sum + parseInt(value), 0);
    if (totalValue == 0) {
        approvedChart.hide();
        approvedChart.dispose();

      am4core.useTheme(am4themes_animated);
      approvedChart = am4core.create("approvedPieChart", am4charts.PieChart);
      approvedChart.data = [{
        "approved": "Dummy",
        "disabled": true,
        "value": 1000,
        "color": am4core.color("#dadada"),
        "opacity": 0.3,
        "strokeDasharray": "4,4",
        "tooltip": "NO DATA"
      }];
      var series = approvedChart.series.push(new am4charts.PieSeries());
      series.dataFields.value = "value";
      series.dataFields.category = "approved";
      var slice = series.slices.template;
      slice.propertyFields.fill = "color";
      slice.propertyFields.fillOpacity = "opacity";
      slice.propertyFields.stroke = "color";
      slice.propertyFields.strokeDasharray = "strokeDasharray";
      slice.propertyFields.tooltipText = "tooltip";

      series.labels.template.propertyFields.disabled = "disabled";
      series.ticks.template.propertyFields.disabled = "disabled";
      approvedChart.appear();
    }else{
      if (approvedChart.data.length == 1){
          approvedChart.data = [];
          travelOderDataSheet.loadGraphApproved(data.approved); 
      }
      approvedChart.data.forEach(item => {
        item.value = parseInt(data.approved[item.approved]);
      });
    }
    approvedChart.invalidateRawData();
};

const makeToApprovedRequest = (data) => {
    return $.ajax({
        url : baseUrl("eforms/travel_order/get_approved_chart"),
        type: "POST",
        dataType: "JSON",
        global: false,
        data: {
            csrf_token: _csrf_hash,
            ...data
        }
    });
  };

  const handleTOApprovedCancel = () => {
    travelOderDataSheet.pieChartLabel = "All Time";
    makeToApprovedRequest({ all: true })
        .done(handleToApprovedResponse);
  };

  const handleToApproveApply = (ev, picker) => {
    const startDate = picker.startDate.format('YYYY-MM-DD');
    const endDate = picker.endDate.format('YYYY-MM-DD');
    
    if (startDate === moment().format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        travelOderDataSheet.pieChartLabel = formatted; 
    } else if (startDate === moment().subtract(6, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        travelOderDataSheet.pieChartLabel = "LAST 7 DAYS"; 
    } else if (startDate === moment().subtract(29, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        travelOderDataSheet.pieChartLabel = "LAST 30 DAYS"; 
    } else if (startDate === moment().subtract(1, 'days').format('YYYY-MM-DD') && endDate === moment().subtract(1, 'days').format('YYYY-MM-DD')) {
        travelOderDataSheet.pieChartLabel = "YESTERDAY"; 
    } 
    else if (startDate === moment("2016-01-01").format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        travelOderDataSheet.pieChartLabel = "ALL TIME"; 
    }
    else {
        pieChartLabel.text(`FROM: ${picker.startDate.format('MMM D, YYYY')} - TO: ${picker.endDate.format('MMM D, YYYY')}`);
    }
    makeToApprovedRequest({ start: startDate, end: endDate })
    .done(handleToApprovedResponse);
  };
  
    $('#approveToPicker').daterangepicker(dateRangeConfig)
  .on('cancel.daterangepicker', handleTOApprovedCancel)
  .on('apply.daterangepicker', handleToApproveApply);
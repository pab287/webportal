let statusChart = null;
let categoryChart = null;
let priorityChart = null;
let assigneeChart = null;
let completionChart = null;
let chartHand = null;
let chartLabel = null;
const textRange = "All Time ";
let ticketDataSheet = new Vue({
  el: "#m-content",
  data: {
    widget: {},
    priorities: {},
    categories: {},
    status: {},
    assignee: {},
    completion: {},
    openTicketRange: textRange,
    totalTicketRange: textRange,
    urgentTicketRange: textRange,
    aveResolveRange: textRange,
    aveResponseRange: textRange,
    totalTicketByStatusRange: textRange,
    totalTicketByCategoryRange: textRange,
    totalTicketByPriorityRange: textRange,
    totalTicketByAsigneeRange: textRange,
    totalTicketCompletionRange: textRange,
  },
  mounted() {
    this.loadTicketData();
    this.getGraphDataStatus();
    this.getGraphDataCategory();
    this.getGraphDataPriority();
    this.getGraphDataAssignee();
    this.getGraphCompletion();
  },
  methods: {
    loadTicketData() {
      const vm = this;
      $.ajax({
        url: baseUrl("ticket/ticket/all_tickets/"),
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function(data) {
          vm.widget = Object.assign({}, data.widget);

        }
      });
    },
    // updatePriorityWidget(data){
    //   $("#high").text(data.high);
    //   const percentage = Math.round((data.high / data.all) * 100);
    //   $("#progress_high")
    //     .css("width", percentage + "%")
    //     .attr("aria-valuenow", percentage);
    //   $("#percent_high").text(percentage + "%");

    //   $("#medium").text(data.medium);
    //   const mediumPercentage = Math.round((data.medium / data.all) * 100);
    //   $("#progress_medium")
    //       .css("width", mediumPercentage + "%")
    //       .attr("aria-valuenow", mediumPercentage);
    //   $("#percent_medium").text(mediumPercentage + "%");

    //   $("#low").text(data.low);
    //   const lowPercentage = Math.round((data.low / data.all) * 100);
    //   $("#progress_low")
    //       .css("width", lowPercentage + "%")
    //       .attr("aria-valuenow", lowPercentage);
    //   $("#percent_low").text(lowPercentage + "%");
    // },
    getGraphDataStatus() {
      const vm = this;
      $.ajax({
        url: baseUrl("ticket/ticket/get_all_status/"),
        type: "POST",
        global: false,
        data: {
          csrf_token: _csrf_hash,
        },
        dataType: "JSON",
        success: function(data) {
          vm.status = Object.assign({}, data.status);
          vm.loadGraphByStatus(vm.status);
        }
      });
    },
    loadGraphByStatus(data){
      if (statusChart != null) {
        statusChart.dispose();
      }
      am4core.useTheme(am4themes_animated);
      let chartData = Object.entries(data).map(([status, count]) => ({
        status: status.charAt(0).toUpperCase() + status.slice(1).replace(/\s/g, ' '),
        count: parseInt(count)
      })).sort((a, b) => b.count - a.count);
      statusChart = am4core.create("active_graph", am4charts.XYChart);
      statusChart.data = chartData;
      let categoryAxis = statusChart.yAxes.push(new am4charts.CategoryAxis());
      categoryAxis.dataFields.category = "status";
      categoryAxis.renderer.grid.template.location = 0;
      categoryAxis.renderer.minGridDistance = 30;
      categoryAxis.renderer.labels.template.dy = 10;

      let valueAxis = statusChart.xAxes.push(new am4charts.ValueAxis());
      valueAxis.title.text = "Number of Tickets";

      let series = statusChart.series.push(new am4charts.ColumnSeries());
      series.dataFields.valueX = "count";
      series.dataFields.categoryY = "status";
      series.name = "Tickets";
      series.columns.template.tooltipText = "{categoryY}: [bold]{valueX}[/] tickets";


      // Color the columns
      series.columns.template.adapter.add("fill", function(fill, target) {
          switch(target.dataItem.categoryY) {
              case "Open":
                  return am4core.color("#36A3F7");  
              case "Resolved":
                  return am4core.color("#4ECDC4"); 
              case "Completed":
                  return am4core.color("#2CA189");  
              case "In Progress":
                return am4core.color("#FFB822");
              case "Cancelled":
                return am4core.color("#F4516C");
              default:
                  return fill;
          }
      });
    },
    getGraphDataCategory() {
      const vm = this;
      $.ajax({
        url: baseUrl("ticket/ticket/get_all_category/"),
        type: "POST",
        global: false,
        data: {
          csrf_token: _csrf_hash,
        },
        dataType: "JSON",
        success: function(data) {
          vm.categories = Object.assign({}, data.categories);
          vm.loadGraphCategory(vm.categories);
        }
      });
    },
    loadGraphCategory(data){
      if (categoryChart != null) {
        categoryChart.dispose();
      }
      am4core.useTheme(am4themes_animated);
      let chartData = Object.entries(data).map(([key, value]) => ({
          status: key.charAt(0).toUpperCase() + key.slice(1),
          count: parseInt(value)
      })).sort((a, b) => b.count - a.count);

      categoryChart = am4core.create("type_graph", am4charts.XYChart);
      categoryChart.data = chartData;
      let categoryAxis = categoryChart.yAxes.push(new am4charts.CategoryAxis());
      categoryAxis.dataFields.category = "status";
      categoryAxis.renderer.grid.template.location = 0;
      categoryAxis.renderer.minGridDistance = 30;
      categoryAxis.renderer.labels.template.dy = 10;

      let valueAxis = categoryChart.xAxes.push(new am4charts.ValueAxis());
      valueAxis.title.text = "Number of Tickets";

      let series = categoryChart.series.push(new am4charts.ColumnSeries());
      series.dataFields.valueX = "count";
      series.dataFields.categoryY = "status";
      series.name = "Tickets";
      series.columns.template.tooltipText = "{categoryY}: [bold]{valueX}[/] tickets";


      // Color the columns
      series.columns.template.adapter.add("fill", function(fill, target) {
        switch(target.dataItem.categoryY) {
              case "Hardware":
                  return am4core.color("#845EC2");
              case "Outlook":
                  return am4core.color("#D65DB1");
              case "Completed":
                  return am4core.color("#FF6F91");
              case "Payroll":
                  return am4core.color("#FF9671");
              case "Website":
                  return am4core.color("#4ECDC4");
              case "Webportal":
                  return am4core.color("#FFC75F");
              default:
                  return fill;
          }
        });
    },
    getGraphDataPriority(){
      const vm = this;
      $.ajax({
        url: baseUrl("ticket/ticket/get_all_priority/"),
        type: "POST",
        global: false,
        data: {
          csrf_token: _csrf_hash,
        },
        dataType: "JSON",
        success: function(data) {
          vm.priorities = Object.assign({}, data.priorities);
          vm.loadGraphPriority(vm.priorities);
        }
      });
    },
    loadGraphPriority(data){
      if (priorityChart != null) {
        priorityChart.dispose();
      }

      let priorityData = Object.entries(data).map(([key, value]) => ({
        priority: key.charAt(0).toUpperCase() + key.slice(1),
        value: parseInt(value),
        color: am4core.color(
          key === 'High' ? '#F4516C' : 
          key === 'Medium' ? '#FFC75F' : 
          key === 'Low' ? '#34BFA3' : '#34BFA3')
      }));

      priorityChart = am4core.create("priority_chart", am4charts.PieChart);

      const totalValue = priorityData.reduce((sum, item) => sum + item.value, 0);
      if (totalValue === 0) {
          priorityChart.data = [{
              priority: "No Data",
              value: 1000,
              disabled: true,
              color: am4core.color("#dadada"),
              opacity: 0.3,
              strokeDasharray: "4,4",
              tooltip: ""
          }];
      } else {
          priorityChart.data = priorityData;
      }
      
      let pieSeries = priorityChart.series.push(new am4charts.PieSeries());
      pieSeries.dataFields.value = "value";
      pieSeries.dataFields.category = "priority";
      pieSeries.slices.template.propertyFields.fill = "color";
      priorityChart.legend = new am4charts.Legend();
    },
    getGraphDataAssignee(){
      const vm = this;
      $.ajax({
        url: baseUrl("ticket/ticket/get_total_assignee/"),
        type: "POST",
        global: false,
        data: {
          csrf_token: _csrf_hash,
        },
        dataType: "JSON",
        success: function(data) {
          vm.assignee = Object.assign({}, data.assigned);
          vm.loadGraphAssignee(vm.assignee);
        }
      });
    },
    loadGraphAssignee(data) {
      if (assigneeChart != null) {
          assigneeChart.dispose();
      }
  
      const chartData = Object.keys(data).map(employee => {
          return {
              name: employee, 
              open: parseInt(data[employee].open), 
              completed: parseInt(data[employee].completed), 
              in_progress: parseInt(data[employee]["in_progress"]), 
              total: parseInt(data[employee].total)
          };
      }).sort((a, b) => a.total - b.total);

  
      am4core.useTheme(am4themes_animated);
      assigneeChart = am4core.create("assignee_chart", am4charts.XYChart);
  
      assigneeChart.data = chartData;
      
      let categoryAxis = assigneeChart.yAxes.push(new am4charts.CategoryAxis());
      categoryAxis.dataFields.category = "name"; 
      categoryAxis.renderer.grid.template.location = 0;
      categoryAxis.renderer.inversed = true; 
  
      let valueAxis = assigneeChart.xAxes.push(new am4charts.ValueAxis());
      valueAxis.renderer.opposite = true; 
  
      const statusColors = {
          "Open": am4core.color("#716ACA"),
          "Completed": am4core.color("#2CA189"),
          "In Progress": am4core.color("#00C5DC")
      };
  
      const statuses = [
          { key: "completed", label: "Completed" },
          { key: "open", label: "Open" },
          { key: "in_progress", label: "In Progress" },
      ];
  
      statuses.forEach(status => {
          let series = assigneeChart.series.push(new am4charts.ColumnSeries());
          series.dataFields.categoryY = "name";
          series.dataFields.valueX = status.key;
          series.name = status.label; 
          series.stacked = true; 

          series.columns.template.fill = statusColors[status.label];
  
          series.columns.template.tooltipText = `${status.label}: [bold]{valueX}[/]`;
      });
  
      assigneeChart.legend = new am4charts.Legend();  
    },
    getGraphCompletion(){
      const vm = this;
      $.ajax({
        url: baseUrl("ticket/ticket/get_completion_rate/"),
        type: "POST",
        global: false,
        data: {
          csrf_token: _csrf_hash,
        },
        dataType: "JSON",
        success: function(data) {
          vm.completion = data;
          vm.loadGraphCompletion(vm.completion);
        }
      });
    },
    loadGraphCompletion(data){
      am4core.useTheme(am4themes_animated);
      completionChart = am4core.create("completion_chart", am4charts.GaugeChart);
      completionChart.innerRadius = 70;
      let axis = completionChart.xAxes.push(new am4charts.ValueAxis());
      axis.min = 0;
      axis.max = 100;
      axis.strictMinMax = true;

      let range = axis.axisRanges.create();
      range.value = 0;
      range.endValue = 50;
      range.axisFill.fillOpacity = 1;
      range.axisFill.fill = am4core.color("#F4516C");
      range.axisFill.zIndex = -1;

      let range2 = axis.axisRanges.create();
      range2.value = 50;
      range2.endValue = 80;
      range2.axisFill.fillOpacity = 1;
      range2.axisFill.fill = am4core.color("#FAC35D");
      range2.axisFill.zIndex = -1;

      let range3 = axis.axisRanges.create();
      range3.value = 80;
      range3.endValue = 100;
      range3.axisFill.fillOpacity = 1;
      range3.axisFill.fill = am4core.color("#34BFA3");
      range3.axisFill.zIndex = -1;

      chartHand = completionChart.hands.push(new am4charts.ClockHand());
      chartHand.axis = axis;
      chartHand.innerRadius = am4core.percent(37);
      chartHand.startWidth = 3;
      chartHand.endWidth = 8;
      chartHand.value = data;
      chartHand.pin.disabled = true;
      
      chartLabel = completionChart.radarContainer.createChild(am4core.Label);
      chartLabel.isMeasured = false;
      chartLabel.fontSize = 30;
      chartLabel.x = am4core.percent(50);
      chartLabel.y = am4core.percent(100);
      chartLabel.horizontalCenter = "middle";
      chartLabel.verticalCenter = "bottom";
      chartLabel.text = chartHand.value + '%';
      
      // Axis labels
      let label0 = completionChart.radarContainer.createChild(am4core.Label);
      label0.isMeasured = false;
      label0.y = 10;
      label0.horizontalCenter = "middle";
      label0.verticalCenter = "top";
      label0.text = "Unacceptable";

      label0.adapter.add("x", function(x, target){
        return -(axis.renderer.pixelInnerRadius + (axis.renderer.pixelRadius - axis.renderer.pixelInnerRadius) / 2);
      });

      let label1 = completionChart.radarContainer.createChild(am4core.Label);
      label1.isMeasured = false;
      label1.y = 10;
      label1.horizontalCenter = "middle";
      label1.verticalCenter = "top";
      label1.text = "Very Good";

      label1.adapter.add("x", function(x, target){
        return (axis.renderer.pixelInnerRadius + (axis.renderer.pixelRadius - axis.renderer.pixelInnerRadius) / 2);
      });
    }
  }
});

const dateRangeConfig = {
  singleDatePicker: false,
  showDropdowns: true,
  autoUpdateInput: false,
  maxDate: moment(),
  locale: {
      format: 'YYYY-MM-DD',
      cancelLabel: 'Show All'
  },
  ranges: {
      'Today': [moment(), moment()],
      'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Last 7 Days': [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()]
  }
};


const makeAveResolveTimeRequest = (data) => {
  return $.ajax({
      url: baseUrl("ticket/ticket/get_average_resolve_time/"),
      type: "POST",
      dataType: "JSON",
      global: false,
      data: {
          csrf_token: _csrf_hash,
          ...data
      }
  });
};

const handleAveResolveTimeResponse = (data) => {
  ticketDataSheet.widget.aveResolve = data === 0 ? 
      "NONE" : 
      data;
};

const handleAveResolveTimeCancel = () => {
  ticketDataSheet.aveResolveRange = "All Time ";
  makeAveResolveTimeRequest({ all: true })
      .done(handleAveResolveTimeResponse);
};

const handleAveResolveTimeApply = (ev, picker) => {
  const startDate = picker.startDate.format('YYYY-MM-DD');
  const endDate = picker.endDate.format('YYYY-MM-DD');

  if (startDate === moment().format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
    ticketDataSheet.aveResolveRange = "Today";
  } else if (startDate === moment().subtract(6, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
    ticketDataSheet.aveResolveRange = "Last 7 Days";
  } else if (startDate === moment().subtract(29, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
    ticketDataSheet.aveResolveRange = "Last 30 Days";
  } else if (startDate === moment().subtract(1, 'days').format('YYYY-MM-DD') && endDate === moment().subtract(1, 'days').format('YYYY-MM-DD')) {
    ticketDataSheet.aveResolveRange = "Yesterday";
  } else {
    ticketDataSheet.aveResolveRange = `From: ${picker.startDate.format('MMM D, YYYY')} - To: ${picker.endDate.format('MMM D, YYYY')}`;
  }
  makeAveResolveTimeRequest({ start: startDate, end: endDate })
      .done(handleAveResolveTimeResponse);
};

$('#aveResolveTicketPicker').daterangepicker(dateRangeConfig)
.on('cancel.daterangepicker', handleAveResolveTimeCancel)
.on('apply.daterangepicker', handleAveResolveTimeApply);


// /////////
const makeAveResponseTimeRequest = (data) => {
  return $.ajax({
      url: baseUrl("ticket/ticket/get_average_response_time/"),
      type: "POST",
      dataType: "JSON",
      global: false,
      data: {
          csrf_token: _csrf_hash,
          ...data
      }
  });
};

const handleAveResponseTimeResponse = (data) => {
  ticketDataSheet.widget.aveResponse = data === 0 ? 
      "NONE" : 
      data;
};

const handleAveResponseTimeCancel = () => {
  ticketDataSheet.aveResponseRange = "All Time ";
  makeAveResponseTimeRequest({ all: true })
      .done(handleAveResponseTimeResponse);
};

const handleAveResponseTimeApply = (ev, picker) => {
  const startDate = picker.startDate.format('YYYY-MM-DD');
  const endDate = picker.endDate.format('YYYY-MM-DD');

  if (startDate === moment().format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
    ticketDataSheet.aveResponseRange = "Today";
  } else if (startDate === moment().subtract(6, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
    ticketDataSheet.aveResponseRange = "Last 7 Days";
  } else if (startDate === moment().subtract(29, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
    ticketDataSheet.aveResponseRange = "Last 30 Days";
  } else if (startDate === moment().subtract(1, 'days').format('YYYY-MM-DD') && endDate === moment().subtract(1, 'days').format('YYYY-MM-DD')) {
    ticketDataSheet.aveResponseRange = "Yesterday";
  } else {
    ticketDataSheet.aveResponseRange = `From: ${picker.startDate.format('MMM D, YYYY')} - To: ${picker.endDate.format('MMM D, YYYY')}`;
  }
  makeAveResponseTimeRequest({ start: startDate, end: endDate })
      .done(handleAveResponseTimeResponse);
};

$('#aveResponseTicketPicker').daterangepicker(dateRangeConfig)
.on('cancel.daterangepicker', handleAveResponseTimeCancel)
.on('apply.daterangepicker', handleAveResponseTimeApply);

const makeOpenTicketRequest = (data) => {
  return $.ajax({
      url: baseUrl("ticket/ticket/get_open_tickets/"),
      type: "POST",
      dataType: "JSON",
      global: false,
      data: {
          csrf_token: _csrf_hash,
          ...data
      }
  });
};

const handleOpenTicketResponse = (data) => {
  ticketDataSheet.widget.open = data === 0 ? 
      "NONE" : 
      data;
};

const handleOpenTicketCancel = () => {
  ticketDataSheet.openTicketRange = "All Time ";
  makeOpenTicketRequest({ all: true })
      .done(handleOpenTicketResponse);
};

const handleOpenTicketApply = (ev, picker) => {
  const startDate = picker.startDate.format('YYYY-MM-DD');
  const endDate = picker.endDate.format('YYYY-MM-DD');

  if (startDate === moment().format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
    ticketDataSheet.openTicketRange = "Today";
  } else if (startDate === moment().subtract(6, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
    ticketDataSheet.openTicketRange = "Last 7 Days";
  } else if (startDate === moment().subtract(29, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
    ticketDataSheet.openTicketRange = "Last 30 Days";
  } else if (startDate === moment().subtract(1, 'days').format('YYYY-MM-DD') && endDate === moment().subtract(1, 'days').format('YYYY-MM-DD')) {
    ticketDataSheet.openTicketRange = "Yesterday";
  } else {
    // Custom range
    ticketDataSheet.openTicketRange = `From: ${picker.startDate.format('MMM D, YYYY')} - To: ${picker.endDate.format('MMM D, YYYY')}`;
  }
  makeOpenTicketRequest({ start: startDate, end: endDate })
      .done(handleOpenTicketResponse);
};

$('#openTicketPicker').daterangepicker(dateRangeConfig)
.on('cancel.daterangepicker', handleOpenTicketCancel)
.on('apply.daterangepicker', handleOpenTicketApply);

const makeUrgentTicketRequest = (data) => {
  return $.ajax({
      url: baseUrl("ticket/ticket/get_urgent_tickets/"),
      type: "POST",
      dataType: "JSON",
      global: false,
      data: {
          csrf_token: _csrf_hash,
          ...data
      }
  });
};

const handleUrgentTicketResponse = (data) => {
  ticketDataSheet.widget.urgent = data === 0 ? 
      "NONE" : 
      data;
};

const handleUrgentTicketCancel = () => {
  ticketDataSheet.urgentTicketRange = "All Time ";
  makeUrgentTicketRequest({ all: true })
      .done(handleUrgentTicketResponse);
};

const handleUrgentTicketApply = (ev, picker) => {
  const startDate = picker.startDate.format('YYYY-MM-DD');
  const endDate = picker.endDate.format('YYYY-MM-DD');

  if (startDate === moment().format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
    ticketDataSheet.urgentTicketRange = "Today";
  } else if (startDate === moment().subtract(6, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
    ticketDataSheet.urgentTicketRange = "Last 7 Days";
  } else if (startDate === moment().subtract(29, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
    ticketDataSheet.urgentTicketRange = "Last 30 Days";
  } else if (startDate === moment().subtract(1, 'days').format('YYYY-MM-DD') && endDate === moment().subtract(1, 'days').format('YYYY-MM-DD')) {
    ticketDataSheet.urgentTicketRange = "Yesterday";
  } else {
    // Custom range
    ticketDataSheet.urgentTicketRange = `From: ${picker.startDate.format('MMM D, YYYY')} - To: ${picker.endDate.format('MMM D, YYYY')}`;
  }
  
  makeUrgentTicketRequest({ start: startDate, end: endDate })
      .done(handleUrgentTicketResponse);
};

$('#urgentTicketPicker').daterangepicker(dateRangeConfig)
.on('cancel.daterangepicker', handleUrgentTicketCancel)
.on('apply.daterangepicker', handleUrgentTicketApply);

const makeTotalTicketRequest = (data) => {
  return $.ajax({
      url: baseUrl("ticket/ticket/get_total_tickets/"), 
      type: "POST",
      dataType: "JSON",
      global: false,
      data: {
          csrf_token: _csrf_hash,
          ...data
      }
  });
};

const handleTotalTicketResponse = (data) => {
  ticketDataSheet.widget.total = data === 0 ? 
      "NONE" : 
      data;
};

const handleTotalCancel = () => {
  ticketDataSheet.totalTicketRange = "All Time ";
  makeTotalTicketRequest({ all: true })
      .done(handleTotalTicketResponse);
};

const handleTotalApply = (ev, picker) => {
  const startDate = picker.startDate.format('YYYY-MM-DD');
  const endDate = picker.endDate.format('YYYY-MM-DD');
  
  if (startDate === moment().format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
    ticketDataSheet.totalTicketRange = "Today";
  } else if (startDate === moment().subtract(6, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
    ticketDataSheet.totalTicketRange = "Last 7 Days";
  } else if (startDate === moment().subtract(29, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
    ticketDataSheet.totalTicketRange = "Last 30 Days";
  } else if (startDate === moment().subtract(1, 'days').format('YYYY-MM-DD') && endDate === moment().subtract(1, 'days').format('YYYY-MM-DD')) {
    ticketDataSheet.totalTicketRange = "Yesterday";
  } else {
    // Custom range
    ticketDataSheet.totalTicketRange = `From: ${picker.startDate.format('MMM D, YYYY')} - To: ${picker.endDate.format('MMM D, YYYY')}`;
  }

  makeTotalTicketRequest({ start: startDate, end: endDate })
      .done(handleTotalTicketResponse);
};

$('#totalTicketPicker')
  .daterangepicker(dateRangeConfig)
  .on('cancel.daterangepicker', handleTotalCancel)
  .on('apply.daterangepicker', handleTotalApply);


const makeTicketStatusRequest = (data) => {
    return $.ajax({
        url: baseUrl("ticket/ticket/get_all_status/"), 
        type: "POST",
        dataType: "JSON",
        global: false,
        data: {
            csrf_token: _csrf_hash,
            ...data
        }
    });
  };
  
  const handleTicketStatusResponse = (data) => {
    ticketDataSheet.loadGraphByStatus(data.status)
  };
  
  const handleStatusCancel = () => {
    ticketDataSheet.totalTicketByStatusRange = "All Time ";
    makeTicketStatusRequest({ all: true })
        .done(handleTicketStatusResponse);
  };
  
  const handleStatusApply = (ev, picker) => {
    const startDate = picker.startDate.format('YYYY-MM-DD');
    const endDate = picker.endDate.format('YYYY-MM-DD');

    if (startDate === moment().format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
      ticketDataSheet.totalTicketByStatusRange = "Today";
    } else if (startDate === moment().subtract(6, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
      ticketDataSheet.totalTicketByStatusRange = "Last 7 Days";
    } else if (startDate === moment().subtract(29, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
      ticketDataSheet.totalTicketByStatusRange = "Last 30 Days";
    } else if (startDate === moment().subtract(1, 'days').format('YYYY-MM-DD') && endDate === moment().subtract(1, 'days').format('YYYY-MM-DD')) {
      ticketDataSheet.totalTicketByStatusRange = "Yesterday";
    } else {
      // Custom range
      ticketDataSheet.totalTicketByStatusRange = `From: ${picker.startDate.format('MMM D, YYYY')} - To: ${picker.endDate.format('MMM D, YYYY')}`;
    }
    
    makeTicketStatusRequest({ start: startDate, end: endDate })
        .done(handleTicketStatusResponse);
  };
  
  $('#ticketStatusPicker')
    .daterangepicker(dateRangeConfig)
    .on('cancel.daterangepicker', handleStatusCancel)
    .on('apply.daterangepicker', handleStatusApply);


    const makeTicketCategoriesRequest = (data) => {
    return $.ajax({
        url: baseUrl("ticket/ticket/get_all_category/"), 
        type: "POST",
        dataType: "JSON",
        global: false,
        data: {
            csrf_token: _csrf_hash,
            ...data
        }
    });
  };
  
  const handleTicketCategoriesResponse = (data) => {
      ticketDataSheet.loadGraphCategory(data.categories)
  };
  
  const handleCategoriesCancel = () => {
    ticketDataSheet.totalTicketByCategoryRange = "All Time ";
    makeTicketCategoriesRequest({ all: true })
        .done(handleTicketCategoriesResponse);
  };
  
  const handleCategoriesApply = (ev, picker) => {
    const startDate = picker.startDate.format('YYYY-MM-DD');
    const endDate = picker.endDate.format('YYYY-MM-DD');

    if (startDate === moment().format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
      ticketDataSheet.totalTicketByCategoryRange = "Today";
    } else if (startDate === moment().subtract(6, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
      ticketDataSheet.totalTicketByCategoryRange = "Last 7 Days";
    } else if (startDate === moment().subtract(29, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
      ticketDataSheet.totalTicketByCategoryRange = "Last 30 Days";
    } else if (startDate === moment().subtract(1, 'days').format('YYYY-MM-DD') && endDate === moment().subtract(1, 'days').format('YYYY-MM-DD')) {
      ticketDataSheet.totalTicketByCategoryRange = "Yesterday";
    } else {
      // Custom range
      ticketDataSheet.totalTicketByCategoryRange = `From: ${picker.startDate.format('MMM D, YYYY')} - To: ${picker.endDate.format('MMM D, YYYY')}`;
    }

    makeTicketCategoriesRequest({ start: startDate, end: endDate })
        .done(handleTicketCategoriesResponse);
  };
  
  $('#ticketCategoryPicker')
    .daterangepicker(dateRangeConfig)
    .on('cancel.daterangepicker', handleCategoriesCancel)
    .on('apply.daterangepicker', handleCategoriesApply);


    const makeTicketPrioritiesRequest = (data) => {
      return $.ajax({
          url: baseUrl("ticket/ticket/get_all_priority/"), 
          type: "POST",
          dataType: "JSON",
          global: false,
          data: {
              csrf_token: _csrf_hash,
              ...data
          }
      });
    };
    
const handleTicketPrioritiesResponse = (data) => {
    const totalValue = Object.values(data.priorities).reduce((sum, value) => sum + parseInt(value), 0);
    if (totalValue == 0) {
      priorityChart.hide();
      priorityChart.dispose();

      am4core.useTheme(am4themes_animated);
      priorityChart = am4core.create("priority_chart", am4charts.PieChart);
      priorityChart.data = [{
        "priority": "Dummy",
        "disabled": true,
        "value": 1000,
        "color": am4core.color("#dadada"),
        "opacity": 0.3,
        "strokeDasharray": "4,4",
        "tooltip": "NO DATA"
      }];
      var series = priorityChart.series.push(new am4charts.PieSeries());
      series.dataFields.value = "value";
      series.dataFields.category = "priority";
      var slice = series.slices.template;
      slice.propertyFields.fill = "color";
      slice.propertyFields.fillOpacity = "opacity";
      slice.propertyFields.stroke = "color";
      slice.propertyFields.strokeDasharray = "strokeDasharray";
      slice.propertyFields.tooltipText = "tooltip";

      series.labels.template.propertyFields.disabled = "disabled";
      series.ticks.template.propertyFields.disabled = "disabled";
      priorityChart.appear();
    }else{
      if (priorityChart.data.length == 1){
          priorityChart.data = [];
          ticketDataSheet.loadGraphPriority(data.priorities); 
      }
      priorityChart.data.forEach(item => {
        item.value = parseInt(data.priorities[item.priority]);
      });
    }
    priorityChart.invalidateRawData();
};
    
    const handlePrioritiesCancel = () => {
      ticketDataSheet.totalTicketByPriorityRange = "All Time ";
      makeTicketPrioritiesRequest({ all: true })
          .done(handleTicketPrioritiesResponse);
    };
    
    const handlePrioritiesApply = (ev, picker) => {
      const startDate = picker.startDate.format('YYYY-MM-DD');
      const endDate = picker.endDate.format('YYYY-MM-DD');

      if (startDate === moment().format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        ticketDataSheet.totalTicketByPriorityRange = "Today";
      } else if (startDate === moment().subtract(6, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        ticketDataSheet.totalTicketByPriorityRange = "Last 7 Days";
      } else if (startDate === moment().subtract(29, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
        ticketDataSheet.totalTicketByPriorityRange = "Last 30 Days";
      } else if (startDate === moment().subtract(1, 'days').format('YYYY-MM-DD') && endDate === moment().subtract(1, 'days').format('YYYY-MM-DD')) {
        ticketDataSheet.totalTicketByPriorityRange = "Yesterday";
      } else {
        // Custom range
        ticketDataSheet.totalTicketByPriorityRange = `From: ${picker.startDate.format('MMM D, YYYY')} - To: ${picker.endDate.format('MMM D, YYYY')}`;
      }
      
      makeTicketPrioritiesRequest({ start: startDate, end: endDate })
          .done(handleTicketPrioritiesResponse);
    };
    
    $('#ticketPriorityPicker')
      .daterangepicker(dateRangeConfig)
      .on('cancel.daterangepicker', handlePrioritiesCancel)
      .on('apply.daterangepicker', handlePrioritiesApply);

      const makeTicketAssignedRequest = (data) => {
        return $.ajax({
            url: baseUrl("ticket/ticket/get_total_assignee/"), 
            type: "POST",
            dataType: "JSON",
            global: false,
            data: {
                csrf_token: _csrf_hash,
                ...data
            }
        });
      };
      
      const handleTicketAssignedResponse = (data) => {
          ticketDataSheet.loadGraphAssignee(data.assigned)
      };
      
      const handleAssignedCancel = () => {
        ticketDataSheet.totalTicketByAsigneeRange = "All Time ";
        makeTicketAssignedRequest({ all: true })
            .done(handleTicketAssignedResponse);
      };
      
      const handleAssignedApply = (ev, picker) => {
        const startDate = picker.startDate.format('YYYY-MM-DD');
        const endDate = picker.endDate.format('YYYY-MM-DD');

        if (startDate === moment().format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
          ticketDataSheet.totalTicketByAsigneeRange = "Today";
        } else if (startDate === moment().subtract(6, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
          ticketDataSheet.totalTicketByAsigneeRange = "Last 7 Days";
        } else if (startDate === moment().subtract(29, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
          ticketDataSheet.totalTicketByAsigneeRange = "Last 30 Days";
        } else if (startDate === moment().subtract(1, 'days').format('YYYY-MM-DD') && endDate === moment().subtract(1, 'days').format('YYYY-MM-DD')) {
          ticketDataSheet.totalTicketByAsigneeRange = "Yesterday";
        } else {
          // Custom range
          ticketDataSheet.totalTicketByAsigneeRange = `From: ${picker.startDate.format('MMM D, YYYY')} - To: ${picker.endDate.format('MMM D, YYYY')}`;
        }
        
        makeTicketAssignedRequest({ start: startDate, end: endDate })
            .done(handleTicketAssignedResponse);
      };
      
      $('#ticketAssigneePicker')
        .daterangepicker(dateRangeConfig)
        .on('cancel.daterangepicker', handleAssignedCancel)
        .on('apply.daterangepicker', handleAssignedApply);


        const makeTicketCompletionRequest = (data) => {
          return $.ajax({
              url: baseUrl("ticket/ticket/get_completion_rate/"), 
              type: "POST",
              dataType: "JSON",
              global: false,
              data: {
                  csrf_token: _csrf_hash,
                  ...data
              }
          });
        };
        
        const handleTicketCompletionResponse = (data) => {
          ticketDataSheet.completion = data;
          chartHand.showValue(data,500);
          chartLabel.text = data+"%";
        };
        
        const handleCompletionCancel = () => {
          ticketDataSheet.totalTicketCompletionRange = "All Time ";
          makeTicketCompletionRequest({ all: true })
              .done(handleTicketCompletionResponse);
        };
        
        const handleCompletionApply = (ev, picker) => {
          const startDate = picker.startDate.format('YYYY-MM-DD');
          const endDate = picker.endDate.format('YYYY-MM-DD');
          
          if (startDate === moment().format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
            ticketDataSheet.totalTicketCompletionRange = "Today";
          } else if (startDate === moment().subtract(6, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
            ticketDataSheet.totalTicketCompletionRange = "Last 7 Days";
          } else if (startDate === moment().subtract(29, 'days').format('YYYY-MM-DD') && endDate === moment().format('YYYY-MM-DD')) {
            ticketDataSheet.totalTicketCompletionRange = "Last 30 Days";
          } else if (startDate === moment().subtract(1, 'days').format('YYYY-MM-DD') && endDate === moment().subtract(1, 'days').format('YYYY-MM-DD')) {
            ticketDataSheet.totalTicketCompletionRange = "Yesterday";
          } else {
            // Custom range
            ticketDataSheet.totalTicketCompletionRange = `From: ${picker.startDate.format('MMM D, YYYY')} - To: ${picker.endDate.format('MMM D, YYYY')}`;
          }

          makeTicketCompletionRequest({ start: startDate, end: endDate })
              .done(handleTicketCompletionResponse);
        };
        
        $('#ticketCompletionPicker')
          .daterangepicker(dateRangeConfig)
          .on('cancel.daterangepicker', handleCompletionCancel)
          .on('apply.daterangepicker', handleCompletionApply);

  // $.ajax({
  //   url : baseUrl("ticket/ticket/all_status/"),
  //   type: "GET",
  //   dataType: "JSON",
  //   success: function(data){
  //     let dataCount = data.count;
  //     let statusIcon = "";
  //     let statusColor = "";
  //       $.each(data, function(i ,val){
  //         if(val.status == 'completed'){
  //           statusIcon = "fa-check-square-o";
  //           statusColor = "text-success";
  //         }else if(val.status == 'in progress'){
  //           statusIcon = "fa-refresh";
  //           statusColor = "text-warning";
  //         }else{
  //           statusIcon = "fa-edit";
  //           statusColor = "text-danger";
  //         }
  //         let status_list =   '<div class="m-widget4__item">'+
  //                               '<div class="m-widget4__ext">'+
  //                                 '<span class="m-widget4__icon m--font-brand">'+
  //                                   '<i class="fa '+statusIcon+' text-info"></i>'+
  //                                 '</span>'+
  //                               '</div>'+
  //                               '<div class="m-widget4__info">'+
  //                                 '<span class="m-widget4__text">'+val.status.toUpperCase()+'</span>'+
  //                               '</div>'+
  //                               '<div class="m-widget4__ext text-right">'+
  //                                 '<span class="m-widget4__number m--font-info">'+val.count+'</span>'+
  //                               '</div>'+
  //                             '</div>';
          

  //         $("#status_list").append(status_list);
  //       });
  //     }
  // });

  // $.ajax({
  //   url : baseUrl("ticket/ticket/all_category/"),
  //   type: "GET",
  //   dataType: "JSON",
  //   success: function(data){
  //     let dataCount = data.count;
  //     let categoryIcon = "";
  //       $.each(data, function(i ,val){
  //         if(val.category == 'hardware'){
  //           categoryIcon = "fa-cogs";
  //         }else if(val.category == 'software'){
  //           categoryIcon = "fa-desktop";
  //         }else{
  //           categoryIcon = "fa-globe";
  //         }
  //         let status_list =   '<div class="m-widget4__item">'+
  //                               '<div class="m-widget4__ext">'+
  //                                 '<span class="m-widget4__icon m--font-brand">'+
  //                                   '<i class="fa '+categoryIcon+' text-info"></i>'+
  //                                 '</span>'+
  //                               '</div>'+
  //                               '<div class="m-widget4__info">'+
  //                                 '<span class="m-widget4__text">'+val.category.toUpperCase()+'</span>'+
  //                               '</div>'+
  //                               '<div class="m-widget4__ext text-right">'+
  //                                 '<span class="m-widget4__number m--font-info">'+val.count+'</span>'+
  //                               '</div>'+
  //                             '</div>';
          

  //         $("#category").append(status_list);
  //       });
  //     }
  // });

  // $.ajax({
  //   url : baseUrl("ticket/ticket/all_sub_category/"),
  //   type: "GET",
  //   dataType: "JSON",
  //   success: function(data){
  //     let dataCount = data.count;
  //     let status_list = "";

  //     if(dataCount){
  //       $.each(data, function(i ,val){
  //         if(val.sub_category == "" || val.sub_category == 0){
  //           status_list = "";
  //         }else{
  //           status_list =   '<div class="m-widget4__item">'+
  //                               '<div class="m-widget4__ext">'+
  //                                 '<span class="m-widget4__icon m--font-brand">'+
  //                                   '<i class="fa fa-tag text-info"></i>'+
  //                                 '</span>'+
  //                               '</div>'+
  //                               '<div class="m-widget4__info">'+
  //                                 '<span class="m-widget4__text">'+val.sub_category.toUpperCase()+'</span>'+
  //                               '</div>'+
  //                               '<div class="m-widget4__ext text-right">'+
  //                                 '<span class="m-widget4__number m--font-info">'+val.count+'</span>'+
  //                               '</div>'+
  //                             '</div>';
          
  //         }
  //         $("#sub_category").append(status_list);
  //       });
  //     }else{
  //       $("#sub_category").text("No tickets for Webportal.");
  //     }
  //   }
  // });
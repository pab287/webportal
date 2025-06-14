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

am4core.ready(function () {
    fetchCUM();
    get_analytics(getCurrentDate(), getCurrentYear(), getCurrentMonth());
    fetchWaterUsage('all');
    $('#datePicker_subdivision').datepicker("setDate", getCurrentYear());
    $('#datePicker_total_payment').datepicker("setDate", getCurrentYear());
    $('#datePicker_top_consumer').datepicker("setDate", getCurrentYear());
    $('#datePicker_billing_graph').datepicker("setDate", getCurrentYear());
    $('#datePicker_consumer_vs_supplier').datepicker("setDate", getCurrentYear());
});

function get_analytics(current_date, filter_year, filter_month) {
    $.ajax({
        type: "POST",
        url: baseUrl('eforms/billing/dashboard_analytics/'),
        dataType: "JSON",
        data: { csrf_token: _csrf_hash, filter_year: filter_year, filter_month:filter_month, current_date:current_date },
        success: function (result) {
            am4core.ready(function () {
                am4core.useTheme(am4themes_animated);
                // result.map((item) => {
                //     item.color = getColor(item.status);
                // });

                $("#datePicker_billing_graph .selected-year").html(filter_year);

                var chart = am4core.create("chartdiv", am4charts.XYChart);

                var valueAxisGender = chart.yAxes.push(new am4charts.ValueAxis());
                valueAxisGender.dataFields.category = "count";
                valueAxisGender.renderer.minGridDistance = 100;
                
                var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                categoryAxis.dataFields.category = "status";
                categoryAxis.renderer.minGridDistance = 1;
                categoryAxis.renderer.cellStartLocation = 0.2
                categoryAxis.renderer.cellEndLocation = 0.8

                var series = chart.series.push(new am4charts.ColumnSeries());
                series.dataFields.categoryX = "status";
                series.dataFields.valueY = "count";
                series.columns.template.propertyFields.fill = "color";
                series.columns.template.tooltipText = "{valueY}";
                series.columns.template.column.stroke = am4core.color("#fff");
                series.columns.template.column.strokeOpacity = 0.2;
                
                series.columns.template.events.on("hit", function (e) {
                    const data = e.target.dataItem.dataContext;
                    const status = data.status;
                    
                    if(status == 'Upcoming Due'){
                        $('#m_graph_upcomingdue').modal('show');
                    } else if(status == 'Overdue'){
                        $('#m_graph_overdue').modal('show');
                    } else if(status == 'Bill Entries'){
                        $('#m_graph_entries').modal('show');
                    } else if(status == 'Disconnected'){
                        $('#m_graph_disconnected').modal('show');
                    } else if(status == 'Total Usage of\n Previous Reading'){
                        $('#m_graph_usage').modal('show');
                        $('.m_graph_title_usage').text(status);
                    }
                    
                }, this);

                chart.data = result;
            });
        }
    });
}

function get_analytics_lineGraph_top_payment(filter_year) {
    $.ajax({
        type: "GET",
        url: baseUrl('eforms/billing/dashboard_subdivision_data/'),
        dataType: "JSON",
        success: function (result) {
            am4core.ready(function () {
                am4core.useTheme(am4themes_animated);
                
                $("#datePicker_total_payment .selected-year").html(filter_year);
                var chart = am4core.create("chartdiv_total_payment", am4charts.XYChart);
                var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                categoryAxis.dataFields.category = "short";
                categoryAxis.renderer.labels.template.rotation = 300;

                chart.data = object_months;
                
                $.each(result, function(i,v){
                    addSeries(v.name,v.id);
                });

                function addSeries(subdivision_name, subdivision_id) {
                  $.ajax({
                    type: "POST",
                    url: baseUrl('eforms/billing/dashboard_linegraph_top_payment/'),
                    dataType: "JSON",
                    data: { csrf_token: _csrf_hash, subdivision_id: subdivision_id, filter_year: filter_year },
                    success: function (result_) {
                        
                        var seriesId = chart.series.length + 1;
                  
                        for(var i = 0; i < result_.length; i++) {
                            chart.data[i]["payment" + seriesId] = result_[i].total_payment;
                        }
                        
                        var series = new am4charts.LineSeries();
                        series.data = chart.data;
                        series.dataFields.valueY = "payment" + seriesId;
                        series.dataFields.categoryX = "short";
                        series.name = subdivision_name;
                        series.strokeWidth = 2;
                        series.minBulletDistance = 10;
                        series.propertyFields.dummyData = "breakdown";
                        series.tooltipText = "[bold]{name}:[/] [bold]₱ {valueY}[/]";
                        series.tooltip.pointerOrientation = "vertical";
                        series.tooltip.getFillFromObject = false;
                        series.tooltip.getStrokeFromObject = true;
                        series.tooltip.background.fill = am4core.color("#fff");
                        series.tooltip.background.strokeWidth = 2;
                        series.tooltip.label.fill = series.stroke;
                        series = chart.series.push(series);
                        }
                    });
                }

                chart.legend = new am4charts.Legend();
                chart.cursor = new am4charts.XYCursor();
            });
        }
    });
}

function get_analytics_top_consumer(filter_date) {
    const date_ = filter_date.split("-");
    const year = date_[0];
    const month = date_[1];

    $.ajax({
        type: "POST",
        url: baseUrl('eforms/billing/dashboard_analytics_top_consumer/'),
        dataType: "JSON",
        data: { csrf_token: _csrf_hash, year: year, month: month },
        success: function (result) {
            
            var monthOf = displayCurrentMonthName(month-1) ? displayCurrentMonthName(month-1) : "December";
            $(".title_top_consumer").html("Top Consumer of ("+monthOf+" - "+displayCurrentMonthName(month)+')');
            $("#datePicker_top_consumer .selected-year").html(year);
            
            if(result.length > 0){
                
                am4core.ready(function () {
                    am4core.useTheme(am4themes_animated);
                    result.map((item) => {
                        item.color = getColor(item.name);
                    });
    
                    var chart = am4core.create("chartdiv_total_usage_top_consumer", am4charts.XYChart);
                    var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
                    categoryAxis.dataFields.category = "name";
                    categoryAxis.renderer.inversed = true;
                    categoryAxis.renderer.minGridDistance = 1;
                    categoryAxis.renderer.cellStartLocation = 0.2;
                    categoryAxis.renderer.cellEndLocation = 0.8;
    
                    var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
    
                    var series = chart.series.push(new am4charts.ColumnSeries());
                    series.dataFields.valueX = "total_usage";
                    series.dataFields.categoryY = "name";
                    series.name = "Count";
                    series.columns.template.propertyFields.fill = "color";
                    series.columns.template.tooltipText = "{valueX}";
                    series.columns.template.column.stroke = am4core.color("#fff");
                    series.columns.template.column.strokeOpacity = 0.2;
    
                    chart.data = result;
                });

            } else {
                $("#chartdiv_total_usage_top_consumer").html("No data found...");
            }
        }
    });
}

function get_analytics_total_usage_per_subdivision(filter_year) {
    $.ajax({
        type: "GET",
        url: baseUrl('eforms/billing/dashboard_subdivision_data/'),
        dataType: "JSON",
        success: function (result) {
            am4core.ready(function () {
                am4core.useTheme(am4themes_animated);

                $("#datePicker_subdivision .selected-year").html(filter_year);
                var chart = am4core.create("chartdiv_total_usage_previous_per_subdivision", am4charts.XYChart);
                var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                categoryAxis.dataFields.category = "short";
                categoryAxis.renderer.labels.template.rotation = 300;

                chart.data = object_months;
                
                $.each(result, function(i,v){
                    addSeriesUsage(v.name,v.id);
                });

                function addSeriesUsage(subdivision_name, subdivision_id) {
                  $.ajax({
                    type: "POST",
                    url: baseUrl('eforms/billing/dashboard_linegraph_total_usage_per_subdivision/'),
                    dataType: "JSON",
                    data: { csrf_token: _csrf_hash, subdivision_id: subdivision_id, filter_year:filter_year },
                    success: function (result_) {
                        
                        var seriesId = chart.series.length + 1;
                  
                        for(var i = 0; i < result_.length; i++) {
                            chart.data[i]["usage" + seriesId] = result_[i].total_usage;
                        }
                        
                        var series = new am4charts.LineSeries();
                        series.data = chart.data;
                        series.dataFields.valueY = "usage" + seriesId;
                        series.dataFields.categoryX = "short";
                        series.name = subdivision_name;
                        series.strokeWidth = 3;
                        series.tensionX = 0.7;
                        series.fillOpacity = 0.2;
                        series.tooltipText = "[bold]{name}:[/] [bold #000]{valueY}[/]";
                        series.tooltip.pointerOrientation = "vertical";
                        series.tooltip.getFillFromObject = false;
                        series.tooltip.getStrokeFromObject = true;
                        series.tooltip.background.fill = am4core.color("#fff");
                        series.tooltip.background.strokeWidth = 2;
                        series.tooltip.label.fill = series.stroke;
                        series = chart.series.push(series);
                        }
                    });
                }

                chart.legend = new am4charts.Legend();
                chart.cursor = new am4charts.XYCursor();
            });
        }
    });
}

function get_consumer_vs_supplier_per_subdivision(filter_year, subdivision_id) {
    am4core.ready(function () {
        am4core.useTheme(am4themes_animated);

        $("#datePicker_consumer_vs_supplier .selected-year").html(filter_year);
        var chart = am4core.create("chartdiv_versus", am4charts.XYChart);
        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "short";
        categoryAxis.renderer.labels.template.rotation = 300;

        chart.data = object_months;
        
        addSeriesVS("Consumer", subdivision_id);
        addSeriesVS("Supplier", subdivision_id);

        function addSeriesVS(name, subdivision_id) {
          $.ajax({
            type: "POST",
            url: baseUrl('eforms/billing/dashboard_consumer_vs_supplier/'),
            dataType: "JSON",
            data: { csrf_token: _csrf_hash, subdivision_id: subdivision_id, name: name, filter_year:filter_year },
            success: function (result_) {
                
                var seriesId = chart.series.length + 1;
          
                for(var i = 0; i < result_.length; i++) {
                    chart.data[i]["vs" + seriesId] = result_[i].total_usage;
                }
                
                var series = new am4charts.LineSeries();
                series.data = chart.data;
                series.dataFields.valueY = "vs" + seriesId;
                series.dataFields.categoryX = "short";
                series.name = name;
                series.strokeWidth = 3;
                series.tensionX = 0.7;
                series.fillOpacity = 0.2;
                series.tooltipText = "[bold]{name}:[/] [bold #000]{valueY}[/]";
                series.tooltip.pointerOrientation = "vertical";
                series.tooltip.getFillFromObject = false;
                series.tooltip.getStrokeFromObject = true;
                series.tooltip.background.fill = am4core.color("#fff");
                series.tooltip.background.strokeWidth = 2;
                series.tooltip.label.fill = series.stroke;
                series = chart.series.push(series);
                }
            });
        }

        chart.legend = new am4charts.Legend();
        chart.cursor = new am4charts.XYCursor();
    });
}

function fetchWaterUsage(id){
    $.ajax({
        type: "POST",
        url: baseUrl('eforms/billing/dashboard_water_usage/'),
        dataType: "JSON",
        data: { csrf_token: _csrf_hash, id: id },
        success: function (result) {
            var monthOf = displayCurrentMonthName(result.current_month-1) ? displayCurrentMonthName(result.current_month-1) : "December";
            $('#monthly_usage').html(numberWithCommas(result.monthly));
            $('#monthly_usage_msg').html(monthOf+" - "+displayCurrentMonthName(result.current_month)+" (CBM)");
            $('#yearly_usage').html(numberWithCommas(result.yearly));
            $('#yearly_usage_msg').html("Year to date usage of "+result.current_year+" (CBM)");
        }
    });
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

var vm_waterUsage = new Vue({
    el: "#waterUsage",
    data: {row:{}, collection:{}, count:0},
    methods: {
        getUsagePerSubd:function(id){
            const _this = this;
            let elements = _this.$el;
            fetchWaterUsage(id);
        },
        getCollection:function(){
            const _this = this;
            $.ajax({
                type: "GET",
                url: baseUrl('eforms/billing/dashboard_water_usage_subdivision/'),
                dataType: "JSON",
                success: function (result) {
                    _this.count = result.length;
                    _this.collection = Object.assign({},result);
                }
            });
        }
    }
});
vm_waterUsage.getCollection();

var vm_waterUsage_cum = new Vue({
    el: "#waterUsage_cum",
    data: {row:{}, collection:{}, count:0},
    methods: {
        getUsagePerSubd:function(id){
            const _this = this;
            let elements = _this.$el;
            var date = document.getElementById("consumer_vs_supplier_date").value;
            get_consumer_vs_supplier_per_subdivision(date, id);
        },
        getCollection:function(){
            const _this = this;
            $.ajax({
                type: "GET",
                url: baseUrl('eforms/billing/dashboard_water_usage_subdivision/'),
                dataType: "JSON",
                success: function (result) {
                    _this.count = result.length;
                    _this.collection = Object.assign({},result);
                }
            });
        }
    }
});
vm_waterUsage_cum.getCollection();

function fetchCUM(){
    $.ajax({
        type: "POST",
        url: baseUrl('eforms/billing/dashboard_cum/'),
        dataType: "JSON",
        data: { csrf_token: _csrf_hash },
        success: function (result) {
            $('#customer_usage').html(numberWithCommas(result.customerUsage));
            $('#distribution_supply').html(numberWithCommas(result.distributionSupply));
        }
    });
}

$('#datePicker_subdivision').datepicker({
    format: 'yyyy',
    todayHighlight: true,
    autoclose: true,
    orientation: "bottom left",
    viewMode: "years",
    minViewMode: "years",
    endDate: getCurrentDate(),
});

$('#datePicker_total_payment').datepicker({
    format: 'yyyy',
    todayHighlight: true,
    autoclose: true,
    orientation: "bottom right",
    viewMode: "years",
    minViewMode: "years",
    endDate: getCurrentDate(),
});

$('#datePicker_billing_graph').datepicker({
    format: 'yyyy',
    todayHighlight: true,
    autoclose: true,
    orientation: "bottom left",
    viewMode: "years",
    minViewMode: "years",
    endDate: getCurrentDate(),
});


$('#datePicker_top_consumer').datepicker({
    format: 'yyyy-mm',
    todayHighlight: true,
    autoclose: true,
    orientation: "bottom right",
    viewMode: "months",
    minViewMode: "months",
    /**
     * Why endDate is set to next year December?
     * If the date picker is set to the current year,
     * it will not allow the user to select a month in the next year or next year.
     * resulting in an error when the user cant select the current year
     */
    endDate: (new Date().getFullYear() + 1) + '-12' // current year + 1, December
});

function getCurrentDate_top_consumer() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    return `${year}-${month}`;
}

$('#datePicker_consumer_vs_supplier').datepicker({
    format: 'yyyy',
    todayHighlight: true,
    autoclose: true,
    orientation: "bottom left",
    viewMode: "years",
    minViewMode: "years",
    endDate: getCurrentDate(),
});

$('#datePicker_total_payment').on('changeDate', function() {
    var date = $('#datePicker_total_payment').datepicker('getFormattedDate');
    get_analytics_lineGraph_top_payment(date);
});

$('#datePicker_billing_graph').on('changeDate', function() {
    var date = $('#datePicker_billing_graph').datepicker('getFormattedDate');
    get_analytics(getCurrentDate(), date, getCurrentMonth());
});

$('#datePicker_subdivision').on('changeDate', function() {
    var date = $('#datePicker_subdivision').datepicker('getFormattedDate');
    get_analytics_total_usage_per_subdivision(date);
});

$('#datePicker_top_consumer').on('changeDate', function() {
    var date = $('#datePicker_top_consumer').datepicker('getFormattedDate');
    get_analytics_top_consumer(date);
});

$('#datePicker_consumer_vs_supplier').on('changeDate', function() {
    var date = $('#datePicker_consumer_vs_supplier').datepicker('getFormattedDate');
    $("#consumer_vs_supplier_date").val(date);
    get_consumer_vs_supplier_per_subdivision(date, "all");
});

var searchGraphDisconnected ="";
$('#table-graph-disconnected').DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl('eforms/billing/get_disconnected_customer'),
        type: 'post',
        dataType: 'json',
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search.value = searchGraphDisconnected;
        }
    },
    searching: true,
    columns: [
        {
            data: 'accountno',
            width: '25%'
        },
        {
            data: 'customer_name',
            width: '25%'
        },
        {
            data: 'disconnect_date',
            width: '25%'
        },
    ],
    order: [
        ['0', 'asc']
    ]
});

var searchGraphEntries ="";
$('#table-graph-entries').DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl('eforms/billing/get_entries_bill'),
        type: 'post',
        dataType: 'json',
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search.value = searchGraphEntries;
        }
    },
    searching: true,
    columns: [
        {
            data: 'accountno',
            width: '25%'
        },
        {
            data: 'customer_name',
            width: '25%'
        },
        {
            data: 'ref_no',
            width: '25%'
        },
        {
            data: 'created_at',
            width: '25%'
        },
    ],
    order: [
        ['0', 'asc']
    ]
});

var searchGraphOverdue ="";
$('#table-graph-overdue').DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl('eforms/billing/get_overdue_bill'),
        type: 'post',
        dataType: 'json',
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search.value = searchGraphOverdue;
        }
    },
    searching: true,
    columns: [
        {
            data: 'accountno',
            width: '25%'
        },
        {
            data: 'customer_name',
            width: '25%'
        },
        {
            data: 'ref_no',
            width: '25%'
        },
        {
            data: 'due_date',
            width: '25%'
        },
    ],
    order: [
        ['0', 'asc']
    ]
});

var searchGraphOverdue ="";
$('#table-graph-usage').DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl('eforms/billing/get_usage_bill'),
        type: 'post',
        dataType: 'json',
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search.value = searchGraphOverdue;
        }
    },
    searching: true,
    columns: [
        {
            data: 'accountno',
            width: '25%'
        },
        {
            data: 'customer_name',
            width: '30%'
        },
        {
            data: 'ref_no',
            width: '30%'
        },
        {
            data: 'usage',
            width: '10%'
        },
    ],
    order: [
        ['0', 'asc']
    ]
});

var searchGraphOverdue ="";
$('#table-graph-upcomingdue').DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl('eforms/billing/get_upcomingdue_bill'),
        type: 'post',
        dataType: 'json',
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search.value = searchGraphOverdue;
        }
    },
    searching: true,
    columns: [
        {
            data: 'accountno',
            width: '25%'
        },
        {
            data: 'customer_name',
            width: '25%'
        },
        {
            data: 'ref_no',
            width: '25%'
        },
        {
            data: 'due_date',
            width: '25%'
        },
    ],
    order: [
        ['0', 'asc']
    ]
});
var searchDueForToday ="";

// function get_due_for_today() {
var due_today_table = $('#table-due-today').DataTable({
      dom: '<"toolbar">rtlip',
      serverSide: true,
      processing: true,
      aaSorting: [],
      ajax: {
          url: baseUrl('eforms/billing/get_due_for_today'),
          type: 'post',
          dataType: 'json',
          data: function (d) {
              d.csrf_token = _csrf_hash;
              d.search.value = searchDueForToday;
          }
      },
      // searching: true,
      columns: [
          {
            width: '1%',
            orderable: false,
            data: null,
            className: 'text-center',
            render: function (data, type, row) {
                return `<label class="m-checkbox m-checkbox-sm m-checkbox--state-primary" style="padding-bottom: 5px;" title='Check to Print'> <input id="chckBoxPayment" type="checkbox" class="text-gray chckBox" value="`+row.id+`" name="selected"><span></span></label>`;
            }
          },
          {
            width: '25%%',
            data: 'accountno',
            render: function (data, type, row) {
              return `<a href="`+baseUrl('eforms/billing/edit_account/'+row.id)+`">`+data+`</a>`;
          }
          },
          {
            width: '25%',
            data: 'total_charges',
            className: "text-right",
          },
          {
            width: '25%',
            data: 'due_date',
            className: "text-right",
          },
      ],
  });
// }

var disconnected_accounts = $('#table-disconnected-accounts').DataTable({
  dom: '<"toolbar">rtlip',
  serverSide: true,
  processing: true,
  aaSorting: [],
  ajax: {
      url: baseUrl('eforms/billing/get_disconnected_account'),
      type: 'post',
      dataType: 'json',
      data: function (d) {
          d.csrf_token = _csrf_hash;
      }
  },
  // searching: true,
  columns: [
      {
        data: 'customer_name',
        render: function (data, type, row) {
          return `<span style='font-size: 12px;'>`+data+`</span>`;
        }
      },
      {
        data: 'disconnect_date',
        render: function (data, type, row) {
          return `<span style='font-size: 12px;'>`+data+`</span>`;
        }
      },
  ],
});

$("#cb-select-all").click(function () {
  $('#table-due-today tbody input[type="checkbox"]').prop('checked', this.checked);
});

$("#table-due-today").on("click", "tbody input[type='checkbox']", function () {
  const allCheckboxes = $("#table-due-today tbody input[type='checkbox']").length;
  const checkedCheckboxes = $("#table-due-today tbody input[type='checkbox']:checked").length;
  const checked = allCheckboxes <= checkedCheckboxes;
  $('#cb-select-all').prop('checked', checked);
});



let overdue_accounts = [];
function disconnectModal(){
  $(".chckBox").each(function(){
    var trig = $(this).is(":checked");
    if(trig){
      overdue_accounts.push($(this).attr("value"));
    }
  });
  if(overdue_accounts.length > 0){
    $("#modal_disconnect").modal('show');
  }else{
    toastr.error("Please select an account.", "No account selected.");
  }
}

function disconnectSelect(){
  $.ajax({
      url: baseUrl("eforms/billing/disconnect_selected"),
      type: "POST",
      data:{overdue_accounts: overdue_accounts, csrf_token: _csrf_hash},
      success: function(response){
        let html = "";
        $.each(response, function (key, value) {
          html += value.toUpperCase()+"<br>";
        });
        toastr.success(html, "Water disconnected on the ff:");
        $("#modal_disconnect").modal('hide');
        due_today_table.ajax.reload();
        disconnected_accounts.ajax.reload();
      },
      error: function (request, status, error) {
        toastr.error("Error disconnecting water.", "Try again later.");
      }
  });
}

function disconnectAllModal(){
  $("#modal_disconnect_all").modal('show');
}

function disconnectAllOverdue(){
  $.ajax({
    url: baseUrl("eforms/billing/disconnect_selected"),
    type: "POST",
    data:{all: 'all', csrf_token: _csrf_hash},
    success: function(response){
      let html = "";
      $.each(response, function (key, value) {
        html += value.toUpperCase()+"<br>";
      });
      toastr.success(html, "Water disconnected on the ff:");
      $("#modal_disconnect").modal('hide');
      due_today_table.ajax.reload();
      disconnected_accounts.ajax.reload();
    },
    error: function (request, status, error) {
      toastr.error("Error disconnecting water.", "Try again later.");
    }
});
}

function itemDatatableActions(row){
	if(row){
    var _actionButton = "";
            _actionButton += "<a href='" + baseUrl('eforms/loa/view_loa?id=') + row.id + "' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditItem' target='__blank'><i class='la la-pencil-square'></i></a>";
        
        return _actionButton;
	}else{
    return false; 
  }
}

var object_months = [
    { num: 01, short: "Jan", long: "January" },
    { num: 02, short: "Feb", long: "February" }, 
    { num: 03, short: "Mar", long: "March" },          
    { num: 04, short: "Apr", long: "April" },
    { num: 05, short: "May", long: "May" },
    { num: 06, short: "Jun", long: "June" },
    { num: 07, short: "Jul", long: "July" },
    { num: 08, short: "Aug", long: "August" },
    { num: 09, short: "Sep", long: "September" },
    { num: 10, short: "Oct", long: "October" },
    { num: 11, short: "Nov", long: "November" },
    { num: 12, short: "Dec", long: "December" }
];

function getCurrentDate(){
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();
    return yyyy+'-'+mm+'-'+dd;
}

function getCurrentYear(){
    return new Date().getFullYear().toString();
}

function getCurrentMonth(){
    var today = new Date();
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    return mm;
}

function displayCurrentMonthName(month){
    for(var i=0; i<object_months.length; i++){
        if(object_months[i].num == month){
            return object_months[i].long;
        }
    }
}
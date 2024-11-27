var search_val = "";
  var tbl = $("#table-tickets").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
    processing: true,
    ajax: {
		url: baseUrl("ts/ticketing/get_recent_tickets/"),
		type: "post",
        dataType: "json",
        data: function(d){
			d.csrf_token = _csrf_hash,
			d.search['value'] = search_val
		}
    },
    searching: false,
    columns: [
        { data: "type"},
        { data: "name"},
        { data: "issue"},
        { data: "status",render: function (data) {return renderStatusHtml(data)}},
        { data: "need_dt"},
    ]
});

function formatCalendarDate(data){
    if(data=="0000-00-00 00:00:00"){
        return "";
    }
    else{
        return moment(data).format("MM/DD/YYYY");
    }
  
}

function renderStatusHtml(data){
  switch(data){
      case "Inprogress":
          return '<div class="m-badge m-badge--success m-badge--wide text-white" role="alert"><strong>Inprogress</strong></div>';
      break;
      case "Confirmed":
          return '<div class="m-badge m-badge--success m-badge--wide text-white" role="alert"><strong>Resolved</strong></div>';
      break;
      case "Closed":
          return '<div class="m-badge m-badge--accent m-badge--wide text-white" role="alert"><strong>Completed</strong></div>';
      break;
      case "Open":
          return '<div class="m-badge m-badge--warning m-badge--wide text-white" role="alert"><strong>Open</strong></div>';
      break;
      case "Onhold":
          return '<div class="m-badge m-badge--warning m-badge--wide text-white" role="alert"><strong>Onhold</strong></div>';
      break;
      default:
          return '<div class="m-badge m-badge--default m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
      break;
  }
}

//custom global search init
$('#generalSearch').donetyping(function(callback) {
	search_val = $(this).val();
    tblCashAdvance.ajax.reload();
    console.log(search_val);
});

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
	tblCashAdvance.ajax.reload();
});


$.ajax({
    url : baseUrl("ts/ticketing/pending_tickets/"),
    type: "GET",
    dataType: "JSON",
    success: function(data)
    {
        var percent = parseInt((data.c / data.ac) * 100);
        $("#pending").append(data.c);
        $("#progress_pending").css("width", percent + "%");
        $("#percent_pending").append(percent+"%");
    }
  });

  $.ajax({
    url : baseUrl("ts/ticketing/overdue_tickets/"),
    type: "GET",
    dataType: "JSON",
    success: function(data)
    {
        var percent = parseInt((data.c / data.ac) * 100);
        $("#overdue").append(data.c);
        $("#progress_overdue").css("width", percent + "%");
        $("#percent_overdue").append(percent+"%");
    }
  });

  $.ajax({
    url : baseUrl("ts/ticketing/closed_tickets/"),
    type: "GET",
    dataType: "JSON",
    success: function(data)
    {
        var percent = parseInt((data.c / data.ac) * 100);
        $("#inprogress").append(data.c);
        $("#progress_confirm").css("width", percent + "%");
        $("#percent_confirm").append(percent+"%");
    }
  });

  $.ajax({
    url : baseUrl("ts/ticketing/all_tickets/"),
    type: "GET",
    dataType: "JSON",
    success: function(data)
    {
        //var percent = parseInt((data.c / data.ac) * 100);
        $("#all").append(data.c);
        //$("#progress_all").css("width", percent + "%");
        //$("#percent_all").append(percent+"%");
    }
  });

  function chart_data(){
    $.ajax({
      url : baseUrl("ts/ticketing/chart_data/"),
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {
        am4core.ready(function() {  

        am4core.useTheme(am4themes_animated);
        var chart = am4core.create("chartdiv", am4charts.PieChart);
        var pieSeries = chart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "c";
        pieSeries.dataFields.category = "type";
        chart.innerRadius = am4core.percent(30);
        pieSeries.slices.template.stroke = am4core.color("#fff");
        pieSeries.slices.template.strokeWidth = 2;
        pieSeries.slices.template.strokeOpacity = 1;
        pieSeries.slices.template
          .cursorOverStyle = [
            {
              "property": "cursor",
              "value": "pointer"
            }
          ];  
        pieSeries.alignLabels = true;
        pieSeries.labels.template.bent = true;
        pieSeries.labels.template.radius = 3;
        pieSeries.labels.template.padding(0,0,0,0);
        pieSeries.ticks.template.disabled = true;
        var shadow = pieSeries.slices.template.filters.push(new am4core.DropShadowFilter);
        shadow.opacity = 0;
        var hoverState = pieSeries.slices.template.states.getKey("hover");
        var hoverShadow = hoverState.filters.push(new am4core.DropShadowFilter);
        hoverShadow.opacity = 0.7;
        hoverShadow.blur = 5;
        chart.legend = new am4charts.Legend();

        chart.data = [{
          "type": "Hardware Tickets",
          "c":  data['1'].c
        },{
          "type": "Software Tickets",
          "c":  data['3'].c
        },{
          "type": "Webportal Tickets",
          "c":  data['0'].c
        },{
          "type": "Outlook Tickets",
          "c":  data['2'].c
        },{
          "type": "Website Tickets",
          "c":  data['4'].c
        }];
        });  
      }
    });
  }
chart_data();
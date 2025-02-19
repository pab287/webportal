var search_val = "";
var status_val="";
var recruitment_val=""
var hired_date_val="";
var interview_date_val="";
var application_method_val ="";
let application_date_val="";
var order_val=[0,'DESC'];
var tblReport = $("#table-crs-report").DataTable({
  dom: 'Brtlip',
  serverSide: true,
  processing: true,
  ajax: {
      url: baseUrl("crs/get_crs_report"),
      type: "post",
      global: false,
      dataType: "json",
      data: function (d) {
          d.csrf_token = _csrf_hash;
          d.search['value'] = search_val;
          d.status = status_val;
          d.recruitment = recruitment_val;
          d.hired_date = hired_date_val;
          d.interview_date = interview_date_val;
          d.application_method = application_method_val;
          d.application_date = application_date_val;
      }
  },
  searching: false,
  order : order_val,
  columns: [
    {data: "id",visible: false},
    {data: "hired_dt",visible: false},
    {data: "interview_dt",visible: false},
    { data: "name", width: "13%" },
    {
        data: "school",
        width: "12%",
        render: function (data) {
            return formatTag(data);
        }
    }, {
        data: "course",
        width: "10%",
        render: function (data) {
            return formatTag(data);
        }
    }, {
        data: "position",
        width: "10%",
        render: function (data) {
            return formatTag(data);
        }
    }, {
        data: "tag1",
        width: "15%",
        render: function (data) {
            return formatTag(data);
        }
    }, {
        data: "recruitment",
        width: "10%"
    }, {
        data: "applied_dt",
        width: "10%"
    }
],
buttons: [ 
  {
    text: 'ADVANCE SEARCH',
    title: 'CRS REPORTS',
    className: 'btnAdvanceSearch',
    action: function ( e, dt, node, config ){
      $('#query_search').modal('show');
    }
},
  {
      extend: 'excelHtml5',
      text: 'EXCEL',
      title: 'CRS REPORTS',
      className: 'btnExcelAction',
      exportOptions: {
        columns: [3, 4, 5, 6, 7, 8, 9] // Adjust the indexes based on your actual column count
    },
    action: function (e, dt, button, config) {
      $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
      logexport(config.title);
  }
  },
  {
    extend: 'pdfHtml5',
    text: 'PDF',
    title: 'CRS REPORTS',
    className: 'btnPdfAction',
    orientation: 'landscape',
    pageSize: 'LEGAL',
    exportOptions: {
        columns: [3, 4, 5, 6, 7, 8, 9] // Adjust the indexes based on your actual column count
    },
    customize: function (doc) {
        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
        logexport("CRS - REPORTS PDF");
    },
    
}
],
initComplete: function (settings, json) {
  $(".btnAdvanceSearch").addClass("btn m-btn--square btn-primary text-white");
  $(".btnPdfAction").addClass("btn m-btn--square btn-warning text-white");
  $(".btnExcelAction").addClass("btn m-btn--square btn-info text-white");
}

});

function formatTag(data) {
  if (data.charAt(0) === ",") {
      return data.substr(1);
  } else {
      return data;
  }
}

$("#crs_report_status").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  allowClear: true
});

$("#crs_report_status").on("change", function(){
   status_val = $("#crs_report_status").val();
  if(status_val == 'hired'){
      $("#hired_dt").val("SELECT DATE");
      $(".hired_dt").removeClass('m--hide');
      $(".interview_dt").addClass('m--hide');
      order_val=[1,'DESC'];
      interview_date_val="";
  }
  else if(status_val == 'forinterview'){
    $("#interview_dt").val("SELECT DATE");
    $(".interview_dt").removeClass('m--hide');
    $(".hired_dt").addClass('m--hide');
    order_val=[2,'DESC'];
    hired_date_val="";
  }
  else{
      $(".hired_dt").addClass('m--hide');
      $(".interview_dt").addClass('m--hide');
      interview_date_val="";
      hired_date_val="";
      order_val=[0,'DESC'];
  }
  tblReport.order(order_val);
  tblReport.ajax.reload();
});

$("#crs_report_recruitment").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  allowClear: true
});

$("#crs_report_recruitment").on("change", function(){
  recruitment_val = $("#crs_report_recruitment").val();
  tblReport.ajax.reload();  
});

$("#application_method").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  allowClear: true
});

$("#application_method").on("change", function(){
  application_method_val = $("#application_method").val();
  tblReport.ajax.reload();
});

$('#hired_dt').daterangepicker({
  alwaysShowCalendars: true,
  todayHighlight: true,
  showDropdowns: true,
  autoclose: true,
  pickerPosition: 'center', 
  todayBtn: 'linked',
  format: 'yyyy/mm/dd',
  autoUpdateInput: false,
  minDate: new Date(2015, 0, 1),
  maxDate: new Date(new Date().getFullYear(), 11, 31),
  ranges: {
      'Today': [moment(), moment()],
      'Last 7 Days': [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'Last 365 Days': [moment().subtract(364, 'days'), moment()]
  },
  locale: {
      cancelLabel: 'Clear'  
  }
}).on('apply.daterangepicker', function(ev, picker) {
   ev.preventDefault();
   let startDate = picker.startDate.format('YYYY/MM/DD');
   let endDate = picker.endDate.format('YYYY/MM/DD');
   hired_date_val = startDate + ' - ' + endDate;
   $(this).val(hired_date_val);   
  tblReport.ajax.reload();
}).on('cancel.daterangepicker', function(ev, picker) {
  $(this).val('SELECT DATE');
  hired_date_val = "";
  tblReport.ajax.reload();
});

$('#interview_dt').daterangepicker({
  alwaysShowCalendars: true,
  todayHighlight: true,
  showDropdowns: true,
  autoclose: true,
  pickerPosition: 'center', 
  todayBtn: 'linked',
  format: 'yyyy/mm/dd',
  autoUpdateInput: false,
  minDate: new Date(2015, 0, 1),
  // maxDate: new Date(new Date().getFullYear(), 11, 31),
  ranges: {
      'Today': [moment(), moment()],
      'Last 7 Days': [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'Last 365 Days': [moment().subtract(364, 'days'), moment()]
  },
  locale: {
      cancelLabel: 'Clear'  
  }
}).on('apply.daterangepicker', function(ev, picker) {
   ev.preventDefault();
   let startDate = picker.startDate.format('YYYY/MM/DD');
   let endDate = picker.endDate.format('YYYY/MM/DD');
   interview_date_val = startDate + ' - ' + endDate;
   $(this).val(interview_date_val);   
  tblReport.ajax.reload();
}).on('cancel.daterangepicker', function(ev, picker) {
  $(this).val('SELECT DATE');
  interview_date_val = "";
  tblReport.ajax.reload();
});

$('#application_dt').daterangepicker({
  alwaysShowCalendars: true,
   todayHighlight: true,
   showDropdowns: true,
   autoclose: true,
   pickerPosition: 'center', 
   todayBtn: 'linked',
   format: 'yyyy/mm/dd',
   autoUpdateInput: false,
   minDate: new Date(2015, 0, 1),
   maxDate: new Date(new Date().getFullYear(), 11, 31),
   ranges: {
       'Today': [moment(), moment()],
       'Last 7 Days': [moment().subtract(6, 'days'), moment()],
       'Last 30 Days': [moment().subtract(29, 'days'), moment()],
       'Last 365 Days': [moment().subtract(364, 'days'), moment()]
   },
   locale: {
       cancelLabel: 'Clear'  
   }
}).on('apply.daterangepicker', function(ev, picker) {
    ev.preventDefault();
    let startDate = picker.startDate.format('YYYY/MM/DD');
    let endDate = picker.endDate.format('YYYY/MM/DD');
    application_date_val = startDate + ' - ' + endDate;
    $(this).val(application_date_val);   
   tblReport.ajax.reload();
}).on('cancel.daterangepicker', function(ev, picker) {
   $(this).val('SELECT DATE');
   application_date_val = "";
   tblReport.ajax.reload();
});

function logexport(type){
  $.ajax({
      url: baseUrl("crs/log_export/"),
      type: "post",
      global: false,
      dataType: "json",
      data: {
          csrf_token : _csrf_hash,
          type : type,
      },
  });
}

$('#generalSearch').donetyping(function (callback) {
  search_val = $(this).val();
  // if(search_val.length >= 3){
  //     $.ajax({
  //         url: baseUrl("crs/search_confirm_val"),
  //         type: "post",
  //         data: {
  //             csrf_token: _csrf_hash,
  //             search_val: search_val
  //         },
  //         success: function(resp){
  //             if(resp == true){
  //                 toastr.error("This user status is currently Blacklisted.", "Invalid Data!", 10000);
  //             }
              
  //         }
  //     });
  // }
  tblReport.ajax.reload();
  // tblResume.ajax.reload();
});
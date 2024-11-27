var search_val = "";
var query_builder = "";
var selected_date;

$("#date-picker").daterangepicker({
  buttonClasses: 'm-btn btn',
  applyClass: 'btn-primary',
  cancelClass: 'btn-secondary',
  locale: {
      format: 'MM/DD/YYYY'
  }
})
.on('apply.daterangepicker', function (ev, picker) {
  var tempStartDate = picker.startDate.format('MMM DD, YYYY');
  var tempEndDate = picker.endDate.format('MMM DD, YYYY');
  tempFormat = tempStartDate + ' - ' + tempEndDate;
  $("#date-range").val(tempFormat);
});

var total_amount;
var tbl_reports = $("#tbl-sales_collection").DataTable({
  dom: '<"toolbar">t',
  destroy: true,
  serverSide: true,
  processing: true,
  aaSorting: [],
   ajax: {
        url: baseUrl("eforms/billing/get_sales_report/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function(d){
           d.csrf_token = _csrf_hash,
           d.date = selected_date
       }
   },
   searching: true,
   columns: [
       { data: "payment_date", class: "text-center"},
       { data: "payment_ref", class: "text-center"},
       { data: "type", class: "text-center"},
       { data: "penalty", class: "text-right"},
       { data: "reconnection_fee", class: "text-right"},
   ],
   select: {
    style:    'os',
    selector: 'td:first-child'
   },
   buttons: [
    {
      // text: 'Print Table',
      title: '',
      extend: 'print',
      messageTop: function () {
        // return 'Hydra - Sales Collection | '+ tempFormat;

        var today = new Date();
        var hours = today.getHours();
        var minutes = today.getMinutes();
        var time = hours + ":" + (minutes < 10 ? "0": "") + minutes;
        var datetoday = moment().format('MM/D/YYYY');
        var timetoday = moment().format('h:mm A');
        var dateTimeToday = datetoday + " " + timetoday;

        var total_count = 0;
        var table = $('#tbl-sales_collection').DataTable();

        table.row().every(function() {
          var data = this.data();
          total_count += parseInt(data.total_count);
        });

        var html = `
        <div class="text-center">
          <h1 class="mb-3">GC&C Portal | Hydra - Sales Collection</h1>
          <p style="font-size: 18px;" class="mb-0">Total Number of Entries: ${total_count}</p>
          <p style="font-size: 18px;" class="mb-0">Generated as of: ${dateTimeToday}</p>
          <p style="font-size: 18px;" class="">Coverage Date: ${tempFormat}</p>
        </div>
        `;
        return html;
      },
      footer: true,
      exportOptions: {
        columns: ':visible',
      },
    },
    { 
        extend: 'csv',
        messageTop: function () {
          return 'Hydra - Sales Collection | '+ tempFormat;
        },
        messageBottom: function () {
          return 'Generated on '+ new Date();
        },
        footer: true,
        exportOptions: {
            columns: "thead th:not(.notExport)"
        }
    }, { 
        extend: 'excelHtml5',
        messageTop: function () {
          return 'Hydra - Sales Collection | '+ tempFormat;
        },
        messageBottom: function () {
          return 'Generated on '+ new Date();
        },
        footer: true,
        exportOptions: {
            columns: "thead th:not(.notExport)"
        }
    }, { 
        extend: 'pdfHtml5',
        title:  function() {
          return `GC&C Portal | Hydra - Sales Collection`;
        },
        messageTop: function () {
          // return 'Hydra - Sales Collection | '+ tempFormat;

          var today = new Date();
          var hours = today.getHours();
          var minutes = today.getMinutes();
          var time = hours + ":" + (minutes < 10 ? "0": "") + minutes;
          var datetoday = moment().format('MM/D/YYYY');
          var timetoday = moment().format('h:mm A');
          var dateTimeToday = datetoday + " " + timetoday;

          var total_count = 0;
          var table = $('#tbl-sales_collection').DataTable();

          table.row().every(function() {
            var data = this.data();
            total_count += parseInt(data.total_count);
          });

          var html = `Total Number of Entries: ${total_count}
                Generated as of: ${dateTimeToday}
                Coverage Date: ${tempFormat}`;
          return html;
        },
        messageBottom: function () {
          // return 'Generated on '+ new Date();
        },
        footer: true,
        exportOptions: {
          columns: [0, 1, 2, 3, 4]
        },
        customize: function(doc) {
          // Make all cell 100% width
          doc.content[2].table.widths = ['*', '*', '*', '*', '*'];

          doc.styles.message = {
            alignment: 'center',
            margin: [0, 0, -50, 0] // <-- not working
          }

          doc.defaultStyle.alignment = 'center';
          doc.defaultStyle.fontSize = 10; //<-- set fontsize to 16 instead of 10 
          doc.styles.tableHeader.fontSize = 10; //<-- set fontsize to 16 instead of 10 
        }  
    }
  ],
  "footerCallback": function ( row, data, start, end, display ) {
    var api = this.api(), data;
    var totalReconnection= api
        .column( 4 )
        .data()
        .reduce( function (a, b) {
            return parseFloat(a) + parseFloat(b);
        }, 0 );

    var totalPenalty= api
        .column( 3 )
        .data()
        .reduce( function (a, b) {
            return parseFloat(a) + parseFloat(b);
        }, 0 );

        
    total_amount = totalPenalty;
    $( api.column( 3 ).footer() ).html('<b>'+numberWithCommas(parseFloat(totalPenalty).toFixed(2))+'</b>');
    $( api.column( 4 ).footer() ).html('<b>'+numberWithCommas(parseFloat(totalReconnection).toFixed(2))+'</b>');
  },
});

var _customer_id = "", _selectedDate = "", _startDate = "", _endDate = "";
let tempRangeDates = {
  min_date: '',
  max_date: '',
};

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function generateReport(){
  selected_date = $("input[name='date_range']").val();
  tbl_reports.ajax.reload();
}

$("#ExportExcel").on("click", function() {
  if(parseFloat(total_amount) == 0){
    toastr.error("No data selected.", "Warning");
  }else{
    tbl_reports.button( '.buttons-excel' ).trigger();
    saveExportLogs('Accounts - Export Excel');
  }
});

$("#ExportCSV").on("click", function() {
  if(parseFloat(total_amount) == 0){
    toastr.error("No data selected.", "Warning");
  }else{
    tbl_reports.button( '.buttons-csv' ).trigger();
    saveExportLogs('Accounts - Export CSV');
  }
});

$("#ExportPDF").on("click", function() {
  if(parseFloat(total_amount) == 0){
    toastr.error("No data selected.", "Warning");
  }else{
    tbl_reports.button( '.buttons-pdf' ).trigger();
    saveExportLogs('Accounts - Export PDF');
  }
});

$("#ExportPrint").on("click", function() {
  tbl_reports.button( '.buttons-print' ).trigger();
  // if(parseFloat(total_amount) == 0){
  //   toastr.error("No data selected.", "Warning");
  // }else{
    // saveExportLogs('Accounts - Print');
  // }
});

function saveExportLogs(export_){
  $.ajax({
      url: baseUrl("eforms/billing/save_export_logs"),
      type: 'post',
      data: { csrf_token: _csrf_hash, export_: export_ },
      success: function (data) {
          
      }
  });
}
var selected_date;
let tempFormat;
$("#employee").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/billing/get_employee_collector"),
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

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

function numberWithCommas(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

var total_amount;
var render_datetime = moment();
var tbl_payment_collection = $("#tbl-payment_collection").DataTable({
  dom: '<"toolbar">t',
  destroy: true,
  serverSide: true,
  processing: true,
  aaSorting: [],
  ajax: {
       url: baseUrl("eforms/billing/get_payment_collection_report/"),
       type: "post",
       global: false,
       dataType: "json",
       data: function(d){
          d.csrf_token = _csrf_hash,
          d.id = $("#employee").val(),
          d.date = selected_date
      },
      error: function (xhr, error, code){
        console.log(error);
      }
  },
  columns: [
      {
        data: "account", orderable: false, className: "text-center",
      },
      { 
        data: "bill_ref", orderable: false, className: "text-center",
      },
      { 
        data: "acknowledgement_receipt", orderable: false, className: "text-center",
      },
      { 
        data: "payment_ref", orderable: false, className: "text-center"
      },
      { 
        data: "type", orderable: false, className: "text-center",
      },
      { data: "amount", orderable: false, className: "text-right", render: function (data){
        return parseFloat(data).toFixed(2);
      }
      },
      { 
        data: "created_date", className: "text-center",
      },
      { 
        data: "cashier", orderable: false, className: "text-center",
      },
  ],
  buttons: [
      { 
          extend: 'csv',
          messageTop: function () {
            return 'Hydra - Payment Collection | '+ tempFormat;
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
            return 'Hydra - Payment Collection | '+ tempFormat;
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
          title: function() {
            return `GC&C Portal | Hydra - Payment Collection`;
          },
          messageTop: function (data, type, row) {
            var today = new Date();
            var hours = today.getHours();
            var minutes = today.getMinutes();
            var time = hours + ":" + (minutes < 10 ? "0": "") + minutes;
            var datetoday = moment().format('MM/D/YYYY');
            var timetoday = moment().format('h:mm A');
            var dateTimeToday = datetoday + " " + timetoday;

            var total_count = 0;
            var table = $('#tbl-payment_collection').DataTable();

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
            columns: [0, 1, 2, 3, 4, 5, 6, 7]
          },
          customize: function(doc) {
            doc.styles.message = {
              alignment: 'center',
              margin: [0, 0, -50, 0] // <-- not working
            }
            doc.defaultStyle.fontSize = 10; //<-- set fontsize to 16 instead of 10 
            doc.styles.tableHeader.fontSize = 10; //<-- set fontsize to 16 instead of 10
          }
      }
  ],
  "footerCallback": function ( row, data, start, end, display ) {
    var api = this.api(), data;
    var totalPayment = api
        .column( 5 )
        .data()
        .reduce( function (a, b) {
            return parseFloat(a) + parseFloat(b);
        }, 0 );
        total_amount = totalPayment;
    $( api.column( 4 ).footer() ).html('<b>Total</b>');
    $( api.column( 5 ).footer() ).html('<b>'+numberWithCommas(parseFloat(totalPayment).toFixed(2))+'</b>');
  },
});

$("#ExportExcel").on("click", function() {
  if(parseFloat(total_amount) == 0){
    toastr.error("No data selected.", "Warning");
  }else{
    tbl_payment_collection.button( '.buttons-excel' ).trigger();
    saveExportLogs('Accounts - Export Excel');
  }
});

$("#ExportCSV").on("click", function() {
  if(parseFloat(total_amount) == 0){
    toastr.error("No data selected.", "Warning");
  }else{
    tbl_payment_collection.button( '.buttons-csv' ).trigger();
    saveExportLogs('Accounts - Export CSV');
  }
});

$("#ExportPDF").on("click", function() {
  if(parseFloat(total_amount) == 0){
    toastr.error("No data selected.", "Warning");
  }else{
    tbl_payment_collection.button( '.buttons-pdf' ).trigger();
    saveExportLogs('Accounts - Export PDF');
  }
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

function generateReport(){
  selected_date = $("input[name='date_range']").val();
  tbl_payment_collection.ajax.reload();
}
var selected_date;
let tempFormat;
$("#employee").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  minimumInputLength: 3,
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
  },
  endDate: moment(),
  maxDate: moment()
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

let total_amount = 0;
let total_balance_covered = 0;
const tbl_payment_collection = $("#tbl-payment_collection").DataTable({
  dom: '<"toolbar">t',
  destroy: true,
  serverSide: true,
  processing: true,
  aaSorting: [],
  scrollY: "440px",
  paging: false,
  scrollCollapse: true,
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
      { data: "account" },
      { data: "bill_ref" },
      { data: "acknowledgement_receipt" },
      { data: "payment_ref" },
      { data: "type" },
      { 
        data: "received_amount", render: function(data) {
            return parseFloat(data).toFixed(2);
        }
      },
      { 
        data: "balance_covered", render: function(data) {
            return parseFloat(data).toFixed(2);
        }
      },
      { 
        data: "payment_date", className: "text-center", render: function(data) {
            return data ? moment(data).format('MMM DD, YYYY') : '';
        }
      },
      { 
        data: "applied_payment_date", className: "text-center", render: function(data) {
            return data ? moment(data).format('MMM DD, YYYY') : '';
        }
      },
      { data: "cashier" },
  ],
  order: [[ 8, "desc" ]],
  columnDefs: [
    {
      targets: [0, 1, 2, 3, 4, 5, 6, 9],
      orderable: false,
    },
    {
      targets: [0, 1, 2, 3, 4, 7, 8, 9],
      className: "text-center",
    },
    {
      targets: [5, 6],
      className: "text-right",
    }
  ],
  createdRow: function( row, data, dataIndex ) {
      const is_archived = data.is_archived;

      if ( is_archived == 1 ) {
          $(row).addClass('table-danger');
      }
  },
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
          },
          customize: function(csv) {
              let data = csv.split('\n');

              let targetUppercase = [7, 8, 9]; // Columns to make uppercase
              let removeSpecialChar = []; // Remove special characters from these columns like peso sign
              let removeComma = []; // Column to remove commas

              // Loop through each row
              data = data.map((row, rowIndex) => {  
                  // Split row into columns, considering quoted fields
                  let columns = row.match(/(".*?"|[^",\s]+)(?=\s*,|\s*$)/g);

                  columns = columns.map((col, columnIndex) => {
                      col = col.trim(); // Remove extra spaces
              
                      if (rowIndex === 0) { 
                          return col.replace(/\b\w/g, char => char.toUpperCase());
                      }
              
                      // Convert to uppercase for specific columns
                      if (targetUppercase.includes(columnIndex)) {
                          col = col.toUpperCase();
                      }

                      // Remove special characters from specific columns
                      if (removeSpecialChar.includes(columnIndex)) {
                          col = col.replace(/[^\w\s.]/gi, '');
                      }
              
                        // Remove commas from specific columns
                      if (columnIndex === removeComma) {
                          col = col.replace(/,/g, '');
                      }
              
                      return col;
                  });

                  return columns.join(","); // Join modified columns
              });

              // Add UTF-8 BOM to the beginning of the CSV data for letter "ñ" to appear correctly
              const utf8BOM = '\uFEFF';
              return utf8BOM + data.join("\n"); // Reassemble CSV
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
          },
          customize: function(xlsx) {
            let sheet = xlsx.xl.worksheets['sheet1.xml'];

            // Convert Column to Uppercase
            $('row:not(:nth-child(2)) c[r^="H"], row:not(:nth-child(2)) c[r^="I"], row:not(:nth-child(2)) c[r^="J"]', sheet).each(function () {
                let cell = $(this).find('is t, v'); // Find the text inside
                let text = cell.text().trim(); // Get the existing text

                if (text) {
                    cell.text(text.toUpperCase()); // Convert to uppercase
                }
            });
          }
      }, { 
          extend: 'pdfHtml5',
          orientation: 'landscape',
          pageSize: 'LEGAL',
          title: function() {
            return `GC&C Portal | Hydra - Payment Collection`;
          },
          messageTop: function (data, type, row) {
            var total_count = 0;
            var table = $('#tbl-payment_collection').DataTable();

            table.row().every(function() {
              var data = this.data();
              total_count += parseInt(data.total_count);
            });

            var html = `Total Number of Entries: ${total_count}
                  Generated as of: ${moment().format('MMM DD, YYYY')}
                  Coverage Date: ${tempFormat}`;

            return html;
          },
          footer: true,
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
          },
          customize: function(doc) {
            doc.styles.message = {
              alignment: 'center',
            }

            doc.defaultStyle.fontSize = 9;
            doc.styles.tableHeader.fontSize = 9;
            doc.styles.tableFooter.fontSize = 9;

            // Set dynamic widths for all columns
            let columnWidths = new Array(doc.content[2].table.body[0].length).fill('*');

            // Define custom widths for specific columns (adjust index as needed)
            columnWidths[0] = '20%';
            columnWidths[5, 6] = '10%';
            columnWidths[9] = '15%';

            // Apply column widths
            doc.content[2].table.widths = columnWidths;

            // Loop through table body and target specific column
            doc.content[2].table.body.forEach(function (row, rowIndex) {
                if (rowIndex === 0) { return; }

                let targetUppercase = [0, 7, 8, 9]; // Columns to make uppercase
                let targetCenter = [0, 1, 2, 3, 4, 7, 8, 9]; // Columns to center align
                let targetRight = [5, 6]; // Column to right align

                row.forEach((cell, columnIndex) => {
                    // normalize if a plain string cell (robustness)
                    if (typeof cell === 'string') {
                      cell = { text: cell };
                      row[columnIndex] = cell;
                    }

                    if (!cell || cell.text === undefined) return;

                    // Uppercase if needed
                    if (targetUppercase.includes(columnIndex)) {
                      cell.text = String(cell.text).toUpperCase();
                    }

                    // Alignment: right takes precedence over center
                    if (targetRight.includes(columnIndex)) {
                      cell.alignment = 'right';
                    } else if (targetCenter.includes(columnIndex)) {
                      cell.alignment = 'center';
                    }
                });
            });
          }
      }
  ],
  "footerCallback": function ( row, data, start, end, display ) {
    var api = this.api(), data;

    // Total Payment
    const totalPayment = api
        .column( 5 )
        .data()
        .reduce( function (a, b) {
            return parseFloat(a) + parseFloat(b);
        }, 0 );
        total_amount = totalPayment;
    $( api.column( 4 ).footer() ).html('<b>Total</b>');
    $( api.column( 5 ).footer() ).html('<b>'+numberWithCommas(parseFloat(totalPayment).toFixed(2))+'</b>');

    // Total Balance Covered
    const totalBalanceCovered = api
        .column( 6 )
        .data()
        .reduce( function (a, b) {
            return parseFloat(a) + parseFloat(b);
        }, 0 );
    total_balance_covered = totalBalanceCovered;
    $( api.column( 6 ).footer() ).html('<b>'+numberWithCommas(parseFloat(total_balance_covered).toFixed(2))+'</b>');
  },
});

$("#ExportExcel").on("click", function() {
  var tbl_payment_collection_count = $('#tbl-payment_collection').DataTable().rows().count();
  if(tbl_payment_collection_count == 0) {
    toastr.error("No data selected.", "Warning");
  } else {
    tbl_payment_collection.button( '.buttons-excel' ).trigger();
    saveExportLogs('Accounts - Export Excel');
  }
});

$("#ExportCSV").on("click", function() {
  var tbl_payment_collection_count = $('#tbl-payment_collection').DataTable().rows().count();
  if(tbl_payment_collection_count == 0) {
    toastr.error("No data selected.", "Warning");
  } else {
    tbl_payment_collection.button( '.buttons-csv' ).trigger();
    saveExportLogs('Accounts - Export CSV');
  }
});

$("#ExportPDF").on("click", function() {
  var tbl_payment_collection_count = $('#tbl-payment_collection').DataTable().rows().count();
  if(tbl_payment_collection_count == 0) {
    toastr.error("No data selected.", "Warning");
  } else {
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
    const employee = $('#employee').val();
    const dateRange = $('#date-range').val();

    if (!employee || !dateRange) {
        toastr.error('Please select Employee and Date Range.', 'Input Required');
        return;
    }
    tbl_payment_collection.ajax.reload();
}
const initReadingStartDate = moment();
const initReadingEndDate = moment();
let selectedReadingStartDate = null;
let selectedReadingEndDate = null;

let search_val = "";
let query_builder = "";
const tblBillings = $("#table-billing").DataTable({
  dom: '<"toolbar">rtlip',
  serverSide: true, 
  processing: true,
  aaSorting: [],
  ajax: {
      url: baseUrl("eforms/billing/get_billing_collection/"),
      type: "post",
      global: false,
      dataType: "json",
      data: function(d) {
          d.csrf_token = _csrf_hash;
          d.search['value'] = search_val;
          d.query_builder = query_builder;

          if (selectedReadingStartDate && selectedReadingEndDate) {
              d.startDate = moment(selectedReadingStartDate).format("YYYY-MM-DD");
              d.endDate = moment(selectedReadingEndDate).format("YYYY-MM-DD");
          } else {
              d.startDate = '';
              d.endDate = '';
          }
      }
  },
  searching: true,
  columns: [
      { data: "checkbox"},
      { data: "ref_no", render: function (data) { return "<span class='m--font-boldest'>"+data+"</span>";} },
      { data: "reading_ref_no" },
      { data: "accountno"},
      { data: "name", orderable: false},
      { data: "meterno"},
      { data: "billing_period", orderable: false, className: "text-center"},
      { data: "due_date", className: "text-center"},
      { 
        data: "total_charges", className: "text-right", render: function (data) {
            return "<span class='m--font-boldest'>"+numberWithCommas(data)+"</span>";
        }
      },
      { data: "status", className: "text-center", render: function (data) {
            return renderStatusDue(data);
        }
      },
      { data: "print_count", width: "8%", className: "text-center", render: function (data) {
            return renderStatusPrint(data);
        }
      },
      { data: null, width: "5%", className: "text-center"},
  ],
  columnDefs: [
      {
          orderable: false,
          className: 'select-checkbox',
          targets: 0
      },
      {
        orderable: false,
        targets: [8, 9]
      },
      {
          data: null,
          defaultContent: "",
          targets: -1,
          orderable: false,

          render: function ( data, type, row, meta ) { return itemDatatableActions(row); },
      }, 
      {
          targets: "_all",
          className: "v-middle",
      }
  ],
  select: {
      style:    'multi',
      selector: 'td:first-child'
  },
  buttons: [
      { 
          extend: 'csv',
          exportOptions: {
              columns: "thead th:not(.notExport)"
          },
          // fieldBoundary: '',
          customize: function (csv) {
              let data = csv.split("\n"); // Split CSV into rows
              
              let targetUppercase = [1, 6]; // Columns to make uppercase
              let targetTotalCharges = 5;
              // Loop through each row
              data = data.map((row, rowIndex) => {
                  // Split row into columns, considering quoted fields
                  let columns = row.match(/(".*?"|[^",\s]+)(?=\s*,|\s*$)/g);
              
                  columns = columns.map((col, columnIndex) => {
                      col = col.trim(); // Remove extra spaces
              
                      if (rowIndex === 0) { 
                          return col.replace(/\b\w/g, char => char.toUpperCase());
                      }
              
                      if (targetUppercase.includes(columnIndex)) {
                          col = col.toUpperCase(); // Convert to uppercase
                      }
              
                      if (columnIndex === targetTotalCharges) {
                          col = col.replace(/,/g, ''); // Remove commas
                      }
              
                      return col;
                  });
              
                  return columns.join(","); // Join modified columns
              });

              return data.join("\n"); // Reassemble CSV
          }
      }, 
      { 
          extend: 'excel',
          exportOptions: {
              columns: "thead th:not(.notExport)"
          },
          customize: function (xlsx) {
              let sheet = xlsx.xl.worksheets['sheet1.xml'];

              // Convert Column B to Uppercase
              $('row:not(:nth-child(2)) c[r^="B"]', sheet).each(function () {
                  let cell = $(this).find('is t, v'); // Find the text inside
                  let text = cell.text().trim(); // Get the existing text

                  if (text) {
                      cell.text(text.toUpperCase()); // Convert to uppercase
                  }
              });
          }
      }, 
      {
          extend: 'pdf',
          exportOptions: {
              columns: "thead th:not(.notExport)"
          },
          orientation: 'landscape',
          pageSize: 'LEGAL',
          customize: function (doc) {
              // Set dynamic widths for all columns
              let columnWidths = new Array(doc.content[1].table.body[0].length).fill('*');

              // Define custom widths for specific columns (adjust index as needed)
              columnWidths[1] = '20%';

              // Apply column widths
              doc.content[1].table.widths = columnWidths;
              
              // Loop through table body and target specific column
              doc.content[1].table.body.forEach(function (row, rowIndex) {
                  if (rowIndex === 0) { return; } // Skip the header row

                  let targetUppercase = [1, 6]; // Columns to make uppercase
                  let targetCenter = [0, 2, 3, 4, 6]; // Columns to center align
                  let targetRight = 5; // Column to right align

                  row.forEach((cell, columnIndex) => {
                    if (!cell.text) { return; }

                    if (targetUppercase.includes(columnIndex)) {
                        cell.text = cell.text.toUpperCase();
                    }

                    if (targetCenter.includes(columnIndex)) {
                        cell.alignment = 'center';
                    }

                    if (columnIndex === targetRight) {
                        cell.alignment = 'right';
                    }
                  });
              });
          }
      },
  ],
  createdRow: function(row, data, dataIndex) {
      $(row).find('td').addClass('v-middle');
  }
});

$('#select_all_bills').on('change', function() {
  if (this.checked) {
    tblBillings.rows().select();
  } else {
    tblBillings.rows().deselect();
  }
});

tblBillings.on('select deselect', function() {
  if (tblBillings.rows({ selected: true }).count() !== tblBillings.rows().count()) {
      $('#select_all_bills').prop('checked', false);  
  } else {
      $('#select_all_bills').prop('checked', true);
  }
});

function numberWithCommas(x) {
  return parseFloat(x).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function renderStatusDue(data) {
  switch (data) {
      case "Paid":
          return '<div class="m-badge text-white m-badge--primary m-badge--wide" role="alert"><strong>Paid</strong></div>';
          break;
      case "Overdue":
          return '<div class="m-badge m-badge--danger m-badge--wide" role="alert"><strong>Overdue</strong></div>';
          break;
      case "Today due":
          return '<div class="m-badge text-white m-badge--warning m-badge--wide" role="alert"><strong>Today due</strong></div>';
          break;
      case "On going":
          return '<div class="m-badge text-white m-badge--accent m-badge--wide" role="alert"><strong>On going</strong></div>';
          break;
      default:
          return '';
          break;
  }
}

function renderStatusPrint(data) {
  if(data > 0){
    return '<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Printed</strong></div>';
  } else {
    return '<div class="m-badge m-badge--default m-badge--wide" role="alert"><strong>Not Printed</strong></div>';
  }
}

// =============== Billing Date Range Picker ===============

$('#billing-date-picker').daterangepicker({
  buttonClasses: 'm-btn btn',
  applyClass: 'btn-primary',
  cancelClass: 'btn-secondary',
  startDate: initReadingStartDate,
  endDate: initReadingEndDate,
  format: "MMM. DD, YYYY"
}, function (start, end, label) {
  selectedReadingStartDate = start;
  selectedReadingEndDate = end;

  let _label = "<strong>" + start.format("MMM. DD, YYYY") + "</strong> to <strong>" + end.format("MMM. DD, YYYY") + "</strong>";

  $(".selected-filter", $('#billing-date-picker')).html(_label);
  tblBillings.ajax.reload();
});

// =============== Billing Date Range Picker ===============
function itemDatatableActions(row) {
	if (row) {
    var tempHtml = "---";

    tempHtml = `<div class="dropdown">
                  <a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown"> 
                      <i class="la la-ellipsis-h"></i>
                  </a>

                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item " data-toggle='modal' data-target='#m_viewBill' href="javascript:void(0);" id='viewBill' data-id='`+row.id+`'><i class="la la-eye"></i>View</a>
                    <a class="dropdown-item" id="tbl-print" onclick="tblprint(`+row.id+`);" href="javascript:void(0);" data-id=''><i class="la la-print"></i> Print</a>
                  `;
                  
                  if (row.status != 'Paid') {
                    tempHtml += `<a class="dropdown-item " style="color: #FF8383;" href="javascript:void(0);" onclick='modalArchive(`+ row.id +`,`+ row.reading_id +`,`+`\"` + row.ref_no + `\")'><i class="la la-trash" style="color: #FF8383;"></i> Archive</a>`;
                  }
    tempHtml += ` </div>
                </div>`;
    return tempHtml;
	} else { 
    return false; 
  }
}

// function itemDatatableActions(row){
// 	if(row){
//     var tempHtml = "---";
//     var tempActions = [];
//     var currentActions = ["edit", "delete"];
//     $.each(currentActions, function(index, value){
//         tempActions.push(value);
//     });

//     tempHtml = `<div class="dropdown">
//             <a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown"> 
//                 <i class="la la-ellipsis-h"></i>
//             </a>
//             <div class="dropdown-menu dropdown-menu-right">`;
//         $.each(tempActions, function(ii, vv){
          
//             switch(vv){
//                 case "edit":
//                   var action_name = "", icon_name = "";
//                   if(row.status!='Paid' && row.status!='Archive'){
//                     action_name = "Edit";
//                     icon_name = "la	la-edit";
//                   } else {
//                     action_name = "View";
//                     icon_name = "la	la-eye";
//                   }
//                   tempHtml += `<a class="dropdown-item " data-toggle='modal' data-target='#m_viewBill' href="javascript:void(0);" id='viewBill' data-id='`+row.id+`'><i class="`+icon_name+`"></i> `+action_name+`</a>`;
//                   tempHtml += `<a class="dropdown-item" id="tbl-print" onclick="tblprint(`+row.id+`);" href="javascript:void(0);" data-id=''><i class="la la-print"></i> Print</a>`;
//                 break;
//                 case "delete":
//                   if(row.status!='Paid' && row.status!='Archive'){
//                     tempHtml += `<a class="dropdown-item " style="color: #FF8383;" href="javascript:void(0);" onclick='modalArchive(`+ row.id +`,`+ row.reading_id +`,`+`\"` + row.ref_no + `\")'><i class="la la-trash" style="color: #FF8383;"></i> Archive</a>`;
//                   }
//                 break;
//             }
//         });
//     tempHtml += `</div></div>`;
//     return tempHtml;
// 	}else{ return false; }
// }

Inputmask.extendAliases({
  pesos: {
            prefix: "₱ ",
            groupSeparator: ".",
            alias: "numeric",
            placeholder: "0",
            autoGroup: !0,
            digits: 2,
            digitsOptional: !1,
            clearMaskOnLostFocus: !1
        }
});

$("input[name=total_charges]").inputmask({ alias : "pesos", removeMaskOnSubmit: true });

$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    tblBillings.ajax.reload();
});

$("#ExportExcel").on("click", function() {
  tblBillings.button( '.buttons-excel' ).trigger();
  saveExportLogs('Billing - Export Excel');
});

$("#ExportCSV").on("click", function() {
  tblBillings.button( '.buttons-csv' ).trigger();
  saveExportLogs('Billing - Export CSV');
});

$("#ExportPDF").on("click", function() {
  tblBillings.button( '.buttons-pdf' ).trigger();
  saveExportLogs('Billing - Export PDF');
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

$('#m_viewBill').on('hidden.bs.modal', function () {
  $('.payment-section').hide();
});

$('#table-billing').on("click","#viewBill",function(){
  $('#frmUpdateBill').trigger("reset");
  var selectedBill_id = $(this).attr("data-id");

  var data_row = $("#table-billing").DataTable().rows($(this).parents('tr')).data();
  $.ajax({
      url: baseUrl("eforms/billing/get_bill_data"),
      type: 'post',
      data: { 
        csrf_token: _csrf_hash, 
        id: selectedBill_id
      },
      success: function(response) {
          const payment_history = response.payment_history || [];

          let total_received_amount = 0;

          if (Array.isArray(payment_history) && payment_history.length > 0) {
              $('.payment-section').show();

              // Generate payment history table rows and sum balance_covered and received_amount
              let paymentRows = '';
              let totalBalanceCovered = 0;
              let totalReceivedAmount = 0;

              payment_history.forEach(item => {
                const balanceCovered = parseFloat(item.balance_covered) || 0;
                const receivedAmount = parseFloat(item.received_amount) || 0;
                totalBalanceCovered += balanceCovered;
                totalReceivedAmount += receivedAmount;

                paymentRows += `
                  <tr>
                    <td>${item.ref_no || '0.00'}</td>
                    <td>${item.balance_covered || '0.00'}</td>
                    <td>${item.net_payment || '0.00'}</td>
                    <td class="text-right">
                      <span style="font-size: 12px;">
                        ${item.received_amount ? numberWithCommas(item.received_amount) : '0.00'}
                      </span>
                    </td>
                  </tr>
                `;
              });

              // Optionally, you can display the totals somewhere, for example:
              $("#m_viewBill .total-balance-covered").text(numberWithCommas(totalBalanceCovered));
              $("#m_viewBill .total-received-amount").text(numberWithCommas(totalReceivedAmount));

              total_received_amount = totalReceivedAmount + totalBalanceCovered;

              // Insert rows into the payment history table body
              $("#m_viewBill tbody.payment_info_body").html(paymentRows);
          } else {
            $('.payment-section').hide();
          }

          // ========================================================

          if(response.billdata.print_count >= response.limit){
            $(".btnPrint").hide();
          }else{
            $(".btnPrint").show();
          }

          if(response.billdata.is_paid == '1' || response.billdata.status != '1'){
            // $(".btnUpdate").hide();
            $('.billing_from').css('pointer-events', 'none');
            $('.billing_to').css('pointer-events', 'none');
            $('.due_date').css('pointer-events', 'none');
          }else{
            // $(".btnUpdate").show();
            $('.billing_from').css("pointer-events", "");
            $('.billing_to').css("pointer-events", "");
            $('.due_date').css("pointer-events", "");
          }
          var final_charge = response.billdata.total_charges;
          // var final_charge = 0;
          // if(response.billdata.is_paid == '1'){
          //   final_charge = response.billdata.total_charges;
          // }else{
          //   final_charge = "0.00";
          // }
          $("#m_viewBill .account_id").text(response.billdata.accountno);
          $("#m_viewBill .reading_id").text(response.billdata.reading_refno);
          $("#m_viewBill .customer_name").text(response.billdata.firstname +" "+response.billdata.lastname);
          $("#m_viewBill .meter_no").text(response.billdata.meterno);
          $("#m_viewBill .block_no").text(response.billdata.block);
          $("#m_viewBill .lot_no").text(response.billdata.lot);
          $("#m_viewBill .billing_address").text(response.billdata.street+", "+response.billdata.brgy+", "+response.billdata.city+", "+response.billdata.province);
          $("#m_viewBill .previous").text(response.billdata.previous);
          $("#m_viewBill .current").text(response.billdata.current);
          $("#m_viewBill .usage").text(response.billdata.usage);
          $("#m_viewBill .rate").text(response.billdata.rate);
          $("#m_viewBill .total_charges").val(data_row[0].total_charges);
          $("#m_viewBill .btnPrint").attr("data-id",response.billdata.id);
          $("#m_viewBill input[name=id]").val(response.billdata.id);
          $("#m_viewBill #ref_no").val(response.billdata.ref_no);
          $('#m_viewBill .billing_from').text(response.billdata.billing_from);
          $('#m_viewBill .billing_to').text(response.billdata.billing_to);
          $('#m_viewBill .due_date').text(response.billdata.due_date);

          let bill_amount = 0;
          let remaining_balance = 0;

          const totalCharges = parseFloat(response.billdata.total_charges) || 0;
          const overdue = parseFloat(response.billdata.overdue) || 0;

          bill_amount = totalCharges + overdue;
          remaining_balance = bill_amount - total_received_amount;

          $('#m_viewBill .bill_amount').text(numberWithCommas(bill_amount));
          $('#m_viewBill .remaining-balance').text(numberWithCommas(remaining_balance < 0 ? 0 : remaining_balance));
      },
      error: function(data){
        $('#m_viewBill').modal('hide');
        toastr.error("Please check your internet connection.", "Connection error");
      }
  });
});

$('.billing_from').datepicker({
  format: 'yyyy/mm/dd',
  todayHighlight: true,
  autoclose: true,
  orientation: "bottom left",
  templates: {
    leftArrow: '<i class="la la-angle-left"></i>',
    rightArrow: '<i class="la la-angle-right"></i>'
  }
});

$('.billing_to').datepicker({
  format: 'yyyy/mm/dd',
  todayHighlight: true,
  autoclose: true,
  orientation: "bottom left",
  templates: {
    leftArrow: '<i class="la la-angle-left"></i>',
    rightArrow: '<i class="la la-angle-right"></i>'
  }
});

$('.due_date').datepicker({
  format: 'yyyy/mm/dd',
  todayHighlight: true,
  autoclose: true,
  orientation: "bottom left",
  templates: {
    leftArrow: '<i class="la la-angle-left"></i>',
    rightArrow: '<i class="la la-angle-right"></i>'
  }
});

jQuery(document).on("click", ".btnPrint", function () {
    var bill_id = document.getElementById('bill_id').value;
    win = window.open(baseUrl("eforms/billing/print_bill") + "/" + bill_id, "_blank");
    window.onbeforeunload = recordPrintCount(bill_id);
});

function modalArchive(id,reading_id,name){
  const temp = `<p>Are you sure you wan't to archive <strong class='m--font-boldest'>${name}</strong>?</p>`;
  $('#m_archived').modal('show');
  $('#archive_text').empty().html(temp);
  $("#m_archived input[name=id]").val(id);
  $("#m_archived input[name=reading_id]").val(reading_id);
  $("#m_archived input[name=archive_ref_no]").val(name);
}

function archiveBill(){
  var bill_id = document.getElementById('archive_id').value;
  var reading_id = document.getElementById('reading_id').value;
  var ref_no = document.getElementById('archive_ref_no').value;
  $.ajax({
      url: baseUrl("eforms/billing/archive_bill"),
      type: 'post',
      data: { csrf_token: _csrf_hash, id: bill_id, reading_id:reading_id, ref_no:ref_no },
      success: function (data) {
        if(data.status){
            $('#m_archived').modal('hide');
            tblBillings.ajax.reload();
        }
      },
      error: function(data){
        toastr.error("Please check your internet connection.", "Connection error");
      }
  });
}

function recordPrintCount(id){
  $.ajax({
      url: baseUrl("eforms/billing/count_print"),
      type: "POST",
      data: {id: id,csrf_token: _csrf_hash},
      success: function(data){
        
      }
  });
}

$.validate({
    form : '#frmUpdateBill',
    lang: 'en',
    onSuccess : function(form) {
        $.ajax({
          url : $(form).attr("action"),
          type: "POST",
          data: $('#frmUpdateBill').serialize(),
          dataType: "JSON",
          success: function(data){
            if(data.status == true){
              toastr.success(data.msg, "Notification");
              tblBillings.ajax.reload();
              $("#m_viewBill").modal("hide");
            }else{
              toastr.warning(data.msg, "Notification");
            }
          },
          error: function(data){
            toastr.error("Please check your internet connection.", "Connection error");
          }
      });
      return false;
    },
  });
  
  $(".massPrint").on("click",function(){
   var c = tblBillings.rows( { selected: true } ).data().pluck('id').toArray();
    
    $.ajax({
      url: baseUrl("eforms/billing/mass_bill_print"),
      type: "POST",
      data:{ids: c,csrf_token: _csrf_hash},
      success: function(response){
        var w = window.open("about:blank");
                  w.document.open();
                  w.document.write(response.html);
                  w.document.close();
                  w.print();
                  w.close();
                  
        // refresh table
        tblBillings.ajax.reload();
      },
      error: function(data){
        toastr.error("Please check your internet connection.", "Connection error");
      }
    });
  });

  $("#singlePrint").on("click", function(){
    var billsArr = [];
    
    var bill_id = $("#bill_id").val();
    
    billsArr.push(bill_id);
    $.ajax({
      url: baseUrl("eforms/billing/mass_bill_print"),
      type: "POST",
      data:{ids: billsArr,csrf_token: _csrf_hash},
      success: function(response){
        var w = window.open("about:blank");
                  w.document.open();
                  w.document.write(response.html);
                  w.document.close();
                  w.print();
                  w.close();
      },
      error: function(data){
        toastr.error("Please check your internet connection.", "Connection error");
      }
    });
  });

  function tblprint(bill_id) {
    var billsArr = [];
    billsArr.push(bill_id);

    $.ajax({
      url: baseUrl("eforms/billing/mass_bill_print"),
      type: "POST",
      data:{ids: billsArr,csrf_token: _csrf_hash},
      success: function(response){
        var w = window.open("about:blank");
                  w.document.open();
                  w.document.write(response.html);
                  w.document.close();
                  w.print();
                  w.close();
      },
      error: function(data){
        toastr.error("Please check your internet connection.", "Connection error");
      }
    });
  }

  $(document).ready(function () {
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': { delay: 100 },
        filters: [
            { id: 'b.id', label: 'ID #', type: 'integer' },
            { id: 'b.ref_no', label: 'Reference No.', type: 'string' },
            { id: 'a.accountno', label: 'Account No.', type: 'string' },
            { id: 'a.firstname', label: 'First Name', type: 'string' },
            { id: 'a.middlename', label: 'Middle Name', type: 'string' },
            { id: 'a.lastname', label: 'Last Name', type: 'string' },
            { id: 'a.meterno', label: 'Meter No.', type: 'string' },
            { id: 'b.billing_from', label: 'Billing Date', type: 'string' },
            { id: 'b.due_date', label: 'Date Due', type: 'string' },
            { id: 'b.total_charges', label: 'Total Charges', type: 'string' },
            {
                id: 'due',
                label: 'Status',
                type: 'integer',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select. .',
                    width: '150%',
                    data: [
                        {
                            id: "1",
                            text: "Paid"
                        }, {
                            id: "2",
                            text: "Overdue"
                        }, {
                            id: "3",
                            text: "On going"
                        }, {
                            id: "4",
                            text: "Today due"
                        }, {
                            id: "5",
                            text: "Archive"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },
        ],
    });

    $("#query-builder_group_0").addClass("col-12");
});

$('#query-builder-btn').on('click', function () {
    var result = $('#query-builder').queryBuilder('getSQL');

    if (!$.isEmptyObject(result)) {

        var myarr = result.sql.split(" ");
        var selected = myarr[0];
        var operator = myarr[1];
        var value = myarr[2];
        var date = getCurrentDate();

        if(selected == 'due' && !result.sql.includes('AND')){

          if(value == 1){ // paid
              query_builder = {sql:"b.is_paid "+operator+" '1'"};
          } else if(value == 2){ // overdue
              query_builder = {sql:"b.is_paid = '0' AND ( b.due_date < \'"+date+"\' ) AND ( b.status = '1' )"};
          } else if(value == 3){ // on going
              query_builder = {sql:"b.is_paid = '0' AND ( b.due_date > \'"+date+"\' ) AND ( b.status = '1' )"};
          } else if(value == 4){ // today due
              query_builder = {sql:"b.is_paid = '0' AND ( b.due_date = \'"+date+"\' ) AND ( b.status = '1' )"};
          } else { // archive
              query_builder = {sql:"b.status = '0'"};
          }

        } else if (result.sql.includes('due') && result.sql.includes('AND')) {
            query_builder = '';
            alert("Cannot join group the Status");
        } else {
            query_builder = result;
        }
        
        if(query_builder != ''){
          tblBillings.ajax.reload();
          $("#modal-query-builder").modal("hide");
        }
    }
});
  
function clear_query_builder() {
  $('#query-builder').queryBuilder('reset');
  query_builder = null;
  tblBillings.ajax.reload();
}

function getCurrentDate(){
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    return yyyy+ '/' + mm + '/' + dd;
}
 
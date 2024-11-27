$(document).ready(function(){

});
var search_val = "";
var query_builder = "";
var tblReadings = $("#table-payments-archive").DataTable({
   dom: '<"toolbar">rtlip',
   serverSide: true,
   processing: true,
   aaSorting: [],
   ajax: {
        url: baseUrl("eforms/billing/get_payment_archive_collection/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function(d){
           d.csrf_token = _csrf_hash,
           d.search['value'] = search_val,
           d.query_builder = query_builder
       }
   },
   searching: true,
   columns: [
       { data: "ref_no", render: function (data) {
              return "<strong style='color: #525252;'>"+data+"</strong>";
            }
        },
       { data: "accountno"},
       { data: "name"},
       { data: "payment_date"},
       { data: "model"},
       { data: "block"},
       { data: "lot"},
       { data: "ar"},
       { data: null, width: "5%", className: "text-center"},
   ],
   columnDefs: [
       { targets: [0]},       
       {
           data: null,
           defaultContent: "",
           targets: -1,
           orderable: false,
           render: function ( data, type, row, meta ) { 
              var _action = "restore("+row.id+", '"+row.ref_no+"')";
              var action = '<td class=" text-center"> <button type="button" onclick="'+_action+'" class="btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnRestore" data-toggle="m-tooltip" data-original-title="Restore" data-placement="bottom" data-delay="{&quot;show&quot;: 300}" aria-describedby="tooltip638481"><i class="la la-reply"></i></button></td>';
              return action; 
          },
       }
   ],buttons: [
       { 
           extend: 'csv',
           exportOptions: {
               columns: "thead th:not(.notExport)"
           }
       }, { 
           extend: 'excel',
           exportOptions: {
               columns: "thead th:not(.notExport)"
           }
       }, { 
           extend: 'pdf',
           exportOptions: {
               columns: "thead th:not(.notExport)"
           }
       }
   ]
});

function numberWithCommas(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function renderStatus(data) {
  switch (data) {
      case "1":
          return '<div class="m-badge text-white m-badge--accent m-badge--wide" role="alert"><strong>Billed</strong></div>';
          break;
      default:
          return '<div class="m-badge text-white m-badge--warning m-badge--wide" role="alert"><strong>Unbilled</strong></div>';
          break;
  }
}

$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    tblReadings.ajax.reload();
});

$("#ExportExcel").on("click", function() {
  tblReadings.button( '.buttons-excel' ).trigger();
  saveExportLogs('Readings - Export Excel');
});

$("#ExportCSV").on("click", function() {
  tblReadings.button( '.buttons-csv' ).trigger();
  saveExportLogs('Readings - Export CSV');
});

$("#ExportPDF").on("click", function() {
  tblReadings.button( '.buttons-pdf' ).trigger();
  saveExportLogs('Readings - Export PDF');
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

$("#cb-select-all").click(function () {
  $('#table-payments tbody input[type="checkbox"]').prop('checked', this.checked);
});

$("#table-payments").on("click", "tbody input[type='checkbox']", function () {
  const allCheckboxes = $("#table-payments tbody input[type='checkbox']").length;
  const checkedCheckboxes = $("#table-payments tbody input[type='checkbox']:checked").length;
  const checked = allCheckboxes <= checkedCheckboxes;
  $('#cb-select-all').prop('checked', checked);
});

function restore(id, ref_no){
  $("#modal-restore").modal("show");

  $.validate({
      form: '#frm-restore',
      lang: 'en',
      onSuccess: function (form) {
          $.ajax({
              url: baseUrl("eforms/billing/restore_payment/"),
              type: "POST",
              data: {id: id, ref_no: ref_no, csrf_token: _csrf_hash},
              dataType: "json",
              beforeSend: function () {
                  $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
              },
              success: function (data) {
                  if (data.status) {
                      $('#modal-restore').modal('hide');
                      toastr.success(data.toastr_msg, "Successfully restored", 5000);
                      tblReadings.ajax.reload();
                  } else {
                      toastr.error(data.toastr_msg, "Error!", 5000);
                  }
                  $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
              }
          });
          return false;
      },
  });
}
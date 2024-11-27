$(document).ready(function(){

  });
  var search_val = "";
  var query_builder = "";
  var tblReadings = $("#table-readings-archive").DataTable({
     dom: '<"toolbar">rtlip',
     serverSide: true,
     processing: true,
     aaSorting: [],
     ajax: {
          url: baseUrl("eforms/billing/get_reading_archive_collection/"),
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
          {
            width: '2%',
            orderable: false,
            data: null,
            className: 'text-center',
            render: function (data, type, row) {
                var temp = "";
                if(row.status == 0){
                  temp = `<label class="m-checkbox m-checkbox--air m-checkbox--state-primary" title='Check to Print'> <input id="selectedReading" type="checkbox" class="text-gray chckBox" value="`+row.id+`" name="selected"><span></span></label>`;
                }
                return temp;
            }
          },
         { data: "ref_no", render: function (data) {
                return "<strong style='color: #525252;'>"+data+"</strong>";
              }
          },
         { data: "accountno"},
         { data: "name"},
         { data: "meterno"},
         { data: "reading_date"},
         { data: "model"},
         { data: "block"},
         { data: "lot"},
         { data: "reading", className: "text-right", render: function (data) {
                return "<strong style='color: #525252;'>"+numberWithCommas(data)+"</strong>";
              }
          },
         { data: "status", className: "text-center", render: function (data) {
              return renderStatus(data)
          }
      },
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
  
  function modalArchive(id, ref_no){
    const temp = `<p>Are you sure you wan't to archive <strong class='m--font-boldest'>${ref_no}</strong>?</p>`;
    $('#m_archived').modal('show');
    $('#archive_text').empty().html(temp);
    $("#m_archived input[name=id]").val(id);
    $("#m_archived input[name=archive_ref_no]").val(ref_no);
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
  
  Inputmask.extendAliases({
    deci: {
              prefix: "",
              groupSeparator: ".",
              alias: "numeric",
              placeholder: "0",
              autoGroup: !0,
              digits: 2,
              digitsOptional: !1,
              clearMaskOnLostFocus: !1
          }
  });
  
  $("#reading").inputmask({ alias : "deci", removeMaskOnSubmit: true });
  $("#edit_reading").inputmask({ alias : "deci", removeMaskOnSubmit: true });
  
  $("#select2_account").select2({
    placeholder: 'SELECT AN OPTION',
    dropdownParent: $("#m_newReading"),
    width: '100%',
    ajax: {
      url: baseUrl("eforms/billing/get_account_select_reading"),
      global: false,
      processResults: function (data) {
        return data;
      }
    }
  });
  
  $("#select2_account_edit").select2({
    placeholder: 'SELECT AN OPTION',
    dropdownParent: $("#m_editReading"),
    width: '100%',
    ajax: {
      url: baseUrl("eforms/billing/get_account_select_reading"),
      global: false,
      processResults: function (data) {
        return data;
      }
    }
  });
  
  function account_details(){
    var account_id = $('[name="account_id"]').val();
    if(typeof account_id != "undefined" && account_id && account_id != 'null'){
      $.ajax({
        url: baseUrl('eforms/billing/get_account_details/') + account_id,
        dataType: "JSON",
        success: function(data){
          $(".meterno").val(data.data.meterno);
          $("#meterno_raw").val(data.data.meterno_raw);
          $(".block").val(data.data.block);
          $(".lot").val(data.data.lot);
          $(".previous_reading").val(data.previous_reading);
          $(".account_name").val(data.data.firstname+" "+data.data.lastname);
          $("#reading").val("")
        },
        error: function (request, status, error) {
          toastr.error("Please check your internet connection.", "Connection error");
          $("#m_newReading").modal("hide");
        }
      });
    }
  }
  
  $('#readingdate').datetimepicker({
    minView:'month',
    format: 'yyyy/mm/dd',
    todayHighlight: true,
      autoclose: true,
    orientation: "bottom left",
    templates: {
      leftArrow: '<i class="la la-angle-left"></i>',
      rightArrow: '<i class="la la-angle-right"></i>'
    },
    endDate: getCurrentDate(),
  });
  
  $.validate({
    form : '#fromCreateReading',
    lang: 'en',
    onSuccess : function(form) {
        $.ajax({
          url : $(form).attr("action"),
          type: "POST",
          data: $('#fromCreateReading').serialize(),
          dataType: "JSON",
          success: function(data){
            if(data.status == true){
              toastr.success(data.msg, "Notification");
              tblReadings.ajax.reload();
              $('#fromCreateReading').trigger("reset");
              $(".picInput").remove();
              $(".dip_img").remove();
              $('#m_newReading #select2_account').val('').trigger("change");
            }else{
              toastr.warning(data.msg, "Notification");
            }
          },
          error: function (request, status, error) {
            toastr.error("Please check your internet connection.", "Connection error");
          }
      });
      return false;
    },
  });

  $('.readingdate').datepicker({
    format: 'yyyy/mm/dd',
    todayHighlight: true,
    autoclose: true,
    orientation: "bottom left",
    templates: {
      leftArrow: '<i class="la la-angle-left"></i>',
      rightArrow: '<i class="la la-angle-right"></i>'
    },
    endDate: getCurrentDate()
  });
  
  $("#fromUpdateReading #img_primary").on("click",".removeAttached",function(){
    var id = $(this).attr("data-id");
    var src = $(this).attr("data-src");
    $("div[data-cont-id="+id+"]").remove();
    $("input[data-cont-id="+id+"]").remove();
  });
  
  $.validate({
    form : '#fromUpdateReading',
    lang: 'en',
    onSuccess : function(form) {
        $.ajax({
            url : $(form).attr("action"),
            type: "POST",
            data: $('#fromUpdateReading').serialize(),
            dataType: "JSON",
            success: function(data){
              if(data.status == true){
                toastr.success(data.msg, "Notification");
                tblReadings.ajax.reload();
                $('#fromUpdateReading').trigger("reset");
                $(".picInput").remove();
                $(".dip_img").remove();
                $("#m_editReading").modal("hide");
              }else{
                toastr.warning(data.msg, "Notification");
              }
          },
          error: function (request, status, error) {
            toastr.error("Please check your internet connection.", "Connection error");
          }
      });
      return false;
    },
  });  
  
  $(".btnApprove_action").on("click",function(){
    var id = $("#fromUpdateReading input[name=id]").val();
     $.ajax({
        url: baseUrl("eforms/billing/approve_reading"),
        type: "POST",
        data: {id: id, csrf_token: _csrf_hash},
        success: function(data){
          
        },
        error: function (request, status, error) {
          toastr.error("Please check your internet connection.", "Connection error");
        }
     });
  });
  
  $("#cb-select-all").click(function () {
    $('#table-readings tbody input[type="checkbox"]').prop('checked', this.checked);
  });
  
  $("#table-readings").on("click", "tbody input[type='checkbox']", function () {
    const allCheckboxes = $("#table-readings tbody input[type='checkbox']").length;
    const checkedCheckboxes = $("#table-readings tbody input[type='checkbox']:checked").length;
    const checked = allCheckboxes <= checkedCheckboxes;
    $('#cb-select-all').prop('checked', checked);
  });
  
  $("#generate_bill").click(function (){
    var selectedReading = [];
       $(".chckBox").each(function(){
          var trig = $(this).is(":checked");
          if(trig){
            selectedReading.push($(this).attr("value"));
          }
      });
  
      if(selectedReading.length > 0){
        $('#m_generate').modal('show');
      } else {
        toastr.warning("No selected reading.", "Notification");
      }
  });
  
  function generateBill(){
    var selectedReading = [];
       $(".chckBox").each(function(){
          var trig = $(this).is(":checked");
          if(trig){
            selectedReading.push($(this).attr("value"));
          }
      });
  
      if(selectedReading.length > 0){
        $.ajax({
          url: baseUrl("eforms/billing/generate_bill"),
          type: "POST",
          data: {csrf_token: _csrf_hash, selectedReading: selectedReading},
          success: function(data){
            
            data.forEach((e)=>{
              if(e.status){
                toastr.success(e.msg, e.ref_no);
              } else {
                toastr.error(e.msg, e.ref_no);
              }
            });
            tblReadings.ajax.reload();
            $('#m_generate').modal('hide');
            $('#cb-select-all').prop('checked', false);
          },
          error: function (request, status, error) {
            tblReadings.ajax.reload();
            $('#m_generate').modal('hide');
            toastr.warning("Please check your internet connection.", "Connection error");
          }
        });
      } else {
        toastr.warning("No selected reading.", "Notification");
      }
  }
  
  function getCurrentDate(){
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();
    return yyyy+'-'+mm+'-'+dd;
  }

  function restore(id, ref_no){
    $("#modal-restore").modal("show");

    $.validate({
        form: '#frm-restore',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("eforms/billing/restore_reading/"),
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
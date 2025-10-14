param_id = window.location.pathname.split("/").pop();
var tempData = {};
var name;
var borrower_id;

$("input[name=id]").val(param_id);
$.ajax({
  url: baseUrl("eforms/billing/get_account_details/") + param_id,
  type: "GET",
  dataType: "JSON",
  success: function(data) {
    if(data.data.status == 0){
      $("#inactive").prop("checked",true);
    }else{
      $("#active").prop("checked",true);
    }

    if(data.data.is_archive == 1){
      $(".btnArchive").hide();
    }

    if(data.data.is_disconnected == 0){
      $("#connected").prop("checked",true);
      document.getElementById("lbl_connected").style.pointerEvents = "none";
    }else{
      $("#disconnected").prop("checked",true);
      document.getElementById("lbl_disconnected").style.pointerEvents = "none";
    }

    vmTab1.vm_tab1 = Object.assign({}, data.data);
    vmTab1.vm_reading = data.current_usage;

    $('#applicationdate').datepicker("setDate", data.data.applicationdate);
    $('#activationdate').datepicker("setDate", data.data.activationdate);

    var newOption = new Option(data.data.subdivision, data.data.subdivision_id, true, true);
    $('#subdivisionSelect').append(newOption).trigger('change');
    balance_for_disconnection()

  },
  error: function(jqXHR, textStatus, errorThrown) {
    toastr.error("Please check your internet connection.", "Connection error");
  }
});

function balance_for_disconnection(){
  
	var status = $('[name="is_disconnected"]').val();
  $.ajax({
    url: baseUrl("eforms/billing/get_balance_for_disconnection/") + param_id,
    type: "GET",
    dataType: "JSON",
    success: function(data) {
      // alert(data.lastbill.total_balance);
      if(data.lastbill.total_balance > 0){
        $("#connected").attr('disabled', true);
        $("#overdue_alert").removeClass('m--hide');
      }else{
        $("#connected").attr('disabled', false);
        $("#overdue_alert").addClass('m--hide');
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
      alert(errorThrown)
    }
  });
}

function change_active(){
  $("#inactive").prop("checked",false);
}

function change_inactive(){
  $("#active").prop("checked",false);
}

function change_connection(){
  $("#disconnected").prop("checked",false);
}

function change_disconnection(){
  $("#connected").prop("checked",false);
}

function showConfirmationDisconnect(){
  var connected = document.getElementById('connected').checked;
  $(".body_title").html("Are you sure you wan't to " + (connected ? "Connect?" : "Disconnect?"));
  $("#m_waterConnection").modal("show");
}

$("#btnWaterConnection").on("click",function(){
  $("#m_waterConnection").modal("hide");
  var connected = document.getElementById('connected').checked;
  document.getElementById("lbl_connected").style.pointerEvents = connected ? "none" : "auto";
  document.getElementById("lbl_disconnected").style.pointerEvents = !connected ? "none" : "auto";
});

$("#btnWaterConnection_cancel").on("click",function(){
  var connected = document.getElementById('connected').checked;
  if(connected){
    $("#disconnected").prop("checked",true);
    $("#connected").prop("checked",false);
  } else {
    $("#disconnected").prop("checked",false);
    $("#connected").prop("checked",true);
  }
});

var vmTab1 = new Vue({
  el: "#formEditAccount",
  data: { vm_tab1: {}, vm_reading: {}},
  mounted: function(){
    var _this = this;
    var vmData =  _this.vm_tab1;
  }
});

$.validate({
    form : '#formEditAccount',
    lang: 'en',
    onSuccess : function(form) {
        $.ajax({
          url : $(form).attr("action"),
          type: "POST",
          data: $('#formEditAccount').serialize(),
          dataType: "JSON",
          success: function(data){
            if(data.status == true){
              toastr.success(data.msg, "Notification");
              window.location.replace(baseUrl("eforms/billing/accounts"));
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

var goIndex = function(){
    window.location.replace(baseUrl("eforms/billing/accounts"));
}

$("#btnConfirmArchive").on("click",function(){
  $.ajax({
    url: baseUrl("eforms/billing/archive_account/"),
    type: "POST",
    dataType: "JSON",
    data:{id : param_id, csrf_token: _csrf_hash},
    success: function(data){
      if(data.status == true){
        toastr.success(data.msg, "Notification");
        $("#m_archiveConfirm").modal("hide");
        $(".btnArchive").hide();
        goIndex();
      }else{
        toastr.warning(data.msg, "Notification");
      }
    },
    error: function(data){
      toastr.error("Please check your internet connection.", "Connection error");
    }
  });
});

$("#subdivisionSelect").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/billing/get_subdivision_select"),
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

$('#subdivisionSelect').on('select2:select', function (e) {
  console.log(e.params.data.id)
});

$('#applicationdate').datepicker({
  minView:'month',
  format: 'yyyy/mm/dd',
  todayHighlight: true,
	autoclose: true,
  orientation: "bottom left",
  templates: {
    leftArrow: '<i class="la la-angle-left"></i>',
    rightArrow: '<i class="la la-angle-right"></i>'
  }
});

$('#activationdate').datepicker({
  minView:'month',
  format: 'yyyy/mm/dd',
  todayHighlight: true,
	autoclose: true,
  orientation: "bottom left",
  templates: {
    leftArrow: '<i class="la la-angle-left"></i>',
    rightArrow: '<i class="la la-angle-right"></i>'
  }
});


// $("input[name=status]").on("change",function(){
//   if($(this).is(':checked')){
//     updateAccountStatus($("input[name=id]").val(), "1");
//   }else{
//     updateAccountStatus($("input[name=id]").val(), "0");
//   }
// });

// var updateAccountStatus = function(id, status){
//   $.ajax({
//     url : baseUrl('eforms/billing/update_account_status/') ,
//     type: "POST",
//     data: {csrf_token: _csrf_hash, id: id, status: status},
//     success: function(data){
//       if(data.status == true){
//         toastr.success(data.msg, "Notification");
//       }else{
//         toastr.warning(data.msg, "Notification");
//       }
//     }
//   })
// }


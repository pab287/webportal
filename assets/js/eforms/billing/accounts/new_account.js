$.validate({
    form : '#formNewAccount',
    lang: 'en',
    onSuccess : function(form) {
        $.ajax({
          url : $(form).attr("action"),
          type: "POST",
          data: $('#formNewAccount').serialize(),
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

function change_connection(){
  $("#disconnected").prop("checked",false);
}

function change_disconnection(){
  $("#connected").prop("checked",false);
}

function change_active(){
  $("#inactive").prop("checked",false);
}

function change_inactive(){
  $("#active").prop("checked",false);
}

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
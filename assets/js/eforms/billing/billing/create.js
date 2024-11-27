$.validate({
  form : '#frmCreateBill',
  lang: 'en',
  onSuccess : function(form) {
      $.ajax({
          url : $(form).attr("action"),
          type: "POST",
          data: $('#frmCreateBill').serialize(),
          dataType: "JSON",
          success: function(data){
            if(data.status == true){
              toastr.success(data.msg, "Notification");
            }else{
              toastr.warning(data.msg, "Notification");
            }
            window.location.replace(baseUrl("eforms/billing/billing"));
        },
        error: function (xhr, ajaxOptions, thrownError) {
          toastr.success("Billing successfully saved.", "Notification");
          toastr.warning("Error Email send.", "Notification");
          window.location.replace(baseUrl("eforms/billing/billing"));
        }
    });
    return false;
  },
});    

$("#accountSelect").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
      url: baseUrl("eforms/billing/get_account_select_billing"),
      global: false,
      processResults: function (data) {
        return data;
      }
    }
});

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

$('#accountSelect').on('select2:select', function (e) {
  $('#readingSelect').empty().trigger("change");
  $("#meterno_raw").val(e.params.data.meterno_raw);
  $.ajax({
    url: baseUrl("eforms/billing/get_readings_by_account_id"),
    type: 'post',
    data: {csrf_token: _csrf_hash, id: e.params.data.id, meterno_raw: e.params.data.meterno_raw},
    success: function(data){
      $.each(data.results,function(i,v){
        var newOption = new Option(v.text, v.id, true, true);
        $('#readingSelect').append(newOption).trigger('change');
      });
      
      getAccountDetails();
      getcurrentMeterReading();
    },
    error: function(data){
      toastr.error("Please check your internet connection.", "Connection error");
    }
});
});

function computeMeterUsage(previous, current, prev_reading_id){
  var totalusage = parseFloat(current) - parseFloat(previous);

  $(".current").val(current);
  $(".previous").val(previous);
  $(".usage").val(parseFloat(totalusage).toFixed(2));
  $("#prev_reading_id").val(prev_reading_id);

  computeTotalCharges(totalusage);
}

function computeTotalCharges(usage){
  var rate = $(".rate").val();

  if(usage > 11.46) {
    var totalcharges = parseFloat(usage) * parseFloat(rate);
    $("input[name=total_charges]").val(numberFormat(totalcharges));
  }else{
    $("input[name=total_charges]").val(numberFormat(300));
  }
}

$("#readingSelect").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
});

$('#readingSelect').on('select2:select', function (e) {
  getcurrentMeterReading();
});

function getAccountDetails(){
  $.ajax({
    url: baseUrl("eforms/billing/get_account_details"),
    type: 'post',
    data: {csrf_token: _csrf_hash, account_id: $("#accountSelect").val()},
    success: function(data){
      $(".customer_name").val(data.data.firstname+" "+data.data.lastname);
      $(".meter_no").val(data.data.meterno);
      $(".block_no").val(data.data.block);
      $(".lot_no").val(data.data.lot);
      $(".billing_address").val(data.data.street+", "+data.data.brgy+", "+data.data.city+", "+data.data.province);
    },
    error: function (request, status, error) {
      countError++;
      if(countError == 5){
        window.location.replace(baseUrl("eforms/billing/billing"));
        toastr.warning("Connection error please try again", "");
      } else {
        getAccountDetails();
      }
    }
  });
}

function getMaxDueDate(){
  var someDate = new Date();
  var numberOfDaysToAdd = 10;
  someDate.setDate(someDate.getDate() + numberOfDaysToAdd); 

  var dd = someDate.getDate();
  var mm = someDate.getMonth() + 1;
  var y = someDate.getFullYear();

  var someFormattedDate = y + '/'+ mm + '/'+ dd;

  return someFormattedDate;
}

function getcurrentMeterReading(){
  if($("#readingSelect").val()){
    $.ajax({
      url: baseUrl("eforms/billing/get_account_current_meter_reading"),
      type: 'post',
      data: {csrf_token: _csrf_hash, reading_id:$("#readingSelect").val(), account_id:$("#accountSelect").val(), meterno_raw:$("#meterno_raw").val()},
      success: function(data){
        var current_reading = 0, previous_reading = 0, prev_reading_id = 0;
  
        if(data.data_current){
          current_reading = data.data_current.reading;
        }
  
        if(data.data_previous){
          previous_reading = data.data_previous.reading;
          prev_reading_id = data.data_previous.id;
        }

        computeMeterUsage(previous_reading, current_reading, prev_reading_id);
      },
      error: function (request, status, error) {
        countError++;
        if(countError == 5){
          window.location.replace(baseUrl("eforms/billing/billing"));
          toastr.warning("Connection error please try again", "");
        } else {
          getcurrentMeterReading();
        }
      }
    });
  } else {
    $(".current").val('');
    $(".previous").val('');
    $(".usage").val('');
    $("#prev_reading_id").val('');
  }
}

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

$(document).ready(function(){
  
  fetchRate();

  $('#duedate').datepicker({
    autoclose: true,
    pickerPosition: 'bottom-left',
    format: 'yyyy/mm/dd',
    startDate: moment(getMaxDueDate()).format("YYYY-MM-DD")
  });
});

var countError = 0;
function fetchRate(){
  $.ajax({
    url: baseUrl("eforms/billing/get_applied_rate"),
    data: {csrf_token: _csrf_hash},
    success: function(data){
        $(".rate").val(data.rate);
    },
    error: function (request, status, error) {
      countError++;
      if(countError == 5){
        window.location.replace(baseUrl("eforms/billing/billing"));
        toastr.warning("Connection error please try again", "");
      } else {
        fetchRate();
      }
    }
  });
}

//document.addEventListener('contextmenu', event => event.preventDefault());
$.validate({
  form : '#frmCreatePayment',
  lang: 'en',
  onSuccess : function(form) {
      $.ajax({
          url : $(form).attr("action"),
          type: "POST",
          data: $('#frmCreatePayment').serialize(),
          dataType: "JSON",
          success: function(data){
            if(data.status == true){
              $("#ar_code").text(data.ar_code);
              $("#ar_modal").modal()
              toastr.success(data.msg, "Notification");
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

$("#ar_modal").on('hidden.bs.modal', function(){
  window.location.replace(baseUrl("eforms/billing/payment"));
});

$("#accountSelect").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  minimumInputLength: 3,
  ajax: {
    url: baseUrl("eforms/billing/get_account_select_payments"),
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

var customer_id = "";
$('#accountSelect').on('select2:select', function (e) {
  $('#btnSave').prop('disabled', true);

  $('#billSelect').empty().trigger("change");
  $.ajax({
    url: baseUrl("eforms/billing/get_upaid_bills_by_account_id"),
    type: 'post',
    data: { csrf_token: _csrf_hash, id: e.params.data.id },
    success: function (data) {
      
      $.each(data.results, function (i, v) {
        var newOption = new Option(v.text, v.id, true, true);
        if(i != 0){
          $(newOption).prop('disabled', true);
        }
        $('#billSelect').append(newOption).trigger('change');
      });

      var newOption = new Option('Select an option', '', true, true);
      $('#billSelect').append(newOption).trigger('change');

      customer_id = e.params.data.id;
      populate_customer_name(customer_id);
      bill_id = "";

      $(".penalty_layout").hide();
      $(".billing_amount").val(0.00);
      $(".net_payment").val(0.00);
      $(".remaining_balance").val(0);
      $("#balance_covered").val(0);
      $("#sub_total").val(0);
      $("#receivedAmount").val(0);
      $("#receive_text").html("Received Amount");
      document.getElementById('receivedAmount').readOnly = false;
    },
    error: function (request, status, error) {
      toastr.error("Please check your internet connection.", "Connection error");
    }
  });
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

$("#sub_total").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
$("#balance_covered").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
$(".overpayment").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
$(".remaining_balance").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
$("#billing_amount").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
$("#receivedAmount").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
$("#net_payment").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
$("#default_billing_amount").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
$(".penalty_layout").hide();
$('.details_layout').hide();
$(".balance_layout").hide();

$('input[name=received_amount').keypress(function() {
  return (/\d/.test(String.fromCharCode(event.which) ));
});

$("#billSelect").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
});

var bill_id = "";
$('#billSelect').on('select2:select', function (e) {
  bill_id = e.params.data.id;
  checkOverdue();
  getBillPayments(bill_id);
});

function handleValueChange(billing_amount) {
  var receivedAmount = document.getElementById('receivedAmount').value.replace(/[^0-9a-zA-Z.]/g, "");
  var balance_covered = $('#balance_covered').val().replace(/[^0-9a-zA-Z.]/g, "");
  var overpayment = $('.overpayment').val().replace(/[^0-9a-zA-Z.]/g, "");
  var parseOverpayment = parseFloat(overpayment);
  var parseBilling_amount = parseFloat(billing_amount);

  var bill_amount = document.getElementById('billing_amount').value.replace(/[^0-9a-zA-Z.]/g, "");
  
  // IF overpayment has value
  if (overpayment > 0) {
    // Has overpayment OR the overpayment is greater than bill amount
    if(parseOverpayment > parseFloat(bill_amount)) {
        submitToggle(false);
    } else {
        if(parseFloat(receivedAmount) > 0 || Math.floor(balance_covered) > 0) {
          submitToggle(false);
        } else {
          submitToggle(true);
        }
    }
  } else {
    // No overpayment OR the overpayment is less than bill amount
    if (parseFloat(receivedAmount) > 0) {
      submitToggle(false);
    } else {
      submitToggle(true);
    }
  }
}

function submitToggle(boolean) {
  switch(boolean) {
    // Disable button
    case true: 
      $("#btnSave").prop("disabled", true);
      break;
    
    // Enable button
    case false:
      $("#btnSave").prop("disabled", false);
      break;
  }
}

$('#payment_type').on('select2:select', function (e) {
  if(e.params.data.id == 'check'){
    $('#label_details').text("Check Details");
    $('.details_layout').show();
  } else if(e.params.data.id == 'bank'){
    $('#label_details').text("Bank Details");
    $('.details_layout').show();
  } else {
    $('.payment_details').val("");
    $('.details_layout').hide();
  }
});

var payment_date = '';
$('#m_datepicker_payment_date').on('changeDate', function() {
    var date = $('#m_datepicker_payment_date').datepicker('getFormattedDate');
    payment_date = date;
    checkOverdue();
});

$('#maintenanance_toggle').on('change', function() {
  var toggle = document.getElementById('maintenanance_toggle').checked;
  if(toggle){
    $("#maintenanance_fee").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
    document.getElementById("maintenanance_fee").style.backgroundColor = null;
    document.getElementById("maintenanance_fee_title").style.textDecoration = 'none';
  } else {
    $("#maintenanance_fee").inputmask();
    document.getElementById("maintenanance_fee").style.backgroundColor='#efefef';
    document.getElementById("maintenanance_fee_title").style.textDecoration = 'line-through';
  }
});

function getBillPayments(id){
  $.ajax({
    url: baseUrl("eforms/billing/get_bill_payments/"),
    type: 'POST',
    data: { csrf_token: _csrf_hash, id: id},
    success: function (data) {
      let html_data = "";
      $.each(data, function(i, value){
        if(i == 0){
          html_data += "<li><b><i>"+value.ref_no+" | ₱"+value.received_amount+" | "+value.created_date+"</i></b></li>";
        }else{
          html_data += "<li>"+value.ref_no+" | ₱"+value.received_amount+" | "+value.created_date+"</li>";
        }
      });
      $(".payment_list").append(html_data);
    },
    error: function (request, status, error) {
     alert("error");
    }
  });
}


var countError = 0;
function checkOverdue() {
  if(payment_date != '' && bill_id != ''){
    $.ajax({
      url: baseUrl("eforms/billing/check_overdue"),
      type: 'post',
      data: { csrf_token: _csrf_hash, id: bill_id, customer_id:customer_id, payment_date:payment_date },
      success: function (data) {
        $(".billing_amount").val(data.billing_amount);
        document.getElementById("is_penalty").value = (data.isPenalty ? 1 : 0);
        document.getElementById("serialize_penalties").value = data.serialize_penalties;
        document.getElementById("reconnection_fee").value = data.reconnectionFee.amount;
        document.getElementById("default_billing_amount").value = data.default_bill_amount;

        var billing_amount = data.billing_amount;
        
        if(data.isPenalty || data.isDisconnection){
          penaltyDetails(data.array_penalties, data.isDisconnection, data.reconnectionFee.amount);
          $(".penalty_layout").show();
        } else {
          $(".penalty_layout").hide();
        }

        var amnt = 0;
        if(accnt_balance > 0){ // has balance

            var netPayment = data.net_payment - accnt_balance;
            
            if(netPayment < 0){ // has remaining balance
              amnt = 0;
              var balance_deduct = parseFloat(accnt_balance) - parseFloat(Math.abs(netPayment));

              $(".remaining_balance").val(Math.abs(netPayment));

              $("#balance_covered").val(balance_deduct);
              document.getElementById('receivedAmount').readOnly = true;
              $("#receive_text").html("Received Amount (Balance Covered)");
            } 
            else {
              amnt = parseFloat(accnt_balance) - parseFloat(Math.abs(data.net_payment));
              $("#balance_covered").val(accnt_balance);
              $(".remaining_balance").val(0);
            }
        } else { // no balance
            amnt = data.net_payment;
        }

        $(".net_payment").val(Math.abs(amnt));
        // $("#sub_total").val(data.net_payment);
        $("#sub_total").val(Math.abs(amnt));
        $("#receivedAmount").val(0);

        handleValueChange(billing_amount);
      },
      error: function (request, status, error) {
        countError++;
        if(countError == 5){
          window.location.replace(baseUrl("eforms/billing/payment"));
          toastr.error("Connection error please try again", "");
        } else {
          checkOverdue();
        }
      }
    });
  }
}

generate_payment_ar();

function generate_payment_ar(){
  $.ajax({
    url: baseUrl("eforms/billing/generate_payment_ar/"),
    type: 'get',
    // data: { csrf_token: _csrf_hash},
    success: function (data) {
      $("#acknowledgement_receipt").val(data);
    },
    error: function (request, status, error) {
     alert("error");
    }
  });
}

$('#billSelect').trigger('change.select2', function (e) {
  $.ajax({
    url: baseUrl("eforms/billing/get_bill_details"),
    type: 'post',
    data: { csrf_token: _csrf_hash, id: e.params.data.id },
    success: function (data) {
      console.log(data); 
    },
    error: function (request, status, error) {
      toastr.error("Please check your internet connection.", "Connection error");
    }
  });
});

var accnt_balance = 0;
function populate_customer_name(customer_id) {
  $.ajax({
    url: baseUrl("eforms/billing/get_customer_details"),
    type: 'post',
    data: { csrf_token: _csrf_hash, customer_id: customer_id },
    success: function (data) {
      
      accnt_balance = data.balance;
      $(".customer_name").val(data.data.firstname + " " + data.data.lastname);
      $(".overpayment").val(accnt_balance);

      if(accnt_balance > 0){
        $(".balance_layout").show();
      } else {
        $(".balance_layout").hide();
      }
    },
    error: function (request, status, error) {
      toastr.error("Please check your internet connection.", "Connection error");
    }
  });
}

function numberWithCommas(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

$('#m_datepicker_payment_date').datepicker({
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

$('#payment_type').select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
});

function penaltyDetails(array_penalties, isDisconnection, reconnectionFee){
  var list = "";
    for (var i = 0; i < array_penalties.length; i++) {
        var overdue = array_penalties[i].overdue;
        var total_amount = array_penalties[i].total_amount;
        var dueDate = array_penalties[i].dueDate;
        list = list + 
            "<div class='col-md-6'>"+
                "<div class='form-group form__group'>"+
                "<label>Due Date: "+dueDate+"</label>"+
                "<input value="+overdue+" class='form-control m-input overdue text-right' readonly type='text'></div>"+
              "</div>";
    }

    if(isDisconnection){
      list = list +
          "<div class='col-md-6'>"+
              "<div class='form-group form__group'>"+
              "<label>Reconnection Fee</label>"+
              "<input value="+reconnectionFee+" class='form-control m-input overdue text-right' readonly type='text'></div>"+
          "</div>";
    }
    if(list == ""){
      list = list + 
      "<div class='col-md-6'>No penalty</div>";
    }
    
    $(".penalty_details").html(list);
    $(".overdue").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
}

function getCurrentDate(){
  var today = new Date();
  var dd = String(today.getDate()).padStart(2, '0');
  var mm = String(today.getMonth() + 1).padStart(2, '0');
  var yyyy = today.getFullYear();
  return yyyy+'-'+mm+'-'+dd;
}

// document.addEventListener('contextmenu', event => event.preventDefault());
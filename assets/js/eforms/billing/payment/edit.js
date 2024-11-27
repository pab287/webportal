param_id = window.location.pathname.split("/").pop();

$("input[name=id]").val(param_id);

$.ajax({
    url: baseUrl("eforms/billing/get_payment_details/") + param_id,
    type: "GET",
    dataType: "JSON",
    success: function(data) {

        var account_details = data.account.data;

        var account_name = account_details.accountno+'|'+account_details.firstname+' '+account_details.lastname;
        var customer_name = account_details.firstname+' '+account_details.lastname;

        $(".customer_name").val(customer_name);
        $(".overdue").val(data.data.overdue);
        $(".net_payment").val(data.data.net_payment);
        $(".received_amount").val(data.data.received_amount);
        $(".payment_date").val(data.data.payment_date);
        
        vmTab1.vm_tab1 = Object.assign({}, data.data);
        initBillingSelection(account_details.id);

        setTimeout(function () {

          var newOption = new Option(account_name, account_details.id, true, true);
          $('#accountSelect').append(newOption).trigger('change');

          var newOption1 = new Option(data.data.payment_type, data.data.payment_type, true, true);
          $('#payment_type').append(newOption1).trigger('change');

          var newOption2 = new Option(data.bill.ref_no, data.bill.id, true, true);
          $('#billSelect').append(newOption2).trigger('change');
          
      }, 400);
        
    },
    error: function (request, status, error) {
      toastr.error("Please check your internet connection.", "Connection error");
    }
});

var vmTab1 = new Vue({
    el: "#updatePayment",
    data: { vm_tab1: {} },
});

$("#accountSelect").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
      url: baseUrl("eforms/billing/get_account_select_payments"),
      global: false,
      processResults: function (data) {
        return data;
      }
    }
  });

  $('#accountSelect').on('select2:select', function (e) {
    initBillingSelection(e.params.data.id);
    populate_customer_name(e.params.data.id);
    checkOverdue(e.params.data.id);
  });

  function initBillingSelection(id){
    $('#billSelect').empty().trigger("change");
    $.ajax({
      url: baseUrl("eforms/billing/get_upaid_bills_by_account_id"),
      type: 'post',
      data: { csrf_token: _csrf_hash, id: id },
      success: function (data) {
        $.each(data.results, function (i, v) {
          // Create a DOM Option and pre-select by default
          var newOption = new Option(v.text, v.id, true, true);
          // Append it to the select
          $('#billSelect').append(newOption).trigger('change');
        });
      },
      error: function (request, status, error) {
        toastr.error("Please check your internet connection.", "Connection error");
      }
    });
  }

  $('#billSelect').on('select2:select', function (e) {
    checkOverdue(e.params.data.id);
  });
  
  function checkOverdue(id) {
    $.ajax({
      url: baseUrl("eforms/billing/check_overdue"),
      type: 'post',
      data: { csrf_token: _csrf_hash, id: id },
      success: function (data) {
        $(".overdue").val(data.overdue);
        $(".net_payment").val(data.net_payment);
      },
      error: function (request, status, error) {
        toastr.error("Please check your internet connection.", "Connection error");
      }
    });
  }
  
  $('#billSelect').trigger('change.select2', function (e) {
    $.ajax({
      url: baseUrl("eforms/billing/get_bill_details"),
      type: 'post',
      data: { csrf_token: _csrf_hash, id: e.params.data.id },
      success: function (data) {
        
      },
      error: function (request, status, error) {
        toastr.error("Please check your internet connection.", "Connection error");
      }
    });
  });

  function populate_customer_name(id) {
    $.ajax({
      url: baseUrl("eforms/billing/get_customer_details"),
      type: 'post',
      data: { csrf_token: _csrf_hash, id: id },
      success: function (data) {
        $(".customer_name").val(data.firstname + " " + data.lastname);
      },
      error: function (request, status, error) {
        toastr.error("Please check your internet connection.", "Connection error");
      }
    });
  }

  $("#billSelect").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
  });

  $("#payment_type").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
  });

  $.validate({
    form : '#updatePayment',
    lang: 'en',
    onSuccess : function(form) {
        $.ajax({
            url : $(form).attr("action"),
            type: "POST",
            data: $('#updatePayment').serialize(),
            dataType: "JSON",
            success: function(data){
              if(data.status == true){
                toastr.success(data.msg, "Notification");
                window.location.replace(baseUrl("eforms/billing/payment"));
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
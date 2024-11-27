$.validate({
    form : '#frmRate',
    lang: 'en',
    onSuccess : function(form) {
        $.ajax({
            url : $(form).attr("action"),
            type: "POST",
            data: $('#frmRate').serialize(),
            dataType: "JSON",
            success: function(data){
              if(data.status == true){
                if(data.type == 'add'){ $("#frmRate input[name=id]").val(data.id); }
                toastr.success(data.msg, "Notification");
              }else{
                toastr.warning(data.msg, "Notification");
              }
          }
      });
      return false;
    },
});  

$.validate({
  form : '#frmLimitPrint',
  lang: 'en',
  onSuccess : function(form) {
      $.ajax({
          url : $(form).attr("action"),
          type: "POST",
          data: $('#frmLimitPrint').serialize(),
          dataType: "JSON",
          success: function(data){
            if(data.status == true){
              if(data.type == 'add'){ $("#frmLimitPrint input[name=id]").val(data.id); }
              toastr.success(data.msg, "Notification");
            }else{
              toastr.warning(data.msg, "Notification");
            }
        }
    });
    return false;
  },
});  

$.validate({
  form : '#frmReconnectionFee',
  lang: 'en',
  onSuccess : function(form) {
      $.ajax({
          url : $(form).attr("action"),
          type: "POST",
          data: $('#frmReconnectionFee').serialize(),
          dataType: "JSON",
          success: function(data){
            if(data.status == true){
              if(data.type == 'add'){ $("#frmReconnectionFee input[name=id]").val(data.id); }
              toastr.success(data.msg, "Notification");
            }else{
              toastr.warning(data.msg, "Notification");
            }
        }
    });
    return false;
  },
});  

$.validate({
  form : '#frmCutOffPeriod',
  lang: 'en',
  onSuccess : function(form) {
      $.ajax({
          url : $(form).attr("action"),
          type: "POST",
          data: $('#frmCutOffPeriod').serialize(),
          dataType: "JSON",
          success: function(data){
            if(data.status == true){
              if(data.type == 'add'){ $("#frmCutOffPeriod input[name=id]").val(data.id); }
              toastr.success(data.msg, "Notification");
            }else{
              toastr.warning(data.msg, "Notification");
            }
        }
    });
    return false;
  },
});  

$(document).ready(function(){
  $.ajax({
    url: baseUrl("eforms/billing/get_applied_rate"),
    data: {csrf_token: _csrf_hash},
    success: function(data){
        $("#frmRate input[name=id]").val(data.id);
        $("input[name=rate]").val(data.rate);
    }
  });

  $.ajax({
    url: baseUrl("eforms/billing/get_applied_limit"),
    data: {csrf_token: _csrf_hash},
    success: function(data){
        $("#frmLimitPrint input[name=id]").val(data.id);
        $("input[name=limit]").val(data.limit);
    }
  });

  $.ajax({
    url: baseUrl("eforms/billing/get_penalty_details"),
    data: {csrf_token: _csrf_hash},
    success: function(data){
        $("#frmPenaltyDetails input[name=id]").val(data.id);
        $("input[name=amount]").val(data.amount);
        $("select[name=type]").val(data.type);
    }
  });

  $.ajax({
    url: baseUrl("eforms/billing/get_reconnection_fee"),
    data: {csrf_token: _csrf_hash},
    success: function(data){
      $("#frmReconnectionFee input[name=id]").val(data.id);
      $("input[name=reconnection_amount]").val(data.amount);
    }
  });

  $.ajax({
    url: baseUrl("eforms/billing/get_cut_off_period"),
    data: {csrf_token: _csrf_hash},
    success: function(data){
      $("#frmCutOffPeriod input[name=id]").val(data.id);
      $("#frmCutOffPeriod input[name=day]").val(data.day);
    }
  });

  $.ajax({
    url: baseUrl("eforms/billing/get_due_date"),
    data: {csrf_token: _csrf_hash},
    success: function(data){
      $("#frmDueDate input[name=id]").val(data.id);
      $("#frmDueDate input[name=day]").val(data.day);
    }
  });
});



$.validate({
  form : '#frmPenaltyDetails',
  lang: 'en',
  onSuccess : function(form) {
      $.ajax({
          url : $(form).attr("action"),
          type: "POST",
          data: $('#frmPenaltyDetails').serialize(),
          dataType: "JSON",
          success: function(data){
            if(data.status == true){
              if(data.type == 'add'){ $("#frmPenaltyDetails input[name=id]").val(data.id); }
              toastr.success(data.msg, "Notification");
            }else{
              toastr.warning(data.msg, "Notification");
            }
        }
    });
    return false;
  },
});

$.validate({
  form : '#frmDueDate',
  lang: 'en',
  onSuccess : function(form) {
      $.ajax({
          url : $(form).attr("action"),
          type: "POST",
          data: $('#frmDueDate').serialize(),
          dataType: "JSON",
          success: function(data){
            if(data.status == true){
              if(data.type == 'add'){ $("#frmDueDate input[name=id]").val(data.id); }
              toastr.success(data.msg, "Notification");
            }else{
              toastr.warning(data.msg, "Notification");
            }
        }
    });
    return false;
  },
});
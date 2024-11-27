var getUrlParameter = function getUrlParameter(sParam){
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
        for (i = 0; i < sURLVariables.length; i++){
                sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] === sParam){
                    return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
};

param_id = getUrlParameter('id');

$.ajax({
    url : baseUrl("eforms/cash_advance/print_cash_advance_details?id=") + param_id,
    type: "GET",
    dataType: "JSON",
    success: function(data)
    {
        vmTab1.vm_tab1 = Object.assign({}, data);
    }
  });

  
var vmTab1 = new Vue({
    el: "#print_cash_advance",
    data: { vm_tab1: {} }
  });


  function view_last_vale(emp,id,date)
      {
        var getUrlParameter = function getUrlParameter(sParam){var sPageURL = decodeURIComponent(window.location.search.substring(1)),sURLVariables = sPageURL.split('&'),sParameterName,i;for (i = 0; i < sURLVariables.length; i++){sParameterName = sURLVariables[i].split('=');if (sParameterName[0] === sParam){return sParameterName[1] === undefined ? true : sParameterName[1];}}};
        $.ajax({
          url : "<?php echo site_url('Cash_advance/ajax_view_last_vale')?>",
          type: "POST",
          dataType: "JSON",
          data: { emp : emp, id : id, date : date },
          success: function(data)
          {  
            if (data.data.length > 0) {
              $('#last_vale').append(parseFloat(data.data[0]).toLocaleString("en-US", {style: "currency", currency: "PHP"}));
              $('#last_vale2').append(parseFloat(data.data[0]).toLocaleString("en-US", {style: "currency", currency: "PHP"}));  
            } else {
              $('#last_vale').append('None');
              $('#last_vale2').append('None');
            }
            print_ca();
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
            alert('Error: "ajax_view_last_vale"');
          }
        });
      }
    window.print();
    window.close();
  
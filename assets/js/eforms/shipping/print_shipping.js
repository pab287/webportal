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

var tblShipping = $("#table-shipping-content").DataTable({
  dom: '<"toolbar">frtlip',
serverSide: true,
  processing: true,
  bPaginate: false,
  bInfo: false,
  ajax: {
  url: baseUrl("eforms/shipping/shipping_content_table/")  + param_id,
  type: "post",
      dataType: "json",
      data: function(d){
    d.csrf_token = _csrf_hash
  }
  },
  searching: false,
  columns: [
      { data: "stock_code"},
      { data: "quantity", render: function(data, type, row, meta){return row.quantity+" "+row.uom; }},
      { data: "description"},
      { data: "item_purpose" },
  ],
});

$.ajax({
    url : baseUrl("eforms/shipping/print_shipping_details?id=") + param_id,
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


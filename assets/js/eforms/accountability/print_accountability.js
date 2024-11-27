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

var tblContent = $("#content").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    bPaginate: false,
    bInfo: false,
    ajax: {
    url: baseUrl("eforms/accountability/content_body_detail/")  + param_id,
    type: "post",
        dataType: "json",
        data: function(d){
      d.csrf_token = _csrf_hash
    }
    },
    searching: false,
    columns: [
        { data: "asset_code"},
        { data: "description"},
        { data: "", render: function(data, type, row, meta){return model(row.brand, row.modelno)}},
        { data: "amount" },
    ],
    footerCallback: function () {
        var api = this.api();
        $("#content tfoot tr").append("<td></td><td></td><td class='text-right'><b>TOTAL: </b></td><td class='text-right'>"+(api.column( 3, {page:'current'} ).data().sum()).toFixed(2)+"</td>");
    },
});

document.getElementById('content').createTFoot().insertRow(0);

function model($brand, $model){
    return $brand+" / "+$model;
}
  
$.ajax({
    url: baseUrl("eforms/accountability/content_detail/")+param_id,
    type: "get",
    success: function(data){
        vmTab1.vm_tab1 = Object.assign({}, data);
    }
})

var vmTab1 = new Vue({
    el: "#print_accountability",
    data: { vm_tab1: {} }
});


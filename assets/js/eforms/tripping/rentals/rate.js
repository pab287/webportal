var dtRates = $('#table-rates').DataTable( {
    serverSide: true,
    processing: true,
    ajax: baseUrl("eforms/tripping/get_rental_rate_collection"),
    searching: true,
    columns: [
        { data: "plateno"},
        { data: "rate"},
        { data: null},
    ],
    columnDefs: [
      { targets: [2] , width: "5%"},
      {
          data: null,
          defaultContent: "",
          targets: -1,
          orderable: false,
          render: function ( data, type, row, meta ) { return itemDatatableActions(row.id); },
      }
    ]
  });


function itemDatatableActions($id){
  if($id){
		var _actionButton ="";
			_actionButton += "<a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' data-id='"+$id+"'><i class='la la-pencil-square'></i></a>";
		return _actionButton;
	}else{ return false; }
}

$("#driversUnitSelect").select2({
    placeholder: 'SELECT Vehicle',
    width: '100%',
    ajax: {
      url: baseUrl("eforms/tripping/get_drivers_vehicle"),
      global: false,
      processResults: function (data) {
        return data;
      }
  
    }
});


$.validate({
    form: '#frmNewRate',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
          url: $("#frmNewRate").attr('action'),
          type: "POST",
          data: $('#frmNewRate').serialize(),
          dataType: "JSON",
          success: function (data) {
                if (data.status) {
                    toastr.success(data.msg, "Notification");
                    dtRates.ajax.reload();
                    $('#frmNewRate')[0].reset();
                } else {
                    toastr.error(data.msg, "Notification");
                }
          }
        });
        return false;
    },
  });

  $("#table-rates").on("click",".btnEditItem",function(){
    var id = $(this).attr("data-id");  

    $.ajax({
        url: baseUrl("eforms/tripping/get_rental_rate_data"),
        type: "POST",
        data: {id: id, csrf_token :_csrf_hash,},
        success: function(response){
            $("#frmEditRate input[name=id]").val(response.id);
            $("#frmEditRate input[name=rate]").val(response.rate);

            $("#driversUnitSelectU").select2({
                placeholder: 'SELECT Vehicle',
                width: '100%',
                ajax: {
                  url: baseUrl("eforms/tripping/get_drivers_vehicle"),
                  global: false,
                  processResults: function (data) {
                    return data;
                  }
              
                }
            });

            var newOption2 = new Option(response.plateno+" | "+response.name, response.rate_id, true, true);
            $('#frmEditRate #driversUnitSelectU').append(newOption2).trigger('change');


        }
    });
  
    $("#m_editRate").modal("show");
  });

  
$.validate({
    form: '#frmEditRate',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
          url: $("#frmEditRate").attr('action'),
          type: "POST",
          data: $('#frmEditRate').serialize(),
          dataType: "JSON",
          success: function (data) {
                if (data.status) {
                    toastr.success(data.msg, "Notification");
                    dtRates.ajax.reload();
                    $('#frmEditRate')[0].reset();
                } else {
                    toastr.error(data.msg, "Notification");
                }
          }
        });
        return false;
    },
  });
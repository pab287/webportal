var dtRates = $('#table-rates').DataTable( {
    serverSide: true,
    processing: true,
    ajax: baseUrl("eforms/tripping/get_rate_collection"),
    searching: true,
    columns: [
        { data: "from"},
        { data: "to"},
        { data: "rate"},
        { data: "project_name"},
        { data: "driver_type"},
        { data: "status",render: function (data) {return renderStatusHtml(data)}},
        { data: null},
    ],
    columnDefs: [
      { targets: [6] , width: "5%"},
      {
          data: null,
          defaultContent: "",
          targets: -1,
          orderable: false,
          render: function ( data, type, row, meta ) { return itemDatatableActions(row.id); },
      }
    ]
  });

function renderStatusHtml(data){
    switch(data){
        case "1":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>Active</strong></div>';
        break;
        default:
            return '<div class="m-badge m-badge--metal text-white m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
        break;
    }
}

function itemDatatableActions($id){
  if($id){
		var _actionButton ="";
			_actionButton += "<a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' data-id='"+$id+"'><i class='la la-pencil-square'></i></a>";
		return _actionButton;
	}else{ return false; }
}

$("#departureSelect").select2({
    placeholder: 'SELECT Departure',
    width: '100%',
    ajax: {
      url: baseUrl("eforms/tripping/get_routes"),
      global: false,
      processResults: function (data) {
        return data;
      }
  
    }
});

$("#destinationSelect").select2({
    placeholder: 'SELECT Destination',
    width: '100%',
    ajax: {
      url: baseUrl("eforms/tripping/get_routes"),
      global: false,
      processResults: function (data) {
        return data;
      }
  
    }
});

$("#projectSelect").select2({
    placeholder: 'SELECT Project',
    width: '100%',
    ajax: {
      url: baseUrl("eforms/tripping/get_projects"),
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
                
              toastr.success(data.message, "Rate updated successfully!");
              dtRates.ajax.reload();
              $('#frmNewRate')[0].reset();
              } else {
                alert('Error get data from ajax');
              }
          }
        });
        return false;
    },
  });

  $("#table-rates").on("click",".btnEditItem",function(){
    var id = $(this).attr("data-id");

    $("#departureSelectU").select2({
        placeholder: 'SELECT Departure',
        width: '100%',
        ajax: {
          url: baseUrl("eforms/tripping/get_routes"),
          global: false,
          processResults: function (data) {
            return data;
          }
      
        }
    });
    
    $("#destinationSelectU").select2({
        placeholder: 'SELECT Destination',
        width: '100%',
        ajax: {
          url: baseUrl("eforms/tripping/get_routes"),
          global: false,
          processResults: function (data) {
            return data;
          }
      
        }
    });
    
    $("#projectSelectU").select2({
        placeholder: 'SELECT Project',
        width: '100%',
        ajax: {
          url: baseUrl("eforms/tripping/get_projects"),
          global: false,
          processResults: function (data) {
            return data;
          }
      
        }
    });
  
    $.ajax({
        url: baseUrl("eforms/tripping/get_rate_data"),
        type: "POST",
        data: {id: id, csrf_token :_csrf_hash,},
        success: function(response){
            $("#frmEditRate input[name=id]").val(response.id);
            $("#frmEditRate input[name=rate]").val(response.rate);
            $("#frmEditRate select[name=typeSelect]").val(response.driver_type);

            var newOption = new Option(response.todesc, response.to, true, true);
            $('#frmEditRate #destinationSelectU').append(newOption).trigger('change');

            var newOption1 = new Option(response.fromdesc, response.from, true, true);
            $('#frmEditRate #departureSelectU').append(newOption1).trigger('change');

            var newOption2 = new Option(response.project_name, response.project_id, true, true);
            $('#frmEditRate #projectSelectU').append(newOption2).trigger('change');
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
                
              toastr.success(data.message, "Rate updated successfully!");
              dtRates.ajax.reload();
              } else {
                alert('Error get data from ajax');
              }
          }
        });
        return false;
    },
  });
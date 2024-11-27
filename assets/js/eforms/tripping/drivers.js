var dtDrivers = $('#table-drivers').DataTable( {
    serverSide: true,
    processing: true,
    ajax: baseUrl("eforms/tripping/get_driver_collection/"),
    searching: true,
    columns: [
        { data: "code"},
        { data: "drivername"},
        { data: "description"},
        { data: "type"},
        { data: "status",render: function (data) {return renderStatusHtml(data)}},
        { data: null},
    ],
    columnDefs: [
      { targets: [5] , width: "5%"},
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

$.validate({
    form: '#frmNewDriver',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
          url: $("#frmNewDriver").attr('action'),
          type: "POST",
          data: $('#frmNewDriver').serialize(),
          dataType: "JSON",
          success: function (data) {
              if (data.status) {
              toastr.success(data.msg, "Notification");
              dtDrivers.ajax.reload();
              $('#frmNewDriver')[0].reset();
              } else {
                toastr.error(data.msg, "Notification");
              }
          }
        });
        return false;
    },
});

$("#driversSelect").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    minimumInputLength: 3,
    ajax: {
      url: baseUrl("eforms/tripping/get_drivers_collection"),
      global: false,
      processResults: function (data) {
        return data;
      }
  
    }
});

$("#typeSelect").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
});

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



$("#table-drivers").on("click",".btnEditItem",function(){
  var id = $(this).attr("data-id");

  $.ajax({
      url: baseUrl("eforms/tripping/get_driver_data"),
      type: "POST",
      data: {id: id, csrf_token :_csrf_hash,},
      success: function(response){
        $("#frmEditDriver input[name=id]").val(response.id);
        $("#frmEditDriver input[name=code]").val(response.code);
        $("#frmEditDriver select[name=type]").val(response.type);
        var newOption = new Option(response.drivername, response.id, true, true);
        $('#frmEditDriver #driversSelect').append(newOption).trigger('change');
        var newOption1 = new Option(response.description, response.vehicle_id, true, true);
        $('#frmEditDriver #driversUnitSelect').append(newOption1).trigger('change');


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


      $("#driversSelectU").select2({
          placeholder: 'SELECT AN OPTION',
          width: '100%',
          ajax: {
            url: baseUrl("eforms/tripping/get_drivers_collection"),
            global: false,
            processResults: function (data) {
              return data;
            }
        
          }
      });


      }
  });

  $("#m_editDriver").modal("show");
});

$.validate({
  form: '#frmEditDriver',
  lang: 'en',
  onSuccess: function (form) {
      $.ajax({
        url: $("#frmEditDriver").attr('action'),
        type: "POST",
        data: $('#frmEditDriver').serialize(),
        dataType: "JSON",
        success: function (data) {
            if (data.status) {
            toastr.success(data.msg, "Notification");
            dtDrivers.ajax.reload();
            $("#m_newDriver").modal("hide");
            } else {
              toastr.error(data.msg, "Notification");
            }
        }
      });
      return false;
  },
});
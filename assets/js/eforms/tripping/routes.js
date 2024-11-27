var dtRoutes = $('#table-routes').DataTable( {
    serverSide: true,
    processing: true,
    ajax: baseUrl("eforms/tripping/get_route_collection"),
    searching: true,
    columns: [
        { data: "code"},
        { data: "name"},
        { data: "status",render: function (data) {return renderStatusHtml(data)}},
        { data: null},
    ],
    columnDefs: [
      { targets: [3] , width: "5%"},
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
    form: '#frmNewRoute',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
          url: $("#frmNewRoute").attr('action'),
          type: "POST",
          data: $('#frmNewRoute').serialize(),
          dataType: "JSON",
          success: function (data) {
              if (data.status) {
                toastr.success(data.msg, "Notification");
                dtRoutes.ajax.reload();
                $('#frmNewRoute')[0].reset();
              } else {
                toastr.error(data.msg, "Notification");
              }
          }
        });
        return false;
    },
  });

  $("#table-routes").on("click",".btnEditItem",function(){
    var id = $(this).attr("data-id");
  
    $.ajax({
        url: baseUrl("eforms/tripping/get_route_data"),
        type: "POST",
        data: {id: id, csrf_token :_csrf_hash,},
        success: function(response){
            $("#frmEditRoute input[name=id]").val(response.id);
            $("#frmEditRoute input[name=code]").val(response.code);
            $("#frmEditRoute input[name=name]").val(response.name);
        }
    });
  
    $("#m_editRoute").modal("show");
  });

  $.validate({
    form: '#frmEditRoute',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
          url: $("#frmEditRoute").attr('action'),
          type: "POST",
          data: $('#frmEditRoute').serialize(),
          dataType: "JSON",
          success: function (data) {
              if (data.status) {
                toastr.success(data.msg, "Notification");
                dtRoutes.ajax.reload();
              } else {
                toastr.error(data.msg, "Notification");
              }
          }
        });
        return false;
    },
  });
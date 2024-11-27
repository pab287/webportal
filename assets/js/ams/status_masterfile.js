var search_val = "";

//init datatable
var tblStatus = $("#table-status").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
	processing: true,
    ajax: {
		url: baseUrl("ams/assets/get_datatable_status_collection"),
		type: "post",
		dataType: "json",
		data: function(d){
			d.csrf_token = _csrf_hash,
			d.search['value'] = search_val
		}
	},
	searching: false,
    columns: [
		{ data: "name"},
		{ data: "category"},
        { data: null, width: "30%", className: "text-center" },
	],
	columnDefs: [{
		data: null,
		defaultContent: "",
		targets: -1,
		orderable: false,
		render: function ( data, type, row, meta ) { return itemDatatableActions(row.id); },
	},
	{ 
		targets: "_all", 
		defaultContent: "", 
	}],
}); 

function itemDatatableActions($id){
    if($id){
		var _actionButton ="";
			_actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' data-toggle='modal' data-target='#mdl_status_edit'><i class='la la-edit'></i></button>";				
			_actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnCancel' data-id='"+$id+"'><i class='la la-file-archive-o'></i></button>";				
		
		return _actionButton;
	}else{ return false; }
}

//validate status form
$.validate({
	form : '#frm_status_new',
	lang: 'en',
	onSuccess : function(form) {
			$.ajax({
				url: form[0].action,
				type: "POST",
				dataType: "json",
				data: $("#frm_status_new").find("input,select").serialize(),
				beforeSend: function(){
					$(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
				},
				success: function(data){
					if(data.status){
						toastr.success(data.toastr_msg, "Notification", 5000);
						tblStatus.ajax.reload();
						$("#mdl_status_new").modal("hide");
					}else{
						toastr.error(data.toastr_msg, "Notification", 5000);
					}
					$(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
				}
			});	
		return false;
	},
});

//init select2 status category3
$("#select2_category").select2({
	placeholder: 'Select option',
	width: '100%',
	ajax: {
	  url: baseUrl("ams/assets/get_status_category_collection"),
	  processResults: function (data) {
		return data;
	  }
	}
  });
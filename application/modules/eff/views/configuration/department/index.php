<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
		<!--begin::Portlet-->
		<div class="m-portlet m-portlet--mobile">
			<div class="m-portlet__head">
				<div class="m-portlet__head-caption">
					<div class="m-portlet__head-title">
						<h3 class="m-portlet__head-text">Department	<small>Configuration</small></h3>
					</div>
				</div>
			</div>
			<div class="m-portlet__body">
				<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
					<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline" id="table-department" width="100%">
						<thead>
							<tr>
								<th>Code</th>
								<th>Description</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
		<!--end::Portlet-->
		</div>
	</div>
</div>

<div class="modal fade" id="modal-department" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<form action="<?php echo base_url('eff/department/add_department')?>" method="POST" id="form-department">
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<div class="modal-header">
				<h5 class="modal-title">New Department</h5>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-control-label">Code *</label>
					<input type="text" name="code" class="form-control inptName" autocomplete=off data-validation="required" />
				</div>
				<div class="form-group">
					<label class="form-control-label">Description *</label>
					<input type="text" name="description" class="form-control inptLabel" autocomplete=off data-validation="required" />
				</div>
				<div class="form-group">
					<label for="">Status</label>
					<div class="m-radio-inline">
						<label class="m-radio"><input id="status1" type="radio" name="status" value="1" checked>Active<span></span></label>
						<label class="m-radio"><input id="status0" type="radio" name="status" value="0">Inactive<span></span></label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary btnSave btn-submit">Save</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-department_edit" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content" id="edit_dept">
			<form action="<?php echo base_url('eff/department/update_department')?>" method="POST" id="form-department_edit">
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<input type="hidden" name="id" v-model="post.id" />
			<div class="modal-header">
				<h5 class="modal-title">Edit Department</h5>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-control-label">Code *</label>
					<input type="text" name="code" class="form-control inptName" autocomplete=off data-validation="required" v-model="post.code" />
				</div>
				<div class="form-group">
					<label class="form-control-label">Description *</label>
					<input type="text" name="description" class="form-control inptLabel" autocomplete=off data-validation="required" v-model="post.description" />
				</div>
				<div class="form-group">
					<label for="">Status</label>
					<div class="m-radio-inline">
						<label class="m-radio"><input id="status1" type="radio" name="status" value="1" checked v-model="post.status">Active<span></span></label>
						<label class="m-radio"><input id="status0" type="radio" name="status" value="0" v-model="post.status">Inactive<span></span></label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary btnSave btn-submit">Save</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-department_delete" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<input type="hidden" id="department_id" value="0" />
			<div class="modal-header">
				<h5 class="modal-title">Remove Department</h5>
			</div>
			<div class="modal-body"><i class="la la-warning"></i>  Are you sure you want to remove this department? </div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary btn-submit-delete btnDelete">Delete</button>
				<button class="btn btn-danger" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>

<script>
var _currentActions = {};
var _csrf_token = "<?php echo $this->security->get_csrf_token_name(); ?>";
var _csrf_hash = "<?php echo $this->security->get_csrf_hash(); ?>";
var _tableDepartment = $("#table-department");
var _modalEditDepartment = $("#modal-department_edit");
var _modalDeleteDepartment = $("#modal-department_delete");

var _dtDepartment = $("#table-department").DataTable({
	dom: '<"toolbar">frtlip',
	serverSide: true,
	processing: true,
	ajax: {
		url: "<?php echo base_url('eff/department/get_department_list'); ?>",
		type: "post",
		dataType: "json",
		data: {  [_csrf_token] : _csrf_hash }
	}, columns: [
		{ data: "code", width: "15%" },
		{ data: "description", width: "72%" },
		{ data: "status", width: "5%" },
		{ data: null, width: "8%"},
	], columnDefs: [{
		data: null,
		defaultContent: "",
		targets: -1,
		orderable: false,
		render: function ( data, type, row, meta ) { return departmentDatatableActions(row.id); },
	},{
		data: "status",
		defaultContent: "",
		targets: 2,
		orderable: false,
		className: "dt-column-center text-center",
		render: function ( data, type, row, meta ) { return departmentDatatableStatus(row.status); },
	}, { 
		targets: "_all", 
		defaultContent: "", 
	}], initComplete: function(settings, json){
	}
});

$.validate({
	form : '#form-department',
	lang: 'en',
	onSuccess : function(form) {
		var _url = form[0].action;
		var _data = jQuery(form[0]).serialize();
		var _btnSubmit = $(form[0]).find(".btn-submit");
		
		$.ajax({
			url: _url,
			type: "POST",
			data: _data,
			beforeSend: function(){
				if(typeof _btnSubmit !== "undefined"){ _btnSubmit.addClass("m-btn--custom m-loader m-loader--light m-loader--right"); }
			},
			success: function(data){
				if(data.response){
					_dtDepartment.ajax.reload();
					form[0].reset();
					toastr.success(data.toastr_msg, "Added Department", 5000);
					$("#modal-department").modal("hide");
				}else{
					toastr.error(data.toastr_msg, "Error Department", 5000);
				}
				if(typeof _btnSubmit !== "undefined"){ _btnSubmit.removeClass("m-btn--custom m-loader m-loader--light m-loader--right"); }
			}
		});
		return false;
	}
});

var _htmlContent = '<button id="department-new" type="button" class="m-portlet__nav-link btn m-btn--square btn-success btnNew"  data-toggle="modal" data-target="#modal-department"><i class="fa fa-plus"></i> New </button>';
$("div.toolbar").html(_htmlContent);

function departmentDatatableActions($id){
	if($id){
		_actionButton = "";
		_actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnEdit btnEditDepartment' data-id='"+$id+"'><i class='la la-edit'></i></button>";				
		_actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnDelete btnRemoveDepartment' data-id='"+$id+"'><i class='la la-trash'></i></button>";				
		if(!_actionButton){ _actionButton = "---"; }
		return _actionButton;
	}else{ return false; }
}

function departmentDatatableStatus($isActive){
	var _html = "";
	if($isActive == 1){ _html = "<span class='btn btn-info m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-check'></i></span>"; }
	else{ _html = "<span class='btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-remove'></i></span>"; }
	return _html;
}

jQuery(document).on("click", ".btnRemoveDepartment", function(){
	var _self = $(this);
	var _dataId = _self.data("id");
	var inpt = _modalDeleteDepartment.find("#department_id");
	if(typeof inpt !== "undefined" && inpt.length > 0){
		inpt.val(_dataId);
		_modalDeleteDepartment.modal("show");
	}
});

jQuery(document).on("click", ".btnEditDepartment", function(){
	var _self = $(this);
	var _dataId = _self.data("id");
	$.ajax({
		url: "<?php echo site_url('eff/department/get_department_data'); ?>",
		type: "post",
		dataType: "json",
		data: { id: _dataId, [_csrf_token] : _csrf_hash },
		success: function(json){
			if(json.response){
				vmDepartment.post = json.data;
				_modalEditDepartment.modal("show");
			}
		}
	});
});

jQuery(document).on("click", ".btn-submit-delete", function(){
	var _btnSubmit = jQuery(this);
	var _dataId = jQuery(this).parent(".modal-footer").parent(".modal-content").children("input#department_id").val();
	if(typeof _dataId !== "undefined"){
		jQuery.ajax({
			url: "<?php echo site_url('eff/department/remove_department'); ?>",
			type: "post",
			dataType: "json",
			data: { id: _dataId, [_csrf_token] : _csrf_hash },
			beforeSend: function(){
				if(typeof _btnSubmit !== "undefined"){
					if(!_btnSubmit.hasClass("m-btn--custom m-loader m-loader--light m-loader--right")){
						_btnSubmit.addClass("m-btn--custom m-loader m-loader--light m-loader--right");
					}
				}
			},
			success: function(json){
				if(json.response){
					toastr.success(json.toastr_msg, "Remove Department", 5000);
					_modalDeleteDepartment.modal("hide");
					_dtDepartment.ajax.reload();
				}else{ toastr.error(json.toastr_msg, "Error removing department", 5000); }
				if(typeof _btnSubmit !== "undefined"){
					if(_btnSubmit.hasClass("m-btn--custom m-loader m-loader--light m-loader--right")){
						_btnSubmit.removeClass("m-btn--custom m-loader m-loader--light m-loader--right");							
					}
				}
			}
		});			
	}
});

jQuery(document).ready(function(){
	_dtDepartment;
});

var _items = {};
var vmDepartment = new Vue({
	el: "#edit_dept",
	data: { post: _items },
	mounted: function(){
		$.validate({
			form : '#form-department_edit',
			lang: 'en',
			onSuccess : function(form) {
				var _url = form[0].action;
				var _data = jQuery(form[0]).serialize();
				var _btnSubmit = $(form[0]).find(".btn-submit");
				
				$.ajax({
					url: _url,
					type: "POST",
					data: _data,
					beforeSend: function(){
						if(typeof _btnSubmit !== "undefined"){ _btnSubmit.addClass("m-btn--custom m-loader m-loader--light m-loader--right"); }
					},
					success: function(data){
						if(data.response){
							_dtDepartment.ajax.reload();
							form[0].reset();
							toastr.success(data.toastr_msg, "Updated Department", 5000);
							$("#modal-department_edit").modal("hide");
						}else{
							toastr.error(data.toastr_msg, "Error Department", 5000);
						}
						if(typeof _btnSubmit !== "undefined"){ _btnSubmit.removeClass("m-btn--custom m-loader m-loader--light m-loader--right"); }
					}
				});
				return false;
			}
		});
	}
});
</script>
<?php $actions = $this->core_layout->getCurrentActions(); ?>
<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Manage Late
							</h3>
						</div>
					</div>
				</div>
				<div class="m-portlet__body">
					<!--begin::Section-->
					<div class="m-section">
						<div class="m-section__content">
							<!--begin: Datatable -->
							<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
								<table class="table table-striped table-bordered" id="table_lates">
									<col width="45%">
									<col width="10%">
									<col width="10%">
									<col width="10%">
									<col width="10%">
									<col width="5%">
									<col width="10%">
									<thead>
										<tr>
											<th>Name</th>
											<th>AM Start</th>
											<th>AM End</th>
											<th>PM Start</th>
											<th>PM End</th>
											<th>Status</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										
									</tbody>
								</table>
							</div>
							<!--end: Datatable -->
						</div>
					</div>
					<!--end::Section-->
				</div>
				<!--end::Form-->
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Custom Late Settings
							</h3>
						</div>
					</div>
				</div>
				<div class="m-portlet__body">
					<!--begin::Section-->
					<div class="m-section">
						<div class="m-section__content">
							<!--begin: Datatable -->
							<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
								<table class="table table-striped table-bordered" id="table_custom-lates">
									<col width="10%">
									<col width="27%">
									<col width="10%">
									<col width="10%">
									<col width="10%">
									<col width="10%">
									<col width="10%">
									<col width="5%">
									<col width="8%">
									<thead>
										<tr>
											<th>biometric No</th>
											<th>Name</th>
											<th>Weekday</th>
											<th>AM Start</th>
											<th>AM End</th>
											<th>PM Start</th>
											<th>PM End</th>
											<th>Status</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										
									</tbody>
								</table>
							</div>
							<!--end: Datatable -->
						</div>
					</div>
					<!--end::Section-->
				</div>
				<!--end::Form-->
			</div>
		</div>
	</div>
</div>

<!-- New Late begin::Modal-->
<div class="modal fade" id="modal-new" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" >
					New 
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="form_late" action="<?php echo site_url('gcctime/Lates/newLate');?>" method="POST">
				<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<div class="form-group">	
					<label class="form-control-label">
						Name:
					</label>
					<input type="text" name="name" class="form-control" data-validation="required">
				</div>
				<div class="form-group">	
					<label class="form-control-label">	
						AM Start:
					</label>
					<input type="text" name="am_start" class="form-control" id="am_start" data-validation="required">
				</div>
				<div class="form-group">	
					<label class="form-control-label">
						AM End:
					</label>
					<input type="text" name="am_end" class="form-control" id="am_end" data-validation="required">
				</div>
				<div class="form-group">	
					<label class="form-control-label">
						PM Start:
					</label>
					<input type="text" name="pm_start" class="form-control" id="pm_start" data-validation="required">
				</div>
				<div class="form-group">	
					<label class="form-control-label">
						PM End:
					</label>
					<input type="text" name="pm_end" class="form-control" id="pm_end" data-validation="required">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary m-btn close" data-dismiss="modal">
					Close
				</button>
				<button type="submit" class="btn btn-brand m-btn btn-submit-late btnSave">
					Submit
				</button>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-custom_new" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					New Custom Late Settings
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="form-custom_late" action="<?php echo site_url('gcctime/lates/add_custom_lates'); ?>" method="POST">
				<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<div class="form-group">	
					<label class="form-control-label">
						Personnel:
					</label>
					<select id="personnel_id" name="personnel_id" class="form-control select2" data-validation="required">
						<option></option>
					</select>
				</div>
				<div class="form-group">	
					<label class="form-control-label">	
						Weekday:
					</label>
					<select id="weekday" name="weekday" class="form-control select2" data-validation="required">
						<option></option>
						<option value="monday">Monday</option>
						<option value="tuesday">Tuesday</option>
						<option value="wednesday">Wednesday</option>
						<option value="thursday">Thursday</option>
						<option value="friday">Friday</option>
						<option value="saturday">Saturday</option>
					</select>
				</div>
				<div class="form-group row">
					<div class="col-md-6">
						<label class="form-control-label">	
							AM Start:
						</label>
						<input type="text" name="am_start" class="form-control" id="am_start" data-validation="required" autocomplete="off" value="0:00">
					</div>
					<div class="col-md-6">	
						<label class="form-control-label">
							AM End:
						</label>
						<input type="text" name="am_end" class="form-control" id="am_end" data-validation="required" autocomplete="off" value="0:00">
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-6">	
						<label class="form-control-label">
							PM Start:
						</label>
						<input type="text" name="pm_start" class="form-control" id="pm_start" data-validation="required" autocomplete="off" value="0:00">
					</div>
					<div class="col-md-6">	
						<label class="form-control-label">
							PM End:
						</label>
						<input type="text" name="pm_end" class="form-control" id="pm_end" data-validation="required" autocomplete="off" value="0:00">
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-6">
						<label class="form-control-label">Status</label>
						<div class="m-radio-inline">
							<label class="m-radio"><input id="is_active1" type="radio" name="is_active" value="1" checked="">Active<span></span></label>
							<label class="m-radio"><input id="is_active0" type="radio" name="is_active" value="0">Inactive<span></span></label>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary m-btn close" data-dismiss="modal">
					Close
				</button>
				<button type="submit" class="btn btn-brand m-btn btn-submit-late btnSave">
					Submit
				</button>
			</div>
			</form>
		</div>
	</div>
</div>
<!-- New Late end::Modal-->
<div class="modal fade" id="modal-custom_edit" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					Edit Custom Late Settings
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="form-custom_edit" action="<?php echo site_url('gcctime/lates/update_custom_lates'); ?>" method="POST">
				<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<input type="hidden" name="id" class="form-control" data-validation="required">
				
				<div class="form-group">	
					<label class="form-control-label">	
						Weekday:
					</label>
					<select id="weekday" name="weekday" class="form-control select2" data-validation="required">
						<option></option>
						<option value="monday">Monday</option>
						<option value="tuesday">Tuesday</option>
						<option value="wednesday">Wednesday</option>
						<option value="thursday">Thursday</option>
						<option value="friday">Friday</option>
						<option value="saturday">Saturday</option>
					</select>
				</div>
				<div class="form-group row">
					<div class="col-md-6">
						<label class="form-control-label">	
							AM Start:
						</label>
						<input type="text" name="am_start" class="form-control" id="am_start" data-validation="required" autocomplete="off" value="0:00">
					</div>
					<div class="col-md-6">	
						<label class="form-control-label">
							AM End:
						</label>
						<input type="text" name="am_end" class="form-control" id="am_end" data-validation="required" autocomplete="off" value="0:00">
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-6">	
						<label class="form-control-label">
							PM Start:
						</label>
						<input type="text" name="pm_start" class="form-control" id="pm_start" data-validation="required" autocomplete="off" value="0:00">
					</div>
					<div class="col-md-6">	
						<label class="form-control-label">
							PM End:
						</label>
						<input type="text" name="pm_end" class="form-control" id="pm_end" data-validation="required" autocomplete="off" value="0:00">
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-6">
						<label class="form-control-label">Status</label>
						<div class="m-radio-inline">
							<label class="m-radio"><input id="is_active1" type="radio" name="is_active" value="1" checked="">Active<span></span></label>
							<label class="m-radio"><input id="is_active0" type="radio" name="is_active" value="0">Inactive<span></span></label>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary m-btn close" data-dismiss="modal">
					Close
				</button>
				<button type="submit" class="btn btn-brand m-btn btn-submit-late btnSave">
					Submit
				</button>
			</div>
			</form>
		</div>
	</div>
</div>
<!-- Edit Late end::Modal-->

<!-- Edit Late begin::Modal-->
<div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" >
					Edit Late
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="form_edit_late" action="<?php echo site_url("gcctime/Lates/updatelate");?>" method="POST">
				<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
					<input type="hidden" name="id" class="form-control" data-validation="required">
					<div class="form-group">	
					<label class="form-control-label">
						Name:
					</label>
					<input type="text" name="name" class="form-control" data-validation="required">
				</div>
				<div class="form-group">	
					<label class="form-control-label">	
						AM Start:
					</label>
					<input type="text" name="am_start" class="form-control" id="am_start" data-validation="required">
				</div>
				<div class="form-group">	
					<label class="form-control-label">
						AM End:
					</label>
					<input type="text" name="am_end" class="form-control" id="am_end" data-validation="required">
				</div>
				<div class="form-group">	
					<label class="form-control-label">
						PM Start:
					</label>
					<input type="text" name="pm_start" class="form-control" id="pm_start" data-validation="required">
				</div>
				<div class="form-group">	
					<label class="form-control-label">
						PM End:
					</label>
					<input type="text" name="pm_end" class="form-control" id="pm_end" data-validation="required">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary close" data-dismiss="modal">
					Cancel
				</button>
				<button type="submit" class="btn btn-success btm-submit-edit btnUpdate">
					Submit
				</button>
			</form>
			</div>
		</div>
	</div>
</div>
<!-- Edit Late end::Modal-->

<!-- Delete begin::Modal-->
<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" >
					Delete Late
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
				</button>
			</div>
			<div class="modal-body">
				<i class="la la-warning"></i>  Are you sure you want to delete this late? 
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary close" data-dismiss="modal">
					Cancel
				</button>
				<button type="submit" class="btn btn-danger btm-submit-delete btnDelete" data-id="">
					Delete
				</button>
			</div>
		</div>
	</div>
</div>
<!--Delete end::Modal-->
<div class="modal fade" id="modal-assigned" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form id="form-assigned">
			<input id="shift_id" type="hidden" name="id" v-model="items.id" value="0" />
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<div class="modal-header">
				<h5 class="modal-title">Assigned Employee List</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
			<table id="table_assigned" class="table table-striped table-bordered" style="width:100% !important;">
					<thead>
						<th>Name</th>
					</thead>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				
			</div>
			</form>
		</div>
	</div>
</div>
<!-- Department List begin::Modal-->
<div class="modal fade" id="modal-select-dept" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" >
					Select Department
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="hidden" name="selected-dept-id" id="selected-dept-id">
				<table id="table_dept_select" class="table table-striped table-bordered" style="width:100%">
					<col width="2%">
					<col width="93%">
					<col width="5%">
					<thead>
						<th><input type="checkbox" id="checkAll"></th>
						<th>Name</th>
						<th>Action</th>
					</thead>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Cancel
				</button>
				<!--button type="button" class="btn btn-info btn-submit-multi-dept" >
					Submit
				</button-->
				<div class="m-dropdown m-dropdown--inline  m-dropdown--arrow" data-dropdown-toggle="click">
					<a href="#" class="m-dropdown__toggle btn btn-success dropdown-toggle btnMass_action">
						Action
					</a>
					<div class="m-dropdown__wrapper">
						<span class="m-dropdown__arrow m-dropdown__arrow--left"></span>
						<div class="m-dropdown__inner">
							<div class="m-dropdown__body">
								<div class="m-dropdown__content">
									<ul class="m-nav">
										<li class="m-nav__item">
											<a href="javascript:void(0)" class="m-nav__link btn-submit-multi-dept btnSelect">
												<span class="m-nav__link-text">
													Select
												</span>
											</a>
										</li>
										<li class="m-nav__item">
											<a href="javascript:void(0)" class="m-nav__link btn-submit-desmulti-dept btnDeselect">
												<span class="m-nav__link-text">
													Deselect
												</span>
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!--Department end::Modal-->


<script type="text/javascript">
	var _currentActions = <?php echo json_encode($actions); ?>;
	

	var tableCustom = $("#table_custom-lates").DataTable({
		dom: '<"toolbar custom-late_toolbar">frtlip',
		ajax: {
			url: "<?php echo base_url('gcctime/Lates/get_custom_collection');?>",
			type: "post",
			dataType: "json",
			data: {  csrf_token: _csrf_hash }
		},
		lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
		serverSide: true,
	    processing: true,
		columns: [
			{ data: "biometricno", width: "10%" },
			{ data: "name", width: "27%" },
			{ data: "weekday", width: "10%" },
			{ data: "am_start", width: "10%" },
			{ data: "am_end", width: "10%" },
			{ data: "pm_start", width: "10%" },
			{ data: "pm_end", width: "10%" },
			{ data: "is_active", width: "5%", className: "text-center" },
			{ data: null, width: "8%", className: "text-center" },
		], columnDefs: [{
			data: null,
			defaultContent: "",
			targets: -1,
			orderable: false,
			render: function ( data, type, row, meta ) {
				console.log(row);
				return tempDatatableActions(row.id);
			},
		},{
			data: "is_active",
			defaultContent: "",
			targets: 7,
			orderable: false,
			className: "dt-column-center",
			render: function ( data, type, row, meta ) { return tempDatatableStatus(row.is_active); },
		}, { 
			targets: "_all", 
			defaultContent: "", 
		}],
		initComplete: function(){
			$("div.custom-late_toolbar").html('<button type="button" class="m-portlet__nav-link btn m-btn--square btn-success btnNew btnNewLateCustom"><i class="fa fa-plus"></i> New </button>');
		}
	});

	var table = $("#table_lates").DataTable({
		"dom": '<"toolbar">frtlip',
		"ajax": "<?php echo base_url('gcctime/Lates/getCollection');?>",
		"lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
		"serverSide": false,
	    "processing": false, 
	});

	var	table_select = $("#table_dept_select").DataTable({
		ajax: {
			url: "<?php echo base_url('gcctime/Department/getDepartmentMin'); ?>",
			data: {  csrf_token: _csrf_hash },
			type: "post",
		},
		lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
		serverSide: true,
	    processing: true,
		columns: [{
			data: "chkbox",
			width: "2%",
		},{
			data: "department_name",
			width: "93%",
		},{
			data: "action",
			width: "5%",
		}],
	    columnDefs: [{
			targets: 0,
			orderable: false,
		},{
			targets: 1,
			className: 'text-center',
		},{
			targets: -1,
			orderable: false,
		}],
		paging: false,
		info: false,
		order: [[ 1, "asc" ]],
		scrollY: '50vh',
		scrollCollapse: true,
	});
	function schedule_list(id){
		$('#modal-assigned').modal('show');
		var	table_assigned = $("#table_assigned").DataTable({
		ajax: {
			url: "<?php echo base_url('gcctime/Department/getDepartmentAssigned'); ?>",
			data: {  csrf_token: _csrf_hash },
			type: "post",
		},
		lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
		serverSide: true,
	    processing: true,
		columns: [{
			data: "department_name",
			width: "100%",
		}],
	    columnDefs: [{
			targets: 0,
			className: 'text-center',
		}],
		paging: false,
		info: false,
		order: [[ 0, "asc" ]],
		scrollY: '50vh',
		scrollCollapse: true,
		destroy: true,
	});
	table_assigned.ajax.reload();
	}
	var tempDatatableStatus = function($status){
		var _html = "";
		if($status == 1){
			_html = "<span class='btn btn-success m-btn m-btn--icon m-btn--icon-only btn-sm' data-toggle='m-popover' data-placement='top' data-content='"+$status+"'><i class='la la-check'></i></span>";
		}else{
			_html = "<span class='btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm' data-toggle='m-popover' data-placement='top' data-content='"+$status+"'><i class='la la-times'></i></span>";
		}
		return _html;
	}

	var tempDatatableActions = function($id){
		var _actionButton ="";
		console.log($id);
		if($id){
			if(typeof _currentActions !== "undefined" && jQuery.inArray("edit", _currentActions) !== -1){
				_actionButton += "<button type='button' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' onclick='edit_custom_late("+$id+")' ><i class='la la-edit'></i></button>";			
			}
		}
		return _actionButton;
	}

	$("div.toolbar").html('<button type="button" class="m-portlet__nav-link btn m-btn--square btn-success btnNew"  data-toggle="modal" data-target="#modal-new"><i class="fa fa-plus"></i> New </button>');
	
	// Add new department Validation
	$.validate({
		form : '#form_late',
	    lang: 'en',
	    onSuccess : function(form) {
	    	$.ajax({
	    		url: form[0].action,
	    		type: "POST",
	    		data: $("#form_late").serialize(),
	    		beforeSend: function(){
	    			$(".btn-submit-late").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    		},
	    		success: function(data){
	    			$(".btn-submit-late").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    			var result = $.parseJSON(data);
	    			$("#modal-new").modal('hide');

	    			if(result.status){
						toastr.success(result.message);
						table.draw();
						table.ajax.reload();
						$('#form_late')[0].reset();
					}else{
						toastr.error(result.message);
					}
	    		}
	    	});
	    	return false;
	    },
	});

	$.validate({
		form : '#form-custom_late',
	    lang: 'en',
	    onSuccess : function(form) {
			var currentForm = form[0];

	    	$.ajax({
	    		url: currentForm.action,
	    		type: "POST",
	    		data: $(currentForm).serialize(),
				dataType: "json",
	    		beforeSend: function(){
	    			$(".btn-submit-late").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    		},
	    		success: function(json){
	    			$(".btn-submit-late").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    			if(json.response){
						toastr.success(json.toastr_msg);
						tableCustom.ajax.reload();
						currentForm.reset();
	    				$("#modal-custom_new").modal('hide');
					}else{
						toastr.error(json.toastr_msg);
					}
	    		}
	    	});

	    	return false;
	    },
	});

	var edit_late = function(id)
	{
		$("#modal-edit").modal("show");

		$.ajax({
			url: "<?php echo site_url('gcctime/Lates/getLate')?>",
			type: "POST",
			data: {id : id, csrf_token: _csrf_hash},
			beforeSend: function()
			{
				mApp.blockPage({
	                overlayColor: '#000000',
	                type: 'loader',
	                state: 'primary',
	                message: 'Please wait...',
	            });

				$(".blockUI.blockMsg .m-blockui").removeAttr("style");
			},
			success: function(data)
			{
				var result = $.parseJSON(data);
				mApp.unblockPage();
				$("#form_edit_late input[name=id]").val(result.id);
				$("#form_edit_late input[name=name]").val(result.name);
				$("#form_edit_late input[name=am_start]").val(result.am_start);
				$("#form_edit_late input[name=am_end]").val(result.am_end);
				$("#form_edit_late input[name=pm_start]").val(result.pm_start);
				$("#form_edit_late input[name=pm_end]").val(result.pm_end);

			}
		});
		return false;
	}
	var edit_custom_late = function(id)
	{
		$("#modal-custom_edit").modal("show");

		$.ajax({
			url: "<?php echo site_url('gcctime/Lates/getcustomLate')?>",
			type: "POST",
			data: {id : id, csrf_token: _csrf_hash},
			beforeSend: function()
			{
				mApp.blockPage({
	                overlayColor: '#000000',
	                type: 'loader',
	                state: 'primary',
	                message: 'Please wait...',
	            });

				$(".blockUI.blockMsg .m-blockui").removeAttr("style");
			},
			success: function(data)
			{
				var result = $.parseJSON(data);
				mApp.unblockPage();
				$("#form-custom_edit input[name=id]").val(result.id);
				$("#form-custom_edit input[name=weekday]").val(result.weekday).trigger('change');
				$("#form-custom_edit input[name=am_start]").val(result.am_start);
				$("#form-custom_edit input[name=am_end]").val(result.am_end);
				$("#form-custom_edit input[name=pm_start]").val(result.pm_start);
				$("#form-custom_edit input[name=pm_end]").val(result.pm_end);

			}
		});
		return false;
	}
	// Edit late Validation
	$.validate({
		form : '#form_edit_late',
	    lang: 'en',
	    onSuccess : function(form) {
	    	$.ajax({
	    		url: form[0].action,
	    		type: "POST",
	    		data: $("#form_edit_late").serialize(),
	    		beforeSend: function(){
	    			$(".btn-submit-late").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    		},
	    		success: function(data){
	    			$(".btn-submit-late").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    			var result = $.parseJSON(data);
	    			$("#modal-edit").modal('hide');

	    			if(result.status){
						toastr.success(result.message);
						table.draw();
						table.ajax.reload();
						$('#form_edit_late')[0].reset();
					}else{
						toastr.error(result.message);
					}
	    		}
	    	});
	    	return false;
	    },
	});
	$.validate({
		form : '#form-custom_edit',
	    lang: 'en',
	    onSuccess : function(form) {
	    	$.ajax({
	    		url: form[0].action,
	    		type: "POST",
	    		data: $("#form-custom_edit").serialize(),
	    		beforeSend: function(){
	    			$(".btn-submit-late").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    		},
	    		success: function(data){
	    			$(".btn-submit-late").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    			var result = $.parseJSON(data);
	    			$("#modal-custom_edit").modal('hide');

	    			if(result.status){
						toastr.success(result.message);
						table.draw();
						tableCustom.ajax.reload();
						$('#form-custom_edit')[0].reset();
					}else{
						toastr.error(result.message);
					}
	    		}
	    	});
	    	return false;
	    },
	});
	$("#table_lates").on("click",".btn-delete",function(){
		var id = $(this).attr("data-id");

		$("#modal-delete").modal("show");

		$("#modal-delete .btm-submit-delete").attr("data-id",id);

	});

	$(".btm-submit-delete").on("click",function(){
		var id = $(this).attr("data-id");
		$.ajax({
			url: "<?php echo site_url("gcctime/Lates/deleteLate");?>",
			type: "POST",
			data: {id: id},
			beforeSend: function()
			{
				mApp.blockPage({
	                overlayColor: '#000000',
	                type: 'loader',
	                state: 'primary',
	                message: 'Please wait...',
	            });

				$(".blockUI.blockMsg .m-blockui").removeAttr("style");
			},
			success: function(data){
				var result = $.parseJSON(data);
				mApp.unblockPage();
				$("#modal-delete").modal("hide");

				if(result.status){
					toastr.success(result.message);
				}else{
					toastr.error(result.message);
				}

				table.draw();
				table.ajax.reload();
			}
		});
	});

	$("#table_lates").on("click",".btn-dept",function(){
		var late_id = $(this).attr("data-id");
		$("#modal-select-dept #selected-dept-id").attr("data-id",late_id);
		$("#modal-select-dept").modal("show");
		setTimeout(function(){
			table_select.draw();
		}, 1000);

	});

	//check box start

	$("#checkAll").change(function() {
        if (this.checked) {
            $(".checkSingle").each(function() {
                this.checked=true;
            });
        } else {
            $(".checkSingle").each(function() {
                this.checked=false;
            });
        }
    });

    $(".checkSingle").click(function () {
        if ($(this).is(":checked")) {
            var isAllChecked = 0;

            $(".checkSingle").each(function() {
                if (!this.checked)
                    isAllChecked = 1;
            });

            if (isAllChecked == 0) {
                $("#checkedAll").prop("checked", true);
            }     
        }
        else {
            $("#checkedAll").prop("checked", false);
        }
    });

    //check box end

    //submit multiple select
    $(".btn-submit-multi-dept").on("click",function(){
    	var check_count = $('.checkSingle:checkbox:checked').length;
    	var late_id = $("#modal-select-dept #selected-dept-id").attr("data-id");
    	if(check_count == 0){
    		toastr.error("No Data Selected!");
    	}else{
    		$('.checkSingle:checkbox:checked').each(function(){
    			var dept_id = $(this).attr("data-id");

    			$.ajax({
    				url: "<?php echo site_url('gcctime/Lates/selectDeptforLate');?>",
    				type: "POST",
    				data: {late_id: late_id, dept_id: dept_id, csrf_token: _csrf_hash},
    				beforeSend: function()
    				{
    					mApp.block('#modal-select-dept .modal-content', {
			                overlayColor: '#000000',
			                state: 'primary'
			            });
    				},
    				success: function()
    				{
    					mApp.unblock('#modal-select-dept .modal-content');
    					table_select.draw();
    				}
    			})
    		});
    	}
    });

    //submit multiple deselect
    $(".btn-submit-desmulti-dept").on("click",function(){
    	var check_count = $('.checkSingle:checkbox:checked').length;
    	var late_id = $("#modal-select-dept #selected-dept-id").attr("data-id");
    	if(check_count == 0){
    		toastr.error("No Data Selected!");
    	}else{
    		$('.checkSingle:checkbox:checked').each(function(){
    			var dept_id = $(this).attr("data-id");

    			$.ajax({
    				url: "<?php echo site_url('gcctime/Lates/deselectDeptforLate');?>",
    				type: "POST",
    				data: {late_id: late_id, dept_id: dept_id, csrf_token: _csrf_hash},
    				beforeSend: function()
    				{
    					mApp.block('#modal-select-dept .modal-content', {
			                overlayColor: '#000000',
			                state: 'primary'
			            });
    				},
    				success: function()
    				{
    					mApp.unblock('#modal-select-dept .modal-content');
    					table_select.draw();
    				}
    			})
    		});
    	}
    });

    //selet single dept
    $("#table_dept_select").on("click",".btn-select-late-trigg",function(){
    	var dept_id = $(this).attr("data-id");
    	var shift_id = $("#selected-dept-id").attr("data-id");

    	$.ajax({
    		url: "<?php echo site_url("gcctime/Lates/selectDeptforLate");?>",
    		type: "POST",
    		data: {late_id: late_id, dept_id: dept_id, csrf_token: _csrf_hash},
    				beforeSend: function()
    				{
    					mApp.block('#modal-select-dept .modal-content', {
			                overlayColor: '#000000',
			                state: 'primary'
			            });
    				},
    				success: function()
    				{
    					mApp.unblock('#modal-select-dept .modal-content');
    					table_select.draw();
    				}
    	});

    });
	function select_dep(id)
{
	var strArray = id.split(",");
	var dept_id = strArray[0];
    	var shift_id = $("#modal-select-dept #selected-dept-id").attr("data-id");
	
	$.ajax({
    		url: "<?php echo site_url("gcctime/Lates/selectLate");?>",
    		type: "POST",
    		data: {shift_id: shift_id, id: dept_id, csrf_token: _csrf_hash},
    				beforeSend: function()
    				{
    					mApp.block('#modal-select-dept .modal-content', {
			                overlayColor: '#000000',
			                state: 'primary'
			            });
    				},
    				success: function()
    				{
    					mApp.unblock('#modal-select-dept .modal-content');
    					table_select.draw();
    				}
    	});
	 
  
}
function deselect_dep(id)
{
	var dept_id = id;
    	var late_id = $("#selected-dept-id").attr("data-id");
	// ajax delete data to database
	$.ajax({
    		url: "<?php echo site_url("gcctime/Lates/deselectLate");?>",
    		type: "POST",
    		data: {late_id: late_id, id: dept_id, csrf_token: _csrf_hash},
    				beforeSend: function()
    				{
    					mApp.block('#modal-select-dept .modal-content', {
			                overlayColor: '#000000',
			                state: 'primary'
			            });
    				},
    				success: function()
    				{
    					mApp.unblock('#modal-select-dept .modal-content');
    					table_select.draw();
    				}
    	});
	 
  
}
	$(document).ready(function(){
		toastr.options = {
		  "closeButton": false,
		  "debug": false,
		  "newestOnTop": false,
		  "progressBar": false,
		  "positionClass": "toast-top-right",
		  "preventDuplicates": false,
		  "onclick": null,
		  "showDuration": "300",
		  "hideDuration": "1000",
		  "timeOut": "5000",
		  "extendedTimeOut": "1000",
		  "showEasing": "swing",
		  "hideEasing": "linear",
		  "showMethod": "fadeIn",
		  "hideMethod": "fadeOut"
		};

		//time picker init start
			$('#am_start, #am_end, #pm_start, #pm_end').timepicker({
				minuteStep: 1,
				showMeridian: false,
				use24hours: false,
				defaultTime: null,

			});
		//time picker init end
		$(".select2").select2({ placeholder: "SELECT AN OPTION", width: "100%" });
	});

	$(document).on("click", ".btnNewLateCustom", function(){
		$.ajax({
			url: "<?php echo site_url("gcctime/lates/get_personnel_items"); ?>",
			dataType: "json",
			success: function(json){
				$("#personnel_id").select2({
					data: json.results,
					placeholder: "SELECT AN OPTION",
					width: "100%",
				});
				$("#modal-custom_new").modal("show");
			}
		});
	});
</script>

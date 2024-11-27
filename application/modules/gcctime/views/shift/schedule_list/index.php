<?php $actions = $this->core_layout->getCurrentActions(); ?>
<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Manage Shift Schedule Data
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
								<table class="table table-striped table-bordered" id="table-shift_schedule_data">
									<col width="15%">
									<col width="28%">
									<col width="10%">
									<col width="8%">
									<col width="8%">
									<col width="8%">
									<col width="8%">
									<col width="5%">
									<col width="10%">
									<thead>
										<tr>
											<th>Name</th>
											<th>Description</th>
											<th>Week Day</th>
											<th>Am Start</th>
											<th>Am End</th>
											<th>Pm Start</th>
											<th>Pm End</th>
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

<div class="modal fade" id="modal-shift_schedule_data" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<form action="<?php echo base_url('gcctime/shift/add_shift_schedule_data'); ?>" method="POST" id="form-shift_schedule_data">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<div class="modal-header">
				<h5 class="modal-title">New - Shift Schedule Data</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-control-label">Name *</label>
					<input type="text" name="name" class="form-control inptName" autocomplete=off data-validation="required" />
				</div>
				<div class="form-group">
					<label class="form-control-label">Description *</label>
					<input type="text" name="description" class="form-control inptDescription" autocomplete=off data-validation="required" />
				</div>
				<div class="form-group">
					<label class="form-control-label">Week Day *</label>
					<select name="weekday" class="form-control selectWeekday" data-validation="required">
						<option value="">Select an option</option>
						<option value="monday">Monday</option>
						<option value="tuesday">Tuesday</option>
						<option value="wednesday">Wednesday</option>
						<option value="thursday">Thursday</option>
						<option value="friday">Friday</option>
						<option value="saturday">Saturday</option>
						<option value="sunday">Sunday</option>
					</select>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="form-control-label">AM Start *</label>
							<input id="inptAmStart_0" type="text" name="am_start" class="form-control col-md-8 inptAmStart" autocomplete=off data-validation="required" />
						</div>				
						<div class="form-group">
							<label class="form-control-label">PM Start *</label>
							<input id="inptPmStart_0" type="text" name="pm_start" class="form-control col-md-8 inptPmStart" autocomplete=off data-validation="required" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="form-control-label">AM End *</label>
							<input id="inptAmEnd_0" type="text" name="am_end" class="form-control col-md-8 inptAmEnd" autocomplete=off data-validation="required" />
						</div>
						<div class="form-group">
							<label class="form-control-label">PM End *</label>
							<input id="inptPmEnd_0" type="text" name="pm_end" class="form-control col-md-8 inptPmEnd" autocomplete=off data-validation="required" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="">Status</label>
					<div class="m-radio-inline">
						<label class="m-radio"><input id="status1" type="radio" name="is_active" value="1" checked>Active<span></span></label>
						<label class="m-radio"><input id="status0" type="radio" name="is_active" value="0">Inactive<span></span></label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-primary btnSave btn-submit">Save</button>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-shift_schedule_data-update" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<form action="<?php echo base_url('gcctime/shift/update_shift_schedule_data'); ?>" method="POST" id="form-shift_schedule_data-update">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<input type="hidden" name="id" value="0" v-model="items.id" />
			<div class="modal-header">
				<h5 class="modal-title">Update - Shift Schedule Data</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-control-label">Name *</label>
					<input type="text" name="name" class="form-control inptName" autocomplete=off v-model="items.name" data-validation="required" />
				</div>
				<div class="form-group">
					<label class="form-control-label">Description *</label>
					<input type="text" name="description" class="form-control inptDescription" autocomplete=off v-model="items.description" data-validation="required" />
				</div>
				<div class="form-group">
					<label class="form-control-label">Week Day *</label>
					<select id="selectWeekday_1" name="weekday" class="form-control selectWeekday" v-model="items.weekday" data-validation="required">
						<option value="">Select an option</option>
						<option value="monday">Monday</option>
						<option value="tuesday">Tuesday</option>
						<option value="wednesday">Wednesday</option>
						<option value="thursday">Thursday</option>
						<option value="friday">Friday</option>
						<option value="saturday">Saturday</option>
						<option value="sunday">Sunday</option>
					</select>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="form-control-label">AM Start *</label>
							<input id="inptAmStart_1" type="text" name="am_start" class="form-control col-md-8 inptAmStart" v-model="items.am_start" autocomplete=off data-validation="required" />
						</div>				
						<div class="form-group">
							<label class="form-control-label">PM Start *</label>
							<input id="inptPmStart_1" type="text" name="pm_start" class="form-control col-md-8 inptPmStart" v-model="items.pm_start" autocomplete=off data-validation="required" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="form-control-label">AM End *</label>
							<input id="inptAmEnd_1" type="text" name="am_end" class="form-control col-md-8 inptAmEnd" v-model="items.am_end" autocomplete=off data-validation="required" />
						</div>
						<div class="form-group">
							<label class="form-control-label">PM End *</label>
							<input id="inptPmEnd_1" type="text" name="pm_end" class="form-control col-md-8 inptPmEnd" v-model="items.pm_end" autocomplete=off data-validation="required" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="">Status</label>
					<div class="m-radio-inline">
						<label class="m-radio"><input id="status1" type="radio" name="is_active" value="1" v-model="items.is_active" v-bind:value="1">Active<span></span></label>
						<label class="m-radio"><input id="status0" type="radio" name="is_active" value="0" v-model="items.is_active" v-bind:value="0">Inactive<span></span></label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-primary btnSave btn-submit">Save</button>
			</div>
			</form>
		</div>
	</div>
</div>

<script>
var _currentActions = <?php echo json_encode($actions); ?>;


jQuery(function($){
	$(".selectWeekday").select2({ width: "100%" });	
});

var _dtShiftSchedule = $("#table-shift_schedule_data").DataTable({
		dom: '<"toolbar">frtlip',
		serverSide: true,
		processing: true,
		ajax: {
			url: "<?php echo base_url("gcctime/shift/get_shift_schedule_data_list"); ?>",
			type: "post",
			dataType: "json",
			data: {  csrf_token: _csrf_hash }
		}, columns: [
			{ data: "name", width: "15%" },
			{ data: "description", width: "30%" },
			{ data: "weekday", width: "15%"},
			{ data: "am_start", width: "5%"},
			{ data: "am_end", width: "5%"},
			{ data: "pm_start", width: "5%"},
			{ data: "pm_end", width: "5%"},
			{ data: "is_active", width: "10%"},
			{ data: null, width: "10%"},
		], columnDefs: [{
			data: null,
			defaultContent: "",
			targets: -1,
			orderable: false,
			render: function ( data, type, row, meta ) { return shiftDatatableActions(row.id); },
		},{
			data: "is_active",
			defaultContent: "",
			targets: 7,
			orderable: false,
			className: "dt-column-center",
			render: function ( data, type, row, meta ) { return shiftDatatableStatus(row.is_active); },
		}, { 
			targets: "_all", 
			defaultContent: "", 
		}], initComplete: function(settings, json){
			if(typeof aclActionUpdate == "function"){ aclActionUpdate(); }
		}
	});
var _htmlContent = '<button id="shift_schedule_data-new" type="button" class="m-portlet__nav-link btn m-btn--square btn-success btnNew"  data-toggle="modal" data-target="#modal-shift_schedule_data"><i class="fa fa-plus"></i> New </button>';
	$("div.toolbar").html(_htmlContent);
	
function shiftDatatableActions($id){
	if($id){
		var _actionButton ="";
		if(typeof _currentActions !== "undefined" && jQuery.inArray("edit", _currentActions) !== -1){
			_actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnEdit btnEditShift' data-id='"+$id+"'><i class='la la-edit'></i></button>";				
		}
		if(typeof _currentActions !== "undefined" && jQuery.inArray("delete", _currentActions) !== -1){
			_actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnDelete btnRemoveShift' data-id='"+$id+"'><i class='la la-trash'></i></button>";				
		}
		
		if(!_actionButton){ _actionButton = "---"; }
		return _actionButton;
	}else{ return false; }
}

function shiftDatatableStatus($isActive){
	var _html = "";
	if($isActive == 1){ _html = "<span class='btn btn-info m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-check'></i></span>"; }
	else{ _html = "<span class='btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-remove'></i></span>"; }
	return _html;
}

$.validate({
	form : '#form-shift_schedule_data',
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
					_dtShiftSchedule.ajax.reload();
					toastr.success(data.toastr_msg, "Added Shift Data", 5000);
					$("#modal-shift_schedule_data").modal("hide");
				}else{
					toastr.error(data.toastr_msg, "Error Shift Data", 5000);
				}
				if(typeof _btnSubmit !== "undefined"){ _btnSubmit.removeClass("m-btn--custom m-loader m-loader--light m-loader--right"); }
			}
		});
		return false;
	}
});
var editShiftValidation = function(){
	$.validate({
		form : '#form-shift_schedule_data-update',
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
						$("#modal-shift_schedule_data-update").modal("hide");
						_dtShiftSchedule.ajax.reload();
						toastr.success(data.toastr_msg, "Added Shift Data", 5000);
					}else{
						toastr.error(data.toastr_msg, "Error Shift Data", 5000);
					}
					if(typeof _btnSubmit !== "undefined"){ _btnSubmit.removeClass("m-btn--custom m-loader m-loader--light m-loader--right"); }
				}
			});
			return false;
		}
	});
}

$('.inptAmStart, .inptAmEnd, .inptPmStart, .inptPmEnd').timepicker({
	minuteStep: 1,
	showMeridian: false,
	use24hours: false,
	defaultTime: null,
	onSelect: function(dateText, inst){
		$('#'+inst.id).attr('value',dateText);
	}
});

$(document).on("click", ".btnEditShift", function(){
	var _dataId = $(this).data("id");
	$.ajax({
		url: "<?php echo site_url("gcctime/shift/get_shift_schedule_item"); ?>",
		dataType: "json",
		type: "post",
		data: { id: _dataId, csrf_token: _csrf_hash },
		beforeSend: function(){},
		success: function(json){
			if(json.response){
				vm.items = json.data;
				vm.$mount();
				editShiftValidation();
				$("#modal-shift_schedule_data-update").modal("show");				
			}else{
				toastr.error(json.toastr_msg, "Error Shift Data", 5000);
			}
		}
	});
});

var _items = { id: 0, name: "",	description: "", weekday: "", am_start: "", am_end: "", pm_start: "", pm_end: "", is_active: 0 };
var vm = new Vue({
	el: '#form-shift_schedule_data-update',
	data: { items: _items },
	mounted: function(){
		$(this.$el).find(".selectWeekday").trigger("change");
		$(this.$el).find('.inptAmStart, .inptAmEnd, .inptPmStart, .inptPmEnd').timepicker({
			minuteStep: 1,
			showMeridian: false,
			use24hours: false,
			defaultTime: null,
			onSelect: function(dateText, inst){
				$('#'+inst.id).attr('value',dateText);
			}
		});
	}
});
</script>
<?php 
	switch($device["status"]){
		case 1:
			$status = '<span class="m-badge m-badge--success m-badge--wide">Connected</span>';
			break;
		case 0:
			$status = '<span class="m-badge m-badge--danger m-badge--wide">Disconnected</span>';
			break;
		default:
			$status = "";
			break;
	}
?>
<div class="m-content">
	<!--Begin::Main Portlet-->
	<div class="row">
		<div class="col-md-3 col-lg-3 col-xl-2">
			<div class="m-portlet m-portlet--mobile ">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">Active Device</h3>
						</div>
					</div>
				</div>
				<div class="m-portlet__body">
					<div class="m-widget1 m-widget1--paddingless">
						<div class="m-widget1__item">
							<div class="row m-row--no-padding align-items-center">
								<div class="col-md-12">
									<div class="m-widget16__active_device">
										<div class="m-widget16__active-device m--margin-bottom-10">
											<span class="m-widget16__active-device-text">
												Name: <strong><?php echo $device["device_name"];?></strong>
											</span>
										</div>
										<div class="m-widget16__active-device m--margin-bottom-10">
											<span class="m-widget16__active-device-text">
												IP Address: <strong><?php echo $device["ip_address"];?></strong>
											</span>
										</div>
										<div class="m-widget16__active-device m--margin-bottom-10">
											<span class="m-widget16__active-device-text">
												Status <?php echo $status;?>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div> 
					</div>
				</div>
			</div>
			<div class="m-portlet m-portlet--mobile ">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">Legends</h3>
						</div>
					</div>
				</div>
				<div class="m-portlet__body">
					<div class="m-widget1 m-widget1--paddingless">
						<div class="m-widget1__item">
							<div class="row m-row--no-padding align-items-center">
								<div class="col">
									<div class="m-widget15__items m-widget15__legends">
										<div class="row m--margin-bottom-10">
											<div class="col-md-12">
												<div class="m-widget15__item">
													<span class="m-widget15__stats">Late</span>
													<span class="m-widget15__text"></span>
													<div class="m--space-10"></div>
													<div class="progress m-progress--sm">
														<div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
													</div>
												</div>
											</div>
										</div>
										<div class="row m--margin-bottom-10">
											<div class="col-md-12">
												<div class="m-widget15__item">
													<span class="m-widget15__stats">Undertime</span>
													<span class="m-widget15__text"></span>
													<div class="m--space-10"></div>
													<div class="progress m-progress--sm">
														<div class="progress-bar bg-warning" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
													</div>
												</div>
											</div>
										</div>
										<div class="row m--margin-bottom-10">
											<div class="col-md-12">
												<div class="m-widget15__item">
													<span class="m-widget15__stats">Double Entry</span>
													<span class="m-widget15__text"></span>
													<div class="m--space-10"></div>
													<div class="progress m-progress--sm">
														<div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-9 col-lg-9 col-xl-10">
			<div class="m-portlet m-portlet--mobile ">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Attendance Masterfile
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<ul class="m-portlet__nav">
							<li class="m-portlet__nav-item">
								<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
									<?php /* <button type="button" class="m-btn btn btn-secondary btnSync" onclick="syncdata()"><i class="m-nav__link-icon flaticon-share"></i> Sync Data</button> */ ?>
									<button type="button" class="m-btn btn btn-secondary btnAdvance_search" data-toggle="modal" data-target="#m_daterangepicker_modal"><i class="m-nav__link-icon flaticon-open-box"></i> Advance Search</button>
									<button type="button" class="m-btn btn btn-secondary btnUpload" data-toggle="modal" data-target="#upload_modal"><i class="m-nav__link-icon fa fa-upload"></i> Upload File</button>
									<div class="btn-group btn-group-sm" role="group">
										<button id="btn-syncdrop" type="button" class="m-btn btn btn-secondary btnSync dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<i class="m-nav__link-icon flaticon-share"></i> Sync Data
										</button>
										<div class="dropdown-menu dropdown-menu-right" aria-labelledby="btn-syncdrop">
											<a class="dropdown-item" data-toggle="modal" href="javascript:void(0)" data-toggle="modal" data-target="#sync_app_attendance_modal">
												<i class="m-nav__link-icon la la-mobile"></i> Sync App Attendance
											</a>
											<a class="dropdown-item" onclick="syncdata()" href="javascript:void(0)">
												<i class="m-nav__link-icon flaticon-technology"></i> Active Device
											</a>
											<a class="dropdown-item" onclick="multisyncdata()" href="javascript:void(0)">
												<i class="m-nav__link-icon flaticon-map"></i> Multiple Device
											</a>
											<a class="dropdown-item" data-toggle="modal" data-target="#m_generate_attendance_modal" href="javascript:void(0)">
												<i class="m-nav__link-icon fa fa-gears"></i> Generate Attendance
											</a>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="m-portlet__body">
					<!--begin: Datatable -->
					<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
						<table class="table table-striped table-bordered" id="attendance-datatable">
							<col width="10%">
							<col width="8%">
							<col width="8%">
							<col width="54%">
							<col width="15%">
							<col width="5%">
							<thead>
								<tr>
									<th>Biometric ID</th>
									<th>Date</th>
									<th>Time</th>
									<th>Name</th>
									<th>Device</th>
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
		</div>
	</div>
	</div>
</div>

<!-- sync app data date picker -->
<div class="modal fade" id="sync_app_attendance_modal" tabindex="-1" role="dialog" aria-labelledby="">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="">
					App Attendance Date
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" class="la la-remove"></span>
				</button>
			</div>
			<div class="modal-body row">
				<div class="col-lg-6">
					<div id="datepicker_from">
						<input type="text" name="app_datetime_from[]" class="form-control" data-validation="required" id="appattendancedatetimepickerfrom" readonly placeholder="Date From">
					</div>
				</div>
				<div class="col-lg-6">
					<div id="datepicker_to">
						<input type="text" name="app_datetime_to[]" class="form-control" data-validation="required" id="appattendancedatetimepickerto" readonly placeholder="Date To">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button onclick="syncAppAttendanceDate()" class="btn btn-primary m-btn" data-dismiss="modal">Sync</button>
				<button class="btn btn-danger m-btn" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<!-- Search Attendance Data :: Start -->
<div class="modal fade" id="m_daterangepicker_modal" tabindex="-1" role="dialog" aria-labelledby="">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="">
					Search Attendance Data
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" class="la la-remove"></span>
				</button>
			</div>
			<form class="m-form m-form--fit m-form--label-align-right" id="form_searchdate" action="<?php echo site_url("gcctime/Attendance/searchrangetest")?>" method="POST">
				<div class="modal-body">
					<div class="form-group">
					<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
						<label class="form-control-label">Employee Name</label>
						<select name="biometric_id" class="form-control" id="employee_name" style="width: 100% !important;">
							<option></option>
							
							<?php 
								$getPersonnelCollection = $this->crud->getCollection(array(),"gcctimeutility.personnel");
								if($getPersonnelCollection){
									foreach($getPersonnelCollection as $_getPersonnelCollection){
							?>
								<option value="<?php echo $_getPersonnelCollection["biometric_id"]; ?>"><?php echo $_getPersonnelCollection["name"]; ?></option>
							<?php
								}
									}
							?>
						</select>
					</div>
					<div class="form-group">
						<label class="form-control-label">Select Date Range</label>
						<input type="text" name="daterange" class="form-control" id="m_daterangepicker_1_modal" readonly="" placeholder="Select time">
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary m-btn" data-dismiss="modal">
						Close
					</button>
					<button type="button" class="btn btn-brand m-btn btnAdvance_search" onclick="searchrange()">
						Search
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="m_generate_attendance_modal" tabindex="-1" role="dialog" aria-labelledby="">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="">
					Generate Attendance
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" class="la la-remove"></span>
				</button>
			</div>
			<form class="m-form m-form--fit m-form--label-align-right" id="form_searchdate" action="<?php echo site_url("gcctime/attendance/generate_attendance_record"); ?>" method="POST">
				<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<div class="modal-body">
					<div class="form-group">
						<label class="form-control-label">Attendance Log</label>
						<select name="attendance_log" class="form-control" id="attendance_log"><option></option></select>
					</div>
					<div class="customTable">
						<table id="attendance-table" class="m-table table"></table>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary m-btn" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- Search Attendance Data :: End -->

<!-- Add Attendance Data :: Start -->
<div class="modal fade" id="modal-new" tabindex="-1" role="dialog" aria-labelledby="">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="">
					Add Attendance Data
				</h5>
				
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" class="la la-remove"></span>
				</button>
			</div>
			<form class="m-form m-form--fit m-form--label-align-right" id="form_add_attendance" action="<?php echo site_url("gcctime/Attendance/addAttendance")?>" method="POST">
				<div class="modal-body">
					<div class="form-group">
					<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
						<label class="form-control-label">
							Employee Name
						</label>
						<select name="biometric_id" class="form-control" id="employee_name_new" data-validation="required" style="width: 100% !important;">	
						<option>&nbsp;</option>							
							<?php 
								$getPersonnelCollection = $this->crud->getCollection(array(),"gcctimeutility.personnel");
								if($getPersonnelCollection){
									foreach($getPersonnelCollection as $_getPersonnelCollection){
							?>
								<option value="<?php echo $_getPersonnelCollection["biometric_id"]; ?>"><?php echo $_getPersonnelCollection["name"]; ?></option>
							<?php
								}
									}
							?>
						</select>
					</div>
					<div class="form-group appendto">
						<label class="form-control-label">
							Select Date & Time
						</label>
						<input type="text" name="datetime[]" class="form-control" data-validation="required" id="datetimepicker" readonly="" placeholder="Select time">
					</div>
					<div class="form-group">
						<button type="button" class="btn btnNew btn-success m-btn m-btn--icon m-btn--icon-only btn-clone-datetimepicker">
							<i class="la la-plus"></i>
						</button>
					</div>
					<div class="form-group">
						<label class="form-control-label" for="remarks">
							Remarks
						</label>
						<textarea class="form-control m-input" id="remarks" name="remarks" rows="3" data-validation="required"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary m-btn" data-dismiss="modal">
						Close
					</button>
					<button type="submit" class="btn btn-brand m-btn btn-submit-add btnSave">
						Submit
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- Add Attendance Data :: End -->

<!-- //clonetemplate -->
<div class="display-none">
	<div class="form-group datetimetemplate">
		<label class="form-control-label">
			Select Date & Time
		</label>
		<input type="text" name="datetime[]" class="form-control custom_datetimepicker" data-validation="required" readonly="" placeholder="Select time">
	</div>
</div>

<!-- Edit Attendance Data :: Start -->
<div class="modal fade" id="edit_modal" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					Edit Attendance Data
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" class="la la-remove"></span>
				</button>
			</div>
			
			<form class="m-form m-form--fit m-form--label-align-right" id="form_edit_attendance" action="<?php echo site_url("gcctime/Attendance/editAttendance")?>" method="POST">
				<div class="modal-body">
					<input type="hidden" name="attendance_id">
					<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
					<div class="form-group">
						<label class="form-control-label">
							Employee Name
						</label>
						<input type="text" class="form-control" name="employee_name_edit" readonly="readonly">
					</div>

					<div class="form-group">
						<label class="form-control-label">
							Select Date Range
						</label>
						<input type="text" name="datetime" class="form-control" data-validation="required" id="datetimepicker_edit" readonly="" placeholder="Select time">
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary m-btn" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-brand m-btn btnUpdate">Submit</button> <!-- tn-submit-edit -->
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="upload_modal" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Upload File</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" class="la la-remove"></span>
				</button>
			</div>
			
			<form class="m-form m-form--fit m-form--label-align-right" id="form_upload_attendance" action="<?php echo site_url("gcctime/attendance/upload_attendance"); ?>" method="POST">
				<input type="hidden" name="full_path">
				<div class="modal-body">
					<div class="row">
					<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
						<label for="assigned_device" class="col-3 col-form-label">
							Assigned Device
						</label>
						<div class="col-6">
							<div class="form-group m-form__group">
							<select class="form-control m-input" name="device_id" id="device_id" data-validation="required">
								<option disabled selected value="0">Choose an option</option>
								<option v-for="item in post.devices" v-bind:value="item.id" v-text="item.device_name"></option>
							</select>
							<span class="help-block form-error"></span>
							</div>
						</div>
						<div class="col-3">
							<span class="btn btn-success fileinput-button">
								<i class="glyphicon glyphicon-plus"></i>
								<span>Select a file</span>
								<input id="fileupload" type="file" name="files">
							</span>
						</div>
					</div>
					<div class="form-group m-form__group row">
						<div class="col-12">
							<div id="progress" class="progress">
								<div class="progress-bar progress-bar-success"></div>
							</div>
							<div id="files" class="files"></div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="col-md-12">
						<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
							<table class="table table-striped table-bordered" id="import-attendance-preview">
								<thead>
									<tr>
										<th>Biometric No#</th>
										<th>Employee Name</th>
										<th>Date</th>
										<th>Time</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-brand m-btn btnUpload">Submit</button>
					<button class="btn btn-secondary m-btn" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- Edit Attendance Data :: End -->

<script type="text/javascript">
var modalGenerateAttendance = $("#m_generate_attendance_modal");
var _nTable = $('#attendance-datatable').DataTable({
	dom: '<"toolbar">frtlip',
	paging: false,
	lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
	serverSide: false,
	processing: false,
	scrollY: '50vh',
	scrollCollapse: true,
	order: [[ 2, "desc" ]],
	columnDefs: [{
		defaultContent: "",
		targets: -1,
		orderable: false,
		className: "text-center"
	}],
	createdRow: function(row, data, dataIndex){
		if(data[6] == "late"){
			$(row).addClass("bg-danger text-white");
		}
		if(data[6] == "undertime"){
			$(row).addClass("bg-warning");
		}
		if(data[6] == "double_entry"){
			$(row).addClass("bg-success");
		}
	}
});

$("div.toolbar").html('<button type="button" class="m-portlet__nav-link btn m-btn--square btn-success btnNew"  data-toggle="modal" data-target="#modal-new"><i class="fa fa-plus"></i> New </button>');

var generateCurrentAttendance = function(){
	$.ajax({
		url: "<?php echo site_url("gcctime/attendance/get_current_attendance"); ?>",
		dataType: "json",
		beforeSend: function(){
			mApp.blockPage({
				overlayColor: '#000000',
				type: 'loader',
				state: 'primary',
				message: 'Please wait...',
			});

			$(".blockUI.blockMsg .m-blockui").removeAttr("style");
			$(".blockUI.blockMsg.blockPage").css("z-index","2000");
		},success: function(json){
			var result = json.data;
			_nTable.clear();
			_nTable.rows.add(result).draw();
			mApp.unblockPage();
		}
		
	});
}

var syncdata = function(){
	$.ajax({
		url: "<?php echo base_url('gcctime/curl_request/syncdata'); ?>",
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
			mApp.unblockPage();
			generateCurrentAttendance();
			toastr.success("Attendance data sync successfully!");
		}
	});
	event.preventDefault();
}

var multisyncdata = function(){
	$.ajax({
		url: "<?php echo site_url('gcctime/curl_request/syncdata/true'); ?>",
		beforeSend: function()
		{
			// mApp.blockPage({
			// 	overlayColor: '#000000',
			// 	type: 'loader',
			// 	state: 'primary',
			// 	message: 'Please wait...',
			// });

			// $(".blockUI.blockMsg .m-blockui").removeAttr("style");

		},
		success: function(data)
		{
			mApp.unblockPage();
			generateCurrentAttendance();
			toastr.success("Attendance data sync successfully!");
		}
	});
	event.preventDefault();
}
// minimum setup
$('#m_daterangepicker_1_modal').daterangepicker({
	buttonClasses: 'm-btn btn',
	applyClass: 'btn-primary',
	cancelClass: 'btn-secondary'
});

var searchrange = function(){
	var form_searchdate = $("#form_searchdate");
	$.ajax({
		url: form_searchdate.attr("action"),
		type: "POST",
		data: form_searchdate.serialize(),
		beforeSend: function()
		{
			mApp.blockPage({
				overlayColor: '#000000',
				type: 'loader',
				state: 'primary',
				message: 'Please wait...',
			});

			$(".blockUI.blockMsg .m-blockui").removeAttr("style");
			$(".blockUI.blockMsg.blockPage").css("z-index","2000");


		},
		success: function(data)
		{
			var result = $.parseJSON(data);
			nData = result;
			
			_nTable.clear();
			_nTable.rows.add(result).draw();
			$('#m_daterangepicker_modal').modal('hide');
			mApp.unblockPage();

		}
	});
}

// Update Late Settings Validation
$.validate({
	form : '#form_edit_attendance',
	lang: 'en',
	onSuccess : function(form) {
		$.ajax({
			url: form[0].action,
			type: "POST",
			data: $("#form_edit_attendance").serialize(),
			beforeSend: function(){
				// $(".btn-submit-edit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
				// mApp.block('#` .modal-content', {
				// 		overlayColor: '#000000',
				// 		state: 'primary'
				// 	});

				// $(".blockUI.blockMsg .m-blockui").removeAttr("style");
			},
			success: function(data){
				$(".btn-submit-edit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
				mApp.unblock('#edit_modal .modal-content');
				$("#modal-shift-settings").modal('hide');
				var result = $.parseJSON(data);

				if(result.status){ toastr.success(result.message); }
				else{ toastr.error(result.message); }
			}
		});
		return false;
	},
});

// open edit view modal
$("#attendance-datatable").on("click",".btn-modal-edit",function(){
var attendance_id = $(this).attr("data-id");
$("input[name=attendance_id]").val(attendance_id);
//console.log(attendance_id);

$("#edit_modal").modal("show");

$.ajax({
	url: "<?php echo site_url('gcctime/attendance/getAttendanceData'); ?>",
	type: "POST",
	data: {id : attendance_id,csrf_token: _csrf_hash},
	beforeSend: function(){
				// mApp.block('#` .modal-content', {
				// 		overlayColor: '#000000',
				// 		state: 'primary'
				// 	});

				// $(".blockUI.blockMsg .m-blockui").removeAttr("style");
			},
	success: function(data)
				{
					mApp.unblock('#edit_modal .modal-content');
					var result = $.parseJSON(data);

					if(result.status){
						$("input[name=employee_name_edit]").val(result.name);
						$("input[name=datetime]").val(result.datetime);
					}

				}

	});
});

//update edit form
$(".btn-submit-edit").on("click",function(){

	var id = $("input[name=attendance_id]").val();
	var datetime = $("#edit_modal input[name=datetime]").val();


	$.ajax({
		url: "<?php echo site_url('gcctime/Attendance/updateAttendanceData'); ?>",
		type: "POST",
		data: {id : id, datetime: datetime,csrf_token: _csrf_hash},
		beforeSend: function(){
				// mApp.block('#edit_modal .modal-content', {
				// 		overlayColor: '#000000',
				// 		state: 'primary'
				// 	});

				// $(".blockUI.blockMsg .m-blockui").removeAttr("style");
			},
		success: function(data)
			{
				mApp.unblock('#edit_modal .modal-content');
				var result = $.parseJSON(data);

				if(result.status){
					toastr.success(result.message);
					$("#edit_modal").modal("hide");
					generateCurrentAttendance();
				}else{
					toastr.error(result.message);
				}

			}
	});
});

// Add Attendance Validation
$.validate({
	form : '#form_add_attendance',
	lang: 'en',
	onSuccess : function(form) {
		$.ajax({
			url: form[0].action,
			type: "POST",
			data: $("#form_add_attendance").serialize(),
			beforeSend: function(){
				$(".btn-submit-add").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
			},
			success: function(data){
				$(".btn-submit-add").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
				$("#modal-new").modal('hide');
				$('#form_add_attendance')[0].reset();
				var result = $.parseJSON(data);
				_nTable.draw();

				if(result.status){
					toastr.success(result.message);
				}else{
					toastr.error(result.message);
				}
			}
		});
		return false;
	},
});

//clone datetimepicker
$(".btn-clone-datetimepicker").on("click",function(){ 
	var content = $(".datetimetemplate").clone(false);
	$(content).removeClass('datetimetemplate').appendTo( ".appendto" );

	$('.custom_datetimepicker').datetimepicker("destroy").datetimepicker({	
		todayHighlight: true,
		autoclose: true,
		format: 'yyyy-mm-dd hh:ii:ss'
	});

});

$('#datetimepicker').datetimepicker({
		todayHighlight: true,
		autoclose: true,
		format: 'yyyy-mm-dd hh:ii:ss'
	});

var d = new Date();
$('#appattendancedatetimepickerfrom').datetimepicker({
	todayHighlight: true,
	autoclose: true,
	format: 'yyyy-mm-dd',
});

$('#appattendancedatetimepickerto').datetimepicker({
	todayHighlight: true,
	autoclose: true,
	format: 'yyyy-mm-dd',
});


$(document).ready(function(){
	$("#employee_name").select2({
		placeholder: "Select an option",
		allowClear: true,
		dropdownParent: $('#m_daterangepicker_modal'),
	});

	$("#employee_name_new").select2({
		placeholder: "Select an option",
		allowClear: true,
		dropdownParent: $('#modal-new'),
	});

	$('#datetimepicker_edit').datetimepicker({
		todayHighlight: true,
		autoclose: true,
		format: 'yyyy-mm-dd hh:ii:ss'
	});

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
		use24hours: true,

	});
//time picker init end

generateCurrentAttendance();

});

var temp_data = {};
<?php if(isset($assigned_device) && $assigned_device): ?>
temp_data = <?php echo json_encode($assigned_device); ?>;
<?php endif; ?>

var vm = new Vue({
	el: "#form_upload_attendance",
	data: { post: temp_data },
	mounted: function(){
		validateFormUpload();
	}
});

$(function () {
'use strict';

var url = "<?php echo site_url('gcctime/attendance/upload_attendance_file'); ?>";
$('#fileupload').fileupload({
	url: url,
	dataType: 'json',
	done: function (e, data) {
		$.each(data.result.files, function (index, file) {
			$('<p/>').text(file.name).appendTo('#files');
		});
		
		mApp.blockPage({
			overlayColor: '#000000',
			type: 'loader',
			state: 'primary',
			message: 'Please wait...',
		});

		$(".blockUI.blockOverlay").css("z-index","1999");
		$(".blockUI.blockMsg .m-blockui").removeAttr("style");
		$(".blockUI.blockMsg.blockPage").css("z-index","2000");
		
		_dtPreview.clear().draw();
		
		$("input[name=full_path]").val(data.result.files[0]['full_path']);
		$.each(data.result.uploaded_data, function (index, val) {
			var datec = val.datetime;
			var res = datec.split(" ");
			if(index != 0){
				_dtPreview.row.add([val.id, val.name, res[0], res[1]]).draw();
			}
		});
		
		mApp.unblockPage();
		
	}, progressall: function (e, data) {
		var progress = parseInt(data.loaded / data.total * 100, 10);
		$('#progress .progress-bar').css(
			'width',
			progress + '%'
		);
	}
}).prop('disabled', !$.support.fileInput)
	.parent().addClass($.support.fileInput ? undefined : 'disabled');
});

var _dtPreview = $("#import-attendance-preview").DataTable({
	pageLength: 0,
	scrollY: '50vh',
	scrollCollapse: true,
	paging: false,
});

function validateFormUpload(){
	$.validate({
		form : '#form_upload_attendance',
	    lang: 'en',
	    onSuccess : function(form) {
			var formUrl = form[0].action;
			var formData = $(form[0]).serialize();
			
			$.ajax({
				url: formUrl,
				type: "post",
				dataType: "json",
				data: formData,
				beforeSend: function(){
					mApp.blockPage({
						overlayColor: '#000000',
						type: 'loader',
						state: 'primary',
						message: 'Please wait...',
					});

					$(".blockUI.blockOverlay").css("z-index","1999");
					$(".blockUI.blockMsg .m-blockui").removeAttr("style");
					$(".blockUI.blockMsg.blockPage").css("z-index","2000");
				},
				success: function(json){
					if(json.response){
						toastr.success(json.toastr_msg);
						form[0].reset();
						_dtPreview.clear().draw();
						$('#progress .progress-bar').css('width', '0%');
					}else{
						toastr.error(json.toastr_msg);
					}
					
					mApp.unblockPage();
				}

			});
	    	return false;
	    },
	});
}

var generate_attendance_record = function(){
	$.ajax({
		url: siteUrl("gcctime/attendance/generate_attendance_record"),
		dataType: "json",
		success: function(json){
			if(json.response){}
		}
	});
}

if(typeof modalGenerateAttendance !== "undefined" && modalGenerateAttendance.length == 1){
	var dtTempTable;

	var attLogSelect2 = modalGenerateAttendance.find("select#attendance_log");
	if(typeof attLogSelect2 !== "undefined"){
		$.ajax({
			url: siteUrl('gcctime/attendance/get_attendance_log_files'),
			dataType: "json",
			success: function(json){
				attLogSelect2.select2({
					width: "100%",
					allowClear: true,
					dropdownParent: modalGenerateAttendance,
					placeholder: "select an option",
					data: json.results,
				}).on("select2:select", function(e){
					var tempFilename = e.params.data.id;
					if(typeof tempFilename !== "undefined"){
						$.ajax({
							url: siteUrl('gcctime/attendance/geneate_attendance_log_file'),
							dataType: "json",
							type: "post",
							data: { filename: tempFilename, [_csrf_token]: _csrf_hash },
							success: function(json){
								if(json.response){
									dtTempTable.rows.add(json.data);
									dtTempTable.draw();
								}
							}
						});
					}
				});
			}

		});		
	}
	var attendanceTable = modalGenerateAttendance.find("#attendance-table");
	if(typeof attendanceTable !== "undefined"){
		dtTempTable = attendanceTable.DataTable({
			columns: [
				{ data: "biometric_id", title: "Biometric #" },
				{ data: "datetime", title: "Date Time" },
				{ data: null, title: "&nbsp;" },
			]
		});
	}
}

function syncAppAttendanceDate(){
	const date_from = $("#appattendancedatetimepickerfrom").val();
	const date_to = $("#appattendancedatetimepickerto").val();
	// console.log(date);
	$.ajax({
		url: baseUrl("gcctime/attendance/sync_app_attendance"),
		type: "POST",
		data: {
			csrf_token: _csrf_hash,
			date_from: date_from,
			date_to: date_to
		},
		dataType: "JSON",
		success: function(resp){
			console.log(resp);
			if(resp > 0){
				toastr.success("Successfully inserted date", "Alert");
			}else{
				toastr.warning("No Attendance Found", "Alert");
			}
		}
	});
}
</script>
<style type="text/css">
	.btn.btn-default:hover, 
	.btn.btn-default.active, 
	.btn.btn-default:active, 
	.btn.btn-default:focus, 
	.show > .btn.btn-default.dropdown-toggle, 
	.btn.btn-secondary:hover, 
	.btn.btn-secondary.active, 
	.btn.btn-secondary:active, 
	.btn.btn-secondary:focus, 
	.show > 
	.btn.btn-secondary.dropdown-toggle{
		background-color: #c3c3c3;
	}
	.btn-secondary:not(:disabled):not(.disabled).active:focus, 
	.btn-secondary:not(:disabled):not(.disabled):active:focus, 
	.show>.btn-secondary.dropdown-toggle:focus{
		box-shadow: 0 0 0 0.2rem rgb(255, 255, 255);
	}
</style>
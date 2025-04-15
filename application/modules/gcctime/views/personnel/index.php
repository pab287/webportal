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
		<!-- <div class="col-xl-2">
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
												Name: <strong><?php// echo $device["device_name"];?></strong>
											</span>
										</div>
										<div class="m-widget16__active-device m--margin-bottom-10">
											<span class="m-widget16__active-device-text">
												IP Address: <strong><?php// echo $device["ip_address"];?></strong>
											</span>
										</div>
										<div class="m-widget16__active-device m--margin-bottom-10">
											<span class="m-widget16__active-device-text">
												Status <?php// echo $status;?>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> -->
		<div class="col-xl-12">
			<div class="m-portlet m-portlet--mobile ">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								List of Personnels
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<ul class="m-portlet__nav">
							<li class="m-portlet__nav-item">
								<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
									<!-- <button type="button" class="m-btn btn btn-secondary btnAdvance_search"><i class="m-nav__link-icon flaticon-open-box"></i> Advance Search</button> -->
									<!--button type="button" class="m-btn btn btn-secondary btnUpload"><i class="m-nav__link-icon flaticon-multimedia-2"></i> Upload File</button-->
									<div class="btn-group">
										<button class="btn btn-secondary btn-sm dropdown-toggle btnUpdate" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											Update Personnel
										</button>
										<div class="dropdown-menu">
											<a class="dropdown-item" onclick="updatePersonnelData();" href="javascript:void(0)">
												<i class="m-nav__link-icon flaticon-technology"></i> Data</a>
											<a class="dropdown-item" onclick="updatePersonnelStatus();" href="javascript:void(0)">
												<i class="m-nav__link-icon flaticon-map"></i> Status</a>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item" onclick="syncEmployeeData();" href="javascript:void(0)">
												<i class="m-nav__link-icon flaticon-users"></i> Sync Employee</a>
										</div>
									</div>
									<!--div class="btn-group">
										<button class="btn btn-secondary btn-sm dropdown-toggle btnSync" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											Sync Data
										</button>
										<div class="dropdown-menu">
											<a class="dropdown-item" onclick="syncdata()" href="javascript:void(0)">
												<i class="m-nav__link-icon flaticon-technology"></i> Active</a>
											<a class="dropdown-item" onclick="syncMultiData()" href="javascript:void(0)">
												<i class="m-nav__link-icon flaticon-map"></i> Multiple</a>
										</div>
									</div -->
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="m-portlet__body">
					<!--begin: Datatable -->
					<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
						<table class="table table-striped table-bordered" id="personnel-datatable" width="100%" style="width: 100%;">
							<col width="7%">
							<col width="22%">
							<col width="12%">
							<!-- col width="10%" -->
							<col width="12%">
							<!-- col width="10%" -->
							<!-- col width="7%" -->
							<col width="*">
							<!-- col width="5%" -->
							<col width="5%">
							<col width="12%">
							<thead>
								<tr>
									<th>Biometric #</th>
									<th>Name</th>
									<th>Assigned Shift</th>
									<!-- th>Location</th --->
									<th>Mobile Device</th>
									<!-- th>Department</th -->
									<!-- th>Role</th -->
									<th>Site Locations</th>
									<!-- th>Flexible</th -->
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					<!--end: Datatable -->
				</div>
			</div>
		</div>
	</div>
	</div>
</div>
<div class="modal" id="modal-personnel_edit" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<form id="form-personnel-edit" action="<?php echo site_url("gcctime/personnel/update_personnel_data"); ?>" method="post">
			<div class="modal-header">
				<h5 class="modal-title">Update Personnel</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
					<input type="hidden" id="id" name="id" value="0" />
					<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
					<div class="form-group">
						<label class="form-control-label required">Biometric No</label>
						<input type="text" id="biometricno" name="biometricno" class="form-control inptBiometricno" autocomplete="off" data-validation="required" onkeypress="return isNumber(event)" />
					</div>
					<div class="form-group">
						<label class="form-control-label required">Name</label>
						<input type="text" id="employee_name" name="name" class="form-control inptName" autocomplete="off" data-validation="required" />
					</div>
					<div class="row">
						<div class="form-group col-lg-6">
							<label class="form-control-label">Mobile Name</label>
							<input type="text" id="mobile_name" name="mobile_name" class="form-control inptName" autocomplete="off" />
						</div>
						<div class="form-group col-lg-6">
							<label class="form-control-label">Mobile ID</label>
							<input type="text" id="mobile_id" name="mobile_id" class="form-control inptName" autocomplete="off" />
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-12 col-12 col-lg-12 col-xl-12 col-sm-12">
							<label for="flexi-dropdown" class="form-control-label">Flexible Time</label>
							<select name="is_flexi" id="flexi-dropdown" class="form-control">
								<option value="4">Default - No time in or out</option>
								<option value="3">Super Flexi - 1 in or 1 out</option>
								<option value="1">Flexi - 1 in and 1 out</option>
								<option value="2">Drivers - 1 in and 1 out</option>
								<option value="0">Regular - 2 in and 2 out</option>
							</select>

							<!-- Original source code for reference -->
							<!-- <div class="m-checkbox-inline">
								<label class="m-checkbox"><input id="isflexi1" type="radio" name="is_flexi" value="1">Yes<span></span></label>
								<label class="m-checkbox"><input id="isflexi2" type="radio" name="is_flexi" value="2">1 IN 1 OUT ONLY<span></span></label>
								<label class="m-checkbox"><input id="isflexi3" type="radio" name="is_flexi" value="3">SUPER FLEXI<span></span></label>
								<label class="m-checkbox"><input id="isflexi0" type="radio" name="is_flexi" value="0" checked>No<span></span></label>
							</div> -->
							<!-- Original source code for reference -->
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-6">
							<label class="form-control-label">Status</label>
							<div class="m-checkbox-inline">
								<label class="m-checkbox"><input id="is_active1" type="radio" name="is_active" value="1" checked="">Active<span></span></label>
								<label class="m-checkbox"><input id="is_active0" type="radio" name="is_active" value="0">Inactive<span></span></label>
							</div>
						</div>
						<div class="col-md-6">
							<label class="form-control-label">Hourly Rate</label>
							<div class="m-checkbox-inline">
								<label class="m-checkbox"><input id="is_perhour1" type="radio" name="is_perhour" value="1" >Yes<span></span></label>
								<label class="m-checkbox"><input id="is_perhour0" type="radio" name="is_perhour" value="0" checked>No<span></span></label>
							</div>
						</div>
					</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-success btnSave btn-submit">Save</button>
			</div>
			</form>
		</div>
	</div>
</div>

<!-- advance personnel search -->
<div class="modal" id="modal-advanced_search" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<form id="form-personnel-edit" action="<?php echo site_url("gcctime/personnel/update_personnel_data"); ?>" method="post">
			<div class="modal-header">
				<h5 class="modal-title">Advanced Search</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-danger btnSubmit-search btn-submit">Save</button>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal" id="modal-assign_schedule" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
		<form id="frmAssignShiftSchedule" method="post" action="<?php echo site_url("gcctime/personnel/set_assigned_shift"); ?>">
			<input type="hidden" name="id" v-model="temp_items.id" value="0" />
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<div class="modal-header">
			<h5 class="modal-title">Assign Shift Schedule</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="form-control-label">Biometric No</label>
							<p class="form-control" v-bind:text-content.prop="temp_items.biometricno">&nbsp;</p>
						</div>
						<div class="form-group">
							<label class="form-control-label">Employee Name</label>
							<p class="form-control" v-bind:text-content.prop="temp_items.name">&nbsp;</p>
						</div>
						<div class="form-group">
							<label class="form-control-label">Status</label>
							<p class="form-control" v-bind:text-content.prop="temp_items.is_active">&nbsp;</p>
						</div>
					</div>
					<div class="col-md-8">
					<div class="m-portlet m-portlet--rounded m-portlet--info m-portlet--head-solid-bg m-portlet--bordered">
						<div class="m-portlet__head">
							<div class="m-portlet__head-caption">
								<div class="m-portlet__head-title">
									<span class="m-portlet__head-icon">
										<i class="flaticon-settings"></i>
									</span>
									<h3 class="m-portlet__head-text">Settings</h3>
								</div>
							</div>
						</div>
						<div class="m-portlet__body">
							<div class="form-group">
								<label for="department_settings">Department</label>
								<select id="department_settings" class="form-control" name="department_id" v-model="temp_items.department_id">
									<option value=""></option>
								</select>
							</div>
							<div class="form-group">
								<label for="location_settings">Location</label>
								<select id="location_settings" class="form-control" name="location_id" v-model="temp_items.location_id">
									<option value=""></option>
								</select>
							</div>
							<div class="form-group">
								<label for="shift_settings">Shift Schedule</label>
								<select id="shift_settings" class="form-control" name="shift_id" v-model="temp_items.shift_id">
									<option value=""></option>
								</select>
							</div>
						</div>
					</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
			<button type="submit" class="btn btn-success btn-submit btnSave btnUpdateAssignedShift">Save</button>
			<button class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal" id="modal-personnel_delete" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Remove Personnel</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<h5>Are you sure you want to remove this personnel?</h5>
				<input type="hidden" id="personnel_id" value="0" />
				<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-danger btn-delete_item btnDelete">Yes</button>
			<button class="btn btn-secondary" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="modal-personnel_site_restric" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form action="" id="set_site">
			<div class="modal-header">
				<h5 class="modal-title">Set Location Site</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="col-lg-12">
					<input type="hidden" name="id" id="site_personnel_id" value="0">
					<select name="sites" id="locations" class="form-control" multiple="multiple">
						<option></option>
					</select>
				</div>
				<div id="allLocationAssigned" class="col-lg-12 mt-3">
					<ul v-for="(item, index) in row" class="list-group">
						<b><li class="list-group-item"><label v-text="item.location_name"></label> <span v-on:click="itemdelete(item)" class="la la-trash text-danger p-0 icon-delete"></span></li></b>
					</ul>
				</div>
			</div>
			<div class="modal-footer">
			<button type="submit" class="btn btn-primary btnSave">Set Location</button>
			</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	var table = $('#personnel-datatable').DataTable({
		dom: '<"toolbar">frtlip',
	    ajax: "<?php echo base_url('gcctime/personnel/getPersonnel'); ?>",
	    ordering: false,
		lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
		columnDefs: [{
			targets: [0, -2, -1],
			className: "text-center",
		}
	],
	});

	var syncdata = function()
	{
		$.ajax({
			url: "<?php echo base_url('gcctime/personnel/syncdata');?>",
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
				toastr.success("Personnel data sync successfully!");
				table.ajax.reload(null, false);
			}
		});
	}

	var	syncMultiData = function(){
		$.ajax({
			url: "<?php echo base_url('gcctime/personnel/syncPersonnelDataMultiDev'); ?>",
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
				toastr.success("Personnel data sync successfully!");
				table.ajax.reload(null, false);
			}
		});
	}

	var updatePersonnelData = function(){
		$.ajax({
			url: "<?php echo base_url('gcctime/personnel/update_personnel_details'); ?>",
			dataType: "json",
			beforeSend: function(){
				mApp.blockPage({
	                overlayColor: '#000000',
	                type: 'loader',
	                state: 'primary',
	                message: 'Please wait...',
	            });

				$(".blockUI.blockMsg .m-blockui").removeAttr("style");

			}, success: function(json){
				if(json.response){ toastr.success(json.toastr_msg); }
				else{ toastr.error(json.toastr_msg); }

				mApp.unblockPage();
				table.ajax.reload(null, false);
			}
		});
	}

	var syncEmployeeData = function(){
		console.log("test");
		$.ajax({
			url: "<?php echo base_url('gcctime/personnel/sync_employee_data'); ?>",
			dataType: "json",
			beforeSend: function(){
				mApp.blockPage({
	                overlayColor: '#000000',
	                type: 'loader',
	                state: 'primary',
	                message: 'Please wait...',
	            });

				$(".blockUI.blockMsg .m-blockui").removeAttr("style");

			}, success: function(json){
				if(json.response){ toastr.success(json.toastr_msg, "Sync Employee Record"); }
				else{ toastr.error(json.toastr_msg, "Sync Employee Record"); }

				mApp.unblockPage();
				table.ajax.reload(null, false);
			}
		});
	}

	var updatePersonnelStatus = function(){
		$.ajax({
			url: "<?php echo base_url('gcctime/personnel/update_personnel_status'); ?>",
			dataType: "json",
			beforeSend: function(){
				mApp.blockPage({
	                overlayColor: '#000000',
	                type: 'loader',
	                state: 'primary',
	                message: 'Please wait...',
	            });

				$(".blockUI.blockMsg .m-blockui").removeAttr("style");

			}, success: function(json){
				if(json.response){ toastr.success(json.toastr_msg); }
				else{ toastr.error(json.toastr_msg); }

				mApp.unblockPage();
				table.ajax.reload(null, false);
			}
		});
	}

	var addSiteLocation = function($id){
		$("#modal-personnel_site_restric").modal('show');
		$("#site_personnel_id").val($id);

		$.ajax({
			url: baseUrl("gcctime/site_points_location/all_appointed_location"),
			type: "POST",
			data: {
				csrf_token: _csrf_hash,
				id: $id
			},
			dataType: "json",
			success: function(resp){
				personnellocation.row = Object.assign({}, resp);
			}
		});

	}

	$("#locations").select2({
		placeholder: 'SELECT AN OPTION',
		width: '100%',
		allowClear: true,
		ajax: {
			url: baseUrl("gcctime/personnel/get_all_site"),
			processResults: function (data) {
				return data;
			}
		}
	});

	$("#set_site").on("submit", function(e){
		e.preventDefault();
		const sites = $("#locations").val();
		const person_id = $("#site_personnel_id").val();
		$.ajax({
			url: baseUrl("gcctime/personnel/personnel_locations"),
			type: "post",
			data: {
				csrf_token: _csrf_hash,
				id: person_id,
				site_names: sites
			},
			dataType: "JSON",
			success: function(resp){
				if(resp !== 0){
					personnellocation.row = Object.assign({}, resp);
					$("#locations").empty();
					table.ajax.reload();
				}
			}
		});
	});

	var personnellocation = new Vue({
		el: "#allLocationAssigned",
		data: {
			row: {},
		},
		methods: {
			itemdelete: function(data){
				const _this = this;
				$.ajax({
					url: baseUrl("gcctime/site_points_location/delete_appointed_location"),
					type: "POST",
					data: {
						csrf_token: _csrf_hash,
						id: data.id,
						personnel_id: data.personnel_id,
					},
					dataType: "json",
					success: function(resp){
						if(resp !== 0){
							_this.row = Object.assign({}, resp);
							table.ajax.reload();
						}else{
							console.log("error on the database");
						}
					}
				});
			}
		}
	});

	var deletepersonnel = function($id){
		var _modalWindow = $("#modal-personnel_delete");
		var tempInput = _modalWindow.find("input#personnel_id");
		if(typeof tempInput !== "undefined"){
			tempInput.val($id);
		}
		_modalWindow.modal("show");
	}

	$(document).on("click", "button.btn-delete_item", function(){
		var _modalWindow = $("#modal-personnel_delete");
		var tempInput = _modalWindow.find("input#personnel_id");
		if(typeof tempInput !== "undefined" && tempInput.val() !== 0){
			var dataId = tempInput.val();
			$.ajax({
				url: "<?php echo site_url("gcctime/personnel/deletepersonnel"); ?>",
				type: "post",
				dataType: "json",
				data: { id: dataId, csrf_token: _csrf_hash },
				beforeSend: function(){
					mApp.blockPage({
						overlayColor: '#000000',
						type: 'loader',
						state: 'primary',
						message: 'Please wait...',
					});

					$(".blockUI.blockMsg .m-blockui").removeAttr("style");
				},success: function(json){
					mApp.unblockPage();
					if(json.response){
						_modalWindow.modal("hide");
						table.ajax.reload();
						toastr.success("Personnel data has been removed");
					}else{
						toastr.success("Failed to remove personnel data!");
					}
				}
			});
		}
	});

	$.validate({
		form : '#form-personnel-edit',
	    lang: 'en',
	    onSuccess : function(form) {
			var _url = form[0].action;
			var _data = jQuery(form[0]).serialize();
			var _btnSubmit = $(form[0]).find(".btn-submit");

	    	$.ajax({
	    		url: _url,
	    		type: "POST",
				dataType: "json",
	    		data: _data, csrf_token: _csrf_hash,
	    		beforeSend: function(){
					if(typeof _btnSubmit !== "undefined"){ _btnSubmit.addClass("m-btn--custom m-loader m-loader--light m-loader--right"); }
	    		},
	    		success: function(data){
					if(data.response){
						table.ajax.reload();
						toastr.success(data.toastr_msg, "Update Personnel", 5000);
						$("#modal-personnel_edit").modal("hide");
					}else{ toastr.error(data.toastr_msg, "Error Personnel", 5000); }
					if(typeof _btnSubmit !== "undefined"){ _btnSubmit.removeClass("m-btn--custom m-loader m-loader--light m-loader--right"); }
	    		}
	    	});
			return false;
	    }
	});

	$(document).on("click", ".btnAssignPersonnelSchedule", function(){
		var _self = $(this);
		var _id = _self.data("id");

		$.ajax({
			url: "<?php echo site_url("gcctime/personnel/get_personnel_schedule"); ?>",
			type: "post",
			dataType: "json",
			data: { id: _id, csrf_token: _csrf_hash },
			beforeSend: function(){
				mApp.blockPage({
						overlayColor: '#000000',
						type: 'loader',
						state: 'primary',
						message: 'Please wait...',
					});

				$(".blockUI.blockMsg .m-blockui").removeAttr("style");
			},
			success: function(json){
				if(json.response){
					var datax = json.data;
					var dropdownItems = json.dropdown;
					var modalContent = $("#modal-assign_schedule").find(".modal-content");
					if(typeof modalContent !== "undefined"){
						modalContent.find("#department_settings").select2({
							data: dropdownItems.query_description.results,
							width: "100%",
							placeholder: "SELECT AN OPTION"
						});

						modalContent.find("#location_settings").select2({
							data: dropdownItems.query_location.results,
							width: "100%",
							placeholder: "SELECT AN OPTION"
						});

						modalContent.find("#shift_settings").select2({
							data: dropdownItems.query_shift.results,
							width: "100%",
							placeholder: "SELECT AN OPTION"
						});

						vmAssign.temp_items = Object.assign({}, datax);
						vmAssign.$mount();

						$("#modal-assign_schedule").modal("show");
					}
				}
				mApp.unblockPage();
			}
		});
	});

	$(document).on("click", ".btnEditPersonnel", function(){
		var _self = $(this);
		var _id = _self.data("id");
		$.ajax({
			url: "<?php echo site_url("gcctime/personnel/get_personnel_data"); ?>",
			type: "post",
			dataType: "json",
			data: { id: _id, csrf_token: _csrf_hash },
			success: function(json){
				if(json.response){
					var currentData = json.data;
					$("#form-personnel-edit").get(0).reset();
					$("#form-personnel-edit").find("#id").val(currentData.id);
					$("#form-personnel-edit").find("#mobile_name").val(currentData.mobile_name);
					$("#form-personnel-edit").find("#mobile_id").val(currentData.mobile_id);
					$("#form-personnel-edit").find("#employee_name").val(currentData.name);
					$("#form-personnel-edit").find("#biometricno").val(currentData.biometricno);

					if(currentData.is_active == 1){
						$("#form-personnel-edit").find("#is_active1").prop("checked", true);
						$("#form-personnel-edit").find("#is_active0").prop("checked", false);
					}else{
						$("#form-personnel-edit").find("#is_active0").prop("checked", true);
						$("#form-personnel-edit").find("#is_active1").prop("checked", false);
					}

					if(currentData.is_perhour == 1){
						$("#form-personnel-edit").find("#is_perhour1").prop("checked", true);
						$("#form-personnel-edit").find("#is_perhour0").prop("checked", false);
					}else{
						$("#form-personnel-edit").find("#is_perhour0").prop("checked", true);
						$("#form-personnel-edit").find("#is_perhour1").prop("checked", false);
					}

					// if(currentData.is_flexi == 1){
					// 	$("#form-personnel-edit").find("#isflexi1").prop("checked", true);
					// 	$("#form-personnel-edit").find("#isflexi2").prop("checked", false);
					// 	$("#form-personnel-edit").find("#isflexi3").prop("checked", false);
					// 	$("#form-personnel-edit").find("#isflexi0").prop("checked", false);
					// }else if(currentData.is_flexi == 2){
					// 	$("#form-personnel-edit").find("#isflexi2").prop("checked", true);
					// 	$("#form-personnel-edit").find("#isflexi3").prop("checked", false);
					// 	$("#form-personnel-edit").find("#isflexi1").prop("checked", false);
					// 	$("#form-personnel-edit").find("#isflexi0").prop("checked", false);
					// }else if(currentData.is_flexi == 3){
					// 	$("#form-personnel-edit").find("#isflexi3").prop("checked", true);
					// 	$("#form-personnel-edit").find("#isflexi0").prop("checked", false);
					// 	$("#form-personnel-edit").find("#isflexi1").prop("checked", false);
					// 	$("#form-personnel-edit").find("#isflexi2").prop("checked", false);
					// }else{
					// 	$("#form-personnel-edit").find("#isflexi0").prop("checked", true);
					// 	$("#form-personnel-edit").find("#isflexi1").prop("checked", false);
					// 	$("#form-personnel-edit").find("#isflexi2").prop("checked", false);
					// 	$("#form-personnel-edit").find("#isflexi3").prop("checked", false);
					// }

					$("#form-personnel-edit").find('#flexi-dropdown').select2({
						width: '100%',
						placeholder: 'Select a Option',
						dropdownParent: $("#modal-personnel_edit"),
						minimumResultsForSearch: -1
					}).val(currentData.is_flexi).trigger('change');
					$("#modal-personnel_edit").modal("show");
				}
			}
		});
	});

	$(document).on("submit", "form#frmAssignShiftSchedule", function(e){
		e.preventDefault();
		var _self = $(this);
		var _btnSubmit = _self.find(".btn-submit");
		$.ajax({
			url: _self.attr("action"),
			type: _self.attr("method"),
			dataType: "json",
			data: _self.serialize(),
			beforeSend: function(){
				if(typeof _btnSubmit !== "undefined"){ _btnSubmit.addClass("m-btn--custom m-loader m-loader--light m-loader--right"); }
			},
			success: function(json){
				if(json.response){
					table.ajax.reload();
					toastr.success("Assign Shift Schedule", json.toastr_msg, 5000);
					$("#modal-assign_schedule").modal("hide");
				}else{
					toastr.error("Assign Shift Schedule", json.toastr_msg, 5000);
				}
				if(typeof _btnSubmit !== "undefined"){ _btnSubmit.removeClass("m-btn--custom m-loader m-loader--light m-loader--right"); }
			}
		});
	});

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
	});

	var _tempItems = { id: 0, biometricno: "---", name: "---", department: "---", is_active: "Inactive", dapartment_id: 0, location_id: 0, shift_id: 0 };
	var vmAssign = new Vue({
		el : "#frmAssignShiftSchedule",
		data: { temp_items : _tempItems },
		mounted: function(){
			$("#department_settings, #location_settings, #shift_settings").trigger("change");
		}
	});

	function isNumber(evt) {
		var charCode = (evt.which) ? evt.which : evt.keyCode
		if (charCode > 31 && (charCode < 48 || charCode > 57))
			return false;
		return true;
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
	.show > .btn.btn-secondary.dropdown-toggle{
		background-color: #c3c3c3;
	}
	.btn-secondary:not(:disabled):not(.disabled).active:focus,
	.btn-secondary:not(:disabled):not(.disabled):active:focus,
	.show>.btn-secondary.dropdown-toggle:focus{
		box-shadow: 0 0 0 0.2rem rgb(255, 255, 255);
	}
</style>
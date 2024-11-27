<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Manage Devices
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<ul class="m-portlet__nav">
							<li class="m-portlet__nav-item"></li>
						</ul>
					</div>
				</div>
				<div class="m-portlet__body">
					<!--begin: Datatable -->
						<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
							<table class="table table-striped table-bordered" id="table-devices">
								<col width="*">
								<col width="20%">
								<col width="10%">
								<col width="6%">
								<col width="10%">
								<col width="6%">
								<col width="6%">
								<col width="5%">
								<thead>
									<tr>
										<th>Name</th>
										<th>Location</th>
										<th>IP Address</th>
										<th>Port</th>
										<th>Status</th>
										<th>Active</th>
										<th>Override</th>
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
			<!--end::Portlet-->
		</div>
	</div>
</div>

<!--new begin::Modal-->
<div class="modal fade" id="modal-new" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">New Device</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="<?php echo base_url('gcctime/devices/add_device')?>" method="POST" id="form-devices">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<div class="modal-body">
				<div class="form-group">
					<label class="form-control-label">Name *</label>
					<input type="text" name="device_name" class="form-control" data-validation="required" autocomplete="off" />
				</div>
				<div class="form-group">
					<label class="form-control-label">IP Address *</label>
					<input type="text" name="ip_address" class="form-control" data-validation="required" autocomplete="off" />
				</div>
				<div class="form-group">
					<label class="form-control-label">Port *</label>
					<input type="text" name="port" class="form-control" data-validation="required" autocomplete="off" />
				</div>
				<div class="form-group">
					<label class="form-control-label">Location *</label>
					<select id="location" name="location_id" class="form-control select2">
						<option>&nbsp;</option>
					</select>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="form-control-label">Active</label>
							<div class="m-checkbox-inline">
								<label class="m-checkbox">
									<input type="radio" name="is_active" value="1" checked>Yes<span></span>
								</label>
								<label class="m-checkbox">
									<input type="radio" name="is_active" value="0">No<span></span>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="form-control-label">Allow Override</label>
							<div class="m-checkbox-inline">
								<label class="m-checkbox">
									<input type="radio" name="allow_override" value="1">Yes<span></span>
								</label>
								<label class="m-checkbox">
									<input type="radio" name="allow_override" value="0" checked>No<span></span>
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary btm-submit btnSave">Save</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
			</div>
			</form>
		</div>
	</div>
</div>
<!--new end::Modal-->

<!-- edit begin::Modal-->
<div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					Edit Device
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
				</button>
			</div>
			<form id="form-devices-edit" action="<?php echo base_url('gcctime/devices/edit_device'); ?>" method="POST">
			<div id="edit_device-content">
				<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<input type="hidden" name="id" v-model="row.id" />
				<div class="modal-body">
					<div class="form-group">
						<label class="form-control-label">Name *</label>
						<input type="text" name="device_name" class="form-control" data-validation="required" v-model="row.device_name" autocomplete="off" />
					</div>
					<div class="form-group">
						<label class="form-control-label">IP Address *</label>
						<input type="text" name="ip_address" class="form-control" data-validation="required" v-model="row.ip_address" autocomplete="off" />
					</div>
					<div class="form-group">
						<label class="form-control-label">Location *</label>
						<select id="location" name="location_id" class="form-control select2">
							<option>&nbsp;</option>
						</select>
					</div>
					<div class="form-group">
					<label class="form-control-label">Port *</label>
					<input type="text" name="port" class="form-control" data-validation="required" v-model="row.port" autocomplete="off" />
				</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-control-label">Active</label>
								<div class="m-checkbox-inline">
									<label class="m-checkbox">
										<input type="radio" name="is_active" value="1" checked v-model="row.is_active">Yes<span></span>
									</label>
									<label class="m-checkbox">
										<input type="radio" name="is_active" value="0" v-model="row.is_active">No<span></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-control-label">Allow Override</label>
								<div class="m-checkbox-inline">
									<label class="m-checkbox">
										<input type="radio" name="allow_override" value="1" v-model="row.allow_override">Yes<span></span>
									</label>
									<label class="m-checkbox">
										<input type="radio" name="allow_override" value="0" checked v-model="row.allow_override">No<span></span>
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary btm-submit-edit btnUpdate">Save</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
			</div>
			</form>
		</div>
	</div>
</div>
<!--edit end::Modal-->

<!-- delete begin::Modal-->
<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Delete Device</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<i class="la-2x la la-warning"></i> Are you sure you want to delete this device? 
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary btm-submit-delete btnDelete" data-id="">Yes</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>
<!--delete end::Modal-->

<!-- connect begin::Modal-->
<div class="modal fade" id="modal-connect" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Connect Device</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<i class="la la-info-circle"></i>  Are you sure you want to establish connection with this device? 
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary btm-submit-connect btnConnect" data-id="">Yes</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>
<!--connect end::Modal-->
<script type="text/javascript">
	var table = $("#table-devices").DataTable({
        dom: '<"toolbar">frtlip',
	    ajax: "<?php echo base_url('gcctime/devices/getDeviceCollection'); ?>",
		lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
		serverSide: false,
		processing: false,
		columns: [
			{ data: "device_name" },
			{ data: "location" },
			{ data: "ip_address" },
			{ data: "port", className: "text-center", orderable: false },
			{ data: "status", className: "text-center", orderable: false, 
				render: function(data){
					var tempHtml = "<span class='m-badge m-badge--danger m-badge--wide'><strong>Disconnected</strong></span>";
					if(data == "1" || data == 1){
						tempHtml = "<span class='m-badge m-badge--success m-badge--wide'><strong>Connected</strong></span>";
					}
					return tempHtml;
				}
			}, { data: "is_active", className: "text-center", orderable: false, 
				render: function(data){
					var tempHtml = "<i class='fa-lg fa fa-remove m--font-danger'></i>";
					if(data == "1" || data == 1){
						tempHtml = "<i class='fa-lg fa fa-check m--font-success'></i>";
					}
					return tempHtml;
				} 
			}, { data: "allow_override", className: "text-center", orderable: false, 
				render: function(data){
					var tempHtml = "<i class='fa-lg fa fa-remove m--font-danger'></i>";
					if(data == "1" || data == 1){
						tempHtml = "<i class='fa-lg fa fa-check m--font-success'></i>";
					}
					return tempHtml;
				}
			}, { data: null, className: "text-center", orderable: false, render: function(data, type, row, meta){
				var tempHtml = "---"; 
				var settings = meta.settings;
				var jsonData = settings.json;
				var ctr = 0;
				var tempActions = [];
				var currentActions = ["edit", "delete", "connect", "disconnect", "sync"];
				if(typeof jsonData.actions == "object"){
					$.each(currentActions, function(index, value){
						if($.inArray(value, jsonData.actions) !== "-1"){
							tempActions.push(value);
						}
					});
				}
				if(tempActions.length > 1){
					tempHtml = `<div class="dropdown">
						<a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown"> 
							<i class="la la-ellipsis-h"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right">`;
					$.each(tempActions, function(ii, vv){
						switch(vv){
							case "edit":
							tempHtml += `<a class="dropdown-item " href="javascript:void(0);" onclick='edit_device(`+row.id+`)'><i class="la la-edit"></i> Edit Device</a>`;
							break;
							case "delete":
							tempHtml += `<a class="dropdown-item " href="javascript:void(0);" onclick='delete_device(`+row.id+`)'><i class="la la-trash"></i> Remove Device</a>`;
							break;
							case "sync":
								if(row.status == 1){
									tempHtml += `<div class='dropdown-divider'></div>`;
									tempHtml += `<a class="dropdown-item " href="javascript:void(0);" onclick='sync_device(`+row.id+`)'><i class="la la-edit"></i> Sync Device</a>`;
								}
							break;
							case "connect":
								var tempLabel = "Disconnect Device";
								var tempIconClass = "la la-unlink";
								var tempEvent = 'disconnect_device('+row.id+')';
								if(row.status == 0){
									tempEvent = 'connect_device('+row.id+')';
									tempLabel = "Connect Device";
									tempIconClass = "la la-link";
								}
								tempHtml += `<div class='dropdown-divider'></div>`;
								tempHtml += `<a class="dropdown-item " href="javascript:void(0);" onclick='`+tempEvent+`'><i class="`+tempIconClass+`"></i> `+tempLabel+`</a>`;
							break;
						}
					});
					tempHtml += `</div></div>`;
				}else if(tempActions.length == 1){
					var xTemp = tempActions[0];
					switch(xTemp){
						case "edit":
						tempHtml += `<a class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit" href="javascript:void(0);" onclick='edit_device(`+row.id+`)'><i class="la la-edit"></i></a>`;
						break;
						case "delete":
						tempHtml += `<a class="btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDelete" href="javascript:void(0);" onclick='delete_device(`+row.id+`)'><i class="la la-trash"></i></a>`;
						break;
						case "sync":
							if(row.status == 1){
								tempHtml += `<a class="btn m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnSync" href="javascript:void(0);" onclick='sync_device(`+row.id+`)'><i class="la la-refresh"></i></a>`;
							}
						break;
						case "connect":
							var tempIconClass = "la la-unlink";
							var tempEvent = 'disconnect_device('+row.id+')';
							if(row.status == 0){
								tempEvent = 'connect_device('+row.id+')';
								tempIconClass = "la la-link";
							}
							tempHtml += `<a class="btn m-btn m-btn--hover-primary m-btn--icon m-btn--icon-only m-btn--pill btnConnect" href="javascript:void(0);" onclick='`+tempEvent+`'><i class="`+tempIconClass+`"></i></a>`;
						break;
					}
				}
				return tempHtml;
			} },
		],
	});

	$("div.toolbar").html('<button type="button" class="m-portlet__nav-link btn m-btn--square btn-success btnNew" data-toggle="modal" data-target="#modal-new"><i class="fa fa-plus"></i> New </button>');

	var tempModalNew = $("#modal-new");
	
	$(document).ready(function(){
		tempModalNew.find("select#location").select2({
			width: "100%",
			placeholder: "Select an option",
			dropdownParent: $("#modal-new"),
			ajax: {
				url: "<?php echo site_url('gcctime/location/get_select2_location'); ?>",
				dataType: "json",
				global: false,
				processResults: function(data){
					return data;
				}
			}
		});
	});

	// New Device Validation
	$.validate({
		form : '#form-devices',
	    lang: 'en',
	    onSuccess : function(form) {
	    	$.ajax({
	    		url: form[0].action,
	    		type: "POST",
	    		data: $("#form-devices").serialize(),
	    		beforeSend: function(){
	    			$(".btm-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    		},
	    		success: function(data){
	    			$(".btm-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    			$("#modal-new").modal('hide');
	    			var result = $.parseJSON(data);
	    			table.draw();
					table.ajax.reload();
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

	// Edit Device Validation
	$.validate({
		form : '#form-devices-edit',
	    lang: 'en',
	    onSuccess : function(form) {
			var currentForm = form[0];
			var formData = $(currentForm).serialize();

	    	$.ajax({
	    		url: currentForm.action,
	    		type: "POST",
				data: $("#form-devices-edit").serialize(),
				dataType: "json",
	    		beforeSend: function(){
	    			$(".btm-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    		},
	    		success: function(json){
	    			if(json.status){
						table.ajax.reload();
						toastr.success(json.message);
						$("#modal-edit").modal("hide");
					}else{
						toastr.error(json.message);
					}
					$(".btm-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    		}
	    	});
	    	return false;
	    },
	});

	//delete process
	$(".btm-submit-delete").on("click",function(){
		var id = $(this).attr("data-id");
		$.ajax({
			url: "<?php echo site_url('gcctime/Devices/delete_device');?>",
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
				$("#modal-delete").modal("hide");
				
				if(result.status){
					table.ajax.reload();
					toastr.success(result.message);
				}else{
					toastr.error(result.message);
				}

			}
		});
	});

	//connect process
	$(".btm-submit-connect").on("click",function(){
		var id = $(this).attr("data-id");
		$.ajax({
			url: "<?php echo site_url('gcctime/devices/connect_device'); ?>",
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
				$("#modal-connect").modal("hide");
				if(result.status){
					table.ajax.reload();
					toastr.success(result.message);
				}else{
					toastr.error(result.message);
				}

			}
		});
	});


	// Edit Device Modal Form
	var edit_device = function(id){
		$.ajax({
			url: "<?php echo site_url('gcctime/devices/getDevice'); ?>",
			type: "POST",
			data: {id : id, csrf_token: _csrf_hash},
			dataType: "json",
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
				mApp.unblockPage();
				if(json.response){
					vmDevice.row = Object.assign({}, json.row);
					vmDevice.setSelect2Location();
					$("#modal-edit").modal("show");
				}
			}
		});
		
	}


	// Delete Device Modal Form
	var delete_device = function(id){
		event.preventDefault();
		$("#modal-delete").modal("show");
		$(".btm-submit-delete").attr("data-id",id);

	}

	// Connect Device Modal Form
	var connect_device = function(id){
		$("#modal-connect").modal("show");
		$("#modal-connect").find(".btm-submit-connect").attr("data-id", id);
	}

	var sync_device = function(id){
		if(id){
			$.ajax({
				url: "<?php echo site_url("gcctime/curl_request/syncdata_device/false"); ?>" + "/" + id,
				dataType: "json",
				success: function(json){
					if(json.response){
						toastr.success("Sync Device", "Sync device successful", { timeOut: 5000 });
					}else{
						toastr.error("Sync Device", "Sync device failed", { timeOut: 5000 });
					}
				}
			});
		}
	}
	// Disconnect Device Modal Form
	var disconnect_device = function(){
		return false;
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
	});
	var vmDevice = new Vue({
		el: "#edit_device-content",
		data: { row: {} },
		methods: {
			setSelect2Location: function(){
				var _this = this;
				var currentRow = _this.row;
				var tempLocation = $(_this.$el).find("select#location");
				tempLocation.empty();
				if(typeof currentRow.location_name !== "undefined" && currentRow.location_name !== null){
					var tempOption = new Option(currentRow.location_name, currentRow.location_id, true, true);
					tempLocation.html(tempOption);
				}
				tempLocation.select2({
					width: "100%",
					placeholder: "Select an option",
					dropdownParent: $("#modal-edit"),
					ajax: {
						url: "<?php echo site_url('gcctime/location/get_select2_location'); ?>",
						dataType: "json",
						global: false,
						processResults: function(data){
							return data;
						}
					}
				});
			}
		}
	});
</script>
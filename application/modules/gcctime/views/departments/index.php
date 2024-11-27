<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Department List
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
								<table class="table table-striped table-bordered" id="table_departments">
									<col>
									<col">
									<col>
									<col>
									<thead>
										<tr>
											<th>Name</th>
											<th>Head</th>
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

<!-- New Department begin::Modal-->
<div class="modal fade" id="modal-new" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" >
					New Department
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="form_new_deparment" action="<?php echo site_url("gcctime/Department/processNew");?>" method="POST">
				<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
					<div class="form-group">	
						<label class="form-control-label">
							Name:
						</label>
						<input type="text" name="description" class="form-control" data-validation="required">
					</div>
					<div class="form-group">
						<label class="form-control-label">
							Department Head:
						</label>
						<select id="personnelList" name="head_id" class="form-control">
							<option disabled="disabled" selected="selected">Select</option>
							<?php
								$getPersonnelList = $this->crud->getCollection(array(),"gcctimeutility.personnel");
								if($getPersonnelList):
									foreach($getPersonnelList as $_getPersonnelList):
								?>
										<option value="<?php echo $_getPersonnelList["id"];?>"><?php echo $_getPersonnelList["name"];?></option>
								<?php
									endforeach;
								endif;
							?>
						</select>
					</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Cancel
				</button>
				<button type="submit" class="btn btn-success btm-submit btnSave">
					Submit
				</button>
			</form>
			</div>
		</div>
	</div>
</div>
<!-- New Department end::Modal-->

<!-- Edit Department begin::Modal-->
<div class="modal fade" id="modal-edit" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" >
					Edit Department
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="form_edit_deparment" action="<?php echo site_url("gcctime/Department/processEdit");?>" method="POST">
				<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
					<input type="hidden" name="id" class="form-control" data-validation="required">
					<div class="form-group">	
						<label class="form-control-label">
							Name:
						</label>
						<input type="text" name="description" class="form-control" data-validation="required">
					</div>
					<div class="form-group">
						<label class="form-control-label">
							Department Head:
						</label>
						<select id="personnelListEdit" name="head_id" class="form-control">
							<?php
								$getPersonnelList = $this->crud->getCollection(array(),"gcctimeutility.personnel");
								if($getPersonnelList):
									foreach($getPersonnelList as $_getPersonnelList):
								?>
										<option value="<?php echo $_getPersonnelList["id"];?>"><?php echo $_getPersonnelList["name"];?></option>
								<?php
									endforeach;
								endif;
							?>
						</select>
					</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
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
<!-- Edit Department end::Modal-->
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
<!-- Personnel List begin::Modal-->
<div class="modal fade" id="modal-select-personnel" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" >
					Select Personnel
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="hidden" name="selected-dept-id" id="selected-dept-id">
				<input type="hidden" name="selected-late-id" id="selected-late-id">
				<table id="table_personnel_select" class="table table-striped table-bordered" style="width:100%">
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
											<a href="javascript:void(0)" class="m-nav__link btn-submit-multi-personnel btnSelect">
												<span class="m-nav__link-text">
													Select
												</span>
											</a>
										</li>
										<li class="m-nav__item">
											<a href="javascript:void(0)" class="m-nav__link btn-submit-desmulti-personnel btnDeselect">
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
<!--Personnel end::Modal-->

<script type="text/javascript">
	var table = $("#table_departments").DataTable({
		"dom": '<"toolbar">frtlip',
		"ajax": "<?php echo base_url('gcctime/Department/getCollection');?>",
		"lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
		"serverSide": false,
		"processing": false,
		"columns": [{ width: "*" }, { width: "25%" }, { width: "10%", orderable: false }]
	});

	var	table_select_personnel = $("#table_personnel_select").DataTable({
		ajax: {
			url: "<?php echo base_url('gcctime/Personnel/getSelectPersonnel');?>",
			type: "post",
			data: function(d){
				d.csrf_token = _csrf_hash;
					var dt_params = $('#table_departments').data('dt_params');
					if(dt_params){ $.extend(d, dt_params); }
			}
		},
		lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
		serverSide: true,
	    processing: true,
		columns: [{
			data: "chkbox",
			width: "2%",
		},{
			data: "employee_name",
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
		        className: 'text-center'
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
		var	table_assigned_personnel = $("#table_assigned").DataTable({
		ajax: {
			url: "<?php echo base_url('gcctime/Personnel/getAssignedPersonnel');?>"+"/"+id,
			type: "post",
			data: function(d){
				d.csrf_token = _csrf_hash;
					var dt_params = $('#table_departments').data('dt_params');
					if(dt_params){ $.extend(d, dt_params); }
			}
		},
		lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
		serverSide: true,
	    processing: true,
		columns: [{
			data: "employee_name",
			width: "100%",
		}],
	    columnDefs: [{
		        targets: 0,
		        className: 'text-center'
		    }],
		paging: false,
		info: false,
		order: [[ 0, "asc" ]],
		scrollY: '50vh',
		scrollCollapse: true,
		destroy: true,
	});
	table_assigned_personnel.ajax.reload();
	}
	// new button in table
	$("div.toolbar").html('<button type="button" class="m-portlet__nav-link btn m-btn--square btn-success btnNew"  data-toggle="modal" data-target="#modal-new"><i class="fa fa-plus"></i> New </button>');

	// Add new department Validation
	$.validate({
		form : '#form_new_deparment',
	    lang: 'en',
	    onSuccess : function(form) {
	    	$.ajax({
	    		url: form[0].action,
	    		type: "POST",
	    		data: $("#form_new_deparment").serialize(),
	    		beforeSend: function(){
	    			$(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    		},
	    		success: function(data){
	    			$(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    			$("#modal-new").modal('hide');
	    			$('.modal-backdrop').remove()
					$(document.body).removeClass("modal-open");

	    			var result = $.parseJSON(data);

	    			if(result.status){
						toastr.success(result.message);
						table.ajax.reload();
						$('#form_new_deparment')[0].reset();

						/*** location.reload(); ***/
					}else{
						toastr.error(result.message);
					}
	    		}
	    	});
	    	return false;
	    },
	});

	//start edit dept
	var edit_dept = function(id)
	{
		$("#modal-edit").modal("show");

		$.ajax({
			url: "<?php echo site_url('gcctime/Department/getDepartment')?>",
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
				$("#form_edit_deparment input[name=description]").val(result.description);
				$("#form_edit_deparment input[name=id]").val(result.id);
				$('#personnelListEdit').find("option[value='" + result.head_id + "']").attr("selected","selected");
				$("#personnelListEdit").select2({"width": "100%"});


			}
		});
		return false;
	}

	//start edit validation
	// Add new department Validation
	$.validate({
		form : '#form_edit_deparment',
	    lang: 'en',
	    onSuccess : function(form) {
	    	$.ajax({
	    		url: form[0].action,
	    		type: "POST",
	    		data: $("#form_edit_deparment").serialize(),
	    		beforeSend: function(){
	    			$(".btn-submit-edit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    		},
	    		success: function(data){
	    			$(".btn-submit-edit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
	    			$("#modal-edit").modal('hide');
	    			var result = $.parseJSON(data);

	    			if(result.status){
						toastr.success(result.message);
						table.ajax.reload();
						$('#form_edit_deparment')[0].reset();
						/*** location.reload(); ***/
					}else{
						toastr.error(result.message);
					}
	    		}
	    	});
	    	return false;
	    },
	});

	//start select personnel
	$("#table_departments").on("click",".btn-late-select-person",function(){
		var id = $(this).attr("data-id");
		$("#selected-dept-id").val(id);
		$("#modal-select-personnel").modal("show");
		setTimeout(function(){
			$('#table_departments').data('dt_params', { dept_id: id });
			table_select_personnel.draw(false);			
		}, 1000);
	});


	$("#table_departments").on("click",".btn-late-select-person",function(){
		var id = $(this).attr("data-id");
		$("#selected-dept-id").val(id);
		$("#modal-select-personnel").modal("show");
		setTimeout(function(){
			$('#table_departments').data('dt_params', { dept_id: id });
			table_select_personnel.draw(false);			
		}, 1000);
	});

	$("#table_personnel_select").on("click",".btn-select-personnel-trigg",function(){
		var _self = $(this);
		var id = $(this).attr("data-id");
		var department_id = $("#selected-dept-id").val();
		$.ajax({
			url: "<?php echo site_url('gcctime/Personnel/selectPersonnel');?>",
			type: "POST",
			dataType: "json",
			data: {id: id, department_id: department_id, csrf_token: _csrf_hash}, 
			beforeSend: function(){
				$(this).addClass("m-btn--custom m-loader m-loader--light m-loader--right");
			},
			success: function(data){
				$(this).removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
				if(data.status == true){
					var btnSelect = _self.hasClass("btn-success btn-select-personnel-trigg");
					if(btnSelect){
						_self.removeClass("btn-success btn-select-personnel-trigg");
						_self.addClass("btn-danger btn-deselect-personnel-trigg");
						_self.empty().append("<i class='la la-minus-circle'></i> Deselect");
					}
				}
				/*** table_select_personnel.draw(); ***/
			}
		});
	});

	$("#table_personnel_select").on("click",".btn-deselect-personnel-trigg",function(){
		var _self = $(this);
		var id = $(this).attr("data-id");
		$.ajax({
			url: "<?php echo site_url('gcctime/Personnel/deselectPersonnel'); ?>",
			type: "POST",
			dataType: "json",
			data: {id: id},
			beforeSend: function(){
				$(this).addClass("m-btn--custom m-loader m-loader--light m-loader--right");
			},
			success: function(data){
				$(this).removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
				if(data.status == true){
					var btnDeselect = _self.hasClass("btn-danger btn-deselect-personnel-trigg");
					if(btnDeselect){
						_self.removeClass("btn-danger btn-deselect-personnel-trigg");
						_self.addClass("btn-success btn-select-personnel-trigg");
						_self.empty().append("<i class='la la-plus-circle'></i> Select");
					}
				}
				/*** table_select_personnel.draw(); ***/
			}
		});
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

    //select multiple personnel
    $(".btn-submit-multi-personnel").on("click",function(){
    	var check_count = $('.checkSingle:checkbox:checked').length;
    	var dept_id = $("#selected-dept-id").val();
    	if(check_count == 0){
    		toastr.error("No Data Selected!");
    	}else{
    		$('.checkSingle:checkbox:checked').each(function(){
				var cRow = $(this).closest("tr");
				var cButton = cRow.find("button.btn");
    			var personnel_id = $(this).attr("data-id");

    			$.ajax({
    				url: "<?php echo site_url('gcctime/Departments/selectPersonnelforDept'); ?>",
    				type: "POST",
					dataType: "json",
    				data: {personnel_id: personnel_id, dept_id: dept_id, csrf_token: _csrf_hash},
    				beforeSend: function(){
    					mApp.block('#modal-select-dept .modal-content', {
			                overlayColor: '#000000',
			                state: 'primary'
			            });
    				}, success: function(data){
						if(data.status){
							cButton.hasClass();
							var btnSelect = cButton.hasClass("btn-success btn-select-personnel-trigg");
							if(btnSelect){
								cButton.removeClass("btn-success btn-select-personnel-trigg");
								cButton.addClass("btn-danger btn-deselect-personnel-trigg");
								cButton.empty().append("<i class='la la-minus-circle'></i> Deselect");
							}
						}
    					mApp.unblock('#modal-select-dept .modal-content');
    				}
    			})
    		});
			$('.checkSingle:checkbox:checked').prop("checked", false);
			/* table_select_personnel.draw(); */
    	}
    });

    //deselect multiple personnel
    $(".btn-submit-desmulti-personnel").on("click",function(){
    	var check_count = $('.checkSingle:checkbox:checked').length;
    	var dept_id = $("#selected-dept-id").val();
    	if(check_count == 0){
    		toastr.error("No Data Selected!");
    	}else{
    		$('.checkSingle:checkbox:checked').each(function(){
				var cRow = $(this).closest("tr");
				var cButton = cRow.find("button.btn");
    			var personnel_id = $(this).attr("data-id");

    			$.ajax({
    				url: "<?php echo site_url('gcctime/Departments/deselectPersonnelforDept');?>",
    				type: "POST",
					dataType: "json",
    				data: {personnel_id: personnel_id, dept_id: dept_id},
    				beforeSend: function(){
    					mApp.block('#modal-select-dept .modal-content', {
			                overlayColor: '#000000',
			                state: 'primary'
			            });
    				}, success: function(data){
						if(data.status == true){
							var btnDeselect = cButton.hasClass("btn-danger btn-deselect-personnel-trigg");
							if(btnDeselect){
								cButton.removeClass("btn-danger btn-deselect-personnel-trigg");
								cButton.addClass("btn-success btn-select-personnel-trigg");
								cButton.empty().append("<i class='la la-plus-circle'></i> Select");
								
								$(this).attr("prop");
							}
						}
    					mApp.unblock('#modal-select-dept .modal-content');
    				}
    			})
    		});
			$('.checkSingle:checkbox:checked').prop("checked", false);
			/* table_select_personnel.draw(); */
    	}
    });



	//toastr start
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

		$("#personnelList").select2({ width: '100%' });


	});

</script>
<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								For Approval
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
								<table class="table table-striped table-bordered" id="table_approval">
									<col width="10%">
									<col width="15%">
									<col width="15%">
									<col width="17%">
									<col width="10%">
									<col width="27%">
									<col width="6%">
									<thead>
										<tr>
											<th>Biometric ID</th>
											<th>Date</th>
											<th>Time</th>
											<th>Name</th>
											<th>Status</th>
											<th>Remarks</th>
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

<!--begin::Modal-->
<div class="modal fade" id="modal-yesno" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Alert!</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<p>Please confirm action.</p>
				<div class="form-group remarks-field display-none">
				<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
					<label class="form-control-label" for="remarks">
						Remarks
					</label>
					<textarea class="form-control m-input" id="remarks" name="remarks" rows="3" data-validation="required"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary btn-confirm btnSave">Confirm</button>
				<button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
<!--end::Modal-->

<script type="text/javascript">
	var _nTable = $('#table_approval').DataTable({
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
	});

	function getData(){
		$.ajax({
			url: "<?php echo site_url("gcctime/Attendance_approval/getData"); ?>",
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

	$("#table_approval").on("click",".btnprocess",function(){
		var id = $(this).attr("data-id");
		var trigg = $(this).attr("data-trigg");
		$("#modal-yesno").modal("show");

		if(trigg == "disapprove"){
			$(".remarks-field").show();
		}else{
			$(".remarks-field").hide();
		}

		$("#modal-yesno button.btn-confirm").attr("data-action",trigg);
		$("#modal-yesno button.btn-confirm").attr("data-id",id);
	});

	$(".btn-confirm").on("click",function(){
		var id = $(this).attr("data-id");
		var trigg = $(this).attr("data-action");
		var	remarks = $("#remarks").val();

		$.ajax({
			url: "<?php echo site_url("gcctime/Attendance_approval/process")?>",
			type: "POST",
			data: {id: id, trigg: trigg, remarks: remarks, csrf_token: _csrf_hash},
			beforeSend: function(){
				mApp.block('#modal-yesno .modal-content', {
			                overlayColor: '#000000',
			                state: 'primary'
			            });

	    		$(".blockUI.blockMsg .m-blockui").removeAttr("style");
	    		},
	    	success: function(data){
	    		mApp.unblock('#modal-yesno .modal-content');

    			$("#modal-yesno").modal('hide');
    			var result = $.parseJSON(data);
    			getData();

    			if(result.status){
    				if(trigg == "approve"){
						toastr.success(result.msg);
    				}else{
						toastr.danger(result.msg);
    				}
				}else{
					toastr.error(result.msg);
				}
	    	}
		});
	});

	$(document).ready(function(){
		getData();

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
</script>
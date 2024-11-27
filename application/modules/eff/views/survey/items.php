<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
		<!--begin::Portlet-->
		<div class="m-portlet m-portlet--mobile">
			<div class="m-portlet__head">
				<div class="m-portlet__head-caption">
					<div class="m-portlet__head-title">
						<h3 class="m-portlet__head-text">Survey	<small>List</small></h3>
					</div>
				</div>
			</div>
			<div class="m-portlet__body">
				<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
					<table class="table table-striped table-bordered table-hover table-checkable dataTable no-footer dtr-inline" id="table-survey_items" width="100%"></table>
				</div>
			</div>
		</div>
		<!--end::Portlet-->
		</div>
	</div>
</div>
<script>
var _departmentId = <?php echo (isset($id) && $id)? $id: 0; ?>;
var _csrf_token = "<?php echo $this->security->get_csrf_token_name(); ?>";
var _csrf_hash = "<?php echo $this->security->get_csrf_hash(); ?>";
var _tableDepartment = $("#table-department");
var _dtDepartment = $("#table-survey_items").DataTable({
	dom: '<"toolbar">rtlip',
	serverSide: true,
	processing: true,
	ajax: {
		url: "<?php echo base_url('eff/survey/get_survey_list'); ?>",
		type: "post",
		dataType: "json",
		data: {  [_csrf_token] : _csrf_hash, department_id: _departmentId }
	}, columns: [
		{ data: "created_at", width: "10%", title: "Survey Date" },
		{ data: "department", width: "15%", title: "Department" },
		{ data: "purpose", width: "22%", title: "Purpose/Process" },
		{ data: "support", width: "7%", title: "Prompt Support", className: "text-center" },
		{ data: "rating", width: "7%", title: "Rating", className: "text-center" },
		{ data: "remarks", width: "24%", title: "Remarks" },
	], columnDefs: [{ 
		targets: "_all", 
		defaultContent: "", 
	}, { 
		targets: [2, 3, 4, 5],
		orderable: false, 
	}, {
		targets: 4,
		render: function( data, type, row, meta ){
			var _max = 5;
			var _diff = parseInt(_max) - parseInt(data);
			var _starUp = "<i class='fa fa-star'></i>";
			var _starDrop = "<i class='fa fa-star-o'></i>";
			var _cStar1 = "";
			var _cStar0 = "";
			
			if(data > 0){
				for(var xx=0; xx<data; xx++){ _cStar1 += _starUp; }
			}else{
				for(var zz=0; zz<_max; zz++){ _cStar0 += _starDrop; }	
			}
			
			if(_diff <= 4){
				for(var ii=0; ii<_diff; ii++){ _cStar0 += _starDrop; }
			}
			
			var _html = _cStar1+""+_cStar0;
			return _html;
		}
	}], initComplete: function(settings, json){}
});
</script>
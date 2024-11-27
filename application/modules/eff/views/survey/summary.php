<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
		<!--begin::Portlet-->
		<div class="m-portlet m-portlet--mobile">
			<div class="m-portlet__head">
				<div class="m-portlet__head-caption">
					<div class="m-portlet__head-title">
						<h3 class="m-portlet__head-text">Department	<small>List</small></h3>
					</div>
				</div>
			</div>
			<div class="m-portlet__body">
				<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
					<table class="table table-striped table-bordered table-hover table-checkable dataTable no-footer dtr-inline" id="table-department_list" width="100%"></table>
				</div>
			</div>
		</div>
		<!--end::Portlet-->
		</div>
	</div>
</div>
<script>
var _csrf_token = "<?php echo $this->security->get_csrf_token_name(); ?>";
var _csrf_hash = "<?php echo $this->security->get_csrf_hash(); ?>";
var _tableDepartment = $("#table-department");
var _dtDepartment = $("#table-department_list").DataTable({
	dom: '<"toolbar">rtlip',
	serverSide: true,
	processing: true,
	ajax: {
		url: "<?php echo base_url('eff/survey/get_department_list'); ?>",
		type: "post",
		dataType: "json",
		data: {  [_csrf_token] : _csrf_hash }
	}, columns: [
		{ data: "department", width: "50%", title: "Department" },
		{ data: "rating", width: "20%", title: "Average Rating" },
		{ data: "surveys", width: "20%", title: "Total Surveys" },
		{ data: "action", width: "10%", title: "Action" },
	], columnDefs: [{ 
		targets: "_all", 
		defaultContent: "", 
	}, { 
		targets: [1,2,3],
		orderable: false, 
	}, {
		targets: 1,
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
 function view_list(id){
	location="<?php echo site_url('eff/survey/items/')?>"+id;
}
</script>
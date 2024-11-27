<table class="table table-striped table-bordered" id="table_departments">
	<thead>
		<th>Name</th>
		<th>Status</th>
		<th>Action</th>
	</thead>
</table>


<script type="text/javascript">
	//$.noConflict();
	$("#table_departments").DataTable({
		"dom": '<"toolbar">frtlip',
	//"ajax": "<?php //echo base_url('Devices/getDeviceCollection');?>",
	});

	$("div.toolbar").html('<button type="button" class="m-portlet__nav-link btn m-btn--square btn-success btnNew" data-toggle="modal" data-target="#modal-new-department"><i class="fa fa-plus"></i> New </button>');

</script>
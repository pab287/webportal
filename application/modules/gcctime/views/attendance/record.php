<div class="m-content">
	<!--Begin::Main Portlet-->
	<div class="row">
		<div class="col-md-12 col-lg-12 col-xl-12">
			<div class="m-portlet m-portlet--tabs">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">Personal Record</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line--primary" role="tablist">
							<li class="nav-item m-tabs__item">
								<a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_user_attendance_tab_1" role="tab">Attendance</a>
							</li>
							<li class="nav-item m-tabs__item">
								<a class="nav-link m-tabs__link" data-toggle="tab" href="#m_user_attendance_tab_2" role="tab">Late</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="tab-content">
					<div class="active tab-pane" id="m_user_attendance_tab_1" role="tabpanel">
						<div class="m-portlet__body">
							<!--begin: Datatable -->
							<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
								<table class="table table-striped table-bordered" id="attendance-datatable" style="width:100%;" width="100%">
									<col width="*">
									<col width="10%">
									<col width="15%">
									<thead>
										<tr>
											<th>Date</th>
											<th>Time</th>
											<th>Device</th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
					<div class="tab-pane " id="m_user_attendance_tab_2" role="tabpanel">
						<div class="m-portlet__body">
						<!--begin: Datatable -->
							<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
							<table class="table table-striped table-bordered" id="late-datatable" style="width:100%;" width="100%">
								<col width="*">
								<col width="10%">
								<col width="10%">
								<col width="15%">
								<thead>
									<tr>
										<th>Date</th>
										<th>Time</th>
										<th>Duration</th>
										<th>Device</th>
									</tr>
								</thead>
							</table>
							</div>
						<!--end: Datatable -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<div class="modal fade" id="modal-add" data-backdrop="static" data-keyboard="false" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header"><h5 class="modal-title">Advance Search</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
		<form id="frm_user" action="" method="POST">
		<div class="modal-body">
			<div class="form-group"> 
				<label for="name">Select Date Range:</label>
					<input type="input" name="daterange" id="daterange" class="form-control" data-validation="required" id="datetimepicker">
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" name="register" class=" btn btn-success btnSearch">Search</button><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>       
		</div>
		</form>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-add2" data-backdrop="static" data-keyboard="false" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header"><h5 class="modal-title">Advance Search</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<form id="frm_user2" action="" method="POST">
			<div class="modal-body">
				<div class="form-group"> 
					<label for="name">Select Date Range:</label>
						<input type="input" name="daterange" id="daterange2" class="form-control" data-validation="required" id="datetimepicker">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" name="register" class=" btn btn-success btnSearch2 ">Search</button><button type="button" class="btn btn-default" data-dismiss="modal">Close</button> 
			</div>
			</form> 
		</div>
	</div>
</div>
<style>
a.m-card-profile__email.m-link {
    color: #000000;
    font-weight: 500;
}
</style>
<script type="text/javascript">
    $(document).ready(function() {});
    var attendanceTable =  $('#attendance-datatable').DataTable( {
		dom: '<"wrapper"<"#tbl_manual_toolbar.dataTables_toolbar1">ftlip>',
        ajax: "<?php echo site_url("profile/get_attendance_collection"); ?>",
		lengthChange : false,
		order: [0, "desc"],
		initComplete: function(){
			var _html = "<button type='button' id='advansearch' class='btn btn-info'><i class='fa fa-search'></i> Advance Search</button>";
			setTimeout(function(){ $(".dataTables_toolbar1").empty().html(_html); }, 500);
		}
        
    });
    
    var lateTable =  $('#late-datatable').DataTable( {
		dom: '<"wrapper"<"#tbl_manual_toolbar.dataTables_toolbar2">ftlip>',
        ajax: "<?php echo site_url("profile/get_attendance_collection/late"); ?>",
		lengthChange : false,
		order: [0, "desc"],
		initComplete: function(){
			var _html = "<button type='button' id='advansearch2' class='btn btn-info'><i class='fa fa-search'></i> Advance Search</button>";
			setTimeout(function(){ $(".dataTables_toolbar2").empty().html(_html); }, 500);
		}
        
    });

    $(document).on("click","#advansearch",function(){
         $("#modal-add").modal("show");
        $("#frm_user").get(0).reset();   
    });
    
    $(document).on("click","#advansearch2",function(){
         $("#modal-add2").modal("show");
        $("#frm_user2").get(0).reset();
    });
	
    $(document).on("click",".btnSearch",function(){
        var daterange = $('#daterange').val();
         var form=$("#frm_user");
            $.ajax({
            url: "<?php echo site_url("gcctime/profile/get_attendance_collection2"); ?>",
            type: "post",
            dataType: "json",
            data: form.serialize(),
            success: function( json ) {
                
                attendanceTable.clear();
                attendanceTable.rows.add(json.data);
                attendanceTable.draw();
            }
           });
        $('#modal-add').modal('hide');
    });
    
    $(document).on("click",".btnSearch2",function(){
        var daterange = $('#daterange2').val();
         var form=$("#frm_user2");
            $.ajax({
            url: "<?php echo site_url("gcctime/profile/get_late_collection/late"); ?>",
            type: "post",
            dataType: "json",
            data: form.serialize(),
            success: function( json ) {
                lateTable.clear();
                lateTable.rows.add(json.data);
                lateTable.draw();
               
            }
           });
         $('#modal-add2').modal('hide');
    });

   $(function() {
	   $('input[name="daterange"]').daterangepicker({
		   opens: 'left'
	   }, function(start, end, label) {
	   console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
	   });
		
		   $('input[name="daterange2"]').daterangepicker({
		   opens: 'left'
	   }, function(start, end, label) {
	   console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
	   });
   });
</script>
</div>
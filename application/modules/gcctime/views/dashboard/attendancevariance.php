<?php
	$last = $this->db->order_by('id',"desc")
		->limit(1)
		->get('gcctimeutility.event')
		->row();
		
	$createdAt = (isset($last->created_at) && $last->created_at)? $last->created_at: date("Y-m-d");
	$currentDate = date_format(date_create($createdAt),"F d, Y");
	$currentDate = date("F d, Y", strtotime("-1 days", strtotime($currentDate)));
?>

<div class="col-xl-8">
	<!--begin:: Widgets/Audit Log-->
	<div class="m-portlet m-portlet" id="m_latemonitor">
		<div class="m-portlet__head">
			<div class="m-portlet__head-caption">
				<div class="m-portlet__head-title">
					<h3 class="m-portlet__head-text">
						LACKING ENTRIES <small>( AS OF <?php echo strtoupper($currentDate); ?> )</small>
					</h3>
				</div>
			</div>
			<div class="m-portlet__head-tools"></div>
		</div>
		<div class="m-portlet__body">
			<div class="m-widget6">
				<div class="m-widget6__head">
					<div class="m-widget6__item">					 
						<span class="m-widget6__caption" style="width: 20%;"><small>EMPLOYEE NAME</small></span>
						<span class="m-widget6__caption text-center" style="width: 25%;"><small>TO REFERENCE #</small></span>
						<span class="m-widget6__caption text-center" style="width: 2%;"><small>COUNT</small></span>
						<span class="m-widget6__caption m--align-right"><small>TIME IN/OUT</small></span>					 
					</div>
				</div>
				<div class="m-widget6__body">
					<div class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="400" style="height: 400px; overflow: visible; max-height: 400px; position: relative;">
						<div class="m-widget__items lacking_entry" id="lacking_list">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--end:: Widgets/Audit Log-->
	</div>
</div>


<script type="text/javascript">
$.ajax({
	url: "<?php echo site_url('gcctime/attendance/getLackingYesterday'); ?>",
	beforeSend: function(){},
	success: function(data){
		$("#lacking_list").append(data);
	}   			
});

</script>
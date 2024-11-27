<?php
	$last = $this->db->order_by('id',"desc")
		->limit(1)
		->get('gcctimeutility.event')
		->row();
		
	$createdAt = (isset($last->created_at) && $last->created_at)? $last->created_at: date("Y-m-d");
	$currentDate = date_format(date_create($createdAt),"F 1 - d, Y");
	$biometricId = (isset($biometric_id) && $biometric_id)? $biometric_id: "0";
?>
<div class="col-md-5">
	<!--begin:: Widgets/Audit Log-->
	<div class="m-portlet m-portlet" id="m_latemonitor">
		<div class="m-portlet__head">
			<div class="m-portlet__head-caption">
				<div class="m-portlet__head-title">
					<h3 class="m-portlet__head-text">
						LACKING ENTRY <small>( AS OF <?php echo strtoupper($currentDate); ?> )</small>
					</h3>
				</div>
			</div>
			<div class="m-portlet__head-tools"></div>
		</div>
		<div class="m-portlet__body">
			<div class="m-widget6">
				<div class="m-widget6__head">
					<div class="m-widget6__item">					 
						<span class="m-widget6__caption" style="width: 10%;"><small>DATE</small></span>
						<span class="m-widget6__caption text-center" style="width: 2%;"><small>COUNT</small></span>
						<span class="m-widget6__caption m--align-right"><small>TIME IN/OUT</small></span>					 
					</div>
				</div>
				<div class="m-widget6__body">
					<div class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="300" style="height: 300px; overflow: visible; max-height: 300px; position: relative;">
						<div class="m-widget__items lacking_entry" id="lacking_list">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--end:: Widgets/Audit Log-->
	</div>
</div>
<div class="col-md-7">
	<!--begin:: Widgets/Audit Log-->
	<div class="m-portlet m-portlet" id="m_latemonitor">
		<div class="m-portlet__head">
			<div class="m-portlet__head-caption">
				<div class="m-portlet__head-title">
					<h3 class="m-portlet__head-text">
						DOUBLE ENTRY <small>( AS OF <?php echo strtoupper($currentDate); ?> )</small>
					</h3>
				</div>
			</div>
			<div class="m-portlet__head-tools"></div>
		</div>
		<div class="m-portlet__body">
			<div class="m-widget6">
				<div class="m-widget6__head">
					<div class="m-widget6__item">					 
						<span class="m-widget6__caption" style="width: 5%;"><small>DATE</small></span>
						<span class="m-widget6__caption text-center" style="width: 2%;"><small>COUNT</small></span>
						<span class="m-widget6__caption m--align-right"><small>TIME IN/OUT</small></span>					 
					</div>
				</div>
				<div class="m-widget6__body">
					<div class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="300" style="height: 300px; overflow: visible; max-height: 300px; position: relative;">
						<div class="m-widget__items double_entry" id="double_list">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--end:: Widgets/Audit Log-->
	</div>
</div>
<script>
$.ajax({
	url: "<?php echo site_url("gcctime/attendance/getPersonnelLackingDoubleEntry/{$biometricId}"); ?>",
	dataType: "json",
	success: function(json){
		if(typeof json.lacking_entry !== "undefined"){
			$("#lacking_list").append(json.lacking_entry);
		}
		if(typeof json.double_entry !== "undefined"){
			$("#double_list").append(json.double_entry);
		}
	}
});
</script>
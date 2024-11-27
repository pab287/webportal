<?php
	$last = $this->db->order_by('id',"desc")
		->limit(1)
		->get('gcctimeutility.event')
		->row();
		
	$createdAt = (isset($last->created_at) && $last->created_at)? $last->created_at: date("Y-m-d");
	$currentDate = date_format(date_create($createdAt),"F 1 - d, Y");
	$biometricId = (isset($biometric_id) && $biometric_id)? $biometric_id: "0";
?>
<div class="col-md-4">
	<!--begin:: Widgets/Audit Log-->
	<div class="m-portlet m-portlet" id="m_latemonitor">
		<div class="m-portlet__head">
			<div class="m-portlet__head-caption">
				<div class="m-portlet__head-title">
					<h3 class="m-portlet__head-text">
						LATE <small>( AS OF <?php echo strtoupper($currentDate); ?> )</small>
					</h3>
				</div>
			</div>
			<div class="m-portlet__head-tools"></div>
		</div>
		<div class="m-portlet__body">
			<div class="tab-content">
				<div class="tab-pane active" id="m_widget4_tab1_content" aria-expanded="true">
					<div class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="300" style="height: 300px; overflow: visible; max-height: 300px; position: relative;">
						<div class="m-list-timeline m-list-timeline--skin-light">
							<div class="m-list-timeline__items late" id="latelist"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--end:: Widgets/Audit Log-->
	</div>
</div>
<div class="col-md-4">
	<!--begin:: Widgets/Audit Log-->
	<div class="m-portlet m-portlet" id="m_latemonitor">
		<div class="m-portlet__head">
			<div class="m-portlet__head-caption">
				<div class="m-portlet__head-title">
					<h3 class="m-portlet__head-text">
						ABSENT <small>( AS OF <?php echo strtoupper($currentDate); ?> )</small>
					</h3>
				</div>
			</div>
			<div class="m-portlet__head-tools"></div>
		</div>
		<div class="m-portlet__body">
			<div class="tab-content">
				<div class="tab-pane active" id="m_widget4_tab1_content" aria-expanded="true">
					<div class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="300" style="height: 300px; overflow: visible; max-height: 300px; position: relative;">
						<div class="m-list-timeline m-list-timeline--skin-light">
							<div class="m-list-timeline__items" id="absentslist"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--end:: Widgets/Audit Log-->
	</div>
</div>
<div class="col-md-4">
	<!--begin:: Widgets/Audit Log-->
	<div class="m-portlet m-portlet" id="m_latemonitor">
		<div class="m-portlet__head">
			<div class="m-portlet__head-caption">
				<div class="m-portlet__head-title">
					<h3 class="m-portlet__head-text">
						UNDERTIME <small>( AS OF <?php echo strtoupper($currentDate); ?> )</small>
					</h3>
				</div>
			</div>
			<div class="m-portlet__head-tools"></div>
		</div>
		<div class="m-portlet__body">
			<div class="tab-content">
				<div class="tab-pane active" id="m_widget4_tab1_content" aria-expanded="true">
					<div class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="300" style="height: 300px; overflow: visible; max-height: 300px; position: relative;">
						<div class="m-list-timeline m-list-timeline--skin-light">
							<div class="m-list-timeline__items ut" id="ut-lists"></div>
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
	url: "<?php echo site_url("gcctime/attendance/getPersonnelToday/{$biometricId}"); ?>",
	success: function(data){
		if(typeof data !== "undefined"){
			$("#latelist").append(data);			
		}
	}
});
$.ajax({
	url: "<?php echo site_url("gcctime/attendance/getPersonnelAbsent/{$biometricId}"); ?>",
	success: function(data){
		if(typeof data !== "undefined"){
			$("#absentslist").append(data);			
		}
	}
});
$.ajax({
	url: "<?php echo site_url("gcctime/attendance/getPersonnelUndertime/{$biometricId}"); ?>",
	success: function(data){
		if(typeof data !== "undefined"){
			$("#ut-lists").append(data);			
		}
	}
});
</script>
<style>
.m-list-timeline .m-list-timeline__items:before{ background-color: transparent; }
</style>
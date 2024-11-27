<?php
	$last = $this->db
		->where("event_name", "sync_data")
		->order_by('id',"desc")
		->limit(1)
		->get('gcctimeutility.event')
		->row();

	$createdAt = (isset($last->created_at) && $last->created_at)? $last->created_at: date("Y-m-d");
	$currentDate = date_format(date_create($createdAt),"F d, Y");
?>
	<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
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
				<div class="m-portlet__head-tools">

				</div>
			</div>
			<div class="m-portlet__body">
				<div class="tab-content">
					<div class="tab-pane active" id="m_widget4_tab1_content" aria-expanded="true">
						<div class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="400" style="height: 400px; overflow: visible; max-height: 400px; position: relative;">
							<div class="m-list-timeline m-list-timeline--skin-light">
								<div class="m-list-timeline__items late" id="latelist">

								</div>
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
       			url: "<?php echo site_url('gcctime/attendance/getLateToday'); ?>",
       			beforeSend: function(){},
       			success: function(data)
       			{
       				// mApp.unblock('#m_latemonitor');
       				$("#latelist").append(data);
       			}
       		})
</script>
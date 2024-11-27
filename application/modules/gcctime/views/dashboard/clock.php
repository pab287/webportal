<style type="text/css">
	/*** @font-face {
		font-family: "palanquin";
		src: url("<?php echo base_url('assets/fonts/palanquin.ttf')?>") format('truetype');
	}
	@import 'https://fonts.googleapis.com/css?family=Open+Sans'; **/
	*{
	  margin: 0;
	  padding: 0;
	}
	body{
	  background: radial-gradient(#eee, #668);
	  height: 100vh;
	  width: 100vw;
	}
	#date{
	  color: #575962;
	  font-family: "palanquin", sas-serif;
	  font-size: 2em;
	  text-align: center;
	}
	.date-container{
		align-items: center;
	    -webkit-align-items: center;
	    display: flex;
	    display: -webkit-flex;
	    left: calc(50% - 300px);
	    /* top: calc(50% - 65px); */
	    width: 700px;
		margin: 0 auto;
	}
	#clock{
		align-items: center;
	    -webkit-align-items: center;
	    display: flex;
	    display: -webkit-flex;
	    height: 130px;
	    justify-content: space-around;
	    -webkit-justify-content: space-around;
	    left: calc(50% - 300px);
	    top: calc(50% - 0px);
	    width: 700px;
	    margin: 0 auto;
	}
	.unit{
	 	 /*background: linear-gradient(#aaa, #777); */
	    border-radius: 15px;
	    /* box-shadow: 0 2px 2px #444; */
	    color: #575962;
	    font-family: "Open Sans", sans-serif;
	    font-size: 5em;
	    line-height: 110px;
	    margin: 0 10px;
	    text-align: center;
	    /* text-shadow: 0 2px 2px #666; */
	    font-family: "palanquin";
	}
	.clock-separator{
		font-family: "palanquin";
    	font-size: 4em;
    	color: #575962;
	}
	.date-container {
		text-transform: uppercase;
	}
	
	/*** notification ***/
	.m-custom-portlet p.m-list_notification-item {
		font-size: 10px;
		margin: 0;
		line-height: 18px;
	}
	.m-custom-portlet span.m-list-timeline__time.custom-time_item {
		width: 35%;
		font-size: 8px;
	}
	span.m-list-timeline__text.custom-text-weight {
		font-weight: 500;
	}
	/*** notification ***/
</style><!--Begin::Main Portlet-->
<?php $roleId = $this->authenticate->getRoleId(); ?>
<?php $colWidth = (($roleId == 1 || $roleId == 2) || $admin_privilege == true)? "8":"12"; ?>
	<div class="row">
		<div class="col-xl-<?php echo $colWidth; ?> col-md-<?php echo $colWidth; ?>">
			<div class="m-portlet m-portlet--mobile ">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								<?php echo strtoupper("Company Standard Time"); ?>
							</h3>
						</div>
					</div>
				</div>
				<div class="m-portlet__body m-custom-portlet">
					<!-- Clock start -->
						<div class="row">
							<div class="date-container">
								<p id="date"></p>
							</div>
						</div>
						<div class="row">
								<div class="col-md-2 col-12 text-center">&nbsp;</div>
								<div class="col-md-2 col-4 text-center" >
									<h1 class="unit">
										<span id="hours" class="custom-unit_data"></span>
									</h1>
								</div>
								<div class="col-md-2 col-4 text-center">
									<h1 class="unit">
										<span id="minutes" class="custom-unit_data"></span>
									</h1>
								</div>
								<div class="col-md-2 col-4 text-center">
									<h1 class="unit">
										<span id="seconds" class="custom-unit_data"></span>
									</h1> 
								</div>
								<div class="col-md-2 col-12 text-center">
									<h1 class="unit">
										<span id="ampm" class="custom-unit_data"></span>
									</h1>
								</div>
								<div class="col-md-2 col-12 text-center">&nbsp;</div>
								<!-- 
								<p class="unit" id="hours"></p><span class="clock-separator">:</span> 
								<p class="unit" id="minutes"></p><span class="clock-separator">:</span>
								<p class="unit" id="seconds"></p>
								<p class="unit" id="ampm"></p> -->
						</div>
					<!-- Clock end -->
				</div>
			</div>
		</div>
		<?php if(($roleId == 1 || $roleId == 2) || $admin_privilege == true): ?>
		<div class="col-xl-4 col-md-4">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">ACTIVITY LOG</h3>
						</div>
					</div>
				</div>
				<div class="m-portlet__body m-custom-portlet">
					<div class="tab-content">
						<div class="tab-pane active" id="m_widget4_tab1_content">
							<div class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="218" style="height: 218px; overflow: visible; max-height: 218px; position: relative;">
							<div id="mCSB_3" class="mCustomScrollBox mCS-minimal-dark mCSB_vertical mCSB_outside" tabindex="0" style="max-height: none;">
								<div id="mCSB_3_container" class="mCSB_container" style="position: relative; top: 0px; left: 0px;" dir="ltr">
									<div class="m-list-timeline m-list-timeline--skin-light">
										<div class="m-list-timeline__items">
											<?php if($logs): krsort($logs); ?>
												<?php foreach($logs as $log): ?>
													<?php 
														$status = "primary";
														$paragraphClass = "";
														
														switch($log->status):
															case "success": 
																$status = "success"; 
																break;
															case "error": 
																$status = "danger";
																$paragraphClass = "text-danger custom-text-weight";
																break;
															case "warning": 
																$status = "warning"; 
																$paragraphClass = "text-warning custom-text-weight";
																break;
															case "info": 
																$status = "info"; 
																$paragraphClass = "text-info custom-text-weight";
																break;
														endswitch;
													?>
													<?php $timeline = $this->core_layout->getTimeAgo($log->created_at); ?>
													<div class="m-list-timeline__item">
														<span class="m-list-timeline__badge m-list-timeline__badge--<?php echo $status; ?>"></span>
														<span class="m-list-timeline__text <?php echo ($paragraphClass)? $paragraphClass:""; ?>">
															<p class="m-list_notification-item"><?php echo strtoupper($log->notification); ?></p>
														</span>
														<span class="m-list-timeline__time custom-time_item"><?php echo strtoupper($timeline); ?></span>
													</div>
												<?php endforeach; ?>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
<!--End::Main Portlet-->   
<script type="text/javascript">
	var $dOut = $('#date'),
    $hOut = $('#hours'),
    $mOut = $('#minutes'),
    $sOut = $('#seconds'),
    $ampmOut = $('#ampm');
var months = [
  'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
];

var days = [
  'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
];

function update(){
  var date = new Date();
  
  var ampm = date.getHours() < 12
             ? 'AM'
             : 'PM';
  
  var hours = date.getHours() == 0
              ? 12
              : date.getHours() > 12
                ? date.getHours() - 12
                : date.getHours();
  
  var minutes = date.getMinutes() < 10 
                ? '0' + date.getMinutes() 
                : date.getMinutes();
  
  var seconds = date.getSeconds() < 10 
                ? '0' + date.getSeconds() 
                : date.getSeconds();
  
  var dayOfWeek = days[date.getDay()];
  var month = months[date.getMonth()];
  var day = date.getDate();
  var year = date.getFullYear();
  
  var dateString = dayOfWeek + ' ' + month + ' ' + day + ', ' + year;
  
  $dOut.text(dateString);
  $hOut.text(hours);
  $mOut.text(minutes);
  $sOut.text(seconds);
  $ampmOut.text(ampm);
} 

update();
window.setInterval(update, 1000);
</script>
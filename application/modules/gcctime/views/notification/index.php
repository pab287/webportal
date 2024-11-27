<div class="m-content">
	<div class="row">
		<!-- col row 4 start -->
		<div class="col-md-4">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<span class="m-portlet__head-icon">
								<i class="flaticon-alert"></i>
							</span>
							<h3 class="m-portlet__head-text">
								Notifications
								<small>
								</small>
							</h3>
						</div>
					</div>
				</div>
				<div class="m-portlet__body">
					<div class="m-list-timeline m-list-timeline--skin-light notification-listing">
						<div class="m-list-timeline__items">
							<?php  
							$check_array = array();
								$query = $this->sm->getCurrentAbsent();
								if(isset($query["check_absent"]["am"])){
									foreach($query["check_absent"]["am"] as $_queryam){
										if(isset($_queryam["content"])){
											if($_queryam["content"] == "N/A" || $_queryam["content"] == ""){
											?>
													<div class="m-list-timeline__item">
														<span class="m-list-timeline__badge m-list-timeline__badge--success"></span>
														<span class="m-list-timeline__text">
															<strong><?php echo $_queryam["name"];?></strong> with biometric no. <strong><?php echo $_queryam["biometricno"];?></strong> is absent today with no LOA/TO created.
														</span>
														<span class="m-list-timeline__time">
															<button class="btn btn-success m-btn m-btn--icon m-btn--icon-only  btn-print-nte" data-id="<?php echo $_queryam["biometricno"];?>">
																<i class="flaticon-visible"></i>
															</button>
														</span>
													</div>
											<?php
											$check_array[] = $_queryam["biometricno"];
											}
										}
									}
								}

								if(isset($query["check_absent"]["pm"])){
									foreach($query["check_absent"]["am"] as $_querypm){
										if(isset($_querypm["content"]) && !in_array($_querypm["biometricno"], $check_array)){
											if($_querypm["content"] == "N/A" || $_querypm["content"] == ""){
											?>
													<div class="m-list-timeline__item">
														<span class="m-list-timeline__badge m-list-timeline__badge--success"></span>
														<span class="m-list-timeline__text">
															<strong><?php echo $_querypm["name"];?></strong> with biometric no. <strong><?php echo $_querypm["biometricno"];?></strong> is absent today with no LOA/TO created.
														</span>
														<span class="m-list-timeline__time">
															<button class="btn btn-success m-btn m-btn--icon m-btn--icon-only btn-print-nte" data-id="<?php echo $_querypm["biometricno"];?>">
																<i class="flaticon-visible"></i>
															</button>
														</span>
													</div>
											<?php
											}
										}
									}
								}
							?>
							<?php
								if(count($query["check_absent"]) == 0){
									?>
										<div class="m-stack m-stack--ver m-stack--general" style="min-height: 180px;">
											<div class="m-stack__item m-stack__item--center m-stack__item--middle">
												<span class="">
													All caught up!
													<br>
													No Results at the moment.
												</span>
											</div>
										</div>
									<?php
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- col row 4 end -->
		
		<!-- col row 8 start -->
		<div class="col-md-8">
			<div class="m-portlet print-preview-pane">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<span class="m-portlet__head-icon">
								<i class="flaticon-file-1"></i>
							</span>
							<h3 class="m-portlet__head-text">
								Preview
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<ul class="m-portlet__nav">
							<li class="m-portlet__nav-item">
								<a href="" onclick="printElem()" class="m-portlet__nav-link m-portlet__nav-link--icon">
									<i class="la la-print"></i>
								</a>
							</li>
						</ul>	
					</div>
				</div>
				<div class="m-portlet__body" id="print-pane">
						<?php 
							$this->load->view("notification/absent-nte-template");
						?>
				</div>
			</div>
		</div>
		<!-- col row 8 end -->

	</div>
</div>

<script type="text/javascript">
	
$(".notification-listing").on("click",".btn-print-nte",function(){ 
	var id = $(this).attr("data-id");

	//$(".print-preview-pane .m-portlet__body").text(id);

	$.ajax({
		url: "<?php echo site_url('gcctime/notification/print_nte');?>",
		type: "POST",
        dataType: "json",
        global: false,
		data: {	csrf_token : _csrf_hash, biometricno: id},
		beforeSend: function(){
			mApp.block('.print-preview-pane', {
			    overlayColor: '#000000',
			    state: 'primary'
			});  
		},
		success: function(data){
			$(".print-preview-pane .m-portlet__body").empty();
			$(".print-preview-pane .m-portlet__body").html(data.content);
 
			setTimeout(function() {
			    mApp.unblock('.print-preview-pane');
			}, 2000);
		}
	});

});


function printElem() {
    var content = document.getElementById("print-pane").innerHTML;
    var mywindow = window.open('', 'Print', 'height=600,width=800');

    mywindow.document.write('<html><head><title>Print</title>');
    mywindow.document.write('</head><body >');
    mywindow.document.write(content);
    mywindow.document.write('</body></html>');

    mywindow.document.close();
    mywindow.focus()
    mywindow.print();
    mywindow.close();
    return true;
}


</script>
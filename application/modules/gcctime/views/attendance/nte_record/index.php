<div class="m-content">
	<!--Begin::Main Portlet-->
	<div class="row">
		<div class="col-md-12 col-lg-12 col-xl-12">
			<div class="m-portlet m-portlet--mobile ">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">NTE Records</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<ul class="m-portlet__nav">
							<li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
								<a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl">
									<?php $query = $this->shift_management->getCurrentAbsent(10); if(isset($query)): ?>
										<span class="m-nav__link-badge m-badge m-badge--dot m-badge--dot-small m-badge--danger m-animate-blink"></span>
										<i class="flaticon-alert m--font-accent m-animate-shake"></i>
									<?php else: ?>
										<i class="flaticon-alert m--font-accent"></i>
									<?php endif; ?>
								</a>
								<div class="m-dropdown__wrapper">
									<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 22.5px;"></span>
									<div class="m-dropdown__inner">
										<div class="m-dropdown__body">
											<div class="m-dropdown__content">
												<ul class="m-nav">
													<li class="m-nav__section m-nav__section--first">
														<span class="m-nav__section-text">
															Notifications
														</span>
													</li><br>
													<li class="m-nav__item">
														<div class="tab-content">
															<div class="m-scrollable mCustomScrollbar _mCS_2 mCS-autoHide" data-scrollable="true" data-max-height="150" data-mobile-max-height="200" style="max-height: 150px; height: 150px; position: relative; overflow: visible;"><div id="mCSB_2" class="mCustomScrollBox mCS-minimal-dark mCSB_vertical mCSB_outside" tabindex="0" style="max-height: none;"><div id="mCSB_2_container" class="mCSB_container" style="position:relative; top:0; left:0;" dir="ltr">
																<div class="m-list-timeline m-list-timeline--skin-light notification-listing">
																	<div class="m-list-timeline__items">
																		<?php  
																		$check_array = array();
																		//$query = $this->shift_management->getCurrentAbsent();
																			if(isset($query["check_absent"]["am"])){
																				foreach($query["check_absent"]["am"] as $_queryam){
																					if(isset($_queryam["content"])){
																						if($_queryam["content"] == "N/A" || $_queryam["content"] == ""){
																						?>
																								<div class="m-list-timeline__item">
																									<span class="m-list-timeline__badge m-list-timeline__badge--success"></span>
																									<span class="m-list-timeline__text">
																										<strong><?php echo $_queryam["name"];?></strong> is absent today with no LOA/TO created.
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
													</li>	
													<?php if(isset($query)): ?>
													<li class="m-nav__separator m-nav__separator--fit"></li>
													<li class="m-nav__item">
														<a href="<?php echo base_url("gcctime/notification/index");?>" id="add_new" class="btn btn-accent btn-block btnView">
															<span>View All</span>
														</a>
													</li>
													<?php endif; ?>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="m-portlet__body">
					<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
						<table class="table table-striped table-bordered" id="attendance_nte-datatable">
							<thead>
								<tr>
									<th>Biometric No</th>
									<th>Name</th>
									<th>Absent Dates</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody> 
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="modalNteRecord" class="modal" tabindex="-1" role="dialog" aria-labelledby="">
		<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="">NTE Record</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" class="la la-remove"></span>
				</button>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-brand m-btn btnSave">Yes</button>
				<button class="btn btn-secondary m-btn" data-dismiss="modal">No</button>
			</div>
		</div>
		</div>
	</div>
</div>
<script>
var _modalNte = $("#modalNteRecord");
var _nTable = $('#attendance_nte-datatable').DataTable({
	dom: '<"toolbar">frtlip',
	ajax: "<?php echo site_url("gcctime/reports/test_absentee_date"); ?>",
	paging: false,
	lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
	scrollY: '50vh',
	scrollCollapse: true,
	columns: [
		{data: "biometricno"},
		{data: "name"},
		{data: "dates"},
		{data: "count"},
	],
	columnDefs: [{
		defaultContent: "",
		targets: 2,
		orderable: false,
	}],
});

var getNteRecord = function($code, $biometricno){
	if($code && $biometricno){
		$.ajax({
			url: "<?php echo site_url("gcctime/reports/get_absentee_data"); ?>",
			type: "post",
			dataType: "json",
			data: { code: $code, biometricno: $biometricno,  csrf_token: _csrf_hash },
			success: function(json){
				if(json.response){
					_modalNte.find(".modal-body").empty().append(json.html);
					_modalNte.find(".modal-footer .btnSave").attr("onclick", "setNteRecord("+$code+","+$biometricno+")");
					_modalNte.modal("show");
				}else{
					_modalNte.find(".modal-body").empty();
				}
			}
		});
	}
}

var setNteRecord = function($code, $biometricno){
	if($code && $biometricno){
		$.ajax({
			url: "<?php echo site_url("gcctime/reports/set_absentee_data"); ?>",
			type: "post",
			dataType: "json",
			data: { code: $code, biometricno: $biometricno,  csrf_token: _csrf_hash },
			success: function(json){
				if(json.response){
					_modalNte.modal("hide");
				}
			}
		});
	}
}
</script>
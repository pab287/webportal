<!--Begin::Main Portlet-->
	<div class="row">
		<div class="col-xl-12">
			<div class="m-portlet m-portlet--mobile ">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Live Attendance Monitor
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<ul class="m-portlet__nav">
							<li class="m-portlet__nav-item">
								<div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
									<a href="#" class="m-portlet__nav-link btn btn-lg btn-secondary  m-btn m-btn--icon m-btn--icon-only m-btn--pill  m-dropdown__toggle">
										<i class="la la-ellipsis-h m--font-brand"></i>
									</a>
									<div class="m-dropdown__wrapper">
										<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
										<div class="m-dropdown__inner">
											<div class="m-dropdown__body">
												<div class="m-dropdown__content">
													<ul class="m-nav">
														<li class="m-nav__section m-nav__section--first">
															<span class="m-nav__section-text">
																Quick Actions
															</span>
														</li>
														<li class="m-nav__item">
															<a href="" class="m-nav__link">
																<i class="m-nav__link-icon flaticon-share"></i>
																<span class="m-nav__link-text">
																	Create Post
																</span>
															</a>
														</li>
														<li class="m-nav__item">
															<a href="" class="m-nav__link">
																<i class="m-nav__link-icon flaticon-chat-1"></i>
																<span class="m-nav__link-text">
																	Send Messages
																</span>
															</a>
														</li>
														<li class="m-nav__item">
															<a href="" class="m-nav__link">
																<i class="m-nav__link-icon flaticon-multimedia-2"></i>
																<span class="m-nav__link-text">
																	Upload File
																</span>
															</a>
														</li>
													</ul>
												</div>
											</div>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="m-portlet__body">
					<!--begin: Datatable -->
					<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
						<table class="table table-striped table-bordered" id="attendance-monitor-datatable">
							<thead>
								<tr>
									<th>ID</th>
									<th>Name</th>
									<th>Department</th>
									<th>Time In</th>
									<th>Time Out</th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>
					<!--end: Datatable -->
				</div>
			</div>
		</div>
		</div>
	</div>
	<!--End::Main Portlet-->   
	<script type="text/javascript">
		$("#attendance-monitor-datatable").DataTable({
			"bScrollInfinite": true,
	        "bScrollCollapse": true,
	        paging: false,
	        aLengthMenu: [
		        [25, 50, 100, 200, -1],
		        [25, 50, 100, 200, "All"]
		    ],
		    iDisplayLength: -1
		});
	</script>
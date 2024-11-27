<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Archives
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<ul class="m-portlet__nav">
							<li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
								<a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl">
									<i class="la la-ellipsis-h m--font-brand"></i>
								</a>
								<div class="m-dropdown__wrapper">
									<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 22.5px;"></span>
									<div class="m-dropdown__inner">
										<div class="m-dropdown__body">
											<div class="m-dropdown__content">
												<ul class="m-nav">
													<li class="m-nav__section m-nav__section--first">
														<span class="m-nav__section-text">
															Quick Action
														</span>
													</li>
													<li class="m-nav__item">
														<a href="<?php echo site_url("ts/ticketing/masterfile");?>" class="m-nav__link">
															<i class="m-nav__link-icon flaticon-open-box"></i>
															<span class="m-nav__link-text">
																Masterfile
															</span>
														</a>
													</li>
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
					<div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
						<div class="row align-items-center">
							<div class="col-xl-8 order-2 order-xl-1">
								<div class="form-group m-form__group row align-items-center">
									<div class="col-md-4">
									
									</div>
									<div class="col-md-4">
										<div class="dropdown">
											<button class="btn btn-brand dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												Actions
											</button>
											<div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
												<a class="dropdown-item" href="#">
													<i class="la la-search-plus"></i> Query Builder
												</a>
												<a class="dropdown-item" href="#">
													<i class="la la-barcode"></i> Generate Barcode
												</a>
												<div class="dropdown-divider"></div>
												<a class="dropdown-item" href="#">
													<i class="la la-file-archive-o"></i> Mass Archive
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xl-4 order-1 order-xl-2 m--align-right">
								<div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
									<input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
									<span class="m-input-icon__icon m-input-icon__icon--left">
										<span>
											<i class="la la-search"></i>
										</span>
									</span>
								</div>
								<div class="m-separator m-separator--dashed d-xl-none"></div>
							</div>
                    	</div>
						<div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
							<div class="row align-items-center">
								<div class="col-xl-8 order-2 order-xl-1">
									<div class="colms-12">
										<div class="btn-group m-btn-group" role="group" aria-label="...">
											<button type="button" class="btn btn-primary" id="reload_dtTbl">
												<i class="la la-refresh"></i>
											</button>
											<div class="m-btn-group btn-group" role="group">
												<button id="tbl-btn-share" type="button" class="btn btn-success m-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="la la-table"></i>
												</button>
												<div class="dropdown-menu" aria-labelledby="btnGroupDrop1" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
													<a class="dropdown-item" href="#">
														Dropdown link
													</a>
													<a class="dropdown-item" href="#">
														Dropdown link
													</a>
													<a class="dropdown-item" href="#">
														Dropdown link
													</a>
													<a class="dropdown-item" href="#">
														Dropdown link
													</a>
												</div>
											</div>
											<div class="m-btn-group btn-group" role="group">
												<button id="tbl-btn-share" type="button" class="btn btn-info m-btn m-btn--pill-last dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="la la-share"></i>
												</button>
												<div class="dropdown-menu" aria-labelledby="btnGroupDrop2" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
													<a class="dropdown-item" href="#">
														Excel
													</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--begin: Datatable -->
					<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
						<table class="table table-striped table-bordered" id="table-tickets" width="100%">
							<thead>
								<tr>
									<th>#</th>
									<th>Type</th>
									<th>Issue</th>
									<th>Status</th>
									<th>Date Needed</th>
                                    <th>Requested by</th>
									<th>Performed by</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>	
							</tbody>
						</table>
					</div>
					<!--end: Datatable -->
				</div>
			</div>
			<!--end::Portlet-->
		</div>
	</div>
</div>
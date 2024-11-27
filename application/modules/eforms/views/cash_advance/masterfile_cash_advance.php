<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Cash Advance
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<ul class="m-portlet__nav">
							<li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
								<a href="javascript:void(0);" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-lg m-dropdown__toggle">
									<i class="la la-ellipsis-h m--font-brand"></i>
								</a>
								<div class="m-dropdown__wrapper">
									<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 72.242px;"></span>
									<div class="m-dropdown__inner">
										<div class="m-dropdown__body">
											<div class="m-dropdown__content">
												<ul class="m-nav">
													<li class="m-nav__section m-nav__section--first">
														<span class="m-nav__section-text">Quick Actions</span>
													</li>
													<li class="m-nav__item">
														<a href="javascript:void(0)" data-toggle="modal" data-target="#modal_blacklisted_employee" class="m-nav__link btnBlacklist">
															Blacklisted
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
									<a href="<?php echo site_url("eforms/cash_advance/new_cash_advance");?>" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew">
										<span> 
											<i class="la la-plus"></i>
											<span>
												new
											</span>
										</span>
									</a>
									<button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<span><i class="fa fa-search"></i><span>Filter</span><span class="dropdown-toggle"></span></span>
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
										<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-query-builder">
											Query Builder
										</a>
									</div>
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
                        <div class="col-xl-4 order-1 order-xl-2 m--align-right d-flex flex-row">
							<div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
								<input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
								<span class="m-input-icon__icon m-input-icon__icon--left">
									<span>
										<i class="la la-search"></i>
									</span>
								</span>
							</div>
							<div class="m-btn-group btn-group" role="group">
								<button id="tbl-btn-share" title="Export" type="button"
										class="btn btnExport btn-success m-btn dropdown-toggle"
										data-toggle="dropdown" aria-haspopup="true"
										aria-expanded="false">
									<i class="la la-external-link"></i>
								</button>
								<div class="dropdown-menu" aria-labelledby="btnGroupDrop1"
										x-placement="bottom-start"
										style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
									<a href="" class="dropdown-item datatable-csv" id="ExportCSV">
										<i class="m-nav__link-icon la la-file-o"></i>
										<span class="m-nav__link-text">
											CSV
										</span>
									</a>
									<a href="" class="dropdown-item datatable-pdf" id="ExportPDF">
										<i class="m-nav__link-icon la la-file-pdf-o"></i>
										<span class="m-nav__link-text">
											PDF
										</span>
									</a>
									<a href="" class="dropdown-item datatable-excel" id="ExportExcel">
										<i class="m-nav__link-icon la la-file-excel-o"></i>
										<span class="m-nav__link-text">
											EXCEL
										</span>
									</a>   
								</div>
							</div>
                            <div class="m-separator m-separator--dashed d-xl-none"></div>
                        </div>
                    </div>
                </div>
					<!--begin: Datatable -->
						<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
            <span style="color: red; font-weight: bold; display: flex; justify-content: flex-end;"><label>* DATE OF RELEASE: 3-4 DAYS AFTER THE DATE OF APPROVAL</label></span>
    <table class="table table-striped table-bordered" id="table-cash-advance" width="100%">
								<thead>
									<tr>
										<th>Status</th>
										<th>CA #</th>
										<th>Employee</th>
										<th>Amount Applied</th>
										<th>Purpose</th>
										<th>Amount Approved</th>
                                        <th>Date Applied</th>
										<th>Date Approved</th>
										<th class="notExport">Action</th>
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

<div class="modal fade" tabindex="-1" role="dialog" id="modal-query-builder">
	<form id="frm-query-builder">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Query Builder</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div id="query-builder"></div>
				</div>
				<div class="modal-footer">
					<button type="button" onclick="clear_query_builder()" class="btn btn-danger btnAdvance_search mr-auto">Clear</button>
					<button type="button" id="query-builder-btn" class="btn btn-primary btnAdvance_search">Generate</button>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_blacklisted_employee">
	<form id="frm-query-builder">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fa fa-user mr-1"></i>Blacklisted <small>Cash Advance</small></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<a href="javascript:void(0);" id="addBlacklist" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air btnNew">
								<span>
									<i class="la la-plus"></i>
									<span>
										Blacklist
									</span>
								</span>
							</a>
						</div>
						<div class="col-md-6">
							<div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
								<input type="text" class="form-control m-input m-input--solid" placeholder="Search Employee..." id="blacklistSearch">
								<span class="m-input-icon__icon m-input-icon__icon--left">
									<span>
										<i class="la la-search"></i>
									</span>
								</span>
							</div>
						</div>
					</div>
					<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
						<table class="table table-striped table-bordered" id="blacklisted-employee-table" width="100%">
							<thead>
								<th>Added By</th>
								<th>Employee</th>
								<th>Remarks</th>
								<th>Blacklisted By</th>
								<th>Action</th>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
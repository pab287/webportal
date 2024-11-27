<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Accounts
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<span>All Accounts<span class="dropdown-toggle"></span></span>
						</button>
						<div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start">
							<a class="dropdown-item" href="<?php echo site_url('eforms/billing/accounts_reconnection') ?>">For Reconnection</a>
							<a class="dropdown-item" href="<?php echo site_url('eforms/billing/accounts_disconnection') ?>">For Disconnection</a>
						</div>

						<!-- <ul class="m-portlet__nav">
							<li class="m-portlet__nav-item">
								<a href="<?php //echo site_url('eforms/billing/accounts_reconnection') ?>" class="m-portlet__nav-link btn btn-success m-btn m-btn--pill m-btn--air btnView">
									<i class="m-nav__link-icon la la-check-circle-o"></i>
									<span class="m-nav__link-text">For Reconnection</span>
								</a>
							</li>
							<li class="m-portlet__nav-item">
								<a href="<?php //echo site_url('eforms/billing/accounts_disconnection') ?>" class="m-portlet__nav-link btn btn-danger m-btn m-btn--pill m-btn--air btnView">
									<i class="m-nav__link-icon la la-user-times"></i>
									<span class="m-nav__link-text">For Disconnection</span>
								</a>
							</li>
						</ul> -->
					</div>
				</div>

				<div class="m-portlet__body">
					<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
						<div class="row align-items-center">
							<div class="col-xl-8 order-2 order-xl-1">
								<div class="form-group m-form__group row align-items-center">
									<div class="col-md-4">
										<a href="<?php echo site_url("eforms/billing/new_account");?>" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white">
											<span>
												<i class="la la-plus"></i>
												<span>
													New
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
											class="btn btnToot btn-success m-btn dropdown-toggle"
											data-toggle="dropdown" aria-haspopup="true"
											aria-expanded="false">
										<i class="la la-external-link"></i>
									</button>
									<div class="dropdown-menu" aria-labelledby="btnGroupDrop1"
											x-placement="bottom-start"
											style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
										<a href="" class="dropdown-item datatable-csv btnExcel" id="ExportCSV">
											<i class="m-nav__link-icon la la-file-o"></i>
											<span class="m-nav__link-text">
												CSV
											</span>
										</a>
										<a href="" class="dropdown-item datatable-pdf btnArchive" id="ExportPDF">
											<i class="m-nav__link-icon la la-file-pdf-o"></i>
											<span class="m-nav__link-text">
												PDF
											</span>
										</a>
										<a href="" class="dropdown-item datatable-excel btnExcel" id="ExportExcel">
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
						<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">	
							<div class="row align-items-center">
								<div class="col-xl-8 order-2 order-xl-1">
									
								</div>
							</div>
						</div>
					</div>
					<!--begin: Datatable -->
						<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
							<table class="table table-striped table-bordered" id="table-accounts" width="100%">
								<thead>
									<tr>
										<th>Account name</th>
										<th>Account No.</th>
										<th>Meter No.</th>
										<th>Subdivision</th>
										<th>House Model</th>
										<th>Street</th>
										<th>Block</th>
										<th>Lot</th>
										<th>Water Con.</th>
										<th>Status</th>
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

<div class="modal fade" id="m_meter_r" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-dialog-centered" role="document" id="m_meter_r_2">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel" style="font-weight: bold; color: #6e6e6e;">
                    Meter Replacement
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">
                    ×
                </span>
                </button>
            </div>
			<form class="m-form m-form--fit" id="frm_meter_r" method="POST" action="<?php echo site_url('eforms/billing/update_account_meter');?>">
				<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<input type="hidden" name="type" value="2">
				<input type="hidden" name="old_meterno">
				<input type="hidden" name="account_id" id="account_id">
				<input type="hidden" name="selectedReading">
				<div class="row">
					<div class="col-12 modal-body" style="width: 40%; padding-top: 6px;">
						<div class="col-12" style="margin-bottom: 10px;">
							<div class="row" style="margin-top: 20px; background-color: #d5415d; border-radius: 3px; color: white; font-weight: bold;">
								<div class="col-md-1" style="padding: 0;">
									<i class="la la-exclamation-circle" style="font-size: 55px; padding-left: 6px;"></i>
								</div>
								<div class="col-md-11">
									<label class="ml-1" style="margin: 0; padding: 7px;">
										Note: Assigning/replacing a new meter number will make a customer’s READING set back to ZERO.
									</label>
								</div>
							</div>
						</div>
						<label class="col-12 col-form-label" style="font-weight: bold; color: #7e7e7e;">
							Account Name :
						</label>
						<div class="col-12" style="margin-bottom: 10px;">
							<input type="text" class="form-control m-input" name="account_name" id="account_name" style="pointer-events: none; font-weight: bold;"/>
						</div>
						<label class="col-12 col-form-label row mx-0" style="font-weight: bold; color: #7e7e7e;">
							<div class="row mx-0 align-items-center justify-content-between w-100">
								<p class="m-0">New Meter No.* :</p>

								<label class="m-checkbox mb-0 ml-5">
									<input type="checkbox" class="reset-meter">
									Reset Meter
									<span></span>
								</label>
							</div>
							
							
						</label>
						<div class="col-12" style="margin-bottom: 10px;">
							<input oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');" maxlength="20" type="text" class="form-control m-input" name="new_meterno" data-validation="required"/>
						</div>
						<label class="col-12 col-form-label" style="font-weight: bold; color: #7e7e7e;">
							Previous Reading.* :
						</label>
						<div class="col-12" style="margin-bottom: 10px;">
							<input oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');" maxlength="20" type="text" class="form-control m-input" name="previous_reading" data-validation="required" readonly/>
						</div>
						<label class="col-12 col-form-label" style="font-weight: bold; color: #7e7e7e;">
							Remarks.* :
						</label>
						<div class="col-12" style="margin-bottom: 10px;">
							<textarea type="text" rows="5" class="form-control m-input" data-validation="required" name="remarks"></textarea>
						</div>
						<div class="col-12 row" style="margin-top: 10px; margin-left: 0;">
							<i class="la la-exclamation-circle" style="font-size: 32px; color: #7f7f7f;" data-toggle="m-tooltip" data-original-title="Enable to edit readings of account with previous meter no."></i>
							<label class="col-form-label" id="toggle_title" style="font-weight: bold; color: #7e7e7e;">
								Enable Update Readings
							</label>
							<span class="m-switch m-switch--icon" style="margin-left: 10px;">
								<label>
									<input type="checkbox" id="toggle_switch">
									<span></span>
								</label>
							</span>
						</div>
					</div>
					<div class="col-12 modal-body" style="width: 60%; padding-left: 0; margin-left: -20px; padding-top: 6px;" id="readings_table">
						<div class="col-12">
							<p style="margin-top: 20px; background-color: #4a6989; border-radius: 3px; color: white; padding: 10px; font-weight: bold;">
								Select readings to change their meter no.
							</p>
							<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll" style="margin-top: -36px;">
								<table class="table table-striped table-bordered table-responsive" id="table-readings" width="100%" style="display: table; width: 100%;">
								<col width="5%" />
								<col width="15%" />
								<col width="10%" />
								<col width="10%" />
								<col width="10%" />
								<col width="10%" />
									<thead>
										<tr>
											<th>
												<label class="m-checkbox m-checkbox--air m-checkbox--state-primary">
													<input type="checkbox" id="cb-select-all"><span></span>
												</label>
											</th>
											<th>Reference No.</th>
											<th>Meter No.</th>
											<th>Reading</th>
											<th>Reading Date</th>
											<th>Status</th>
										</tr>
									</thead>
									<tfoot align="right">
										<tr><th></th><th></th><th style="text-align: right;"></th><th></th><th></th><th style="font-weight: bold; color: #525252;"></th></tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="colmn-2">
						<button type="submit" class="btn btn-brand btnSave">
							Save
						</button>
						<button type="button" class="btn btn-danger text-white" data-dismiss="modal">
							Close
						</button>
					</div>
				</div>
			</form>
        </div>
    </div>
</div>
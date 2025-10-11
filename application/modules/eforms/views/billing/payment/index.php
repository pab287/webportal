<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Payment
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
            			<ul class="m-portlet__nav">
							<li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
								<a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl"><i class="la la-ellipsis-h m--font-brand"></i></a>
								<div class="m-dropdown__wrapper">
									<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
									<div class="m-dropdown__inner">
										<div class="m-dropdown__body">
											<div class="m-dropdown__content">
												<ul class="m-nav">
													<li class="m-nav__item">
														<a href="<?php echo site_url('eforms/billing/payment_archive') ?>" class="m-nav__link btnArchive">
															<i class="m-nav__link-icon la la-archive"></i><span class="m-nav__link-text">Archive</span>
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
					<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
						<div class="row align-items-center">
							<div class="col-xl-8 order-2 order-xl-1">
								<div class="form-group m-form__group row align-items-center">
									<div class="col-md-12">
										<a href="<?php echo site_url('eforms/billing/create_payment')?>" class="btn btn-accent m-btn m-btn--icon m-btn--pill btnNew text-white">
											<span>
												<i class="la la-plus"></i>
												<span>New</span>
											</span>
										</a>

										<button class="btn btn-accent m-btn m-btn--icon m-btn--pill massPrint text-white">
											<i class="la la-print"></i> Print
										</button>    
										
										<button class="btn btn-brand m-btn m-btn--icon m-btn--pill" id="payment-date-picker">
											<span>
												<em class="fa fa-calendar"></em>
												<span class="selected-filter pl-3 text-uppercase">Date Filter</span>
											</span>
										</button>

										<button id="tbl-btn-share" title="Export" type="button" class="btn btnExport btn-success m-btn--pill m-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<i class="la la-external-link"></i>
											<span>Export</span>
											<span class="dropdown-toggle"></span>
										</button>

										<div class="dropdown-menu mt-2" aria-labelledby="btnGroupDrop1" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
											<a href="javascript:void(0);" class="dropdown-item datatable-csv" id="ExportCSV">
												<i class="m-nav__link-icon la la-file-o"></i>
												<span class="m-nav__link-text">CSV</span>
											</a>

											<a href="javascript:void(0);" class="dropdown-item datatable-pdf" id="ExportPDF">
												<i class="m-nav__link-icon la la-file-pdf-o"></i>
												<span class="m-nav__link-text">PDF</span>
											</a>

											<a href="javascript:void(0);" class="dropdown-item datatable-excel" id="ExportExcel">
												<i class="m-nav__link-icon la la-file-excel-o"></i>
												<span class="m-nav__link-text">EXCEL</span>
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
						<table class="table table-striped table-bordered" id="table-payment" width="100%">
							<thead>
								<tr>
									<th class="toggle-all notExport">
										<input type="checkbox" id="cb-select-all"> <span></span>
									</th>
									<th title="Reference No.">Ref No.</th>
									<th title="Account Name">Acc. Name</th>
									<th>Bill</th>
									<th title="Payment Type">Pay Type</th>
									<th>Due Date</th>
									<th>Penalty</th>
									<th title="Net Payment">Net Pay</th>
									<th title="Received Amount">Rec. Amount</th>
									<th>AR</th>
									<th>Payment Date</th>
									<th title="Applied Payment Date">AP. Date</th>
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

<div class="modal fade" id="m_viewPayment" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document" style="min-width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">View Payment</h5>
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">×</span>
				</button>
			</div>

			<div class="modal-body">
				<div class="section-area">
					<div class="bg-card mb-4">
						<!-- <h4 class="info-label mb-4">Account Information</h4> -->

						<div class="group">
							<div class="info-group">
								<p class="info-label">Cashier</p>
								<p class="info-val mv_cashier"></p>
							</div>

							<div class="info-group">
								<p class="info-label">Applied Payment Date</p>
								<p class="info-val mv_applied_payment_date"></p>
							</div>

							<div class="info-group">
								<p class="info-label">AR. #</p>
								<p class="info-val mv_ar"></p>
							</div>

							<div class="info-group">
								<p class="info-label">REF. #</p>
								<p class="info-val mv_ref_no"></p>
							</div>

							<div class="info-group">
								<p class="info-label">BILL REF. #</p>
								<p class="info-val mv_bill_ref_no"></p>
							</div>

							<div class="info-group">
								<p class="info-label">READ. REF. #</p>
								<p class="info-val mv_read_ref_no"></p>
							</div>

							<div class="info-group">
								<p class="info-label">PAYMENT DATE</p>
								<p class="info-val mv_payment_date"></p>
							</div>

							<div class="info-group">
								<p class="info-label">PAYMENT TYPE</p>
								<p class="info-val mv_payment_type"></p>
							</div>

							<div class="info-group">
								<p class="info-label">PAYMENT DETAILS</p>
								<p class="info-val mv_payment_details"></p>
							</div>
						</div>
					</div>
					
					<div class="bg-card mb-4">
						<!-- <h4 class="info-label mb-4">Billing / Payment Info</h4> -->

						<div class="group">
							<div class="info-group">
								<p class="info-label">ACC. NO.</p>
								<p class="info-val mv_account_no"></p>
							</div>

							<div class="info-group">
								<p class="info-label">NAME</p>
								<p class="info-val mv_name"></p>
							</div>

							<div class="info-group">
								<p class="info-label">METER NO.</p>
								<p class="info-val mv_meter_no"></p>
							</div>

							<div class="info-group">
								<p class="info-label">ADDRESS</p>
								<p class="info-val mv_address"></p>
							</div>
						</div>
					</div>

					<div class="bg-card">
						<!-- <h4 class="info-label mb-4">Billing / Payment Info</h4> -->

						<div class="group">
							<div class="info-group">
								<p class="info-label">BILL AMOUNT</p>
								<p class="info-val mv_bill_amount"></p>
							</div>

							<div class="info-group">
								<p class="info-label">OVERDUE FEE</p>
								<p class="info-val mv_overdue_fee"></p>
							</div>

							<div class="info-group">
								<p class="info-label">RECONNECTION FEE:</p>
								<p class="info-val mv_reconnection_fee"></p>
							</div>

							<div class="info-group">
								<p class="info-label">BALANCE COVERED</p>
								<p class="info-val mv_balance_covered"></p>
							</div>

							<div class="info-group">
								<p class="info-label font-weight-bold">NET PAYMENT</p>
								<p class="info-val mv_net_payment"></p>
							</div>

							<div class="info-group">
								<p class="info-label font-weight-bold">RECEIVED AMOUNT</p>
								<p class="info-val mv_received_amount"></p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="m_archived" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<input type="hidden" name="id" id="archive_id">
			<input type="hidden" name="payment_ref_no" id="payment_ref_no">

			<div class="modal-header">
				<h5 class="modal-title">Archive Payment</h5>
			</div>

			<div class="modal-body" id="archive_text"></div>

			<div class="modal-footer">
				<button type="submit" class="btn btn-danger btnArchive" onclick="archivePayment()">Archive</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="m_view_penalty" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document" style="min-width: 50%">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="title_penalties">Penalties</h5>
			</div>

			<div class="modal-body" id="view_penalties"></div>

			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<style>
	.v-middle {
		vertical-align: middle!important;
	}

	#table-payment tbody td.select-checkbox:before {
		top: 0!important;
		bottom: 0!important;
		left: 0!important;
		right: 0!important;
		margin: auto!important;
		border: 1px solid #767676;
		border-radius: 2px!important;
		height: 13px!important;
		width: 13px!important;
	}

	#table-payment tbody td.select-checkbox:after {
		position: absolute!important;
		top: -8px!important;
		bottom: 0!important;
		left: 0!important;
		right: 0!important;
		margin: auto!important;
	}

	#table-payment tbody tr.selected td.select-checkbox:before {
		border: 1px solid #ffffff !important;
	}

	#table-payment tbody > *{
		font-size: 12px;
	}

	#m_viewPayment .section-area .bg-card {
		border-radius: 5px;
		padding: 20px;
		/* background: #f2f3f8; */
		transition: .2s ease-in-out;
	}

	#m_viewPayment h4.info-label {
		font-size: 15px;
	}

	#m_viewPayment .info-label {
		color: #71737b;
	}
	
	#m_viewPayment .info-val {
		color: #535353;
		font-weight: 600;
	}

	#m_viewPayment .info-group {
		display: flex;
		justify-content: space-between;
		align-items: start;
	}

	#m_viewPayment .info-group p {
		margin: 0;
	}

	#m_viewPayment .info-group:not(:last-child) {
		margin: 0 0 10px;
	}

	.table-archived .checkbox-col {
		pointer-events: none; 
	}

	#table-payment tbody tr.selected a#viewPayment {
		color: #575962;
	}
</style>
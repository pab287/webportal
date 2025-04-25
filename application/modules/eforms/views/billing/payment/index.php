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
									<th>Reference No.</th>
									<th>Account Name</th>
									<th>Bill</th>
									<th>Payment Type</th>
									<th>Due Date</th>
									<th>Overdue Fee</th>
									<th>Net Payment</th>
									<th>Received Amount</th>
									<th>AR</th>
									<th>Payment Date</th>
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
	<div class="modal-dialog modal-dialog-centered" role="document" style="min-width: 80%">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">View Payment</h5>
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">×</span>
				</button>
			</div>

			<div class="modal-body" style="pointer-events: none;">
				<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
					<div class="m-form__heading">
						<h3 class="m-form__heading-title">Account Information</h3>
					</div>

					<div class="row m--margin-bottom-20">
						<div class="col-md-4">
							<div class="form-group m-form__group">   
								<label>Account No.</label>
								<input class="form-control m-input account_no" readonly type="text">
							</div>             
						</div>  

						<div class="col-md-4">
							<div class="form-group m-form__group">   
								<label>Bill</label>
								<input class="form-control m-input bill" readonly type="text">
							</div>             
						</div>       

						<div class="col-md-4">
							<div class="form-group m-form__group">
								<label>Payment Date</label>
								<input class="form-control m-input payment_date" readonly type="text" data-validation="required">
							</div>             
						</div>        
					</div>

					<div class="row m--margin-bottom-20">
						<div class="col-md-4">
							<div class="form-group m-form__group">   
								<label>Customer name</label>
								<input class="form-control m-input customer_name" type="text" readonly>
							</div>             
						</div>

						<div class="col-md-4">
							<div class="form-group m-form__group">   
								<label>Meter No.</label>
								<input class="form-control m-input meter_no" type="text" readonly>
							</div>             
						</div>  

						<div class="col-md-2">
							<div class="form-group m-form__group">   
								<label>Block No.</label>
								<input class="form-control m-input block_no" type="text" readonly>
							</div>             
						</div>  

						<div class="col-md-2">
							<div class="form-group m-form__group">   
								<label>Lot No.</label>
								<input class="form-control m-input lot_no" type="text" readonly>
							</div>             
						</div>  
					</div>

					<hr>

					<div class="m-form__heading">
						<h3 class="m-form__heading-title">Payment Information</h3>
					</div>

					<div class="row m--margin-bottom-20">
						<div class="col-md-3">
							<div class="form-group m-form__group">   
								<label>Payment Type</label>
								<input class="form-control m-input payment_type" type="text" readonly>
							</div>             
						</div>  
						<div class="col-md-6 payment_details_layout">
							<div class="form-group m-form__group">
								<label class="payment_details_title">Payment Details</label>
								<input class="form-control m-input payment_details" type="text" readonly data-validation="required">
							</div>
						</div>
					</div>

					<div class="row m--margin-bottom-20">
						<div class="col-md-3">
							<div class="form-group m-form__group">   
								<label>Overdue Fee</label>
								<input class="form-control m-input overdue_fee" type="text" readonly>
							</div>             
						</div>  
						<div class="col-md-3">
							<div class="form-group m-form__group">
								<label>Reconnection Fee</label>
								<input class="form-control m-input reconnection_fee" type="text" readonly data-validation="required">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group m-form__group">
								<label>Bill Amount</label>
								<input class="form-control m-input bill_amount" type="text" readonly data-validation="required">
							</div>
						</div>
					</div>

					<div class="row m--margin-bottom-20">
						<div class="col-md-3">
							<div class="form-group m-form__group">   
								<label>Sub Total</label>
								<input class="form-control m-input sub_total" type="text" readonly>
							</div>             
						</div>  
						<div class="col-md-3">
							<div class="form-group m-form__group">
								<label>Balance Covered</label>
								<input class="form-control m-input balance_covered" type="text" readonly data-validation="required">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group m-form__group">   
								<h6>Net Payment</h6>
								<input class="form-control m-input net_payment" type="text" readonly data-validation="required">
							</div>
						</div>
					</div>

					<hr>

					<div class="row m--margin-bottom-20">
						<div class="col-md-3">
							<div class="form-group m-form__group">   
								<label>Created By</label>
								<input class="form-control m-input created_by" type="text" readonly>
							</div>             
						</div>
						<div class="col-md-3">
							<div class="form-group m-form__group">   
								<label>Created Date</label>
								<input class="form-control m-input created_date" type="text" readonly>
							</div>             
						</div>
						<div class="col-md-3">
							<div class="form-group form__group">   
								<label>Acknowledgement Receipt</label>
								<input class="form-control m-input acknowledgement_receipt" readonly type="text">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group form__group">   
								<h6 class="m-form__heading-title">
									Received Amount
								</h6>
								<input class="form-control-lg m-input text-right received_amount" readonly type="text" style="font-weight: bold;" >
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
</style>
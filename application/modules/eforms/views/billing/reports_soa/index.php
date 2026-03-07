<style>
	.table-header .col, .table-body .col, .table-footer .col {
		flex: 0 0 20%;
		max-width: 20%;
	}

	.table-header .col {
		padding: 20px 0;
	}
	.table-row .bill-data p {
		font-weight: 700;
		color: #525252;
	}

	.payment-data.active .col:first-child::after {
		content: "";
		height: 30px;
		width: 10px;
		background: #4895ef;
		position: absolute;
		top: 0;
		bottom: 0;
		margin: auto;
		left: 60px;
		border-radius: 20px;
	}

	.payment-data.archived .col:first-child::after {
		content: "";
		height: 30px;
		width: 10px;
		background: #f66e84;
		position: absolute;
		top: 0;
		bottom: 0;
		margin: auto;
		left: 60px;
		border-radius: 20px;
	}

	.payment-data div.col {
		color: #737373;
		font-weight: 400;
	}

	.bill-data small {
		font-size: 9px;
	}
	#ledgerAccordion .accord_item-icon {
		position: absolute;
		top: 0;
		bottom: 0;
		margin: auto;
		height: max-content;
	}
	#ledgerAccordion .accord_item-icon i {
		transition: .3s ease-in-out;
	}
	#ledgerAccordion .accord_item-head.collapsed .accord_item-icon i {
		rotate: -90deg;
	}
	#ledgerAccordion .accord_item-head .accord_item-icon i {
		rotate: 0deg;
	}
	#soa_ledger_vue_wrap .accord_item:not(:last-child) {
		margin: 0 0 10px 0;
	}
	#ledgerAccordion .accord_item-head.collapsed {
		background: #f4f5f8;
		border-radius: 5px;
	}

	#ledgerAccordion .accord_item-head.collapsed div, 
	#ledgerAccordion .accord_item-head.collapsed span i {
		color: #737373;
	}

	#ledgerAccordion .accord_item-head span i {
		color: #fff;
	}

	#ledgerAccordion .accord_item-head div {
    	color: #fff;
		font-weight: 500;
	}

	#ledgerAccordion .accord_item-head {
		background: #4895ef;
		position: relative;
		transition: .3s ease-in-out;
		border-top-left-radius: 5px;
    	border-top-right-radius: 5px;
	}

	.accord_item-body {
		background: #fff;
    	border: 1px solid #4895ef;
		border-bottom-left-radius: 5px;
		border-bottom-right-radius: 5px;
		overflow: hidden;
	}

	.accord_item-content .payment-data:nth-child(odd) {
		background: #f4f5f8;
	}

	#soa_details .col-6 strong {
		color: #737373;
	}

	#soa_details .col-6 strong:last-child {
		font-weight: 900;
	}

	.is_archived_text > * {
		color: #f66e84!important;
	}

	.v-mid {
		vertical-align: middle!important;
	}
</style>

<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">Statement of Account</h3>
						</div>
					</div>

					<div class="m-portlet__head-tools"></div>
				</div>

				<div class="m-portlet__body">
					<div class="m-form m-form--label-align-right">
						<div class="row align-items-center">
							<div class="col-xl-8 order-2 order-xl-1">
								<div class="form-group m-form__group row align-items-center">
									<div class="col-md-4">
										<!-- <a class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white" id="btnNew">
											<span>
												<i class="la la-plus"></i><span>New</span>
											</span>
										</a> -->
									</div>
								</div>
							</div>

							<div class="col-xl-4 order-1 order-xl-2 m--align-right d-flex flex-row">
								<div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
									<input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
									<span class="m-input-icon__icon m-input-icon__icon--left">
										<span><i class="la la-search"></i></span>
									</span>
								</div>

								<div class="m-btn-group btn-group" role="group">
									<button id="tbl-btn-share" title="Export" type="button" class="btn btnExport btn-success m-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<i class="la la-external-link"></i>
									</button>

									<div class="dropdown-menu" aria-labelledby="btnGroupDrop1" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
										<a href="" class="dropdown-item datatable-csv" id="ExportCSV">
											<i class="m-nav__link-icon la la-file-o"></i>
											<span class="m-nav__link-text">CSV</span>
										</a>

										<a href="" class="dropdown-item datatable-pdf" id="ExportPDF">
											<i class="m-nav__link-icon la la-file-pdf-o"></i>
											<span class="m-nav__link-text">PDF</span>
										</a>

										<a href="" class="dropdown-item datatable-excel" id="ExportExcel">
											<i class="m-nav__link-icon la la-file-excel-o"></i>
											<span class="m-nav__link-text">EXCEL</span>
										</a>   
									</div>
								</div>

								<div class="m-separator m-separator--dashed d-xl-none"></div>
							</div>
						</div>

						<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">	
							<div class="row align-items-center">
								<div class="col-xl-8 order-2 order-xl-1"></div>
							</div>
						</div>
					</div>
				
					<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
						<table class="table table-striped table-bordered table-responsive" id="table-reports" width="100%">
							<thead>
								<tr>
									<th>Customer</th>
									<th>Account No.</th>
									<th>Meter No.</th>
									<th>Subdivision</th>
									<th>Over Payment</th>
									<th>Total Penalty</th>
									<th>Total Charges</th>
									<th>Total Balance</th>
									<th class="notExport">Action</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="m_soa" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered" style="min-width: 70%">
		<div class="modal-content">
			<div class="modal-header" id="soa_modal">
				<h5 class="modal-title" style="margin-top: 7px;">Account Statement</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span>×</span>
				</button>
			</div>

        	<div class="modal-body">
				<div class="row mb-3">
					<div class="col-md-3">
						<div class="form-group m-form__group row">
							<label for="example-text-input" class="col-2 col-form-label">Type</label>
							<div class="col-10">
								<select class="form-control" id="report_type">
									<option></option>
									<option value="payment">PAYMENT</option>
									<option value="billing">BILLING</option>
									<option value="reading">READING</option>
									<option value="ledger">LEDGER</option>
								</select>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group m-form__group row">
							<label for="example-text-input" class="col-2 col-form-label">Year</label>
							<div class="col-10">
								<select class="form-control" id="date_filter">
									<option></option>
								</select>
							</div>
						</div>
					</div>

					<div class="col-md-4 m--hide" id="custom_range">
						<div class="input-group" id="date-picker">
							<input type="text" class="form-control m-input" readonly placeholder="MMM DD, YYYY - MMM DD, YYYY" name="date_range" data-validation="required" id="date-range" style="border-color: rgb(185, 74, 72);">
							<span class="input-group-addon">
								<i class="la la-calendar-check-o"></i>
							</span>
						</div>

						<span class="m-form__help">
							<small style="color: darkgray">Billing From - Billing To</small>
						</span>
					</div>

					<div class="col-md-2">
						<button class="btn btn-success m-btn m-btn-success" onclick="generateReport()">Generate</button>
					</div>
				</div>

				<hr>

				<input type="hidden" value="" id="selectedDate">
				<input type="hidden" value="customer_id" id="customer_id">
				<input type="hidden" value="account_name" id="account_name">
				<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

				<div id="soa_details" class="row">
					<div class="col-6">
						<div class="col-6 mb-2">
							<strong>Name : </strong>
							<strong id="name"></strong>
						</div>

						<div class="col-6 mb-2">
							<strong>Account No. : </strong>
							<strong id="account_no"></strong>
						</div>

						<div class="col-6 mb-2">
							<strong>Meter No. : </strong>
							<strong id="meter_no"></strong>
						</div>
					</div>

					<div id="statement_details" class="col-6">
						<div class="col-6 mb-2">
							<strong>Overdue Charges : </strong>
							<strong id="balance">₱ {{ total_charges }}</strong>
						</div>

						<div class="col-6 mb-2">
							<strong>Total Penalty : </strong>
							<strong id="total_penalty">₱ {{ total_penalty }}</strong>
						</div>

						<div class="col-6 mb-2">
							<strong>Overpayment Balance : </strong>
							<strong id="overPayment">₱ {{ overpayment }}</strong>
						</div>

						<div class="col-12">
							<strong style="font-weight: bold; color: #6b6b71; font-size: 15px;">Total balance : </strong>
							<strong id="total_balance" style="font-weight: bold; color: #6b6b71; font-size: 15px;">₱ {{ total_balance }}</strong>
						</div>
					</div>
				</div>

				<div id="report-tbl-wrapper" class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll mt-5">
					<div class="report-wrapper m--hide" data-type="payment">
						<h4 class="text-center mb-3" style="font-weight: 600;">Payment</h4>

						<table class="table table-striped table-bordered table-responsive" id="table-reports_soa" width="100%" style="display: table; width: 100%;">
							<col width="10%">
							<col width="10%">
							<col width="12%">
							<col width="12%">
							<col width="5%">
							<col width="*">
							<col width="*">
							<col width="*">
							<col width="*">
							<col width="*">
							<thead>
								<tr>
									<th>Date Paid</th>
									<th>Date Log</th>
									<th>Pay. Ref. #</th>
									<th>Bill. Ref. #</th>
									<th>Type</th>
									<th>Bill</th>
									<th>Penalty</th>
									<th>Covered</th>
									<th>Net</th>
									<th>Received</th>
								</tr>
							</thead>

							<tfoot align="right">
								<tr>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th style="text-align: right;"></th>
									<th></th>
									<th></th>
									<th id="payment_footer_total" style="font-weight: bold; color: #525252;"></th>
								</tr>
							</tfoot>
						</table>
					</div>

					<div class="report-wrapper m--hide" data-type="billing">
						<h4 class="text-center mb-3" style="font-weight: 600;">Billing</h4>

						<table class="table table-striped table-bordered table-responsive" id="table-reports_billing" width="100%" style="display: table; width: 100%;">
							<thead>
								<tr>
									<th>Reference no.</th>
									<th>From</th>
									<th>To</th>
									<th>Status</th>
									<th class="text-center">Usage</th>
									<th style="text-align: right;">Total Charges</th>
								</tr>
							</thead>

							<tfoot align="right">
								<tr>
									<th></th>
									<th></th>
									<th></th>
									<th style="text-align: right;">Total</th>
									<th></th>
									<th style="text-align: right;"></th>
								</tr>
							</tfoot>
						</table>
					</div>

					<div class="report-wrapper m--hide" data-type="reading">
						<h4 class="text-center mb-3" style="font-weight: 600;">Reading</h4>

						<table class="table table-striped table-bordered table-responsive" id="table-reports_reading" width="100%" style="display: table; width: 100%;">
							<thead>
								<tr>
									<th>Reference no.</th>
									<th>Reading Date</th>
									<th>Reading</th>
								</tr>
							</thead>

							<tfoot align="right">
								<tr>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>

					<div class="report-wrapper m--hide" data-type="ledger">
						<h4 class="text-center mb-3" style="font-weight: 600;">Ledger</h4>

						<div id="soa_ledger_vue_wrap">
							<div class="table-header">
								<div class="row align-items-start justify-content-between mx-0">
									<div class="col">
										<h6 class="text-center mb-0">Due / Payment Date</h6>
									</div>

									<div class="col">
										<h6 class="text-center mb-0">Ref. No.</h6>
									</div>

									<div class="col px-3">
										<h6 class="text-right mb-0">DEBIT</h6>
										<small class="d-block text-right mt-1 mb-0" style="font-size: 9px;">Total Charges + Penalties + RF = Debit</small>
									</div>

									<div class="col px-3">
										<h6 class="text-right mb-0">CREDIT</h6>
										<small class="d-block text-right mt-1 mb-0" style="font-size: 9px;">Rec Amt + Bal Cov = Debit</small>
									</div>

									<div class="col px-3">
										<h6 class="text-right mb-0">BALANCE</h6>
									</div>
								</div>
							</div>
							
							<div class="table-body">
								<div v-if="ledger_data.length" id="ledgerAccordion" class="m-accordion m-accordion--default m-accordion--solid m-accordion--section m-accordion--toggle-arrow" role="tablist">
									<div v-for="(item, index) in ledger_data" :key="`acc-${index}`" class="accord_item">
										<div :id="`ledger_head_${index}`" :href="`#ledger_body_${index}`" class="accord_item-head py-2 collapsed" role="tab" data-toggle="collapse" aria-expanded="false">
											<span class="accord_item-icon ml-4"><i class="la la-angle-down"></i></span>
											<span class="accord_item-title">
												<div class="row align-items-center w-100 mx-0">
													<div class="col text-center">{{ formatDate(item.due_date) }}</div>
													<div class="col text-center">{{ item.b_ref_no }}</div>
													<div class="col text-right">
														<small>{{ item.total_charges }} + {{ item.penalty }} + {{ item.reconnection_fee }} =</small>
														<div>₱ {{ item.debit }}</div>
													</div>
													<div class="col text-right">₱ {{ item.credit }}</div>
													<div class="col text-right">₱ {{ item.balance }}</div>
												</div>
											</span>
										</div>

										<div :id="`ledger_body_${index}`" class="accord_item-body collapse" role="tabpanel" data-parent="#ledgerAccordion">
											<div class="accord_item-content">
												<template v-if="item.payment_history && item.payment_history.length">
													<div v-for="(pay, pIndex) in item.payment_history" :key="`p-${index}-${pIndex}`" :class="pay.is_archive == 1 ? 'archived' : 'active'" class="payment-data py-3 row mx-0 align-items-center justify-content-between">
														<div class="col text-center" :style="{ color: pay.font_color }">{{ formatDate(pay.payment_date) }}</div>
														<div class="col text-center" :style="{ color: pay.font_color }">{{ pay.ref_no }}</div>
														<div class="col text-right" :style="{ color: pay.font_color }">₱ {{ pay.raw_net_payment }}</div>
														<div class="col text-right" :style="{ color: pay.font_color }">
															<small>{{ pay.raw_received_amount }} + {{ pay.balance_covered }} =</small>
															<div>₱ {{ pay.received_amount }}</div>
														</div>
														<div class="col text-right" :style="{ color: pay.font_color }">₱ {{ pay.balance }}</div>
													</div>
												</template>

												<template v-else>
													<div class="alert m-alert--default mb-0" role="alert">
														<p class="text-center text-muted mb-0" style="font-weight: 600;">No payment history</p>
													</div>
												</template>
											</div>
										</div>
									</div>
								</div>
								<template v-else>
									<div class="alert m-alert--default mb-0" role="alert">
										<p class="text-center text-muted mb-0" style="font-weight: 600;">No ledger data</p>
									</div>
								</template>
							</div>
							
							<div class="table-footer">
								<div class="row align-items-center justify-content-between mx-0 mt-4">
									<div class="col"></div>
									<div class="col"></div>
									<div class="col"></div>
									<div class="col text-right">
										 <h5 style="font-weight: 900;color: #737373;">TOTAL</h5>
									</div>
									<div class="col text-right"><h5 style="font-weight: 900;color: #737373;">₱ {{ totalBalance < 0 ? 0 : totalBalance}}</h5></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-success btnPrint" id="print_ledger" disabled>Print</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
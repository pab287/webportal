<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
                                Statement of Account
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						
					</div>
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
                </div>
					<!--begin: Datatable -->
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
										<th>Total Balance</th>
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

<div class="modal fade" id="m_soa" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered" style="min-width: 70%">
		<div class="modal-content">
			<div class="modal-header" id="soa_modal">
				<h5 class="modal-title" style="margin-top: 7px;">
					Account Statement
				</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">
              ×
          </span>
        </button>
			</div>
        <div class="col-12 modal-body">
          <div class="row mb-3">
            <div class="col-md-3">
              <div class="form-group m-form__group row">
                <label for="example-text-input" class="col-2 col-form-label">
                  Type
                </label>
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
                <label for="example-text-input" class="col-2 col-form-label">
                  Date
                </label>
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
                <small style="color: darkgray">
                  Billing From - Billing To
                </small>
              </span>
            </div>
            <div class="col-md-2">
              <button class="btn btn-success m-btn m-btn-success" onclick="generateReport()">Generate</button>
            </div>
          </div>
				<hr>
				<input type="hidden" value="selectedDate" id="selectedDate">
				<input type="hidden" value="customer_id" id="customer_id">
				<input type="hidden" value="account_name" id="account_name">
				<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<div class="row col-12" style="padding: 0;">
					<div class="col-6"style="padding: 0;">
						<div class="col-6" style="margin-bottom: 3px;">
							<strong style="font-weight: bold; color: #6b6b71;">Name : </strong>
							<strong id="name"></strong>
						</div>
						<div class="col-6" style="margin-bottom: 3px;">
							<strong style="font-weight: bold; color: #6b6b71;">Account No. : </strong>
							<strong id="account_no"></strong>
						</div>
						<div class="col-6" style="margin-bottom: 3px;">
							<strong style="font-weight: bold; color: #6b6b71;">Meter No. : </strong>
							<strong id="meter_no"></strong>
						</div>
					</div>
					<div class="col-6">
						<div class="col-6" style="margin-bottom: 3px;">
							<strong style="font-weight: bold; color: #6b6b71;">Overdue Charges : </strong>
							<strong id="balance"></strong>
						</div>
						<div class="col-6" style="margin-bottom: 3px;">
							<strong style="font-weight: bold; color: #6b6b71;">Total Penalty : </strong>
							<strong id="total_penalty"></strong>
						</div>
						<div class="col-6" style="margin-bottom: 3px;">
							<strong style="font-weight: bold; color: #6b6b71;">Overpayment Balance : </strong>
							<strong id="overPayment"></strong>
						</div>
            <div class="col-12" style="margin-bottom: 3px;">
            <i>
							<strong style="font-weight: bold; color: #6b6b71; font-size: 15px;">Total balance : </strong>
							<strong id="total_balance" style="font-weight: bold; color: #6b6b71; font-size: 15px;"></strong>
            </i>
						</div>
					</div>
				</div>
				<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
					<table class="table table-striped table-bordered table-responsive m--hide" id="table-reports_soa" width="100%" style="display: table; width: 100%;">
						<col width="13%" />
						<col width="13%" />
						<col width="13%" />
						<col width="*" />
						<col width="10%" />
						<col width="10%" />
						<col width="13%" />
						<col width="12%" />
						<thead>
							<tr><th class="text-center" colspan="8"><h6>PAYMENT</h6></th></tr>
							<tr>
								<th>Reference no.</th>
								<th>Bill</th>
								<th>Date</th>
								<th>Payment Type</th>
								<th>Bill Amount</th>
								<th>Net Payment</th>
								<th>Balance Covered</th>
								<th>Payment Amount</th>
							</tr>
						</thead>
						<tfoot align="right">
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th style="text-align: right;"></th>
								<th></th>
								<th></th>
								<th style="font-weight: bold; color: #525252;"></th>
							</tr>
						</tfoot>
					</table>


					<table class="table table-striped table-bordered table-responsive m--hide" id="table-reports_billing" width="100%" style="display: table; width: 100%;">
						<thead>
							<tr><th class="text-center" colspan="6">BILLING</th></tr>
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

					<table class="table table-striped table-bordered table-responsive m--hide" id="table-reports_reading" width="100%" style="display: table; width: 100%;">
						<thead>
							<tr rowspan="3"><th class="text-center">READING</th></tr>
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

					<table class="table table-striped table-bordered table-sm table-condensed table-responsive m--hide" id="table-reports_ledger" width="100%" style="display: table; width: 100%;">
						<thead>
							<tr><th class="text-center" colspan="5" style="font-size: 24px; font-weight: bold;">Ledger</th></tr>
							<tr>
								<th>Due Date</th>
								<th>Reference no.</th>
								<th>Debit</th>
								<th>Credit</th>
								<!-- <th>Balance Covered</th> -->
								<th>Balance</th>
							</tr>
						</thead>
						<tfoot align="right">
							<tr>
								<th></th>
								<th></th>
								<th><span style="font-color: 'black'"><small style="font-size: 9px;">Total Charges + Penalties + RF = Debit</small></span></th>
								<th></th>
								<!-- <th></th> -->
								<th id="remaining_balance"></th>
							</tr>
						</tfoot>
					</table>
					
				</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success btnPrint" id="print_ledger">
					Print
				</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>
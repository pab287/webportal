
<script src='https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js'></script>
<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Billing
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
                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                    <div class="m-dropdown__inner">
                      <div class="m-dropdown__body">
                        <div class="m-dropdown__content">
                          <ul class="m-nav">
                            <li class="m-nav__item">
                              <a href="<?php echo site_url('eforms/billing/billing_archive') ?>" class="m-nav__link btnArchive">
                                <i class="m-nav__link-icon la la-archive"></i>
                                <span class="m-nav__link-text">
                                  Archive
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
          <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
            <div class="row align-items-center">
                <div class="col-xl-8 order-2 order-xl-1">
                    <div class="form-group m-form__group row align-items-center">
                        <div class="col-md-6">
          <!-- <a href="<?php echo site_url('eforms/billing/create')?>" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white">
            <span>
              <i class="la la-plus"></i>
              <span>
                New
              </span>
            </span>
                            </a> -->
                      <button class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--pill massPrint text-white"><i class="la la-print"></i> Print</button>
                                        <button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span><i class="fa fa-search"></i><span>Filter</span><span class="dropdown-toggle"></span></span>
                      </button>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-query-builder">
                          Query Builder
                        </a>
                      </div>
                      <button class="btn btn-success m-btn m-btn--icon m-btn--pill btnBilling_settings" type="button">
                        Settings
                      </button>
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
					<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">	
						<div class="row align-items-center">
							<div class="col-xl-8 order-2 order-xl-1">
								
							</div>
						</div>
					</div>
          </div>
					<!--begin: Datatable -->
          <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
            <table class="table table-striped table-bordered" id="table-billing" width="100%">
              <thead>
                <tr>
                  <th></th>
                  <th>Reference No.</th>
				  <th>Reading Ref No.</th>
                  <th>Account No.</th>
                  <th>Account Name</th>
                  <th>Meter No.</th>
                  <th>Billing Date</th>
                  <th>Due Date</th>
                  <th>Total Charges</th>
                  <th>Status</th>
                  <th>Print</th>
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

<div class="modal fade" id="m_viewBill" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document" style="min-width: 80%">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">
					View Bill
				</h5>
				<button type="button" class="close" data-dismiss="modal" >
					<span aria-hidden="true">
						×
					</span>
				</button>
			</div>
			<form action="<?php echo site_url("eforms/billing/updatebill")?>" method="POST" id="frmUpdateBill">
			<input type="hidden" name="id" id="bill_id">
			<input type="hidden" name="ref_no" id="ref_no">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<div class="modal-body">
				<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
					<div class="m-form__heading">
						<h3 class="m-form__heading-title">
							Account Information
						</h3>
					</div>
					<div class="row m--margin-bottom-20">
						<div class="col-md-4">
							<div class="form-group m-form__group">   
								<label>Account</label>
								<input class="form-control m-input account_id" readonly type="text" style="pointer-events: none;">
							</div>             
						</div>  
						<div class="col-md-4">
							<div class="form-group m-form__group">   
								<label>Reading</label>
								<input class="form-control m-input reading_id" readonly type="text" style="pointer-events: none;">
							</div>             
						</div>       

						<div class="col-md-4">
							<div class="form-group m-form__group">
								<label>Due Date</label>
								<input class="form-control m-input due_date" readonly type="text" name="due_date" data-validation="required">
							</div>             
						</div>        
					</div>
					<div class="row m--margin-bottom-20">
						<div class="col-md-4">
							<div class="form-group m-form__group">   
								<label>Customer name :</label>
								<input class="form-control m-input customer_name" type="text" readonly style="pointer-events: none;">
							</div>             
						</div>  
						<div class="col-md-4">
							<div class="form-group m-form__group">   
								<label>Meter No :</label>
								<input class="form-control m-input meter_no" type="text" readonly style="pointer-events: none;">
							</div>             
						</div>  
						<div class="col-md-2">
							<div class="form-group m-form__group">   
								<label>Block No. :</label>
								<input class="form-control m-input block_no" type="text" readonly style="pointer-events: none;">
							</div>             
						</div>  
						<div class="col-md-2">
							<div class="form-group m-form__group">   
								<label>Lot No. :</label>
								<input class="form-control m-input lot_no" type="text" readonly style="pointer-events: none;">
							</div>             
						</div>  
					</div>
					<hr>
					<div class="m-form__heading">
						<h3 class="m-form__heading-title">
							Billing Information
						</h3>
					</div>
					<div class="row m--margin-bottom-20">
						<div class="col-md-6">
							<div class="form-group m-form__group">   
								<label>Billing Address :</label>
								<input class="form-control m-input billing_address" type="text" readonly style="pointer-events: none;">
							</div>             
						</div>  
						<div class="col-md-3">
							<div class="form-group m-form__group">
								<label>Billing From :</label>
								<input class="form-control m-input billing_from" name="billing_from" type="text" readonly data-validation="required">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group m-form__group">   
								<label>Billing To :</label>
								<input class="form-control m-input billing_to" name="billing_to" type="text" readonly data-validation="required">
							</div>
						</div>
					</div>
					<div class="m-form__heading">
						<h3 class="m-form__heading-title">
							Readings
						</h3>
					</div>
					<div class="row m--margin-bottom-20"> 
						<div class="col-md-2">
							<div class="form-group m-form__group">   
								<label>Previous :</label>
								<input class="form-control m-input previous" readonly name="previous"  type="text" style="pointer-events: none;">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group m-form__group">   
								<label>Current :</label>
								<input class="form-control m-input current" readonly name="current" type="text" style="pointer-events: none;">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group m-form__group">   
								<label>Usage :</label>
								<input class="form-control m-input usage" name="usage" readonly type="text" style="pointer-events: none;">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group m-form__group">   
								<label>Rate :</label>
								<input class="form-control m-input rate" readonly type="text" style="pointer-events: none;">
							</div>
						</div>
					</div>
					<hr>
					<div class="row m--margin-bottom">
						<div class="col-md-4" style="pointer-events: none;">
							<div class="form-group form__group">   
								<h5 class="m-form__heading-title">
									Total Charges
								</h5>
								<input class="form-control-lg m-input total_charges text-right" name="total_charges" readonly type="text" style="font-weight: bold;">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-accent btnUpdate" data-id="">
        <span class="la la-edit"></span> Update
				</button>
        <button type="button" class="btn btn-success btnPrint" id="singlePrint" data-id="">
					<span class="la la-print"></span> Print
				</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">
        <span class="la la-times"></span> Close
				</button>
				<!-- <button type="button" class="btn btn-primary btnPrint" data-id="">
					Print
				</button> -->
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="m_archived" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
      <input type="hidden" name="id" id="archive_id">
      <input type="hidden" name="reading_id" id="reading_id">
      <input type="hidden" name="archive_ref_no" id="archive_ref_no">
			<div class="modal-header">
				<h5 class="modal-title">
					Archive Bill
				</h5>
			</div>
			<div class="modal-body" id="archive_text">
				
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-danger btnArchive" onclick="archiveBill()">
					Archive
				</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Cancel
				</button>
			</div>
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
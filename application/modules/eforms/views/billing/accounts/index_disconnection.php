<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Accounts for Disconnection
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
            <button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<span>For Disconnection<span class="dropdown-toggle"></span></span>
						</button>
						<div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start">
							<a class="dropdown-item" href="<?php echo site_url('eforms/billing/accounts') ?>">All Accounts</a>
							<a class="dropdown-item" href="<?php echo site_url('eforms/billing/accounts_reconnection') ?>">For Reconnection</a>
						</div>
            <!-- <ul class="m-portlet__nav">
              <li class="m-portlet__nav-item">
                <a href="<?php //echo site_url('eforms/billing/accounts') ?>" class="m-portlet__nav-link btn btn-primary m-btn m-btn--pill m-btn--air btnView">
                  <i class="m-nav__link-icon la la-users"></i>
                  <span class="m-nav__link-text">
                  All Accounts
                  </span>
                </a>
              </li>
              <li class="m-portlet__nav-item">
                <a href="<?php //echo site_url('eforms/billing/accounts_reconnection') ?>" class="m-portlet__nav-link btn btn-success m-btn m-btn--pill m-btn--air btnView">
                  <i class="m-nav__link-icon la la-check-circle-o"></i>
                    <span class="m-nav__link-text">
                    For Reconnection
                    </span>
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
                    <button class="btn btn-danger m-btn m-btn--icon m-btn--pill btnDisconnect" type="button" id="disconnect" onclick="disconnectModal()">
                      <i class="fa fa-user-times"></i> Disconnect
                    </button>
                    <!-- <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                      <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-query-builder">
                        Query Builder
                      </a>
                    </div> -->
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
                <!-- <div class="m-btn-group btn-group" role="group">
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
                </div> -->
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
                  <th>
                    <label class="m-checkbox m-checkbox--air m-checkbox--state-primary">
                      <input type="checkbox" id="cb-select-all"><span></span>
                    </label>
                  </th>
                  <th>Account name</th>
                  <th>Account No.</th>
                  <th>Meter No.</th>
                  <th>Subdivision</th>
                  <th>House Model</th>
                  <th>Street</th>
                  <th>Balance</th>
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

<div class="modal fade" id="modal_disconnect" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title m_graph_title_upcoming_due">
          Disconnect Accounts
				</h5>
			</div>
			<div class="modal-body">
        <div>Are you sure you want to disconnect selected accounts? </div>
			</div>
			<div class="modal-footer">
        <button type="button" class="btn btn-success btnSave" onclick="disconnectSelect()">
					Confirm
				</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>
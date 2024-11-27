<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Subdivision
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						
					</div>
				</div>
				<div class="m-portlet__body">
                <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                    <div class="row align-items-center">
                        <div class="col-xl-8 order-2 order-xl-1">
                            <div class="form-group m-form__group row align-items-center">
                                <div class="col-md-4">
									<a class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white">
										<span>
											<i class="la la-plus"></i>
											<span>
												New
											</span>
										</span>
                                    </a>
									<!-- <button class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill massPrint text-white"><i class="la la-print"></i> Print</button>
                                    <button class="btn btn-success m-btn m-btn--icon m-btn--pill btnBilling_settings" type="button">
										Settings
									</button>
									<button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
										<span><i class="fa fa-search"></i><span>Filter</span><span class="dropdown-toggle"></span></span>
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
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
						<table class="table table-striped table-bordered" id="table-subdivision" width="100%">
							<thead>
								<tr>
									<th>Name</th>
									<th>Meter No.</th>
									<th>Address</th>
									<th>Description</th>
									<th>Created_by</th>
									<th>Date Created</th>
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

<div class="modal fade" id="m_subdivision" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered" style="min-width: 50%">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					Create Subdivision
				</h5>
			</div>
            <form class="m-form m-form--fit" id="formSaveSubdivision" method="POST" action="<?php echo site_url('eforms/billing/save_subdivision');?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="col-12">
                        <label>Name</label>
                        <input id="name" type="text" class="form-control m-input" name="name" autocomplete="off" data-validation="required" style="font-weight: bold;">
                        <p id="help-block" style="color: red; font-size: 12px;"><i></i></p>
                    </div>
					<div class="col-12">
                        <label>Meter No.</label>
                        <input id="meterno" type="text" class="form-control m-input" name="meterno" autocomplete="off" data-validation="required">
                        <p id="help-block" style="color: red; font-size: 12px;"><i></i></p>
                    </div>
                    <div class="col-12">
                        <label>Address</label>
                        <textarea class="form-control" name="address" rows="3" data-validation="required"></textarea>
                        <p id="help-block" style="color: red; font-size: 12px;"><i></i></p>
                    </div>
                    <div class="col-12">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="5"></textarea>
                        <p id="help-block" style="color: red; font-size: 12px;"><i></i></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        Save
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>
		</div>
	</div>
</div>

<div class="modal fade" id="m_archived" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
            <input type="hidden" name="id" id="subd_id">
            <input type="hidden" name="subd_name" id="subd_name">
			<div class="modal-header">
				<h5 class="modal-title">
					Archive Subdivision
				</h5>
			</div>
			<div class="modal-body" id="archive_text">
				
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-danger btnArchive" onclick="archiveSubdivision()">
					Archive
				</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="m_edit_subdivision" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered" style="min-width: 50%">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					Edit Subdivision
				</h5>
			</div>
            <form class="m-form m-form--fit" id="formUpdateSubdivision" method="POST" action="<?php echo site_url('eforms/billing/update_subdivision_details');?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="id">
                <div class="col-12 modal-body">
                    <div class="col-12">
                        <label>Name</label>
                        <input id="name" type="text" class="form-control m-input name" name="name" autocomplete="off" data-validation="required" style="font-weight: bold;">
                        <p id="help-block" style="color: red; font-size: 12px;"><i></i></p>
                    </div>
					<div class="col-12">
                        <label>Meter No.</label>
                        <input id="meterno" type="text" class="form-control m-input meterno" name="meterno" autocomplete="off" data-validation="required" style="pointer-events: none;">
                        <p id="help-block" style="color: red; font-size: 12px;"><i></i></p>
                    </div>
                    <div class="col-12">
                        <label>Address</label>
                        <textarea class="form-control address" name="address" rows="3" data-validation="required"></textarea>
                        <p id="help-block" style="color: red; font-size: 12px;"><i></i></p>
                    </div>
                    <div class="col-12">
                        <label>Description</label>
                        <textarea class="form-control description" name="description" rows="5"></textarea>
                        <p id="help-block" style="color: red; font-size: 12px;"><i></i></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        Update
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>
		</div>
	</div>
</div>

<div class="modal fade" id="m_meter_r" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel" style="font-weight: bold; color: #7e7e7e;">
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
                <input type="hidden" name="type" value="1">
                <input type="hidden" name="old_meterno">
                <input type="hidden" name="account_id">
                <input type="hidden" name="selectedReading">
                <div class="col-12 modal-body">
					<label class="col-12 col-form-label" style="font-weight: bold; color: #7e7e7e;">
						Subdivision :
					</label>
					<div class="col-12" style="margin-bottom: 10px;">
						<input type="text" class="form-control m-input" name="account_name" id="subdivision_name" style="pointer-events: none; font-weight: bold;" />
					</div>
					<label class="col-12 col-form-label" style="font-weight: bold; color: #7e7e7e;">
						New Meter No.* :
					</label>
					<div class="col-12" style="margin-bottom: 10px;">
						<input type="text" class="form-control m-input" name="new_meterno" data-validation="required" />
					</div>
					<label class="col-12 col-form-label" style="font-weight: bold; color: #7e7e7e;">
						Remarks.* :
					</label>
					<div class="col-12">
						<textarea type="text" rows="5" class="form-control m-input" data-validation="required" name="remarks"></textarea>
					</div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-brand btnSave">
                        Save
                    </button>
                    <button type="button" class="btn btn-danger text-white" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

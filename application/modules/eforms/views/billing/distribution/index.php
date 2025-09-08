<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
                                Distribution (cu.m)
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
									<a class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white" id="btnNew" style="display: none;">
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
							<table class="table table-striped table-bordered table-responsive" id="table-water_supply" width="100%" style="display: inline-table;">
								<thead>
									<tr>
										<th>Subdivision</th>
										<th>Meter no.</th>
										<th>Reading Date</th>
										<th>Created By</th>
										<th>Distribute</th>
										<th class="text-center">Date Created</th>
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

<div class="modal fade" id="m_water" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered" style="min-width: 30%">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					Create Distribution
				</h5>
			</div>
            <form class="m-form m-form--fit" id="formWater" method="POST" action="<?php echo site_url('eforms/billing/save_distribution');?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<input type="hidden" name="subd_name">
				<input type="hidden" name="meterno_raw">
                <div class="col-12 modal-body">
					<div class="col-12">
                        <label>Subdivision</label>
                        <div>
							<select id="select_subd" name="subdivision_id" data-validation="required" data-validation="required"></select>
						</div>
                    </div>
					<br>
					<div class="col-12">
						<div class="form-group form__group">   
							<label>Reading Date</label>
							<div class="input-group date" id="m_datepicker_reading_date">
								<input type="text" class="form-control m-input" readonly name="reading_date" placeholder="Select date" data-validation="required">
								<span class="input-group-addon">
									<i class="la la-calendar"></i>
								</span>
							</div>
						</div>
					</div>
                    <div class="col-12">
                        <label>Distribute</label>
                        <input id="distribute" type="text" class="form-control m-input" name="distribute" autocomplete="off" data-validation="required">
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

<div class="modal fade" id="m_edit_water" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered" style="min-width: 30%">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					Edit Distribution
				</h5>
			</div>
            <form class="m-form m-form--fit" id="formUpdateDistribution" method="POST" action="<?php echo site_url('eforms/billing/update_distribution_details');?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="id">
                <input type="hidden" name="old_distribute">
                <input type="hidden" name="old_reading_date">
                <input type="hidden" name="subdivision_id">
                <input type="hidden" name="meterno">
                <div class="col-12 modal-body">
					<div class="col-12">
                        <label>Subdivision</label>
                        <div>
							<input id="edit_select_subd" name="subd_name" readonly type="text" class="form-control m-input" autocomplete="off" data-validation="required" style="pointer-events: none; font-weight: bold;">
						</div>
                    </div>
					<br>
					<div class="col-12">
						<div class="form-group form__group">   
							<label>Reading Date</label>
							<div class="input-group date" id="m_datepicker_reading_date_edit">
								<input type="text" class="form-control m-input reading_date" readonly name="reading_date" placeholder="Select date" data-validation="required">
								<span class="input-group-addon">
									<i class="la la-calendar"></i>
								</span>
							</div>
						</div>             
					</div>
                    <div class="col-12">
                        <label>Distribute</label>
                        <input id="distribute" type="text" class="form-control m-input" name="distribute" autocomplete="off" data-validation="required">
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

<div class="modal fade" id="m_archived" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
            <input type="hidden" id="distribution_id">
            <input type="hidden" id="archive_subd_name">
            <input type="hidden" id="archive_distribute_date">
			<div class="modal-header">
				<h5 class="modal-title">
					Archive Destribution
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

<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<!--<span class="m-portlet__head-icon">
                                <a type="button" href="index" title="Go to Dashboard" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>-->
							<h3 class="m-portlet__head-text">
								Travel Order
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<?php 
						$hasActions = 0;
						$ses_id = $this->core_layout->getCurrentEmployeeId();	
						$role = $this->core_layout->getEmployeeData($ses_id);
						if($role['group_id'] == 1){ $hasActions++; }
						?>
						<?php if($hasActions > 0): ?>
						<!-- <span class="btn m-btn" id="telegram_config" data-toggle="modal" data-target="#modal_telegram_config"><i class="la la-ellipsis-h m--font-brand"></i></span> -->
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
												<?php if($role['group_id'] == 1): ?>
													<li class="m-nav__item">
														<a href="javascript:void(0);" 
														class="m-nav__link btnAdvance_search" 
														data-toggle="modal" 
														data-target="#modal_temp_to_personnel">
															<i class="m-nav__link-icon flaticon-users"></i>
															<span class="m-nav__link-text">TO Temp Personnel</span>
														</a>
													</li>
													<li class="m-nav__item">
														<a href="javascript:void(0);" 
														class="m-nav__link btnAdvance_search" 
														id="telegram_config" 
														data-toggle="modal" 
														data-target="#modal_telegram_config">
															<i class="m-nav__link-icon flaticon-settings"></i>
															<span class="m-nav__link-text">Telegram Integration</span>
														</a>
													</li>
												<?php endif; ?>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</li>
						</ul>
						<?php endif; ?>
					</div>
				</div>
				<div class="m-portlet__body">
                <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                    <div class="row align-items-center">
                        <div class="col-xl-8 order-2 order-xl-1">
                            <div class="form-group m-form__group row align-items-center">
                                <div class="col-md-6">
									<a href="<?php echo site_url("eforms/travel_order/new_travel_order");?>" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white">
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
									<a href="javascript:approve_to();" class="btn btn-success m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnAccomplishment text-white" data-skin="dark" data-toggle="m-tooltip" data-placement="top" title="" data-original-title="Accomplish" hidden>
										<span>
											<i class="la la-thumbs-up"></i>
											<span>
												&nbsp;
											</span>
										</span>
									</a>  

									<button type="button" class="btn btn-success" >
											Dark skin
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
							<div class="col-xl-4 order-1 order-xl-2 m--align-right">
								<div class="form-group m-form__group row">
									<div class="col-lg-12 col-md-9 col-sm-12">
										<div class="input-group" id="m_daterangepicker_2">
											<input type="text" name="range_date" id="range_date" class="form-control m-input" placeholder="Select date range">
											<span class="input-group-addon">
												<i class="la la-calendar-check-o"></i>
											</span>
											<input type='hidden' name="start_date" id="start_date">
											<input type='hidden' name="end_date" id="end_date">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
                </div>
					<!--begin: Datatable -->
						<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
							<table class="table table-striped table-bordered" id="table-travel_order" width="100%">
								<thead>
									<tr>
										<th>
				                            <label class="m-checkbox m-checkbox--air m-checkbox--state-success">
				                                <input type="checkbox" id="cb-select-all"><span></span>
				                            </label>
				                        </th>
										<th>Details</th>
										<!-- <th>Status</th>
										<th>Reference#</th>
										<th>File Under</th> -->
										<th>Driver &amp; Vehicle</th>
										<th>Personnels</th>
										<th>Destination</th>
										<th>Date Created</th>
										<th>Date Time</th>
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
		<div class="modal-dialog modal-xl" role="document">
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

<div class="modal fade" tabindex="-1" role="dialog" id="modal_telegram_config">
	<form id="frm-query-builder">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fa fa-telegram mr-1"></i>Telegram Notification</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="save_telegram_config">
					<div class="modal-body">
						<div class="form-group m-form__group row">
							<input type="hidden" id="config_id">
							<input type="hidden" id="module" value="travel_order">
							<label class="col-4 col-form-label">
								Chat Id
							</label>
							<div class="col-8">
								<input type="text" class="form-control" id="chat_id" name="chat_id">
							</div>
						</div>
						<div class="form-group m-form__group row">
							<label class="col-4 col-form-label">
								Bot Token
							</label>
							<div class="col-8">
								<textarea class="form-control" row="4" id="telegram_bot_token" name="telegram_bot_token"></textarea>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" onclick="save_telegram_config()" class="btn btn-primary btnAdvance_search"><i class="la la-save mr-2"></i>Save</button>
					</div>
				</form>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_temp_to_personnel">
	<form id="frm-query-builder">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fa fa-car mr-1"></i>Temp Personnel <small>Travel Order</small></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
						<input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="temporarySearch">
						<span class="m-input-icon__icon m-input-icon__icon--left">
							<span>
								<i class="la la-search"></i>
							</span>
						</span>
					</div>
					<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
						<table class="table table-striped table-bordered" id="temp-travel_order-table" width="100%">
							<colgroup>
							<col width="*">
							<col width="40%">
							<col width="5%">
							</colgroup>
						</table>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="approved-modal"> 
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Accomplish Travel Order</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<div class="message col-md-12"><h5>ARE YOU SURE TO WANT TO ACCOMPLISH THE SELECTED TRAVEL ORDER?</h5></div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2">Remarks</label>
					<div class="col-md-12">
						<textarea name="accomplishment_remarks" id="accomplishment_remarks"  rows="5" class="form-control" data-validation="required"> </textarea> 
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-md-8">Date Return <span class="text-danger">*</span></label>
					<div class="col-12 input-group date" id="due_dt">
						<input class="form-control m-input" type="text" name="accomplishment_dt" id="accomplishment_dt" maxlength="22" data-validation="required">
						<span class="input-group-addon">
								<i class="la la-calendar glyphicon-th"></i>
						</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary btnSave submit_approval">Yes</button>
				<button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div> 
</div>

<div class="modal fade" id="m_viewDetails" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document" style="min-width: 80%">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel" style="font-weight: bold; color: #727272;">
					View Details
				</h5>
				<button type="button" class="close" data-dismiss="modal" >
					<span aria-hidden="true">
						×
					</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
					<div class="row m--margin-bottom-20">

						<div class="col-md-6">
							<div class="row">
								<div class="col-md-5"><label>FILE UNDER: </label></div>
								<div class="col-md-7"><span style="font-weight: bold; color: #727272;" id="label_file_under">sasasa</span></div>
							</div>
							<div class="row">
								<div class="col-md-5"><label>DEPARTMENT: </label></div>
								<div class="col-md-7"><label style="font-weight: bold; color: #727272;" id="label_department">sasasa</label></div>
							</div>
							<div class="row">
								<div class="col-md-5"><label>TYPE: </label></div>
								<div class="col-md-7"><label style="font-weight: bold; color: #727272;" id="label_type">sasasa</label></div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-5"><label>OFFICIAL STATION: </label></div>
								<div class="col-md-7"><label style="font-weight: bold; color: #727272;" id="label_official_station">sasasa</label></div>
							</div>
							<div class="row">
								<div class="col-md-5"><label>TRAVEL TYPE: </label></div>
								<div class="col-md-7"><label style="font-weight: bold; color: #727272;" id="label_travel_type">sasasa</label></div>
							</div>
							<div class="row" id="layout_remarks">
								<div class="col-md-5"><label>REMARKS: </label></div>
								<div class="col-md-7"><label style="font-weight: bold; color: #727272;" id="label_remarks">sasasa</label></div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-5"><label>DRIVER: </label></div>
								<div class="col-md-7"><label style="font-weight: bold; color: #727272;" id="label_driver">sasasa</label></div>
							</div>
							<div class="row">
								<div class="col-md-5"><label>VEHICLE: </label></div>
								<div class="col-md-7"><label style="font-weight: bold; color: #727272;" id="label_vehicle">sasasa</label></div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-5"><label>REQUESTED BY: </label></div>
								<div class="col-md-7"><label style="font-weight: bold; color: #727272;" id="label_requested_by">sasasa</label></div>
							</div>
						</div>
						<div class="col-md-6">
							<div>
								<label>PERSONNEL: </label>
							</div>
								<label style="font-weight: bold; color: #727272;" id="label_personnel">sasasa</label>
							<div>
								<label>DESTINATION: </label>
							</div>
								<label style="font-weight: bold; color: #727272;" id="label_destination">sasasa</label>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<!-- <button type="submit" class="btn btn-accent btnUpdate" data-id="" id="label_">
					departure
				</button> -->
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>
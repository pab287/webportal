<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Readings
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
														<a href="archive" class="m-nav__link btnArchive">
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
                  <div class="col-md-12">
                    <a href="javascript:void(0)" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--pill btnNew text-white" data-toggle="modal" data-target="#m_newReading">
                      <span><i class="la la-plus"></i><span>New</span></span>
                    </a>

                    <a href="javascript:void(0)" class="btn btn-warning m-btn m-btn--custom m-btn--icon m-btn--pill btnNew text-white" id="generate_bill">
                      <span>
                        <i class="la la-money-bill"></i>
                        <span>Generate Bill</span>
                      </span>
                    </a>

                    <button class="btn btn-brand m-btn m-btn--custom m-btn--icon  m-btn--pill btnNew text-white" id="readings-date-picker">
                        <span>
                            <em class="fa fa-calendar"></em>
                            <span class="selected-filter pl-3 pr-2">TODAY</span>
                        </span>
                    </button>

                    <button id="tbl-btn-share" title="Export" type="button" class="btn btnExport btn-success m-btn m-btn--custom m-btn--pill text-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="la la-external-link"></i>
                      <span>Export</span>
                      <span class="dropdown-toggle"></span>
                    </button>
                    
                    <div class="dropdown-menu mt-2" aria-labelledby="btnGroupDrop1" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
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
            <table class="table table-striped table-bordered" id="table-readings" width="100%">
              <thead>
                <tr>
                  <th>
                    <label class="m-checkbox m-checkbox--air m-checkbox--state-primary">
                      <input type="checkbox" id="cb-select-all"><span></span>
                    </label>
                  </th>
                  <th>Reference No.</th>
                  <th>Account No.</th>
                  <th class="no-sort">Account Name</th>
                  <th>Meter No.</th>
                  <th>Reading Date</th>
                  <th>House Model</th>
                  <th>Block No.</th>
                  <th>Lot No.</th>
                  <th>Reading</th>
                  <th>Status</th>
                  <th class="notExport">Action</th>
                </tr>
              </thead>
              <tbody></tbody>
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
					<button type="button" onclick="clear_query_builder()" class="btn btn-danger btnNew mr-auto">Clear</button>
					<button type="button" id="query-builder-btn" class="btn btn-primary btnNew">Generate</button>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" id="m_newReading" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 60%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
              New Reading
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">
                  ×
              </span>
          </button>
        </div>
        <form class="m-form m-form--fit" id="fromCreateReading" method="POST" action="<?php echo site_url('eforms/billing/createreading');?>" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <div class="modal-body">
              <div class="m-form m-form--label-align-right m--margin-12">
                <div class="m-form__heading">
                  <h6>
                    Account Info:
                  </h6>
                </div>
                <div class="row m--margin-bottom-12">
                  <div class="col-md-6">
                    <div class="form-group m-form__group row">
                      <label class="col-4 col-form-label">
                        Account No.* :
                      </label>
                      <div class="col-8">
                        <select id="select2_account" name="account_id" data-validation="required" onchange="account_details()" data-validation="required"></select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group m-form__group row">
                      <label class="col-4 col-form-label">
                        Meter No.* :
                      </label>
                      <div class="col-8">
                        <input type="text" readonly class="form-control m-input meterno" style="pointer-events: none;">
                        <input type="hidden" name="meterno" id="meterno_raw">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row m--margin-bottom-12">
                  <div class="col-md-6">
                    <div class="form-group m-form__group row">
                      <label class="col-4 col-form-label">
                        Name :
                      </label>
                      <div class="col-8">
                        <input type="text" readonly class="form-control m-input account_name" style="pointer-events: none;">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group m-form__group row">
                      <label class="col-4 col-form-label">
                        Block No. :
                      </label>
                      <div class="col-8">
                        <input type="text" readonly class="form-control m-input block" style="pointer-events: none;">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row m--margin-bottom-12">
                  <div class="col-md-6">
                    <div class="form-group m-form__group row">
                      <label class="col-4 col-form-label">
                        Previous Reading
                      </label>
                      <div class="col-8">
                        <input type="text" readonly class="form-control m-input previous_reading" style="pointer-events: none;">
                      </div>
                    </div>
                  </div>
                    <div class="col-md-6">
                      <div class="form-group m-form__group row">
                        <label class="col-4 col-form-label">
                          Lot No. :
                        </label>
                        <div class="col-8">
                          <input type="text" readonly class="form-control m-input lot" style="pointer-events: none;">
                        </div>
                      </div>
                    </div>
                </div>
              </div>
              <br>
              <div class="m-form m-form--label-align-right m--margin-12">
                <div class="m-form__heading">
                  <h6>
                    Reading Info:
                  </h6>
                </div>
                <div class="row m--margin-bottom-12">
                  <div class="col-md-6">
                    <div class="form-group m-form__group row">
                      <label class="col-4 col-form-label">
                        Reading Date* :
                      </label>
                      <div class="col-8">
                        <input autocomplete="off" readonly type="text" id="readingdate" name="reading_date" class="form-control m-input" data-validation="required">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group m-form__group row">
                      <label class="col-4 col-form-label">
                        Reading* :
                      </label>
                      <div class="col-8">
                        <div class="initial-reading" style="position: absolute;top: -25px;"></div>
                        <input autocomplete="off" type="text" id="reading" name="reading" class="form-control m-input" data-validation="required">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <br>
              <div class="m-form m-form--label-align-right m--margin-12">
                <div class="m-form__heading">
                  <h6>
                    Attachment :
                  </h6>
                </div>
                <div class="row m--margin-bottom-10">
                    <div class="col-md-12">
                        <div class="form-group m-form__group row">
                            <div class="custom-image_container col-12">
                                <div id="img_primary" class="row">
                                    <img id="picture_null" name="picture" style="max-width: 250px; margin: 0 auto;" src="<?php echo site_url();?>/assets/images/ams/images/no_image.jpg"><br>
                                </div>
                                <div class="col-md-12 text-center">
                                    <div id="progress" class="progress">
                                        <div class="progress-bar progress-bar-success"></div>
                                    </div>  
                                    <div class="attachValueContaniner">
                                        <input type='hidden' name="pic[]" id="pic"><br>
                                    </div>
                                    <span class="btn btn-success fileinput-button">
                                        <i class="glyphicon glyphicon-plus"></i>
                                        <i class="fa fa-camera"></i>
                                        <span>Select file</span>
                                        <input type="file" id="fileupload" name="files" multiple>
                                    </span>
                                </div>
                                <div id="alt_images"></div> 
                            </div>
                        </div>
                    </div>
                </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="submit" class="btn btn-primary btnNew">
                  Save
              </button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">
                  Close
              </button>
          </div>
          </form>
      </div>
  </div>
</div>

<div class="modal fade" id="m_editReading" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="min-width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Edit Reading
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form class="m-form m-form--fit" id="fromUpdateReading" method="POST" action="<?php echo site_url('eforms/billing/updatereading');?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id">
            <input type="hidden" name="ref_no" id="ref_no_">
            <input type="hidden" name="account_id" id="account_id">
            <input type="hidden" name="old_reading" id="old_reading">
            <input type="hidden" name="old_reading_date" id="old_reading_date">
            <div class="modal-body" id="main_body">
                <div class="m-form m-form--label-align-right m--margin-10">
                    <div class="m-form__heading">
                        <h6>
                            Account Info:
                        </h6>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group m-form__group row">
                                <label class="col-4 col-form-label">
                                    Reference No.* :
                                </label>
                                <div class="col-8">
                                    <input type="text" style="pointer-events: none;" class="form-control m-input ref_no">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group m-form__group row">
                                <label class="col-4 col-form-label">
                                    Meter No.* :
                                </label>
                                <div class="col-8">
                                    <input type="text" style="pointer-events: none;" class="form-control m-input meterno" name="meterno">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group m-form__group row">
                                <label class="col-4 col-form-label">
                                    Account No.* :
                                </label>
                                <div class="col-8">
                                    <!-- <select id="select2_account_edit" name="account_id" data-validation="required" onchange="account_details()"></select> -->
                                    <input type="text" style="pointer-events: none;" class="form-control m-input account_no">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group m-form__group row">
                                <label class="col-4 col-form-label">
                                    Block No. :
                                </label>
                                <div class="col-8">
                                    <input type="text" style="pointer-events: none;" class="form-control m-input block">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group m-form__group row">
                                <label class="col-4 col-form-label">
                                    Name :
                                </label>
                                <div class="col-8">
                                    <input type="text" style="pointer-events: none;" class="form-control m-input account_name">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group m-form__group row">
                                <label class="col-4 col-form-label">
                                    Lot No. :
                                </label>
                                <div class="col-8">
                                    <input type="text" style="pointer-events: none;" class="form-control m-input lot">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="m-form m-form--label-align-right m--margin-10">
                    <div class="m-form__heading">
                        <h6>
                            Reading Info:
                        </h6>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group m-form__group row">
                                <label class="col-4 col-form-label">
                                    Reading Date* :
                                </label>
                                <div class="col-8">
                                    <p class="form-control m-input m-input--solid readingdate_readonly"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group m-form__group row">
                                <label class="col-4 col-form-label">
                                    Reading* :
                                </label>
                                <div class="col-8">
                                    <input type="text" id="edit_reading" name="reading" class="form-control m-input reading" data-validation="required">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <br>
                <div class="m-form m-form--label-align-right m--margin-10">
                    <div class="m-form__heading">
                        <h6>
                            Attachment:
                        </h6>
                    </div>

                    <div class="row m--margin-bottom-10">
                        <div class="col-md-12">
                            <div class="form-group m-form__group row">
                                <div class="custom-image_container col-12">
                                    <div id="img_primary" class="row">
                                        <img id="picture_null" name="picture" style="max-width: 250px; margin: 0 auto;" src="<?php echo site_url();?>/assets/images/ams/images/no_image.jpg"><br>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <div id="progress" class="progress">
                                            <div class="progress-bar progress-bar-success"></div>
                                        </div>  
                                        <div class="attachValueContaniner">
                                            <input type='hidden' name="pic[]" id="pic"><br>
                                        </div>
                                        <span class="btn btn-success fileinput-button">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            <i class="fa fa-camera"></i>
                                            <span>Select file</span>
                                            <input type="file" id="fileupload_" name="files" multiple>
                                        </span>
                                    </div>
                                    <div id="alt_images"></div> 
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnUpdate">
                    Update
                </button>
                <!-- <button type="submit" class="btn btn-warning btnCancel">
                    Cancel
                </button> -->
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="m_generate" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					Confirmation
				</h5>
			</div>
			<div class="modal-body" id="generate_text">
				Are you sure you wan't to generate bill?
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary btnNew" onclick="generateBill()">
					Yes
				</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="m_archived" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
            <input type="hidden" name="id" id="archive_id">
            <input type="hidden" name="archive_ref_no" id="archive_ref_no">
			<div class="modal-header">
				<h5 class="modal-title">
					Archive Reading
				</h5>
			</div>
			<div class="modal-body" id="archive_text">
				
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-danger btnArchive" onclick="archiveReading()">
					Archive
				</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>
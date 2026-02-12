<style>
    #modal-import-overtime .modal-content.dimmed > * {
        position: relative;
        z-index: 11;
    }
    #modal-import-overtime .modal-content.dimmed {
        filter: blur(2px) brightness(0.7);
    }
</style>

<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Overtime
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
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
														<span class="m-nav__section-text">
															Quick Action
														</span>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="<?=base_url('eforms/overtime/signatories') ?>" class="m-nav__link btnView">
                                                            <i class="m-nav__link-icon flaticon-open-box"></i>
                                                            <span class="m-nav__link-text">
																OT Signatories
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
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-12">
                                        <a href="new_overtime" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew">
                                            <span> 
                                                <i class="la la-plus"></i>
                                                <span>
                                                    New
                                                </span>
                                            </span>
                                        </a>
                                        <!-- <button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span><i class="fa fa-search"></i><span>Filter</span><span class="dropdown-toggle"></span></span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#modal-advance-search">
                                                Advanced Search
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#modal-query-builder">
                                                Query Builder
                                            </a>
                                        </div> -->

                                        <a href="javascript:void(0);" class="btn btn-warning m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnUpload" data-toggle="modal" data-target="#modal-import-overtime">
                                            <span> 
                                                <i class="la la-upload"></i>
                                                <span>
                                                    Import
                                                </span>
                                            </span>
                                        </a>

                                        <button class="btn btn-default m-btn m-btn--icon m-btn--pill btnQuick_action" type="button" id="dropdownMenuMassButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <!-- <span><i class="la la-ellipsis-h"></i> Actions <span class="dropdown-toggle"></span></span> -->
                                            <span> Actions <span class="dropdown-toggle"></span></span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuMassButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <li>
                                                <a class="dropdown-item btn btnAdvance_search" href="javascript:void(0);" data-toggle="modal" data-target="#modal-advance-search">
                                                    Advanced Search
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item btn btnAdvance_search" href="javascript:void(0);" data-toggle="modal" data-target="#modal-query-builder">
                                                    Query Builder
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item btn btnMass_approve" href="javascript:void(0);" data-toggle="modal" data-target="#modal-mass-approve">
                                                    Mass Approve
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item btn btnMass_approve" href="javascript:void(0);" data-toggle="modal" data-target="#modal-mass-disapprove">
                                                    Mass Disapprove
                                                </a>
                                            </li>
                                        </ul>
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
                                <div class="m-btn-group btn-group">
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
                    </div>
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
                        <table class="table table-striped table-bordered" id="table-overtime" width="100%">
                            <thead>
                                <tr>
                                    <th>
                                        <label class="m-checkbox m-checkbox--air m-checkbox--state-success">
                                            <input type="checkbox" id="cb-select-all"><span></span>
                                        </label>
                                    </th>
                                    <th>Status</th>
                                    <th>Reference No</th>
                                    <th>Employee</th>
                                    <th>Purpose</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Hrs Work</th>
                                    <th class="notExport">Action</th>
                                </tr>
                            </thead>
                            <tbody>	
                            </tbody>
                        </table>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-advance-search">
    <div class="modal-dialog" role="document">
        <form id="frm-advance-search" method="post" action="<?php echo site_url("eforms/overtime/advanced_search_request"); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Advance Search</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">&nbsp;</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Disapproved">Disapproved</option>
                        </select>
                    </div>

                    <div class="form-group pt-3">
                        <label for="employee">Company</label>
                        <select class="form-control" name="company" id="company"></select>
                    </div>
                    <div class="form-group pt-3">
                        <label for="employee">Employee</label>
                        <select class="form-control" name="employee" id="employee"></select>
                    </div>
                    <div class="form-group pt-3">
                        <label for="date_time">Date From &amp; To</label>
                        <div class="input-group date">
                            <input class="form-control m-input" type="text" name="date_time" id="date_time" autocomplete="off" data-validation="required" />
                            <span class="input-group-addon">
                                <i class="la la-calendar glyphicon-th"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="refresh" class="btn btn-info btnView" data-dismiss="modal">Refresh</button>
                    <button type="submit" id="advanced_search" class="btn btn-primary btnAdvance_search">Search</button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                </div>

            </div>
        </form>
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
					<button type="button" onclick="clear_query_builder()" class="btn btn-danger btnCancel mr-auto">Clear</button>
					<button type="button" id="query-builder-btn" class="btn btn-primary btnAdvance_search">Generate</button>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-import-overtime">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Overtime</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="accordionMain">
                    <div class="card">
                        <form id="frm-import_overtime" method="post" action="<?php echo site_url("eforms/overtime/import_approved_overtime"); ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <!-- data-toggle="collapse" -->
                            <div class="card-header" id="import-head">
                                <h5 class="mb-0">
                                    <div class="row justify-content-between align-items-center">
                                        <div class="col-md-6">
                                            <a class="collapsed" data-target="#temp-uploaded_content" aria-expanded="false" aria-controls="temp-uploaded_content"  style="font-size: 1.15rem !important">
                                                Import
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="m-portlet__nav-link btn btn-success m-btn m-btn--pill m-btn--air fileinput-button btnUpload pull-right">
                                                <i class="fa fa-plus"></i>
                                                <span>Upload File</span>
                                                <input id="import_csv" type="file" name="import_csv" />
                                            </span>
                                        </div>
                                    </div>
                                </h5>
                            </div>

                            <div id="temp-uploaded_content" class="collapse show" aria-labelledby="import-head" data-parent="#accordionMain">
                                <div class="card-body">
                                    <div class="content">
                                        <div id="progress_uploaded_csv" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="display:none;">
                                            <div class="progress-bar progress-bar-success" style="width: 0%;"></div>
                                        </div>

                                        <h6 id="to-hide" class="text-center">No Data Found.</h6>

                                        <template v-if="has_uploaded_file === true">
                                            <input type="hidden" name="json_file" v-model="json_file" />
                                            <template v-if="invalid_ctr > 0">
                                                <div class="mt-3 m-alert m-alert--icon m-alert--outline alert alert-warning fade show" role="alert">
                                                    <div class="m-alert__icon">
                                                        <i class="la la-warning"></i>
                                                    </div>
                                                    <div class="m-alert__text">
                                                        <strong>Invalid Overtime Entries!</strong> There are <strong>`{{ invalid_ctr }}`</strong> invalid entries that are not allowed to be imported. Please read <strong>remarks</strong>.
                                                    </div>
                                                </div>
                                            </template>
                                            <div class="mb-5">
                                                <table id="uploaded_csv_table" class="table"></table>
                                            </div>
                                            <template v-if="valid_ctr > 0">
                                                <div class="row m--margin-top-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group m-form__group">
                                                            <label for="approved_by" class="required">
                                                                Approved By
                                                            </label>
                                                            <select class="form-control m-input m-input--air" id="approved_by" name="approved_by" data-validation="required"></select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="m-portlet m-portlet--bordered m-portlet--unair">
                                                            <div class="m-portlet__head" style="height: 4.5rem !important;">
                                                                <div class="m-portlet__head-caption">
                                                                    <div class="m-portlet__head-title">
                                                                        <h3 class="m-portlet__head-text">
                                                                            Attachment Image <small style="color: red;">( Required )</small>
                                                                        </h3>
                                                                    </div>
                                                                </div>
                                                                <div class="m-portlet__head-tools">
                                                                    <ul class="m-portlet__nav">
                                                                        <li class="m-portlet__nav-item">
                                                                            <span class="m-portlet__nav-link btn btn-success m-btn m-btn--pill m-btn--air fileinput-button btnUpload">
                                                                                <i class="fa fa-plus"></i>
                                                                                <span>Upload File</span>
                                                                                <input id="temp_fileupload" type="file" name="files" multiple />
                                                                            </span>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="m-portlet__body">
                                                                <div id="progress_approve" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="display:none;" >
                                                                    <div class="progress-bar progress-bar-success" style="width: 0%;"></div>
                                                                </div>
                                                                <template v-if="count > 0">
                                                                    <div class="row">
                                                                        <div class="col-2 col-md-2" v-for="(item, index) in rows">
                                                                            <div class="m-temp__pic text-center">
                                                                                <a :href="item.image" data-lightbox="tempimage" :data-title="item.filename">
                                                                                    <img class="m-temp__img" :src="item.thumbnail" width="75" height="75" style="margin-bottom: 0.5rem;" />
                                                                                </a>
                                                                                <div class="m-checkbox-inline">
                                                                                    <label class="m-checkbox">
                                                                                        <input type="checkbox" name="attachment_image[]" :value="item.current_image" class="temp-attachment_image" @click="getCheckedCount" />{{renderImageLabel(index)}}<span></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-12 m--margin-top-10 text-left">
                                                                            <input id="checked_count" type="hidden" data-validation="checkbox_group_min1" value="0" />
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                                <template v-else>
                                                                    <div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                                                                        <div class="m-alert__icon">
                                                                            <i class="flaticon-exclamation-1"></i>
                                                                            <span></span>
                                                                        </div>
                                                                        <div class="m-alert__text">
                                                                            <strong>
                                                                                Image(s) not found!
                                                                            </strong>
                                                                            Upload image first
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </template>
                                        <template v-else>
                                            <template v-if="employee_records.length > 0">
                                                <div class="mt-3">
                                                    <div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                                                        <div class="m-alert__icon">
                                                            <i class="flaticon-exclamation-1"></i>
                                                            <span></span>
                                                        </div>
                                                        <div class="m-alert__text">
                                                            <strong>
                                                                Existing Overtime!
                                                            </strong>
                                                            Employee overtime already exist.
                                                        </div>
                                                    </div>
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Reference #</th>
                                                                <th>Employee Name</th>
                                                                <th>Date From</th>
                                                                <th>Date To</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(item, index) in employee_records">
                                                                <td>
                                                                    <template v-for="(vv, ii) in item.reference_no">
                                                                        <span class="m-badge m-badge--metal m-badge--wide m-badge--rounded mr-1 m--font-boldest">{{vv}}</span>
                                                                    </template>
                                                                </td>
                                                                <td>{{item.display_name}}</td>
                                                                <td>{{item.date_from}}</td>
                                                                <td>{{item.date_to}}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                        </template>

                                    </div> 
                                    
                                    <div class="content-footer text-right" v-if="has_uploaded_file === true && valid_ctr > 0">
                                        <button type="submit" id="submit-import-overtime" class="btn btn-primary btnSave">Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card mt-3">
                        <!-- data-toggle="collapse" -->
                        <form id="print-import_overtime" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="card-header" id="signatory-head">
                                <h5 class="mb-0">
                                    <a class="collapsed" data-target="#signatory-content" aria-expanded="false" aria-controls="signatory-content"  style="font-size: 1.15rem !important">
                                        Overtime Signatory
                                    </a>
                                </h5>
                            </div>

                            <div id="signatory-content" class="collapse" aria-labelledby="signatory-head" data-parent="#accordionMain">
                                <input type="hidden" name="approvedIds" v-model="approvedIds">
                                <div class="card-body">
                                    <template v-if="isPrint">
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <div id="signatory-section" class="row mt-3 align-items-end align-items-center">
                                                    <div class="col-md-3 pr-0">
                                                        <label for="signatory" class="required m-0">Signatory Filter</label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-0">
                                                            <select id="signatory" name="signatory" class="form-control" data-validation="required">
                                                                <option></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5 p-0">
                                                        <button type="button" class="btn btn-warning btn-sm btnEdit m-btn m-btn--pill m-btn--air d-none mr-2" id="editSignatory" @click="editSignatory" title="Edit Signatory" data-toggle="m-tooltip" data-original-title="Edit Signatory" data-skin="dark">
                                                            <i class="la la-pencil"></i> Edit Signatory
                                                        </button>
                                                        <button type="button" class="btn btn-accent btn-sm btnEdit m-btn m-btn--pill m-btn--air d-none" id="resetSignatory" @click="resetModalSignatory" title="Reset Signatory" data-toggle="m-tooltip" data-original-title="Edit Signatory" data-skin="dark">
                                                            <i class="la la-rotate-left"></i> Reset Signatory
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4 justify-content-start align-items-center">
                                            <template v-if="signatory.count > 0"> 
                                                <template v-for="(item, index) in signatory.meta">
                                                    <div class="col-md-4 mb-2" v-if="item.is_active === true">
                                                        <p style="font-weight: bold; margin-left: 10px;">{{ item.label }}</p>
                                                        <p class="signatory-value" style="font-weight: 600; margin-left: 10px; margin-right: 50px; margin-top: 50px; padding-top: 10px; border-top: 1px solid rgb(0, 0, 0);">
                                                            {{ item.value }}
                                                        </p>
                                                    </div>
                                                </template>
                                            </template>
                                        </div>
                                        
                                        <div class="content-footer text-right">
                                            <button type="submit" id="print-import-overtime" class="btn btn-primary btnSave">Print Summary</button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal-mass-update">
    <div class="modal-dialog">
        <form id="frm-mass-update" method="post" action="<?php echo site_url("eforms/overtime/mass_update"); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mass Update Overtime</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group m-form__group row align-items-center">
                        <label for="" class="col-4">Date & Time *</label>
                        <div class="col-7 input-group date p-0" id="mass_date_time">
                            <input class="form-control m-input mb-0" type="text" id="date" data-validation="required" autocomplete="off" />
                            <input type="hidden" name="date_from" id="date_from"/>
                            <input type="hidden" name="date_to" id="date_to"/>
                            <span class="input-group-addon">
                                    <i class="la la-calendar glyphicon-th"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="advanced_search" class="btn btn-primary btnEdit btn-submit">Update</button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal-mass-approve">
    <div class="modal-dialog">
        <form id="frm-mass-approve" method="post" action="<?php echo site_url("eforms/overtime/mass_approve"); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mass Approve Overtime</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group m-form__group row">
                        <div class="col-12">
                            <label for="mass_approved_by">
                                Approved By
                            </label>
                            <select class="form-control m-input m-input--air" id="mass_approved_by" name="approved_by" data-validation="required"></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="advanced_search" class="btn btn-primary btnEdit btn-submit">Approve</button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal-mass-disapprove">
    <div class="modal-dialog">
        <form id="frm-mass-disapprove" method="post" action="<?php echo site_url("eforms/overtime/mass_disapprove"); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mass Disapprove Overtime</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group m-form__group row">
                        <div class="col-12">
                            <label for="mass_disapproved_by">
                                Disapproved By
                            </label>
                            <select class="form-control m-input m-input--air" id="mass_disapproved_by" name="disapproved_by" data-validation="required"></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="advanced_search" class="btn btn-primary btnEdit btn-submit">Disapprove</button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-ot--signatory">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" id="signatory--content">
            <div class="modal-header">
                <h5 class="modal-title">Overtime Signatories</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="updatePrintableSignatories" method="post" action="<?php echo site_url("eforms/overtime/update_printable_signatories"); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="id" v-model="row.tempId" />
                <input type="hidden" name="signatory_id" v-model="row.signatory_id" />
                <div class="modal-body">
                    <template v-if="count > 0">
                        <template v-for="(item, index) in row.meta">
                        <div class="form-group m-form__group row">
                            <label class="col-3 col-form-label">{{item.label}}</label>
                            <div class="col-8">
                                <select class="form-control m-input select2--value" 
                                    data-validation="required" 
                                    :name="'value['+index+']'" 
                                    :disabled="item.is_active === false">
                                    <option :value="item.value" selected>{{item.value}}</option>
                                </select>
                            </div>
                            <div class="col-1 text-right">
                                <span class="m-switch m-switch--sm">
                                    <label>
                                        <input type="checkbox" :checked="item.is_active === true" @click="activeSignatory(event)" />
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                        </template>
                    </template>
                    <template v-else>
                        <div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="m-alert__icon">
                                <i class="flaticon-exclamation-1"></i>
                                <span></span>
                            </div>
                            <div class="m-alert__text">
                                <strong>
                                    NO ASSIGNED SIGNATORIES!
                                </strong>
                                Please add/update the signatory.
                            </div>
                        </div>
                    </template>
                </div>
                <template v-if="count > 0">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnSave">Update</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
                    </div>
                </template>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-ot--reset-signatory">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" id="reset-signatory--content">
            <div class="modal-header">
                <h5 class="modal-title">Reset - Overtime Signatories</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="resetPrintableSignatories" method="post" action="<?php echo site_url("eforms/overtime/reset_printable_signatories"); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="id" v-model="row.tempId" />
                <div class="modal-body">
                    <h4>Are you sure you want to reset the current signatories?</h4>
                    <template v-if="count > 0">
                        <template v-for="(item, index) in row.meta">
                        <div class="form-group m-form__group row m--marginless" v-if="item.is_active === true">
                            <label class="col-4 col-form-label">{{item.label}}</label>
                            <label class="col-8 col-form-label m--font-bolder">{{item.value}}</label>
                        </div>
                        </template>
                    </template>
                    <template v-else>
                        <div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="m-alert__icon">
                                <i class="flaticon-exclamation-1"></i>
                                <span></span>
                            </div>
                            <div class="m-alert__text">
                                <strong>
                                    NO ASSIGNED SIGNATORIES!
                                </strong>
                                Please add/update the signatory.
                            </div>
                        </div>
                    </template>
                </div>
                <template v-if="count > 0">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnSave">Reset</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
                    </div>
                </template>
            </form>
        </div>
    </div>
</div>
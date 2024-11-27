<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div id="temp_portlet-head" class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                <template v-if="row_count > 0">
                                <span v-text="row.company+' - '+row.location">&nbsp;</span>
                                <small v-text="row.project_code">&nbsp;</small>
                                </template>
                                <template v-else>
                                    Units
                                </template>
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                                data-dropdown-toggle="hover" aria-expanded="true">
                                <a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl">
                                    <i class="la la-ellipsis-h m--font-brand"></i>
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"
                                          style="left: auto; right: 22.5px;"></span>
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
                                                        <a href="javascript:void()" class="m-nav__link" data-toggle="modal"
                                                           data-target="#modal_archives">
                                                            <i class="m-nav__link-icon flaticon-open-box"></i>
                                                            <span class="m-nav__link-text">
																Archives
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
                                        <a href="javascript:void(0);"
                                           class="btn btn-success m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew btnNewSequenceBlocks"
                                            data-toggle="modal" 
								            data-target="#modal-add_project_unit">
											<span>
												<i class="la la-plus"></i>
												<span> New</span>
											</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
										<span>
											<i class="la la-search"></i>
										</span>
									</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--begin: Datatable -->
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
                        <table class="table table-striped table-bordered" id="table-project_unit" width="100%">
                            <thead>
                            <tr>
                                <th>Code</th>
                                <th>Block</th>
                                <th>Lot</th>
                                <th>Description</th>
                                <th>Checklist Template</th>
                                <th>Checklist Status</th>
                                <th>Status</th>
                                <th>Action</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-add_project_unit">
        <div class="modal-dialog" role="document">
            <form action="<?php echo site_url("pms/project/set_modal_project_unit"); ?>" id="frmAddProjectUnit">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="project_id" value="<?php echo $id; ?>" />
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Unit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="code">Code *</label>
                            <input id="code" name="code" class="form-control m-input m--uniqueCode" data-validation="required" autocomplete="off" />
                        </div>
                        <div class="form-group">
                            <label for="">Description *</label>
                            <input id="description" name="description" class="form-control m-input" data-validation="required" autocomplete="off" />
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="block">Block *</label>
                                    <input id="block" name="block" class="form-control m-input" data-validation="required" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lot">Lot *</label>
                                    <input id="lot" name="lot" class="form-control m-input" data-validation="required" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div id="select2template" class="form-group">
                            <label for="checklist_id">Checklist Template *</label>
                            <select id="checklist_id" name="checklist_id" class="form-control m-input select2" data-validation="required">
                                <option selected disabled>Select an option</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Status</label>
                            <div class="m-radio-inline">
                                <label class="m-radio"><input id="status1" type="radio" name="is_active" value="1" checked>Active<span></span></label>
                                <label class="m-radio"><input id="status0" type="radio" name="is_active" value="0">Inactive<span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnSave">Save</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-edit_project_unit">
        <div class="modal-dialog" role="document">
            <form action="<?php echo site_url("pms/project/update_modal_project_unit"); ?>" id="frmEditProjectUnit">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="id" value="<?php echo $id; ?>" />
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Unit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="code">Code *</label>
                            <input id="code" name="code" class="form-control m-input m--uniqueCode" data-validation="required" autocomplete="off" />
                        </div>
                        <div class="form-group">
                            <label for="">Description *</label>
                            <input id="description" name="description" class="form-control m-input" data-validation="required" autocomplete="off" />
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="block">Block *</label>
                                    <input id="block" name="block" class="form-control m-input" data-validation="required" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lot">Lot *</label>
                                    <input id="lot" name="lot" class="form-control m-input" data-validation="required" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div id="select2template" class="form-group">
                            <label for="checklist_id">Checklist Template *</label>
                            <select id="checklist_id" name="checklist_id" class="form-control m-input select2" data-validation="required">
                                <option selected disabled>Select an option</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Status</label>
                            <div class="m-radio-inline">
                                <label class="m-radio"><input id="status1" type="radio" name="is_active" value="1" checked>Active<span></span></label>
                                <label class="m-radio"><input id="status0" type="radio" name="is_active" value="0">Inactive<span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnSave">Save</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
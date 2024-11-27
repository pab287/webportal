<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Checklist Items
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                                data-dropdown-toggle="hover" aria-expanded="true">
                                <a href="#"
                                   class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl">
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
                                                        <a href="javascript:void()" class="m-nav__link"
                                                           data-toggle="modal"
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
                                           class="btn btn-success m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew btnNewSequenceChecklistItem"
                                           data-toggle="modal"
                                           data-target="#modal-add_checklist_item">
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
                                    <input type="text" class="form-control m-input m-input--solid"
                                           placeholder="Search..." id="generalSearch">
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
                        <table class="table table-striped table-bordered" id="table-sequence_checklist_items"
                               width="100%">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Label</th>
                                <th>DEVELOPMENT SITE</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-add_checklist_item">
        <div class="modal-dialog" role="document">
            <form action="<?php echo site_url("pms/task/add_checklist_item"); ?>"
                  id="frmAddChecklistItem">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Checklist Item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Name *</label>
                            <input name="name" class="form-control m-input m--uniqueCode" data-validation="required"
                                   autocomplete="off"/>
                        </div>
                        <div class="form-group">
                            <label for="">Label *</label>
                            <input name="label" class="form-control m-input" data-validation="required"
                                   autocomplete="off"/>
                        </div>
                        <div class="form-group">
                            <label for="">Development Site *</label>
                            <select id="project_id" name="project_id" class="form-control m-input select2" data-validation="required"></select>
                        </div>
                        <div class="form-group">
                            <label for="">Status</label>
                            <div class="m-radio-inline">
                                <label class="m-radio"><input id="status1" type="radio" name="is_active" value="1"
                                                              checked>Active<span></span></label>
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
    <div class="modal fade" id="modal-checklist_item-list" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-m modal-xl" role="document">
            <div class="modal-content"></div>
        </div>
    </div>

    <div class="modal fade" modal-exempt-custom
         id="modal-container" tabindex="-1" role="dialog" aria-hidden="true"></div>

    <div class="modal fade" modal-exempt-custom
         id="modal-container-overlay" tabindex="-1" role="dialog" aria-hidden="true"></div>

    <div class="modal fade" id="confirm-archive-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="frm-archive-confirmation">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Archive Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure to archive <span id="item-name" class="m--font-boldest text-primary"></span>?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnArchive">Yes</button>
                        <button type="button" class="btn btn-danger btnArchive" data-dismiss="modal">No</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="confirm-update-rate-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="frm-update-rate-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Rate Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p></p>
                        <input type="hidden" value="<?= $this->security->get_csrf_hash() ?>" name="csrf_token">
                        <input type="hidden" value="" name="id">
                        <input type="hidden" value="" name="tariff">
                        <input type="hidden" value="" name="unit">
                        <input type="hidden" value="" name="checklist_id">
                        <input type="hidden" value="" name="category_id">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnArchive">Yes</button>
                        <button type="button" class="btn btn-danger btnArchive" data-dismiss="modal">No</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog"
         id="confirm-revert-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">REVERT CONFIRMATION</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure to revert to this record?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btnSave" onclick="revertRate()">YES</button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">NO</button>
                </div>
            </div>
        </div>
    </div>
</div>
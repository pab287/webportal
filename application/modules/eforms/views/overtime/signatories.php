<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon"></span>
                            <h3 class="m-portlet__head-text">
                                Overtime Summary Signatory
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
                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 22.5px;"></span>
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
                                                        <a href="<?=base_url('eforms/overtime/masterfile') ?>" class="m-nav__link btnBack">
                                                            <i class="m-nav__link-icon flaticon-open-box"></i>
                                                            <span class="m-nav__link-text">
																Back to Masterfile
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
									<div class="col-md-4">
                                        <button id="btnNewSignatory" type="button"
                                        class="btn btn-success m-btn m-btn--icon btnNew" 
                                        data-toggle="modal" 
                                        data-target="#payroll--create-signatory-modal">
                                        <span>
                                            <i class="fa fa-plus"></i>
                                            <span>NEW SIGNATORY</span>
                                        </span>
                                        </button>
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
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
                        <table class="table table-striped table-bordered" id="table-signatory" width="100%">
                            <thead>
                            <tr>
                                <th>Company</th>
                                <th>Signatory</th>
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
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="payroll--create-signatory-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Signatory</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frmCreateSignatory" method="post" action="<?php echo site_url("eforms/overtime/create_printable_signatory"); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 col-md-6 col-lg-6 col-sm-12">
                            <div class="form-group m-form__group">
                                <label for="company">
                                    Company *
                                </label>
                                <select class="form-control m-input" id="company" name="company_id" data-validation="required">
                                    <option value="">&nbsp;</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6 col-sm-12"></div>
                    </div>
                    <div id="signatory--container" class="signatory_field">
                        <div class="row">
                            <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                                <button type="button" class="btn btn-primary btn-sm btnNew" @click="addSignatoryField(event)">Add Field</button>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                                <div class="ui-sortable" id="m_sortable_portlets">
                                    <?php $this->load->view("eforms/overtime/modals/signatory_portlet"); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info m-btn m-btn--icon btnSave">Save</button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="payroll--edit-signatory-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div id="editSignatoryContent" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">edit Signatory</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frmEditSignatory" method="post" action="<?php echo site_url("eforms/overtime/update_printable_signatory"); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="id" v-model="row.id" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 col-md-6 col-lg-6 col-sm-12">
                            <div class="form-group m-form__group">
                                <label for="company">
                                    Company *
                                </label>
                                <select class="form-control m-input" id="company" name="company_id" data-validation="required">
                                    <option value="">&nbsp;</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6 col-sm-12"></div>
                    </div>
                    <div id="signatory--container" class="signatory_field">
                        <div class="row">
                            <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                                <button type="button" class="btn btn-primary btn-sm btnNew" @click="addSignatoryField(event)">Add Field</button>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                                <div class="ui-sortable" id="m_sortable_portlets">
                                    <?php $this->load->view("eforms/overtime/modals/edit_signatory_portlet"); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info m-btn m-btn--icon btnSave">Save</button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
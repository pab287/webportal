<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile m-portlet--tabs">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Contract
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--primary m-tabs-line--right m-tabs-line-primary" role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_portlet_tab_contract_list" role="tab">
                                    <i class="flaticon-file-1"></i> <small>Contract</small></a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_portlet_tab_contract_extension" role="tab">
                                    <i class="flaticon-event-calendar-symbol"></i> <small>Due Date Extension</small></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="m_portlet_tab_contract_list" role="tabpanel">
                            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                                <div class="row align-items-center">
                                    <div class="col-xl-8 order-2 order-xl-1">
                                        <div class="form-group m-form__group row align-items-center">
                                            <div class="col-md-12">
                                                <a href="javascript:void(0);"
                                                class="btn btn-success m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew btnNewContract">
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
                                <table class="table table-striped table-bordered" id="table-contract" width="100%">
                                    <thead>
                                    <tr>
                                        <th>Work Order No</th>
                                        <th>Reference WO Code</th>
                                        <th>Issued Date</th>
                                        <th>Due Date</th>
                                        <th>Contractor</th>
                                        <th>Supervisor</th>
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
                        <div class="tab-pane" id="m_portlet_tab_contract_extension" role="tabpanel">
                            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                                    <div class="row align-items-center">
                                        <div class="col-xl-8 order-2 order-xl-1">
                                            <div class="form-group m-form__group row align-items-center">
                                                <div class="col-md-12">&nbsp;</div>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                            <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                                <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearchExtension">
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
                                    <table class="table table-striped table-bordered" id="table-contract_extension" width="100%">
                                        <thead>
                                        <tr>
                                            <th>Work Order No</th>
                                            <th>Due Date</th>
                                            <th>Extension Date</th>
                                            <th>Extended By</th>
                                            <th>Approved / Disapproved By</th>
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
                </div>
            </div>
            <!--end::Portlet-->
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-add_contract">
        <div class="modal-dialog" role="document">
            <form action="<?php echo site_url("pms/contract/set_modal_contract"); ?>" id="frmAddContract">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Contract</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="company_id">Company *</label>
                            <select id="company_id" name="company_id" class="form-control m-input select2" data-validation="required"></select>
                        </div>
                        <div class="form-group">
                            <label for="location_id">Location *</label>
                            <select id="location_id" name="location_id" class="form-control m-input select2" data-validation="required"></select>
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
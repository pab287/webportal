<div class="m-section">
    <h3 class="m-section__heading lead">
        ASSET COMPONENTS
    </h3>
    <span class="m-section__sub">
    </span>
    <div class="m-section__content">
        <div class="row">
            <div class="col-md-12">
                <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                    <div class="row align-items-center">
                        <div class="col-xl-3">
                            <div class="m-input-icon m-input-icon--left">
                                <input type="text" class="form-control m-input" placeholder="Search" id="search-asset-components">
                                <span class="m-input-icon__icon m-input-icon__icon--left">
                                    <span><i class="la la-binoculars"></i></span>
                                </span>
                            </div>
                        </div>
                        <div class="col-xl-5"></div>
                        <div class="col-xl-4 m--align-right">
                            <button type="button" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew" data-toggle="modal"
                                    data-target="#mdl_add_component">
                                <span>
                                    <i class="la la-plus"></i>
                                    <span>
                                        Add Component
                                    </span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew" data-toggle="modal"
                                    data-target="#mdl_new_component">
                                <span>
                                    <i class="la la-plus"></i>
                                    <span>
                                        New Component
                                    </span>
                                </span>
                            </button>
                            <div class="m-separator m-separator--dashed d-xl-none"></div>
                        </div>
                    </div>
                </div>
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table class="table table-striped table-bordered" id="table-asset-component" width="100%">
                        <thead>
                        <th>Asset Code</th>
                        <th>Description</th>
                        <th>Remarks</th>
                        <th>Action</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mdl-delete-asset-component-from-mother_asset" tabindex="-1" role="dialog">
    <form id="frm-delete-asset-component-from-mother_asset">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" class="id" name="id">
        <input type="hidden" class="asset-id" name="asset_id">
        <input type="hidden" class="asset-code" name="asset_code">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Exclude Component
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="confirmation-message-container">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger btnNew">
                        Yes
                    </button>
                    <button type="button" class="btn btn-secondary btnNew" data-dismiss="modal">
                        No
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
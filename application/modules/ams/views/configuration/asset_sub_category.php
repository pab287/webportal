<div class="m-content">
    <div class="m-portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="flaticon-cogwheel"></i>
                    </span>
                    <h3 class="m-portlet__head-text">
                        ASSET SUB CATEGORY
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools"></div>
        </div>
        <div class="m-portlet__body">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6">
                    <button class="btn btn-accent m-btn m-btn--pill btnNew" onclick="open_new_asset_sub_category_dialog()">
                        <i class="fa fa-plus m--margin-right-5"></i>
                        <span>New Asset Sub Category</span>
                    </button>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3"></div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <div class="form-group m-form__group">
                        <div class="m-input-icon m-input-icon--left">
                            <span class="m-input-icon__icon m-input-icon__icon--left"
                                  style="z-index: 5;">
                                <span>
                                    <i class="la la-binoculars"></i>
                                </span>
                            </span>
                            <div class="input-group">
                                <input type="search" class="form-control"
                                       placeholder="Search Here..."
                                       style="height: auto;" id="search-sub-category">
                                <span class="input-group-btn">
                                    <button class="btn btn-secondary" title="Clear Filter"
                                            style="border-color: #cdcdcd;" onclick="clearFilter()">
                                        <i class="la la-close"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll m--margin-top-25">
                <table class="table table-striped table-bordered" id="table-asset-config-sub-category" width="100%">
                    <thead>
                    <th>Main Category</th>
                    <th>Sub Category Description</th>
                    <th>Code</th>
                    <th>Action</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade document-modal-container" tabindex="-1" role="dialog"></div>
</div>
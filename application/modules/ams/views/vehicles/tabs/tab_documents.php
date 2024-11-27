<div class="row">
    <div class="col-xl-8 order-2 order-xl-1">
        <button class="btn btn-success m-btn--pill btnAdd_document" type="button"
                onclick="openAddDocumentDialog()">
            <i class="fa fa-plus"></i>
            <span class="ml-1">Add Document</span>
        </button>
    </div>
    <div class="col-xl-4 order-1 order-xl-2">
        <div class="form-group m-form__group pb-0">
            <div class="m-input-icon m-input-icon--left">
                <span class="m-input-icon__icon m-input-icon__icon--left" style="z-index: 5;">
                    <span>
                        <i class="la la-binoculars"></i>
                    </span>
                </span>
                <div class="input-group">
                    <input type="search" class="form-control" placeholder="Search Here..."
                           style="height: auto;" id="search-vehicle-document" autocomplete="off">
                    <span class="input-group-btn">
                        <button class="btn btn-secondary btnNew tab-clear-search" type="button"
                                title="Clear Search"
                                data-placement="bottom"
                                style="border-color: #cdcdcd;">
                            <i class="la la-close"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll mt-3 table-responsive">
    <table class="table table-striped table-bordered" id="tab-documents-table" width="100%">
        <thead>
        <tr>
            <th>DESCRIPTION</th>
            <th>FILENAME</th>
            <th>ACTION</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
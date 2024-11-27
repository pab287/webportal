<div class="row">
    <div class="col-xl-8 order-2 order-xl-1"></div>
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
                           style="height: auto;"
                           id="search-asset-component" autocomplete="off">
                    <span class="input-group-btn">
                        <button class="btn btn-secondary btnArchive tab-clear-search" type="button"
                                data-toggle="m-tooltip" data-original-title="Clear Search"
                                data-placement="bottom" data-delay='{"show": 300}' data-skin="dark"
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
    <table class="table table-striped table-bordered table-hover" id="asset-viewing-components-table" width="100%">
        <thead>
        <tr>
            <th>CODE</th>
            <th>DESCRIPTION</th>
            <th>REMARKS</th>
            <th>STATUS</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
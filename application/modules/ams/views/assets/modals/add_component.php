<div class="modal fade" id="mdl_add_component" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" >
    <div class="modal-dialog modal-lg" role="document" style="min-width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >
                    Add Component
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs  m-tabs-line" role="tablist">
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_mother_assets_tab" role="tab">
                            Mother Assets
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_asset_components_tab" role="tab">
                            Asset Components
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" role="tabpanel" id="m_mother_assets_tab">
                        <h3>Mother Assets</h3>
                        <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                            <table class="table table-striped table-bordered" id="table-mother-asset-list" width="100%">
                                <thead>
                                    <th>Asset Code</th>
                                    <th>Description</th>
                                    <th>Location</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" role="tabpanel" id="m_asset_components_tab">
                        <h3>Asset Components</h3>
                        <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                            <table class="table table-striped table-bordered" id="table-asset-component-list" width="100%">
                                <thead>
                                    <th>Asset Code</th>
                                    <th>Description</th>
                                    <th>Location</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btnNew" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<div class="m-content">
    <div class="row d-flex flex-row align-items-end position-relative" style="z-index: 1;">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4>
                DASHBOARD
                <small class="text-muted">Quality Management System</small>
            </h4>
        </div>
    </div>
    <div class="row mt-3 mb-3">
        <div class="col-lg-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-line-graph"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Active Document by Company
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div id="companyChart" style="width: 100%;height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-7">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-graph"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Active Document by Category
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div id="categoryChart" style="width: 100%;height: 500px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-event-calendar-symbol"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Recently Added
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
                        <table class="table table-striped table-bordered" id="table-document" width="100%">
                            <thead>
                                <tr>
                                    <th>added date</th>
                                    <th>Title</th>
                                    <th>Document No.</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTempView" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div id="modalTempContainer" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="view-title">View Document</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="view-pdf" title="PDF" src="" style="width: 100%; height: 80vh;"></iframe>
            </div>
        </div>
    </div>
</div>
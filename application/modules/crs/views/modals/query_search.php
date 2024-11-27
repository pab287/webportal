<div class="modal" id="query_search">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title text-uppercase">Advance Search</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
                <div class="m-portlet__body">
                    <form id="crs_query_form" onsubmit="event.preventDefault(); generateReport(this)">
                        <label for="">FILTER</label>
                        <div id="query-builder" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0"></div>

                        <div class="row mt-3">
                            <div class="col-xl-3">
                                <div class="form-group">
                                    <label for="">Template</label>
                                    <select name="template" class="form-control" id="template">
                                        <option value=""></option>
                                    </select>
                                </div>

                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-primary btnView" type="button"
                                            onclick="CRSloadTemplate()">Load
                                    </button>
                                    <button class="btn btn-success btnNew d-flex" type="button"
                                            onclick="openAddFieldTemplateDialog()">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                    <button class="btn btn-info btnEdit d-flex" type="button"
                                            onclick="openEditFieldTemplateDialog()">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xl-5">
                                <div class="form-group">
                                    <label for="">Fields</label>
                                    <select name="field[]" class="form-control" id="field" multiple="multiple"
                                            data-validation="required">
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div class="form-group">
                                    <label for="">Order Field</label>
                                    <select name="order_field" class="form-control" id="order_field">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div class="form-group">
                                    <label for="">Order By</label>
                                    <select name="order_by" class="form-control" id="order_by">
                                        <option value=""></option>
                                        <option value="asc">ASCENDING</option>
                                        <option value="desc">DESCENDING</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                       
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-xl-12 text-right">
                                
                                    <button id="generate" class="btn btn-primary">Generate
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="m-portlet">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                    <span class="m-portlet__head-icon">
                                        <i class="flaticon-list-3"></i>
                                    </span>
                                <h3 class="m-portlet__head-text">
                                    APPLICANT LIST
                                </h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                        </div>
                    </div>
                    <div class="m-portlet__body pt-2 pb-3">
                        <div id="empty-table-message"></div>
                        <div class="table-responsive-sm mt-4 mb-4"
                            id="generated-data-container">
                            <div class="empty-table-message">
                                <div class="m-alert m-alert--outline m-alert--outline-2x alert alert-success alert-dismissible fade show mb-0"
                                    role="alert">
                                    <strong>To Generate :</strong>
                                    <span>Select Field(s) or Load Template. (FILTER is <strong>OPTIONAL</strong>).</span>
                                </div>
                            </div>
                            <table id="table-generated-report" class="table table-hover table-bordered">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal fade document-modal-container"
                data-keyboard="false" data-backdrop="static"
                modal-exempt-custom tabindex="-1"
                role="dialog"></div>

        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>
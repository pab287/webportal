<style>
    .rules-group-container {
        width: 100%;
        background-color: #fbfbfb !important;
        border: 1px solid #eaeaea !important;
    }

    .rules-group-container .btn-group .btn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }

    .table.dataTable thead .sorting_asc:before, table.dataTable thead .sorting_desc:after {
        opacity: 0 !important;
    }

    /* hide sorting in first column */
    table.dataTable thead .sorting:before,
    table.dataTable thead .sorting:after,
    table.dataTable thead .sorting_asc:before,
    table.dataTable thead .sorting_asc:after,
    table.dataTable thead .sorting_desc:before,
    table.dataTable thead .sorting_desc:after,
    table.dataTable thead .sorting_asc_disabled:before,
    table.dataTable thead .sorting_asc_disabled:after,
    table.dataTable thead .sorting_desc_disabled:before,
    table.dataTable thead .sorting_desc_disabled:after {
        opacity: 0 !important;
    }
</style>

<div class="m-content">
    <div class="row">
        <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                SELECTED FIELDS
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body pt-2 pb-3">
                    <div class="empty-field-selection">
                        <p class="lead text-muted mt-3">Please select at least one field.</p>
                    </div>
                    <div class="m-widget4">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-10 col-lg-10 col-md-9 col-sm-12">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-line-graph"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                REPORT CREATOR
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body">
                    <form onsubmit="event.preventDefault(); generateReport(this)">
                        <label for="">FILTER</label>
                        <div id="query-builder" class="col-xs-12 col-sm-12 col-md-12"></div>

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
                                            onclick="loadTemplate()">Load
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
                                <div class="offset-xl-6 col-xl-6 text-right">
                                    <button type="submit"
                                            class="btn btn-primary">Generate
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                                <span class="m-portlet__head-icon">
                                    <i class="flaticon-list-3"></i>
                                </span>
                            <h3 class="m-portlet__head-text">
                                EMPLOYEE LIST
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
        </div>
    </div>

    <div class="modal fade document-modal-container"
         data-keyboard="false" data-backdrop="static"
         modal-exempt-custom tabindex="-1"
         role="dialog"></div>
</div>
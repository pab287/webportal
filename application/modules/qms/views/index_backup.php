<div class="m-content">
    <div class="row d-flex flex-row align-items-end position-relative" style="z-index: 1;">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4>
                DASHBOARD
                <small class="text-muted">Quality MANAGEMENT System</small>
            </h4>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-lg-3">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-filter"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Filter
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="">COMPANY </label>
                            <select id="policy-company" class="form-control">
                                <option>Select an Option</option>
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="">DEPARTMENT </label>
                            <select id="policy-department" class="form-control">
                                <option>Select an Option</option>
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="">AUTHOR</label>
                            <select id="policy-author" class="form-control">
                                <option>Select an Option</option>
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="">CATEGORY</label>
                            <select id="policy-category" class="form-control">
                                <option>Select an Option</option>
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="">TITLE <small>( OPTIONAL )</small></label>
                            <textarea type="text" id="policy-title" class="form-control" rows="4" cols="50"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-right">
                                <button type="button" onclick="clear_filter()" class="m-btn btn btn-warning btnCancel btn-clear text-white">Clear Filter</button>
                                <button type="button" onclick="submit_filter()" class="m-btn btn btn-success btnAdvance_search btn-submit">GO</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__body">
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-8">
                            <div class="btnContainer">
                                <button class="btn btn-default" id="list"><span class="la la-reorder"></span> List</button>
                                <button class="btn btn-default btn-primary active" id="grid"><span class="la la-th-large"></span> Grid</button>
                            </div>
                        </div>
                        <div class="col-md-4 m--align-right">
                            <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                <span class="m-input-icon__icon m-input-icon__icon--left">
                                    <span> <i class="la la-search"></i> </span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="columns">
                                <table width="100%" id="table-policy" class="is-grid">
                                    <thead>
                                        <th>Date Added</th>
                                        <th>Document</th>
                                        <th>Description</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTempContent" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div id="modalTempContainer" class="modal-content" >
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Temp Title</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">test</div>
            </div>
        </div>
    </div>
</div>
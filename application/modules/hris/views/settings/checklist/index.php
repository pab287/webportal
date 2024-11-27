<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Pre-Employment Checklist
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                                data-dropdown-toggle="hover" aria-expanded="true">
                                <a href="<?=base_url('hris/settings/archived') ?>" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl btnView">
                                    Archives
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-12">
                                        <a href="javascript:void(0);"
                                            class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew" id="btnNewChecklist">
                                            <span>
                                                <i class="la la-plus"></i>
                                                <span>
                                                    New Checklist
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span> <i class="la la-search"></i> </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
                        <table class="table table-striped table-bordered" id="table-checklist" width="100%">
                            <thead>
                                <tr>
                                    <th>Checklist</th>
                                    <th>Description</th>
                                    <th>added date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTempContent" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div id="modalTempContainer" class="modal-content">
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
    <div class="modal fade" id="modalRemoveChecklist" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div id="modalTempContainer" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-trash mr-2"></i>Archive Checklist</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="checklistId" value="0"/>
                    <p>Are you sure you want to archive this checklist on the list?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger btnClose" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary btn-submit btnDelete btnRemoveCurrentChecklist">Yes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
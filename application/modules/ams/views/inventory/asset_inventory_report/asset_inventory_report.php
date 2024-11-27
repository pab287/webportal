<style>
    .m-checkbox span {
        height: 16px;
        width: 16px;
    }

    .m-checkbox span:after {
        height: 8px;
        width: 3px;
    }
</style>

<div class="m-content">
    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                SELECT FILE
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <form>
                        <input type="hidden" name="csrf_token"
                               value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="form-group m-form__group row">
                            <div class="col-12">
                                <div class="form-group m-form__group mb-1">
                                    <div class="custom-file">
                                        <input type="file" name="files[]" id="fileupload" class="custom-file-input">
                                        <span class="custom-file-control" id="file_append"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div id="progress" class="progress m-progress--sm"
                                     style="background-color:#ffffff;">
                                    <div class="progress-bar m--bg-primary"></div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <div class="dropdown flex-grow-0 flex-shrink-0">
                                <button class="btn btn-success dropdown-toggle btnSave" type="button"
                                        id="inventory-action-dropdown" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" style="box-shadow: none;">
                                    Inventory Actions
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-end">
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="inventoryCheck('verified')">VERIFIED</a>
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="inventoryCheck('recovered')">RECOVERED</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">ASSET INVENTORY LIST</h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div id="files" class="files"></div>
                    <div class="m_datatable m-datatable m-datatable--default
                                m-datatable--loaded m-datatable--scroll table-responsive-sm">
                        <form id="frm-asset-list">
                            <table class="table table-striped table-bordered"
                                   id="load-asset" width="100%">
                                <thead>
                                <tr>
                                    <th>
                                        <label class="m-checkbox d-inline"><input type="checkbox" id="select-all"><span></span></label>
                                    </th>
                                    <th>ASSET</th>
                                    <th>DESCRIPTION</th>
                                    <th>LAST UPDATED</th>
                                    <th>STATUS</th>
                                    <th>REMARKS</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="asset-not-found-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ASSET CODE NOT FOUND IN THE SYSTEM</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Assumenda atque blanditiis consequatur doloremque eius ipsum iusto laborum
                libero maiores, minus nam nesciunt optio provident quis sit soluta vitae voluptatem voluptates?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary m-btn m-btn--icon btnExport" onclick="exportList()">
                    <span><i class="fa fa-download"></i><span>Export</span></span>
                </button>
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="duplicated-asset-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">DUPLICATE ASSET CODE ON UPLOADED TEXT FILE</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Assumenda atque blanditiis consequatur doloremque eius ipsum iusto laborum
                libero maiores, minus nam nesciunt optio provident quis sit soluta vitae voluptatem voluptates?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
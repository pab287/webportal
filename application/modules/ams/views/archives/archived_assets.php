<style type="text/css">
    .m-checkbox > span:after {
        width: 4px;
        height: 8px;
    }
</style>
<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                <?= $title ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="row">
                        <?php
                            $goback = null;
                            if ($type === "mother") {
                                $goback = base_url("ams/assets/fixed_masterfile");
                            } elseif ($type === "component") {
                                $goback = base_url("ams/assets/asset_components_masterfile");
                            }
                        ?>

                        <div class="m--margin-left-15 d-flex align-items-end"
                             style="margin-bottom: 13px;">
                            <button data-toggle="m-tooltip"
                                    data-original-title="Restore Selected"
                                    data-skin="dark"
                                     type="button"
                                    data-delay='{"show": 300}'
                                    class="btn btn-primary m-btn m-btn--icon m-btn--icon-only m--margin-right-5 btn-restore-multiple btnMass_restore"
                                    onclick="confirmRestoreSelections()" disabled>
                                <i class="la la-reply"></i>
                            </button>
                            <button data-toggle="m-tooltip"
                                    data-original-title="Permanently Delete Selected"
                                    data-skin="dark"
                                    data-delay='{"show": 300}'
                                    type="button"
                                    class="btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-delete-multiple btnMass_delete"
                                    onclick="confirmDeleteSelections()" disabled hidden>
                                <i class="la la-trash-o"></i>
                            </button>
                        </div>

                        <?php
                            if ($type === "mother" || $type === "component") { ?>
                                <div class="col-xl-2">
                                    <a href="<?= $goback ?>"
                                       class="btn btn-outline-metal m-btn m-btn--icon m-btn--custom m-btn--pill m-btn--hover-primary btnClose"
                                       title="Return to Fixed Asset Masterfile">
                                        <span>
                                            <i class="la la-arrow-left"></i>
                                            <span>Back</span>
                                        </span>
                                    </a>
                                </div>
                            <?php } else { ?>
                                <div class="col-xl-2">
                                    <div class="form-group m-form__group">
                                        <label for="filterAssetsByType">Filter by Type</label>
                                        <select class="form-control m-input" id="filterAssetsByType">
                                            <option value="all">All Types</option>
                                            <option value="mother">Mother Asset</option>
                                            <option value="component">Components</option>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?>
                        <div class="col"></div>
                        <div class="col-xl-3" style="flex-direction: column; justify-content: flex-end; display: flex;">
                            <div class="form-group m-form__group">
                                <div class="m-input-icon m-input-icon--left">
                                    <span class="m-input-icon__icon m-input-icon__icon--left" style="z-index: 5;">
                                        <span>
                                            <i class="la la-binoculars"></i>
                                        </span>
                                    </span>
                                    <div class="input-group">
                                        <input type="search" class="form-control" placeholder="Search Here..."
                                               style="height: auto;" id="search-archived-assets">
                                        <span class="input-group-btn">
                                            <button class="btn btn-secondary" title="Clear Search"
                                                    data-placement="bottom"
                                                    style="border-color: #cdcdcd;" onclick="clearFilter()">
                                                <i class="la la-close"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                
                            </div>
                            
                        </div>
                        <div class="col-xl-1" style="flex-direction: column; justify-content: flex-end; display: flex;">
                            <div class="form-group m-form__group">
                                <div class="btn-group">
                                    <span data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <button id="btn-export-fixed-assets" type="button"
                                                data-toggle="m-tooltip" data-original-title="Export"
                                                data-skin="dark"
                                                data-delay='{"show": 300}'
                                                class="m-btn btn btn-success dropdown-toggle btnExport">
                                            <i class="la la-external-link"></i>
                                        </button>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right"
                                            aria-labelledby="btnGroupDrop1"
                                            x-placement="bottom-start">
                                        <a href="javascript:void(0);" class="dropdown-item datatable-pdf"
                                            id="ExportPDF"
                                            onclick="exportAs('pdf');">
                                            <i class="m-nav__link-icon fa fa-file-pdf-o"></i>
                                            <span class="m-nav__link-text">PDF</span>
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item datatable-excel"
                                            id="ExportExcel"
                                            onclick="exportAs('excel');">
                                            <i class="m-nav__link-icon fa fa-file-excel-o"></i>
                                            <span class="m-nav__link-text">EXCEL</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--begin: Datatable -->
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll m--margin-top-20 table-responsive-sm">
                        <table class="table table-striped table-bordered" id="table-archived-assets" width="100%">
                            <thead>
                            <tr>
                                <th class="d-flex justify-content-center">
                                    <label class='m-checkbox'>
                                        <input type='checkbox' class='form-control' id="select-all-archived-page">
                                        <span></span>
                                    </label>
                                </th>
                                <th>Status</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Archive Remarks</th>
                                <th>Location</th>
                                <th>Category</th>
                                <th>Station</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <!--end: Datatable -->
                </div>
            </div>
            <!--end::Portlet-->
        </div>
    </div>
    <div class="modal fade document-modal-container" modal-exempt-custom data-backdrop="static" data-keyboard="false"
         tabindex="-1" role="dialog"></div>
    <input type="hidden" value="<?= $type ?>" id="archived_assets_type">
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="confirm-delete-multiple">
    <form id="frm-confirm-delete-multiple" action="<?= base_url('ams/assets/delete_archived_assets_multiple') ?>">
        <input type="hidden" name="csrf_token"
               value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="multiple_id" class="multiple_id">

        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Confirm Permanent Delete
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div style="text-transform: none; font-size: 20px;">
                        Are you sure to permanently delete selected assets?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnNew">
                        Yes
                    </button>
                    <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                        No
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="confirm-restore-multiple">
    <form id="frm-confirm-restore-multiple" action="<?= base_url('ams/assets/restore_archived_assets_multiple') ?>">
        <input type="hidden" name="csrf_token"
               value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="multiple_id" class="multiple_id">

        <div class="modal-dialog" role="dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Confirm Restore
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div style="text-transform: none; font-size: 20px;">
                        Are you sure to restore selected assets?
                    </div>

                    <div class="mt-3">
                        <label for="" class="required">Status </label>
                        <select name="status" id="restore-status" class="form-control" data-validation="required">
                            <option value=""></option>
                            <option value="brandnew">Brand New</option>
                            <option value="operational">Operational</option>
                            <option value="repair">Repair</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnNew">
                        Yes
                    </button>
                    <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                        No
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
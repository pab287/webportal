<style>
    #table-fixed-asset > thead > tr > th:first-child:after,
    #table-fixed-asset > thead > tr > th:first-child:before {
        content: "" !important;
    }
/* 
    label.m-checkbox {
        cursor: pointer;
    } */
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
                                Fixed Assets Masterfile
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <a href="<?= base_url('ams/assets/archive/?type=mother') ?>" class="custom-btn-link">
                            <span class="m--font-bolder">Go to Archive</span>
                        </a>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-12">
                                        <a href="<?php echo site_url("ams/assets/new_fixed_asset"); ?>"
                                           class="btn btn-accent m-btn m-btn--icon m-btn--pill mb-1 btnNew">
                                            <span>
                                                <i class="fa fa-plus"></i>
                                                <span>
                                                    NEW ASSET
                                                </span>
                                            </span>
                                        </a>

                                        <div class="dropdown d-inline">
                                            <button class="btn btn-primary m-btn m-btn--icon m-btn--pill mb-1 btnAdvance_search"
                                                    type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <span><i class="fa fa-search"></i><span>Search</span>
                                                    <span class="dropdown-toggle"></span></span>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                                 x-placement="bottom-start"
                                                 style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <a class="dropdown-item" href="#" data-toggle="modal"
                                                   data-target="#modal-accountability-search">
                                                    <i class="fa fa-user"></i>
                                                    Accountability Search
                                                </a>
                                                <a class="dropdown-item" data-toggle="modal"
                                                   data-target="#modal-advance-search" href="#">
                                                    <i class="fa fa-search-plus"></i>
                                                    Advanced Search
                                                </a>
                                            </div>
                                        </div>

                                        <div class="dropdown d-inline">
                                            <button class="btn btn-brand m-btn m-btn--icon m-btn--pill mb-1 btnAdvance_search"
                                                    type="button" id="dropdownActions" data-toggle="dropdown"
                                                    aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <span><span>Action</span>
                                                    <span class="dropdown-toggle"></span></span>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownActions"
                                                 x-placement="bottom-start"
                                                 style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <a class="dropdown-item" data-toggle="modal"
                                                   data-target="#modal-print-barcode" href="#" id="generate-barcode">
                                                    <i class="fa fa-barcode"></i>
                                                    GENERATE BARCODE
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right d-flex flex-row">
                                <div class="form-group m-form__group pb-0 flex-grow-1 flex-shrink-0">
                                    <div class="m-input-icon m-input-icon--left">
                                    <span class="m-input-icon__icon m-input-icon__icon--left" style="z-index: 5;">
                                        <span>
                                            <i class="la la-binoculars"></i>
                                        </span>
                                    </span>
                                        <div class="input-group">
                                            <input type="search" class="form-control" placeholder="Search Here..."
                                                   style="height: auto;" id="generalSearch" autocomplete="off">
                                            <span class="input-group-btn">
                                            <button class="btn btn-secondary"
                                                    style="border-color: #cdcdcd;"
                                                    data-toggle="m-tooltip" data-original-title="Clear Search"
                                                    data-placement="bottom" data-delay='{"show": 300}'
                                                    data-skin="dark"
                                                    onclick="clearSearch()">
                                                <i class="la la-close"></i>
                                            </button>
                                        </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-group ml-3" role="group"
                                     aria-label="Button group with nested dropdown">
                                    <div class="btn-group" role="group">
                                        <span data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <button type="button"
                                                    data-toggle="m-tooltip" data-original-title="Show/Hide Columns"
                                                    data-skin="dark"
                                                    data-delay='{"show": 300}'
                                                    class="m-btn btn btn-success dropdown-toggle btnAdvance_search">
                                                <i class="fa fa-th"></i>
                                            </button>
                                        </span>
                                        <ul class="dropdown-menu dropdown-menu-right"
                                            id="column-options"
                                            aria-labelledby="btnGroupDrop1"
                                            x-placement="bottom-start">
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" checked="checked"
                                                           oninput="showOrHideColumn(2, this)">
                                                    CODE
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" checked="checked"
                                                           oninput="showOrHideColumn(3, this)">
                                                    NAME
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" checked="checked"
                                                           oninput="showOrHideColumn(4, this)">
                                                    DESCRIPTION
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" checked="checked"
                                                           oninput="showOrHideColumn(5, this)">
                                                    LOCATION
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" oninput="showOrHideColumn(6, this)">
                                                    COMPANY
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" oninput="showOrHideColumn(7, this)">
                                                    DEPARTMENT
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" checked="checked"
                                                           oninput="showOrHideColumn(8, this)">
                                                    CATEGORY
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" checked="checked"
                                                           oninput="showOrHideColumn(9, this)">
                                                    STATION
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" oninput="showOrHideColumn(10, this)">
                                                    BRAND
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" oninput="showOrHideColumn(11, this)">
                                                    SERIAL
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" oninput="showOrHideColumn(12, this)">
                                                    PO #
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" checked="checked" oninput="showOrHideColumn(13, this)">
                                                    CHECK NO
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" oninput="showOrHideColumn(14, this)">
                                                    MODEL
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" oninput="showOrHideColumn(15, this)">
                                                    STATUS
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" oninput="showOrHideColumn(16, this)">
                                                    DATE PURCHASED
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" oninput="showOrHideColumn(17, this)">
                                                    DATE CREATED
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" oninput="showOrHideColumn(18, this)">
                                                    ACCOUNTED TO
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" oninput="showOrHideColumn(19, this)">
                                                    PRICE
                                                    <span></span>
                                                </label>
                                            </li>
                                            <li class="dropdown-item pt-1 pb-1">
                                                <label class="m-checkbox mb-0">
                                                    <input type="checkbox" oninput="showOrHideColumn(20, this)">
                                                    STOCK CODE
                                                    <span></span>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="btn-group ml-1" role="group">
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

                                <div class="m-separator m-separator--dashed d-xl-none"></div>
                            </div>
                        </div>
                    </div>
                    <!--begin: Datatable -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="table-fixed-asset" width="100%">
                            <thead>
                            <tr>
                                <th>
                                    <label class="m-checkbox m-checkbox--state-primary">
                                        <input type="checkbox" id="selectall">
                                        <span></span>
                                    </label>
                                </th>
                                <th class="no-sort"></th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Location</th>
                                <th>Company</th>
                                <th>Department</th>
                                <th>Category</th>
                                <th>Station</th>
                                <th>Brand</th>
                                <th>Serial</th>
                                <th>PO #</th>
                                <th>Check No</th>
                                <th>Model</th>
                                <th>Status</th>
                                <th>Date Purchased</th>
                                <th>Date Created</th>
                                <th>Accounted To</th>
                                <th>Price</th>
                                <th>Stock Code</th>
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

    <div class="modal fade document-modal-container"
         data-keyboard="false" data-backdrop="static"
         modal-exempt-custom tabindex="-1"
         role="dialog"></div>

    <div class="modal fade cant-archive-alert-dialog" role="dialog">
        <div class="modal-dialog" role="dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Oops! Unable to archive.
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btnClose"
                            data-dismiss="modal">
                        Ok, I Understand.
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade archive-remarks" role="dialog">
        <div class="modal-dialog" role="dialog">
            <form>
                <input type="hidden" name="csrf_token"
                       value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><span class="m--font-bolder">Archive</span> Asset Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                ×
                            </span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group m-form__group">
                                <label>Status</label>
                                <select class="form-control m-input" id="select2-status" name="status" data-validation="required">
                                    <option></option>
                                    <option value="damage">DAMAGED</option>
                                    <option value="destructed">DESTRUCTED</option>
                                    <option value="lost">LOST</option>
                                    <option value="sold">SOLD</option>
                                    <option value="junk">JUNK</option>
                                    <option value="others">OTHERS</option>
                                </select>
                        </div>
                        <div class="form-group m-form__group">
                            <label for="archive-remarks-text-area">Please leave a remark</label>
                            <textarea name="archive_remark" class="form-control m-input" id="archive-remarks-text-area"
                                      rows="3"
                                      data-validation="required"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btnArchive" type="submit">Archive</button>
                        <button class="btn btn-danger btnClose" type="button" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-accountability-search">
    <form id="frm-accountability-search">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Accountability Search</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Employee Name</label>
                        <select id="emp_name" name="emp_name"></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="clear_acc_search()" class="btn btn-danger btnAdvance_search mr-auto">
                        Clear
                    </button>
                    <button type="button" id="accountability_search" class="btn btn-primary btnAdvance_search">Search
                    </button>
                    <button type="button" class="btn btn-secondary btnAdvance_search" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-advance-search">
    <form id="frm-advance-search">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Advanced Search</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>
                            Asset Code
                        </label>
                        <input type="text" class="form-control" id="asset_code" autocomplete="off">
                    </div>
                    <div class="form-group pt-3">
                        <label>
                            Asset Name
                        </label>
                        <input type="text" class="form-control" id="asset_name" autocomplete="off">
                    </div>
                    <div class="form-group pt-3">
                        <label>
                            Description
                        </label>
                        <input type="text" class="form-control" id="asset_desc" autocomplete="off">
                    </div>
                    <div class="form-group pt-3">
                        <label>
                            Serial #
                        </label>
                        <input type="text" class="form-control" id="serialno" autocomplete="off">
                    </div>
                    <div class="form-group pt-3">
                        <label>
                            PO #
                        </label>
                        <input type="text" class="form-control" id="po_no" autocomplete="off">
                    </div>
                    <div class="form-group pt-3">
                        <label>
                            Date Created
                        </label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="la la-calendar"></i> FROM
                            </span>
                            <input type="text" class="form-control dt-picker" id="date_created_from" autocomplete="off">
                            <span class="input-group-addon">
                                <i class="la la-calendar"></i> TO
                            </span>
                            <input type="text" class="form-control dt-picker" id="date_created_to" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group pt-3">
                        <label>
                            Location
                        </label>
                        <select id="location" name="location">

                        </select>
                    </div>
                    <div class="form-group pt-3">
                        <label>
                            Category
                        </label>
                        <select id="cat" name="cat">

                        </select>
                    </div>
                    <div class="form-group pt-3">
                        <label>
                            Station
                        </label>
                        <select id="station" name="station">

                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="clear_adv_search()" class="btn btn-danger btnAdvance_search mr-auto">
                        Clear
                    </button>
                    <button type="button" id="advanced_search" class="btn btn-primary btnAdvance_search">Search</button>
                    <button type="button" class="btn btn-secondary btnAdvance_search" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
    $this->load->view("modals/print_barcode");
?>
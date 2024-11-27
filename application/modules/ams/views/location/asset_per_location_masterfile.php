<style>
    #table-fixed-asset > thead > tr > th:first-child:after,
    #table-fixed-asset > thead > tr > th:first-child:before,
    #table-vehicles > thead > tr > th:first-child:after,
    #table-vehicles > thead > tr > th:first-child:before {
        content: "" !important;
    }
</style>

<div class="m-content">
    <div class="m-portlet m-portlet--tabs">
        <div class="m-portlet__head">
            <div class="m-portlet__head-tools">
                <ul class="nav nav-tabs m-tabs-line m-tabs-line--info m-tabs-line--2x" role="tablist">
                    <li class="nav-item m-tabs__item">
                        <a id="released_assets" onclick="assetsTab(event, 'assets')"
                           class="nav-link m-tabs__link active" data-toggle="tab"
                           href="#m_portlet_base_demo_1_tab_content" role="tab">
                            <i class="flaticon-open-box"></i>
                            Assets & Components
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a id="returned_assets" onclick="assetsTab(event, 'vehicles')" class="nav-link m-tabs__link"
                           data-toggle="tab" href="#m_portlet_base_demo_1_tab_content" role="tab">
                            <i class="flaticon-truck"></i>
                            Vehicles & Equipment
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
                            <div class="col-md-4">
                                <button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search"
                                        type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    <span><i class="fa fa-search"></i><span>Filter</span><span
                                                class="dropdown-toggle"></span></span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                     x-placement="bottom-start"
                                     style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                                    <a class="dropdown-item" href="#" data-toggle="modal"
                                       data-target="#modal-accountability-search">
                                        Accountability Search
                                    </a>
                                    <!--<a class="dropdown-item" data-toggle="modal" data-target="#modal-advance-search" href="#">
                                        Advanced Search
                                    </a>-->
                                    <a class="dropdown-item" data-toggle="modal" data-target="#modal-location-search"
                                       href="#">
                                        Location Search
                                    </a>
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
                                           style="height: auto;" id="generalSearch">
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
                            <div class="btn-group column-options-assets" role="group">
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
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" checked="checked"
                                                   oninput="showOrHideColumn(1, this, 'tblAssets')">
                                            CODE
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" checked="checked"
                                                   oninput="showOrHideColumn(2, this, 'tblAssets')">
                                            NAME
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(3, this, 'tblAssets')">
                                            DESCRIPTION
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" checked="checked"
                                                   oninput="showOrHideColumn(4, this, 'tblAssets')">
                                            LOCATION
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(5, this, 'tblAssets')">
                                            COMPANY
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(6, this, 'tblAssets')">
                                            DEPARTMENT
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" checked="checked"
                                                   oninput="showOrHideColumn(7, this, 'tblAssets')">
                                            CATEGORY
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox"
                                                   oninput="showOrHideColumn(8, this, 'tblAssets')">
                                            STATION
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(9, this, 'tblAssets')">
                                            BRAND
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(10, this, 'tblAssets')">
                                            SERIAL
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(11, this, 'tblAssets')">
                                            MODEL
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(12, this, 'tblAssets')">
                                            STATUS
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(13, this, 'tblAssets')">
                                            DATE PURCHASED
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(14, this, 'tblAssets')"
                                                   checked="checked">
                                            ACCOUNTED TO
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(15, this, 'tblAssets')">
                                            PO #
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(16, this, 'tblAssets')"
                                                   checked="checked">
                                            Price
                                            <span></span>
                                        </label>
                                    </li>
                                </ul>
                            </div>

                            <div class="btn-group column-options-vehicles m--hide" role="group">
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
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" checked="checked"
                                                   oninput="showOrHideColumn(1, this, 'tblVehicle')">
                                            CODE
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" checked="checked"
                                                   oninput="showOrHideColumn(2, this, 'tblVehicle')">
                                            NAME
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox"
                                                   oninput="showOrHideColumn(3, this, 'tblVehicle')">
                                            DESCRIPTION
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" checked="checked"
                                                   oninput="showOrHideColumn(4, this, 'tblVehicle')">
                                            LOCATION
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(5, this, 'tblVehicle')">
                                            COMPANY
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" checked="checked"
                                                   oninput="showOrHideColumn(6, this, 'tblVehicle')">
                                            CATEGORY
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(7, this, 'tblVehicle')">
                                            BRAND
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(8, this, 'tblVehicle')">
                                            SERIAL
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(9, this, 'tblVehicle')">
                                            MODEL
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(10, this, 'tblVehicle')">
                                            PLATE NO
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(11, this, 'tblVehicle')">
                                            STATUS
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(12, this, 'tblVehicle')">
                                            DATE PURCHASED
                                            <span></span>
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="m-checkbox mb-0">
                                            <input type="checkbox" oninput="showOrHideColumn(13, this, 'tblVehicle')"
                                                   checked="checked">
                                            ACCOUNTED TO
                                            <span></span>
                                        </label>
                                    </li>
                                </ul>
                            </div>

                            <div class="m-btn-group btn-group ml-1" role="group">
                                <span data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <button id="btn-export-fixed-assets" type="button"
                                            data-toggle="m-tooltip" data-original-title="Export" data-skin="dark"
                                            data-delay='{"show": 300}'
                                            class="btn btnExport btn-success m-btn dropdown-toggle">
                                        <i class="la la-external-link"></i>
                                    </button>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1"
                                     x-placement="bottom-start">
                                    <a href="javascript:void(0);" class="dropdown-item datatable-pdf" id="ExportPDF"
                                       onclick="exportAs('pdf');">
                                        <i class="m-nav__link-icon fa fa-file-pdf-o"></i>
                                        <span class="m-nav__link-text">PDF</span>
                                    </a>
                                    <a href="javascript:void(0);" class="dropdown-item datatable-excel" id="ExportExcel"
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
            <div class="tab-content">
                <div id="loader">
                </div>

                <input type="hidden" id="type" value="asset">
                <div class="tab-pane active" id="assets" role="tabpanel">
                    <div class="table-responsive table-assets-container">
                        <table class="table table-striped table-bordered" id="table-fixed-asset" width="100%">
                            <thead>
                            <tr>
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
                                <th>Model</th>
                                <th>Status</th>
                                <th>Date Purchased</th>
                                <th>Accounted To</th>
                                <th>PO #</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="vehicles" role="tabpanel">
                    <div class="table-responsive table-vehicles-container">
                        <table class="table table-striped table-bordered" id="table-vehicles" width="100%">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Location</th>
                                <th>Company</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Serial</th>
                                <th>Model</th>
                                <th>Plate No</th>
                                <th>Status</th>
                                <th>Date Purchased</th>
                                <th>Accounted To</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--end: Datatable -->
        </div>
        <!--end::Portlet-->
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
                    <div class="form-group pt-3">
                        <label>
                            Employee Name
                        </label>
                        <select id="emp_name" name="emp_name">

                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="clear_acc_search()" class="btn btn-danger btnAdvance_search mr-auto">
                        Clear
                    </button>
                    <button type="button" onclick="accountabilitySearch()" class="btn btn-primary btnAdvance_search">
                        Search
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
                    <div class="form-group pt-3">
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
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="clear_adv_search()" class="btn btn-danger btnAdvance_search mr-auto">
                        Clear
                    </button>
                    <button type="button" onclick="advancedSearch()" class="btn btn-primary btnAdvance_search">Search
                    </button>
                    <button type="button" class="btn btn-secondary btnAdvance_search" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-location-search">
    <form id="frm-location-search">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Location Search</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group pt-3">
                        <label>
                            Location
                        </label>
                        <select id="area" name="area">

                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="clear_loc_search()" class="btn btn-danger btnAdvance_search mr-auto">
                        Clear
                    </button>
                    <button type="button" onclick="locationSearch()" class="btn btn-primary btnAdvance_search">Search
                    </button>
                    <button type="button" class="btn btn-secondary btnAdvance_search" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-accountability-list">
    <form id="frm-accountability-list">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Accountability History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="m_datatable m-datatable m-datatable--default
                                m-datatable--loaded m-datatable--scroll table-responsive-sm">
                        <table class="table table-striped table-bordered" id="table-accountability-list" width="100%">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reference No.</th>
                                <th>Issued To</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btnAdvance_search" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
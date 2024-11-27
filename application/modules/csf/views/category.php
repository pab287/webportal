<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Category masterfile
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">

                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                        <a class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white"
                                           onclick="open_category()">
                                            <span>
                                                <i class="la la-plus"></i>
                                                <span>
                                                    New
                                                </span>
                                            </span>
                                        </a>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="dropdown">
                                            <button class="btn btn-brand dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start"
                                                 style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <a class="dropdown-item" href="#">
                                                    <i class="la la-search-plus"></i> Query Builder
                                                </a>
                                                <a class="dropdown-item" href="#">
                                                    <i class="la la-barcode"></i> Generate Barcode
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="#">
                                                    <i class="la la-file-archive-o"></i> Generate Barcode
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                    </span>
                                </div>
                                <div class="m-separator m-separator--dashed d-xl-none"></div>
                            </div>
                        </div>
                        <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                            <div class="row align-items-center">
                                <div class="col-xl-12 order-2 order-xl-1">
                                    <div style='float:right !important;' class="form-group m-form__group row">
                                        <div class="col-md-4">
                                            <div class="m-input-icon m-input-icon--left">
                                                <button class="btn btn-secondary dropdown-toggle" aria-expanded="true"><i class="la la-table"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="m-input-icon m-input-icon--left">
                                                <button class="btn btn-secondary dropdown-toggle" aria-expanded="true"><i
                                                            class="la la-external-link"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="colms-12">
                                        <div class="btn-group m-btn-group" role="group" aria-label="...">
                                            <button type="button" class="btn btn-primary" id="reload_dtTbl">
                                                <i class="la la-refresh"></i>
                                            </button>
                                            <div class="m-btn-group btn-group" role="group">
                                                <button id="tbl-btn-share" type="button" class="btn btn-success m-btn dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="la la-table"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" x-placement="bottom-start"
                                                     style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <a class="dropdown-item" href="#">
                                                        Dropdown link
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        Dropdown link
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        Dropdown link
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        Dropdown link
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="m-btn-group btn-group" role="group">
                                                <button id="tbl-btn-share" type="button" class="btn btn-info m-btn m-btn--pill-last dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="la la-share"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop2" x-placement="bottom-start"
                                                     style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <a class="dropdown-item" href="#">
                                                        Excel
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--begin: Datatable -->
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                        <table class="table table-striped table-bordered" id="table-category" width="100%">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <!--end: Datatable -->
                    <div class="modal fade" id="modal_form_delete" role="dialog">
                        <form action="#" id="form_delete" class="form-horizontal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h3 class="modal-title"></h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                    aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body form">
                                        <input type="hidden" value="" name="delete_id"/>
                                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                        <p class="mb-0 m--regular-font-size-lg1">Are you sure you want to Delete data?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" onclick="delete_category()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnNew">
                                            Yes
                                        </button>
                                        <button type="button" class="btn btn-danger m-btn m-btn--custom m-btn--icon  btnNew" data-dismiss="modal">
                                            No
                                        </button>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal fade" id="modal_form_category" role="dialog">
                    <div class="modal-dialog">
                        <form action="#" id="form_category" class="form-horizontal">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title"></h3>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body form">
                                    <input type="hidden" value="" name="id"/>
                                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

                                    <div class="form-group">
                                        <label class="control-label col-md-2">Name</label>
                                        <div class="col-md-12">
                                            <input type="text" name="name" class="form-control" data-validation="required">
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" id="btnSave" onclick="save_category()"
                                            class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnNew">Save
                                    </button>
                                    <button type="button" class="btn btn-danger m-btn m-btn--custom m-btn--icon  btnNew" data-dismiss="modal">Cancel
                                    </button>
                                </div>
                            </div><!-- /.modal-content -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
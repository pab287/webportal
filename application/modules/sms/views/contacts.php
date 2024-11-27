<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Contacts
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
                                           onclick="open_contact()">
										    <span>
											<i class="la la-plus"></i>
											<span>
												New
											</span>
										</span>
                                        </a>
                                        <a class="btn btn-warning m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white"
                                           onclick="open_import()">
                                            <span>
                                                <i class="la la-upload"></i>
                                                <span>
                                                    Import
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
                        <div class="m-form m-form--label-align-right m--margin-top-20">
                            <div class="row align-items-center">
                                <div class="col-xl-8 order-2 order-xl-1">
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
                        <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                            <table class="table table-striped table-bordered" id="table-contacts" width="100%">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--add-->
    <div class="modal fade" id="modal_form_contacts" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="#" id="form_contacts" class="form-horizontal">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="modal-body form">
                        <input type="hidden" value="" name="id"/>
                        <div class="form-group">
                            <label class="control-label col-md-2">Firstname</label>
                            <div class="col-md-12">
                                <input type="text" name="firstname" class="form-control" data-validation="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Lastname</label>
                            <div class="col-md-12">
                                <input type="text" name="lastname" class="form-control" data-validation="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Mobile No.</label>
                            <div class="col-md-12">
                                <input type="text" name="cp_no" class="form-control" data-validation="required" maxlength="11" onkeypress="return isNumberKey(event)">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave" onclick="save_contact()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnNew">
                            Save
                        </button>
                        <button type="button" class="btn btn-danger text-white m-btn m-btn--custom m-btn--icon  btnNew" data-dismiss="modal">Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--delete -->
    <div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">
                        Delete Form
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                    </button>
                </div>
                <form id="delete_form" onsubmit="processDeleteContact(this); return false;">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" id="name" name="name" value="">
                    <div class="col-12 modal-body">
                        <p class="mb-0 m--regular-font-size-lg1">Do you want to remove this contact?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-submit btn-primary btnNew">
                            Yes
                        </button>
                        <button type="button" class="btn btn-danger text-white btnNew" data-dismiss="modal">
                            No
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!--import -->
    <div class="modal fade" id="import_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">
                        Import Contacts
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                    </button>
                </div>
                <form id="upload_csv" method="post" enctype="multipart/form">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="col-12 modal-body">
                        <div class="custom-file">
                            <input type="file" name="files" id="fileupload" class="custom-file-input">
                            <span class="custom-file-control" id="file_append"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" name="upload" id="upload" class="btn btn-brand btnNew">
                            Upload
                        </button>
                        <button type="button" class="btn btn-danger text-white btnNew" data-dismiss="modal">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 id="header" class="m-portlet__head-text">
                                IT Mobile Application Masterfile
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <a href="<?php echo base_url("ticket/archive"); ?>" class="m-nav__link btnArchive">
                            <i class="m-nav__link-icon flaticon-open-box"></i>
                            <span class="m-nav__link-text">
                                Archive
                            </span>
                        </a>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                    <a href="javascript:void(0);" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew" data-toggle="modal" data-target="#newITMARModal">
                                        <span>
                                            <i class="la la-plus"></i>
                                            <span>New</span>
                                        </span>
                                    </a>

                                    </div>
                                    <div class="col-4">
                                        <div id="filter-by-date-range" class="form-group m-0">
                                            <div id="date-picker" class="input-group">
                                                <input type="text" readonly="readonly" placeholder="SELECT DATE RANGE" id="date-range" name="date_range" data-validation="required" class="form-control m-input valid"> 
                                                <span class="input-group-addon"><i class="la la-calendar-check-o"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right d-flex flex-row">
                                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll m-datatable--scroll">
                        <table class="table table-striped table-bordered table-sm" id="table-tickets" width="100%">
                            <thead>
                            <tr>
                                <th class="notExport"></th>
                                <th>Reference #</th>
                                <th>Category</th>
                                <!-- <th>Sub Category</th> -->
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Date Needed</th>
                                <th>Created At</th>
                                <th>Requested by</th>
                                <th>Performed by</th>
                                <th class="notExport">Action</th>
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

<div class="modal fade" id="newITMARModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    IT MOBILE APPLICATION FORM
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="new_itmar" method="post" onsubmit="event.preventDefault();">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="form-group m-form__group row col">
                        <div class="col-6 d-flex align-items-center">
                            <label class="m-radio m-radio--brand">
                                <input type="radio" name="app_type" value="TGCloudBAS">
                                TG CloudBAS
                                <span></span>
                            </label>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <label class="m-radio m-radio--brand">
                                <input type="radio" name="app_type" value="GCCTIME" checked>
                                GCCTIME Mobile APP
                                <span></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group m-form__group row col">
                        <label for="employee" class="col-sm-12 col-xs-12 col-md-3 form-label required">Requested By:</label>
                        <div class="col-sm-12 col-xs-12 col-md-9">
                            <select name="employee" id="employee" data-validation="required">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group m-form__group row col">
                        <label for="position" class="col-2 form-label">POSITION: </label>
                        <input type="text" name="position" id="position" class="col-4 form-control m-input" readonly>
                        <label for="department" class="col-2 form-label">DEPARTMENT: </label>
                        <input type="text" name="department" id="department" class="col-4 form-control m-input" style="padding-right: 15px;" readonly>
                    </div>
                    <div class="m-separator m-separator--dashed"></div>
                    <div class="form-group m-form__group row col">
                        <label for="purpose" class="col-12 form-label">PURPOSE: </label>
                        <textarea name="purpose" class="col-12 form-control" id=""></textarea>
                    </div>
                    <div class="m-separator m-separator--dashed"></div>
                    <div class="form-group m-form__group row col">
                        <label for="ass_loc" class="col-12 form-label">ASSIGNED LOCATION/S: </label>
                        <textarea name="ass_loc" class="col-12 form-control" id="ass_loc"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        SAVE
                    </button>
                    <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                        CANCEL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
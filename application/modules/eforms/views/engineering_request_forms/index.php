<style>
#cc_to + .select2-container .select2-selection__rendered {
    padding-bottom: 0 !important;
}
</style>
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 id="header" class="m-portlet__head-text">
                        Request Forms
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <a href="javascript:void(0);" class="m-nav__link btnArchive" onclick="archiveRecordShow();">
                    <span class="m-nav__link-text" id="archiveLabel">
                        Archive
                    </span>
                </a>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="form-group m-form__group row col">
                        <div class="col-3 d-flex align-items-center">
                            <label class="m-radio m-radio--brand fs-3">
                                <input type="radio" name="eng_request_form" value="RFI" checked="">
                                Request for Information (RFI)
                                <span></span>
                            </label>
                        </div>
                        <!-- <div class="col-3 d-flex align-items-center">
                            <label class="m-radio m-radio--brand fs-3">
                                <input type="radio" name="eng_request_form" value="RFA">
                                Request For Approval (RFA)
                                <span></span>
                            </label>
                        </div> -->
                    </div>
                </div>
            </div>
            <div id="rfi-content">
                <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                    <div class="row align-items-center">
                        <div class="col-xl-8 order-2 order-xl-1">
                            <div class="form-group m-form__group row align-items-center">
                                <div class="col-md-4">
                                    <a id="addNew" href="new_request" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew">
                                        <span>
                                            <i class="la la-plus"></i>
                                            <span>New</span>
                                        </span>
                                    </a>
                                </div>
                                <!-- <div class="col-4">
                                    <div id="filter-by-date-range" class="form-group m-0">
                                        <div id="date-picker" class="input-group">
                                            <input type="text" readonly="readonly" placeholder="SELECT DATE RANGE" id="date_range" name="date_range" data-validation="required" class="form-control m-input valid"> 
                                            <span class="input-group-addon"><i class="la la-calendar-check-o"></i></span>
                                        </div>
                                    </div>
                                </div> -->
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
                    <table class="table table-striped table-bordered table-sm" id="rfi_table" width="100%">
                        <thead>
                        <tr>
                            <th></th>
                            <th>RFI INFO</th>
                            <th>Project</th>
                            <th>Reply Needed</th>
                            <th>Created At</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="m--hide" id="rfa-content">
                <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                    <div class="row align-items-center">
                        <div class="col-xl-8 order-2 order-xl-1">
                            <div class="form-group m-form__group row align-items-center">
                                <div class="col-md-4">
                                    <a id="addNew" href="javascript:void(0);" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew" data-toggle="modal" data-target="#newRFIModal">
                                        <span>
                                            <i class="la la-plus"></i>
                                            <span>New</span>
                                        </span>
                                    </a>
                                </div>
                                <!-- <div class="col-4">
                                    <div id="filter-by-date-range" class="form-group m-0">
                                        <div id="date-picker" class="input-group">
                                            <input type="text" readonly="readonly" placeholder="SELECT DATE RANGE" id="date_range" name="date_range" data-validation="required" class="form-control m-input valid"> 
                                            <span class="input-group-addon"><i class="la la-calendar-check-o"></i></span>
                                        </div>
                                    </div>
                                </div> -->
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
                    <table class="table table-striped table-bordered table-sm" id="rfa_table" width="100%">
                        <thead>
                        <tr>
                            <th></th>
                            <th>RFI NO</th>
                            <th>Project</th>
                            <th>Location</th>
                            <th>Reply Needed</th>
                            <th>Created At</th>
                            <th>Created By</th>
                            <th>Actions</th>
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

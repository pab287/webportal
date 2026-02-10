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
                <a href="javascript:void(0);" class="m-nav__link btnArchive">
                    <span class="m-nav__link-text">
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
                        <div class="col-3 d-flex align-items-center">
                            <label class="m-radio m-radio--brand fs-3">
                                <input type="radio" name="eng_request_form" value="RFA">
                                Request For Approval (RFA)
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div id="rfi-content">
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
                    <table class="table table-striped table-bordered table-sm" id="rfi_table" width="100%">
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

<div class="modal fade" id="newRFIModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    NEW REQUEST FOR INFORMATION
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="project_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="project_name" class="form-control-label required">
                                    Project Name
                                </label>
                                <select type="text" id="project_name" name="project_name" class="form-control m-input" data-validation="required" autocomplete="off">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="project_name" class="form-control-label required">
                                    Project Location
                                </label>
                                <input type="text" id="project_location" name="project_location" class="form-control m-input" data-validation="required"  autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="prepared_dt" class="form-control-label required">
                                    Date Prepared
                                </label>
                                <input type="text" id="prepared_dt" name="prepared_dt" class="form-control m-input" data-validation="required" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="reply_needed" class="form-control-label required">
                                    Reply Needed
                                </label>
                                <input type="text" id="reply_needed" name="reply_needed" class="form-control m-input" data-validation="required" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="m-separator m-separator--dashed m-separator--md"></div>
                    <!-- <div class="row">
                        <div class="col-md-4">
                            <div class="card h-100 ">
                                <div class="card-body bg-secondary">
                                    <div class="form-group mb-0">
                                        <label for="requested_by" class="form-control-label required">
                                            Requested By
                                        </label>
                                        <input type="text" id="requested_by" name="requested_by" class="form-control bg-light color-white" autocomplete="off" data-validation="required">
                                        <small class="text-muted">Project-In-Charge</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="form-group mb-0">
                                        <label for="checked_by" class="form-control-label required">
                                            Checked By
                                        </label>
                                        <input type="text" id="checked_by" name="checked_by" class="form-control" autocomplete="off" data-validation="required">
                                        <small class="text-muted">Planning Head</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="form-group mb-0">
                                        <label for="noted_by" class="form-control-label required">
                                            Noted By
                                        </label>
                                        <input type="text"
                                            id="noted_by"
                                            name="noted_by"
                                            class="form-control"
                                            autocomplete="off"
                                            data-validation="required"
                                        >
                                        <small class="text-muted">Operations Manager</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-separator m-separator--dashed m-separator--md"></div> -->
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="send_to" class="form-control-label required">
                                    TO:
                                </label>
                                <input type="text" id="send_to" name="send_to" class="form-control m-input" autocomplete="off" data-validation="required email">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="cc_to" class="form-control-label required">CC:</label>
                                <input type="text" id="cc_to" name="cc_to" class="form-control m-input" multiple autocomplete="off" data-validation="required" placeholder="email1@example.com, email2@example.com">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="consultant" class="form-control-label required">
                                    ATTENTION: (Name of Consultant)
                                </label>
                                <input type="text" id="consultant" name="consultant" class="form-control m-input" autocomplete="off" data-validation="required">
                            </div>
                        </div>
                    </div>
                    <div class="m-separator m-separator--dashed m-separator--md"></div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-control-label required">TYPE:</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="type_arch" class="m-radio m-radio--solid form-control-label">
                                            <input type="radio" name="request_type" value="Architectural" id="type_arch" data-validation="required">
                                            Architectural
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="type_mech" class="m-radio m-radio--solid form-control-label">
                                            <input type="radio" name="request_type" value="Mechanical" id="type_mech">
                                            Mechanical
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="type_fire" class="m-radio m-radio--solid form-control-label">
                                            <input type="radio" name="request_type" value="Fire Protection" id="type_fire">
                                            Fire Protection
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="type_civil" class="m-radio m-radio--solid form-control-label">
                                            <input type="radio" name="request_type" value="Civil / Structural" id="type_civil">
                                            Civil / Structural
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="type_elec" class="m-radio m-radio--solid form-control-label">
                                            <input type="radio" name="request_type" value="Electrical" id="type_elec">
                                            Electrical
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="type_others" class="m-radio m-radio--solid form-control-label">
                                            <input type="radio" name="request_type" value="Others" id="type_others">
                                            Others
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="type_interior" class="m-radio m-radio--solid form-control-label">
                                            <input type="radio" name="request_type" value="Interior Design" id="type_interior">
                                            Interior Design
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="type_plumbing" class="m-radio m-radio--solid form-control-label">
                                            <input type="radio" name="request_type" value="Plumbing" id="type_plumbing_radio">
                                            Plumbing
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" type="text" id="type_other_text" placeholder="Specify if Others">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-separator m-separator--dashed m-separator--md"></div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-control-label required">INFORMATION NEEDED:</label>
                                <textarea name="information_needed" class="form-control" id=""></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="m-separator m-separator--dashed m-separator--md"></div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-control-label required">ATTACHMENTS:</label>
                                <div class="row">
                                    <div class="col-6">
                                        <label for="att_plans" class="m-checkbox m-checkbox--solid form-control-label">
                                            <input type="checkbox" name="attachments[]" value="Plumbling" id="att_plans" data-validation="validate_checkbox_group" data-validation-qty="min1">
                                            Plans/Drawings
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label for="att_mats" class="m-checkbox m-checkbox--solid form-control-label">
                                            <input type="checkbox" name="attachments[]" value="Plumbling" id="att_mats" data-validation="validate_checkbox_group" data-validation-qty="min1">
                                            Material Sample/s
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label for="att_spec" class="m-checkbox m-checkbox--solid form-control-label">
                                            <input type="checkbox" name="attachments[]" value="Plumbling" id="att_spec" data-validation="validate_checkbox_group" data-validation-qty="min1">
                                            Specifications
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label for="att_others" class="m-checkbox m-checkbox--solid form-control-label">
                                            <input type="checkbox" name="attachments[]" value="Plumbling" id="att_others" data-validation="validate_checkbox_group" data-validation-qty="min1">
                                            Others:
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label for="att_test" class="m-checkbox m-checkbox--solid form-control-label">
                                            <input type="checkbox" name="attachments[]" value="Plumbling" id="att_test" data-validation="validate_checkbox_group" data-validation-qty="min1">
                                            Test Results
                                            <span></span>
                                        </label>
                                    </div>
                                     <div class="col-6">
                                        <input class="form-control" type="text" id="att_others_text">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-separator m-separator--dashed m-separator--md"></div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-control-label required">REMARKS:</label>
                                <textarea name="remarks" class="form-control" id=""></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="m-separator m-separator--dashed m-separator--md"></div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-control-label required">REPLY FROM:</label>
                                <textarea name="reply" class="form-control" id=""></textarea>
                            </div>
                        </div>
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

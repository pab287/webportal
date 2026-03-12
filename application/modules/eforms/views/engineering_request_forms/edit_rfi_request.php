<div class="m-content" id="edit_rfi_content">
    <div class="row">
        <div class="col-md-8 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Request for Information details
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <dvi class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12 m-portlet__head-tools text-align-right">
                                    <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left m-tabs-line--primary" role="tablist">
                                        <li class="nav-item m-tabs__item">
                                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#rfiForm" role="tab">
                                                <h5 class="m-portlet__head-text">FORM</h5>
                                            </a>
                                        </li>
                                        <li class="nav-item m-tabs__item">
                                            <a class="nav-link m-tabs__link" data-toggle="tab" href="#replies" role="tab">
                                                <h5 class="m-portlet__head-text">REPLY</h5>
                                            </a>
                                        </li>
                                        <li class="nav-item m-tabs__item">
                                            <a class="nav-link m-tabs__link" data-toggle="tab" href="#attachment" role="tab">
                                                <h5 class="m-portlet__head-text">ATTACHMENT</h5>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 tab-content">
                                    <div class="tab-pane active" id="rfiForm">
                                        <div class="row">
                                            <div class="col-12">
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
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="tab-pane" id="replies">

                                    </div>
                                    <div class="tab-pane" id="attachment">

                                    </div>
                                </div>
                            </div>

                        </div>
                    </dvi>

                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Other Information
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">

                </div>
            </div>
        </div>
    </div>
</div>

<!-- <div class="m-content" id="edit_rfi">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 id="header" class="m-portlet__head-text">
                        Request for Information
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="nav nav-pills nav-pills--brand m-nav-pills--align-right m-nav-pills--btn-pill m-nav-pills--btn-sm" role="tablist">
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link active" data-toggle="tab" href="#manpowerTab" role="tab">
                            Manpower
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item" v-show="content.status != 'for approval'">
                        <a class="nav-link m-tabs__link" data-toggle="tab" href="#candidatesTab" role="tab">
                            Candidates
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="row">
                <div class="col-8">
                    <form action="">
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
                                    <input type="text" id="project_location" name="project_location" class="form-control m-input" data-validation="required" autocomplete="off" readonly="">
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
                                    <input type="text" id="cc_to" name="cc_to" class="form-control m-input" multiple="" autocomplete="off" data-validation="required" placeholder="email1@example.com, email2@example.com">
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed m-separator--md"></div>
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
                                    <label for="info_need" class="form-control-label required">INFORMATION NEEDED:</label>
                                    <textarea name="information_needed" class="form-control" id="info_need"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed m-separator--md"></div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-control-label required" for="attachments_radio">ATTACHMENTS:</label>
                                    <div class="row" id="attachments_radio">
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
                                    <label class="form-control-label required" for="remarks">REMARKS:</label>
                                    <textarea name="remarks" class="form-control" id="remarks"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed m-separator--md"></div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-control-label required" for="reply_from">REPLY FROM:</label>
                                    <textarea name="reply" class="form-control" id="reply_from"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed m-separator--md"></div>
                        <div class="row">
                    </form>
                </div>
            </div>
            <div class="col-4">
                <div class="m-portlet m-portlet--mobile">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">
                                Other Information
                                </h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <div class="m-portlet__nav">
                                <ul class="m-portlet__nav">
                                    <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover">
                                        <a href="javascript:void(0);" class="m-portlet__nav-link m-dropdown__toggle dropdown-toggle btn btn--sm m-btn--pill btn-secondary m-btn m-btn--label-brand btnQuick_action" v-show="content.status != 'DISAPPROVED'">
                                            Actions
                                        </a>
                                        <div class="m-dropdown__wrapper">
                                            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 40.5px;"></span>
                                            <div class="m-dropdown__inner">
                                                <div class="m-dropdown__body">
                                                    <div class="m-dropdown__content">
                                                        <ul class="m-nav">
                                                            <li class="m-nav__section m-nav__section--first">
                                                                <span class="m-nav__section-text">
                                                                    Quick Actions
                                                                </span>
                                                            </li>
                                                            <li class="m-nav__item">
                                                                <a href="javascript:void(0);" class="m-nav__link btnApprove_action" @click="approveRFI()">
                                                                    <i class="m-nav__link-icon la la-thumbs-o-up"></i>
                                                                    <span class="m-nav__link-text">
                                                                        Approve Request
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="row align-items-center">
                            <div class="col-lg-12">
                                <table class="table table-striped m-table"> 
                                <thead>
                                    <tr>
                                        <th>Reference no:</th> 
                                        <th class="text-right" style="font-weight: bold;">MRF-GC&amp;C-2026-0002</th>
                                    </tr>
                                    <tr>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Status:</td>
                                        <td class="text-right" style="font-weight: bold;">ONGOING</td>
                                    </tr>
                                    <tr>
                                        <td>Prepared by:</td>
                                        <td class="text-right" style="font-weight: bold;">Juan Dela Cruz</td>
                                    </tr>
                                    <tr>
                                        <td>Created by:</td>
                                        <td class="text-right" style="font-weight: bold;">Jane Doe</td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->
<div class="m-content">
    <div class="row">
        <div class="col-xl-9 col-lg-12">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-file-1"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Edit Personnel Request
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                <form id="form-update_personnel_request" method="post" action="<?php echo site_url("hris/masterfile/update_personnel_request"); ?>">
                    <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <template v-if="left_pane.status !== 'Cancelled' && left_pane.status !== 'Denied' && left_pane.status !== 'Completed' && left_pane.current === '0'">
                    <input type="hidden" id="id" name="id" value="0" v-model="left_pane.id" />
                    </template>
                    <div class="row m--margin-bottom-10">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group m-form__group row">
                                <label for="company_id" class="col-xs-12 col-sm-12 col-form-label">Company:</label>
                                <div class="col-xs-12 col-sm-12">
                                    <p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="left_pane.company" disabled>
                                        &nbsp;</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group m-form__group row">
                                <label for="department_id" class="col-xs-12 col-sm-12 col-form-label">Department:</label>
                                <div class="col-xs-12 col-sm-12">
                                    <template v-if="left_pane.status !== 'Cancelled' && left_pane.status !== 'Denied' && left_pane.status !== 'Completed' && left_pane.current === '0'"> 
                                    <select id="department_id" name="department_id" autocomplete="off" data-validation="required" class="form-control m-input select2">
                                        <option><option>    
                                    </select>
                                    </template>
                                    <template v-else>
                                    <p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="left_pane.department" disabled>&nbsp;</p>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group m-form__group row">
                                <label for="position_id" class="col-xs-12 col-sm-12 col-form-label">Position:</label>
                                <div class="col-xs-12 col-sm-12">
                                    <template v-if="left_pane.status !== 'Cancelled' && left_pane.status !== 'Denied' && left_pane.status !== 'Completed' && left_pane.current === '0'"> 
                                    <select id="position_id" name="position_id" autocomplete="off" data-validation="required" class="form-control m-input select2">
                                        <option><option>    
                                    </select>
                                    </template>
                                    <template v-else>
                                    <p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="left_pane.position" id="appli_position" disabled>&nbsp;</p>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group m-form__group row">
                                <label for="salary_id" class="col-xs-12 col-sm-12 col-form-label">Salary:</label>
                                <div class="col-xs-12 col-sm-12">
                                    <template v-if="left_pane.status !== 'Cancelled' && left_pane.status !== 'Denied' && left_pane.status !== 'Completed' && left_pane.current === '0'"> 
                                    <select id="salary_id" name="salary_id" autocomplete="off" data-validation="required" class="form-control m-input select2">
                                        <option><option>    
                                    </select>
                                    </template>
                                    <template v-else>
                                    <p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="left_pane.salary" disabled>&nbsp;</p>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group m-form__group row">
                                <label for="type" class="col-xs-12 col-sm-12 col-form-label">Type:</label>
                                <div class="col-xs-12 col-sm-12">
                                    <template v-if="left_pane.status !== 'Cancelled' && left_pane.status !== 'Denied' && left_pane.status !== 'Completed' && left_pane.current === '0'"> 
                                    <select id="type" name="type" autocomplete="off" data-validation="required" class="form-control m-input select2" v-model="left_pane.type">
                                        <option><option>    
                                        <option value="SKILLED RANK AND FILE">SKILLED RANK AND FILE<option>
                                        <option value="RANK AND FILE">RANK AND FILE<option>
                                        <option value="SUPERVISORY">SUPERVISORY<option>
                                        <option value="MANAGERIAL">MANAGERIAL<option>
                                    </select>
                                    </template>
                                    <template v-else>
                                    <p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="left_pane.type" disabled>&nbsp;</p>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group m-form__group row">
                                <label for="requested_by" class="col-xs-12 col-sm-12 col-form-label">Requested By:</label>
                                <div class="col-xs-12 col-sm-12">
                                    <template v-if="left_pane.status !== 'Cancelled' && left_pane.status !== 'Denied' && left_pane.status !== 'Completed' && left_pane.current === '0'"> 
                                    <select id="requested_by" name="requested_by" autocomplete="off" data-validation="required" class="form-control m-input select2">
                                        <option><option>    
                                    </select>
                                    </template>
                                    <template v-else>
                                    <p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="left_pane.requested_name" disabled>&nbsp;</p>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group m-form__group row">
                                <label for="need_dt" class="col-xs-12 col-sm-12 col-form-label">Date Needed:</label>
                                <div class="col-xs-12 col-sm-12">
                                    <template v-if="left_pane.status !== 'Cancelled' && left_pane.status !== 'Denied' && left_pane.status !== 'Completed' && left_pane.current === '0'"> 
                                    <input id="need_dt" type="text" name="need_dt" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" v-model="left_pane.need_dt" />
                                    </template>
                                    <template v-else>
                                    <p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="left_pane.need_dt" disabled>&nbsp;</p>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group m-form__group row">
                                <label for="people_no" class="col-xs-12 col-sm-12 col-form-label">Needed:</label>
                                <div class="col-xs-12 col-sm-12">
                                    <template v-if="left_pane.status !== 'Cancelled' && left_pane.status !== 'Denied' && left_pane.status !== 'Completed' && left_pane.current === '0'">
                                    <input id="people_no" min="1"
                                           type="number" name="people_no"
                                           class="form-control m-input"
                                           maxlength="10" size="10" autocomplete="off" v-model="left_pane.people_no - left_pane.current" data-validation="required" />
                                    </template>
                                    <template v-else>
                                    <p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="left_pane.people_no" disabled>&nbsp;</p>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group m-form__group row">
                                <label for="jobDescription" class="col-xs-12 col-sm-12 col-md-12 col-form-label">Job Description:</label>
                                <div class="col-xs-12 col-sm-12 col-md-12" id="current-job_description">
                                    <textarea id="job_description" class="form-control m-input m-input--custom_textarea" contenteditable="true"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group m-form__group row">
                                <label for="qualification" class="col-xs-12 col-sm-12 col-md-12 col-form-label">Qualification:</label>
                                <div class="col-xs-12 col-sm-12 col-md-12" id="current-qualification">
                                    <textarea id="qualification" class="form-control m-input m-input--custom_textarea" contenteditable="true"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row m--margin-bottom-25">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group m-form__group row">
                                <label for="request_remark" class="col-xs-12 col-sm-12 col-md-12 col-form-label">Request Remarks:</label>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <template v-if="left_pane.status !== 'Cancelled' && left_pane.status !== 'Denied' && left_pane.status !== 'Completed' && left_pane.current === '0'">
                                    <textarea id="request_remark" class="form-control m-input m-input--custom_textarea" rows="10" contenteditable="true" style="min-height: 180px;" v-text="left_pane.request_remark"></textarea>
                                    </template>
                                    <template v-else>
                                    <textarea id="request_remark" class="form-control m-input m-input--custom_textarea" v-text="left_pane.request_remark"></textarea>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group m-form__group row">
                                <label for="remarks" class="col-xs-12 col-sm-12 col-md-12 col-form-label">Approval / Denial Remarks:</label>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                <textarea id="remark" class="form-control m-input m-input--custom_textarea" v-text="left_pane.remark" contenteditable="true"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-12" id="right_pane-content">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Other Information
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    <template v-if="right_pane.status !== 'Cancelled' && right_pane.status !== 'Denied' && right_pane.status !== 'Completed'">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
                                <a href="javascript:void(0);" class="m-portlet__nav-link m-dropdown__toggle dropdown-toggle btn btn--sm m-btn--pill btn-secondary m-btn m-btn--label-brand btnQuick_action">
                                    Action
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
                                                    <template v-if="right_pane.status === 'On Hold' && right_pane.current === '0'">
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0);" class="m-nav__link btnActivate_action activatePersonnelRequestAction">
                                                            <i class="m-nav__link-icon la la-check-circle"></i>
                                                            <span class="m-nav__link-text">
                                                                Activate Request
                                                            </span>
                                                        </a>
                                                    </li>
                                                    </template>
                                                    <template v-if="right_pane.current === '0'">
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0);" class="m-nav__link btnCancel_action cancelPersonnelRequestAction">
                                                            <i class="m-nav__link-icon la la-times-circle"></i>
                                                            <span class="m-nav__link-text">
                                                                Cancel Request
                                                            </span>
                                                        </a>
                                                    </li>
                                                    </template>
                                                    <template v-if="right_pane.status !== 'On Hold' && right_pane.current === '0'">
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0);" class="m-nav__link btnHold_action holdPersonnelRequestAction">
                                                            <i class="m-nav__link-icon la la-hand-paper-o"></i>
                                                            <span class="m-nav__link-text">
                                                                Hold Request
                                                            </span>
                                                        </a>
                                                    </li>
                                                    </template>
                                                    <template v-if="right_pane.current !== '0'">
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0);" class="m-nav__link btnComplete_action completePersonnelRequestAction">
                                                            <i class="m-nav__link-icon la la-check-circle"></i>
                                                            <span class="m-nav__link-text">
                                                                Complete Request
                                                            </span>
                                                        </a>
                                                    </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </template>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <table class="table table-striped m-table">
                                <tbody>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <div class="text-right">
                                        <input type="hidden" v-model="right_pane.status" id="pr_status">
                                        <template v-if="right_pane.status === 'Ongoing'">
                                            <span class="m-badge m-badge--accent m-badge--wide m-badge--custom-large" v-text="right_pane.status">&nbsp;</span>
                                        </template>
                                        <template v-if="right_pane.status === 'Completed'">
                                            <span class="m-badge m-badge--success m-badge--wide m-badge--custom-large" v-text="right_pane.status">&nbsp;</span>
                                        </template>
                                        <template v-if="right_pane.status === 'Cancelled'">
                                            <span class="m-badge m-badge--danger m-badge--wide m-badge--custom-large" v-text="right_pane.status">&nbsp;</span>
                                        </template>
                                        <template v-if="right_pane.status === 'On Hold'">
                                            <span class="m-badge m-badge--warning m-badge--wide m-badge--custom-large" v-text="right_pane.status">&nbsp;</span>
                                        </template>
                                        <template v-if="right_pane.status === 'For Approval'">
                                            <span class="m-badge m-badge--info m-badge--wide m-badge--custom-large" v-text="right_pane.status">&nbsp;</span>
                                        </template>
                                        <template v-if="right_pane.status === 'Denied'">
                                            <span class="m-badge m-badge--danger m-badge--wide m-badge--custom-large" v-text="right_pane.status">&nbsp;</span>
                                        </template>
                                        </div>
                                    </td>
                                </tr>
                                <template v-if="right_pane.approved_by !== ''">
                                <tr>
                                    <th>Approved By</th>
                                    <td>
                                        <p class="text-right m--margin-bottom-0" style="font-weight: bold;" v-text="right_pane.approved_by">&nbsp;</p>
                                        <p class="text-right m--margin-bottom-0"><small v-text="right_pane.approved_dt">&nbsp;</small></p>
                                    </td>
                                </tr>
                                </template>
                                <tr>
                                    <th>Created By</th>
                                    <td>
                                        <p class="text-right m--margin-bottom-0" style="font-weight: bold;" v-text="right_pane.created_by">&nbsp;</p>
                                        <p class="text-right m--margin-bottom-0"><small v-text="right_pane.created_dt">&nbsp;</small></p>
                                    </td>
                                </tr>
                                <template v-if="right_pane.modify_by !== ''">
                                <tr>
                                    <th>Last Updated By</th>
                                    <td>
                                        <p class="text-right m--margin-bottom-0" style="font-weight: bold;" v-text="right_pane.modify_by">&nbsp;</p>
                                        <p class="text-right m--margin-bottom-0"><small v-text="right_pane.modify_dt">&nbsp;</small></p>
                                    </td>
                                </tr>
                                </template>
                                <template v-if="right_pane.completed_by !== ''">
                                <tr>
                                    <th>Completed By</th>
                                    <td>
                                        <p class="text-right m--margin-bottom-0" style="font-weight: bold;" v-text="right_pane.completed_by">&nbsp;</p>
                                        <p class="text-right m--margin-bottom-0"><small v-text="right_pane.completed_dt">&nbsp;</small></p>
                                    </td>
                                </tr>
                                </template>
                                </tbody>
                                <template v-if="right_pane.status !== 'Cancelled' && right_pane.status !== 'Denied' && right_pane.current !== '0'">
                                <tfoot>
                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <p class="text-right m--margin-bottom-0" style="font-weight: bold;">Hired</p>
                                            <p class="text-right m--margin-bottom-0" style="font-weight: bold; font-size: 40px; font-family: serif;" v-text="right_pane.current">0</p>
                                        </th>
                                        <th>
                                            <p class="text-right m--margin-bottom-0" style="font-weight: bold;">Available Slots</p>
                                            <p class="text-right m--margin-bottom-0" style="font-weight: bold; font-size: 40px; font-family: serif;" v-text="right_pane.available_slot">0</p>
                                        </th>
                                    </tr>
                                </tfoot>
                                </template>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__foot text-right">
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                        <template v-if="right_pane.status !== 'Cancelled' && right_pane.status !== 'Denied' && right_pane.status !== 'Completed' && right_pane.current === '0'">
                            <button type="button" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air btnUpdatePersonnelRequest btnSave">Save</button>
                        </template>
                            <button type="button" class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--air btnBack btnBackToList">Back</button>
                        </div>
                    </div>
                </div>
            </div>
            <template v-if="right_pane.meta_count > 0">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Activity Logs
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                        <div class="m-widget2">
                            <template v-for="(item, index) in right_pane.meta">
                                <template v-if="item.type === 'denied'">
                                <div class="m-widget2__item m-widget2__item--log m-widget2__item--no_hover m-widget2__item--danger">
                                    <div class="m-widget2__checkbox"></div>
                                    <div class="m-widget2__desc">
                                        <p class="m-widget2__text m--margin-bottom-0">Personnel Request has been <strong>Denied</strong> By <strong>{{item.name}}</strong></p>
                                        <p class="m-widget2__user-name m--margin-bottom-0"><small>{{item.date}}</small></p>
                                    </div>
                                </div>
                                </template>
                                <template v-if="item.type === 'cancelled'">
                                <div class="m-widget2__item m-widget2__item--log m-widget2__item--no_hover m-widget2__item--danger">
                                    <div class="m-widget2__checkbox"></div>
                                    <div class="m-widget2__desc">
                                        <p class="m-widget2__text m--margin-bottom-0">Personnel Request has been <strong>Cancelled</strong> By <strong>{{item.name}}</strong></p>
                                        <p class="m-widget2__user-name m--margin-bottom-0"><small>{{item.date}}</small></p>
                                    </div>
                                </div>
                                </template>
                                <template v-if="item.type === 'hold'">
                                <div class="m-widget2__item m-widget2__item--log m-widget2__item--no_hover m-widget2__item--warning">
                                    <div class="m-widget2__checkbox"></div>
                                    <div class="m-widget2__desc">
                                        <p class="m-widget2__text m--margin-bottom-0">Personnel Request has been put on <strong>Hold</strong> By <strong>{{item.name}}</strong></p>
                                        <p class="m-widget2__user-name m--margin-bottom-0"><small>{{item.date}}</small></p>
                                    </div>
                                </div>
                                </template>
                                <template v-if="item.type === 'updated'">
                                <div class="m-widget2__item m-widget2__item--log m-widget2__item--no_hover m-widget2__item--success">
                                    <div class="m-widget2__checkbox"></div>
                                    <div class="m-widget2__desc">
                                        <p class="m-widget2__text m--margin-bottom-0">Personnel Request has been <strong>Updated</strong> By <strong>{{item.name}}</strong></p>
                                        <p class="m-widget2__user-name m--margin-bottom-0"><small>{{item.date}}</small></p>
                                    </div>
                                </div>
                                </template>
                                <template v-if="item.type === 'activated'">
                                <div class="m-widget2__item m-widget2__item--log m-widget2__item--no_hover m-widget2__item--primary">
                                    <div class="m-widget2__checkbox"></div>
                                    <div class="m-widget2__desc">
                                        <p class="m-widget2__text m--margin-bottom-0">Personnel Request has been <strong>Activated</strong> By <strong>{{item.name}}</strong></p>
                                        <p class="m-widget2__user-name m--margin-bottom-0"><small>{{item.date}}</small></p>
                                    </div>
                                </div>
                                </template>
                                <template v-if="item.type === 'completed'">
                                <div class="m-widget2__item m-widget2__item--log m-widget2__item--no_hover m-widget2__item--success">
                                    <div class="m-widget2__checkbox"></div>
                                    <div class="m-widget2__desc">
                                        <p class="m-widget2__text m--margin-bottom-0">Personnel Request has been <strong>Completed</strong> By <strong>{{item.name}}</strong></p>
                                        <p class="m-widget2__user-name m--margin-bottom-0"><small>{{item.date}}</small></p>
                                    </div>
                                    <div class="m-widget2__actions">
                                        <div class="m-widget2__actions-nav">
                                                <a href="javascript:void(0);" class="m-dropdown__toggle m-nav__link btnView viewCompletedRemarks" v-bind:data-meta="item.meta">
                                                    <i class="la la-comments" style="font-size: 2.1rem;"></i>
                                                </a>
                                        </div>
                                    </div>
                                </div>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            </template>
        </div>
    </div>
    <div class="modal fade" id="modalTempContent" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div id="modalTempContainer" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Temp Title</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">test</div>
            </div>
        </div>
    </div>
</div>

<script>

</script>
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
                                Personnel Request <small>For Approval</small>
                            </h3>
                        </div>
                    </div>
                </div>
                <div id="left_pane-content" class="m-portlet__body">
                <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
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
                                    <p class="form-control m-input custom-p_textarea m--margin-bottom-0" id="approval_department" v-text="left_pane.department"
                                                      disabled>&nbsp;</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group m-form__group row">
                                <label for="position_id" class="col-xs-12 col-sm-12 col-form-label">Position:</label>
                                <div id="position_id_forApproval" class="col-xs-12 col-sm-12"><p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="left_pane.position"
                                                      disabled>&nbsp;</p></div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group m-form__group row">
                                <label for="salary_id" class="col-xs-12 col-sm-12 col-form-label">Salary:</label>
                                <div class="col-xs-12 col-sm-12"><p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="left_pane.salary"
                                                      disabled>&nbsp;</p></div>
                            </div>
                        </div>
                    </div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group m-form__group row">
                                <label for="type" class="col-xs-12 col-sm-12 col-form-label">Type:</label>
                                <div class="col-xs-12 col-sm-12"><p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="left_pane.type"
                                                      disabled>&nbsp;</p></div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group m-form__group row">
                                <label for="requested_name" class="col-xs-12 col-sm-12 col-form-label">Requested By:</label>
                                <div class="col-xs-12 col-sm-12"><p class="form-control m-input custom-p_textarea m--margin-bottom-0"
                                                      v-text="left_pane.requested_name" disabled>&nbsp;</p></div>
                            </div>
                        </div>
                    </div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group m-form__group row">
                                <label for="need_dt" class="col-xs-12 col-sm-12 col-form-label">Date Needed:</label>
                                <div class="col-xs-12 col-sm-12"><p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="left_pane.need_dt"
                                                      disabled>&nbsp;</p></div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group m-form__group row">
                                <label for="people_no" class="col-xs-12 col-sm-12 col-form-label">Needed:</label>
                                <div class="col-xs-12 col-sm-12"><p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="left_pane.people_no"
                                                      disabled>&nbsp;</p></div>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-12 col-md-12">
                            <div class="form-group m-form__group row">
                                <label for="jobDescription" class="col-xs-12 col-sm-12 col-form-label">Job Description:</label>
                                <div class="col-xs-12 col-sm-12" id="current-job_description">
                                    <div id="job_description_forApproval" class="form-control m-input m-input--custom_textarea" disabled
                                         >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-12 col-md-12">
                            <div class="form-group m-form__group row">
                                <label for="qualification" class="col-xs-12 col-sm-12 col-form-label">Qualification:</label>
                                <div class="col-xs-12 col-sm-12" id="current-qualification">
                                    <div id="qualification_forApproval" class="form-control m-input m-input--custom_textarea" disabled >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row m--margin-bottom-25">
                        <div class="col-12 col-md-12">
                            <div class="form-group m-form__group row">
                                <label for="request_remark" class="col-xs-12 col-sm-12 col-form-label">Request Remarks:</label>
                                <div class="col-xs-12 col-sm-12">
                                    <div id="request_remark" class="form-control m-input m-input--custom_textarea" disabled
                                         v-text="left_pane.request_remark">&nbsp;
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                        <template v-if="right_pane.status === 'Ongoing' || right_pane.status === 'For Approval'">
                            <ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                                    data-dropdown-toggle="hover" aria-expanded="true">
                                    <a href="javascript:void(0);"
                                       class="m-portlet__nav-link m-dropdown__toggle dropdown-toggle btn btn--sm m-btn--pill btn-secondary m-btn m-btn--label-brand btnQuick_action">
                                        Action
                                    </a>
                                    <div class="m-dropdown__wrapper">
                                        <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"
                                              style="left: auto; right: 40.5px;"></span>
                                        <div class="m-dropdown__inner">
                                            <div class="m-dropdown__body">
                                                <div class="m-dropdown__content">
                                                    <ul class="m-nav">
                                                        <li class="m-nav__section m-nav__section--first">
                                                        <span class="m-nav__section-text">
                                                            Quick Actions
                                                        </span>
                                                        </li>
                                                        <template v-if="right_pane.status === 'For Approval'">
                                                            <li class="m-nav__item">
                                                                <a href="javascript:void(0);"
                                                                   class="m-nav__link btnApprove_action approvalRequestAction">
                                                                    <i class="m-nav__link-icon la la-thumbs-up"></i>
                                                                    <span class="m-nav__link-text">
                                                                    Approve Request
                                                                </span>
                                                                </a>
                                                            </li>
                                                            <li class="m-nav__item">
                                                                <a href="javascript:void(0);" class="m-nav__link btnDeny_action denyRequestAction">
                                                                    <i class="m-nav__link-icon la la-ban"></i>
                                                                    <span class="m-nav__link-text">
                                                                    Deny Request
                                                                </span>
                                                                </a>
                                                            </li>
                                                            <li class="m-nav__separator m-nav__separator--fit"></li>
                                                        </template>
                                                        <li class="m-nav__item">
                                                            <a href="javascript:void(0);" class="m-nav__link btnEdit editPersonnelRequestAction">
                                                                <i class="m-nav__link-icon la la-pencil"></i>
                                                                <span class="m-nav__link-text">
                                                                    Edit Request
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
                                            <p class="text-right m--margin-bottom-0" style="font-weight: bold;" v-text="right_pane.approved_by">
                                                &nbsp;</p>
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
                                            <p class="text-right m--margin-bottom-0" style="font-weight: bold;" v-text="right_pane.modify_by">
                                                &nbsp;</p>
                                            <p class="text-right m--margin-bottom-0"><small v-text="right_pane.modify_dt">&nbsp;</small></p>
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__foot">
                    <div class="row align-items-center">
                        <div class="col-lg-12 text-right">
                            <button type="button" class="btnApprove_action approvalRequestAction btn m-btn m-btn--icon btn-primary"
                                    v-if="right_pane.status !== 'Ongoing'">
                                <span>
                                    <i class="la la-thumbs-o-up"></i>
                                    <span>Approve Request</span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--air btnBack btnBackToList">Back
                            </button>
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
                                                    <p class="m-widget2__text m--margin-bottom-0">Personnel Request has been <strong>Denied</strong>
                                                        By <strong>{{item.name}}</strong></p>
                                                    <p class="m-widget2__user-name m--margin-bottom-0"><small>{{item.date}}</small></p>
                                                </div>
                                            </div>
                                        </template>
                                        <template v-if="item.type === 'cancelled'">
                                            <div class="m-widget2__item m-widget2__item--log m-widget2__item--no_hover m-widget2__item--danger">
                                                <div class="m-widget2__checkbox"></div>
                                                <div class="m-widget2__desc">
                                                    <p class="m-widget2__text m--margin-bottom-0">Personnel Request has been
                                                        <strong>Cancelled</strong> By <strong>{{item.name}}</strong></p>
                                                    <p class="m-widget2__user-name m--margin-bottom-0"><small>{{item.date}}</small></p>
                                                </div>
                                            </div>
                                        </template>
                                        <template v-if="item.type === 'hold'">
                                            <div class="m-widget2__item m-widget2__item--log m-widget2__item--no_hover m-widget2__item--warning">
                                                <div class="m-widget2__checkbox"></div>
                                                <div class="m-widget2__desc">
                                                    <p class="m-widget2__text m--margin-bottom-0">Personnel Request has been put on
                                                        <strong>Hold</strong> By <strong>{{item.name}}</strong></p>
                                                    <p class="m-widget2__user-name m--margin-bottom-0"><small>{{item.date}}</small></p>
                                                </div>
                                            </div>
                                        </template>
                                        <template v-if="item.type === 'updated'">
                                            <div class="m-widget2__item m-widget2__item--log m-widget2__item--no_hover m-widget2__item--success">
                                                <div class="m-widget2__checkbox"></div>
                                                <div class="m-widget2__desc">
                                                    <p class="m-widget2__text m--margin-bottom-0">Personnel Request has been <strong>Updated</strong>
                                                        By <strong>{{item.name}}</strong></p>
                                                    <p class="m-widget2__user-name m--margin-bottom-0"><small>{{item.date}}</small></p>
                                                </div>
                                            </div>
                                        </template>
                                        <template v-if="item.type === 'activated'">
                                            <div class="m-widget2__item m-widget2__item--log m-widget2__item--no_hover m-widget2__item--primary">
                                                <div class="m-widget2__checkbox"></div>
                                                <div class="m-widget2__desc">
                                                    <p class="m-widget2__text m--margin-bottom-0">Personnel Request has been
                                                        <strong>Activated</strong> By <strong>{{item.name}}</strong></p>
                                                    <p class="m-widget2__user-name m--margin-bottom-0"><small>{{item.date}}</small></p>
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
<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">Manage Users</h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item"></li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--marginless">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-12">
                                        <button id="user-new"
                                            class="btn btn-success m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew btnNewUser"
                                            onclick="open_user()">
                                            <span>
                                                <i class="la la-plus"></i>
                                                <span>
                                                    New
                                                </span>
                                            </span>
                                        </button>
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
                            </div>
                        </div>
                    </div>
                    <!--begin: Datatable -->
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                        <table class="table table-striped table-bordered" id="table-users" style="width: 100%">
                            <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Biometric #</th>
                                <th>Account Name</th>
                                <th>User Account</th>
                                <th>Assigned Role</th>
                                <th>Sensitive Data</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!--end: Datatable -->
                </div>
            </div>
            <!--end::Portlet-->
        </div>
    </div>
</div>

<!--begin::Modal-->
<div class="modal fade" id="modal-user_role-assign" tabindex="-1">
    <div class="modal-dialog modal-m">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal fade" id="modal-confirm-suspend-user" tabindex="-1">
    <div class="modal-dialog">
        <form onsubmit="event.preventDefault(); process_suspend_account(this);">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Suspend User Confirmation</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <span class="m--regular-font-size-lg1">DO YOU WANT TO SUSPEND THIS USER?</span>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary m-btn btnNew">Yes</button>
                    <button type="button" class="btn btn-danger m-btn btnNew" data-dismiss="modal">No</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_form_user">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">&nbsp;</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="form_user">
            <div class="modal-body form-horizontal">
                <input type="hidden" value="" name="id" />
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

                <div class="form-group row" id="employee">
                    <label for="emp_id" class="control-label col-md-2 required">Employee</label>
                    <div class="col-md-12">
                        <select id="select2_employee" name="emp_id" data-validation="required"></select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="control-label col-md-2 required">Email</label>
                    <div class="col-md-12">
                        <input type="text" name="email" class="form-control" data-validation="required" autocomplete="off" />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="username" class="control-label col-md-4 required">Username</label>
                    <div class="col-md-12">
                        <input type="text" name="username" class="form-control" data-validation="required" autocomplete="off" />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="control-label col-md-4 required">Password</label>
                    <div class="col-md-12">
                        <input type="password" name="password" class="form-control" data-validation="required" autocomplete="off" />
                    </div>
                </div>

                <div class="form-group row">
                    <label for="user_role" class="control-label col-md-4">User Role</label>
                    <div class="col-md-12">
                        <select id="user_role" name="role_id" data-validation="required"></select>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-brand alert-dismissible fade show" role="alert">
                            <div class="m-alert__icon">
                                <i class="flaticon-exclamation-1"></i>
                                <span></span>
                            </div>
                            <div class="m-alert__text">
                                <strong>Note!</strong> For employee that needs payslip viewing as default user, select <strong>`Default User with Payslip`</strong>.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="telegram_chat_id" class="control-label col-md-4">Telegram ID <small>(Optional)</small></label>
                    <div class="col-md-12">
                        <input type="text" name="telegram_chat_id" class="form-control" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="margin-left: 15px;">
                        <div class="form-group">
                            <div class="m-checkbox-inline">
                                <label class="m-checkbox">
                                    <input type="checkbox" name="is_important" /> Has sensitive data
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btnSave" class="btn btn-success m-btn m-btn--custom m-btn--icon btnNew btnEdit">Save</button>
                <button type="button" class="btn btn-danger m-btn m-btn--custom m-btn--icon btnClose" data-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div>
</div>
<!--end::Modal-->
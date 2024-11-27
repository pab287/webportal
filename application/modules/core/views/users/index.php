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
                                        <a id="user-new" href="javascript:void(0);"
                                            class="btn btn-success m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew btnNewUser" onclick="open_user()">
                                            <span>
                                                <i class="la la-plus"></i>
                                                <span>
                                                    New
                                                </span>
                                            </span>
                                        </a>
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
                        <table class="table table-striped table-bordered" id="table-users" width="100%">
                            <thead>
                            <tr>
                                <th>Biometric No</th>
                                <th>Lastname</th>
                                <th>Firstname</th>
                                <th>Middlename</th>
                                <th>Email</th>
                                <th>Assigned Role</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
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
<div class="modal fade" id="modal-user_role-assign" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal fade" id="modal-confirm-suspend-user" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
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

<div class="modal fade" id="modal_form_user" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

            </div>
            <div class="modal-body form">
                <form action="#" id="form_user" class="form-horizontal">
                    <input type="hidden" value="" name="id"/>
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

                    <div class="form-group" id="employee">
                        <label class="control-label col-md-2">Employee</label>
                        <div class="col-md-12">
                            <select id="select2_employee" name="emp_id" data-validation="required">

                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">Email</label>
                        <div class="col-md-12">
                            <input type="text" name="email" class="form-control" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">Username</label>
                        <div class="col-md-12">
                            <input type="text" name="username" class="form-control" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">Password</label>
                        <div class="col-md-12">
                            <input type="password" name="password" class="form-control" data-validation="required">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-2">Group</label>
                        <div class="col-md-12">
                            <select id="select2_group" name="group_id" data-validation="required">

                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Telegram ID</label>
                        <div class="col-md-12">
                            <input type="text" name="telegram_chat_id" class="form-control">
                        </div>
                    </div>

            </div>
            <div class="modal-footer">

                <button type="submit" id="btnSave" onclick="save_user()" class="btn btn-success m-btn m-btn--custom m-btn--icon  btnNew">Save</button>
                <button type="button" class="btn btn-danger m-btn m-btn--custom m-btn--icon  btnNew" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div>

    <script>

        var _currentActions = "<?php echo isset($actions) ? json_encode($actions) : ""; ?>";
        var _csrf_token = "<?php echo $this->security->get_csrf_token_name(); ?>";
        var _csrf_hash = "<?php echo $this->security->get_csrf_hash(); ?>";

    </script>
    <!--end::Modal-->
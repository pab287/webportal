<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" href="<?php echo site_url("eforms/billing/accounts"); ?>" title="Go to Masterfile"
                                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
                            <h3 class="m-portlet__head-text">Edit Account</h3>
                        </div>
                    </div>

                    <div class="m-portlet__head-tools">
                        
                    </div>
                </div>

                <form class="m-form m-form--fit" id="formEditAccount" method="POST" action="<?php echo site_url('eforms/billing/updateaccount');?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="id" value="">

                    <div class="m-portlet__body">
                        <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                                <div class="m-form__heading">
                                    <h3 class="m-form__heading-title">Customer Info:</h3>
                                </div>

                                <div class="row m--margin-bottom-10">
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">Account No.* :</label>
                                            <div class="col-8">
                                                <input oninput="this.value = this.value.replace(/[^0-9-]/g, '');" maxlength="11" autocomplete="off" type="text" name="accountno" class="form-control m-input" data-validation="required" v-model="vm_tab1.accountno">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4" style="pointer-events: none;">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">Meter No.* :</label>
                                            <div class="col-8">
                                                <input oninput="this.value = this.value.replace(/[^0-9-]/g, '').replace(/(\..*)\./g, '$1');" maxlength="20" autocomplete="off" type="text" name="meterno" class="form-control m-input" data-validation="required" v-model="vm_tab1.meterno">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row m--margin-bottom-10">
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">Firstname* :</label>
                                            <div class="col-8">
                                                <input autocomplete="off" maxlength="20" type="text" name="firstname" class="form-control m-input" data-validation="required" v-model="vm_tab1.firstname">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">Middlename* :</label>
                                            <div class="col-8">
                                                <input autocomplete="off" maxlength="20" type="text" name="middlename" class="form-control m-input" data-validation="required" v-model="vm_tab1.middlename">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">Lastname* :</label>
                                            <div class="col-8">
                                                <input autocomplete="off" maxlength="20" type="text" name="lastname" class="form-control m-input" data-validation="required" v-model="vm_tab1.lastname">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m--margin-bottom-10">
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">Occupation* :</label>
                                            <div class="col-8">
                                                <input autocomplete="off" type="text" name="occupation" class="form-control m-input" data-validation="required" v-model="vm_tab1.occupation">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">Subdivision* :</label>
                                            <div class="col-8">
                                                <select name="subdivision_id" class="form-control m-input" id="subdivisionSelect" data-validation="required"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m--margin-bottom-10">
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">House Model.* :</label>
                                            <div class="col-8">
                                                <input autocomplete="off" type="text" name="model" class="form-control m-input" data-validation="required" v-model="vm_tab1.model">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">Block no.* :</label>
                                            <div class="col-8">
                                                <input oninput="this.value = this.value.replace(/[^0-9-]/g, '');" maxlength="11" autocomplete="off" type="text" name="block" class="form-control m-input" data-validation="required" v-model="vm_tab1.block">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">Lot no.* :</label>
                                            <div class="col-8">
                                                <input oninput="this.value = this.value.replace(/[^0-9-]/g, '');" maxlength="11" autocomplete="off" type="text" name="lot" class="form-control m-input" data-validation="required" v-model="vm_tab1.lot">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row m--margin-bottom-10">
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">Phone Number* :</label>
                                            <div class="col-8">
                                                <input oninput="this.value = this.value.replace(/[^0-9]/g, '');" autocomplete="off" type="text" name="phonenumber" class="form-control m-input" data-validation="required" v-model="vm_tab1.phonenumber">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">email address* :</label>
                                            <div class="col-8">
                                                <input autocomplete="off" type="text" name="email" class="form-control m-input" data-validation="required email" v-model="vm_tab1.email">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m--margin-bottom-10">
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">Application Date* :</label>
                                            <div class="col-8">
                                                <input autocomplete="off" readonly type="text" id="applicationdate" name="applicationdate" class="form-control m-input" data-validation="required">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">Activation Date* :</label>
                                            <div class="col-8">
                                                <input autocomplete="off" readonly type="text" id="activationdate" name="activationdate" class="form-control m-input" data-validation="required">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="m-form__seperator m-form__seperator--dashed m--margin-bottom-20"></div>

                                <div class="m-form__section m-form__section--first">
                                    <div class="m-form__heading">
                                        <h3 class="m-form__heading-title">Billing Information:</h3>
                                    </div>

                                    <div class="row m--margin-bottom-10">
                                        <div class="col-md-4">
                                            <div class="form-group m-form__group row">
                                                <label class="col-4 col-form-label">Street* :</label>
                                                <div class="col-8">
                                                    <input autocomplete="off" type="text" name="street" class="form-control m-input" data-validation="required" v-model="vm_tab1.street">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group m-form__group row">
                                                <label class="col-4 col-form-label">Barangay* :</label>
                                                <div class="col-8">
                                                    <input autocomplete="off" type="text" name="brgy" class="form-control m-input" data-validation="required" v-model="vm_tab1.brgy">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group m-form__group row">
                                                <label class="col-4 col-form-label">City* :</label>
                                                <div class="col-8">
                                                    <input autocomplete="off" type="text" name="city" class="form-control m-input" data-validation="required" v-model="vm_tab1.city">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row m--margin-bottom-10">
                                        <div class="col-md-4">
                                            <div class="form-group m-form__group row">
                                                <label class="col-4 col-form-label">Province* :</label>
                                                <div class="col-8">
                                                    <input autocomplete="off" type="text" name="province" class="form-control m-input" data-validation="required" v-model="vm_tab1.province">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="m-form__seperator m-form__seperator--dashed m--margin-bottom-20"></div>
                                
                                <div class="row m--margin-bottom-10">
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-12 text-center col-form-label">Customer Status :</label>
                                            <div class="col-12">
                                                <div class="m-checkbox-inline row justify-content-center">
                                                    <label class="m-checkbox"><input id="active" onclick="change_active()" checked type="radio" name="status" value="1">Active<span></span></label>
                                                    <label class="m-checkbox"><input id="inactive" onclick="change_inactive()" type="radio" name="status" value="0">Inactive<span></span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row justify-content-center">
                                            <label class="col-12 text-center col-form-label">Water Connection :</label>
                                            <div class="col-12">
                                                <div class="m-checkbox-inline row justify-content-center">
                                                    <label class="m-checkbox" id="lbl_connected"><input id="connected" onclick="showConfirmationDisconnect()" checked type="radio" name="is_disconnected" value="0">Connect<span></span></label>
                                                    <label class="m-checkbox" id="lbl_disconnected"><input id="disconnected" onclick="showConfirmationDisconnect()" type="radio" name="is_disconnected" value="1">Disconnect<span></span></label>
                                                </div>
                                                <div id="overdue_alert" class="m--hide text-center mt-2"><span class="m-badge m-badge--danger m-badge--wide">Overdue Bill</span></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-12 text-center col-form-label">Current Usage:</label>
                                            <div class="col-12">
                                                <div class="m-checkbox-inline row justify-content-center">
                                                    <input autocomplete="off" type="text" readonly class="form-control col-8 m-input text-right" data-validation="required" v-model="vm_reading">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="m-portlet__foot">
                            <div class="row">
                                <div class="col-lg-6"></div>
                                <div class="col-lg-6 d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger btnArchive mr-2" data-toggle="modal" data-target="#m_archiveConfirm">Archive</button>
                                    <button type="submit" class="btn btn-primary btnSave mr-2">Update</button>
                                    <button type="button" onclick="goIndex()" class="btn btn-secondary btnCancel">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade show" id="m_archiveConfirm" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <input type="hidden" name="id" value="">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Account Notification!</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="alert alert-danger alert-dismissible fade show m-alert m-alert--square m-alert--air" role="alert">
                    Are you sure to archive this account?
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger btnArchive" id="btnConfirmArchive">Archive</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade show" id="m_waterConnection" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Water Connection</h5>
                <button type="button" class="close btnWaterConnection_cancel" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body body_title">Are you sure to archive this account?</div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary btnArchive btnWaterConnection" id="btnWaterConnection">Yes</button>
                <button type="button" class="btn btn-secondary btnWaterConnection_cancel" data-dismiss="modal" id="btnWaterConnection_cancel">No</button>
            </div>
        </div>
    </div>
</div>
<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
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
							<h3 class="m-portlet__head-text">
								New Account
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						
					</div>
                </div>
                <!--begin::Form-->
                <form class="m-form m-form--fit" id="formNewAccount" method="POST" action="<?php echo site_url('eforms/billing/createaccount');?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="m-portlet__body">
                       <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                            <div class="m-form__heading">
                                <h3 class="m-form__heading-title">
                                    Customer Info:
                                </h3>
                            </div>
                            <div class="row m--margin-bottom-10">
                                <div class="col-md-4">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            Account No.: <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-8">
                                            <input oninput="this.value = this.value.replace(/[^0-9-]/g, '');" maxlength="11" autocomplete="off" type="text" name="accountno" class="form-control m-input" data-validation="required">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            Meter No.: <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-8">
                                            <input oninput="this.value = this.value.replace(/[^0-9-]/g, '').replace(/(\..*)\./g, '$1');" maxlength="20" autocomplete="off" type="text" name="meterno" class="form-control m-input" data-validation="required">
                                        </div>
                                    </div>
                                </div>
                             </div>
                             
                             <div class="row m--margin-bottom-10">
                                <div class="col-md-4">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            Firstname: <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-8">
                                            <input autocomplete="off" maxlength="20" type="text" name="firstname" class="form-control m-input" data-validation="required">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            Middlename: <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-8">
                                            <input autocomplete="off" maxlength="20" type="text" name="middlename" class="form-control m-input" data-validation="required">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            Lastname: <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-8">
                                            <input autocomplete="off" maxlength="20" type="text" name="lastname" class="form-control m-input" data-validation="required">
                                        </div>
                                    </div>
                                </div>
                             </div>

                             <div class="row m--margin-bottom-10">
                                <div class="col-md-4">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            Occupation: <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-8">
                                            <input autocomplete="off" type="text" name="occupation" class="form-control m-input" >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            Subdivision: <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-8">
                                            <select name="subdivision_id" class="form-control m-input" id="subdivisionSelect" data-validation="required">
                                            
                                            </select>
                                        </div>
                                    </div>
                                </div>
                             </div>

                             <div class="row m--margin-bottom-10">
                                <div class="col-md-4">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            House Model.: <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-8">
                                            <input autocomplete="off" type="text" name="model" class="form-control m-input" data-validation="required">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            Block no.: <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-8">
                                            <input oninput="this.value = this.value.replace(/[^0-9-]/g, '');" maxlength="11" autocomplete="off" type="text" name="block" class="form-control m-input" data-validation="required">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            Lot no.: <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-8">
                                            <input oninput="this.value = this.value.replace(/[^0-9-]/g, '');" maxlength="11" autocomplete="off" type="text" name="lot" class="form-control m-input" data-validation="required">
                                        </div>
                                    </div>
                                </div>
                             </div>
                             
                             <div class="row m--margin-bottom-10">
                                <div class="col-md-4">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            Phone Number: <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-8">
                                            <input oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,11);" autocomplete="off" type="text" name="phonenumber" id="phonenumber" class="form-control m-input" data-validation="required">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            email address: <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-8">
                                            <input autocomplete="off" type="text" name="email" class="form-control m-input" data-validation="required email" >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row m--margin-bottom-10">
                                <div class="col-md-4">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            Application Date: <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-8">
                                            <input autocomplete="off" readonly type="text" id="applicationdate" name="applicationdate" class="form-control m-input" data-validation="required">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            Activation Date: <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-8">
                                            <input autocomplete="off" readonly type="text" id="activationdate" name="activationdate" class="form-control m-input" data-validation="required">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="m-form__seperator m-form__seperator--dashed m--margin-bottom-20"></div>

                            <div class="m-form__section m-form__section--first">
                                <div class="m-form__heading">
                                    <h3 class="m-form__heading-title">
                                        Billing Information:
                                    </h3>
                                </div>
                                <div class="row m--margin-bottom-10">
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">
                                                Street: <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-8">
                                                <input autocomplete="off" type="text" name="street" class="form-control m-input" data-validation="required">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">
                                                Barangay: <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-8">
                                                <input autocomplete="off" type="text" name="brgy" class="form-control m-input" data-validation="required">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">
                                                City: <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-8">
                                                <input autocomplete="off" type="text" name="city" class="form-control m-input" data-validation="required">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m--margin-bottom-10">
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row">
                                            <label class="col-4 col-form-label">
                                                Province: <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-8">
                                                <input autocomplete="off" type="text" name="province" class="form-control m-input" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="m-form__seperator m-form__seperator--dashed m--margin-bottom-20"></div>

                            <div class="row m--margin-bottom-10">
                                <div class="col-md-4">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            Customer Status:
                                        </label>
                                        <div class="col-9">
                                            <div class="m-checkbox-inline">
                                                <label class="m-checkbox"><input id="active" onclick="change_active()" checked type="radio" name="status" value="1">Active<span></span></label>
                                                <label class="m-checkbox"><input id="inactive" onclick="change_inactive()" type="radio" name="status" value="0">Inactive<span></span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4 col-form-label">
                                            Water Connection:
                                        </label>
                                        <div class="col-9">
                                            <div class="m-checkbox-inline">
                                                <label class="m-checkbox"><input id="connected" onclick="change_connection()" checked type="radio" name="is_disconnected" value="0">Connect<span></span></label>
                                                <label class="m-checkbox"><input id="disconnected" onclick="change_disconnection()" type="radio" name="is_disconnected" value="1">Disconnect<span></span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                <div class="m-portlet__foot">
                    <div class="m-form__actions m--paddingless text-right" style="padding:0;">
                        <div class="row">
                            <div class="col-lg-6"></div>
                            <div class="col-lg-6">
                                <button type="submit" class="btn btn-primary btnSave">
                                    Save
                                </button>
                                <a href="<?php echo site_url("eforms/billing/accounts"); ?>">
                                    <button type="button" class="btn btn-secondary btnCancel">
                                        Cancel
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
			<!--end::Form-->
			<!--end::Portlet-->
		</div>
	</div>
</div>

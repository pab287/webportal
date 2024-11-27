<div class="m-content">
    <div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" href="masterfile" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
							<h3 class="m-portlet__head-text">Edit Return to work Form</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools"></div>
				</div>
                <form id="frm-edit_rtw" method="post" action="<?php echo site_url("eforms/return_to_work/update_rtw_data"); ?>">
				    <div class="m-portlet__body" id="return_to_work-content">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" name="id" v-model="row.id" />
                        <input type="hidden" name="company" v-model="row.company" />
                        <input type="hidden" name="department" v-model="row.department" />
                        <input type="hidden" name="position" v-model="row.position" />
                        <input type="hidden" name="company_id" v-model="row.company_id" />
                        <input type="hidden" name="department_id" v-model="row.department_id" />
                        <input type="hidden" name="position_id" v-model="row.position_id" />
					    <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 text-right">Employee *</label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <select id="employee" name="employee_id" data-validation="required"></select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 text-right">Company</label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <textarea class="form-control m-input" 
                                            id="details" rows="5" data-validation="required" 
                                            v-model="setCompanyDetails()" 
                                            style="min-height: 105px;" disabled></textarea>
                                    </div>
                                </div>
                                <div class="m-form__group form-group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label text-right">Type *</label>
                                    <div class="col-md-6 col-lg-6 col-sm-6 col-xs-12">
                                        <div class="m-checkbox-list">
                                            <label class="m-checkbox">
                                                <input type="radio" id="return_type_0" class="check_type" name="return_type" v-model="row.return_type" value="0" checked />
                                                Unauthorized Absence / No Notification
                                                <span></span>
                                            </label>
                                            <label class="m-checkbox">
                                                <input type="radio" id="return_type_1" class="check_type" name="return_type" v-model="row.return_type" value="1">
                                                    Recalled
                                                <span></span>
                                            </label>
                                            <label class="m-checkbox">
                                                <input type="radio" id="return_type_2" class="check_type" name="return_type" v-model="row.return_type" value="2">
                                                    Request to extend days of work
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="temp-fields"></div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12 text-right">
                                        Reason *
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <textarea class="form-control m-input" id="reason" 
                                            name="reason" v-model="row.reason" rows="9" style="min-height: 162px;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot">
                        <div class="row align-items-center">
                            <div class="col-md-12 col-lg-12 col-12 m--align-right">
                                <button type="submit" class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btn-submit btnSave"><i class="la la-save"></i> Save</button>
                                <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnCancel" onclick="redirectMasterfile()">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
			</div>
		</div>
	</div>
</div>
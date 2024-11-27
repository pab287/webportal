<div class="m-content">
    <div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" title="Go back" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack" onclick="back()">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
							<h3 class="m-portlet__head-text">
								Edit Overtime Request
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
				</div>
                <form id="form_overtime">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				    <div class="m-portlet__body">
					    <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12">
                                        Employee*
                                    </label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                        <select id="employee" name="employee"  data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12">
                                        Company*
                                    </label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                        <textarea class="form-control m-input" id="details" rows="5" data-validation="required" v-model="vm_tab1.company+'\n'+vm_tab1.department+'\n'+vm_tab1.position" disabled></textarea>
                                        <input type="hidden" name="company" id="company" v-model="vm_tab1.company"/>
                                        <input type="hidden" name="department" id="department" v-model="vm_tab1.department"/>
                                        <input type="hidden" name="position" id="position" v-model="vm_tab1.position"/>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12">Date & Time*</label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12 input-group date" id="date_time">
                                        <input class="form-control m-input" type="text" id="date" v-model="moment(vm_tab1.date_from).format('MM/D/Y hh:mm a')+' - '+moment(vm_tab1.date_to).format('MM/D/Y hh:mm a')" data-validation="required"/>
                                        <input type="hidden" name="date_from" v-model="vm_tab1.date_from" id="date_from"/>
                                        <input type="hidden" name="date_to" v-model="vm_tab1.date_to" id="date_to"/>
                                        <span class="input-group-addon">
                                                <i class="la la-calendar glyphicon-th"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12">
                                        Purpose*
                                    </label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                        <textarea class="form-control m-input" id="purpose" name="purpose" rows="6" data-validation="required"></textarea>
                                        <input type="hidden" id="prevLen" value="0"/>
                                        <span class="m-form__help" style="text-transform: none; font-width: 600;">
                                            <i>Note: Auto bullets when pressing "Enter".</i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12">
                                        Requested by*
                                    </label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                        <select id="requested_by" name="requested_by"  data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12">
                                        Remarks
                                    </label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                        <textarea class="form-control m-input" id="remarks" name="remarks" rows="5" v-model="vm_tab1.requested_remarks"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br><br><br><br><br>
                        <div class="m-separator m-separator--solid d-xl-12"></div>
                        <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                            <button type="submit" class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btn-submit btnSave" onclick="save()">
                                <span>
                                <i class="la la-save"></i>
                                    <span>
                                    Save
                                    </span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnCancel" onclick="back()">
                                <span>
                                    Cancel
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
			</div>
		</div>
	</div>
</div>
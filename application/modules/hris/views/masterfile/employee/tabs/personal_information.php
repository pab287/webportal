<form id="frmEditEmployeeData" class="m-form m-form--fit m-form--label-align-right" method="post" action="<?php echo site_url("hris/masterfile/update_employee_personal_info"); ?>">
    <input type="hidden" name="id" v-model="vm_tab1.id" />
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <div class="m-portlet__body">
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                <label for="firstname" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">First Name *</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <input id="firstname" type="text" name="firstname" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" v-model="vm_tab1.firstname"/>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="middlename" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Middle Name</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <input id="middlename" type="text" name="middlename" class="form-control m-input" placeholder="( Optional )" maxlength="25" size="25" autocomplete="off" v-model="vm_tab1.middlename" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="lastname" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Last Name *</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <input id="lastname" type="text" name="lastname" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" v-model="vm_tab1.lastname" />
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="suffix" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Suffix</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <input id="suffix" type="text" name="suffix" class="form-control m-input" placeholder="( Optional )" maxlength="10" size="10" autocomplete="off" v-model="vm_tab1.suffix" />
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="tel_no" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Telephone No</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="la la-chain"></i>
                            </span>
                            <input id="tel_no" type="text" name="tel_no" class="form-control m-input" placeholder="( Optional )" maxlength="11" size="11" autocomplete="off" v-model="vm_tab1.tel_no" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="mobile_no" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Mobile No</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="la la-chain"></i>
                            </span>
                            <input id="mobile_no" type="text" name="mobile_no" class="form-control m-input" placeholder="( Optional )" maxlength="11" size="11" autocomplete="off" v-model="vm_tab1.mobile_no" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="email" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Email Address</label>
                    <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                        <input id="email" type="text" name="email" class="form-control m-input" placeholder="( Optional )" maxlength="100" size="100" autocomplete="off" v-model="vm_tab1.email" />
                        <span class="m-form__help">We'll never share your email with anyone else</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-10">
            <div class="col-xs-10 col-sm-10 col-md-12 col-lg-12 col-xl-12">
                <div class="form-group m-form__group row">
                    <label for="curr_addr" class="col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-form-label">Current Address *</label>
                    <div class="col-xs-6 col-sm-6 col-md-8 col-lg-9 col-xl-7">
                        <input id="curr_addr" type="text" name="curr_addr" class="form-control m-input" maxlength="200" size="200" autocomplete="off" data-validation="required" v-model="vm_tab1.curr_addr" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-xs-10 col-sm-10 col-md-12 col-lg-12 col-xl-12">
                <div class="form-group m-form__group row">
                    <label for="prov_addr" class="col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-form-label">Permanent Address *</label>
                    <div class="col-xs-6 col-sm-6 col-md-8 col-lg-9 col-xl-7">
                        <input id="prov_addr" type="text" name="prov_addr" class="form-control m-input" maxlength="200" size="200" autocomplete="off" data-validation="required" v-model="vm_tab1.prov_addr" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-10 col-sm-10 col-md-12 col-lg-12 col-xl-12">
                <div class="form-group m-form__group row">
                    <label for="long_lat_coordinates" class="col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-form-label">Map Coordinates</label>
                    <div class="col-xs-6 col-sm-6 col-md-8 col-lg-9 col-xl-7">
                        <input id="long_lat_coordinates" type="text" name="long_lat_coordinates" class="form-control m-input" maxlength="100" size="100" autocomplete="off" v-model="vm_tab1.map_coordinates" />
                        <div class="m-form__help">latitude and longitude map coordinates</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="gender" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Gender *</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <div class="m-checkbox-inline">
                            <label class="m-checkbox">
                                <input id="gender" type="radio" name="gender" data-validation="required" value="Male" v-model="vm_tab1.gender">
                                Male<span></span>
                            </label>
                            <label class="m-checkbox">
                                <input id="gender" type="radio" name="gender" data-validation="required" value="Female" v-model="vm_tab1.gender">
                                Female<span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="civil_stat" class="col-sm-5 col-md-5 col-lg-5 col-xl-5 col-form-label">Civil Status *</label>
                    <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7">
                        <select id="civil_stat" class="form-control select2" name="civil_stat" data-validation="required" v-model="vm_tab1.civil_stat">
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Separated">Separated</option>
                            <option value="Divorced">Divorced</option>
                            <option value="Annulled">Annulled</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Single / Solo Parent">Single / Solo Parent</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="citizenship" class="col-sm-5 col-md-5 col-lg-5 col-xl-5 col-form-label">Citizenship *</label>
                    <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7">
                        <input id="citizenship" type="text" name="citizenship" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" v-model="vm_tab1.citizenship" />
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="religion" class="col-sm-5 col-md-5 col-lg-5 col-xl-5 col-form-label">Religion *</label>
                    <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7">
                    <input id="religion" type="text" name="religion" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" v-model="vm_tab1.religion" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="bday" class="col-sm-5 col-md-5 col-lg-5 col-xl-5 col-form-label">Birth Date *</label>
                    <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="la la-calendar"></i>
                            </span>
                            <input type="text" id="m_datepicker-birthdate" name="bday" class="form-control m-input" maxlength="12" size="12" autocomplete="off" data-validation="required" v-model="vm_tab1.bday" />
                        </div>
                        <span class="m-form__help pull-right">Date Format: YYYY-MM-DD</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-10">
            <div class="col-xs-10 col-sm-10 col-md-12 col-lg-12">
                <div class="form-group m-form__group row">
                    <label for="birthplace" class="col-md-3 col-xs-6 col-sm-6 col-lg-3 col-xl-3 col-form-label">Place of Birth *</label>
                    <div class="col-md-9 col-xs-6 col-sm-6 col-lg-9 col-xl-7">
                    <input id="birthplace" type="text" name="birthplace" class="form-control m-input" maxlength="50" size="50" autocomplete="off" data-validation="required" v-model="vm_tab1.birthplace" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-10 col-sm-10 col-md-12 col-lg-12">
                <div class="form-group m-form__group row">
                    <label for="languages" class="col-md-3 col-xs-6 col-sm-6 col-lg-3 col-xl-3 col-form-label">Languages *</label>
                    <div class="col-md-9 col-xs-6 col-sm-6 col-lg-9 col-xl-7">
                    <input id="languages" type="text" name="languages" class="form-control m-input" maxlength="200" size="200" autocomplete="off" data-validation="required" v-model="vm_tab1.languages" />
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="height" class="col-sm-5 col-md-5 col-lg-5 col-xl-5 col-form-label">Height *</label>
                    <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7">
                        <input id="height" type="text" name="height" class="form-control m-input" maxlength="10" size="10" autocomplete="off" data-validation="required" v-model="vm_tab1.height" />
                        <span class="m-form__help pull-right">Format: FEET INCHES</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="weight" class="col-sm-5 col-md-5 col-lg-5 col-xl-5 col-form-label">Weight *</label>
                    <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7">
                        <input id="weight" type="text" name="weight" class="form-control m-input" maxlength="10" size="10" autocomplete="off" data-validation="required" v-model="vm_tab1.weight" />
                        <span class="m-form__help pull-right">Format: Kilograms</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="complexion" class="col-sm-5 col-md-5 col-lg-5 col-xl-5 col-form-label">Complexion *</label>
                    <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7">
                        <input id="complexion" type="text" name="complexion" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" v-model="vm_tab1.complexion" />
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="hair_color" class="col-sm-5 col-md-5 col-lg-5 col-xl-5 col-form-label">Hair Color *</label>
                    <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7">
                        <input id="hair_color" type="text" name="hair_color" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" v-model="vm_tab1.hair_color" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="bloodtype" class="col-sm-5 col-md-5 col-lg-5 col-xl-5 col-form-label">Blood Type *</label>
                    <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7">
                        <input id="bloodtype" type="text" name="bloodtype" class="form-control m-input" maxlength="30" size="30" autocomplete="off" data-validation="required" v-model="vm_tab1.bloodtype" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if(in_array("save", $this->core_layout->getCurrentActions())): ?>
    <div class="m-portlet__foot m-portlet__foot--fit">
        <div class="m-form__actions">
            <div class="row">
                <div class="col-12 text-right">
                    <button type="submit" class="btn btnSave btn-primary m-btn m-btn--air m-btn--custom btn-submit"><i class="la la-check mr-2"></i>Save</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</form>
<input type="hidden" id="change_personal_info">
<script>

</script>
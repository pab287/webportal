<style>
   .custom-file{
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
   }

</style>

<!-- 
    NOTE: added :value to input fields for importing data from crs
 -->

<?php // echo site_url("hris/masterfile/add_employee_personal_information"); ?>
<form id="frmAddEmployeeData" class="m-form m-form--fit m-form--label-align-right" method="post" action="<?php echo site_url("hris/masterfile/add_employee_personal_information"); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="pic_filename" value="" v-model="vm_tab1.pic_filename" />
    <input type="hidden" name="require_clearance" v-model="vm_hire.require_clearance" />
    <div class="m-portlet__body">
        <template v-if="vm_hire.require_clearance == 1">
            <div class="row m--margin-bottom-10">
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="clearance_type" class="col-sm-5 col-md-5 col-lg-5 col-xl-5 col-form-label">CLEARANCE TYPE</label>
                        <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7">
                            <select id="clearance_type" class="form-control select2" name="clearance_type" data-validation="required" >
                                <option value="police_clearance">Police Clearance</option>
                                <option value="nbi">NBI Clearance</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="type" class="col-sm-6 col-md-4 col-lg-5 col-xl-5 col-form-label">File <span style="color: red;">*</span></label>
                        <div class="col-sm-6 col-md-8 col-lg-7 col-xl-7">
                            <div class="custom-file">
                                <input type="file" name="files" multiple id="documentupload" class="custom-file-input">
                                <input type="hidden" id="path" name="path"/>
                                <input type="hidden" id="filename" name="filename"/>
                                <span class="custom-file-control" id="file_append"></span>
                            </div>
                            <input type="hidden" id="document_names" name="document_names">	
                        <span class="m-form__help m--font-danger m--font-boldest">This information is important! Please select at least two .pdf file to upload.</span>
                        <div id="picture"></div><br>
                        </div>			
                    </div>
                </div>
            </div>
            <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        </template>
        <template v-if="vm_tab1.is_hiring === true">
            <input type="hidden" name="is_hiring" value="true" />
            <input type="hidden" name="application_id" v-model="vm_hire.id" />
            <input type="hidden" name="company_id" v-model="vm_hire.company_id" />
            <input type="hidden" name="department_id" v-model="vm_hire.department_id" />
            <input type="hidden" name="position" v-model="vm_hire.position_id" />
            <input type="hidden" name="level" v-model="vm_hire.type" />
            <input type="hidden" name="body_id" v-model="vm_crs.body_id" />
            <div class="row m--margin-bottom-10">
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="company" class="col-sm-6 col-md-4 col-lg-5 col-xl-5 col-form-label">Company:</label>
                        <div class="col-sm-6 col-md-8 col-lg-7 col-xl-7">
                        <p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="vm_hire.company" disabled>&nbsp;</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="department" class="col-sm-6 col-md-4 col-lg-5 col-xl-5 col-form-label">Department:</label>
                        <div class="col-sm-6 col-md-8 col-lg-7 col-xl-7">
                        <p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="vm_hire.department" disabled>&nbsp;</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row m--margin-bottom-10">
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="position" class="col-sm-6 col-md-4 col-lg-5 col-xl-5 col-form-label">Position:</label>
                        <div class="col-sm-6 col-md-8 col-lg-7 col-xl-7">
                        <p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="vm_hire.position" disabled>&nbsp;</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="type" class="col-sm-6 col-md-4 col-lg-5 col-xl-5 col-form-label">Level / Ranking:</label>
                        <div class="col-sm-6 col-md-8 col-lg-7 col-xl-7">
                        <p class="form-control m-input custom-p_textarea m--margin-bottom-0" v-text="vm_hire.type" disabled>&nbsp;</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        </template>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                <label for="firstname" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">First Name *</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <input id="firstname" type="text" name="firstname" :value="vm_crs.fname" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" />
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="middlename" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Middle Name</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <input id="middlename" type="text" name="middlename" :value="vm_crs.mname" class="form-control m-input" placeholder="( Optional )" maxlength="25" size="25" autocomplete="off" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="lastname" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Last Name *</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <div class="position-relative search-with-dropdown-container">
                            <input class="form-control m-input" id="lastname" name="lastname" :value="vm_crs.lname" maxlength="25" size="25" autocomplete="off" data-validation="required"/>
                            <span class="m-form__help m--hide" id="emp_exist_notif" style="color: red; font-weight: 1000;">* This person already exists in the system</span>
                            <div class="position-absolute options-container invisible search-with-dropdown-suggestion-list" style='z-index: 1;'>
                                <ul class="employee-suggestion">
                                </ul>
                            </div>
                        </div>
                        <!-- <input id="lastname" type="text" name="lastname" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" /> -->
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="suffix" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Suffix</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <input id="suffix" type="text" name="suffix" :value="vm_crs.suff" class="form-control m-input" placeholder="( Optional )" maxlength="10" size="10" autocomplete="off" />
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="tel_no" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Telephone No</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7" style="z-index: 0;">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="la la-chain"></i>
                            </span>
                            <input id="tel_no" type="text" name="tel_no" :value="vm_crs.telephone_no" class="form-control m-input" placeholder="( Optional )" maxlength="11" size="11" autocomplete="off"/>
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
                            <input id="mobile_no" type="text" name="mobile_no" :value="vm_crs.contact" class="form-control m-input" placeholder="( Optional )" maxlength="11" size="11" autocomplete="off" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="email" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Email Address</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <input id="email" type="email" name="email" :value="vm_crs.email" class="form-control m-input" placeholder="( Optional )" maxlength="100" size="100" autocomplete="off" />
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
                        <input id="curr_addr" type="text" name="curr_addr" :value="vm_crs.curr_addr" class="form-control m-input" maxlength="200" size="200" autocomplete="off" data-validation="required" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-xs-10 col-sm-10 col-md-12 col-lg-12 col-xl-12">
                <div class="form-group m-form__group row">
                    <label for="prov_addr" class="col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-form-label">Permanent Address *</label>
                    <div class="col-xs-6 col-sm-6 col-md-8 col-lg-9 col-xl-7">
                        <input id="prov_addr" type="text" name="prov_addr" :value="vm_crs.prov_addr" class="form-control m-input" maxlength="200" size="200" autocomplete="off" data-validation="required" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-10 col-sm-10 col-md-12 col-lg-12 col-xl-12">
                <div class="form-group m-form__group row">
                    <label for="long_lat_coordinates" class="col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-form-label">Map Coordinates</label>
                    <div class="col-xs-6 col-sm-6 col-md-8 col-lg-9 col-xl-7">
                        <input id="long_lat_coordinates" type="text" name="long_lat_coordinates" class="form-control m-input" maxlength="100" size="100" autocomplete="off" />
                        <div class="m-form__help" style="font-size: 10px;">latitude and longitude map coordinates</div>
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
                                <input id="gender" type="radio" name="gender" data-validation="required" value="Male" />
                                Male<span></span>
                            </label>
                            <label class="m-checkbox">
                                <input id="gender" type="radio" name="gender" data-validation="required" value="Female" />
                                Female<span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="civil_stat" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Civil Status *</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <select id="civil_stat" class="form-control select2" name="civil_stat" data-validation="required">
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Separated">Separated</option>
                            <option value="Divorced">Divorced</option>
                            <option value="Annulled">Annulled</option>
                            <option value="Widowed">Widowed</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="citizenship" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Citizenship *</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <input id="citizenship" type="text" name="citizenship" :value="vm_crs.citizenship" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" />
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="religion" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Religion *</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                    <input id="religion" type="text" name="religion" :value="vm_crs.religion" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="bday" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Birth Date *</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="la la-calendar"></i>
                            </span>
                            <input id="bday" type="text" id="m_datepicker-birthdate" name="bday" :value="vm_crs.bday" class="form-control m-input" maxlength="12" size="12" autocomplete="off" data-validation="required" />
                        </div>
                        <span class="m-form__help pull-right" style="font-size: 10px;">Date Format: YYYY-MM-DD</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-10">
            <div class="col-xs-10 col-sm-10 col-md-12 col-lg-12 col-xl-12">
                <div class="form-group m-form__group row">
                    <label for="birthplace" class="col-md-3 col-xs-6 col-sm-6 col-lg-3 col-xl-3 col-form-label">Place of Birth *</label>
                    <div class="col-md-9 col-xs-6 col-sm-6 col-lg-9 col-xl-7">
                    <input id="birthplace" type="text" name="birthplace" :value="vm_crs.birthplace" class="form-control m-input" maxlength="50" size="50" autocomplete="off" data-validation="required" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-10 col-sm-10 col-md-12 col-lg-12 col-xl-12">
                <div class="form-group m-form__group row">
                    <label for="languages" class="col-md-3 col-xs-6 col-sm-6 col-lg-3 col-xl-3 col-form-label">Languages *</label>
                    <div class="col-md-9 col-xs-6 col-sm-6 col-lg-9 col-xl-7">
                    <input id="languages" type="text" name="languages" :value="vm_crs.languages" class="form-control m-input" maxlength="200" size="200" autocomplete="off" data-validation="required" />
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="height" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Height *</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <input id="height" type="text" name="height" :value="vm_crs.height" class="form-control m-input" maxlength="10" size="10" autocomplete="off" data-validation="required" />
                        <span class="m-form__help pull-right">Format: FEET INCHES</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="weight" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Weight *</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <input id="weight" type="text" name="weight" :value="vm_crs.weight" class="form-control m-input" maxlength="10" size="10" autocomplete="off" data-validation="required" />
                        <span class="m-form__help pull-right">Format: Kilograms</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="complexion" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Complexion *</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <input id="complexion" type="text" name="complexion" :value="vm_crs.complexion" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" />
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="hair_color" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Hair Color *</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <input id="hair_color" type="text" name="hair_color" :value="vm_crs.hair_color" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="bloodtype" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Blood Type *</label>
                    <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                        <input id="bloodtype" type="text" name="bloodtype" :value="vm_crs.bloodtype" class="form-control m-input" maxlength="30" size="30" autocomplete="off" data-validation="required" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="m-portlet__foot m-portlet__foot--fit">
        <div class="m-form__actions">
            <div class="row">
                <div class="col-12 text-right">
                    <button type="submit" class="btn btnSave btn-primary m-btn m-btn--air m-btn--custom btn-submit"><i class="la la-check mr-2"></i>Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
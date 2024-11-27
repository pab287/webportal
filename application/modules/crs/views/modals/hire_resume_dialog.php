<div class="modal fade" id="modal-form_hire" role="dialog">
    <div class="modal-dialog modal-lg">
        <form action="#" id="form_hire"
              class="form-horizontal m-form m-form--fit m-form--label-align-right has-validation-callback"
              enctype="multipart/form-data" onsubmit="hireResumeInfo(this); return false;">
            <div class="modal-content" id="hire-content">
                <div class="modal-header">
                    <h3 class="modal-title">Hire</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body form">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="application_id" v-model="row.id" />
                    <input type="hidden" name="company_id" v-model="position.company_id" />
                    <input type="hidden" name="department_id" v-model="position.department_id" />
                    <input type="hidden" name="level" v-model="position.level" />
                    <input type="hidden" name="request_id" v-model="position.request_id" />
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Position <span class="text-danger">*</span></label>
                                <select name="position" id="position" class="form-control" data-validation="required"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Company</label>
                                <p class="form-control" v-text="position.company">&nbsp;</p>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Level / Ranking</label>
                                <p class="form-control" v-text="position.level">&nbsp;</p>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Department</label>
                                <p class="form-control" v-text="position.department">&nbsp;</p>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row mt-3">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">First name *</label>
                                <input id="firstname" type="text" name="firstname" 
                                    maxlength="25" size="25" autocomplete="off" 
                                    data-validation="required" class="form-control m-input" 
                                    v-model="row.firstname" />
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Middle name</label>
                                <input id="middlename" type="text" name="middlename" 
                                    maxlength="25" size="25" autocomplete="off" 
                                    class="form-control m-input" placeholder="( OPTIONAL )" />
                            </div>
                        </div>
                        
                    </div>
                    <div class="row mt-3">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Last name *</label>
                                <input id="lastname" type="text" name="lastname" 
                                    maxlength="25" size="25" autocomplete="off"  class="form-control m-input" 
                                    v-model="row.lastname" />
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Suffix</label>
                                <input id="suffix" type="text" name="suffix" 
                                    maxlength="25" size="25" autocomplete="off" 
                                    class="form-control m-input" placeholder="( Optional )" />
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row mt-3">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Telephone no</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="la la-chain"></i>
                                    </span>
                                    <input id="tel_no" type="text" name="tel_no" placeholder="( Optional )" 
                                        maxlength="11" size="11" autocomplete="off" class="form-control m-input" />
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                                <label class="control-label">Mobile no</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="la la-chain"></i>
                                    </span>
                                    <input id="mobile_no" type="text" name="mobile_no" placeholder="( Optional )" 
                                        maxlength="11" size="11" autocomplete="off" class="form-control m-input" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Email Address</label>
                                <input id="email" type="email" name="email" placeholder="( Optional )" maxlength="100" size="100" autocomplete="off" class="form-control m-input" />
                                <span class="m-form__help">We'll never share your email with anyone else</span>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row mt-3">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Current Address *</label>
                                <input id="curr_addr" type="text" name="curr_addr" maxlength="200" size="200" autocomplete="off" data-validation="required" class="form-control m-input" />
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Permanent Address *</label>
                                <input id="prov_addr" type="text" name="prov_addr" maxlength="200" size="200" autocomplete="off" data-validation="required" class="form-control m-input" />
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Map Coordinates</label>
                                <input id="long_lat_coordinates" type="text" name="long_lat_coordinates" maxlength="100" size="100" autocomplete="off" class="form-control m-input" />
                                <div class="m-form__help">latitude and longitude map coordinates</div>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row mt-3">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Gender *</label>
                                <div class="m-checkbox-inline">
                                    <label class="m-checkbox">
                                        <input id="gender" type="radio" name="gender" data-validation="required" value="Male" checked />
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
                    <div class="row mt-3">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Citizenship *</label>
                                <input id="citizenship" type="text" name="citizenship" maxlength="25" size="25" autocomplete="off" data-validation="required" class="form-control m-input" />
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Civil Status *</label>
                                <select id="civil_stat" name="civil_stat" data-validation="required" class="form-control select2">
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
                    <div class="row mt-3">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Religion *</label>
                                <input id="religion" type="text" name="religion" maxlength="25" size="25" autocomplete="off" data-validation="required" class="form-control m-input" />
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Birth Date *</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                    <input id="bday" type="text" name="bday" maxlength="12" size="12" 
                                    autocomplete="off" data-validation="required" class="form-control m-input" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row mt-3">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Place of Birth *</label>
                                <input id="birthplace" type="text" name="birthplace" maxlength="50" size="50" autocomplete="off" data-validation="required" class="form-control m-input" />
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Languages *</label>
                                <input id="languages" type="text" name="languages" maxlength="200" size="200" autocomplete="off" data-validation="required" class="form-control m-input" />
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row mt-3">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Height *</label>
                                <input id="height" type="text" name="height" maxlength="10" size="10" autocomplete="off" data-validation="required" class="form-control m-input" />
                                <span class="m-form__help pull-right">Format: FEET INCHES</span>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Weight *</label>
                                <input id="weight" type="text" name="weight" maxlength="10" size="10" autocomplete="off" data-validation="required" class="form-control m-input" />
                                <span class="m-form__help pull-right">Format: Kilograms</span>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Complexion *</label>
                                <input id="complexion" type="text" name="complexion" maxlength="25" size="25" autocomplete="off" data-validation="required" class="form-control m-input" />
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Hair Color *</label>
                                <input id="hair_color" type="text" name="hair_color" maxlength="25" size="25" autocomplete="off" data-validation="required" class="form-control m-input" />
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Blood Type *</label>
                                <input id="bloodtype" type="text" name="bloodtype" maxlength="30" size="30" autocomplete="off" data-validation="required" class="form-control m-input" />
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
                <div class="modal-footer">
                    <button type="submit" id="btnSave" class="btn btn-primary m-btn m-btn--custom m-btn--icon btnSave">Save</button>
                    <button type="reset" class="btn btn-danger m-btn m-btn--custom m-btn--icon btnCancel" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
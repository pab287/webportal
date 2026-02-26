<div class="m-content">
    <div class="row">
        <div class="col-12">
            <div class="m-portlet mt-2" id="m_portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="la la-users"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Registration
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="row mt-2">
                        <div class="col">
                            <div class="m-portlet__head-title">
                                <h5 class="m-portlet__head-text">PERSONAL INFORMATION</h5>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label required">First name</label>
                                <input type="text" name="firstname" id="firstname" placeholder="" class="form-control" data-validation="required" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label">Middle name</label>
                                <input type="text" name="middlename" id="middle" placeholder="(optional)" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label required">Last name</label>
                                <input type="text" name="lastname" id="lastname" placeholder="" class="form-control" data-validation="required" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label">Suffix</label>
                                <input type="text" name="suffix" id="suffix" placeholder="(optional)" class="form-control" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label">School</label>
                                <select id="school_id" name="schools[]" multiple="multiple" class="form-control"></select>
                                <label class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">You can select multiple school.</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label">Course</label>
                                <select id="course_id" name="courses[]" multiple="multiple" class="form-control"></select>
                                <label class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">You can select multiple course.</label>
                            </div>
                        </div>
                    </div>
                    <div class="m-separator m-separator m-separator--lg m-separator--dashed"></div>
                    <div class="row mt-2">
                        <div class="col">
                            <div class="m-portlet__head-title">
                                <h5 class="m-portlet__head-text">ADDITIONAL INFORMATION</h5>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label required">Citizenship</label>
                                <input type="text" name="citizenship" id="citizenship" placeholder="" class="form-control" data-validation="required" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label required">Religion</label>
                                <input type="text" name="religion" id="religion" placeholder="" class="form-control" data-validation="required" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label required">Civil Status</label>
                                <select id="civil_status" name="civil_status" class="form-control" data-validation="required">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label required">Height</label>
                                <input type="text" name="height" id="height" placeholder="" class="form-control" data-validation="required" autocomplete="off">
                                <label class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">Feet/Inches</label>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label required">Weight</label>
                                <input type="text" name="weight" id="weight" placeholder="" class="form-control" data-validation="required" autocomplete="off">
                                <label class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">Kilograms</label>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label required">Birthdate</label>
                                <div class="input-group date">
                                    <input class="form-control m-input" type="text" id="birthdate" name="birthdate" placeholder="mm/dd/yyyy" maxlength="12" size="12" data-validation="required" oninput="formatDate(this)" autocomplete="off">
                                    <span class="input-group-addon">
                                    <i class="la la-calendar glyphicon-th"></i>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label required">Gender</label>
                                <select id="gender" name="gender" class="form-control" data-validation="required">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="m-separator m-separator m-separator--lg m-separator--dashed"></div>
                    <div class="row mt-2">
                        <div class="col">
                            <div class="m-portlet__head-title">
                                <h5 class="m-portlet__head-text">CONTACT DETAILS </h5>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label required">Mobile Number</label>
                                <input type="text" name="contact_no" id="contact_no" placeholder="" class="form-control" data-validation="required" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label">Email</label>
                                <input type="email" name="email" id="email" placeholder="(optional)" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label">Telephone Number</label>
                                <input type="text" name="tel_no" id="tel_no" placeholder="(optional)" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-control-label required">Current Address</label>
                                <input type="text" name="address" id="address" class="form-control" data-validation="required" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-control-label required">Permanent Address</label>
                                <input type="text" name="permanent_address" id="permanent_address" class="form-control" data-validation="required" autocomplete="off">
                            </div>
                        </div>
                        <!-- <div class="col-12">
                            <div class="form-group">
                                <label class="form-control-label">Map Coordinates</label>
                                <input type="text" name="long_lat_coordinates" id="long_lat_coordinates" class="form-control" placeholder="(OPTIONAL)" autocomplete="off">
                            </div>
                        </div> -->
                    </div>
                    <div class="m-separator m-separator m-separator--lg m-separator--dashed"></div>
                    <div class="row mt-2">
                        <div class="col">
                            <div class="m-portlet__head-title">
                                <h5 class="m-portlet__head-text">APPLICATION INFORMATION</h5>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label required">Recruitment Source</label>
                                <select id="recruitment" name="recruitment" class="form-control" data-validation="required">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label required">Position Applied FOR</label>
                                <select id="position_id" name="positions[]" multiple="multiple" class="form-control" data-validation="required">
                                </select>
                                <label class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1"
                                       style="text-transform: none;">Note: You can select multiple position.</label>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 referral d-none">
                            <div class="form-group">
                                <label class="form-control-label required">referral</label>
                                <select id="referral" name="referral" class="form-control"  data-validation="required">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label required">Date of Application</label>
                                <div class='input-group date'>
                                    <input class="form-control m-input" type="text" id="applied_dt" name="applied_dt" placeholder="mm/dd/yyyy" maxlength="12" size="12" data-validation="required" autocomplete="off" readonly/>
                                    <span class="input-group-addon">
                                    <i class="la la-calendar glyphicon-th"></i>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-separator m-separator m-separator--lg m-separator--dashed"></div>
                    <div class="row mt-2">
                        <div class="col">
                            <div class="m-portlet__head-title">
                                <h5 class="m-portlet__head-text">RESUME UPLOAD</h5>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-4">
                        <span class="btn btn-success fileinput-button">
                            <i class="glyphicon glyphicon-plus"></i>
                            <span>Select file</span>
                            <input type="file" id="fileupload" name="files[]" accept=".pdf, .docx, application/pdf, .jpg" multiple>
                        </span>
                    </div>
                    <div id="new_preview" class="m-widget2 row mt-">
                        <template v-if="uploadedFiles.length >= 1">
                            <div class="m-portlet m-portlet--rounded col-lg-12">
                                <div class="m-portlet__head">
										<div class="m-portlet__head-caption">
											<div class="m-portlet__head-title">
												<h3 class="m-portlet__head-text">
													Attachments
												</h3>
											</div>
										</div>
									</div>
                                <div class="m-portlet__body row">
                                    <template v-for="(item, index) in uploadedFiles">
                                        <div v-bind:class="getClass(item.type)">
                                            <div class="m-widget4__item m-0 p-0">
                                                <div class="m-widget4__img m-widget4__img--icon">
                                                    <img v-bind:src="getExtension(item.type)" alt="" height="50" width="50">
                                                </div>
                                                <div class="m-widget2__desc">
                                                <span class="m-widget4__text">{{ item.name.length > 20 ? item.name.slice(0, 20) + '...' : item.name }}</span>
                                                </div>
                                                <div class="m-widget2__actions">
                                                    <button type="button" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView" v-on:click="fileDelete(item.id)">
                                                        <i class="m-nav__link-icon flaticon-circle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="m-portlet m-portlet--rounded col-lg-12">
                                <div class="m-portlet__head">
                                    <div class="m-portlet__head-caption">
                                        <div class="m-portlet__head-title">
                                            <h3 class="m-portlet__head-text">
                                                Attachments
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-portlet__body row">
                                    <strong><h5>NO ATTACHMENTS</h5></strong>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form class="m-content" id="New_Add_File" onsubmit="saveResumeInfo(this, event); return false;" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

    <div class="row">
        <div class="col-xl-12 col-lg-12 order-2 order-xl-1">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <!--<i class="flaticon-truck"></i>-->
                                <a type="button" href="resume"
                                data-toggle="m-tooltip" data-original-title="Back to Master file"
                                data-skin="dark" data-delay='{"show": 600}'
                                class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
                            <h3 class="m-portlet__head-text">
                                NEW RESUME
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Firstname <span class="text-danger">*</span></label>
                                <input type="text" name="firstname" id="firstname" placeholder="" class="form-control"
                                       data-validation="required" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Middlename</label>
                                <input type="text" name="middlename" id="middle" placeholder="" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Lastname <span class="text-danger">*</span></label>
                                <input type="text" name="lastname" id="lastname" placeholder="" class="form-control"
                                       data-validation="required" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Suffix</label>
                                <input type="text" name="suffix" id="suffix" placeholder="" class="form-control" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Contact no <span class="text-danger">*</span></label>
                                <input type="text" name="contact_no" id="contact_no" placeholder="" class="form-control"
                                    data-validation="required" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Recruitment Source <span class="text-danger">*</span></label>
                                <select id="recruitment" name="recruitment"
                                        class="form-control" data-validation="required">
                                    <option value=""></option>
                                    <option value="Mynimo">MYNIMO</option>
                                    <option value="Jobstreet">JOBSTREET</option>
                                    <option value="Facebook">FACEBOOK</option>
                                    <option value="Linkedin">LINKEDIN</option>
                                    <option value="Walk In">WALK IN</option>
                                    <option value="REFERRAL">REFERRAL</option>
                                    <option value="JOB FAIR">JOB FAIR</option>
                                    <option value="Indeed">INDEED</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Eligible Position/Tag <span class="text-danger">*</span></label>
                                <select id="tag_id" name="tags[]" data-placeholder="" multiple="multiple" class="form-control"
                                        data-validation="required">
                                </select>
                                <label class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1"
                                       style="text-transform: none;">Note: You can select multiple tags.</label>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value=""></option>
                                    <option value="pending">PENDING</option>
                                    <option value="forinterview">FOR INTERVIEW</option>
                                    <option value="doneinterview">DONE INTERVIEW</option>
                                    <option value="pooling">POOLING</option>
                                    <option value="blacklisted">BLACKLISTED</option>
                                    <option value="hired">HIRED</option>
                                    <option value="shortlisted">SHORTLISTED</option>
                                    <option value="eligible">ELIGIBLE</option>
                                    <option value="disqualified">DISQUALIFIED</option>
                                    <option value="reserve">RESERVE</option>
                                    <option value="overqualified">OVERQUALIFIED</option>
                                    <option value="disregard">DISREGARD</option>
                                    <option value="fortesting">FOR TESTING</option>
                                    <option value="foronboarding">FOR ONBOARDING</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 m--hide interview_dt">
                            <div class="form-group">
                                <label class="control-label">Interview Date</label>
                                <input type="text" name="interview_dt" id="interview_dt" placeholder="" class="form-control date" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">School</label>
                                <select id="school_id" name="schools[]" data-placeholder="" multiple="multiple" class="form-control">
                                </select>
                                <label class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1"
                                       style="text-transform: none;">Note: You can select multiple school.</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Course</label>
                                <select id="course_id" name="courses[]" data-placeholder="" multiple="multiple" class="form-control"></select>
                                <label class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1"
                                       style="text-transform: none;">Note: You can select multiple course.</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Position <span class="text-danger">*</span></label>
                                <select id="position_id" name="positions[]" data-placeholder="" multiple="multiple" class="form-control"
                                        data-validation="required">
                                </select>
                                <label class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1"
                                       style="text-transform: none;">Note: You can select multiple position.</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Date of Application <span class="text-danger">*</span></label>
                                <div class='input-group date' id="applied_dt">
                                    <input class="form-control m-input" type="text" name="applied_dt" data-validation="required"
                                           autocomplete="off"/>
                                    <span class="input-group-addon">
                                    <i class="la la-calendar glyphicon-th"></i>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label class="control-label">Remarks</label>
                        <textarea name="description" id="description" placeholder="" class="form-control"></textarea>
                    </div>

                    <div class="form-group mt-4">
                        <span class="btn btn-success fileinput-button">
                            <i class="glyphicon glyphicon-plus"></i>
                            <span>Select file</span>
                            <input type="file" id="temp_fileupload" name="files" multiple>
                        </span>
                        <div id="progress" class="progress mt-2 d-none">
                            <div class="progress-bar progress-bar-success"></div>
                        </div>
                        <div id="temp_files" class="files"></div>
                    </div>
                    <!-- sample -->
                    <div id="new_preview" class="m-widget2 row mt-3">
                        <template v-if="count">
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
                                    <template v-for="(item, index) in rows">
                                        <div v-bind:class="getClass(item.filename)">
                                            <div class="m-widget4__item m-0 p-0">
                                                <div class="m-widget4__img m-widget4__img--icon">
                                                    <img v-bind:src="getExtension(item.filename)" alt="" height="50" width="50">
                                                </div>
                                                <div class="m-widget4__info">
                                                    <span class="m-widget4__text" v-text="item.filename">
                                                    </span>
                                                </div>
                                                <div class="m-widget2__actions">
                                                    <div class="m-widget2__actions-nav">
                                                        <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
                                                            <a href="#" class="m-dropdown__toggle">
                                                                <i class="la la-ellipsis-h"></i>
                                                            </a>
                                                            <div class="m-dropdown__wrapper">
                                                                <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 14.7032px;"></span>
                                                                <div class="m-dropdown__inner">
                                                                    <div class="m-dropdown__body">
                                                                        <div class="m-dropdown__content">
                                                                            <ul class="m-nav">
                                                                                <li class="m-nav__item">
                                                                                    <a href="#" class="m-nav__link" v-on:click="fileDelete(item.id)">
                                                                                        <i class="m-nav__link-icon flaticon-circle"></i>
                                                                                        <span class="m-nav__link-text">
                                                                                            Remove
                                                                                        </span>
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                <!-- end sample -->
                </div>
                <div class="m-portlet__foot text-right">
                    <button id="addfilereset" class="btn btn-md btn-default m-btn btnNew mr-3" type="button">Reset
                    </button>
                    <button class="btn btn-md btn-primary m-btn">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
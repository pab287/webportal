<div class="m-content">
    <div class="row">
        <div class="col-xl-12 col-lg-12 order-2 order-xl-1">
            <div class="m-portlet">
            
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" href="../resume"
                                data-toggle="m-tooltip" data-original-title="Back to Master file"
                                data-skin="dark" data-delay='{"show": 600}'
                                class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
                            <h3 class="m-portlet__head-text">
                                EDIT RESUME
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body">
                <form id="frm-edit-resume-dialog" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="id">
                    <input type="hidden" name="current_filename">
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
                                <input type="text" name="middlename" id="middlename" placeholder="" class="form-control" autocomplete="off">
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
                                <select id="edit_resume_recruitment" name="recruitment"
                                        class="form-control" data-validation="required">
                                    <option value=""></option>
                                    <option value="Mynimo">MYNIMO</option>
                                    <option value="Jobstreet">JOBSTREET</option>
                                    <option value="Facebook">FACEBOOK</option>
                                    <option value="Linkedin">LINKEDIN</option>
                                    <option value="Walk In">WALK IN</option>
                                    <option value="REFERRAL">REFERRAL</option>
                                    <option value=">JOB FAIR">JOB FAIR</option>
                                    <option value=">Indeed">INDEED</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Eligible Position/Tag <span class="text-danger">*</span></label>
                                <select id="edit_resume_tag_id" name="tags[]" data-placeholder="" multiple="multiple" class="form-control"
                                        data-validation="required">
                                </select>
                                <label class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1"
                                    style="text-transform: none;">Note: You can select multiple tags.</label>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Status</label>
                                <select id="edit_resume_status" name="status" class="form-control">
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
                                <input type="text" name="interview_dt" id="interview_dt" placeholder="SELECT DATE" class="form-control date" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 m--hide hired_dt">
                            <div class="form-group">
                                <label class="control-label">Hired Date<span class="text-danger">*</span></label>
                                <input type="text" name="hired_dt" id="hired_dt" placeholder="SELECT DATE" class="form-control date" autocomplete="off"  data-validation="required">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3 m--hide" id="reason_blacklisted">
                        <label class="control-label">Reason for blacklisting *</label>
                        <textarea name="blacklist_remarks" id="blacklist_remarks"  class="form-control" data-validation="required"></textarea>
                    </div>

                    <div class="row mt-3">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">School</label>
                                <select id="edit_resume_school_id" name="schools[]" data-placeholder="" multiple="multiple" class="form-control">
                                </select>
                                <label class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1"
                                    style="text-transform: none;">Note: You can select multiple school.</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Course</label>
                                <select id="edit_resume_course_id" name="courses[]" data-placeholder="" multiple="multiple" class="form-control"></select>
                                <label class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1"
                                    style="text-transform: none;">Note: You can select multiple course.</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Position <span class="text-danger">*</span></label>
                                <select id="edit_resume_position_id" name="positions[]" data-placeholder="" multiple="multiple" class="form-control"
                                        data-validation="required">
                                </select>
                                <label class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1"
                                    style="text-transform: none;">Note: You can select multiple position.</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Date of Application <span class="text-danger">*</span></label>
                                <div class='input-group date' id="edit_resume_applied_dt">
                                    <input class="form-control m-input" type="text" name="applied_dt" data-validation="required" autocomplete="off"/>
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
                    </form>
                    <div class="m-separator m-separator--dashed d-xl-12"></div> 
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group m-form__group row">
                                <div class="m_datatable  m-datatable--default  m-datatable--scroll col-12">
                                <table class="table table-striped table-bordered" id="edit_file_table" width="100%">
                                    <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>File Size</th>
                                        <th>Uploaded By</th>
                                        <th>Date Uploaded</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__foot text-right">
                    <button id="editfilereset" class="btn btn-md btn-default m-btn btnNew mr-3" type="button">Reset
                    </button>
                    <button id="saveEdit" class="btn btn-md btn-primary m-btn">Save</button>
                </div>
            </div>

            
        </div>
    </div>
</div>

<?php $this->load->view("modals/edit_file_modal.php"); ?>
<?php $this->load->view("modals/edit_view_file_modal.php"); ?>


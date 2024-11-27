<div class="modal fade" id="modal-edit-resume-dialog" role="dialog">
    <div class="modal-dialog modal-lg">
        <form action="#" id="frm-edit-resume-dialog"
              class="form-horizontal"
              enctype="multipart/form-data" onsubmit="editResumeInfo(this); return false;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">EDIT RESUME</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body form">
                    <input type="hidden" name="id">
                    <input type="hidden" name="current_filename">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Firstname <span class="text-danger">*</span></label>
                                <input type="text" name="firstname" id="firstname" placeholder="" class="form-control"
                                       data-validation="required">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Lastname <span class="text-danger">*</span></label>
                                <input type="text" name="lastname" id="lastname" placeholder="" class="form-control"
                                       data-validation="required">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Recruitment Source <span class="text-danger">*</span></label>
                                <select id="edit_resume_recruitment" name="recruitment" data-placeholder="Select Recruitment Soruce"
                                        class="form-control" data-validation="required">
                                    <option value=""></option>
                                    <option value="Mynimo">MYNIMO</option>
                                    <option value="Jobstreet">JOBSTREET</option>
                                    <option value="Walk In">WALK IN</option>
                                    <option value="REFERRAL">REFERRAL</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Status</label>
                                <select id="edit_resume_status" name="status" class="form-control">
                                    <option value=""></option>
                                    <option value="pending">PENDING</option>
                                    <option value="forinterview">FOR INTERVIEW</option>
                                    <option value="doneinterview">DONE INTERVIEW</option>
                                    <option value="blacklisted">BLACKLISTED</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Tag <span class="text-danger">*</span></label>
                                <select id="edit_resume_tag_id" name="tags[]" data-placeholder="" multiple="multiple" class="form-control"
                                        data-validation="required">
                                </select>
                                <label class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1"
                                       style="text-transform: none;">Note: You can select multiple tags.</label>
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

                    <div class="form-group mt-4">
                            <span class="btn btn-sm btn-success fileinput-button">
                                <i class="glyphicon glyphicon-plus"></i>
                                <span>ATTACH FILE</span>
                                <!-- The file input field used as target for the file upload widget -->
                                <input id="fileupload" type="file" name="files" onchange="getFilename(this, '#frm-edit-resume-dialog', true)">
                            </span>
                    </div>

                    <div class="form-group">
                        <label class="control-label">File Name</label>
                        <input id="filename" name="filename" placeholder="" class="form-control" type="text" disabled
                               data-validation="required">
                        <input id="doc_filename" placeholder="" class="form-control" type="hidden">
                    </div>
                </div><!-- /.modal-content -->
                <div class="modal-footer">
                    <button type="submit" id="btnSave"
                            class="btn btn-success m-btn m-btn--custom m-btn--icon btnNew">Save
                    </button>
                    <button type="reset" class="btn btn-danger m-btn m-btn--custom m-btn--icon  cancel" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modal_form_document" role="dialog">
    <div class="modal-dialog modal-xl">
        <form action="#" id="form_document"
              class="form-horizontal"
              enctype="multipart/form-data" onsubmit="saveResumeInfo(this, event); return false;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body form">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Firstname <span class="text-danger">*</span></label>
                                <input type="text" name="firstname" id="firstname" placeholder="" class="form-control"
                                       data-validation="required">
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">middlename <span class="text-danger">*</span></label>
                                <input type="text" name="middlename" id="middle" placeholder="" class="form-control"
                                       data-validation="required">
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Lastname <span class="text-danger">*</span></label>
                                <input type="text" name="lastname" id="lastname" placeholder="" class="form-control"
                                       data-validation="required">
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">suffix</label>
                                <input type="text" name="suffix" id="suffix" placeholder="" class="form-control"
                                       data-validation="required">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Recruitment Source <span class="text-danger">*</span></label>
                                <select id="recruitment" name="recruitment" data-placeholder="Select Source"
                                        class="form-control" data-validation="required">
                                    <option value=""></option>
                                    <option value="Mynimo">MYNIMO</option>
                                    <option value="Jobstreet">JOBSTREET</option>
                                    <option value="Walk In">WALK IN</option>
                                    <option value="REFERRAL">REFERRAL</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value=""></option>
                                    <option value="pending">PENDING</option>
                                    <option value="forinterview">FOR INTERVIEW</option>
                                    <option value="doneinterview">DONE INTERVIEW</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Tag <span class="text-danger">*</span></label>
                                <select id="tag_id" name="tags[]" data-placeholder="" multiple="multiple" class="form-control"
                                        data-validation="required">
                                </select>
                                <label class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1"
                                       style="text-transform: none;">Note: You can select multiple tags.</label>
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
                        <div id="progress" class="progress mt-2">
                            <div class="progress-bar progress-bar-success"></div>
                        </div>
                        <div id="temp_files" class="files"></div>
                    </div>
                </div><!-- /.modal-content -->
                <div class="modal-footer">
                    <button type="submit" id="btnSave"
                            class="btn btn-success m-btn m-btn--custom m-btn--icon btnNew">Save
                    </button>
                    <button id="newResumeClose" type="reset" class="btn btn-danger m-btn m-btn--custom m-btn--icon  cancel" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
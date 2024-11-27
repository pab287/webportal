<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form id="employee-update-medical-record" method="post" action="<?php echo site_url("hris/masterfile/update_medical_record"); ?>">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Edit Medical History/Record</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id" value="<?= $data->id ?>">
            <input type="hidden" name="current_filename" value="<?= $data->filename ?>">

            <div class="modal-body">
                <div class="form-group">
                    <label for="med_details" class="form-control-label">Details
                        <span class="text-danger">*</span>
                    </label>
                    <input id="med_details" name="med_details" type="text" maxlength="200" size="200" autocomplete="off" data-validation="required"
                           class="form-control m-input" value="<?= $data->med_details ?>"/>
                </div>
                <div class="form-group">
                    <label for="med_no" class="form-control-label">Medical No
                        <span class="text-danger">*</span>
                    </label>
                    <input id="med_no" name="med_no" type="text" maxlength="25" size="25" autocomplete="off" data-validation="required"
                           class="form-control m-input" value="<?= $data->med_no ?>"/>
                </div>
                <div class="form-group">
                    <label for="med_date" class="form-control-label">Date
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input id="med_date" type="text" name="med_date" maxlength="12" size="12" autocomplete="off" data-validation="required"
                               class="form-control m-input date" value="<?= $data->med_date ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="med_venue" class="form-control-label">Venue
                        <span class="text-danger">*</span>
                    </label>
                    <input id="med_venue" name="med_venue" type="text" maxlength="50" size="50" autocomplete="off" data-validation="required"
                           class="form-control m-input" value="<?= $data->med_venue ?>"/>
                </div>
                <div class="form-group">
                    <label for="med_physician" class="form-control-label">Physician
                        <span class="text-danger">*</span>
                    </label>
                    <input id="med_physician" name="med_physician" type="text" maxlength="50" size="50" autocomplete="off" data-validation="required"
                           class="form-control m-input" value="<?= $data->med_physician ?>"/>
                </div>
                <div class="form-group">
                    <label for="med_findings" class="form-control-label">Findings
                        <span class="text-danger">*</span>
                    </label>
                    <input id="med_findings" name="med_findings" type="text" maxlength="200" size="200" autocomplete="off" data-validation="required"
                           class="form-control m-input" value="<?= $data->med_findings ?>"/>
                </div>
                <div class="form-group">
                    <label for="med_remarks" class="form-control-label">Remarks</label>
                    <textarea id="med_remarks" name="remarks" rows="7" autocomplete="off" class="form-control m-input"
                              style="min-height: 120px; resize: vertical;"><?= $data->remarks ?></textarea>
                </div>
                <div class="form-group">
                    <label for="fileupload_medical" class="form-control-label">Attachment</label>
                    <span class="btn btn-success fileinput-button btn-sm pull-right">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>Select file</span>
                        <input type="file" id="fileupload_medical" name="files" onchange="setFilename(this, '#temp_fileupload')">
                    </span>
                    <p id="temp_fileupload" style='text-overflow: ellipsis; white-space: nowrap; overflow: hidden;' class="form-control m-input m--margin-top-10" disabled="disabled"><?= $data->filename ?></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
                <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
            </div>
        </form>
    </div>
</div>
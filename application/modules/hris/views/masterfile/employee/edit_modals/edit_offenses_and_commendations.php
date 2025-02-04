<div class="modal-dialog" role="dialog">
    <form id="employee-data-update-offenses-and-commendations"
          action="<?= base_url('hris/masterfile/update_offenses_and_commendations') ?>"
          enctype="multipart/form-data">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Edit Offense and Commendation</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <input type="hidden" name="id" value="<?= $data->id; ?>"/>
            <input type="hidden" name="current_filename" value="<?= $data->filename; ?>"/>
            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="modal-body">
                <div class="form-group">
                <label for="offcom_type" class="form-control-label">Type <span style="color: red;">*</span></label>
                    <select id="offcom_type_edit" name="offcom_type" data-validation="required" class="form-control m-input select2">
                        <option> </option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="offcom_date" class="form-control-label">Date <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input id="offcom_date" type="text" name="offcom_date" maxlength="12" size="12" autocomplete="off" data-validation="required" readonly
                               class="form-control m-input date" value="<?= $data->offcom_date ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="offcom_nature" class="form-control-label">Nature <span style="color: red;">*</span></label>
                    <textarea id="offcom_nature" name="offcom_nature" maxlength="200" size="200" autocomplete="off" data-validation="required"
                              rows="7"
                              class="form-control m-input" style="min-height: 120px; resize: vertical;"><?= $data->offcom_nature ?></textarea>
                </div>
                <div class="form-group">
                    <label for="offcom_action" class="form-control-label">Action Taken <span style="color: red;">*</span></label>
                    <textarea id="offcom_action" name="offcom_action" maxlength="200" size="200" autocomplete="off" data-validation="required"
                              class="form-control m-input" style="min-height: 120px; resize: vertical;"><?= $data->offcom_action ?></textarea>
                </div>
                <div class="form-group">
                    <label for="fileupload_offenses" class="form-control-label">Attachment <span style="color: red;">*</span></label>
                    <span class="btn btn-success fileinput-button btn-sm pull-right">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>Select file</span>
                        <input type="file" id="fileupload_offenses" name="files" onchange="setFilename(this, '#temp_fileupload')"
                               accept=".jpg, .jpeg, .png, .doc, .docx, .pdf">
                    </span>
                    <p id="temp_fileupload" class="form-control m-input m--margin-top-10" disabled="disabled">
                        <?= $data->filename ?>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
                <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
            </div>
        </div>
    </form>
</div>


///                        <!-- <option value="OFFENSE">Offenses</option>
                        <option value="COMMENDATION">Commendations</option>
                        <option value="NOTICES">Notices</option>
                        <option value="1ST OFFENSE">1st Offense</option>
                        <option value="2ND OFFENSE">2nd Offense</option>
                        <option value="3RD OFFENSE">3rd Offense</option>
                        <option value="4TH OFFENSE">4th Offense</option>
                        <option value="5TH OFFENSE">5th Offense</option>
                        <option value="6TH OFFENSE">6th Offense</option>
                        <option value="7TH OFFENSE">7th Offense</option>
                        <option value="8TH OFFENSE">8th Offense</option>
                        <option value="9TH OFFENSE">9th Offense</option>
                        <option value="10TH OFFENSE">10th Offense</option>
                        <option value="11TH OFFENSE">11th Offense</option>
                        <option value="12TH OFFENSE">12th Offense</option>
                        <option value="LAST WARNING">Last Warning</option>
                        <option value="FINAL WRITTEN WARNING">Final Written Warning</option>
                        <option value="VERBAL WARNING">Verbal warning</option>
                        <option value="WRITTEN WARNING">Written Warning</option>
                        <option value="COUNSELING">Counseling</option>
                        <option value="SUSPENSION">Suspension</option>
                        <option value="INCIDENT REPORT">Incident Report</option>
                        <option value="REMINDER NOTICE">Reminder Notice</option>
                        <option value="RETURN TO WORK NOTICE">Return to Work Notice</option>
                        <option value="NTE">NTE</option>
                        <option value="NOD">NoD</option>
                        <option value="NOTICE OF ADMINISTRATIVE HEARING">Notice of Administrative Hearing</option> -->///
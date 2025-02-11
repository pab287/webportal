<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add Offenses and Commendations</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-offenses" method="post" action="<?php echo site_url("hris/masterfile/set_modal_offenses"); ?>">
<input type="hidden" name="emp_id" value="<?php echo $id; ?>" />
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group">
		<label for="offcom_type" class="form-control-label">Type <span style="color: red;">*</span></label>
		<select id="offcom_type" name="offcom_type" data-validation="required" class="form-control m-input select2">
            <option></option>
            <!-- <option value="OFFENSE">Offenses</option>
            <option value="COMMENDATION">Commendations</option>
            <option value="NOTICES">Notices</option> -->
        </select>
	</div>
    <div class="form-group">
		<label for="offcom_date" class="form-control-label">Date <span style="color: red;">*</span></label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="offcom_date" type="text" name="offcom_date" maxlength="12" size="12" autocomplete="off" data-validation="required" class="form-control m-input" readonly/>
        </div>
	</div>
    <div class="form-group">
		<label for="offcom_nature" class="form-control-label">Nature <span style="color: red;">*</span></label>
		<textarea id="offcom_nature" name="offcom_nature" maxlength="200" size="200" autocomplete="off" data-validation="required" rows="7" class="form-control m-input" style="min-height: 120px; resize: vertical;"></textarea>
	</div>
    <div class="form-group">
		<label for="offcom_action" class="form-control-label">Sanction/Remarks <span style="color: red;">*</span></label>
		<textarea id="offcom_action" name="offcom_action" maxlength="200" size="200" autocomplete="off" data-validation="required" class="form-control m-input" style="min-height: 120px; resize: vertical;"></textarea>
	</div>
    <div class="form-group">
		<label for="fileupload_offenses" class="form-control-label">Attachment <span style="color: red;">*</span></label>
        <span class="btn btn-success fileinput-button btn-sm pull-right">
            <i class="glyphicon glyphicon-plus"></i>
            <span>Select file</span>
            <input type="file" id="fileupload_offenses" name="files" accept=".jpg,.jpeg,.png,.pdf" data-validation="required" />
            <input type="hidden" id="offenses_attachment" name="offenses_attachment">
        </span>
        <p id="temp_fileupload" style='text-overflow: ellipsis; white-space: nowrap; overflow: hidden;' class="form-control m-input m--margin-top-10" disabled="disabled">&nbsp;</p>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>
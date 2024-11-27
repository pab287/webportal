<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add Medical History/Record</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-medical_history" method="post" action="<?php echo site_url("crs/online_registration/set_modal_medical_history"); ?>">
<input type="hidden" name="applicant_id" value="<?php echo $id; ?>" />
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group">
		<label for="med_details" class="form-control-label">Details *</label>
		<input id="med_details" name="med_details" type="text" maxlength="200" size="200" autocomplete="off" data-validation="required" class="form-control m-input" />
    </div>
    <div class="form-group">
		<label for="med_no" class="form-control-label">Medical No *</label>
		<input id="med_no" name="med_no" type="text" maxlength="25" size="25" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="med_date" class="form-control-label">Date *</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="med_date" type="text" name="med_date" maxlength="12" size="12" autocomplete="off" data-validation="required" class="form-control m-input" />
        </div>
	</div>
    <div class="form-group">
		<label for="med_venue" class="form-control-label">Venue *</label>
		<input id="med_venue" name="med_venue" type="text" maxlength="50" size="50" autocomplete="off" data-validation="required" class="form-control m-input" />
    </div>
    <div class="form-group">
		<label for="med_physician" class="form-control-label">Physician *</label>
		<input id="med_physician" name="med_physician" type="text" maxlength="50" size="50" autocomplete="off" data-validation="required" class="form-control m-input" />
    </div>
    <div class="form-group">
		<label for="med_findings" class="form-control-label">Findings *</label>
		<input id="med_findings" name="med_findings" type="text" maxlength="200" size="200" autocomplete="off" data-validation="required" class="form-control m-input" />
    </div>
    <div class="form-group">
		<label for="med_remarks" class="form-control-label">Remarks</label>
		<textarea id="med_remarks" name="med_remarks" rows="7" autocomplete="off" class="form-control m-input" style="min-height: 120px; resize: vertical;"></textarea>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>
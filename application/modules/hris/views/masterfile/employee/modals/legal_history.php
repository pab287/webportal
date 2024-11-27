<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add Legal History/Record</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-legal_history" method="post" action="<?php echo site_url("hris/masterfile/set_modal_legal_history"); ?>">
<input type="hidden" name="emp_id" value="<?php echo $id; ?>" />
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group">
		<label for="leg_case_no" class="form-control-label">Case No *</label>
		<input id="leg_case_no" name="leg_case_no" type="number" maxlength="50" size="50" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="leg_details" class="form-control-label">Details *</label>
		<input id="leg_details" name="leg_details" type="text" maxlength="200" size="200" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="leg_case_date" class="form-control-label">Date *</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="leg_case_date" type="text" name="leg_case_date" maxlength="12" size="12" autocomplete="off" data-validation="required" class="form-control m-input" />
        </div>
	</div>
    <div class="form-group">
		<label for="leg_court_field" class="form-control-label">Court Filed *</label>
		<input id="leg_court_field" name="leg_court_field" type="text" maxlength="100" size="100" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="leg_prosecutor" class="form-control-label">Prosecutor *</label>
		<input id="leg_prosecutor" name="leg_prosecutor" type="text" maxlength="100" size="100" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="leg_status" class="form-control-label">Status *</label>
		<input id="leg_status" name="leg_status" type="text" maxlength="50" size="50" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>
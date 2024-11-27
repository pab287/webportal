<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add Work Experience</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-work_experience" method="post" action="<?php echo site_url("crs/online_registration/set_modal_work_experience"); ?>">
<input type="hidden" name="applicant_id" value="<?php echo $id; ?>" />
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group">
		<label for="work_from" class="form-control-label">From Year *</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="work_from" type="text" name="work_from" maxlength="4" size="4" autocomplete="off" data-validation="required" class="form-control m-input" />
        </div>
	</div>
    <div class="form-group">
		<label for="work_to" class="form-control-label">To Year *</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="work_to" type="text" name="work_to" maxlength="4" size="4" autocomplete="off" data-validation="required" class="form-control m-input" />
        </div>
	</div>
    <div class="form-group">
		<label for="work_company" class="form-control-label">Company *</label>
		<input id="work_company" name="work_company" type="text" maxlength="200" size="200" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="work_position" class="form-control-label">Position *</label>
		<input id="work_position" name="work_position" type="text" maxlength="200" size="200" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <!-- <div class="form-group">
		<label for="old_idno" class="form-control-label">ID No</label>
		<input id="old_idno" name="old_idno" type="text" maxlength="200" size="200" autocomplete="off" class="form-control m-input" />
	</div> -->
    <div class="form-group">
		<label for="work_status" class="form-control-label">Status *</label>
		<input id="work_status" name="work_status" type="text" maxlength="50" size="50" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="work_reason" class="form-control-label">Reason for leaving *</label>
		<input id="work_reason" name="work_reason" type="text" maxlength="200" size="200" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>
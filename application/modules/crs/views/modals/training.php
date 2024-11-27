<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add Training and Seminar</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-trainings" method="post" action="<?php echo site_url("crs/online_registration/set_modal_trainings"); ?>">
<input type="hidden" name="applicant_id" value="<?php echo $id; ?>" />
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group">
		<label for="train_from" class="form-control-label">From Date *</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="train_from" type="text" name="train_from" maxlength="12" size="12" autocomplete="off" data-validation="required" class="form-control m-input" />
        </div>
	</div>
    <div class="form-group">
		<label for="train_to" class="form-control-label">To Date *</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="train_to" type="text" name="train_to" maxlength="12" size="12" autocomplete="off" data-validation="required" class="form-control m-input" />
        </div>
	</div>
    <div class="form-group">
		<label for="training" class="form-control-label">Training *</label>
		<input id="training" name="training" type="text" maxlength="100" size="100" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="train_institution" class="form-control-label">Institution *</label>
		<input id="train_institution" name="train_institution" type="text" maxlength="100" size="100" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="train_conductor" class="form-control-label">Conducted By *</label>
		<input id="train_conductor" name="train_conductor" type="text" maxlength="100" size="100" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="train_venue" class="form-control-label">Venue *</label>
		<input id="train_venue" name="train_venue" type="text" maxlength="100" size="100" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>
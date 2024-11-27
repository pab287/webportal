<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add Organization</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-organization" method="post" action="<?php echo site_url("crs/online_registration/set_modal_organization"); ?>">
<input type="hidden" name="applicant_id" value="<?php echo $id; ?>" />
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group">
		<label for="org_institution" class="form-control-label">Institution *</label>
		<input id="org_institution" name="org_institution" type="text" maxlength="100" size="100" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="org_membership_title" class="form-control-label">Membership Title *</label>
		<input id="org_membership_title" name="org_membership_title" type="text" maxlength="100" size="100" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="org_from" class="form-control-label">From Year *</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="org_from" type="text" name="org_from" maxlength="12" size="12" autocomplete="off" data-validation="required" class="form-control m-input" />
        </div>
	</div>
    <div class="form-group">
		<label for="org_to" class="form-control-label">To Year *</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="org_to" type="text" name="org_to" maxlength="12" size="12" autocomplete="off" data-validation="required" class="form-control m-input" />
        </div>
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add Dependentt</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-dependents" method="post" action="<?php echo site_url("hris/masterfile/set_modal_dependents"); ?>">
<input type="hidden" name="emp_id" value="<?php echo $id; ?>" />
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group">
		<label for="dep_name" class="form-control-label">Name *</label>
		<input id="dep_name" name="dep_name" type="text" maxlength="100" size="100" autocomplete="off" data-validation="required" class="form-control m-input"/>
	</div>
    <div class="form-group">
		<label for="dep_birthdate" class="form-control-label">Birth Date *</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="dep_birthdate" type="text" name="dep_birthdate" maxlength="12" size="12" autocomplete="off" data-validation="required" class="form-control m-input" readonly/>
        </div>
	</div>
    <div class="form-group">
		<label for="dep_relation" class="form-control-label">Relation *</label>
		<input id="dep_relation" name="dep_relation" type="text" maxlength="50" size="50" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>
<div class="modal-header">
    <h5 class="modal-title" id="departmentModalLabel"><i class="la la-plus mr-2"></i>Add Contractor</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-add_contractor" method="post" action="<?php echo site_url("hris/masterfile/set_modal_contractor"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group">
		<label for="contractor" class="form-control-label">Contractor *</label>
		<input id="contractor" name="contractor" type="text" maxlength="60" size="60" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="company" class="form-control-label">Company *</label>
		<select id="company" name="company"data-validation="required" class="form-control select2 m-input">
            <?php if(isset($company_code) && $company_code): ?>
            <option></option>
            <?php foreach($company_code as $code): ?>
            <option value="<?php echo $code; ?>"><?php echo $code; ?></option>
            <?php endforeach; ?>
            <?php endif; ?>
        </select>
	</div>
    <div class="form-group">
		<label for="representative" class="form-control-label">Representative *</label>
		<input id="representative" name="representative" type="text" maxlength="50" size="50" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="address" class="form-control-label">Address *</label>
		<textarea id="address" name="address" autocomplete="off" data-validation="required" class="form-control m-input"></textarea>
	</div>
    <div class="form-group">
		<label for="phone" class="form-control-label">Contact No *</label>
		<input id="phone" name="phone" type="text" maxlength="25" size="25" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="project" class="form-control-label">Project *</label>
		<input id="project" name="project" type="text" maxlength="50" size="50" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>
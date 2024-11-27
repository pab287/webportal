<div class="modal-header">
    <h5 class="modal-title" id="departmentModalLabel"><i class="la la-plus mr-2"></i>Add Department</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-add_department" method="post" action="<?php echo site_url("hris/masterfile/set_modal_department"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group">
		<label for="code" class="form-control-label">Code *</label>
		<input id="code" name="code" type="text" maxlength="40" size="40" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="description" class="form-control-label">Department *</label>
		<input id="description" name="description" type="text" maxlength="50" size="50" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="head_id" class="form-control-label">Department Head *</label>
		<select id="head_id" name="head_id" data-validation="required" class="form-control select2">
            <option></option>
            <?php if(isset($employee_list) && $employee_list): ?>
            <?php foreach($employee_list as $key => $value): ?>
                <option value="<?php echo $value->id; ?>"><?php echo $value->display_name; ?></option>
            <?php endforeach; ?>
            <?php endif; ?>
        </select>
	</div>
    <div class="form-group">
        <label class="m-checkbox m-checkbox--success">
            <input type="checkbox" name="require_clearance">
            Require Clearance
            <span></span>
        </label>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>
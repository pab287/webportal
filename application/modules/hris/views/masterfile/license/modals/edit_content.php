<div class="modal-header">
    <h5 class="modal-title" id="departmentModalLabel"><i class="la la-plus mr-2"></i>Add License</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-edit_license" method="post" action="<?php echo site_url("hris/masterfile/update_modal_license"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="id" value="<?php echo ($data->id)? $data->id: ""; ?>" />
<div class="modal-body">
    <div class="form-group">
	    <label for="code" class="form-control-label">Code *</label>
		<input id="type" name="code" type="text" size="40" autocomplete="off" data-validation="required" class="form-control m-input" value="<?php echo ($data->code)? $data->code: ""; ?>" />
	</div>
    <div class="form-group">
	    <label for="code" class="form-control-label">Type *</label>
        <select name="type" id="select2_type" class="form-control" data-validation="required"></select>
	</div>
    <div class="form-group">
		<label for="type" class="form-control-label">License Name *</label>
		<input id="type" name="description" type="text" size="40" autocomplete="off" data-validation="required" class="form-control m-input" value="<?php echo ($data->description)? $data->description: ""; ?>" />
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>
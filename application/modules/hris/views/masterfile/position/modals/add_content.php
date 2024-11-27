<div class="modal-header">
    <h5 class="modal-title" id="departmentModalLabel"><i class="la la-plus mr-2"></i>Add Position</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-add_position" method="post" action="<?php echo site_url("hris/masterfile/set_modal_position"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group">
		<label for="name" class="form-control-label">Position Name *</label>
		<input id="name" name="name" type="text" size="40" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="type" class="form-control-label">Type *</label>
		<select id="type" name="type" data-validation="required" class="form-control select2 col-md-5">
            <option></option>
            <option style="SKILLED RANK AND FILE">SKILLED RANK AND FILE</option>
            <option style="RANK AND FILE">RANK AND FILE</option>
            <option style="SUPERVISORY">SUPERVISORY</option>
            <option style="MANAGERIAL">MANAGERIAL</option>
        </select>
	</div>
    <div class="form-group">
		<label for="job_desc" class="form-control-label">Job Description *</label>
		<textarea id="job_desc" name="job_desc" autocomplete="off" data-validation="required" class="form-control m-input" rows="8" style="min-height: 160px;"></textarea>
	</div>
    <div class="form-group">
		<label for="qualification" class="form-control-label">Qualifications *</label>
		<textarea id="qualification" name="qualification" autocomplete="off" data-validation="required" class="form-control m-input" rows="8" style="min-height: 160px;"></textarea>
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>
<script>

</script>

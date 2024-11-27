<style>
#qualification ul{
  list-style: disc !important;
  list-style-position: inside !important;
}

#qualification ol {
  list-style: decimal !important;
  list-style-position: inside !important;
}

#job_desc ul{
  list-style: disc !important;
  list-style-position: inside !important;
}

#job_desc ol {
  list-style: decimal !important;
  list-style-position: inside !important;
}
</style>
<div class="modal-header">
    <h5 class="modal-title" id="departmentModalLabel"><i class="la la-edit mr-2"></i>Edit Position</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-edit_position" method="post" action="<?php echo site_url("hris/masterfile/update_modal_position"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="id" value="<?php echo ($data->id)? $data->id: ""; ?>" />
<div class="modal-body">
    <div class="form-group">
		<label for="name" class="form-control-label">Position Name *</label>
		<input id="name" name="name" type="text" size="70" autocomplete="off" data-validation="required" class="form-control m-input" value="<?php echo ($data->name)? $data->name: ""; ?>" />
	</div>
    <div class="form-group">
		<label for="type" class="form-control-label">Type *</label>
		<select id="type" name="type" data-validation="required" class="form-control select2">
            <option></option>
            <option style="SKILLED RANK AND FILE">SKILLED RANK AND FILE</option>
            <option style="RANK AND FILE">RANK AND FILE</option>
            <option style="SUPERVISORY">SUPERVISORY</option>
            <option style="MANAGERIAL">MANAGERIAL</option>
        </select>
	</div>
    <div class="form-group">
		<label for="job_desc" class="form-control-label">Job Description *</label>
		<textarea id="job_desc" name="job_desc" autocomplete="off" data-validation="required" class="form-control m-input" rows="8" style="min-height: 160px;"><?php echo ($data->job_desc)? $data->job_desc: ""; ?></textarea>
	</div>
    <div class="form-group">
		<label for="qualification" class="form-control-label">Qualifications *</label>
		<textarea id="qualification" name="qualification" autocomplete="off" data-validation="required" class="form-control m-input" rows="8" style="min-height: 160px;"><?php echo ($data->qualification)? $data->qualification: ""; ?></textarea>
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>
<script>


</script>
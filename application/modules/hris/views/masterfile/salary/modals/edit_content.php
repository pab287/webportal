<div class="modal-header">
    <h5 class="modal-title" id="departmentModalLabel"><i class="la la-edit mr-2"></i>Edit Salary</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-edit_salary" method="post" action="<?php echo site_url("hris/masterfile/update_modal_salary"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="id" value="<?php echo ($data->id)? $data->id: ""; ?>" />
<div class="modal-body">
    <div class="form-group">
		<label for="description" class="form-control-label">Salary *</label>
		<input id="description" name="description" type="text" maxlength="50" size="50" autocomplete="off" data-validation="required" class="form-control m-input" value="<?php echo ($data->description)? $data->description: ""; ?>"  onkeypress="return isNumberKey(event)"/>
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>
<script>
 function isNumberKey(evt)
  {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode != 46  && charCode > 31 && charCode != 45 && (charCode < 48 || charCode > 57))
        return false;

     return true;
  }
</script>
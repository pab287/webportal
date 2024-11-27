<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add Background Check</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-background_check" method="post" action="<?php echo site_url("hris/masterfile/set_modal_background_check"); ?>">
<input type="hidden" name="emp_id" value="<?php echo $id; ?>" />
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group">
		<label for="fileupload_background_check" class="form-control-label">Attachment</label>
        <span class="btn btn-success fileinput-button btn-sm pull-right">
            <i class="glyphicon glyphicon-plus"></i>
            <span>Select file</span>
            <input type="file" id="fileupload_background_check" name="files">
            <input type="hidden" id="background_check_attachment" name="background_check_attachment" />
        </span>
        <p id="temp_fileupload" style='text-overflow: ellipsis; white-space: nowrap; overflow: hidden;' class="form-control m-input m--margin-top-10" disabled="disabled">&nbsp;</p>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>
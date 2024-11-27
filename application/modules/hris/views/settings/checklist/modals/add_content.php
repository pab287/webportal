<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add Checklist</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-add_checklist" method="post" action="<?php echo site_url("hris/settings/set_modal_checklist"); ?>">
    <div class="modal-body">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="form-group">
            <label for="checklist-name" class="form-control-label required">Name</label>
            <input type="text" name="name" id="checklist-name" class="form-control" data-validation="required">
        </div>
        <div class="form-group">
            <label for="checklist-desc" class="form-control-label required">Description</label>
            <textarea name="description" id="checklist-desc" cols="30" rows="10" class="form-control" data-validation="required"></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
        <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
    </div>
</form>
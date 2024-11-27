<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Edit Checklist</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-edit_checklist" method="post" action="<?=site_url("hris/settings/update_modal_checklist"); ?>">
    <div class="modal-body">
        <input type="hidden" name="csrf_token" value="<?=$this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="id" value="<?=($data->id) ? $data->id : ""; ?>"/>
        <div class="form-group">
            <label for="checklist-name" class="form-control-label required">Name</label>
            <input type="text" name="name" id="checklist-name" class="form-control" value="<?=($data->name) ? $data->name : ""; ?>" data-validation="required">
        </div>
        <div class="form-group">
            <label for="checklist-desc" class="form-control-label required">Description</label>
            <textarea name="description" id="checklist-desc" cols="30" rows="10" class="form-control" data-validation="required"><?=($data->description) ? $data->description : "" ?></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
        <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
    </div>
</form>
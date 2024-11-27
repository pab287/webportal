<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Edit Category</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-edit_category" method="post" action="<?=site_url("qms/masterfile/update_modal_category"); ?>">
    <div class="modal-body">
        <input type="hidden" name="csrf_token" value="<?=$this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="id" value="<?=($data->id) ? $data->id : ""; ?>"/>
        <div class="form-group">
            <label for="category-name" class="form-control-label required">Code</label>
            <input type="text" name="code" id="category-code" class="form-control" value="<?=($data->code) ? $data->code : ""; ?>" data-validation="required">
        </div>
        <div class="form-group">
            <label for="category-name" class="form-control-label required">Name</label>
            <input type="text" name="name" id="category-name" class="form-control" value="<?=($data->name) ? $data->name : ""; ?>" data-validation="required">
        </div>
        <div class="form-group">
            <label for="category-desc" class="form-control-label required">Description</label>
            <textarea name="description" id="category-desc" cols="30" rows="10" class="form-control" data-validation="required"><?=($data->description) ? $data->description : "" ?></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
        <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
    </div>
</form>
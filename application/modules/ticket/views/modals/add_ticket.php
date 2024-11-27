<div class="modal-header">
    <h5 class="modal-title">
        Add Category
    </h5>
</div>
<form id="form_category">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <div class="modal-body" id="archive_text">
        <div class="form-group">
            <label for="name" class="form-control-label required">Name</label>
            <input id="name" name="name" type="text" autocomplete="off"
                    data-validation="required" class="form-control m-input"/>
        </div>
        <div class="form-group mt-4">
            <label for="description" class="form-control-label required">Type</label>
            <select name="type" id="type" class="form-control" data-validation="required">
                <option></option>
                <option value="category">Category</option>
                <option value="sub-category">Sub-category</option>
                <option value="status">Status</option>
                <option value="severity">Severity</option>
            </select>
        </div>
        <div class="form-group mt-4">
            <label for="sss_class" class="form-control-label required">Status</label>
            <select name="status" id="status" class="form-control" data-validation="required">
                <option></option>
                <option value="0">Active</option>
                <option value="1">Inactive</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btnNew" onclick="archiveBill()">
            Save
        </button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">
            Cancel
        </button>
    </div>
</form>
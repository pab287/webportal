<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form action="<?= base_url("ams/vehicles/save_document") ?>" id="frm-add-document">
            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
            <div class="modal-header">
                <h5 class="modal-title">Add Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group m-form__group">
                    <label>Description</label>
                    <input type="text" class="form-control m-input input-auto-height" autocomplete="off"
                           name="description" data-validation="required">
                </div>

                <div class="form-group m-form__group mt-4">
                    <label>File Browser</label>
                    <div></div>
                    <label class="custom-file">
                        <input type="file" name="file" class="custom-file-input" data-validation="required"
                               onchange="showFilename(this)">
                        <span class="custom-file-control"></span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew">
                    Save
                </button>
                <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
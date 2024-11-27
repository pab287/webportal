<form id="frm-asset-configuration-new-asset-sub-category"
      action="ams/configuration/save_new_asset_sub_category">

    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    New Asset Sub Category
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group m-form__group">
                    <label for="select2-new-sub-category" class="m--font-bolder">
                        <span>Category</span>
                    </label>
                    <select class="form-control" required
                            id="select2-new-sub-category" name="category">
                    </select>
                </div>
                <div class="form-group m-form__group m--margin-top-40">
                    <label class="m--font-bolder">
                        <span>DESCRIPTION</span>
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control m-input" name="description" autocomplete="off" required>
                </div>
                <div class="form-group m-form__group m--margin-top-30">
                    <label class="m--font-bolder">
                        <span>CODE</span>
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control m-input" name="code" autocomplete="off" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btnNew" data-dismiss="modal">
                    Close
                </button>
                <button type="submit" class="btn btn-brand btnNew">
                    Save
                </button>
            </div>
        </div>
    </div>
</form>
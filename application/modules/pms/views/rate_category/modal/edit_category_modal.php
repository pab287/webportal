<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form action="<?= base_url('pms/rate_category/edit_rate_category/' . $id) ?>"
              id="frm-edit-rate-category">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Category</label>
                    <input type="text" class="form-control" autocomplete="off" name="category"
                           data-validation="required" value="<?= $category ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew">Save Changes</button>
                <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>

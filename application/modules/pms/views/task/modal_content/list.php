<div class="modal-header">
    <h5 class="modal-title">List Sequence Item</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div class="form-group m-form__group mb-4 pl-2 pr-2">
        <label for="">Search</label>
        <input type="search" id="searchBox" name="searchBox" class="form-control">
    </div>
    <div id="tree_sequence_item-list" style="min-height: 300px;"></div>
</div>
<div class="modal-footer">
    <?php $actions = $this->core_layout->getCurrentActions(); ?>
    <?php if (in_array("save", $actions)): ?>
        <button type="button" class="btn btn-primary btn-submit-list btnSave">Save</button>
    <?php endif; ?>
    <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>
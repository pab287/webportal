<div class="modal-header">
	<h5 class="modal-title">Access Control Actions</h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<div class="form-group">
	<label class="form-control-label">Description</label>
	<p class="form-control"><?php echo (isset($row["label"]) && $row["label"])? $row["label"]: ""; ?></p>
	<label class="form-control-label">Actions</label>
	<div id="tree_acl-action"></div>
</div>
</div>
<div class="modal-footer">
	<button class="btn btn-danger" data-dismiss="modal">Cancel</button>
	<?php $actions = $this->core_layout->getCurrentActions(); ?>
	<?php if(in_array("update", $actions) ): ?>
	<button type="button" class="btn btn-primary btn-submit-list" data-id="<?php echo (isset($row["id"]) && $row["id"])? $row["id"]: 0; ?>">Save</button>
	<?php endif; ?>
</div>
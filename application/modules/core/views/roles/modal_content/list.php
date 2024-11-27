<div class="modal-header">
	<h5 class="modal-title">Assigned User Role</h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<div class="container-fluid">
    <div class="row">
      <div class="col-md-4">
		<div class="form-group">
			<label class="form-control-label">Name</label>
			<p class="form-control"><?php echo (isset($row["name"]) && $row["name"])? $row["name"]:""; ?></p>
		</div>
		<div class="form-group">
			<label class="form-control-label">Description</label>
			<p class="form-control"><?php echo (isset($row["description"]) && $row["description"])? $row["description"]:""; ?></p>
		</div>
		<div class="form-group">
			<label class="form-control-label">Status</label>
			<p class="form-control"><?php echo (isset($row["status"]) && intval($row["status"]) == 1)? "Active":"Inactive"; ?></p>
		</div>
	  </div>
      <div class="col-md-8"><div id="tree_role-list"></div></div>
    </div>
  </div>
</div>
<div class="modal-footer">
	<button class="btn btn-danger" data-dismiss="modal">Cancel</button>
	<?php $actions = $this->core_layout->getCurrentActions(); ?>
	<?php if(in_array("update", $actions) ): ?>
	<button type="button" class="btn btn-primary btn-submit-save" data-id="<?php echo $row["id"]; ?>">Save</button>
	<?php endif; ?>
</div>
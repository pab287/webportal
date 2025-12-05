<div class="modal-header">
	<h5 class="modal-title">Module Actions</h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<div class="row">
	<div class="col-md-4">
	<div class="form-group">
			<label class="form-control-label">Name</label>
			<p class="form-control"><?php echo (isset($row["name"]) && $row["name"])? $row["name"]: ""; ?></p>
		</div>
		<div class="form-group">
			<label class="form-control-label">Description</label>
			<p class="form-control custom-p_textarea"><?php echo (isset($row["label"]) && $row["label"])? $row["label"]: ""; ?></p>
		</div>
		<div class="form-group">
			<label class="form-control-label">Status</label>
			<p class="form-control"><?php echo (isset($row["is_active"]) && $row["is_active"] == 1)? "Active": "Inactive"; ?></p>
		</div>
	</div>
	<div class="col-md-8">
		<div>
			<div class="form-group m-form__group">
				<label for="example_input_full_name">
					Allowed IP:
				</label>
				<select class="form-control" id="ip_restrictions" multiple name="allowed_ip" data-module_id="<?php echo (isset($row["id"]) && $row["id"])? $row["id"]: 0; ?>">
				</select>
				<span class="m-form__help">
					<small>
						Please enter the IP addresses that are allowed to access this module
					</small>
				</span>
			</div>
		</div>
		<label class="form-control-label">Actions</label>
		<div id="tree_module-action"></div>
	</div>
</div>
</div>
<div class="modal-footer">
	<button class="btn btn-danger" data-dismiss="modal">Cancel</button>
	<?php $actions = $this->core_layout->getCurrentActions(); ?>
	<?php if(in_array("update", $actions) ): ?>
	<button type="button" class="btn btn-primary btn-submit-list" data-id="<?php echo (isset($row["id"]) && $row["id"])? $row["id"]: 0; ?>">Save</button>
	<?php endif; ?>
</div>
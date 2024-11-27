<div class="modal-header">
	<h5 class="modal-title">List Modules</h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body"><div id="tree_module-list"></div></div>
<div class="modal-footer">
	<button class="btn btn-danger" data-dismiss="modal">Cancel</button>
	<?php $actions = $this->core_layout->getCurrentActions(); ?>
	<?php if(in_array("update", $actions) ): ?>
	<button type="button" class="btn btn-primary btn-submit-list">Save</button>
	<?php endif; ?>
</div>
<div class="modal-header">
<h5 class="modal-title" id="exampleModalLabel">Remarks</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
</button>
</div>
<form id="frmUpdateUnitRemarks" method="post" action="<?php echo site_url("pms/project/set_modal_project_unit_remarks"); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <div class="modal-body">
        <div class="form-group">
            <textarea id="sf_remarks" class="form-control" name="sf_remarks" rows="5" style="min-height: 100px;"><?php echo $sf_remarks; ?></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btnSave">Save</button>
        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
    </div>
</form>
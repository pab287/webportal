<div class="modal-header">
    <h5 class="modal-title"><i class="la la-plus mr-2"></i>Add Skill</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form id="form-skill" method="post" action="<?php echo site_url("crs/online_registration/add_skill"); ?>">
<input type="hidden" name="applicant_id" value="<?php echo $id; ?>" />
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group">
        <label for="">Skill</label>
        <input type="text" class="form-control"
                data-validation="required" name="skills"
                autocomplete="off">
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnNew"><i class="la la-check mr-2"></i>Save</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Close
    </button>
</div>
</form>
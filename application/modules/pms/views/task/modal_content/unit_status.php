<div class="modal-header">
<h5 class="modal-title" id="exampleModalLabel">Status</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
</button>
</div>
<form id="frmUpdateUnitStatus" method="post" action="<?php echo site_url("pms/project/set_modal_project_unit_status"); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <div class="modal-body">
        <div class="form-group">
            <div class="m-checkbox-inline">
                <label class="m-checkbox">
                    <input type="radio" name="sf_status" value="0" v-model="row.sf_status"> Awaiting
                    <span></span>
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="m-checkbox-inline">
                <label class="m-checkbox">
                    <input type="radio" name="sf_status" value="1" v-model="row.sf_status"> Ongoing
                    <span></span>
                </label>
                <label class="m-checkbox">
                    <input type="radio" name="sf_status" value="2" v-model="row.sf_status"> On Hold
                    <span></span>
                </label>
                <label class="m-checkbox">
                    <input type="radio" name="sf_status" value="3" v-model="row.sf_status"> Completed
                    <span></span>
                </label>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btnSave">Save</button>
        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
    </div>
</form>
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Set Task Duration</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    </button>
</div>
<form id="frmSetTaskDuration" method="post" action="<?php echo site_url("pms/task/do_post_event/set_modal_task_duration"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="id" v-model="row.id" />
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="issued_date">Issued Date *</label>
                <input id="issued_date" type="text" class="form-control form-datepicker" maxlength="10" size="10" name="issued_date" data-validation="required" autocomplete="off" v-model="row.issued_date" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="due_date">Due Date *</label>
                <input id="due_date" type="text" class="form-control form-datepicker" maxlength="10" size="10" name="due_date" data-validation="required" autocomplete="off" v-model="row.due_date" />
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave">Save</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
</div>
</form>
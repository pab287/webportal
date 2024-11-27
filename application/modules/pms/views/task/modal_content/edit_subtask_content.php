<form id="frmUpdateSubtask" method="post" action="<?php echo site_url("pms/task/do_post_event/update_modal_subtask"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="id" v-model="row.id" />
<div class="modal-header">
<h5 class="modal-title" id="exampleModalLabel"><span>{{row.task_name}}</span> <small>Edit Sub-task</small></h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
</button>
</div>
<div class="modal-body">
    <div class="form-group m-form__group">
        <label for="contractor_id">Contractor</label>
        <p class="form-control" v-text="row.contractor">&nbsp;</p>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group m-form__group">
                <label for="issued_date">Date Issued</label>
                <p class="form-control" v-text="row.issued_date">&nbsp;</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group m-form__group">
                <label for="due_date">Date Due</label>
                <p class="form-control" v-text="row.due_date">&nbsp;</p>
            </div>
        </div>
    </div>
    <div class="form-group m-form__group">
        <label for="description">Description</label>
        <textarea class="form-control" name="description" id="description" rows="10" v-model="row.description"></textarea>
    </div>
    <div class="form-group m-form__group">
        <label>Task Status *</label>
        <div class="m-checkbox-inline">
            <label class="m-checkbox col-4"><input type="radio" name="task_status" value="0" v-model="row.task_status"> Awaiting <span></span></label>
            <label class="m-checkbox col-4"><input type="radio" name="task_status" value="1" v-model="row.task_status"> In Progress <span></span></label> 
        </div>
        <div class="m-checkbox-inline m--margin-bottom-15">
            <label class="m-checkbox col-4"><input type="radio" name="task_status" value="2" v-model="row.task_status"> Deferred <span></span></label> 
            <label class="m-checkbox col-4"><input type="radio" name="task_status" value="3" v-model="row.task_status"> Completed <span></span></label>
        </div>
        <div class="m-checkbox-inline">
            <label class="m-checkbox col-4"><input type="radio" name="task_status" value="4" v-model="row.task_status"> Terminated <span></span></label> 
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave">Save</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
</div>
</form>
<div class="modal-header">
<h5 class="modal-title" id="exampleModalLabel">New Task</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
</button>
</div>
<form id="frmNewTask" method="post" action="<?php echo site_url("pms/task/set_modal_task_item"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="parent_id" value="<?php echo $id; ?>" />
    <div id="tempNewTask" class="modal-body">
        <div class="form-group m-form__group">
            <label for="task_id">Task</label>
            <select id="task_id" class="form-control m-input select2" name="task_id" data-validation="required"></select>
        </div>
        <div class="form-group m-form__group">
            <label for="contractor_id">Contractor</label>
            <select id="contractor_id" name="contractor_id" class="form-control m-input select2" data-validation="required"></select>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group m-form__group">
                    <label for="issued_date">Date Issued</label>
                    <input type="text" id="issued_date" name="issued_date" class="form-control m-input form-datepicker" data-validation="required" autocomplete="off" @change="checkDatePicker" disabled />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group m-form__group">
                    <label for="due_date">Date Due</label>
                    <input type="text" id="due_date" name="due_date" class="form-control m-input form-datepicker" data-validation="required" autocomplete="off" disabled />
                </div>
            </div>
        </div>
        <div class="form-group m-form__group">
            <label for="description">Description</label>
        <textarea id="description" class="form-control m-input" name="description" rows="10"></textarea>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave">Save</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
</div>
</form>
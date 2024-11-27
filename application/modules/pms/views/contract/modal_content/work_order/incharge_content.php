<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Update Task In-charge</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="frmContractIncharge" method="post" action="<?php echo site_url("pms/contract/do_post_event/update_contract_incharge"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="id" v-model="row.id" />
<div class="modal-body">
    <div class="form-group m-form__group">
        <label for="task_incharge">Task In-charge</label>
        <select class="form-control m-input select2" id="task_incharge" name="task_incharge" data-validation="required"></select>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Save</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
</div>
</form>
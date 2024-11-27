<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Update Task Leadman</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="frmContractLeadman" method="post" action="<?php echo site_url("pms/contract/do_post_event/update_contract_leadman"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="id" v-model="row.id" />
<div class="modal-body">
    <div class="form-group m-form__group">
        <label for="leadman">Leadman *</label>
        <input type="text" class="form-control m-input" id="leadman" name="leadman" data-validation="required" />
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Save</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
</div>
</form>
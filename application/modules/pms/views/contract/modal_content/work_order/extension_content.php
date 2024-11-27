<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Task Extension</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="frmContractExtension" method="post" action="<?php echo site_url("pms/contract/do_post_event/update_contract_extension"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="contract_id" v-model="row.id" />
<div class="modal-body">
    <div class="form-group m-form__group">
        <label for="extension_date">Extension Date *</label>
        <input id="extension_date" type="text" class="form-control col-md-5 form-datepicker" maxlength="10" size="10" name="extension_date" data-validation="required" autocomplete="off" />
    </div>
    <div class="form-group m-form__group">
        <label for="remarks">Remarks *</label>
        <textarea id="remarks" class="form-control" name="remarks" data-validation="required" rows="5"></textarea>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Save</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
</div>
</form>
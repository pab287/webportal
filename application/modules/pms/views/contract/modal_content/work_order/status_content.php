<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Update Status</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="frmContractStatus" method="post" action="<?php echo site_url("pms/contract/do_post_event/update_contract_status"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="id" v-model="row.id" />
<div class="modal-body">
    <div class="m-form__group form-group">
        <label for="">Status</label>
        <div class="m-checkbox-inline">
            <label class="m-checkbox">
                <input type="radio" name="status" v-model="row.status" value="1" /> Active <span></span>
            </label>
            <label class="m-checkbox">
                <input type="radio" name="status" v-model="row.status" value="2" /> Completed <span></span>
            </label>
            <label class="m-checkbox">
                <input type="radio" name="status" v-model="row.status" value="3" /> Terminated <span></span>
            </label>
        </div>
    </div>
    <div class="form-group m-form__group">
        <label for="status_remarks">Remarks</label>
        <textarea class="form-control m-input" id="status_remarks" name="status_remarks" rows="3" data-validation="required"></textarea>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Save</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
</div>
</form>
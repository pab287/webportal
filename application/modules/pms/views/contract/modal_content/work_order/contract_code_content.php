<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Generate Work Order Code</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="frmContractCode" method="post" action="<?php echo site_url("pms/contract/do_post_event/generate_contract_code"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="wo_version" v-model="row.wo_version" />
<input type="hidden" name="project_id" v-model="row.project_id" />
<input type="hidden" name="item_id" v-model="row.parent_id" />
<input type="hidden" name="block_no" v-model="row.block_no" v-if="row.block_no" />
<input type="hidden" name="lot_no" v-model="row.lot_no" v-if="row.lot_no" />
<input type="hidden" name="selected_items" v-model="row.selected_items" />
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group m-form__group">
                <label for="issued_date">ISSUED DATE *</label> 
                <input id="issued_date" type="text" maxlength="10" size="10" name="issued_date" data-validation="required" autocomplete="off" class="form-control form-datepicker" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group m-form__group">
                <label for="due_date">DUE DATE *</label> 
                <input id="due_date" type="text" maxlength="10" size="10" name="due_date" data-validation="required" autocomplete="off" class="form-control form-datepicker" />
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Generate Code</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
</div>
</form>
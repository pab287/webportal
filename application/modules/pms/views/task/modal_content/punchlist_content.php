<div class="modal-header">
<h5 class="modal-title" id="exampleModalLabel">Punchlist</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="frmPunchlistTask" method="post" action="<?php echo site_url("pms/task/do_post_event/set_modal_punchlist"); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>" />
    <input type="hidden" name="unit_id" v-model="unit_id" />
    <div class="modal-body">
        <div class="m-portlet m-portlet--unair m-portlet--mobile m-portlet--bordered">
            <div class="m-portlet__body">
                <div id="tree-punchlist_items" class="m--marginless"></div>
                <div id="selectedItems" class="form-group m-form__group m--marginless">
                    <input type="hidden" id="selected_items" name="selected_items" data-validation="required" data-validation-error-msg-required="Select on the above task" v-model="selected_items" />
                </div>
            </div>
        </div>
        <div class="form-group m-form__group">
            <label>Status</label>
                <div class="m-checkbox-inline">
                    <label class="m-checkbox"><input type="radio" name="status" value="0"> Completed<span></span></label>
                    <label class="m-checkbox"><input type="radio" name="status" value="1" checked="checked"> Punchlist<span></span></label>
                    <label class="m-checkbox"><input type="radio" name="status" value="2"> Punchlisted<span></span></label>
                </div>
            </div>
        <div class="form-group m-form__group">
            <label for="remarks">Remarks *</label>
            <textarea class="form-control m-input" id="remarks" name="remarks" rows="5" data-validation="required"></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btnSave">Save</button>
        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
    </div>
</form>
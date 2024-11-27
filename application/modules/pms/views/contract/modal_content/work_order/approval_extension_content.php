<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Contract Extension</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="frmExntensionApproval" method="post" action="<?php echo site_url("pms/contract/do_post_event/update_contract_extension_approval"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="id" v-model="row.id" v-if="row.id" />
<input type="hidden" name="contract_id" v-model="row.contract_id" v-if="row.contract_id" />
<input type="hidden" name="extension_status" v-bind:value="type === 'Approve'? 1:2" />
<div class="modal-body">
    <div class="m-widget4">
        <div class="m-widget4__item">
            <div class="m-widget4__info">
                <div class="row">
                    <div class="col-7">
                        <p class="m-widget4__title m--marginless" v-text="row.wo_code">&nbsp;</p>
                        <p class="m-widget4__sub m--marginless">Work Order #</p>
                    </div>
                    <div class="col-5">
                        <p class="m-widget4__title m--marginless" v-text="row.contractor">&nbsp;</p>
                        <p class="m-widget4__sub m--marginless">Contractor</p>
                    </div>
                </div>
            </div>
            <div class="m-widget4__ext">&nbsp;</div>
        </div>
        <div class="m-widget4__item">
            <div class="m-widget4__info">
                <div class="row">
                    <div class="col-7">
                        <p class="m-widget4__title m--marginless" v-text="row.due_date">&nbsp;</p>
                        <p class="m-widget4__sub m--marginless">Due Date</p>
                    </div>
                    <div class="col-5">
                        <p class="m-widget4__title m--marginless" v-text="row.extension_date">&nbsp;</p>
                        <p class="m-widget4__sub m--marginless">Extension Date</p>
                    </div>
                </div>
            </div>
            <div class="m-widget4__ext">&nbsp;</div>
        </div>
        <div class="m-widget4__item">
            <div class="m-widget4__info">
                <p class="m-widget4__text">
                    Are you sure you want to <span class="m--font-boldest " v-text="type" v-bind:class="type === 'Approve'? 'm--font-success':'m--font-danger'">&nbsp;</span> this contract extension?
                </p>
            </div>
            <div class="m-widget4__ext">&nbsp;</div>
        </div>
    </div>
    <div class="form-group m-form__group">
        <label for="extended_remarks">Remarks *</label>
        <textarea class="form-control m-input" id="extended_remarks" name="extended_remarks" rows="5" data-validation="required"></textarea>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary" v-if="row.id">Yes</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">No</button>
</div>
</form>
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Generate Lot</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="frmEditBlockLot" method="post" v-bind:action="url">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="id" v-model="id" />
<div class="modal-body">
    <template v-if="count > 0">
        <template v-for="(aa, bb) in blocks">
            <div class="m-form__group form-group">
                <label class="m--font-darkest">BLOCK {{aa}}</label>
                <div class="m-checkbox-inline">
                    <template v-for="(cc, dd) in units[aa]">
                        <label class="m-checkbox">
                            <input 
                            type="checkbox" 
                            :name="'unit['+aa+'][]'" 
                            :value="cc.id" 
                            data-validation="checkbox_group" 
                            data-validation-qty="min1" />Lot {{cc.lot}}<span></span>
                        </label>
                    </template>
                </div>
            </div>
        </template>
    </template>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Update Lots</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
</div>
</form>
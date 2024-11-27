<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Generate Lot</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="frmGenerateLot" @submit="generateLots">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <template v-if="count > 0">
        <template v-for="(aa, bb) in blocks">
            <div class="m-form__group form-group">
                <label for="">
                    BLOCK {{aa}}
                </label>
                <div class="m-checkbox-inline">
                    <template v-for="(cc, dd) in units[aa]">
                        <label class="m-checkbox">
                            <input type="checkbox" :value="cc.id" :data-lot="cc.lot" :data-block="aa" />
                            Lot {{cc.lot}}
                            <span></span>
                        </label>
                    </template>
                </div>
            </div>
        </template>
    </template>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Assign Lots</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
</div>
</form>
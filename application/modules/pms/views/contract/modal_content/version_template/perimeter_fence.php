<div class="row">
<?php if(isset($contractor_count) && $contractor_count > 0): ?>
    <div class="col-lg-4">
        <div class="form-group m-form__group">
            <label>Task</label>
            <select id="gen_task" name="gen_task" class="form-control m-input form-control-sm select2" data-validation="required"></select>
        </div>
    </div>
<?php endif; ?>
    <div class="col-lg-4">
        <div class="form-group m-form__group">
            <label>Starting Column Number</label>
            <input type="text" name="block_no" class="form-control m-input" maxlength="6" size="6" />
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group m-form__group">
            <label>Last Column Number</label>
            <input type="text" name="lot_no" class="form-control m-input" maxlength="6" size="6" />
        </div>
    </div>
</div>
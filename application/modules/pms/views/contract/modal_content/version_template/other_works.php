<div class="row">
    <div class="col-lg-6 col-md-6">
        <div class="form-group m-form__group">
            <label>Type of Work *</label>
            <select id="version-wo_type" name="wo_type" class="form-control m-input select2 col-md-6" data-validation="required"></select>
        </div>
    </div>
    <div class="col-lg-12 col-md-12">
    <?php if($items->num_rows() > 0): ?>
        <div class="m-form__group form-group">
        <label>Assign Task</label>
        <div class="m-checkbox-inline">
        <?php foreach($items->result() as $index => $rs): ?> 
        <?php $xx = $index % 3; ?>
            <label class="m-checkbox">
                <input type="checkbox" name="sub_task[]" value="<?php echo $rs->id; ?>" data-validation="checkbox_group" data-validation-qty="min1" /><?php echo $rs->label; ?><span></span>
            </label>
        <?php if($xx == 2): ?>
        </div><div class="m-checkbox-inline">
        <?php endif; ?>
        <?php endforeach; ?>
        </div>
        </div>
        <?php endif; ?>
    </div>
</div>
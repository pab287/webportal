<form id="frm-asset-configuration-edit-equipment-type"
      action="ams/configuration/update_equipment_type">

    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="id" value="<?= $data->id ?>">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Edit Equipment Type
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group m-form__group">
                    <label for="select2-edit-equipment-type-equipment-category" class="m--font-bolder">
                        <span>EQUIPMENT CATEGORY</span>
                    </label>
                    <select class="form-control" required
                            id="select2-edit-equipment-type-equipment-category" name="equipment_category">
                    </select>
                </div>

                <div class="form-group m-form__group m--margin-top-30">
                    <label class="m--font-bolder">
                        <span>DESCRIPTION</span>
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control m-input" name="description" autocomplete="off" required value="<?= $data->description ?>">
                </div>

                <div class="form-group m-form__group m--margin-top-20">
                    <label class="m--font-bolder">
                        <span>CODE</span>
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control m-input" name="code" autocomplete="off" required value="<?= $data->code ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btnNew" data-dismiss="modal">
                    Close
                </button>
                <button type="submit" class="btn btn-brand btnNew">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    var newOption = new Option("<?=$data->equipment_category?>", "<?=$data->id?>", false, false);
    $('#select2-edit-equipment-type-equipment-category').append(newOption).trigger('change');
</script>
<form id="frm-asset-configuration-edit-station"
      action="ams/configuration/update_station">

    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="id" value="<?= $data->id ?>">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Edit Station
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group m-form__group">
                    <label class="m--font-bolder">
                        <span>STATION</span>
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control m-input" name="station" autocomplete="off" required
                           value="<?= $data->station ?>">
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
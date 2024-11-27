<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form id="employee-data-update-driverlicense" method="post" action="<?= base_url("crs/online_registration/update_driverlicense"); ?>">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Edit Driver's License</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id" value="<?= $data->id; ?>">

            <div class="modal-body">
                <div class="form-group">
                    <label for="license_type" class="form-control-label">
                        Restriction
                        <span class="text-danger">*</span>
                    </label>
                    <input id="restriction" name="restriction" type="text" maxlength="72" size="100" autocomplete="off" data-validation="required"
                           class="form-control m-input" value="<?= $data->restriction ?>"/>
                </div>
                <div class="form-group">
                    <label for="license_no" class="form-control-label">License No.</label>
                    <input id="license_no" name="license_no" type="text" maxlength="100" size="100" autocomplete="off" class="form-control m-input"
                           value="<?= $data->license_no ?>"/>
                </div>
                <div class="form-group">
                    <label for="expiration_date" class="form-control-label">Expiration Date</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input id="expiration_date" type="text" name="expiration_date" autocomplete="off"
                               class="form-control m-input date" value="<?= $data->expiration_date == '0000-00-00' ? '' : $data->expiration_date ?>"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
                <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
            </div>
        </form>
    </div>
</div>
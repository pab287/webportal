<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form id="employee-data-update-award" method="post" action="<?= base_url("hris/employee_fillout/update_award"); ?>">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Edit Awards and Achievements</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id" value="<?= $data->id ?>">

            <div class="modal-body">
                <div class="form-group">
                    <label for="award" class="form-control-label">Award / Achievement
                        <span class="text-danger">*</span>
                    </label>
                    <input id="award" name="award" type="text" maxlength="200" size="200" autocomplete="off" data-validation="required"
                           class="form-control m-input" value="<?= $data->award ?>"/>
                </div>
                <div class="form-group">
                    <label for="award_institution" class="form-control-label">Institution
                        <span class="text-danger">*</span>
                    </label>
                    <input id="award_institution" name="award_institution" type="text" maxlength="200" size="200" autocomplete="off"
                           data-validation="required" class="form-control m-input" value="<?= $data->award_institution ?>"/>
                </div>
                <div class="form-group">
                    <label for="award_date" class="form-control-label">Given Date
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input id="award_date" type="text" name="award_date" maxlength="12" size="12" autocomplete="off" data-validation="required"
                               class="form-control m-input date" value="<?= $data->award_date ?>"/>
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
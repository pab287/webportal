<div class="modal-dialog" role="dialog">
    <form action="<?= base_url('hris/masterfile/update_legal_records') ?>"
          id="employee-data-update-legal-records">
        <div class="modal-content">
            <input type="hidden" name="id" value="<?= $data->id ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <div class="modal-header">
                <h5 class="modal-title"><i class="la la-edit mr-2"></i>Edit Legal History/Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group m-form m-form__group">
                    <label for="">Case No. <span class="text-danger">*</span></label>
                    <input type="text" class="form-control m-input" name="leg_case_no" value="<?= $data->leg_case_no ?>" autocomplete="off" data-validation="required"/>
                </div>
                <div class="form-group m-form m-form__group">
                    <label for="">Details <span class="text-danger">*</span></label>
                    <input type="text" class="form-control m-input" name="leg_details" value="<?= $data->leg_details ?>" autocomplete="off" data-validation="required"/>
                </div>
                <div class="form-group m-form m-form__group">
                    <label for="">Date <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input type="text" maxlength="12" size="12"
                               name="leg_case_date" value="<?= $data->leg_case_date ?>"
                               data-validation="required" class="form-control m-input date" autocomplete="off"/>
                    </div>
                </div>
                <div class="form-group m-form m-form__group">
                    <label for="">Court Field <span class="text-danger">*</span></label>
                    <input type="text" class="form-control m-input" name="leg_court_field" value="<?= $data->leg_court_field ?>" autocomplete="off" data-validation="required"/>
                </div>
                <div class="form-group m-form m-form__group">
                    <label for="">Prosecutor <span class="text-danger">*</span></label>
                    <input type="text" class="form-control m-input" name="leg_prosecutor" value="<?= $data->leg_prosecutor ?>" autocomplete="off" data-validation="required"/>
                </div>
                <div class="form-group m-form m-form__group">
                    <label for="">Status <span class="text-danger">*</span></label>
                    <input type="text" class="form-control m-input" name="leg_status" value="<?= $data->leg_status ?>" autocomplete="off" data-validation="required"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnEdit"><i class="la la-check mr-2"></i>
                    Save
                </button>
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal"><i class="la la-times mr-2"></i>
                    Cancel
                </button>
            </div>
        </div>
    </form>
</div>
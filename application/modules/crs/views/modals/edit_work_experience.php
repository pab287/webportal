<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form id="employee-date-update-work-experience"
              method="post" action="<?= base_url("crs/online_registration/update_work_experience"); ?>">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Edit Work Experience</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id" value="<?= $data->id ?>">

            <div class="modal-body">
                <div class="form-group">
                    <label for="work_from" class="form-control-label">From Year
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input id="work_from" type="text" name="work_from" maxlength="4" size="4" autocomplete="off" data-validation="required"
                               class="form-control m-input" value="<?=$data->work_from?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="work_to" class="form-control-label">To Year
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input id="work_to" type="text" name="work_to" maxlength="4" size="4" autocomplete="off" data-validation="required"
                               class="form-control m-input date-year" value="<?=$data->work_to?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="work_company" class="form-control-label">Company
                        <span class="text-danger">*</span>
                    </label>
                    <input id="work_company" name="work_company" type="text" maxlength="200" size="200" autocomplete="off" data-validation="required"
                           class="form-control m-input" value="<?=$data->work_company?>"/>
                </div>
                <div class="form-group">
                    <label for="work_position" class="form-control-label">Position
                        <span class="text-danger">*</span>
                    </label>
                    <input id="work_position" name="work_position" type="text" maxlength="200" size="200" autocomplete="off"
                           data-validation="required" class="form-control m-input" value="<?=$data->work_position?>"/>
                </div>
                <div class="form-group">
                    <label for="work_status" class="form-control-label">Status
                        <span class="text-danger">*</span>
                    </label>
                    <input id="work_status" name="work_status" type="text" maxlength="50" size="50" autocomplete="off" data-validation="required"
                           class="form-control m-input" value="<?=$data->work_status?>"/>
                </div>
                <!-- <div class="form-group">
                    <label for="work_status" class="form-control-label">ID NO
                    </label>
                    <input id="old_idno" name="old_idno" type="text" maxlength="50" size="50" autocomplete="off"
                           class="form-control m-input" value="<?//=$data->old_idno?>"/>
                </div> -->
                <div class="form-group">
                    <label for="work_reason" class="form-control-label">Reason for leaving
                        <span class="text-danger">*</span>
                    </label>
                    <input id="work_reason" name="work_reason" type="text" maxlength="200" size="200" autocomplete="off" data-validation="required"
                           class="form-control m-input" value="<?=$data->work_reason?>"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
                <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
            </div>
        </form>
    </div>
</div>
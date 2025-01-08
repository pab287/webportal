<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Edit Training and Seminar</h5>
            <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <form id="employee-data-update-training" method="post" action="<?= base_url("hris/masterfile/update_training"); ?>">
            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id" value="<?= $data->id ?>">
            <input type="hidden" name="current_filename" value="<?= $data->attachment ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="train_from" class="form-control-label">From Date
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input id="train_from" type="text" name="train_from" maxlength="12" size="12" autocomplete="off" data-validation="required"
                               class="form-control m-input date" value="<?= $data->train_from ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="train_to" class="form-control-label">To Date
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input id="train_to" type="text" name="train_to" maxlength="12" size="12" autocomplete="off" data-validation="required"
                               class="form-control m-input date" value="<?= $data->train_to ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="training" class="form-control-label">Training
                        <span class="text-danger">*</span>
                    </label>
                    <input id="training" name="training" type="text" maxlength="100" size="100" autocomplete="off" data-validation="required"
                           class="form-control m-input" value="<?= $data->training ?>"/>
                </div>
                <div class="form-group">
                    <label for="train_institution" class="form-control-label">Institution
                        <span class="text-danger">*</span>
                    </label>
                    <input id="train_institution" name="train_institution" type="text" maxlength="100" size="100" autocomplete="off"
                           data-validation="required" class="form-control m-input" value="<?= $data->train_institution ?>"/>
                </div>
                <div class="form-group">
                    <label for="train_conductor" class="form-control-label">Conducted By
                        <span class="text-danger">*</span>
                    </label>
                    <input id="train_conductor" name="train_conductor" type="text" maxlength="100" size="100" autocomplete="off"
                           data-validation="required" class="form-control m-input" value="<?= $data->train_conductor ?>"/>
                </div>
                <div class="form-group">
                    <label for="train_venue" class="form-control-label">Venue
                        <span class="text-danger">*</span>
                    </label>
                    <input id="train_venue" name="train_venue" type="text" maxlength="100" size="100" autocomplete="off" data-validation="required"
                           class="form-control m-input" value="<?= $data->train_venue ?>"/>
                </div>
                <div class="form-group">
                    <label for="fileupload_training" class="form-control-label">Attachment</label>
                    <span class="btn btn-success fileinput-button btn-sm pull-right">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>Select file</span>
                        <input type="file" id="fileupload_training" name="files" accept=".pdf,application/pdf" onchange="setFilename(this, '#temp_fileupload')">
                    </span>
                    <p id="temp_fileupload" class="form-control m-input m--margin-top-10" disabled="disabled"><?= $data->attachment ?></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
                <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
            </div>
        </form>
    </div>
</div>
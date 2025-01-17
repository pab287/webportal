<div class="modal-dialog" role="dialog">
    <form action="<?= base_url('hris/masterfile/update_dependent') ?>"
          id="employee-data-update-dependent">
        <div class="modal-content">
            <input type="hidden" name="id" value="<?= $data->id ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <div class="modal-header">
                <h5 class="modal-title"><i class="la la-edit mr-2"></i>Edit Dependent</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-control-label">
                        Dependent Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control m-input" name="dep_name" data-validation="required" autocomplete="off"
                           value="<?= $data->dep_name ?>">
                </div>
                <div class="form-group">
                    <label class="form-control-label">
                        Birth Date <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input type="text" id="dep_birthdate"  maxlength="12" size="12" autocomplete="off"
                               data-validation="required" class="form-control m-input" value="<?= $data->dep_birthdate ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-control-label">
                        Relation <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control m-input" name="dep_relation" data-validation="required" autocomplete="off"
                           value="<?= $data->dep_relation ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnEdit"><i class="la la-check mr-2"></i>
                    Save Changes
                </button>
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal"><i class="la la-times mr-2"></i>
                    Cancel
                </button>
            </div>
        </div>
    </form>
</div>
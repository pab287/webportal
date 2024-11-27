<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form id="employee-data-update-personal-references"
              method="post" action="<?= base_url("hris/employee_fillout/update_personal_references"); ?>">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Edit Personal Reference</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id" value="<?= $data->id ?>">

            <div class="modal-body">
                <div class="form-group">
                    <label for="ref_name" class="form-control-label">Name
                        <span class="text-danger">*</span>
                    </label>
                    <input id="ref_name" name="ref_name" type="text" maxlength="100" size="100" autocomplete="off" data-validation="required"
                           class="form-control m-input" value="<?= $data->ref_name ?>"/>
                </div>
                <div class="form-group">
                    <label for="ref_contact_no" class="form-control-label">Contact No
                        <span class="text-danger">*</span>
                    </label>
                    <input id="ref_contact_no" name="ref_contact_no" type="text" maxlength="25" size="25" autocomplete="off"
                           data-validation="required" class="form-control m-input" value="<?= $data->ref_contact_no ?>"/>
                </div>
                <div class="form-group">
                    <label for="ref_address" class="form-control-label">Address</label>
                    <input id="ref_address" name="ref_address" type="text" maxlength="200" size="200" autocomplete="off"
                           class="form-control m-input" value="<?= $data->ref_address ?>"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
                <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
            </div>
        </form>
    </div>
</div>
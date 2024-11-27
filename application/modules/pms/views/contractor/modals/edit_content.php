<div class="modal-header">
    <h5 class="modal-title" id="departmentModalLabel">Edit Contractor</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-edit_contractor" method="post"
      action="<?php echo site_url("pms/contractor/update_modal_contractor"); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="id" value="<?php echo ($data->id) ? $data->id : ""; ?>"/>
    <div class="modal-body">
        <div class="form-group">
            <label for="contractor" class="form-control-label">Contractor *</label>
            <input id="contractor" name="contractor" type="text" maxlength="60" size="60" autocomplete="off"
                   data-validation="required" class="form-control m-input"
                   value="<?php echo ($data->contractor) ? $data->contractor : ""; ?>"/>
        </div>
        <div class="form-group">
            <label for="company" class="form-control-label">Company *</label>
            <select id="company" name="company" data-validation="required" class="form-control select2 m-input">
                <?php if (isset($company_code) && $company_code): ?>
                    <option></option>
                    <?php foreach ($company_code as $code): ?>
                        <option value="<?php echo $code; ?>"><?php echo $code; ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="representative" class="form-control-label">Representative *</label>
            <input id="representative" name="representative" type="text" maxlength="50" size="50" autocomplete="off"
                   data-validation="required" class="form-control m-input"
                   value="<?php echo ($data->representative) ? $data->representative : ""; ?>"/>
        </div>
        <div class="form-group">
            <label for="address" class="form-control-label">Address *</label>
            <textarea id="address" name="address" autocomplete="off" data-validation="required"
                      class="form-control m-input"><?php echo ($data->address) ? $data->address : ""; ?></textarea>
        </div>
        <div class="form-group">
            <label for="phone" class="form-control-label">Contact No *</label>
            <input id="phone" name="phone" type="text" maxlength="25" size="25" autocomplete="off"
                   data-validation="required" class="form-control m-input"
                   value="<?php echo ($data->phone) ? $data->phone : ""; ?>"/>
        </div>
        <div class="form-group">
            <label for="project" class="form-control-label">Project *</label>
            <input id="project" name="project" type="text" maxlength="50" size="50" autocomplete="off"
                   data-validation="required" class="form-control m-input"
                   value="<?php echo ($data->project) ? $data->project : ""; ?>"/>
        </div>

        <div class="form-group">
            <label for="">Status</label>
            <select name="status" data-validation="required" class="form-control"
                    id="status">
                <option value=""></option>
                <option value="active" <?= $data->status === "active" ? "selected='selected'" : "" ?>>
                    Active
                </option>
                <option value="inactive" <?= $data->status === "inactive" ? "selected='selected'" : "" ?>>
                    Inactive
                </option>
                <option value="blacklisted" <?= $data->status === "blacklisted" ? "selected='selected'" : "" ?>>
                    Blacklisted
                </option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btnSave">Save</button>
        <button class="btn btn-danger modalClose" data-dismiss="modal">Cancel</button>
    </div>
</form>
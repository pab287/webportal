<form action="<?php echo site_url('core/users/assign_user_role'); ?>" method="POST" id="form-users-assign">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="id" value="<?php echo (isset($row["id"]) && $row["id"]) ? intval($row["id"]) : 0; ?>"/>
    <div class="modal-header"><h5 class="modal-title">Assign User Role</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="form-control-label">Biometric No</label>
            <p class="form-control"><?php echo (isset($row["biometricno"]) && $row["biometricno"]) ? $row["biometricno"] : "---"; ?></p>
        </div>
        <div class="form-group">
            <label class="form-control-label">Lastname</label>
            <p class="form-control"><?php echo (isset($row["lastname"]) && $row["lastname"]) ? ucwords(strtolower($row["lastname"])) : ""; ?></p>
        </div>
        <div class="form-group">
            <label class="form-control-label">Firstname</label>
            <p class="form-control"><?php echo (isset($row["firstname"]) && $row["firstname"]) ? ucwords(strtolower($row["firstname"])) : ""; ?></p>
        </div>
        <div class="form-group">
            <label class="form-control-label">Middlename</label>
            <p class="form-control"><?php echo (isset($row["middlename"]) && $row["middlename"]) ? ucwords(strtolower($row["middlename"])) : ""; ?></p>
        </div>
        <div class="form-group">
            <label class="form-control-label">User Role</label>
            <select id="role_id" name="role_id" class="form-control">
                <!--<option value="0" disabled selected>Select an option</option>-->
                <?php if ($roles): ?>
                    <?php foreach ($roles as $role): ?>
                        <?php if (isset($row["role_id"]) && $row["role_id"] && $role["id"] == $row["role_id"]): ?>
                            <option value="<?php echo $role["id"]; ?>" selected><?php echo $role["description"]; ?></option>
                        <?php else: ?>
                            <option value="<?php echo $role["id"]; ?>"><?php echo $role["description"]; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
        <?php $actions = $this->core_layout->getCurrentActions(); ?>
        <?php if (in_array("update", $actions)): ?>
            <button type="button" class="btn btn-primary btn-submit">Save</button>
        <?php endif; ?>
    </div>
</form>

<script type="text/javascript">
    $("select[name='role_id']")
        .select2({
            placeholder: "Select Role",
            width: "100%",
            dropdownParent: $("#form-users-assign")
        });
</script>
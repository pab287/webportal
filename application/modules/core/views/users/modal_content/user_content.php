<form action="<?php echo site_url('core/users/assign_user_role'); ?>" method="POST" id="form-users-assign">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="id" value="<?php echo (isset($row["id"]) && $row["id"]) ? intval($row["id"]) : 0; ?>"/>
    <div class="modal-header"><h5 class="modal-title">Assign User Role</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="" class="form-control-label">Biometric No</label>
            <p class="form-control" disabled><?php echo (isset($row["biometricno"]) && $row["biometricno"]) ? $row["biometricno"] : "---"; ?></p>
        </div>
        <div class="form-group">
            <label for="" class="form-control-label">Account Name</label>
            <p class="form-control" disabled><?php echo (isset($row["employee_name"]) && $row["employee_name"]) ? ucwords(strtolower($row["employee_name"])) : ""; ?></p>
        </div>
        <div class="form-group">
            <label for="" class="form-control-label">User Role</label>
            <select id="assign--role_id" name="role_id" class="form-control">
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
        <?php $actions = $this->core_layout->getCurrentActions(); ?>
        <?php if (in_array("update", $actions)): ?>
            <button type="button" class="btn btn-primary btn-submit">Save</button>
        <?php endif; ?>
        <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
    </div>
</form>

<script type="text/javascript">
    $("select#assign--role_id")
        .select2({
            placeholder: "Select Role",
            width: "100%",
            dropdownParent: $("#form-users-assign")
        });
</script>
<form id="frmAssignShiftSchedule" method="post" action="<?php echo site_url("personnel/set_assigned_shift"); ?>">
<input type="hidden" name="id" value="<?php echo $row->id? $row->id: 0; ?>" />
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-header">
<h5 class="modal-title">Assign Shift Schedule</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-control-label">Biometric No</label>
                <p class="form-control"><?php echo ($row->biometricno)? $row->biometricno: "---"; ?></p>
            </div>
            <div class="form-group">
                <label class="form-control-label">Employee Name</label>
                <p class="form-control"><?php echo ($row->name)? $row->name: "---"; ?></p>
            </div>
            <div class="form-group">
                <label class="form-control-label">Department</label>
                <p class="form-control cp-textarea"><?php echo $row->department? $row->department: "No assigned department"; ?></p>
            </div>
            <div class="form-group">
                <label class="form-control-label">Status</label>
                <p class="form-control"><?php echo ($row->is_active == 1)? "Active": "Inactive"; ?></p>
            </div>
        </div>
        <div class="col-md-8">
        <div class="m-portlet m-portlet--rounded m-portlet--info m-portlet--head-solid-bg m-portlet--bordered">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <span class="m-portlet__head-icon">
                            <i class="flaticon-settings"></i>
                        </span>
                        <h3 class="m-portlet__head-text">Settings</h3>
                    </div>
                </div>
            </div>
            <div class="m-portlet__body">
                <div class="form-group">
                    <label for="department_settings">Department</label>
                    <select id="department_settings" class="form-control" name="department_id" v-model="temp_items.department_id">
                        <option value=""></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="location_settings">Location</label>
                    <select id="location_settings" class="form-control" name="location_id" v-model="temp_items.location_id">
                        <option value=""></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="shift_settings">Shift Schedule</label>
                    <select id="shift_settings" class="form-control" name="shift_id" v-model="temp_items.shift_id">
                        <option value=""></option>
                    </select>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
<div class="modal-footer">
<button type="submit" class="btn btn-success btnSave">Save</button>
<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
</form>
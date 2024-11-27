<div class="modal-header">
    <h5 class="modal-title" id="departmentModalLabel"><i class="la la-plus mr-2"></i>Add Personnel Request</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-add_personnel_request" method="post" action="<?php echo site_url("hris/masterfile/set_modal_personnel_request"); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <div class="modal-body">
        <div class="row">
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label for="company_id" class="form-control-label">Company *</label>
                    <select id="company_id" name="company_id" autocomplete="off" data-validation="required" class="form-control m-input select2">
                        <option>
                        <option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="department_id" class="form-control-label">Department *</label>
                    <select id="department_id" name="department_id" autocomplete="off" data-validation="required"
                            class="form-control m-input select2">
                        <option>
                        <option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="position_id" class="form-control-label">Position *</label>
                    <select id="position_id" name="position_id" autocomplete="off" data-validation="required" class="form-control m-input select2">
                        <option>
                        <option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="type" class="form-control-label">Type *</label>
                    <select id="type" name="type" autocomplete="off" data-validation="required" class="form-control m-input select2">
                        <option>
                        <option>
                        <option value="SKILLED RANK AND FILE">SKILLED RANK AND FILE
                        <option>
                        <option value="RANK AND FILE">RANK AND FILE
                        <option>
                        <option value="SUPERVISORY">SUPERVISORY
                        <option>
                        <option value="MANAGERIAL">MANAGERIAL
                        <option>
                    </select>
                </div>
            </div>
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label for="salary_id" class="form-control-label">Salary *</label>
                    <select id="salary_id" name="salary_id" autocomplete="off" data-validation="required" class="form-control m-input select2">
                        <option>
                        <option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="requested_by" class="form-control-label">Requested By *</label>
                    <select id="requested_by" name="requested_by" autocomplete="off" data-validation="required" class="form-control m-input select2">
                        <option>
                        <option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="people_no" class="form-control-label">Needed *</label>
                    <input id="people_no" min="1"
                           name="people_no" type="number" maxlength="11" size="11" autocomplete="off" data-validation="required"
                           class="form-control m-input"/>
                </div>
                <div class="form-group">
                    <label for="need_dt" class="form-control-label">Date Needed *</label>
                    <div class="input-group">
                    <span class="input-group-addon">
                        <i class="la la-calendar"></i>
                    </span>
                        <input id="need_dt" type="text" name="need_dt" maxlength="12" size="12" autocomplete="off" data-validation="required"
                               class="form-control m-input"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="request_remark" class="form-control-label">Remarks</label>
            <textarea id="request_remark" name="request_remark" autocomplete="off" class="form-control m-input" rows="8"
                      style="min-height: 160px;"></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
        <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
    </div>

    <script type="text/javascript">
        $("input[name='people_no']")
            .on('keydown', function (e) {
                if (e.shiftKey == true) {
                    e.preventDefault();
                }

                if ((e.keyCode >= 48 && e.keyCode <= 57) ||
                    (e.keyCode >= 96 && e.keyCode <= 105) ||
                    e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 37 ||
                    e.keyCode == 39 || e.keyCode == 46 || e.keyCode == 190) {

                } else {
                    e.preventDefault();
                }

                if ($(this).val().indexOf('.') !== -1 && e.keyCode == 190)
                    e.preventDefault();
                //if a decimal has been added, disable the "."-button
            });
    </script>
</form>
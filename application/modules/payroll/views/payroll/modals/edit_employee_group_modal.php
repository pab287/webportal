<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Payroll Employee Group</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="frmUpdateEmployeeGroup" class="m-form" method="post" action="<?php echo site_url("payroll/employee/update_payroll_employee_group"); ?>">
      <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
      <input type="hidden" name="id" value="<?= $data->id; ?>">
      <div class="modal-body">
        <div class="form-group m-form__group">
            <label for="company_id">Company *</label>
            <div class="row">
                <div class="col-md-6">
                    <select id="company_id" class="form-control m-input" name="company_id" data-validation="required" <?= intval($data->company_id) == 0 ? "disabled": ""; ?>>
                      <?php if(intval($data->company_id) == 0): ?>
                        <option value="">&nbsp;<option>
                      <?php else: ?>
                        <option value="<?= $data->company_id; ?>"><?= $data->company_code; ?><option>
                      <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-6">
                  <div class="m-form__group form-group row">
                    <label class="col-9 col-form-label text-right">
                      All Company Filter
                    </label>
                    <div class="col-3">
                      <span class="m-switch m-switch--sm">
                        <label>
                          <input type="checkbox" value="1" id="all_company_filter" <?= intval($data->company_id) == 0 ? "checked": ""; ?> />
                          <span></span>
                        </label>
                      </span>
                    </div>
                  </div>
                </div>
            </div>
        </div>

        <div class="form-group m-form__group row">
          <label class="col-3 col-form-label text-left" style="font-weight: 600;">
            Active Employees<br>
            <span class="m-form__help p-0" style="text-transform: none; font-weight: 600;">Filter Employee Status</span>
          </label>
          <div class="col-1">
            <span class="m-switch m-switch--outline m-switch--sm m-switch--icon m-switch--success">
              <label>
                <input type="checkbox" value="1" id="active_employees" <?= intval($data->active_only) == "1" ? "checked": ""; ?> />
                <span></span>
              </label>
            </span>
          </div>
          <div class="col-6">
            <span class="m-form__help p-0" style="font-weight: 600;">Turning off this switch will include all employees on the search filter field.</span>
          </div>
        </div>

        <div class="form-group m-form__group">
            <label for="employee_id">Employee(s) *</label>
            <select id="employee_id" class="form-control m-input" name="employee_id[]" multiple="multiple" data-validation="required">
            <option value="">&nbsp;<option>
            </select>
        </div>
        <div class="form-group m-form__group">
            <label for="description">Description *</label>
            <input id="description" type="text" class="form-control m-input" name="description" data-validation="required" value="<?= $data->description; ?>" />
        </div>

        <div class="form-group m-form__group row align-items-center">
            <div class="col-md-4 pr-0">
              <label>Allow view by Company </label>
            </div>
            <div class="col-md-4 pl-0 d-flex align-items-center">
              <span class="m-switch m-switch--sm mr-2">
                <label class="m-0">
                  <input type="checkbox" value="1" name="is_allow_view" id="allow_view" />
                  <span class="m-0"></span>
                </label>
              </span>
              <span class="fa fa-question-circle" data-toggle="tooltip" title="Toggle this to assign employee" style="font-size: 18px"></span>
            </div>
        </div>

        <div id="assign_employee_div" class="form-group m-form_group" hidden>
            <label for="assign_employee_id">Assign Employee(s) *</label>
            <select id="assign_employee_id" class="form-control m-input" name="assigned_employee_id[]" multiple="multiple" data-validation="required">
                <option value="">&nbsp;<option>
            </select>
        </div>

        <div class="m-form__group form-group">
          <label>
            Group Status
          </label>
          <div class="m-checkbox-inline">
            <label class="m-checkbox">
              <input type="radio" name="status" value="1" <?= intval($data->status) == 1 ? "checked":""; ?> />
              Active
              <span></span>
            </label>
            <label class="m-checkbox">
              <input type="radio" name="status" value="0" <?= intval($data->status) == 0 ? "checked":""; ?> />
              Inactive
              <span></span>
            </label>
          </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x m--margin-top-5 m--margin-bottom-15"></div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group m-form__group">
                <label for="description">Created By </label>
                <?php $empData = (object) $this->core_layout->getEmployeeData($data->created_by); ?>
                <p class="m--marginless"><?= isset($empData->display_name_1) && $empData->display_name_1 ? $empData->display_name_1: "No assigned name";  ?></p>
                <p class="m--marginless m--font-bolder"><small><?= date("F d, Y h:i A", strtotime($data->created_at));  ?></small></p>
            </div>
          </div>
          <?php if(isset($data->updated_by) && $data->updated_by): ?>
            <div class="col-md-6">
            <div class="form-group m-form__group">
                <label for="description">Last Updated By </label>
                <?php $empData = (object) $this->core_layout->getEmployeeData($data->updated_by); ?>
                <p class="m--marginless"><?= isset($empData->display_name_1) && $empData->display_name_1 ? $empData->display_name_1: "No assigned name";  ?></p>
                <p class="m--marginless m--font-bolder"><small><?= date("F d, Y h:i A", strtotime($data->updated_at));  ?></small></p>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
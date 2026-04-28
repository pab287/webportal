<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">New Payroll Employee Group</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="frmNewEmployeeGroup" method="post" action="<?php echo site_url("payroll/employee/set_new_payroll_employee_group"); ?>">
      <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
      <div class="modal-body">
        <div class="form-group m-form__group">
            <label for="company_id">Company *</label>
            <div class="row">
                <div class="col-md-6">
                    <select id="company_id" class="form-control m-input" name="company_id" data-validation="required">
                        <option value="">&nbsp;<option>
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
                          <input type="checkbox" value="1" id="all_company_filter" />
                          <span></span>
                        </label>
                      </span>
                    </div>
                  </div>
                </div>
            </div>
        </div>
        <div class="form-group m-form__group">
          <div class="row">
            <div class="col-md-6">
              <label for="">Payout Mode *</label>
              <select id="payment_mode" name="payout_mode" class="form-control m-input" data-validation="required">
                <option value=""><option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="">Payout Schedule *</label>
              <select id="payroll_sched" name="payout_sched" class="form-control m-input" data-validation="required">
                <option value=""><option>
              </select>
            </div>
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
            <input id="description" type="text" class="form-control m-input" name="description" data-validation="required" />
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
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
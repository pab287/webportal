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
            <label for="employee_id">Employee(s) *</label>
            <select id="employee_id" class="form-control m-input" name="employee_id[]" multiple="multiple" data-validation="required">
                <option value="">&nbsp;<option>
            </select>
        </div>
        <div class="form-group m-form__group">
            <label for="description">Description *</label>
            <input id="description" type="text" class="form-control m-input" name="description" data-validation="required" />
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
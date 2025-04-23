<div class="modal fade" tabindex="-1" role="dialog" id="change-employee-company-dialog">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('hris/masterfile/change_employee_company') ?>"
              id="frm-change-employee-company-dialog" onsubmit="changeEmployeeCompany(this); return false;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">CHANGE COMPANY</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="current_company">
                    <input type="hidden" name="current_position">
                    <input type="hidden" name="current_department">
                    <input type="hidden" name="current_status">
                    <input type="hidden" name="work_from">
                    <input type="hidden" name="emp_id">

                    <div class="form-group">
                        <label for="" class="required">Date Started</label>
                        <div class="input-group date" id="m_datepicker_2">
                            <input type="text" class="form-control m-input" readonly
                                   placeholder="Select date" name="start_date"
                                   data-validation="required" value="<?= date('M d, Y') ?>">
                            <span class="input-group-addon">
                            <i class="la la-calendar-check-o"></i>
                        </span>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label for="" class="required">Company</label>
                        <select id="change-company-company_id" name="company_id" class="form-control"
                                data-validation="required"></select>
                    </div>

                    <div class="form-group mt-4">
                        <label for="" class="required">Department</label>
                        <select id="change-company-department_id" name="department_id" class="form-control"
                                data-validation="required"></select>
                    </div>

                    <div class="form-group mt-4">
                        <label for="" class="required">Position</label>
                        <select id="change-company-position" name="position" class="form-control"
                                data-validation="required"></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnEdit">Save changes</button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
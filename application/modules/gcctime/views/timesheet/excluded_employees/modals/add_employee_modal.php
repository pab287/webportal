<div class="modal fade" tabindex="-1" role="dialog"
     id="add-excluded-employee-from-timesheet-modal">
    <form id="frm-add-excluded-employee-from-timesheet" class="m-form">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ADD EXCLUDED EMPLOYEE</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group m-form__group">
                        <label for="employee-list" class="required">SELECT EMPLOYEE(S)</label>
                        <select name="employees[]" id="employee-list" class="form-control select2-multiple-custom" multiple></select>
                    </div>
                    <div class="form-group m-form__group">
                        <label for="exclusion-reason" class="required">Reason</label>
                        <textarea name="reason" id="exclusion-reason" class="form-control" data-validation="required"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnSave">SAVE</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </form>
</div>
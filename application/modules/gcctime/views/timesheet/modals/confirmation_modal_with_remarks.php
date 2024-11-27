<div class="modal fade" tabindex="-1" role="dialog"
     id="timesheet-confirmation-modal-with-remarks">
    <form action="" id="frm-timesheet-confirmation-modal-with-remarks" class="m-form">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="message"></div>
                    <div class="form-group m-form__group">
                        <label for="confirmation_remarks">
                            <span>Remarks/Comments</span>
                        </label>
                        <textarea name="confirmation_remarks" id="confirmation_remarks" rows="3" class="form-control"></textarea>
                    </div>
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="status" name="status">
                    <input type="hidden" id="modal-parent">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnSave"></button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal"></button>
                </div>
            </div>
        </div>
    </form>
</div>
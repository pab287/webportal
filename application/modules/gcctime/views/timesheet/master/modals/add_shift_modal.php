<div class="modal fade" tabindex="-1" role="dialog"
     id="add-shift-modal">
    <div class="modal-dialog" role="document">
        <form id="frm-add-shift-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="message-info" class="m--font-bolder">Modal body text goes here.</p>
                    <div class="form-group mt-4">
                        <label for="shift_id">SHIFT SCHEDULE</label>
                        <select name="shift_id" id="shift_id" class="form-control" data-validation="required"></select>
                    </div>

                    <div class="flex-column m--margin-top-25">
                        <label for="">FLEXI TIME?</label>
                        <div class="d-flex flex-row">
                            <span class="m-switch m-switch--outline m-switch--icon m-switch--info flex-grow-0 flex-shrink-0">
                                <label>
                                    <input type="checkbox" name="is_flexi" id="add-shift-is_flexi">
                                    <span></span>
                                </label>
                            </span>
                            <span class="flex-grow-0 flex-shrink-0 m--margin-top-5 m--margin-left-5
                                             m--font-metal"
                                  id="add-shift-is_flexi-label">
                                NO
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnSave">Save & Continue link</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">CANCEL</button>
                </div>
            </div>
        </form>
    </div>
</div>
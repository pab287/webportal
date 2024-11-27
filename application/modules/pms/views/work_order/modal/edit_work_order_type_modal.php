<div class="modal-dialog" role="document">
    <form action="<?php echo site_url("pms/work_order/edit_work_order_type/" . $id); ?>"
          id="frm-edit-wo-type">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Work Order Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Code *</label>
                    <input type="text" name="code" class="form-control m-input m--uniqueCode"
                           value="<?=$code?>"
                           data-validation="required" autocomplete="off"/>
                </div>
                <div class="form-group">
                    <label for="">Label *</label>
                    <input type="text" name="label" class="form-control m-input" data-validation="required"
                           value="<?=$label?>"
                           autocomplete="off"/>
                </div>
                <div class="form-group m-form__group row">
                    <div class="col-lg-6">
                        <label for="">Series *</label>
                        <input type="number" name="series" class="form-control m-input col-md-8"
                               value="<?=$series?>"
                               data-validation="required" autocomplete="off"/>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Status</label>
                        <div class="m-radio-inline">
                            <label class="m-radio">
                                <input id="status1" type="radio" name="is_active" value="1"
                                    <?= intval($is_active) === 0 ?: "checked" ?>>
                                Active<span></span>
                            </label>
                            <label class="m-radio">
                                <input id="status0" type="radio" name="is_active" value="0"
                                    <?= intval($is_active) === 1 ?: "checked" ?>>
                                Inactive<span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnSave">Save</button>
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
</div>
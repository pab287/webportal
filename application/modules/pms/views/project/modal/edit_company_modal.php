<div class="modal-dialog" role="document">
    <form action="<?php echo site_url("pms/project/edit_company/" . $id); ?>"
          id="frm-edit-company">
        <input type="hidden" name="csrf_token"
               value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Company</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Code *</label>
                    <input type="text" name="code" class="form-control m-input m--uniqueCode"
                           value="<?= $code ?>"
                           data-validation="required" autocomplete="off"/>
                </div>
                <div class="form-group">
                    <label for="">Description *</label>
                    <input type="text" name="description" class="form-control m-input"
                           value="<?= $description ?>"
                           data-validation="required" autocomplete="off"/>
                </div>
                <div class="form-group">
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
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
</div>
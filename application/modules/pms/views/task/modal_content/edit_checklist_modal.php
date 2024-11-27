<div class="modal-dialog" role="document">
    <form action="<?php echo site_url("pms/task/edit_checklist_item/" . $id); ?>"
          id="frm-edit-checklist-item">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Checklist Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Name *</label>
                    <input name="name" class="form-control m-input m--uniqueCode" data-validation="required"
                           value="<?= $name ?>"
                           autocomplete="off"/>
                </div>
                <div class="form-group">
                    <label for="">Label *</label>
                    <input name="label" class="form-control m-input" data-validation="required"
                           value="<?= $label ?>"
                           autocomplete="off"/>
                </div>
                <div class="form-group">
                    <label for="">Development Site *</label>
                    <select id="project_id" name="project_id" class="form-control m-input select2"
                            data-validation="required"></select>
                </div>
                <div class="form-group">
                    <label for="">Status</label>
                    <div class="m-radio-inline">
                        <label class="m-radio"><input id="status1" type="radio" name="is_active" value="1"
                                                      <?= intval($is_active) === 0 ?: "checked" ?> checked>Active<span></span></label>
                        <label class="m-radio"><input id="status0" type="radio" name="is_active"
                                                      <?= intval($is_active) === 1 ?: "checked" ?> value="0">Inactive<span></span></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnSave">Save Changes</button>
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
</div>
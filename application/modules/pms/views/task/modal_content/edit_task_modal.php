<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form action="<?= base_url("pms/task/edit_task_item/" . $id) ?>"
              id="frm-edit-task-modal">
            <div class="modal-header">
                <h5 class="modal-title">Edit Sequence Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" value="<?= $this->security->get_csrf_hash() ?>" name="csrf_token"
                       class="form-control">

                <div class="form-group">
                    <label for="">Name *</label>
                    <input type="text" class="form-control" autocomplete="off" name="name" data-validation="required"
                           value="<?= $name ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="">Label *</label>
                    <input type="text" class="form-control" autocomplete="off" name="label" data-validation="required"
                           value="<?= $label ?>">
                </div>

                <div class="form-group">
                    <label for="">Work Code</label>
                    <input type="text" class="form-control col-md-3" autocomplete="off" name="wo_code" 
                            value="<?= $wo_code ?>">
                </div>

                <div class="form-group">
                    <label for="">Work Order Type</label>
                    <select name="wo_type_id" id="work-order-type" class="form-control"></select>
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
                <button type="submit" class="btn btn-primary btnSave">Save changes</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>
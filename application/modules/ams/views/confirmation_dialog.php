<form id="confirmation-dialog" action="<?= $action ?>" data-table="<?= isset($table) ? $table : "" ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

    <div class="modal-dialog" role="dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?= $title ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div style="font-size: 16px;">
                    <?= $message ?>
                </div>

                <?php if (isset($type) && $type == 'archive'): ?>
                    <div class="mt-3">
                        <label for="" class="required">Status </label>
                        <select name="status" id="restore-status" class="form-control" data-validation="required">
                            <option value=""></option>
                            <option value="brandnew">Brand New</option>
                            <option value="operational">Operational</option>
                            <option value="repair">Repair</option>
                        </select>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew">
                    Yes
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    No
                </button>
            </div>
        </div>
    </div>
</form>
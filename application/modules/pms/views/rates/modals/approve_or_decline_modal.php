<?php
$title = $label === "approve" ? "<span class='text-primary'>Approval</span>" : "<span class='text-danger'>Decline</span>";
?>
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form id="frm-approve-or-decline" action="<?= base_url("pms/rates/set_rate_status/" . $rate_id) ?>">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?= $title ?> Confirmation
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4>Are you sure to
                    <span class="m--font-bolder <?= $label === 'approve' ? "text-primary" : "text-danger" ?>"><?= $label ?></span>
                    this rate for <span class="m--font-bolder"><?= $item->label ?></span>?
                </h4>

                <div class="form-group pt-4">
                    <label for="">Remarks (Optional)</label>
                    <textarea name="approved_remarks" id="approved_remarks" class="form-control"></textarea>
                </div>

                <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="approved_status" value="<?= $label === 'approve' ? 1 : 2 ?>">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Yes</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>
        </form>
    </div>
</div>

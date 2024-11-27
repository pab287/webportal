<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <?php if(strtolower($title) == "approval"): ?>
                <h5 class="modal-title" id="exampleModalLabel"><span class="m--font-bolder text-primary"><?php echo $title; ?></span> Confirmation</h5>
            <?php else: ?>
                <h5 class="modal-title" id="exampleModalLabel"><span class="m--font-bolder text-danger"><?php echo $title; ?></span> Confirmation</h5>
            <?php endif; ?>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="frmApprovalTaskQty" method="post" action="<?php echo site_url("pms/task/do_post_event/set_approval_checklist_item_qty"); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="id" value="<?php echo ($id)? $id: 0; ?>" />
        <input type="hidden" name="approval_status" value="<?php echo ($approval_status)? $approval_status: 0; ?>" />
        <div class="modal-body">
            <?php if(strtolower($title) == "approval"): ?>
                <h4 class="m--margin-bottom-25">Are you sure to <span class="m--font-bolder text-primary"><?php echo $status; ?></span> this rate for <span class="m--font-bolder"><?php echo $current_task; ?></span>?</h4>
            <?php else: ?>
                <h4 class="m--margin-bottom-25">Are you sure to <span class="m--font-bolder text-danger"><?php echo $status; ?></span> this rate for <span class="m--font-bolder"><?php echo $current_task; ?></span>?</h4>
            <?php endif; ?>
            <div class="form-group m-form__group">
                <label for="approval_remarks" class="m-widget1__title m--margin-bottom-10">Remarks (Optional)</label>
                <textarea class="form-control m-input" id="approval_remarks" name="approval_remarks" data-validation="required" rows="5"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-submit btnSave">Yes</button>
            <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">No</button>
        </div>
        </form>
    </div>
</div>
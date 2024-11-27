<div class="modal-header">
    <?php if($status == "approval"): ?>
    <h5 class="modal-title" id="personnelRequestModalLabel">Approve Personnel Request</h5>
    <?php elseif($status == "complete"): ?>
    <h5 class="modal-title" id="personnelRequestModalLabel">Complete Personnel Request</h5>
    <?php else: ?>
    <h5 class="modal-title" id="personnelRequestModalLabel">Deny Personnel Request</h5>
    <?php endif; ?>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-approval_personnel_request" method="post" action="<?php echo site_url("hris/masterfile/set_modal_approval_personnel_request"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<?php
    $tempStatusValue = "Ongoing";
    switch($status){
        case "approval": $tempStatusValue = "Ongoing"; break;
        case "complete": $tempStatusValue = "Completed"; break;
        case "deny": $tempStatusValue = "Denied"; break;
        default: $tempStatusValue = "Ongoing"; break;
    }
?>
<input type="hidden" name="status" value="<?php echo $tempStatusValue; ?>" />
<div class="modal-body">
    <div class="row">
        <div class="col-12">
        <?php if($status == "approval"): ?>
            <h5 style="line-height: 24px;">Are you sure you want to Approve this personnel request?</h5>
        <?php elseif($status == "complete"): ?>
            <h5 style="line-height: 24px;">Are you sure you want to Complete this personnel request?</h5>
        <?php else: ?>
            <h5 style="line-height: 24px;">Are you sure you want to Deny this personnel request?</h5>
        <?php endif; ?>
        </div>
    </div>
    <div class="form-group" style="margin-top: 25px;">
        <?php if($status == "approval"): ?>
            <label for="remark" class="form-control-label">Reason for Approval</label>
            <textarea id="remark" name="remark" autocomplete="off" class="form-control m-input" rows="8" style="min-height: 160px;"></textarea>
        <?php elseif($status == "complete"): ?>
            <label for="completed_remark" class="form-control-label">Reason for Completing Personnel Request *</label>
            <textarea id="completed_remark" name="completed_remark" autocomplete="off" class="form-control m-input" rows="8" style="min-height: 160px;"></textarea>
        <?php else: ?>
            <label for="remark" class="form-control-label">Reason for Denial *</label>
            <textarea id="remark" name="remark" autocomplete="off" class="form-control m-input" rows="8" style="min-height: 160px;" data-validation="required"></textarea>
        <?php endif; ?>
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave">Submit</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal">Cancel</button>
</div>
</form>
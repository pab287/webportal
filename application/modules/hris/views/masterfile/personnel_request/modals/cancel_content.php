<?php
$tempStatus = "Cancelled";
$tempStatusNote = "Cancel";
switch($status){
    case "cancel": 
        $tempStatus = "Cancelled"; 
        $tempStatusNote = "Cancel";
    break; 
    case "hold": 
        $tempStatus = "onHold"; 
        $tempStatusNote = "Hold";
    break;
    case "activate": 
        $tempStatus = "Ongoing"; 
        $tempStatusNote = "Activate";
    break;
    default:
        $tempStatus = "Cancelled"; 
        $tempStatusNote = "Cancel";
    break; 
}
?>
<div class="modal-header">
    <h5 class="modal-title" id="personnelRequestModalLabel"><?php echo $tempStatusNote; ?> Personnel Request</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-set_status_personnel_request" method="post" action="<?php echo site_url("hris/masterfile/set_modal_status_personnel_request"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="status" value="<?php echo $tempStatus; ?>" />
<div class="modal-body">
    <p>Are you sure you want to <?php echo $tempStatusNote; ?> this personnel request?</p>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btn-submit btnSave">Submit</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal">Cancel</button>
</div>
</form>
<style>
@media (max-width: 767px) {
  .password-change-reminder .modal-dialog {
    width: 95%;
    margin: 10px auto;
    max-width: none;
  }
  
  .password-change-reminder .modal-footer {
    flex-direction: column;
    align-items: stretch;
    padding: 15px;
  }
  
  .password-change-reminder .modal-footer button {
    margin: 5px 0;
    padding: 12px;
    width: 100%;
    font-size: 16px;
  }
  
  .password-change-reminder .modal-body {
    font-size: 16px;
    padding: 20px 15px;
  }
  
  .password-change-reminder .modal-header {
    padding: 15px;
  }
  
  .password-change-reminder .modal-title {
    font-size: 18px;
    width: 100%;
    text-align: center;
  }
}
</style>
<div class="modal fade password-change-reminder" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog" role="dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordReminderLabel">Password Update Required</h5>
            </div>
            <div class="modal-body">
                <p>For your account security, we require you to update your password as it has been over 
                    <?php
                    if (isset($this->session->userdata('logged_in')['is_important']) && 
                        $this->session->userdata('logged_in')['is_important'] == 1):
                    ?>
                        60 days
                    <?php else: ?>
                        90 days
                    <?php endif; ?>
                    since your last change. Please take a moment to set a new password now.
                </p>
                <p id="waiveNull" class="small" style="color: red; display: none;">
                    <small>You have exceeded 3 waived attempts. You are now required to update your password.</small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" id="changePasswordLater" class="btn btn-warning btnClose" data-bs-dismiss="modal" onclick="changePasswordLater(<?php echo $this->session->userdata('logged_in')['id']; ?>)">
                    Waive Password Update
                </button>
                <button type="button" id="changePasswordNow" class="btn btn-primary btnSave" data-bs-dismiss="modal" onclick="changePasswordNow()">
                    Update Password Now
                </button>
            </div>
        </div>
    </div>
</div>
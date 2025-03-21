<div class="modal fade password-change-reminder" tabindex="-1">
    <div class="modal-dialog" role="dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordReminderLabel">Password Update Required</h5>
            </div>
            <div class="modal-body">
                <p>For your account security, we require you to update your password as it has been over 60 days since your last change. Please take a moment to set a new password now.</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="changePasswordLater" class="btn btn-warning btnClose" data-bs-dismiss="modal" onclick="changePasswordLater()">
                    Remind Me Later (30 Days)
                </button>
                <button type="button" id="changePasswordNow" class="btn btn-primary btnSave" data-bs-dismiss="modal" onclick="changePasswordNow()">
                    Update Password Now
                </button>
            </div>
        </div>
    </div>
</div>
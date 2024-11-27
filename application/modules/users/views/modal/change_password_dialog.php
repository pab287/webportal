<div class="modal fade change-password-modal"
     tabindex="-1">
    <div class="modal-dialog" role="dialog">
        <form id="changepassword_modal" action="" onsubmit="event.preventDefault(); processChangePassword(this)">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        CHANGE PASSWORD
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $user->id ?>">
                    <div class="form-group m-form__group">
                        <label>
                            ENTER NEW PASSWORD
                            <span class="text-danger">*</span>
                        </label>
                        <div class="m-input-icon m-input-icon--right">
                            <input type="password" class="form-control m-input password"
                                   style="height: auto; text-transform: none;"
                                   data-validation="required length" data-validation-length="min8"
                                   name="password">
                            <span class="m-input-icon__icon m-input-icon__icon--right">
                                <span style="cursor: pointer;">
                                    <i class="la la-eye btn-reveal-password"></i>
                                </span>
                            </span>
                        </div>
                        <div class="mt-1" style="text-transform: none;">
                            Enter unique combination. Minimum of <strong class="text-primary">eight characters</strong>.
                        </div>
                    </div>
                    <div class="form-group m-form__group mt-5">
                        <label>
                            PIN VERIFICATION
                            <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control m-input" style="height: auto; letter-spacing: 12px;"
                               data-validation="required" name="reset_pin" maxlength="6">
                        <div class="mt-1" style="text-transform: none;">
                            Enter your <strong class="text-primary">PIN</strong> created when you first log in.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnSave" disabled>
                        SAVE CHANGES
                    </button>
                    <button type="button" class="btn btn-danger btnClose"
                            data-dismiss="modal">
                        CANCEL
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $("[name='reset_pin']")
        .bind('input paste', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
</script>
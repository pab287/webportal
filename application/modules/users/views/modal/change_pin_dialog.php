<div class="modal fade change-pin-modal"
     tabindex="-1">
    <div class="modal-dialog" role="dialog">
        <form action="" onsubmit="event.preventDefault(); processChangePin(this)">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        CHANGE PIN
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
                            ENTER OLD PIN
                            <span class="text-danger">*</span>
                        </label>
                        <div class="m-input-icon m-input-icon--right">
                            <input type="password" class="form-control m-input password"
                                   style="height: auto; letter-spacing: 12px;"
                                   data-validation="required | length |" data-validation-length="min6"
                                   name="old_pin" maxlength="6">
                            <span class="m-input-icon__icon m-input-icon__icon--right">
                                <span style="cursor: pointer;">
                                    <i class="la la-eye btn-reveal-password"></i>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group m-form__group">
                        <label>
                            ENTER NEW PIN
                            <span class="text-danger">*</span>
                        </label>
                        <div class="m-input-icon m-input-icon--right">
                            <input type="password" class="form-control m-input password"
                                   style="height: auto; letter-spacing: 12px;"
                                   data-validation="required | length" data-validation-length="min6"
                                   name="new_pin" maxlength="6">
                            <span class="m-input-icon__icon m-input-icon__icon--right">
                                <span style="cursor: pointer;">
                                    <i class="la la-eye btn-reveal-password"></i>
                                </span>
                            </span>
                        </div>
                        <div class="mt-1" style="text-transform: none;">
                            Enter unique combination. Maximum of <strong class="text-primary">six characters</strong>.
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
    $("[name='old_pin']")
        .bind('input paste', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

    $("[name='new_pin']")
        .bind('input paste', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });    
</script>
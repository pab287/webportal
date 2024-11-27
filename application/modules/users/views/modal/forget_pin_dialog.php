<div class="modal fade forget-pin-modal"
     tabindex="-1">
    <div class="modal-dialog" role="dialog">
        <form action="" id="forget_pin_form" method="POST" onsubmit="event.preventDefault(); sendPin(this)">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        FORGOT PIN
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="user_id" value="<?= $user->id ?>">
                    <p>The default pin will be sent to the email address of this account.</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnSave">
                        SEND PIN
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

<div class="modal-dialog" role="document">
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
            <div style="text-transform: none; font-size: 18px;">
                <?= $message ?>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn <?= $color ?> btnNew"
                    data-dismiss="modal">
                Ok, I understand.
            </button>
        </div>
    </div>
</div>
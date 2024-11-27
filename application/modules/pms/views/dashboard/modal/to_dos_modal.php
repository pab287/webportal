<style>
    .m-badge {
        word-break: break-word;
    }
</style>

<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header" style="font-family: sans-serif;">
            <h5 class="modal-title" style="font-size: 1.5rem;">
                <span class="m--font-bolder"><?= $details->description ?></span>
                <span class="m--font-bolder m--font-danger ml-2">TO-DO<span style="text-transform: lowercase">s</span></span>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body pt-0">
            <div class="row m-row--col-separator-xl"
                 id="modal-to-do-list">
                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-1 mt-4 pb-3">
                    <p class="status-title">AWAITING</p>
                    <?php
                        $awaiting = $todos->awaiting;
                        foreach ($awaiting as $key => $todo) {
                            ?>
                            <div class="<?= $key === 0 ?: "mt-3" ?>">
                                <?php if (count($todo->children) > 0) : ?>
                                    <p class="header"><?= $todo->parent ?></p>
                                    <ul class="children">
                                        <?php foreach ($todo->children as $child) { ?>
                                            <li>
                                                <span class="m-badge px-3 py-1"><?= $child->label ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                            <?php
                        } ?>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-1 mt-4 pb-3">
                    <p class="status-title">IN PROGRESS</p>
                    <?php
                        $inProgress = $todos->inProgress;
                        foreach ($inProgress as $key => $todo) {
                            ?>
                            <div class="<?= $key === 0 ?: "mt-3" ?>">
                                <?php if (count($todo->children) > 0) : ?>
                                    <p class="header"><?= $todo->parent ?></p>
                                    <ul class="children">
                                        <?php foreach ($todo->children as $child) { ?>
                                            <li>
                                                <span class="m-badge m-badge--primary px-3 py-1"><?= $child->label ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                            <?php
                        } ?>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-1 mt-4 pb-3">
                    <p class="status-title">BACK LOGS</p>
                    <?php
                        $backLogs = $todos->backLogs;
                        foreach ($backLogs as $key => $todo) {
                            ?>
                            <div class="<?= $key === 0 ?: "mt-3" ?>">
                                <?php if (count($todo->children) > 0) : ?>
                                    <p class="header"><?= $todo->parent ?></p>
                                    <ul class="children">
                                        <?php foreach ($todo->children as $child) { ?>
                                            <li>
                                                <span class="m-badge m-badge--danger px-3 py-1"><?= $child->label ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                            <?php
                        } ?>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-1 mt-4 pb-3">
                    <p class="status-title">DEFERRED</p>
                    <?php
                        $deferred = $todos->deferred;
                        foreach ($deferred as $key => $todo) {
                            ?>
                            <div class="<?= $key === 0 ?: "mt-3" ?>">
                                <?php if (count($todo->children) > 0) : ?>
                                    <p class="header"><?= $todo->parent ?></p>
                                    <ul class="children">
                                        <?php foreach ($todo->children as $child) { ?>
                                            <li>
                                                <span class="m-badge m-badge--success px-3 py-1"><?= $child->label ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                            <?php
                        } ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
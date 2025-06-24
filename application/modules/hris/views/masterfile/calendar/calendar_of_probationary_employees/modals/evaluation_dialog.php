<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form id="frm-add-probee-evaluation">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="exampleModalLabel" style="font-weight: 600;"><?= $title ?></h5>
                    <p class="mb-0"><small><?= $string_eval ?></small></p>
                </div>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body text-center">
                <?php if ($evaluation_stage != 'status') { ?>
                    <div class="btn-group m-btn-group" role="group" aria-label="..."
                         style="width: 100%;">
                        <button type="button" class="btn btn-success"
                                style="width: 33%;"
                                onclick="addProbeeEvaluation('<?= $cal_id ?>','<?= $emp_id ?>','pass', '<?= $evaluation_stage ?>')">
                            PASS
                        </button>
                        <button type="button" class="btn btn-warning"
                                style="width: 33%;"
                                onclick="addProbeeEvaluation('<?= $cal_id ?>','<?= $emp_id ?>','fail', '<?= $evaluation_stage ?>')">
                            FAIL
                        </button>
                        <button type="button" class="btn btn-danger"
                                style="width: 33%;"
                                onclick="addProbeeEvaluation('<?= $cal_id ?>','<?= $emp_id ?>','discontinue', 'status')">
                            DISCONTINUE
                        </button>
                    </div>

                <?php } else { ?>
                    <div class="btn-group m-btn-group" role="group" aria-label="..."
                         style="width: 100%;">
                        <button type="button" class="btn btn-success"
                                style="width: 50%;"
                                onclick="addProbeeEvaluation('<?= $cal_id ?>','<?= $emp_id ?>','regularize', 'status')">
                            Regularize
                        </button>
                        <button type="button" class="btn btn-danger"
                                style="width: 50%;"
                                onclick="addProbeeEvaluation('<?= $cal_id ?>','<?= $emp_id ?>','discontinue', 'status')">
                            DISCONTINUE
                        </button>
                    </div>

                <?php } ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger modalClose" data-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>
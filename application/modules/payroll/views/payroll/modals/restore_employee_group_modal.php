<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Restore Payroll Employee Group</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <form id="frmRestoreEmployeeGroup" method="post" action="<?php echo site_url("payroll/employee/restore_payroll_employee_group"); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id" value="<?= $data->id; ?>" />

            <div class="modal-body">
                <h5>Are you sure you want to restore this payroll employee group entry?</h5>

                <div class="form-group m-form__group row m--margin-bottom-0">
                    <label class="col-lg-3 col-form-label m--marginless">Company</label>
                    <div class="col-lg-6 col-form-label m--marginless"><?= $data->company_code; ?></div>
                </div>

                <div class="form-group m-form__group row m--margin-bottom-0">
                    <label class="col-lg-3 col-form-label m--marginless">Description</label>
                    <div class="col-lg-6 col-form-label m--marginless"><?= $data->description; ?></div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label m--marginless">Included Employee(s)</label>
                    <div class="col-lg-8 col-form-label m--marginless m--font-bolder">
                        <?php 
                            if($data->employees) {
                                $tempNames = array();

                                foreach($data->employees as $rs) {
                                    $tempNames[] = $rs["text"];
                                }

                                echo implode(", ", $tempNames);
                            } else {
                                echo "---";
                            } 
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">YES</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">NO</button>
            </div>
        </form>
    </div>
</div>
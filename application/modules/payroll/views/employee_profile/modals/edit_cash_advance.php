<?php
    $date_applied = new DateTime($data->created_dt);
    $data_applied = $date_applied->format("M d,Y h:i:s a");
?>

<div class="modal-dialog" role="dialog">
    <form action="<?= base_url('payroll/employee/update_cash_advance') ?>"
          id="employee-data-update-cash-advance">
        <div class="modal-content">
            <input type="hidden" name="id" value="<?= $data->id ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <div class="modal-header">
                <h5 class="modal-title">Edit Cash Advance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group m-form m-form__group">
                    <label for="">Reference No. <span class="text-danger">*</span></label>
                    <input type="text" disabled class="form-control m-input" name="leg_case_no" value="<?= $data->reference_no ?>"
                           autocomplete="off"/>
                </div>

                <div class="form-group m-form m-form__group mt-4">
                    <label for="">Purpose <span class="text-danger">*</span></label>
                    <textarea class="form-control m-input" name="purpose" data-validation="required"><?= $data->purpose ?></textarea>
                </div>

                <div class="row mt-4">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group m-form m-form__group">
                            <label for="">Amount Applied <span class="text-danger">*</span></label>
                            <input type="number" class="form-control m-input text-right" name="amt_applied"
                                   value="<?= $data->amt_applied ?>" min="0"
                                   autocomplete="off" data-validation="required"/>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group m-form m-form__group">
                            <label for="">
                                Amount Approved
                                <?php if ($data->status == 'Approved') echo '<span class="text-danger">*</span>' ?>
                            </label>
                            <input type="number" class="form-control m-input" name="amt_approved"
                                   value="<?= $data->amt_approved ?>" min="0"
                                   <?= $data->status == 'Disapproved' ? 'disabled' : 'data-validation="required"' ?>
                                   autocomplete="off"/>
                        </div>
                    </div>
                </div>

                <div class="form-group m-form m-form__group mt-4">
                    <label for="">Date</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input type="text" maxlength="12" size="12"
                               name="leg_case_date"
                               value="<?= $data_applied ?>"
                               data-validation="required"
                               class="form-control m-input date" disabled/>
                    </div>
                </div>

                <div class="form-group m-form m-form__group mt-4">
                    <label for="">Status</label>
                    <input type="text" class="form-control m-input" name="leg_court_field"
                           value="<?= $data->status ?>" disabled/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnEdit">
                    Save Changes
                </button>
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">
                    Cancel
                </button>
            </div>
        </div>
    </form>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="generate-manually-modal">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form method="POST"
            action="<?= base_url('gcctime/timesheet_cron/create_multiple/null/1') ?>"
            class="m-form" id="frm-generate-manually">
            <div class="modal-header">
                <h5 class="modal-title">GENERATE TIMESHEET MANUALLY</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group"
                            style="min-height: 76px;">
                        <label for="" class="required">Start Date</label>
                        <div class="input-group date" id="date-start">
                            <span class="input-group-addon">
                                <i class="la la-calendar-check-o"></i>
                            </span>
                            <input name="dateStart" type="text" class="form-control" data-validation="required"
                                    autocomplete="off">
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group"
                            style="min-height: 76px;">
                        <label for="" class="required">End Date</label>
                        <div class="input-group date" id="date-end">
                            <span class="input-group-addon">
                                <i class="la la-calendar-check-o"></i>
                            </span>
                            <input name="dateEnd" type="text" class="form-control" data-validation="required"
                                    autocomplete="off">
                        </div>
                    </div>
                </div>
                
                <div class="form-group m-form__group pt-0 pb-3">
                    <label for="company">COMPANY
                    <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                    </label>
                    <select name="company" id="company" class="form-control">
                        <option></option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?= $company->id ?>"><?= $company->text ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group m-form__group pt-0 pb-3">
                    <label for="payroll_group">
                        PAYROLL GROUP
                        <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                    </label>
                    <select class="form-control select2" id="payroll_group" multiple></select>
                </div>
                
                <div class="form-group m-form__group pt-0 pb-3">
                    <label for="">SELECT EMPLOYEES</label>
                    <select name="employees[]"
                            class="form-control select2-multiple-custom" id="employees"
                            multiple></select>
                </div>

                <label class="m-checkbox m-checkbox--info mb-0 m--font-bold text-muted"
                        style="padding-left: 24px;">
                    <input type="checkbox" id="cb-newly">Newly Added
                    <span></span>
                </label>

                <div class="form-group m--margin-top-40">
                    <label for="remarks" class="required">Remarks</label>
                    <textarea name="remarks" id="remarks" cols="5" rows="3" class="form-control"
                                data-validation="required"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btnAdvance_search text-white" onclick="resetFilter(this)">
                    <span>
                        <i class="fa fa-refresh"></i>
                        <span>Reset Filter</span>
                    </span>
                </button>
                <button type="submit" class="btn btn-primary btnSave">Generate</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </form>
        </div>
    </div>
</div>
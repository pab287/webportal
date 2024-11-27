<?php
    $_date = new DateTime($date);
?>
<div class="modal-dialog" role="dialog">
    <form action="<?= base_url('hris/calendar/save_holiday') ?>"
          id="calendar-save-holiday">
        <input type="hidden" value="<?= $fromTabular ?>">
        <input type="hidden" name="csrf_token"
               value="<?= $this->security->get_csrf_hash(); ?>">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="la la-plus mr-2"></i>ADD HOLIDAY</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Date From</label>
                    <div class="input-group">
                    <span class="input-group-addon">
                        <i class="la la-calendar"></i>
                    </span>
                        <input name="date_from" type="text" class="form-control m-input date"
                               value="<?= $_date->format('F d,Y') ?>" data-validation="required"
                               <?= $fromTabular == 'true' ? '' : 'readonly style="background-color: #f4f5f8;"' ?>>
                    </div>
                </div>

                <div class="form-group">
                    <label for="">Date To</label>
                    <div class="input-group">
                    <span class="input-group-addon">
                        <i class="la la-calendar"></i>
                    </span>
                        <input name="date_to" type="text" class="form-control m-input date"
                               value="<?= $_date->format('F d,Y') ?>" data-validation="required"
                               <?= $fromTabular == 'true' ? '' : 'readonly style="background-color: #f4f5f8;"' ?>>
                    </div>
                    <p style="margin-top: 5px; color: #ff0000;"><small><strong>Note:</strong> set the time to 00:00 for a whole day event.</small></p>
                </div>

                <div class="form-group mt-4">
                    <label for="">
                        Holiday Description
                        <span class="text-danger">*</span>
                    </label>
                    <textarea name="description" class="form-control m-input" data-validation="required"></textarea>
                </div>
                <div class="form-group mt-4">
                    <label for="classification">
                        Classification
                        <span class="text-danger">*</span>
                    </label>
                    <select name="classification" class="form-control m-input" id="classification" data-validation="required">
                    </select>
                </div>
                <div class="form-group mt-4">
                    <label for="company">
                        Company
                        <!-- <span class="text-danger">*</span> -->
                    </label>
                    <select class="form-control m-input" name="company[]" id="company" multiple="multiple"></select>
                </div>
                <div class="form-group mt-4">
                    <label for="department">
                        Department
                        <!-- <span class="text-danger">*</span> -->
                    </label>
                    <select class="form-control m-input" name="department[]" id="department" multiple="multiple" disabled></select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="la la-check mr-2"></i>Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="la la-times mr-2"></i>Close</button>
            </div>
        </div>
    </form>
</div>
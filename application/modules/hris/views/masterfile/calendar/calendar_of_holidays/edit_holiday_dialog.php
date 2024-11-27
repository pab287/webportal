<?php
    $_date_from = new DateTime($start_date);
    $_date_to = new DateTime($end_date);
?>
<div class="modal-dialog" role="dialog">
    <form action="<?= base_url('hris/calendar/update_holiday') ?>"
          id="calendar-edit-holiday">
        <input type="hidden" name="csrf_token"
               value="<?= $this->security->get_csrf_hash(); ?>">
        <input type="hidden" value="<?= $id ?>" name="id">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="la la-edit mr-2"></i>EDIT HOLIDAY</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" value=false name="drag">
                <div class="form-group">
                    <label for="">Date From</label>
                    <div class="input-group">
                    <span class="input-group-addon">
                        <i class="la la-calendar"></i>
                    </span>
                        <input name="date_from" type="text" class="form-control m-input date"
                               value="<?= $_date_from->format('F d,Y') ?>" data-validation="required"
                               <?= $fromTabular == true ? '' : 'readonly style="background-color: #f4f5f8;"' ?>>
                    </div>
                </div>

                <div class="form-group">
                    <label for="">Date To</label>
                    <div class="input-group">
                    <span class="input-group-addon">
                        <i class="la la-calendar"></i>
                    </span>
                        <input name="date_to" type="text" class="form-control m-input date"
                               value="<?= $_date_to->format('F d,Y') ?>" data-validation="required"
                               <?= $fromTabular == true ? '' : 'readonly style="background-color: #f4f5f8;"' ?>>
                    </div>
                    <p style="margin-top: 5px; color: #ff0000;"><small><strong>Note:</strong> set the time to 00:00 for a whole day event.</small></p>
                </div>

                <div class="form-group mt-4">
                    <label for="">
                        Holiday Description
                        <span class="text-danger">*</span>
                    </label>
                    <textarea name="description" class="form-control m-input" data-validation="required"><?= $description ?></textarea>
                </div>
                <div class="form-group mt-4">
                    <label for="classification">
                        Clasification
                        <span class="text-danger">*</span>
                    </label>
                    <select name="classification" class="form-control m-input" id="classification" data-validation="required">
                    </select>
                </div>
                <div class="form-group mt-4">
                    <label for="company">
                        Company
                    </label>
                    <select class="form-control m-input" name="company[]" id="company" multiple="multiple"></select>
                </div>
                <div class="form-group mt-4">
                    <label for="department">
                        Department
                    </label>
                    <select class="form-control m-input" name="department[]" id="department" multiple="multiple" disabled></select>
                </div>
            </div>
            <div class="modal-footer">
                <div class="flex-row d-flex flex-grow-1 flex-shrink-1">
                    <div class="flex-grow-1 flex-shrink-1">
                        <button type="button" class="btn btn-danger m-btn m-btn--icon m-btn--icon-only float-left"
                                onclick="confirmDeleteHoliday(<?= $id ?>)">
                            <i class="la la-trash-o"></i>
                        </button>
                    </div>
                    <div class="flex-grow-1 flex-shrink-1 text-right">
                        <button type="submit" class="btn btn-primary"><i class="la la-check mr-2"></i>Save Changes</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="la la-times mr-2"></i>Close</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
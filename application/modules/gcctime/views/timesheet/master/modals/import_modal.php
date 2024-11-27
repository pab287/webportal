<div class="modal fade" tabindex="-1" role="dialog"
     id="timesheet-import-modal">
    <form id="frm-timesheet-import-modal" class="m-form">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group m-form__group">
                        <label class="required">ASSIGNED DEVICE</label>
                        <select name="device_id" id="device-id" class="form-control" data-validation="required">
                            <option></option>
                            <?php foreach ($biometric_devices as $biometric_device): ?>
                                <option value="<?= $biometric_device->id ?>"><?= $biometric_device->device_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group m-form__group">
                        <label for="exampleInputEmail1" class="required">SELECT FILE</label>
                        <div></div>
                        <label class="custom-file">
                            <input type="file" name="file_import" id="file-import" class="custom-file-input"
                                   data-validation="required"
                                   accept=".dat,.csv,.xlsx,.xls,,.txt">
                            <span class="custom-file-control"></span>
                        </label>
                    </div>

                    <div class="form-group m-form__group">
                        <label class="required">IMPORT INCLUSIVE DATES</label>
                        <div class="m-input-icon" id="import-inclusive-dates">
                            <input type="text" class="form-control m-input" data-validation="required"
                                   name="inclusive_dates" autocomplete="off">
                            <span class="m-input-icon__icon m-input-icon__icon--right">
                                <span>
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="form-group m-form__group">
                        <label>REMARKS</label>
                        <textarea name="remarks" id="" class="form-control"></textarea>
                    </div>

                    <input type="hidden" name="type" id="type">
                    <div class="m--margin-top-20 m-alert m-alert--outline m-alert--outline-2x alert alert-info"
                         style="display: none;"
                         id="importing-alert-message">
                        <div class="d-flex flex-row">
                            <div class="flex-grow-0 flex-shrink-0 m--margin-right-10">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="m--font-bolder">
                                Importing & Generating Timesheet. Please wait...
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnExport btn-import m-btn m-btn--icon">
                        <span><i class="fa fa-download"></i><span
                                    class="btn-import__text">Import & Generate</span></span>
                    </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form id="employee-data-update-licensure" method="post" action="<?= base_url("hris/masterfile/update_licensure"); ?>">
            <div class="modal-header">
                <!-- <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Edit Licensure Exam and Certification</h5> -->
                <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Edit Licenses and Certifications</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id" value="<?= $data->id; ?>">

            <div class="modal-body">
                <div class="form-group">
                    <label for="license_type" class="form-control-label">
                        License Type
                        <span class="text-danger">*</span>
                    </label>
                    <!-- <input id="license_type" name="license_type" type="text" maxlength="100" size="100" autocomplete="off" data-validation="required"
                           class="form-control m-input" value="<?//= $data->license_type ?>"/> -->
                    <select id="license_type" name="license_type" data-validation="required" class="form-control m-input">
                        <option value="">Select an Option</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exam_place" class="form-control-label">Exam Place *</label>
                    <input id="exam_place" name="exam_place" type="text" maxlength="100" size="100" data-validation="required" autocomplete="off" class="form-control m-input"
                           value="<?= $data->exam_place ?>"/>
                </div>
                <div class="form-group">
                    <label for="rating" class="form-control-label">Rating</label>
                    <input id="rating" name="rating" type="text" maxlength="25" size="25" autocomplete="off" class="form-control m-input"
                           value="<?= $data->rating ?>"/>
                </div>
                <div class="form-group">
                    <label for="release_date" class="form-control-label">Release Date</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input id="release_date" type="text" name="release_date" data-validation="required" autocomplete="off"
                               class="form-control m-input date" value="<?= $data->release_date == '0000-00-00' ? '' : $data->release_date ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="exam_date" class="form-control-label">
                        Exam Date
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group date-range">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input id="exam_date" type="text" name="exam_date" autocomplete="off"
                               class="form-control m-input" value="<?= $data->exam_date ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="license_no" class="form-control-label">
                        License No
                        <span class="text-danger">*</span>
                    </label>
                    <input id="license_no" name="license_no" type="text" maxlength="50" size="50" autocomplete="off"
                           class="form-control m-input" value="<?= $data->license_no ?>"/>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">With Expiry Date</label>
                    <div class="col-2 p-0">
                        <span class="m-switch m-switch--sm m-switch--icon" id="expiry-switch">
                            <label>
                                <input type="checkbox" name="is_active" value="1" <?=$data->expiration_date != '0000-00-00' && $data->expiration_date ? 'checked' : '' ?> >
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div id="with-expiry" class="form-group <?= $data->expiration_date != '0000-00-00' && $data->expiration_date ? '' : 'd-none' ?>">
                    <label for="license_no" class="form-control-label">License Expiry Date *</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input id="release_date" type="text" name="expiration_date" value="<?= $data->expiration_date != '0000-00-00' && $data->expiration_date ? $data->expiration_date : '' ?>" maxlength="12" size="12" autocomplete="off" data-validation="required" class="form-control m-input date" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
                <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
            </div>
        </form>
    </div>
</div>
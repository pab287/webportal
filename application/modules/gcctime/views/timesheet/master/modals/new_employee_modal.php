<div class="modal fade" role="dialog" id="new-employee-modal" data-backdrop="static" data-keyboard="false">
    <form action="" id="frm-new-employee-modal" class="m-form">
        <input type="file" accept="image/*" id="file-image" class="m--hide">
        <input type="hidden" id="origin">

        <div class="modal-dialog modal-new-employee" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">NEW EMPLOYEE</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 m--margin-bottom-25">
                            <div class="image-container"
                                 style="--container-width: 150px; --container-height: 150px;">
                                <img class="image-container__img" id="image--holder"
                                     src="<?= base_url('assets/images/profile/no_image.jpg') ?>" alt="">
                                <div class="image-container__button-container">
                                    <i class="fa fa-camera image-container__button-container__camera-button"
                                       onclick="$('#file-image').click();"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                            <div class="row">
                                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 form-group">
                                    <label>Laborer?</label>
                                    <div>
                                        <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
                                            <label>
                                                <input type="checkbox" name="isLaborer" id="is-laborer">
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 form-group">
                                    <label for="" class="required">Biometric No.</label>
                                    <input type="text" class="form-control" name="biometricno" id="biometricno">
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                                    <label for="">Position</label>
                                    <select class="form-control" id="position" name="position">
                                        <option></option>
                                        <?php foreach ($dropdown_position as $position): ?>
                                            <option value="<?= $position->id ?>"><?= $position->text ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                                    <label for="">Department</label>
                                    <select class="form-control" id="department" name="department_id">
                                        <option></option>
                                        <?php foreach ($dropdown_department as $department): ?>
                                            <option value="<?= $department->id ?>"><?= $department->text ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                                    <label for="">Company</label>
                                    <select class="form-control" id="company" name="company_id">
                                        <option></option>
                                        <?php foreach ($dropdown_company as $company): ?>
                                            <option value="<?= $company->id ?>"><?= $company->text ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                                    <label for="" class="required">Classification</label>
                                    <select class="form-control" data-validation="required"
                                            id="classification" name="employee_status">
                                        <option value=""></option>
                                        <option value="Active">ACTIVE</option>
                                        <option value="Inactive">INACTIVE</option>
                                    </select>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                                    <label for="" class="required">Status</label>
                                    <select class="form-control" data-validation="required"
                                            id="work-status" disabled name="work_status">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="m--margin-top-30 m--margin-bottom-20 m--font-bolder">PERSONAL INFORMATION</p>
                    <div class="row m--margin-top-15">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group">
                            <label for="" class="required">First name</label>
                            <input type="text" class="form-control" data-validation="required"
                                   name="firstname" autocomplete="off">
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group">
                            <label for="">Middle name</label>
                            <input type="text" class="form-control" name="middlename" autocomplete="off">
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group">
                            <label for="" class="required">Last name</label>
                            <input type="text" class="form-control" data-validation="required"
                                   name="lastname"  autocomplete="off">
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group">
                            <label for="">Suffix</label>
                            <input type="text" class="form-control" name="suffix" autocomplete="off">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group">
                            <label for="" class="required">Gender</label>
                            <div class="m-radio-inline">
                                <label class="m-radio">
                                    <input type="radio" name="gender" value="Male"
                                           data-validation="required">
                                    MALE
                                    <span></span>
                                </label>
                                <label class="m-radio">
                                    <input type="radio" name="gender" value="Female"
                                           data-validation="required">
                                    FEMALE
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group">
                            <label for="" class="required">Civil Status</label>
                            <select id="civil_stat" class="form-control" name="civil_stat"
                                    data-validation="required">
                                <option></option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Separated">Separated</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Annulled">Annulled</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group">
                            <label for="" class="required">Citizenship</label>
                            <input type="text" class="form-control" data-validation="required"
                                   name="citizenship" autocomplete="off">
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group">
                            <label for="" class="required">Religion</label>
                            <input type="text" class="form-control" data-validation="required"
                                   name="religion" autocomplete="off">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group">
                            <label for="" class="required">Birth Date</label>
                            <div class="input-group date" id="dob">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input type="text" class="form-control" data-validation="required"
                                       autocomplete="off" name="bday">
                            </div>
                            <span class="m-form__help">FORMAT: YYYY-MM-DD</span>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                            <label for="" class="required">Place of Birth</label>
                            <input type="text" class="form-control" data-validation="required"
                                   autocomplete="off" name="birthplace">
                        </div>
                    </div>

                    <div class="row m--margin-top-20">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                            <label for="" class="required">Current Address</label>
                            <input type="text" class="form-control" data-validation="required" autocomplete="off"
                                   name="curr_addr" autocomplete="off">
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                            <label for="" class="required">Permanent Address</label>
                            <input type="text" class="form-control" data-validation="required" autocomplete="off"
                                   name="prov_addr" autocomplete="off">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                            <label for="" class="required">Languages</label>
                            <input type="text" class="form-control" data-validation="required" autocomplete="off"
                                   name="languages">
                        </div>
                    </div>

                    <div class="row m--margin-top-20">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group">
                            <label for="" class="required">HEIGHT</label>
                            <input type="text" class="form-control" data-validation="required"
                                   autocomplete="off" name="height">
                            <span class="m-form__help">FORMAT: FEET INCHES</span>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group">
                            <label for="" class="required">WEIGHT</label>
                            <input type="text" class="form-control" data-validation="required"
                                   autocomplete="off" name="weight">
                            <span class="m-form__help">FORMAT: FEET INCHES</span>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group">
                            <label for="" class="required">COMPLEXION</label>
                            <input type="text" class="form-control" data-validation="required"
                                   autocomplete="off" name="complexion">
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group">
                            <label for="" class="required">HAIR COLOR</label>
                            <input type="text" class="form-control" data-validation="required"
                                   autocomplete="off" name="hair_color">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group">
                            <label for="" class="required">BLOOD TYPE</label>
                            <input type="text" class="form-control" data-validation="required"
                                   autocomplete="off" name="bloodtype">
                        </div>
                    </div>

                    <hr class="mt-4 mb-4"/>
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                            <label for="shift_id_new_employee" class="required">SHIFT SCHEDULE</label>
                            <select name="shift_id" id="shift_id_new_employee" class="form-control"
                                    data-validation="required"></select>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 d-flex flex-column">
                            <label for="">FLEXI TIME?</label>
                            <div class="d-flex flex-row">
                                <span class="m-switch m-switch--outline m-switch--icon m-switch--info flex-grow-0 flex-shrink-0">
                                    <label>
                                        <input type="checkbox" name="is_flexi" id="is_flexi">
                                        <span></span>
                                    </label>
                                </span>
                                <span class="flex-grow-0 flex-shrink-0 m--margin-top-5 m--margin-left-5
                                             m--font-metal"
                                      id="is_flexi-label">
                                    NO
                                </span>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="existInPersonnelList" name="existInPersonnelList" value="false">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnSave">Submit</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </form>
</div>
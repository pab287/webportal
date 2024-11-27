<div class="m-content">
    <div class="row">
        <div class="col-xl-3 col-lg-4">
            <div class="m-portlet">
                <div class="m-portlet__body">
                    <div id="left_pane-card" class="m-card-profile">
                        <div class="m-card-profile__title m--hide">Title Profile</div>
                        <div class="m-card-profile__pic m-card-user__pic">
                            <div class="m-card-profile__pic-wrapper">
                                <img id="image--holder" alt="no_image.jpg"
                                     src="<?= base_url('assets/images/profile/no_image.jpg') ?>">
                            </div>
                        </div>
                        <ul class="m-nav m-nav--hover-bg m-portlet-fit--sides">
                            <li class="m-nav__separator m-nav__separator--fit"></li>
                            <li class="m-nav__item">
                                <a href="javascript:void(0);" class="m-nav__link btnNew" data-toggle="modal"
                                   onclick="$('#file-image').click();">
                                    <i class="m-nav__link-icon fa fa-camera"></i>
                                    <span class="m-nav__link-text">Upload Profile Photo</span>
                                </a>
                            </li>
                            <li class="m-nav__item">
                                <a class="m-nav__link btnBack"
                                   href="employee_masterlist">
                                    <i class="m-nav__link-icon fa fa-arrow-left"></i>
                                    <span class="m-nav__link-text">Back to Employee List</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-8">
            <div class="m-portlet m-portlet--tabs">
                <div class="m-portlet__head d-flex flex-row align-items-center">
                    <div>
                        <a href="employee_masterlist"
                           class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill mr-4 btnBack">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left m-tabs-line--primary">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_user_profile_tab_1"
                                   role="tab" aria-expanded="true">Personal Information</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="m_user_profile_tab_1" aria-expanded="true">
                        <form id="frm-new-employee"
                              class="m-form m-form--fit m-form--label-align-right">
                            <input type="hidden" name="csrf_token"
                                   value="<?php echo $this->security->get_csrf_hash(); ?>">

                            <div class="m-portlet__body">
                                <div class="row">
                                    <div class="col-5">
                                        <div class="form-group m-form__group row">
                                            <label for="biometricno"
                                                   class="col-5 col-form-label text-right">
                                                LABORER ?
                                            </label>
                                            <div class="col-7">
                                                <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
                                                    <label>
                                                        <input type="checkbox" name="isLaborer" id="is-laborer">
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-10 offset-1">
                                        <div class="m-alert alert
                                                    alert-info alert-dismissible m--margin-bottom-40 m--margin-top-10
                                                    alert-biometric-exist"
                                             style="display: none;" role="alert">
                                            <button type="button" class="close"
                                                    aria-label="Close"></button>
                                            <div class="alert-biometric-exist__message">
                                                <strong>
                                                    Heads up!
                                                </strong>
                                                This alert needs your attention, but it's not super important.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m--margin-bottom-10">
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="biometricno" class="col-5 col-form-label text-right">
                                                BIOMETRIC NO.
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-7">
                                                <input id="biometricno" type="text" name="biometricno"
                                                       class="form-control m-input" autocomplete="off"
                                                       data-validation="required"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="position" class="col-5 col-form-label text-right">
                                                POSITION
                                            </label>
                                            <div class="col-7">
                                                <select class="form-control"
                                                        name="position" id="position">
                                                    <option></option>
                                                    <?php foreach ($dropdown_position as $position): ?>
                                                        <option value="<?= $position->id ?>">
                                                            <?= $position->text ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m--margin-bottom-10">
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="department" class="col-5 col-form-label text-right">
                                                DEPARTMENT
                                            </label>
                                            <div class="col-7">
                                                <select class="form-control"
                                                        name="department_id" id="department">
                                                    <option></option>
                                                    <?php foreach ($dropdown_department as $department): ?>
                                                        <option value="<?= $department->id ?>">
                                                            <?= $department->text ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="company" class="col-5 col-form-label text-right">
                                                COMPANY
                                            </label>
                                            <div class="col-7">
                                                <select class="form-control"
                                                        name="company_id" id="company">
                                                    <option></option>
                                                    <?php foreach ($dropdown_company as $company): ?>
                                                        <option value="<?= $company->id ?>">
                                                            <?= $company->text ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="department" class="col-5 col-form-label text-right">
                                                CLASSIFICATION
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-7">
                                                <select class="form-control" data-validation="required"
                                                        id="classification" name="employee_status">
                                                    <option value=""></option>
                                                    <option value="Active">ACTIVE</option>
                                                    <option value="Inactive">INACTIVE</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="department" class="col-5 col-form-label text-right">
                                                STATUS
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-7">
                                                <select class="form-control" disabled data-validation="required"
                                                        id="work-status" name="work_status">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>

                                <div class="row m--margin-bottom-10">
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="firstname" class="col-5 col-form-label text-right">
                                                First Name
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-7">
                                                <input id="firstname" type="text" name="firstname"
                                                       class="form-control m-input" maxlength="25" size="25"
                                                       autocomplete="off" data-validation="required"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="middlename" class="col-5 col-form-label text-right">
                                                Middle Name
                                            </label>
                                            <div class="col-7">
                                                <input id="middlename" type="text" name="middlename"
                                                       class="form-control m-input" placeholder="( Optional )"
                                                       maxlength="25" size="25" autocomplete="off"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="lastname" class="col-5 col-form-label text-right">
                                                Last Name
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-7">
                                                <input id="lastname" type="text" name="lastname"
                                                       class="form-control m-input" maxlength="25" size="25"
                                                       autocomplete="off" data-validation="required"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="suffix" class="col-5 col-form-label text-right">Suffix</label>
                                            <div class="col-7">
                                                <input id="suffix" type="text" name="suffix"
                                                       class="form-control m-input" placeholder="( Optional )"
                                                       maxlength="10" size="10" autocomplete="off"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                                <div class="row m--margin-bottom-10">
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="tel_no" class="col-5 col-form-label text-right">Telephone
                                                No</label>
                                            <div class="col-7">
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="la la-chain"></i>
                                                    </span>
                                                    <input id="tel_no" type="text" name="tel_no"
                                                           class="form-control m-input" placeholder="( Optional )"
                                                           maxlength="11" size="11" autocomplete="off"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="mobile_no" class="col-5 col-form-label text-right">Mobile
                                                No</label>
                                            <div class="col-7">
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="la la-chain"></i>
                                                    </span>
                                                    <input id="mobile_no" type="text" name="mobile_no"
                                                           class="form-control m-input" placeholder="( Optional )"
                                                           maxlength="11" size="11" autocomplete="off"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="email" class="col-5 col-form-label text-right">
                                                Email Address
                                            </label>
                                            <div class="col-7">
                                                <input id="email" type="email" name="email" class="form-control m-input"
                                                       placeholder="( Optional )" maxlength="100" size="100"
                                                       autocomplete="off"/>
                                                <span class="m-form__help">We'll never share your email with anyone else</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                                <div class="row m--margin-bottom-10">
                                    <div class="col-10 col-md-10">
                                        <div class="form-group m-form__group row">
                                            <label for="curr_addr" class="col-3 col-form-label text-right">
                                                Current Address
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-9">
                                                <input id="curr_addr" type="text" name="curr_addr"
                                                       class="form-control m-input" maxlength="200" size="200"
                                                       autocomplete="off" data-validation="required"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row m--margin-bottom-10">
                                    <div class="col-10 col-md-10">
                                        <div class="form-group m-form__group row">
                                            <label for="prov_addr" class="col-3 col-form-label text-right">
                                                Permanent Address
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-9">
                                                <input id="prov_addr" type="text" name="prov_addr"
                                                       class="form-control m-input" maxlength="200" size="200"
                                                       autocomplete="off" data-validation="required"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-10 col-md-10">
                                        <div class="form-group m-form__group row">
                                            <label for="long_lat_coordinates" class="col-3 col-form-label text-right">
                                                Map Coordinates
                                            </label>
                                            <div class="col-9">
                                                <input id="long_lat_coordinates" type="text" name="long_lat_coordinates"
                                                       class="form-control m-input" maxlength="100" size="100"
                                                       autocomplete="off"/>
                                                <div class="m-form__help">latitude and longitude map coordinates</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                                <div class="row m--margin-bottom-10">
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="gender" class="col-5 col-form-label text-right">
                                                Gender <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-7">
                                                <div class="m-checkbox-inline">
                                                    <label class="m-checkbox">
                                                        <input id="gender" type="radio" name="gender"
                                                               data-validation="required" value="Male"/>
                                                        Male<span></span>
                                                    </label>
                                                    <label class="m-checkbox">
                                                        <input id="gender" type="radio" name="gender"
                                                               data-validation="required" value="Female"/>
                                                        Female<span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="civil_stat" class="col-5 col-form-label text-right">
                                                Civil Status <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-7">
                                                <select id="civil_stat" class="form-control select2" name="civil_stat"
                                                        data-validation="required">
                                                    <option value="Single">Single</option>
                                                    <option value="Married">Married</option>
                                                    <option value="Separated">Separated</option>
                                                    <option value="Divorced">Divorced</option>
                                                    <option value="Annulled">Annulled</option>
                                                    <option value="Widowed">Widowed</option>
                                                    <option value="Single / Solo Parent">Single / Solo Parent</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row m--margin-bottom-10">
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="citizenship" class="col-5 col-form-label text-right">
                                                Citizenship
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-7">
                                                <input id="citizenship" type="text" name="citizenship"
                                                       class="form-control m-input" maxlength="25" size="25"
                                                       autocomplete="off" data-validation="required"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="religion" class="col-5 col-form-label text-right">
                                                Religion
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-7">
                                                <input id="religion" type="text" name="religion"
                                                       class="form-control m-input" maxlength="25" size="25"
                                                       autocomplete="off" data-validation="required"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="bday" class="col-5 col-form-label text-right">
                                                Birth Date
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-7">
                                                <div class="input-group date" id="m_datepicker_2">
                                                    <span class="input-group-addon">
													    <i class="la la-calendar-check-o"></i>
												    </span>
                                                    <input type="text" class="form-control m-input"
                                                           placeholder="" autocomplete="off" name="bday"
                                                           data-validation="required">
                                                </div>
                                                <span class="m-form__help pull-right">Date Format: YYYY-MM-DD</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                                <div class="row m--margin-bottom-10">
                                    <div class="col-10 col-md-10">
                                        <div class="form-group m-form__group row">
                                            <label for="birthplace" class="col-3 col-form-label text-right">
                                                Place of Birth
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-9">
                                                <input id="birthplace" type="text" name="birthplace"
                                                       class="form-control m-input" maxlength="50" size="50"
                                                       autocomplete="off" data-validation="required"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-10 col-md-10">
                                        <div class="form-group m-form__group row">
                                            <label for="languages" class="col-3 col-form-label text-right">
                                                Languages
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-9">
                                                <input id="languages" type="text" name="languages"
                                                       class="form-control m-input" maxlength="200" size="200"
                                                       autocomplete="off" data-validation="required"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                                <div class="row m--margin-bottom-10">
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="height" class="col-5 col-form-label text-right">
                                                Height
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-7">
                                                <input id="height" type="text" name="height"
                                                       class="form-control m-input" maxlength="10" size="10"
                                                       autocomplete="off" data-validation="required"/>
                                                <span class="m-form__help pull-right">Format: FEET INCHES</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="weight" class="col-5 col-form-label text-right">
                                                Weight
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-7">
                                                <input id="weight" type="text" name="weight"
                                                       class="form-control m-input" maxlength="10" size="10"
                                                       autocomplete="off" data-validation="required"/>
                                                <span class="m-form__help pull-right">Format: Kilograms</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row m--margin-bottom-10">
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="complexion" class="col-5 col-form-label text-right">
                                                Complexion
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-7">
                                                <input id="complexion" type="text" name="complexion"
                                                       class="form-control m-input" maxlength="25" size="25"
                                                       autocomplete="off" data-validation="required"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="hair_color" class="col-5 col-form-label text-right">
                                                Hair Color
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-7">
                                                <input id="hair_color" type="text" name="hair_color"
                                                       class="form-control m-input" maxlength="25" size="25"
                                                       autocomplete="off" data-validation="required"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-5">
                                        <div class="form-group m-form__group row">
                                            <label for="bloodtype" class="col-5 col-form-label text-right">
                                                Blood Type
                                                <span class="m--font-danger m--font-boldest ml-1">*</span>
                                            </label>
                                            <div class="col-7">
                                                <input id="bloodtype" type="text" name="bloodtype"
                                                       class="form-control m-input" maxlength="30" size="30"
                                                       autocomplete="off" data-validation="required"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="m-portlet__foot m-portlet__foot--fit">
                                <div class="m-form__actions">
                                    <div class="row">
                                        <div class="col-12 text-right">
                                            <button type="submit"
                                                    class="btn btnSave btn-accent m-btn m-btn--air m-btn--custom btn-submit">
                                                Save
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input class="m--hide" type="file" id="file-image" name="file_image" accept="image/*">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('gcctime/timesheet/modals/alert_modal'); ?>
<?php $this->load->view('gcctime/timesheet/modals/confirmation_modal'); ?>
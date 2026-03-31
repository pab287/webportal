<style>
#new_preview {
    border: 1px solid rgba(0, 0, 0, 0.15);
    border-radius: 6px;
    padding: 15px;
}
.m-tabs__link {
    pointer-events: none;
    cursor: default;
}

@media (max-width: 768px) {
    .m-tabs .nav-item {
        display: none;
    }

    .m-tabs .nav-item:has(.active) {
        display: block;
    }
}

</style>
<div class="m-content" id="m_content">
    <div class="row">
        <div class="col-12 d-flex align-items-center justify-content-center py-4 gap-3">
            <img src="<?= base_url('assets/logo_png.png'); ?>" alt="GC&C Logo" height="70" class="mr-4">
            <div class="text-left">
                <h2 class="font-weight-bold text-dark mb-1">ONLINE APPLICATION FORM</h2>
                <p class="text-center text-muted small mb-0">PLEASE FILL IN ALL REQUIRED FIELDS TO COMPLETE YOUR APPLICATION.</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="m-portlet mt-2" id="m_portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-tools" style="width: 100%;">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left m-tabs-line--primary" role="tablist" style="display: flex; justify-content: space-between; width: 100%;">
                            <li class="nav-item m-tabs__item">
                                <a data-toggle="tab" href="#personal_information" role="tab" class="nav-link m-tabs__link active">
                                    <i class="fa fa-user"></i> Personal Info
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a data-toggle="tab" href="#contact_information" role="tab" class="nav-link m-tabs__link">
                                    <i class="fa fa-envelope"></i> Contact Info
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a data-toggle="tab" href="#work_experience" role="tab" class="nav-link m-tabs__link">
                                    <i class="fa fa-briefcase"></i> Work Experience
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a data-toggle="tab" href="#educational_information" role="tab" class="nav-link m-tabs__link">
                                    <i class="fa fa-graduation-cap"></i> Educational Info
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a data-toggle="tab" href="#application_information" role="tab" class="nav-link m-tabs__link">
                                    <i class="fa fa-file"></i> Application Info
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a data-toggle="tab" href="#resume_upload" role="tab" class="nav-link m-tabs__link">
                                    <i class="fa fa-upload"></i> Resume Upload
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="alert alert-info mt-2 mb-3 py-2">
                        <small>
                            <i class="fa fa-info-circle"></i>
                            <strong>DISCLAIMER:</strong> PLEASE ENSURE ALL INFORMATION PROVIDED IS ACCURATE AND COMPLETE. 
                            FALSE INFORMATION MAY RESULT IN DISQUALIFICATION. FIELDS MARKED <span class="text-danger">*</span> ARE REQUIRED. 
                            LEAVE FIELDS BLANK IF NOT APPLICABLE.
                        </small>
                    </div>
                    <hr/>
                    <div class="tab-content">
                        <div id="personal_information" class="tab-pane active">
                            <form id="personal_information_form" action="javascript:void(0);">
                                <div class="row mt-2">
                                    <div class="col">
                                        <div class="m-portlet__head-title">
                                            <h5 class="m-portlet__head-text">PERSONAL INFORMATION</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label for="firstname" class="form-control-label required">First name</label>
                                            <input type="text" name="firstname" id="firstname" placeholder="ENTER FIRST NAME" class="form-control" data-validation="required" autocomplete="off" v-model="validate.firstname">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label for="middle" class="form-control-label">Middle name</label>
                                            <input type="text" name="middlename" id="middle" placeholder="(optional)" class="form-control" autocomplete="off" v-model="validate.middlename">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label for="lastname" class="form-control-label required">Last name</label>
                                            <input type="text" name="lastname" id="lastname" placeholder="ENTER LAST NAME" class="form-control" data-validation="required" autocomplete="off" v-model="validate.lastname">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label for="suffix" class="form-control-label">Suffix</label>
                                            <input type="text" name="suffix" id="suffix" placeholder="(optional)" class="form-control" autocomplete="off" v-model="validate.suffix">
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="row mt-2">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="school_id" class="form-control-label">School</label>
                                            <select id="school_id" name="schools" multiple="multiple" class="form-control"></select>
                                            <span class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">YOU CAN SELECT MULTIPLE SCHOOL</span>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="course_id" class="form-control-label">Course</label>
                                            <select id="course_id" name="courses" multiple="multiple" class="form-control"></select>
                                            <span class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">YOU CAN SELECT MULTIPLE COURSE</span>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="m-separator m-separator--dashed d-xl-12"></div>
                                <div class="row mt-2">
                                    <div class="col">
                                        <div class="m-portlet__head-title">
                                            <h5 class="m-portlet__head-text">ADDITIONAL INFORMATION</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label for="gender" class="form-control-label required">Gender</label>
                                            <select id="gender" name="gender" class="form-control" data-validation="required">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label for="civil_status" class="form-control-label required">Civil Status</label>
                                            <select id="civil_status" name="civil_status" class="form-control" data-validation="required">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label for="religion" class="form-control-label required">Religion</label>
                                            <input type="text" name="religion" id="religion" placeholder="ENTER RELIGION" class="form-control" data-validation="required" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label for="height" class="form-control-label">Height</label>
                                            <input type="text" name="height" id="height" placeholder="(OPTIONAL)" class="form-control" autocomplete="off">
                                            <span class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">* IN FEET AND INCHES *</span>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label for="weight" class="form-control-label">Weight</label>
                                            <input type="text" name="weight" id="weight" placeholder="(OPTIONAL)" class="form-control" autocomplete="off">
                                            <span class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">* IN KILOGRAMS *</span>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label for="birthdate" class="form-control-label required">Birthdate</label>
                                            <div class="input-group date">
                                                <input class="form-control m-input" type="text" id="birthdate" name="birthdate" placeholder="mm/dd/yyyy" maxlength="12" size="12" data-validation="required" oninput="formatDate(this)" autocomplete="off">
                                                <span class="input-group-addon">
                                                <i class="la la-calendar glyphicon-th"></i>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label for="citizenship" class="form-control-label required">Citizenship</label>
                                            <input type="text" name="citizenship" id="citizenship" placeholder="ENTER CITIZENSHIP" class="form-control" data-validation="required" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="contact_information" class="tab-pane">
                            <form id="contact_information_form" action="javascript:void(0);">
                                <div class="row mt-2">
                                    <div class="col">
                                        <div class="m-portlet__head-title">
                                            <h5 class="m-portlet__head-text">CONTACT INFORMATION </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label for="contact_no" class="form-control-label required">Mobile Number</label>
                                            <input type="text" v-model="contactFormData.contact_no" name="contact_no" id="contact_no" placeholder="ENTER YOUR MOBILE NUMBER" class="form-control" data-validation="required" autocomplete="off" maxlength="12" @input="contactFormData.contact_no = $event.target.value.replace(/\D/g, '')">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label for="email" class="form-control-label">Email</label>
                                            <input type="email" v-model="contactFormData.email" name="email" id="email" placeholder="(optional)" class="form-control" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label for="tel_no" class="form-control-label">Telephone Number</label>
                                            <input type="text" v-model="contactFormData.tel_no" name="tel_no" id="tel_no" placeholder="(optional)" class="form-control" autocomplete="off" maxlength="12" @input="contactFormData.tel_no = $event.target.value.replace(/\D/g, '')"></div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="address" class="form-control-label required">Current Address</label>
                                            <input type="text" v-model="contactFormData.address" name="address" id="address" placeholder="ENTER YOUR CURRENT ADDRESS" class="form-control" data-validation="required" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="permanent_address" class="form-control-label required">Permanent Address</label>
                                            <input type="text" v-model="contactFormData.permanent_address" name="permanent_address" id="permanent_address" placeholder="ENTER YOUR PERMANENT ADDRESS" class="form-control" data-validation="required" autocomplete="off">
                                        </div>
                                    </div>
                                    <!-- <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-control-label">Map Coordinates</label>
                                            <input type="text" name="long_lat_coordinates" id="long_lat_coordinates" class="form-control" placeholder="(OPTIONAL)" autocomplete="off">
                                        </div>
                                    </div> -->
                                </div>
                                <div class="m-separator m-separator--dashed d-xl-12"></div>
                                <div class="row mt-2">
                                    <div class="col">
                                        <div class="m-portlet__head-title">
                                            <h5 class="m-portlet__head-text">REFERENCES </h5>
                                        </div>
                                    </div>
                                </div>
                                <div v-for="(reference, index) in references" :key="'reference_' + index" class="card border mb-3">
                                    <div class="card-header d-flex align-items-center py-2">
                                        <span class="rounded-circle bg-light border d-flex align-items-center justify-content-center mr-2"
                                            style="width:28px; height:28px; font-size:12px; font-weight:500;">
                                            {{ index + 1 }}
                                        </span>
                                        <span style="font-size:13px; font-weight:500;">REFERENCE {{ index + 1 }}</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="`full_name_${index + 1}`" class="form-control-label required">Full Name</label>
                                                    <input :id="`full_name_${index + 1}`" v-model="reference.ref_name" :name="`references[${index}][ref_name]`" placeholder="ENTER FULL NAME" type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input"/>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="`contact_no_${index + 1}`" class="form-control-label required">Contact Number</label>
                                                    <input :id="`contact_no_${index + 1}`" v-model="reference.ref_contact_no" :name="`references[${index}][ref_contact_no]`" placeholder="ENTER CONTACT NUMBER" type="text" maxlength="11" autocomplete="off" data-validation="required" class="form-control m-input"
                                                        @input="reference.ref_contact_no = $event.target.value.replace(/\D/g, '')"/>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="`address_${index + 1}`" class="form-control-label required">Address</label>
                                                    <input :id="`address_${index + 1}`" v-model="reference.ref_address" :name="`references[${index}][ref_address]`" placeholder="ENTER ADDRESS" type="text" maxlength="500" autocomplete="off" data-validation="required" class="form-control m-input"/>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="`company_${index + 1}`" class="form-control-label required">Company</label>
                                                    <input :id="`company_${index + 1}`" v-model="reference.ref_company" :name="`references[${index}][ref_company]`" placeholder="ENTER COMPANY" type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input"/>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="`position_${index + 1}`" class="form-control-label required">Position</label>
                                                    <input :id="`position_${index + 1}`" v-model="reference.ref_position" :name="`references[${index}][ref_position]`" placeholder="ENTER POSITION" type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input"/>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="`relationship_${index + 1}`" class="form-control-label required">Relationship</label>
                                                    <input :id="`relationship_${index + 1}`" v-model="reference.ref_relationship" :name="`references[${index}][ref_relationship]`" placeholder="ENTER RELATIONSHIP" type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="work_experience" class="tab-pane">
                            <form id="work_experience_form" action="javascript:void(0);">
                                <div class="row my-2">
                                    <div class="col">
                                        <div class="m-portlet__head-title">
                                            <h5 class="m-portlet__head-text">WORK EXPERIENCE</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="m-checkbox">
                                                <input type="checkbox" v-model="isFreshGraduate" @change="onFreshGraduateChange"/>
                                                I am a Fresh Graduate / No Work Experience
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <template v-if="!isFreshGraduate">
                                    <template v-for="(work, index) in workExperiences">
                                        <div class="row mt-2 align-items-center">
                                            <div class="col-12 d-flex justify-content-between">
                                                <strong>ENTRY #{{ index + 1 }}</strong>
                                                <button type="button" class="btn btn-danger btn-sm" v-on:click="removeWork(index)" v-if="workExperiences.length > 1">
                                                    <i class="la la-trash"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="'work_company_' + index" class="form-control-label required">Company</label>
                                                    <input :id="'work_company_' + index" name="work_company" type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input" v-model="work.company"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="'work_position_' + index" class="form-control-label required">Position</label>
                                                    <input :id="'work_position_' + index" name="work_position" type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input" v-model="work.position"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="'work_from_' + index" class="form-control-label required">From Year</label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                        <input :id="'work_from_' + index" name="work_from_year" type="text" maxlength="4" autocomplete="off" data-validation="required" class="form-control m-input" @input="work.from = work.from.replace(/\D/g, '')" v-model="work.from"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="'work_status_' + index" class="form-control-label required">Status</label>
                                                    <input :id="'work_status_' + index" name="work_status" type="text" maxlength="50" autocomplete="off" data-validation="required" class="form-control m-input" v-model="work.status"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="'work_reason_' + index" class="form-control-label">Reason for leaving *</label>
                                                    <input :id="'work_reason_' + index" name="work_reason" type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input" v-model="work.reason"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="'work_to_' + index" class="form-control-label required">To Year</label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                        <input :id="'work_to_' + index" name="work_to_year" type="text" maxlength="4" autocomplete="off" data-validation="required" class="form-control m-input" @input="work.to = work.to.replace(/\D/g, '')"  v-model="work.to"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </template>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <button type="button" class="btn btn-success" v-on:click="addWork">
                                                <i class="la la-plus"></i> New
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="alert alert-info mt-2">
                                        <i class="fa fa-info-circle"></i> 
                                        MARKED AS <strong>FRESH GRADUATE / NO WORK EXPERIENCE</strong>
                                    </div>
                                </template>
                            </form>
                        </div>
                        <div id="educational_information" class="tab-pane">
                            <form id="educational_information_form" action="javascript:void(0);">
                                <div class="row mt-2">
                                    <div class="col">
                                        <div class="m-portlet__head-title">
                                            <h5 class="m-portlet__head-text">EDUCATIONAL BACKGROUND</h5>
                                        </div>
                                    </div>
                                </div>
                                <template v-for="(item, index) in educInfo">
                                    <div class="row mt-2 align-items-center">
                                        <div class="col-12 d-flex justify-content-between">
                                            <span></span>
                                            <button type="button" class="btn btn-danger btn-sm" v-on:click="removeEducInfo(index)" v-if="educInfo.length > 1">
                                                <i class="la la-trash"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label :for="'level_' + index" class="form-control-label required">Level</label>
                                                <select :id="'level_' + index" :name="'schools[' + index + '][level]'" :data-index="index" class="form-control m-input educ-level-select" data-validation="required" v-model="item.level">
                                                    <option></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label :for="'educ_school_' + index" class="form-control-label required">School</label>
                                                <input :id="'educ_school_' + index" name="school" type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input" v-model="item.school"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label :for="'educ_from_' + index" class="form-control-label required">From Year</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                    <input :id="'educ_from_' + index" name="educ_from_year" type="text" maxlength="4" autocomplete="off" data-validation="required" class="form-control m-input" @input="item.from = item.from.replace(/\D/g, '')" v-model="item.from" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label :for="'educ_degree_' + index" class="form-control-label">Educational Degree</label>
                                                <input :id="'educ_degree_' + index" name="educ_degree" type="text" maxlength="200" autocomplete="off" class="form-control m-input" v-model="item.degree"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label :for="'honor_' + index" class="form-control-label">Honor</label>
                                                <input :id="'honor_' + index" name="educ_honor" type="text" maxlength="200" autocomplete="off" class="form-control m-input" v-model="item.honor"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label :for="'educ_to_' + index" class="form-control-label required">To Year</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                    <input :id="'educ_to_' + index" name="educ_to_year" type="text" maxlength="4" autocomplete="off" data-validation="required" class="form-control m-input" @input="item.to = item.to.replace(/\D/g, '')" v-model="item.to"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-success" v-on:click="addEducInfo">
                                            <i class="la la-plus"></i> New
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="application_information" class="tab-pane">
                            <form id="application_information_form" action="javascript:void(0);">
                                <div class="row mt-2">
                                    <div class="col">
                                        <div class="m-portlet__head-title">
                                            <h5 class="m-portlet__head-text">APPLICATION INFORMATION</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label for="recruitment" class="form-control-label required">Recruitment Source</label>
                                            <select id="recruitment" name="recruitment" class="form-control" data-validation="required">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 referral d-none">
                                        <div class="form-group">
                                            <label for="referral" class="form-control-label required">referral</label>
                                            <input type="text" name="referral" id="referral" placeholder="Enter Referral Name" class="form-control" data-validation="required" autocomplete="off" maxlength="200">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 referral d-none">
                                        <div class="form-group">
                                            <label for="referral-relationship" class="form-control-label required">relationship</label>
                                            <input type="text" name="referral-relationship" id="referral-relationship" placeholder="Enter Referral Relationship" class="form-control" data-validation="required" autocomplete="off" maxlength="200">
                                        </div>
                                    </div>
                                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                        <div class="form-group">
                                            <label for="position_id" class="form-control-label required">Position Applied FOR</label>
                                            <select id="position_id" name="positions" multiple="multiple" class="form-control" data-validation="required">
                                            </select>
                                            <span class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">You can select multiple position.</span>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label for="applied_dt" class="form-control-label required">Date of Application</label>
                                            <div class='input-group date'>
                                                <input class="form-control m-input" type="text" id="applied_dt" name="applied_dt" placeholder="mm/dd/yyyy" maxlength="12" size="12" data-validation="required" autocomplete="off" readonly/>
                                                <span class="input-group-addon">
                                                <i class="la la-calendar glyphicon-th"></i>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="resume_upload" class="tab-pane">
                            <form id="resume_upload_form" action="javascript:void(0);" enctype="multipart/form-data">
                                <div class="row mt-2 mb-2">
                                    <div class="col">
                                        <div class="m-portlet__head-title">
                                            <h5 class="m-portlet__head-text">RESUME UPLOAD</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="row pb-2">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <span class="btn btn-success fileinput-button">
                                                <i class="glyphicon glyphicon-plus"></i>
                                                <span>SELECT FILE</span>
                                                <input type="file" id="fileupload" name="files" accept=".pdf, application/pdf">
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div id="new_preview" class="m-portlet__body">
                                    <template v-if="uploadedFiles.length >= 1">
                                        <div class="row col-12 m-widget2">
                                            <template v-for="(item, index) in uploadedFiles">
                                                <div v-bind:class="getClass(item.type)">
                                                    <div class="m-widget4__item d-flex align-items-center">
                                                        <div class="m-widget4__img m-widget4__img--icon">
                                                            <img v-bind:src="getExtension(item.type)" alt="" height="50" width="50">
                                                        </div>
                                                        <div class="m-widget2__desc">
                                                            <span class="m-widget4__text">{{item.name}}</span>
                                                        </div>
                                                        <div class="m-widget2__actions ml-auto">
                                                            <button type="button" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView" v-on:click="fileDelete(index)">
                                                                <i class="m-nav__link-icon flaticon-circle"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="col-12">
                                            <strong><h5>PLEASE UPLOAD RESUME AS PDF FORMAT</h5></strong>
                                        </div>
                                    </template>
                                </div>
                            </form>
                            <div class="m-separator m-separator--dashed d-xl-12"></div>
                            <div class="row mt-2">
                                <div class="col">
                                    <div class="card border-secondary">
                                        <div class="card-body">
                                            <h6 class="card-title fw-bold text-uppercase">Privacy Consent Statement</h6>
                                            <p class="mb-2">
                                                BY SUBMITTING YOUR APPLICATION TO <strong>GC&amp;C GROUP OF COMPANIES</strong>, YOU CONSENT TO THE 
                                                COLLECTION AND PROCESSING OF YOUR PERSONAL DATA FOR RECRUITMENT PURPOSES.
                                            </p>
                                            <p class="mb-2">
                                                YOUR INFORMATION WILL BE KEPT CONFIDENTIAL AND ACCESSED ONLY BY AUTHORIZED PERSONNEL. 
                                                ALL DATA WILL BE HANDLED IN COMPLIANCE WITH THE <STRONG>DATA PRIVACY ACT OF 2012 (RA 10173)</STRONG> 
                                                AND WILL NOT BE SHARED WITHOUT YOUR CONSENT, UNLESS REQUIRED BY LAW.
                                            </p>
                                            <p class="mb-0">
                                                BY PROCEEDING, YOU CONFIRM YOUR UNDERSTANDING AND AGREEMENT TO THIS CONSENT.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__foot">
                    <div class="row">
                        <div class="col-6">
                            <button id="back" type="button" class="btn btn-warning text-white" @click="goBack" v-if="canGoBack">BACK</button>
                        </div>
                        <div class="col-6 m--align-right">
                            <button id="next" type="button" class="btn btn-primary" @click="goNext" v-if="canGoNext">NEXT</button>
                            <button id="submit" type="submit" class="btn btn-success" @click="submitAll" v-if="canSubmit" :disabled="isSubmitting">
                                <span v-if="isSubmitting">
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    Submitting...
                                </span>
                                <span v-else>SUBMIT APPLICATION</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalConsent" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Data Privacy Consent</h5>
                </div>
                <div class="modal-body">
                    <p>
                        By continuing, you agree to the collection and processing of
                        your personal information for recruitment and application
                        purposes in accordance with the Data Privacy Act.
                    </p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn btn-success" @click="acceptConsent">
                        I Agree
                    </button>
                    <button class="btn btn-danger" @click="declineConsent">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalExisting" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="fa fa-exclamation-triangle"></i>
                        Existing Application Detected
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-3">
                        An application with the same personal information already exists
                        in our system.
                    </p>
                    <p class="text-muted mb-0">
                        Multiple submissions are not allowed. If you believe this is an
                        error, please contact the HR office for assistance.
                    </p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>



<style>
    .modal-xxl {
        max-width: 90%;
    }

    .tab-disabled {
        pointer-events: none;
        opacity: 0.6;
    }

</style>

<div class="m-content" id="m_content">
    <div class="row">
        <div class="col-lg-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text" id="page_title">
                                EDIT APPLICATION PAGE
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools btnArchive">
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left m-tabs-line--primary" role="tablist" id="mainTabNav">
                                    <li class="nav-item m-tabs__item">
                                        <a href="#main_candidate_information" role="tab" class="nav-link m-tabs__link active main-tab-link">
                                            <i class="fa fa-user"></i> Candidate Information
                                        </a>
                                    </li>
                                    <li class="nav-item m-tabs__item">
                                        <a href="#main_manpower_information" role="tab" class="nav-link m-tabs__link main-tab-link">
                                            <i class="fa fa-inbox"></i> Manpower Information
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="tab-content" id="content_candidate_info">
                        <div class="tab-pane active show" id="main_candidate_information">
                            <div class="row p-0 text-uppercase">
                                <div class="col-12">
                                    <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left m-tabs-line--primary"   :class="{ 'tab-disabled': can_edit_candidate }"  role="tablist" id="innerTabNav">
                                        <li class="nav-item m-tabs__item">
                                            <a data-toggle="tab" href="#candidate_personal_information" role="tab" class="nav-link m-tabs__link active" @click="setSelectedForm('#personal_information_form')">
                                                <i class="fa fa-user"></i> Personal Info
                                            </a>
                                        </li>
                                        <li class="nav-item m-tabs__item">
                                            <a data-toggle="tab" href="#candidate_contact_information" role="tab" class="nav-link m-tabs__link" @click="setSelectedForm('#contact_information_form')">
                                                <i class="fa fa-envelope"></i> Contact Info
                                            </a>
                                        </li>
                                        <li class="nav-item m-tabs__item">
                                            <a data-toggle="tab" href="#candidate_work_experience" role="tab" class="nav-link m-tabs__link" @click="setSelectedForm('#work_experience_form')">
                                                <i class="fa fa-briefcase"></i> Work Experience
                                            </a>
                                        </li>
                                        <li class="nav-item m-tabs__item">
                                            <a data-toggle="tab" href="#candidate_educational_information" role="tab" class="nav-link m-tabs__link" @click="setSelectedForm('#educational_information_form')">
                                                <i class="fa fa-graduation-cap"></i> Educational Info
                                            </a>
                                        </li>
                                        <li class="nav-item m-tabs__item">
                                            <a data-toggle="tab" href="#candidate_application_information" role="tab" class="nav-link m-tabs__link" @click="setSelectedForm('#application_information_form')">
                                                <i class="fa fa-file"></i> Application Info
                                            </a>
                                        </li>
                                        <li class="nav-item m-tabs__item">
                                            <a data-toggle="tab" href="#candidate_resume_upload" role="tab" class="nav-link m-tabs__link" @click="setSelectedForm('#resume_upload_form')">
                                                <i class="fa fa-upload"></i> Resume Upload
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="candidate_personal_information">
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
                                                    <label for="firstname" class="form-control-label mb-2 required">First name</label>
                                                    <input type="text" name="firstname" id="firstname" class="form-control" v-model="selectedApplication.firstname" :disabled="!can_edit_candidate" data-validation="required">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="middlename" class="form-control-label mb-2">Middle name</label>
                                                    <input type="text" name="middlename" id="middlename" class="form-control" placeholder="(optional)" v-model="selectedApplication.middlename" :disabled="!can_edit_candidate">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="lastname" class="form-control-label mb-2 required">Last name</label>
                                                    <input type="text" name="lastname" id="lastname" class="form-control" v-model="selectedApplication.lastname" :disabled="!can_edit_candidate" data-validation="required">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="suffix" class="form-control-label mb-2">Suffix</label>
                                                    <input type="text" name="suffix" id="suffix" class="form-control" placeholder="(optional)" v-model="selectedApplication.suffix" :disabled="!can_edit_candidate">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="m-separator m-separator--dashed d-xl-12"></div>

                                        <div class="row mt-2">
                                            <div class="col">
                                                <div class="m-portlet__head-title">
                                                    <h5 class="m-portlet__head-text">ADDITIONAL INFORMATION</h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="gender" class="form-control-label required">Gender</label>
                                                    <select id="gender" name="gender" class="form-control" data-validation="required" :disabled="!can_edit_candidate">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="civil_status" class="form-control-label required">Civil Status</label>
                                                    <select id="civil_status" name="civil_status" class="form-control" data-validation="required" :disabled="!can_edit_candidate">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <label for="religion" class="form-control-label required">Religion</label>
                                                    <input type="text" name="religion" id="religion" placeholder="ENTER RELIGION" class="form-control" data-validation="required" autocomplete="off" v-model="selectedApplication.religion" :disabled="!can_edit_candidate">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <label for="height" class="form-control-label">Height</label>
                                                    <input type="text" name="height" id="height" placeholder="(OPTIONAL)" class="form-control" autocomplete="off" v-model="selectedApplication.height" :disabled="!can_edit_candidate">
                                                    <span class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">* IN FEET AND INCHES *</span>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <label for="weight" class="form-control-label">Weight</label>
                                                    <input type="text" name="weight" id="weight" placeholder="(OPTIONAL)" class="form-control" autocomplete="off" v-model="selectedApplication.weight" :disabled="!can_edit_candidate">
                                                    <span class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">* IN KILOGRAMS *</span>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="birthdate" class="form-control-label required">Birthdate</label>
                                                    <div class="input-group date">
                                                        <input class="form-control m-input" type="text" id="birthdate" name="birthdate" placeholder="mm/dd/yyyy" maxlength="12" size="12" data-validation="required" oninput="formatDate(this)" autocomplete="off" :disabled="!can_edit_candidate" readonly>
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
                                                    <input type="text" name="citizenship" id="citizenship" placeholder="ENTER CITIZENSHIP" class="form-control" data-validation="required" autocomplete="off" v-model="selectedApplication.citizenship" :disabled="!can_edit_candidate">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="candidate_contact_information">
                                    <div class="row mt-2">
                                        <div class="col">
                                            <div class="m-portlet__head-title">
                                                <h5 class="m-portlet__head-text">CONTACT INFORMATION</h5>
                                            </div>
                                        </div>
                                    </div>

                                    <form id="contact_information_form" action="javascript:void(0);">
                                        <div class="row mt-2">
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="contact_no" class="form-control-label required">Mobile Number</label>
                                                    <input type="text" v-model="selectedApplication.contact_no" name="contact_no" id="contact_no" placeholder="ENTER YOUR MOBILE NUMBER" class="form-control" data-validation="required" autocomplete="off" :disabled="!can_edit_candidate">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="email" class="form-control-label">Email</label>
                                                    <input type="email" v-model="selectedApplication.email" name="email" id="email" placeholder="(optional)" class="form-control" autocomplete="off" :disabled="!can_edit_candidate">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="tel_no" class="form-control-label">Telephone Number</label>
                                                    <input type="text" v-model="selectedApplication.tel_no" name="tel_no" id="tel_no" placeholder="(optional)" class="form-control" autocomplete="off" :disabled="!can_edit_candidate">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="address" class="form-control-label required">Current Address</label>
                                                    <input type="text" v-model="selectedApplication.address" name="address" id="address" placeholder="ENTER YOUR CURRENT ADDRESS" class="form-control" data-validation="required" autocomplete="off" :disabled="!can_edit_candidate">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="permanent_address" class="form-control-label required">Permanent Address</label>
                                                    <input type="text" v-model="selectedApplication.permanent_address" name="permanent_address" id="permanent_address" placeholder="ENTER YOUR PERMANENT ADDRESS" class="form-control" data-validation="required" autocomplete="off" :disabled="!can_edit_candidate">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="m-separator m-separator--dashed d-xl-12"></div>

                                        <div class="row mt-2">
                                            <div class="col">
                                                <div class="m-portlet__head-title">
                                                    <h5 class="m-portlet__head-text">CHARACTER REFERENCES</h5>
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
                                                    <input type="text" :name="'references[' + index + '][id]'" :value="reference.id" hidden>
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                        <div class="form-group">
                                                            <label :for="`full_name_${index + 1}`" class="form-control-label required">Full Name</label>
                                                            <input :id="`full_name_${index + 1}`" v-model="reference.ref_name" :name="`references[${index}][ref_name]`" placeholder="ENTER FULL NAME" type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input" :disabled="!can_edit_candidate">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                        <div class="form-group">
                                                            <label :for="`contact_no_${index + 1}`" class="form-control-label required">Contact Number</label>
                                                            <input :id="`contact_no_${index + 1}`" v-model="reference.ref_contact_no" :name="`references[${index}][ref_contact_no]`" placeholder="ENTER CONTACT NUMBER" type="text" maxlength="11" autocomplete="off" data-validation="required" class="form-control m-input"
                                                                @input="reference.ref_contact_no = $event.target.value.replace(/\D/g, '')" :disabled="!can_edit_candidate">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                        <div class="form-group">
                                                            <label :for="`address_${index + 1}`" class="form-control-label required">Address</label>
                                                            <input :id="`address_${index + 1}`" v-model="reference.ref_address" :name="`references[${index}][ref_address]`" placeholder="ENTER ADDRESS" type="text" maxlength="500" autocomplete="off" data-validation="required" class="form-control m-input" :disabled="!can_edit_candidate">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                        <div class="form-group">
                                                            <label :for="`company_${index + 1}`" class="form-control-label required">Company</label>
                                                            <input :id="`company_${index + 1}`" v-model="reference.ref_company" :name="`references[${index}][ref_company]`" placeholder="ENTER COMPANY" type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input" :disabled="!can_edit_candidate">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                        <div class="form-group">
                                                            <label :for="`position_${index + 1}`" class="form-control-label required">Position</label>
                                                            <input :id="`position_${index + 1}`" v-model="reference.ref_position" :name="`references[${index}][ref_position]`" placeholder="ENTER POSITION" type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input" :disabled="!can_edit_candidate">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                        <div class="form-group">
                                                            <label :for="`relationship_${index + 1}`" class="form-control-label required">Relationship</label>
                                                            <input :id="`relationship_${index + 1}`" v-model="reference.ref_relationship" :name="`references[${index}][ref_relationship]`" placeholder="ENTER RELATIONSHIP" type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input" :disabled="!can_edit_candidate">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="candidate_work_experience">
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
                                                        <input type="checkbox" name="is_fresh_graduate" v-model="isFreshGraduate" @change="onFreshGraduateChange" :disabled="!can_edit_candidate">
                                                        I am a Fresh Graduate / No Work Experience
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <template v-if="!isFreshGraduate">
                                            <template v-for="(work, index) in workExperiences">
                                                <input type="text" :name="'work_experiences[' + index + '][id]'" :value="work.id" hidden>
                                                <div class="row mt-2 align-items-center">
                                                    <div class="col-12 d-flex justify-content-between">
                                                        <strong>ENTRY #{{ index + 1 }}</strong>
                                                        <button type="button" class="btn btn-danger btn-sm btnArchive" v-on:click="removeWork(index)" v-show="workExperiences.length > 1" :disabled="!can_edit_candidate">
                                                            <i class="la la-trash"></i> Remove
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group">
                                                            <label :for="'work_company_' + index" class="form-control-label required">Company</label>
                                                            <input :id="'work_company_' + index" :name="`work_experiences[${index}][work_company]`" type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input" v-model="work.work_company" :disabled="!can_edit_candidate">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group">
                                                            <label :for="'work_position_' + index" class="form-control-label required">Position</label>
                                                            <input :id="'work_position_' + index" :name="`work_experiences[${index}][work_position]`"  type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input" v-model="work.work_position" :disabled="!can_edit_candidate">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group">
                                                            <label :for="'work_from_' + index" class="form-control-label required">From Year</label>
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                                <input :id="'work_from_' + index" :name="`work_experiences[${index}][work_from]`" type="text" maxlength="4" autocomplete="off" data-validation="required" class="form-control m-input" @input="work.work_from = work.work_from.replace(/\D/g, '')" v-model="work.work_from" :disabled="!can_edit_candidate">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group">
                                                            <label :for="'work_status_' + index" class="form-control-label required">Status</label>
                                                            <input :id="'work_status_' + index" :name="`work_experiences[${index}][work_status]`" type="text" maxlength="50" autocomplete="off" data-validation="required" class="form-control m-input" v-model="work.work_status" :disabled="!can_edit_candidate">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group">
                                                            <label :for="'work_reason_' + index" class="form-control-label required">Reason for leaving</label>
                                                            <input :id="'work_reason_' + index" :name="`work_experiences[${index}][work_reason]`" type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input" v-model="work.work_reason" :disabled="!can_edit_candidate">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group">
                                                            <label :for="'work_to_' + index" class="form-control-label required">To Year</label>
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                                <input :id="'work_to_' + index" :name="`work_experiences[${index}][work_to]`" type="text" maxlength="4" autocomplete="off" data-validation="required" class="form-control m-input" @input="work.work_to = work.work_to.replace(/\D/g, '')" v-model="work.work_to" :disabled="!can_edit_candidate">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            </template>
                                            <div class="row mt-2">
                                                <div class="col-12">
                                                    <button type="button" class="btn btn-success btnSave" v-on:click="addWork" :disabled="!can_edit_candidate">
                                                        <i class="la la-plus"></i> New
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <div class="alert alert-info mt-2">
                                                <i class="fa fa-info-circle"></i>
                                                Marked as <strong>Fresh Graduate / No Work Experience</strong>
                                            </div>
                                        </template>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="candidate_educational_information">
                                    <div class="row mt-2">
                                        <div class="col">
                                            <div class="m-portlet__head-title">
                                                <h5 class="m-portlet__head-text">EDUCATIONAL BACKGROUND</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <form action="javascript:void(0);" id="educational_information_form">
                                        <template v-for="(item, index) in educInfo" :key="'educ_' + index">
                                        <input type="text" :name="'schools[' + index + '][id]'" :value="item.id" hidden>
                                            <div class="row mt-2 align-items-center">
                                                <div class="col-12 d-flex justify-content-between">
                                                    <span></span>
                                                    <button type="button" class="btn btn-danger btn-sm btnArchive" v-on:click="removeEducInfo(index)" v-show="educInfo.length > 1" :disabled="!can_edit_candidate">
                                                        <i class="la la-trash"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label :for="'level_' + index" class="form-control-label required">Level</label>
                                                        <select :id="'level_' + index" :name="'schools[' + index + '][educ_level_type]'" :data-index="index" class="form-control m-input educ-level-select" :data-id="item.id" data-validation="required" :disabled="!can_edit_candidate">
                                                            <option></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label :for="'educ_school_' + index" class="form-control-label required">School</label>
                                                        <input :id="'educ_school_' + index" :name="'schools[' + index + '][educ_school]'" type="text" maxlength="200" autocomplete="off" data-validation="required" class="form-control m-input" v-model="item.educ_school" :disabled="!can_edit_candidate">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label :for="'educ_from_' + index" class="form-control-label required">From Year</label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                            <input :id="'educ_from_' + index" :name="'schools[' + index + '][educ_from]'" type="text" maxlength="4" autocomplete="off" data-validation="required" class="form-control m-input" @input="item.educ_from = item.educ_from.replace(/\D/g, '')" v-model="item.educ_from" :disabled="!can_edit_candidate">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label :for="'educ_degree_' + index" class="form-control-label">Educational Degree</label>
                                                        <input :id="'educ_degree_' + index" :name="'schools[' + index + '][educ_degree]'" type="text" maxlength="200" autocomplete="off" class="form-control m-input" v-model="item.educ_degree" :disabled="!can_edit_candidate">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label :for="'honor_' + index" class="form-control-label">Honor</label>
                                                        <input :id="'honor_' + index" :name="'schools[' + index + '][educ_honors]'" type="text" maxlength="200" autocomplete="off" class="form-control m-input" v-model="item.educ_honors" :disabled="!can_edit_candidate">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label :for="'educ_to_' + index" class="form-control-label required">To Year</label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                            <input :id="'educ_to_' + index" :name="'schools[' + index + '][educ_to]'" type="text" maxlength="4" autocomplete="off" data-validation="required" class="form-control m-input" @input="item.educ_to = item.educ_to.replace(/\D/g, '')" v-model="item.educ_to" :disabled="!can_edit_candidate">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-success btnNew" v-on:click="addEducInfo" v-show="can_edit_candidate">
                                                    <i class="la la-plus"></i> New
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="candidate_application_information">
                                    <div class="row mt-2">
                                        <div class="col">
                                            <div class="m-portlet__head-title">
                                                <h5 class="m-portlet__head-text">APPLICATION INFORMATION</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <form action="javascript:void(0);" id="application_information_form">
                                        <div class="row mt-2">
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="recruitment" class="form-control-label required">Recruitment Source</label>
                                                    <select id="recruitment" name="recruitment" class="form-control" data-validation="required" :disabled="!can_edit_candidate">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 referral d-none">
                                                <div class="form-group">
                                                    <label for="referral" class="form-control-label required">referral</label>
                                                    <input type="text" name="referral" id="referral" placeholder="Enter Referral Name" class="form-control" data-validation="required" autocomplete="off" maxlength="200" :disabled="!can_edit_candidate">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 referral d-none">
                                                <div class="form-group">
                                                    <label for="referral-relationship" class="form-control-label required">relationship</label>
                                                    <input type="text" name="referral-relationship" id="referral-relationship" placeholder="Enter Referral Relationship" class="form-control" data-validation="required" autocomplete="off" maxlength="200" :disabled="!can_edit_candidate">
                                                </div>
                                            </div>
                                            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                                <div class="form-group">
                                                    <label for="position_id" class="form-control-label required">Position Applied FOR</label>
                                                    <select id="position_id" name="positions" multiple="multiple" class="form-control" data-validation="required" :disabled="!can_edit_candidate"></select>
                                                    <span class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">You can select multiple position.</span>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="applied_dt" class="form-control-label required">Date of Application</label>
                                                    <div class="input-group date">
                                                        <input class="form-control m-input" type="text" id="applied_dt" name="applied_dt" placeholder="mm/dd/yyyy" maxlength="12" size="12" data-validation="required" autocomplete="off" readonly :disabled="!can_edit_candidate">
                                                        <span class="input-group-addon">
                                                            <i class="la la-calendar glyphicon-th"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="candidate_resume_upload">
                                    <div class="row mt-2">
                                        <div class="col">
                                            <div class="m-portlet__head-title">
                                                <h5 class="m-portlet__head-text">RESUME UPLOAD</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <form action="javascript:void(0);" id="resume_upload_form" enctype="multipart/form-data">
                                        <div class="row pb-2">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <span class="btn btn-success fileinput-button" v-show="can_edit_candidate">
                                                        <i class="glyphicon glyphicon-plus"></i>
                                                        <span>SELECT FILE</span>
                                                        <input type="file" id="resume-fileupload" name="file" accept=".pdf, application/pdf">
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="new_preview" class="m-portlet__body">
                                            <template v-if="uploadedFile">
                                                <div class="row col-12 m-widget2">
                                                    <div :class="getClass(uploadedFile)">
                                                        <div class="m-widget4__item d-flex align-items-center">
                                                            <div class="m-widget4__img m-widget4__img--icon">
                                                                <img :src="getExtension(uploadedFile)" alt="" height="50" width="50">
                                                            </div>
                                                            <div class="m-widget2__desc">
                                                                <span class="m-widget4__text text-uppercase">{{ uploadedFile }}</span>
                                                            </div>
                                                            <div class="m-widget2__actions ml-auto">
                                                                <button type="button" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView" @click="resumefileDelete()" v-show="can_edit_candidate">
                                                                    <i class="m-nav__link-icon flaticon-circle"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <div class="col-12">
                                                    <strong><h5>PLEASE UPLOAD RESUME AS PDF FORMAT</h5></strong>
                                                </div>
                                            </template>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="m-portlet__foot mt-2">
                                <div class="row">
                                    <div class="col-lg-12 text-right">
                                        <button type="button" class="btn btn-warning text-light btnEdit" v-show="!can_edit_candidate" @click="startEdit">
                                            EDIT INFORMATION
                                        </button>

                                        <button type="button" class="btn btn-success btnUpdate" v-show="can_edit_candidate" @click="updateApplication">
                                            UPDATE
                                        </button>

                                        <button type="button" class="btn btn-danger btnCancel" v-show="can_edit_candidate" @click="cancelEdit">
                                            CANCEL
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="main_manpower_information">
                            <div class="row">
                                <div class="col-8">
                                    <div class="accordion">
                                        <div class="card">
                                            <div id="interviewAccordionHead"
                                                class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm">
                                                <div class="m-portlet__head">
                                                    <div class="m-portlet__head-caption">
                                                        <a href="javascript:;" class="m-portlet__nav-link collapsed" data-toggle="collapse" data-target="#interviewAccordionBody" aria-expanded="false" aria-controls="interviewAccordionBody">
                                                            <h5 class="m-portlet__head-text mb-0">
                                                                <span style="white-space: nowrap;">INTERVIEW</span>
                                                            </h5>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="interviewAccordionBody" class="collapse show" aria-labelledby="interviewAccordionHead" data-parent="#candidateAccordion">
                                                <div class="card-body m-portlet__body--custom">
                                                    <div class="row mb-2">
                                                        <div class="col-12">
                                                            <button class="btn btn-success" data-toggle="modal" data-target="#interviewModal">Schedule Interview</button>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <table class="table table-striped table-bordered" width="100%" id="interviewTable">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Interview Details</th>
                                                                        <th>Status</th>
                                                                        <th>Created By</th>
                                                                        <th>Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <template v-if="interviews && interviews.length">
                                                                        <tr v-for="item in interviews" :key="item.id">
                                                                            <td>
                                                                                <div class="m--font-boldest">{{ formatDateTime(item.schedule_dt) }}</div>
                                                                                <div><small class="text-muted">Type:</small> {{ item.interview_type || '-' }}</div>
                                                                                <div><small class="text-muted">Interviewer:</small> {{ item.interviewer_name || '-' }}</div>
                                                                                <div><small class="text-muted">Location:</small> {{ formatPlatform(item.platform_id) }}</div>
                                                                            </td>
                                                                            <td>
                                                                                <span class="badge"
                                                                                    :class="{
                                                                                        'bg-warning text-dark': item.assessment.status === 'pending',
                                                                                        'bg-success': item.assessment.status === 'pass',
                                                                                        'bg-danger': item.assessment.status === 'fail',
                                                                                        'bg-secondary': !item.assessment.status
                                                                                    }">
                                                                                    {{ item.assessment.status || '-' }}
                                                                                </span>
                                                                            </td>
                                                                            <td>
                                                                                <span>{{ item.created_by_name || '-' }}</span>
                                                                            </td>
                                                                            <td class="text-nowrap">
                                                                                <template v-if="item.assessment.status === 'pending'">
                                                                                    <button type="button" class="btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnSave" data-toggle="m-tooltip" data-placement="bottom" data-skin="dark" title="Pass Interview" @click="passInterview(item)">
                                                                                        <i class="la la-check"></i>
                                                                                    </button>
                                                                                    <button type="button" class="btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnSave" data-toggle="m-tooltip" data-placement="bottom" data-skin="dark" title="Fail Interview" @click="failInterview(item)">
                                                                                        <i class="la la-times"></i>
                                                                                    </button>
                                                                                </template>
                                                                                <template v-else>
                                                                                    <button type="button" class="btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnView" data-toggle="m-tooltip" data-placement="bottom" data-skin="dark" title="View Interview Assessment" @click="viewInterview(item)">
                                                                                        <i class="la la-eye"></i>
                                                                                    </button>
                                                                                </template>
                                                                                <button type="button" class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnArchive" data-toggle="m-tooltip" data-placement="bottom" data-skin="dark" title="Delete Interview" @click="deleteInterview(item.id)">
                                                                                    <i class="la la-trash"></i>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    </template>

                                                                    <tr v-else>
                                                                        <td colspan="4" class="text-center text-muted py-4">
                                                                            No interview records found.
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div id="bgCheckAccordionHead"
                                                class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm">
                                                <div class="m-portlet__head">
                                                    <div class="m-portlet__head-caption">
                                                        <a href="javascript:;" class="m-portlet__nav-link collapsed" data-toggle="collapse" data-target="#bgCheckAccordionBody" aria-expanded="false" aria-controls="bgCheckAccordionBody">
                                                            <h5 class="m-portlet__head-text mb-0">
                                                                <span style="white-space: nowrap;">BACKGROUND CHECK</span>
                                                            </h5>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="bgCheckAccordionBody" class="collapse" aria-labelledby="bgCheckAccordionHead" data-parent="#candidateAccordion">
                                                <div class="card-body m-portlet__body--custom">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            FOREACH REFERENCE
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div id="skillAssAccordionHead"
                                                class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm">
                                                <div class="m-portlet__head">
                                                    <div class="m-portlet__head-caption">
                                                        <a href="javascript:;" class="m-portlet__nav-link collapsed" data-toggle="collapse" data-target="#skillAssAccordionBody" aria-expanded="false" aria-controls="skillAssAccordionBody">
                                                            <h5 class="m-portlet__head-text mb-0">
                                                                <span style="white-space: nowrap;">SKILLS ASSESMENT</span>
                                                            </h5>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="skillAssAccordionBody" class="collapse" aria-labelledby="skillAssAccordionHead" data-parent="#candidateAccordion">
                                                <div class="card-body m-portlet__body--custom">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            SKILL ASSESMENT
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div id="jobOfferAccordionHead"
                                                class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm">
                                                <div class="m-portlet__head">
                                                    <div class="m-portlet__head-caption">
                                                        <a href="javascript:;" class="m-portlet__nav-link collapsed" data-toggle="collapse" data-target="#jobOfferAccordionBody" aria-expanded="false" aria-controls="skillAssAccordionBody">
                                                            <h5 class="m-portlet__head-text mb-0">
                                                                <span style="white-space: nowrap;">JOB OFFER</span>
                                                            </h5>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="jobOfferAccordionBody" class="collapse" aria-labelledby="jobOfferAccordionHead" data-parent="#candidateAccordion">
                                                <div class="card-body m-portlet__body--custom">
                                                    <div class="row">
                                                        <div class="col-12">
                                                           JOB OFFER
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div id="preEmpCheckAccordionHead"
                                                class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm">
                                                <div class="m-portlet__head">
                                                    <div class="m-portlet__head-caption">
                                                        <a href="javascript:;" class="m-portlet__nav-link collapsed" data-toggle="collapse" data-target="#preEmpCheckAccordionBody" aria-expanded="false" aria-controls="skillAssAccordionBody">
                                                            <h5 class="m-portlet__head-text mb-0">
                                                                <span style="white-space: nowrap;">PRE-EMPLOYMENT CHECKLIST</span>
                                                            </h5>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="preEmpCheckAccordionBody" class="collapse" aria-labelledby="preEmpCheckAccordionHead" data-parent="#candidateAccordion">
                                                <div class="card-body m-portlet__body--custom">
                                                    <div class="row">
                                                        <div class="col-12">
                                                        PRE-EMPLOYMENT CHECKLIST
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div id="contractAccordionHead"
                                                class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm">
                                                <div class="m-portlet__head">
                                                    <div class="m-portlet__head-caption">
                                                        <a href="javascript:;" class="m-portlet__nav-link collapsed" data-toggle="collapse" data-target="#contractAccordionBody" aria-expanded="false" aria-controls="skillAssAccordionBody">
                                                            <h5 class="m-portlet__head-text mb-0">
                                                                <span style="white-space: nowrap;">CONTRACT</span>
                                                            </h5>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="contractAccordionBody" class="collapse" aria-labelledby="contractAccordionHead" data-parent="#candidateAccordion">
                                                <div class="card-body m-portlet__body--custom">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            CONTRACT
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card shadow-sm border-0 text-uppercase">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0 font-weight-bold">Assigned Manpower Request</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">MRF Reference No</small>
                                                        <strong>{{ assigned_manpower_request.mrf_reference_no }}</strong>
                                                    </div>

                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Position</small>
                                                        <strong>{{ assigned_manpower_request.position }}</strong>
                                                    </div>

                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Company</small>
                                                        <span>{{ assigned_manpower_request.company }}</span>
                                                    </div>

                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Department</small>
                                                        <span>{{ assigned_manpower_request.department }}</span>
                                                    </div>

                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Contract Type</small>
                                                        <span class="badge badge-primary">{{ assigned_manpower_request.contract_type }}</span>
                                                    </div>

                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Request Nature</small>
                                                        <span class="badge badge-info">{{ assigned_manpower_request.request_nature }}</span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Gender</small>
                                                        <span>{{ assigned_manpower_request.gender }}</span>
                                                    </div>

                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Relationship Status</small>
                                                        <span>{{ assigned_manpower_request.relationship_status }}</span>
                                                    </div>

                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Age Range</small>
                                                        <span>{{ assigned_manpower_request.age_range }}</span>
                                                    </div>

                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Station</small>
                                                        <span>{{ assigned_manpower_request.station }}</span>
                                                    </div>

                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">People Needed</small>
                                                        <strong>{{ assigned_manpower_request.people_no }}</strong>
                                                    </div>

                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Requested By</small>
                                                        <strong>
                                                            {{ assigned_manpower_request.firstname }}
                                                            {{ assigned_manpower_request.middlename }}
                                                            {{ assigned_manpower_request.lastname }}
                                                        </strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="assessInterviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assess Interview</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="interview_assessment" onsubmit="return false;" action="javascript:void(0);" enctype="multipart/form-data">
                    <div class="modal-body" v-if="selectedInterview">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="font-weight-bold d-block mb-1">Interview Type</label>
                                    <div>{{ selectedInterview.interview_type || '-' }}</div>
                                </div>

                                <div class="mb-4">
                                    <label class="font-weight-bold d-block mb-1">Schedule</label>
                                    <div>{{ formatDateTime(selectedInterview.schedule_dt) }}</div>
                                </div>

                                <div class="mb-4">
                                    <label class="font-weight-bold d-block mb-1">Interview Notes</label>
                                    <div>{{ selectedInterview.remarks || '-' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="font-weight-bold d-block mb-1">Interviewer</label>
                                    <div>{{ selectedInterview.interviewer_name || '-' }}</div>
                                </div>

                                <div class="mb-4">
                                    <label class="font-weight-bold d-block mb-1">Platform</label>
                                    <div>{{ formatPlatform(selectedInterview.platform_id) }}</div>
                                </div>

                                <div class="mb-4">
                                    <label class="font-weight-bold d-block mb-1">Current Status</label>
                                    <div>{{ selectedInterview.status || '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="form-group">
                            <label for="assessmentResult" class="required">Assessment Result</label>
                            <select class="form-control" id="assessmentResult" name="status">
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="assessmentRemarks" class="required">Assessment Remarks</label>
                            <textarea class="form-control" id="assessmentRemarks" rows="4" name="assessment_remarks" placeholder="Enter assessment remarks..." data-validation="required"></textarea>
                        </div>

                        <div class="form-group mb-0" id="assessAttachment">
                            <label for="assessment_fileupload" class="required">Attachment</label>
                            <div class="form-group">
                                <span class="btn btn-success fileinput-button">
                                    <i class="glyphicon glyphicon-plus"></i>
                                    <span>SELECT FILE</span>
                                    <input type="file" id="assessment_fileupload" multiple accept=".pdf, image/*,.jpg,.jpeg,.png,.bmp,.webp">
                                </span><br/>
                            </div>
                        </div>
                        
                        <div id="assessment_new_preview" class="m-portlet__body">
                            <template v-if="assessment.attachments && assessment.attachments.length > 0">
                                <div class="row col-12 m-widget2" v-for="(file, index) in assessment.attachments" :key="index">
                                    <div :class="getClass(file.name)">
                                        <div class="m-widget4__item d-flex align-items-center">
                                            <div class="m-widget4__img m-widget4__img--icon">
                                                <img :src="getExtension(file.name)" alt="" height="50" width="50">
                                            </div>

                                            <div class="m-widget2__desc">
                                                <span class="m-widget4__text text-uppercase">{{ file.name }}</span>
                                            </div>

                                            <div class="m-widget2__actions ml-auto">
                                                <button type="button" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView" @click="assessmentFileDelete(index)">
                                                    <i class="m-nav__link-icon flaticon-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template v-else>
                                <div class="col-12">
                                    <strong><h5>NO ATTACHMENTS UPLOADED</h5></strong>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btnSave">Save Assessment</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="interviewModal" tabindex="-1">
        <form id="interviewForm" onsubmit="return false;" onkeydown="return event.key !== 'Enter';">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Set Interview Date</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="candidate-info mb-3">
                            <h5 class="mb-3 text-muted">Candidate Information</h5>
                            <p class="m--font-boldest mb-0"><strong>{{ fullName || '-' }}</strong></p>
                            <p><small></small></p>
                        </div>
                        <hr>
                        <div class="form-group m-form__group">
                            <label for="employee" class="required">Interviewer</label>
                            <select class="form-control m-input" id="employee" name="interviewer_id" data-validation="required">
                                <option></option>
                            </select>
                        </div>
                        <div class="form-group m-form__group">
                            <label for="interview_type" class="required">Interview Type</label>
                            <input class="form-control m-input" id="interview_type" name="interview_type" placeholder="INTERVIEW TYPE" data-validation="required">
                        </div>
                        <div class="form-group m-form__group">
                            <label for="interviewDate" class="required">Schedule</label>
                            <input type="text" class="form-control m-input" id="interviewDate" name="schedule_dt" placeholder="mm/dd/yyyy hh:mm" data-validation="required" autocomplete="off">
                        </div>
                        <div class="form-group m-form__group">
                            <label for="interviewLocation" class="required">Location/Platform</label>
                            <select class="form-control m-input" id="interviewLocation" name="platform_id" data-validation="required">
                                <option></option>
                            </select>
                        </div>

                        <div class="form-group m-form__group">
                            <label for="interviewNotes">Additional Notes</label>
                            <textarea class="form-control" id="interviewNotes" name="remarks" rows="3" placeholder="Any additional instructions or notes for the candidate..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btnSave" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btnSave">Schedule Interview</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="viewInterviewModal" tabindex="-1" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Interview Assessment</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="view_interview_assessment" onsubmit="return false;" action="javascript:void(0);" enctype="multipart/form-data">
                    <div class="modal-body" v-if="selectedInterview">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="font-weight-bold d-block mb-1">Interview Type</label>
                                    <div>{{ selectedInterview.interview_type || '-' }}</div>
                                </div>

                                <div class="mb-4">
                                    <label class="font-weight-bold d-block mb-1">Schedule</label>
                                    <div>{{ formatDateTime(selectedInterview.schedule_dt) }}</div>
                                </div>

                                <div class="mb-4">
                                    <label class="font-weight-bold d-block mb-1">Interview Notes</label>
                                    <div>{{ selectedInterview.remarks || '-' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="font-weight-bold d-block mb-1">Interviewer</label>
                                    <div>{{ selectedInterview.interviewer_name || '-' }}</div>
                                </div>

                                <div class="mb-4">
                                    <label class="font-weight-bold d-block mb-1">Platform</label>
                                    <div>{{ formatPlatform(selectedInterview.platform_id) }}</div>
                                </div>

                                <div class="mb-4">
                                    <label class="font-weight-bold d-block mb-1">Current Status</label>
                                    <div>{{ selectedInterview.status || '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="form-group">
                            <label for="edit_assessmentResult" class="required">Assessment Result</label>
                            <select class="form-control" id="edit_assessmentResult" name="status" :disabled="!isEditable">
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_assessmentRemarks" class="required">Assessment Remarks</label>
                            <textarea class="form-control" id="edit_assessmentRemarks" rows="4" name="assessment_remarks" placeholder="Enter assessment remarks..." v-model="selectedInterview.assessment.assessment_remarks" :disabled="!isEditable" data-validation="required"></textarea>
                        </div>

                        <div class="form-group mb-0" id="assessAttachment">
                            <label for="edit_assessment_fileupload" class="required">Attachment</label>
                            <div class="form-group" v-show="isEditable">
                                <span class="btn btn-success fileinput-button">
                                    <i class="glyphicon glyphicon-plus"></i>
                                    <span>SELECT FILE</span>
                                    <input type="file" id="edit_assessment_fileupload" name="files[]" multiple accept=".pdf,image/*,.jpg,.jpeg,.png,.bmp,.webp">
                                </span><br/>
                            </div>
                        </div>
                        
                        <div id="assessment_edit_preview" class="m-portlet__body">
                            <template v-if="edit_attachments && edit_attachments.length > 0">
                                <div class="row col-12 m-widget2" v-for="(file, index) in edit_attachments" :key="index">
                                    <div :class="getClass(file.name)">
                                        <div class="m-widget4__item d-flex align-items-center">
                                            <div class="m-widget4__img m-widget4__img--icon">
                                                <img :src="getExtension(file.name)" alt="" height="50" width="50">
                                            </div>

                                            <div class="m-widget2__desc">
                                                <span class="m-widget4__text text-uppercase">{{ file.name }}</span>
                                            </div>

                                            <div class="m-widget2__actions ml-auto" v-show="isEditable">
                                                <button type="button" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView" @click="interviewfileDelete(index)">
                                                    <i class="m-nav__link-icon flaticon-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template v-else>
                                <div class="col-12">
                                    <strong><h5>NO ATTACHMENTS UPLOADED. PLEASE UPLOAD A FILE.</h5></strong>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning text-light btnEdit" v-show="!isEditable" @click="editInterview">Edit Assessment</button>
                        <button type="submit" class="btn btn-success btnSave" v-show="isEditable">Update Assessment</button>
                        <button type="button" class="btn btn-danger text-light btnCancel" v-show="isEditable" @click="cancelEditInterview">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>



<div class="modal fade" id="manpowerRequestModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-xxl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign To Manpower Request</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <table class="table table-striped table-bordered" width="100%" id="table-manpower_request">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Reference No</th>
                                    <th>MRF INFO</th>
                                    <th>ACtions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


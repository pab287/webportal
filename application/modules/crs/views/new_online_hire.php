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
                                CANDIDATE HIRE
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push">
                                 <button class="nav-link btn btn-info btn-sm btnSave" href="javascript:void(0)" aria-expanded="true" @click="hireToHr()">Hire To HRIS</button>
                            </li>
                        </ul>
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
                    </div>
                </div>
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


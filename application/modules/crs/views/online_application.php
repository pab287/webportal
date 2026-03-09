<style>
    .modal-xxl {
        max-width: 90%;
    }
</style>
<div class="m-content" id="m_content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                ONLINE APPLICATION
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="row m--margin-top-20 m--margin-bottom-30">
                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mb-2">
                            <div class="input-group">
                               <select name="year" id="year"></select>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                <span class="m-input-icon__icon m-input-icon__icon--left">
                                    <span>
                                        <i class="la la-search"></i>
                                    </span>
                                </span>
                                <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                            </div>
                        </div>
                    </div>
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
                        <table class="table table-hover table-striped table-bordered" id="candidates_table" width="100%">
                            <thead>
                            <tr >
                                <th>#</th>
                                <th style="white-space: nowrap;">Application Details</th>
                                <th>Applicant Info</th>
                                <!-- <th>School</th>
                                <th>Course</th> -->
                                <th>Desired Position</th>
                                <th>Attached Files</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="view-application-modal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-xxl">
            <div class="modal-content">
                <div class="modal-header"> 
                    <h5 class="modal-title">Application Information</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-tools" style="width: 100%;">
                                    <ul role="tablist" class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left m-tabs-line--primary" style="display: flex; justify-content: space-between; width: 100%;">
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
                                <div class="tab-content">
                                    <div id="personal_information" class="tab-pane active">
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
                                                    <input type="text" name="firstname" id="firstname" placeholder="ENTER FIRST NAME" class="form-control" autocomplete="off" :value="selectedApplication.firstname" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="middle" class="form-control-label">Middle name</label>
                                                    <input type="text" name="middlename" id="middle" placeholder="(optional)" class="form-control" autocomplete="off" :value="selectedApplication.middlename" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="lastname" class="form-control-label required">Last name</label>
                                                    <input type="text" name="lastname" id="lastname" placeholder="ENTER LAST NAME" class="form-control" autocomplete="off" :value="selectedApplication.lastname" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="suffix" class="form-control-label">Suffix</label>
                                                    <input type="text" name="suffix" id="suffix" placeholder="(optional)" class="form-control" autocomplete="off" :value="selectedApplication.suffix" readonly>
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
                                                    <input type="text" name="gender" id="gender" class="form-control" autocomplete="off" :value="selectedApplication.gender" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="civil_status" class="form-control-label required">Civil Status</label>
                                                    <input type="text" name="civil_status" id="civil_status" class="form-control" autocomplete="off" :value="selectedApplication.civil_status" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <label for="religion" class="form-control-label required">Religion</label>
                                                    <input type="text" name="religion" id="religion" placeholder="ENTER RELIGION" class="form-control" autocomplete="off" :value="selectedApplication.religion" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <label for="height" class="form-control-label">Height</label>
                                                    <input type="text" name="height" id="height" placeholder="(OPTIONAL)" class="form-control" autocomplete="off" :value="selectedApplication.height" readonly>
                                                    <span class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">* IN FEET AND INCHES *</span>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <label for="weight" class="form-control-label">Weight</label>
                                                    <input type="text" name="weight" id="weight" placeholder="(OPTIONAL)" class="form-control" autocomplete="off" :value="selectedApplication.weight" readonly>
                                                    <span class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">* IN KILOGRAMS *</span>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="birthdate" class="form-control-label required">Birthdate</label>
                                                    <div class="input-group date">
                                                        <input class="form-control m-input" type="text" id="birthdate" name="birthdate" placeholder="mm/dd/yyyy" maxlength="12" size="12" autocomplete="off" :value="formatDate(selectedApplication.birthdate)" readonly>
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
                                                    <input type="text" name="citizenship" id="citizenship" placeholder="ENTER CITIZENSHIP" class="form-control"  autocomplete="off" :value="selectedApplication.citizenship" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="contact_information" class="tab-pane">
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
                                                    <input type="text" name="contact_no" id="contact_no" placeholder="ENTER YOUR MOBILE NUMBER" class="form-control" autocomplete="off" :value="selectedApplication.contact_no" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="email" class="form-control-label">Email</label>
                                                    <input type="email" name="email" id="email" placeholder="(optional)" class="form-control" autocomplete="off" :value="selectedApplication.email" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="tel_no" class="form-control-label">Telephone Number</label>
                                                    <input type="text" name="tel_no" id="tel_no" placeholder="(optional)" class="form-control" autocomplete="off" :value="selectedApplication.tel_no" readonly>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="address" class="form-control-label required">Current Address</label>
                                                    <input type="text" name="address" id="address" placeholder="ENTER YOUR CURRENT ADDRESS" class="form-control"  autocomplete="off" :value="selectedApplication.address" readonly>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="permanent_address" class="form-control-label required">Permanent Address</label>
                                                    <input type="text" name="permanent_address" id="permanent_address" placeholder="ENTER YOUR PERMANENT ADDRESS" class="form-control"  autocomplete="off" :value="selectedApplication.permanent_address" readonly>
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
                                        <div class="row mt-2">
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="full_name_1" class="form-control-label required">Full Name</label>
                                                    <input id="full_name_1" name="references[0][ref_name]" placeholder="ENTER FULL NAME" type="text" maxlength="200" autocomplete="off"  class="form-control m-input" />
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="contact_no_1" class="form-control-label required">Contact Number</label>
                                                    <input id="contact_no_1" name="references[0][ref_contact_no]" placeholder="ENTER CONTACT NUMBER" type="text" maxlength="20" autocomplete="off"  class="form-control m-input" />
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="address_1" class="form-control-label required">Address</label>
                                                    <input id="address_1" name="references[0][ref_address]" placeholder="ENTER ADDRESS" type="text" maxlength="500" autocomplete="off"  class="form-control m-input" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="full_name_2" class="form-control-label required">Full Name</label>
                                                    <input id="full_name_2" name="references[1][ref_name]" placeholder="ENTER FULL NAME" type="text" maxlength="200" autocomplete="off"  class="form-control m-input" />
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="contact_no_2" class="form-control-label required">Contact Number</label>
                                                    <input id="contact_no_2" name="references[1][ref_contact_no]" placeholder="ENTER CONTACT NUMBER" type="text" maxlength="20" autocomplete="off"  class="form-control m-input" />
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="address_2" class="form-control-label required">Address</label>
                                                    <input id="address_2" name="references[1][ref_address]" placeholder="ENTER ADDRESS" type="text" maxlength="500" autocomplete="off"  class="form-control m-input" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="full_name_3" class="form-control-label required">Full Name</label>
                                                    <input id="full_name_3" name="references[2][ref_name]" placeholder="ENTER FULL NAME" type="text" maxlength="200" autocomplete="off"  class="form-control m-input" />
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="contact_no_3" class="form-control-label required">Contact Number</label>
                                                    <input id="contact_no_3" name="references[2][ref_contact_no]" placeholder="ENTER CONTACT NUMBER" type="text" maxlength="20" autocomplete="off"  class="form-control m-input" />
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="address_3" class="form-control-label required">Address</label>
                                                    <input id="address_3" name="references[2][ref_address]"  placeholder="ENTER ADDRESS" type="text" maxlength="500" autocomplete="off"  class="form-control m-input" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="work_experience" class="tab-pane">
                                        <div class="row mt-2">
                                            <div class="col">
                                                <div class="m-portlet__head-title">
                                                    <h5 class="m-portlet__head-text">WORK EXPERIENCE</h5>
                                                </div>
                                            </div>
                                        </div>
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
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label :for="'work_company_' + index" class="form-control-label required">Company</label>
                                                        <input :id="'work_company_' + index" name="work_company" type="text" maxlength="200" autocomplete="off"  class="form-control m-input" :value="work.company"/>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label :for="'work_position_' + index" class="form-control-label required">Position</label>
                                                        <input :id="'work_position_' + index" name="work_position" type="text" maxlength="200" autocomplete="off"  class="form-control m-input" :value="work.position"/>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label :for="'work_from_' + index" class="form-control-label required">From Year</label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                            <input :id="'work_from_' + index" name="work_from_year" type="text" maxlength="4" autocomplete="off"  class="form-control m-input" :value="work.from"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label :for="'work_status_' + index" class="form-control-label required">Status</label>
                                                        <input :id="'work_status_' + index" name="work_status" type="text" maxlength="50" autocomplete="off"  class="form-control m-input" :value="work.status"/>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label :for="'work_reason_' + index" class="form-control-label">Reason for leaving *</label>
                                                        <input :id="'work_reason_' + index" name="work_reason" type="text" maxlength="200" autocomplete="off"  class="form-control m-input" :value="work.reason"/>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label :for="'work_to_' + index" class="form-control-label required">To Year</label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                            <input :id="'work_to_' + index" name="work_to_year" type="text" maxlength="4" autocomplete="off"  class="form-control m-input" :value="work.to"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        </template>
                                    </div>
                                    <div id="educational_information" class="tab-pane">
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
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label :for="'level_' + index" class="form-control-label required">Level</label>
                                                        <input :id="'level_' + index" name="level" type="text" maxlength="200" autocomplete="off"  class="form-control m-input" :value="item.level"/>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label :for="'educ_school_' + index" class="form-control-label required">School</label>
                                                        <input :id="'educ_school_' + index" name="school" type="text" maxlength="200" autocomplete="off"  class="form-control m-input" :value="item.school"/>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label :for="'educ_from_' + index" class="form-control-label required">From Year</label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                        <input :id="'educ_from_' + index" name="educ_from_year" type="text" maxlength="4" autocomplete="off"  class="form-control m-input" :value="item.from"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label :for="'educ_degree_' + index" class="form-control-label required">Educational Degree</label>
                                                        <input :id="'educ_degree_' + index" name="educ_degree" type="text" maxlength="200" autocomplete="off"  class="form-control m-input" :value="item.degree"/>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label :for="'honor_' + index" class="form-control-label required">Honor</label>
                                                        <input :id="'honor_' + index" name="educ_honor" type="text" maxlength="200" autocomplete="off"  class="form-control m-input" :value="item.honor"/>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label :for="'educ_to_' + index" class="form-control-label required">To Year</label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                        <input :id="'educ_to_' + index" name="educ_to_year" type="text" maxlength="4" autocomplete="off"  class="form-control m-input" :value="item.to"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-12">
                                                    <button type="button" class="btn btn-success" v-on:click="addEducInfo">
                                                        <i class="la la-plus"></i> New
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div id="application_information" class="tab-pane">
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
                                                    <select id="recruitment" name="recruitment" class="form-control" >
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                                <div class="form-group">
                                                    <label for="position_id" class="form-control-label required">Position Applied FOR</label>
                                                    <select id="position_id" name="positions" multiple="multiple" class="form-control" >
                                                    </select>
                                                    <span class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">You can select multiple position.</span>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 referral d-none">
                                                <div class="form-group">
                                                    <label for="referral" class="form-control-label required">referral</label>
                                                    <select id="referral" name="referral" class="form-control"  >
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="applied_dt" class="form-control-label required">Date of Application</label>
                                                    <div class='input-group date'>
                                                        <input class="form-control m-input" type="text" id="applied_dt" name="applied_dt" placeholder="mm/dd/yyyy" maxlength="12" size="12"  autocomplete="off" readonly/>
                                                        <span class="input-group-addon">
                                                        <i class="la la-calendar glyphicon-th"></i>
                                                    </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="resume_upload" class="tab-pane">
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
                                                    <strong><h5>NO ATTACHMENTS</h5></strong>
                                                </div>
                                            </template>
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
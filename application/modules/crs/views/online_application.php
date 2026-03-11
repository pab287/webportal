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
                            <h3 class="m-portlet__head-text" id="page_title">
                                ONLINE APPLICATION MASTERFILE
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools btnArchive">
                        <a href="javascript:void(0)" onclick="openArchive()" class="custom-btn-link">
                            <span id="archive_text" class="m--font-bolder">Archive</span>
                        </a>
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
                                                    <span class="form-control-label mb-2 ">First name</span>
                                                    <div class="form-control" v-text="selectedApplication.firstname || '-'"></div>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <span  class="form-control-label mb-2">Middle name</span>
                                                    <div class="form-control" v-text="selectedApplication.middlename || '-'"></div>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <span class="form-control-label mb-2 ">Last name</span>
                                                    <div class="form-control" v-text="selectedApplication.lastname || '-'"></div>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <span class="form-control-label mb-2">Suffix</span>
                                                    <div class="form-control" v-text="selectedApplication.suffix || '-' "></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="row mt-2">
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <label for="school_id" class="form-control-label mb-2">School</label>
                                                    <select id="school_id" name="schools" multiple="multiple" class="form-control"></select>
                                                    <span class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">YOU CAN SELECT MULTIPLE SCHOOL</span>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <label for="course_id" class="form-control-label mb-2">Course</label>
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
                                                    <span class="form-control-label mb-2 ">Gender</span>
                                                    <div class="form-control" v-text="selectedApplication.gender || '-'"></div>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <span class="form-control-label mb-2 ">Civil Status</span>
                                                    <div class="form-control" v-text="selectedApplication.civil_status || '-'"></div>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <span class="form-control-label mb-2 ">Religion</span>
                                                    <div class="form-control" v-text="selectedApplication.religion || '-'"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <span class="form-control-label mb-2">Height</span>
                                                    <div class="form-control" v-text="selectedApplication.height || 'NOT AVAILABLE'"></div>
                                                    <span class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">* IN FEET AND INCHES *</span>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <span class="form-control-label mb-2">Weight</span>
                                                    <div class="form-control" v-text="selectedApplication.weight || 'NOT AVAILABLE'"></div>
                                                    <span class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">* IN KILOGRAMS *</span>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <span class="form-control-label mb-2 ">Birthdate</span>
                                                    <div class="input-group date">
                                                        <div class="form-control" v-text="formatDate(selectedApplication.birthdate) || '-' "></div>
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
                                                    <span class="form-control-label mb-2 ">Citizenship</span>
                                                    <div class="form-control" v-text="selectedApplication.citizenship || '-'"></div>
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
                                                    <span class="form-control-label mb-2">Mobile Number</span>
                                                    <div class="form-control" v-text="selectedApplication.contact_no || '-'"></div>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <span class="form-control-label mb-2">Email</label>
                                                    <div class="form-control" v-text="selectedApplication.email || '-'"></div>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <span for="tel_no" class="form-control-label mb-2">Telephone Number</span>
                                                    <div class="form-control" v-text="selectedApplication.tel_no || '-'"></div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <span class="form-control-label mb-2 ">Current Address</span>
                                                    <div class="form-control" v-text="selectedApplication.address || '-'"></div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <span class="form-control-label mb-2 ">Permanent Address</span>
                                                    <div class="form-control" v-text="selectedApplication.permanent_address || '-'"></div>
                                                </div>
                                            </div>
                                            <!-- <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-control-label mb-2">Map Coordinates</label>
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
                                        <template v-for="(reference, index) in selectedApplication.reference" :key="index">
                                            <div class="row mt-2">
                                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <span class="form-control-label mb-2">Full Name</span>
                                                        <div class="form-control" v-text="reference.ref_name || '-'"></div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <span class="form-control-label mb-2">Contact Number</span>
                                                        <div class="form-control" v-text="reference.ref_contact_no || '-'"></div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <span class="form-control-label mb-2">Address</span>
                                                        <div class="form-control" v-text="reference.ref_address || '-'"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div id="work_experience" class="tab-pane">
                                        <div class="row mt-2">
                                            <div class="col">
                                                <div class="m-portlet__head-title">
                                                    <h5 class="m-portlet__head-text">WORK EXPERIENCE</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <template v-for="(work, index) in selectedApplication.work_experience" :key="index">
                                            <div class="row mt-2">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <span class="form-control-label mb-2">Company</span>
                                                        <div class="form-control" v-text="work.work_company || '-'"></div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <span  class="form-control-label mb-2">Position</span>
                                                        <div class="form-control" v-text="work.work_position || '-'"></div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <span class="form-control-label mb-2">From Year</span>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                            <div class="form-control" v-text="work.work_from || '-'"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <span class="form-control-label mb-2">Status</span>
                                                        <div class="form-control" v-text="work.work_status || '-'"></div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <span class="form-control-label mb-2">Reason for leaving</span>
                                                        <div class="form-control" v-text="work.work_reason || '-'"></div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <span class="form-control-label mb-2 ">To Year</span>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                            <div class="form-control" v-text="work.work_to || '-'"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="m-separator m-separator--dashed d-xl-12"></div>
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
                                        <template v-for="(educ, index) in selectedApplication.education">
                                            <div class="row mt-2">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <span class="form-control-label mb-2">Level</span>
                                                        <div class="form-control" v-text="educ.educ_level_type || '-'"></div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <span class="form-control-label mb-2">Degree</span>
                                                        <div class="form-control" v-text="educ.educ_degree || '-'"></div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <span class="form-control-label mb-2 ">From Year</span>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                            <div class="form-control" v-text="educ.educ_from || '-'"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <span class="form-control-label mb-2">School</span>
                                                        <div class="form-control" v-text="educ.educ_school || '-'"></div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <span class="form-control-label mb-2">Honor</span>
                                                        <div class="form-control" v-text="educ.educ_honors || 'NOT AVAILABLE'"></div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <span class="form-control-label mb-2">To Year</span>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                            <div class="form-control" v-text="educ.educ_to || '-'"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="m-separator m-separator--dashed d-xl-12"></div>
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
                                                    <span for="recruitment" class="form-control-label mb-2">Recruitment Source</span>
                                                    <div class="form-control" v-text="selectedApplication.recruitment || '-'"></div>
                                                </div>
                                            </div>
                                            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                                <div class="form-group">
                                                    <span class="form-control-label mb-2">Position Applied FOR</span>
                                                    <div class="form-control" v-text="selectedApplication.positions && selectedApplication.positions.length ? selectedApplication.positions.join(', ') : '-'"></div>
                                                    <span class="m-form__help mb-0 pt-1 text-muted m--regular-font-size-sm1" style="text-transform: none;">You can select multiple position.</span>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 referral" v-show="selectedApplication.recruitment == 'referral'">
                                                <div class="form-group">
                                                    <span  class="form-control-label mb-2">referral name</span>
                                                    <div class="form-control" v-text="selectedApplication.referral_name || '-'"></div>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <span class="form-control-label mb-2">Date of Application</span>
                                                    <div class='input-group date'>
                                                        <div class="form-control" v-text="formatDate(selectedApplication.applied_dt) || '-'"></div>
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
                                                    <h5 class="m-portlet__head-text">RESUME</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="m-portlet__body">
                                            <iframe title="Resume" v-if="selectedApplication.resume" :src="selectedApplication.resume" width="100%" height="700"
                                                style="border: none;">
                                            </iframe>
                                            <div v-else class="text-center text-muted">
                                                No resume uploaded.
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
<?php 
$url = base_url(uri_string());
$str = explode("/", $url);    

?>
<input type="hidden" id="csrf_token" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
<input type="hidden" value="<?=$str[7]?>" id="accordion_emp_id">
<div class="m-portlet__body">
    <div class="form-group m-form__group row">
        <div class="col-12 ml-auto">
            <h4 class="m-form__header m-form__section">Other Information</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div id="accordionOtherAdditionalInfo" class="accordion" role="tablist" aria-multiselectable="true">
                <div class="card">
                    <div id="headingDependents"
                         class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm"
                         role="tab">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <a class="m-portlet__nav-link collapsed" data-toggle="collapse"
                                   data-parent="#accordionOtherAdditionalInfo" href="#collapseDependents"
                                   aria-expanded="false" aria-controls="collapseDependents">
                                    <h5 class="m-portlet__head-text">
                                        <span>Dependents</span>
                                        <i class="la pull-right la-angle-down"></i>
                                    </h5>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="collapseDependents" class="collapse show" role="tabpanel"
                         aria-labelledby="headingDependents" data-parent="#accordionOtherAdditionalInfo">
                        <div class="card-body m-portlet__body--custom table-responsive">
                            <table id="tbl-dependents_list" class="table display table-bordered table-striped"
                                   width="100%"></table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="headingEducationalBackground"
                         class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm"
                         role="tab">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <a class="m-portlet__nav-link collapsed" data-toggle="collapse"
                                   data-parent="#accordionOtherAdditionalInfo" href="#collapseEducationalBackground"
                                   aria-expanded="false" aria-controls="collapseEducationalBackground">
                                    <h5 class="m-portlet__head-text">
                                        <span>Educational Background</span>
                                        <i class="la pull-right la-angle-down"></i>
                                    </h5>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="collapseEducationalBackground" class="collapse" role="tabpanel"
                         aria-labelledby="headingEducationalBackground" data-parent="#accordionOtherAdditionalInfo">
                        <div class="card-body m-portlet__body--custom table-responsive">
                            <table id="tbl-educational_background_list"
                                   class="table display table-bordered table-striped" width="100%"></table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="headingLicensureExams"
                         class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm"
                         role="tab">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <a class="m-portlet__nav-link collapsed" data-toggle="collapse"
                                   data-parent="#accordionOtherAdditionalInfo" href="#collapseLicensureExams"
                                   aria-expanded="false" aria-controls="collapseLicensureExams">
                                    <h5 class="m-portlet__head-text">
                                        <!-- <span>Licensure exams and certifications</span> -->
                                        <span>Licenses and Certifications</span>
                                        <i class="la pull-right la-angle-down"></i>
                                    </h5>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="collapseLicensureExams" class="collapse" role="tabpanel"
                         aria-labelledby="headingLicensureExams" data-parent="#accordionOtherAdditionalInfo" style="">
                        <div class="card-body m-portlet__body--custom table-responsive">
                            <table id="tbl-licensure_exams_list" class="table display table-bordered table-striped"
                                   width="100%"></table>
                        </div>
                    </div>
                </div>
                <div class="card" id="DrLicenseCard">
                    <div id="headingDriversLicense"
                         class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm"
                         role="tab">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <a class="m-portlet__nav-link collapsed" data-toggle="collapse"
                                   data-parent="#accordionOtherAdditionalInfo" href="#collapseDriversLicense"
                                   aria-expanded="false" aria-controls="collapseDriversLicense">
                                    <h5 class="m-portlet__head-text">
                                        <span>Driver's License</span>
                                        <i class="la pull-right la-angle-down"></i>
                                    </h5>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="collapseDriversLicense" class="collapse" role="tabpanel"
                         aria-labelledby="headingDriversLicense" data-parent="#accordionOtherAdditionalInfo" style="">
                        <div class="card-body m-portlet__body--custom table-responsive">
                            <table id="tbl-driverlicense" class="table display table-bordered table-striped"
                                   width="100%"></table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="headingWorkExperiences"
                         class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm"
                         role="tab">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <a class="m-portlet__nav-link collapsed" data-toggle="collapse"
                                   data-parent="#accordionOtherAdditionalInfo" href="#collapseWorkExperiences"
                                   aria-expanded="false" aria-controls="collapseWorkExperiences">
                                    <h5 class="m-portlet__head-text">
                                        <span>Work Experiences</span>
                                        <i class="la pull-right la-angle-down"></i>
                                    </h5>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="collapseWorkExperiences" class="collapse" role="tabpanel"
                         aria-labelledby="headingWorkExperiences" data-parent="#accordionOtherAdditionalInfo">
                        <div class="card-body m-portlet__body--custom table-responsive">
                            <table id="tbl-work_experiences_list" class="table display table-bordered table-striped"
                                   width="100%"></table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="headingAwards"
                         class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm"
                         role="tab">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <a class="m-portlet__nav-link collapsed" data-toggle="collapse"
                                   data-parent="#accordionOtherAdditionalInfo" href="#collapseAwards"
                                   aria-expanded="false" aria-controls="collapseAwards">
                                    <h5 class="m-portlet__head-text">
                                        <span>Awards and Achievements</span>
                                        <i class="la pull-right la-angle-down"></i>
                                    </h5>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="collapseAwards" class="collapse" role="tabpanel" aria-labelledby="headingAwards"
                         data-parent="#accordionOtherAdditionalInfo">
                        <div class="card-body m-portlet__body--custom table-responsive">
                            <table id="tbl-awards_list" class="table display table-bordered table-striped"
                                   width="100%"></table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="headingOrganization"
                         class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm"
                         role="tab">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <a class="m-portlet__nav-link collapsed" data-toggle="collapse"
                                   data-parent="#accordionOtherAdditionalInfo" href="#collapseOrganization"
                                   aria-expanded="false" aria-controls="collapseOrganization">
                                    <h5 class="m-portlet__head-text">
                                        <span>Organization</span>
                                        <i class="la pull-right la-angle-down"></i>
                                    </h5>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="collapseOrganization" class="collapse" role="tabpanel"
                         aria-labelledby="headingOrganization" data-parent="#accordionOtherAdditionalInfo">
                        <div class="card-body m-portlet__body--custom table-responsive">
                            <table id="tbl-organizations_list" class="table display table-bordered table-striped"
                                   width="100%"></table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="headingTrainings"
                         class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm"
                         role="tab">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <a class="m-portlet__nav-link collapsed" data-toggle="collapse"
                                   data-parent="#accordionOtherAdditionalInfo" href="#collapseTrainings"
                                   aria-expanded="false" aria-controls="collapseTrainings">
                                    <h5 class="m-portlet__head-text">
                                        <span>Trainings and Seminars</span>
                                        <i class="la pull-right la-angle-down"></i>
                                    </h5>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="collapseTrainings" class="collapse" role="tabpanel" aria-labelledby="headingTrainings"
                         data-parent="#accordionOtherAdditionalInfo">
                        <div class="card-body m-portlet__body--custom table-responsive">
                            <table id="tbl-trainings_list" class="table display table-bordered table-striped"
                                   width="100%"></table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="headingPersonalReferences"
                         class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm"
                         role="tab">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <a class="m-portlet__nav-link collapsed" data-toggle="collapse"
                                   data-parent="#accordionOtherAdditionalInfo" href="#collapsePersonalReferences"
                                   aria-expanded="false" aria-controls="collapsePersonalReferences">
                                    <h5 class="m-portlet__head-text">
                                        <span>Personal References</span>
                                        <i class="la pull-right la-angle-down"></i>
                                    </h5>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="collapsePersonalReferences" class="collapse" role="tabpanel"
                         aria-labelledby="headingPersonalReferences" data-parent="#accordionOtherAdditionalInfo">
                        <div class="card-body m-portlet__body--custom table-responsive">
                            <table id="tbl-personal_references_list" class="table display table-bordered table-striped"
                                   width="100%"></table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="headingMedicalRecords"
                         class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm"
                         role="tab">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <a class="m-portlet__nav-link collapsed" data-toggle="collapse"
                                   data-parent="#accordionOtherAdditionalInfo" href="#collapseMedicalRecords"
                                   aria-expanded="false" aria-controls="collapseMedicalRecords">
                                    <h5 class="m-portlet__head-text">
                                        <span>Medical History/Records</span>
                                        <i class="la pull-right la-angle-down"></i>
                                    </h5>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="collapseMedicalRecords" class="collapse" role="tabpanel"
                         aria-labelledby="headingMedicalRecords" data-parent="#accordionOtherAdditionalInfo">
                        <div class="card-body m-portlet__body--custom table-responsive">
                            <table id="tbl-medical_records_list" class="table display table-bordered table-striped"
                                   width="100%"></table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="headingMedicalRecords"
                         class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm"
                         role="tab">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <a class="m-portlet__nav-link collapsed" data-toggle="collapse"
                                   data-parent="#accordionOtherAdditionalInfo" href="#collapseSkills"
                                   aria-expanded="false" aria-controls="collapseSkills">
                                    <h5 class="m-portlet__head-text">
                                        <span>Skills</span>
                                        <i class="la pull-right la-angle-down"></i>
                                    </h5>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="collapseSkills" class="collapse" role="tabpanel"
                         aria-labelledby="headingMedicalRecords" data-parent="#accordionOtherAdditionalInfo">
                        <div class="card-body m-portlet__body--custom table-responsive">
                            <table id="tbl-skills-list" class="table display table-bordered table-striped"
                                   width="100%"></table>
                        </div>
                    </div>

                    <div class="modal fade" tabindex="-1" role="dialog" id="add-skill-modal">
                        <form onsubmit="addSkill(this); return false;"
                              id="frm-add-skill">
                            <input type="hidden" name="csrf_token"
                                   value="<?= $this->security->get_csrf_hash() ?>">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="la la-plus mr-2"></i>Add Skill</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="">Skill</label>
                                            <input type="text" class="form-control"
                                                   data-validation="required" name="skills"
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary btnNew"><i class="la la-check mr-2"></i>Save</button>
                                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="modal fade" tabindex="-1" role="dialog" id="edit-skill-modal">
                        <form onsubmit="editSkill(this); return false;"
                              id="frm-edit-skill">
                            <input type="hidden" name="csrf_token"
                                   value="<?= $this->security->get_csrf_hash() ?>">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="la la-edit mr-2"></i>Edit Skill</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id">
                                        <div class="form-group">
                                            <label for="">Skill</label>
                                            <input type="text" class="form-control"
                                                   data-validation="required" name="skills"
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary btnNew"><i class="la la-check mr-2"></i>Save</button>
                                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
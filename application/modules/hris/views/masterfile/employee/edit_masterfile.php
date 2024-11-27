<?php
    $name = $this->core_layout->getDisplayName(
        array("lastname" => $data->lastname,
            "firstname" => $data->firstname,
            "middlename" => $data->middlename,
            "suffix" => $data->suffix));
?>

<style>
    .avatar-buttons-container {
        left: 0;
        right: 0;
        bottom: 0;
        height: 16%;
        padding: 10px;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: row;
        justify-content: center;
    }

    #modal-rehire .select2-container .select2-selection--single {
        height: auto !important;
        padding: 0;
    }

    #modal-rehire .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
    }

    #modal-rehire .select2-container .select2-selection--single .select2-selection__rendered {
        white-space: normal !important;
    }

    #modal-rehire .select2-container--default .select2-results > .select2-results__options {
        max-height: 500px;
    }

    #modal-rehire .select2-results__option {
        border-bottom: 1px solid #efefef;
    }
</style>

<div class="m-content">
    <div class="row">
        <div class="col-xl-3 col-lg-12 col-md-12">
            <div class="m-portlet">
                <div class="m-portlet__body">
                    <div id="left_pane-card" class="m-card-profile">
                        <div class="m--hide">
                            <button class="btn btn-primary m-btn m-btn--icon m-btn--icon-only m-btn--pill">
                                <i class="fa fa-arrow-left"></i>
                            </button>
                        </div>
                        <div class="m-card-profile__pic m-card-user__pic">
                            <div class="m-card-profile__pic-wrapper position-relative"
                                 style="overflow: hidden;">
                                <img id="image--holder" :src="left_pane.display_avatar" alt="">
                                <div class="position-absolute avatar-buttons-container"
                                     v-if="left_pane.display_avatar.indexOf('no_image.jpg') <= -1">
                                    <a href="" class="text-light m--font-boldest" onclick="removeProfilePicture(); return false;"
                                       data-trigger="focus"
                                       data-toggle="m-tooltip" data-placement="bottom" title=""
                                       data-skin="dark" data-delay='{"show":500, "hide":200}'
                                       data-original-title="Reset to Default.">Remove</a>
                                </div>
                            </div>
                        </div>
                        <div class="m-card-profile__details">
                            <span class="m-card-profile__name">
                                <?= $name['display_name_1'] ?>
                            </span>
                            <a href="javascript:void(0);" class="m-card-profile__email m-link" v-if="left_pane.display_email !==''">{{left_pane.display_email}}</a>
                            <div class="rating">
                                <div id="performance-rating" onclick="openPerformanceRatingRemarksModal()" style="margin-top: 8px;"></div>
                                <div id="performance-rating-description" style="margin-top: 4px;"
                                     class="text-muted m--font-boldest m--regular-font-size-sm2"></div>
                                <div id="performance-rating-rehire" style="margin-top: 4px;"
                                class="text-muted m--font-boldest m--regular-font-size-sm2"></div>
                                <div id="performance-rating-remarks" style="margin-top: 4px;"
                                class="text-muted m--font-boldest m--regular-font-size-sm2"></div>
                            </div>
                        </div>

                        <ul class="m-nav m-nav--hover-bg m-portlet-fit--sides">
                            <li class="m-nav__separator m-nav__separator--fit"></li>
                            <li class="m-nav__item">
                                <a class="m-nav__link btnUpload" href="javascript:void(0);" data-toggle="modal" data-target="#modalUpdatePhoto">
                                    <i class="m-nav__link-icon flaticon-profile-1"></i>
                                    <span class="m-nav__link-text">Update Profile Photo</span>
                                </a>
                            </li>
                            <li class="m-nav__item">
                                <a class="m-nav__link btnView"
                                   href="<?php echo base_url("hris/masterfile/view_employee_masterfile/{$data->id}"); ?>">
                                    <i class="m-nav__link-icon flaticon-profile"></i>
                                    <span class="m-nav__link-text">View Employee Profile</span>
                                </a>
                            </li>
                            <li class="m-nav__item">
                                <a class="m-nav__link btnBack" href="<?php echo base_url("hris/masterfile/employee"); ?>">
                                    <i class="m-nav__link-icon fa fa-arrow-left"></i>
                                    <span class="m-nav__link-text">Back to Employee List</span>
                                </a>
                            </li>
                        </ul>

                        <ul class="m-nav m-nav--hover-bg m-portlet-fit--sides m--hide"
                            id="rehire-button-container">
                            <li class="m-nav__separator m-nav__separator--fit my-3"></li>
                            <li class="m-nav__item">
                                <a class="m-nav__link btnUpdate btnRehire" data-toggle="modal" data-target="#modal-rehire"
                                   style="cursor:pointer;">
                                    <i class="m-nav__link-icon fa fa-rotate-left"></i>
                                    <span class="m-nav__link-text">Rehire Employee</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-12 col-md-12">
            <div class="m-portlet m-portlet--tabs">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left m-tabs-line--primary">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link active" id="personal_information" data-toggle="tab" href="#m_user_profile_tab_1" role="tab"
                                   aria-expanded="true">Personal Information</a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" id="additional_information" data-toggle="tab" href="#m_user_profile_tab_2" role="tab" aria-expanded="true">Additional
                                    Information</a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" id="employment_information" data-toggle="tab" href="#m_user_profile_tab_3" role="tab" aria-expanded="true">Employment
                                    Data</a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" id="payroll_information" data-toggle="tab" href="#m_user_profile_tab_4" role="tab" aria-expanded="true">Payroll Information</a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" onclick="loadDocuments()"
                                   data-toggle="tab" href="#m_user_profile_tab_5" role="tab" aria-expanded="true">UPLOADED FILES</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="m_user_profile_tab_1" aria-expanded="true">
                        <?php echo $this->load->view("hris/masterfile/employee/tabs/personal_information"); ?>
                    </div>
                    <div class="tab-pane" id="m_user_profile_tab_2" aria-expanded="true">
                        <?php echo $this->load->view("hris/masterfile/employee/tabs/additional_information"); ?>
                        <?php echo $this->load->view("hris/masterfile/employee/tabs/other_information/additional_content"); ?>
                    </div>
                    <div class="tab-pane" id="m_user_profile_tab_3" aria-expanded="true">
                        <?php echo $this->load->view("hris/masterfile/employee/tabs/employment_data"); ?>
                        <?php echo $this->load->view("hris/masterfile/employee/tabs/other_information/employment_content"); ?>
                    </div>
                    <div class="tab-pane" id="m_user_profile_tab_4" aria-expanded="true">
                        <?php echo $this->load->view("hris/masterfile/employee/tabs/payroll_information/payroll_content"); ?>
                    </div>
                    <div class="tab-pane" id="m_user_profile_tab_5" aria-expanded="true">
                        <?php echo $this->load->view("hris/masterfile/employee/tabs/documents"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalUpdatePhoto" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div id="modalUpdatePhotoContainer" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload Photo</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span class="btn btn-success fileinput-button">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>Select file</span>
                        <input type="file" id="fileupload" name="files">
                    </span>
                    <div id="progress" class="progress">
                        <div class="progress-bar progress-bar-success"></div>
                    </div>
                    <div id="files" class="files"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger modalClose" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTempContent" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div id="modalTempContainer" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Temp Title</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">test</div>
            </div>
        </div>
    </div>

    <div class="modal fade document-modal-container"
         data-keyboard="false" data-backdrop="static"
         modal-exempt-custom tabindex="-1"
         role="dialog"></div>

    <div class="modal fade" id="modal-rehire" tabindex="-1" role="dialog">
        <form action="" id="frm-rehire">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            Rehire Form
                        </h5>
                        <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rehire-application">Position</label>
                            <select name="application_id" id="rehire-application" data-validation="required"></select>
                        </div>

                        <input type="hidden" name="company_id">
                        <input type="hidden" name="department_id">
                        <input type="hidden" name="position">
                        <input type="hidden" name="needed">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

                        <div class="form-group pt-3">
                            <label for="txt-date-rehired">Date Hired</label>
                            <div class="input-group date" id="date-rehired">
                                <input type="text" class="form-control m-input" readonly=""
                                       name="date_rehired" id="txt-date-rehired" data-validation="required">
                                <span class="input-group-addon">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnUpdate"><i class="la la-check mr-2"></i>Save Changes</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Close</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    var ratingScale = <?=json_encode($rating_scale)?>;

    $(document).ready(function(){
        const htmlContent = `<i style='color: blue !important;' 
            class='fa fa-exclamation-circle ml-2' data-toggle='tooltip' 
            data-theme='dark' title='Changes have been made in this page. Do not forget to click SAVE!'></i>`;

        const personalInfo = $("#personal_information");
        const additionalInfo = $("#additional_information");
        const employmentInfo = $("#employment_information");
        const payrollInfo = $("#payroll_information");

        setTimeout(function () {
            $("#frmEditEmployeeData input[type=text], #frmEditEmployeeData select").change(function(){
                var change_notif = $("#change_personal_info").val(1);
                if(typeof personalInfo != "undefined" && personalInfo.length == 1){
                    if(change_notif = 1){
                        personalInfo.find("i").remove();
                        personalInfo.append(htmlContent);
                    }else{ personalInfo.find("i").remove(); }
                }
            });
    
            $("#frmEditAdditionalData input[type=text], #frmEditAdditionalData select").change(function(){
                var change_notif = $("#change_additional_info").val(1);
                if(typeof additionalInfo != "undefined" && additionalInfo.length == 1){
                    if(change_notif = 1){
                        additionalInfo.find("i").remove();
                        additionalInfo.append(htmlContent);
                    }else{ additionalInfo.find("i").remove(); }    
                }
            });
    
            $("#frmEditEmploymentData input[type=text], #frmEditEmploymentData select").change(function(){
                var change_notif = $("#change_employment_info").val(1);
                if(typeof employmentInfo != "undefined" && employmentInfo.length == 1){
                    if(change_notif = 1){
                        employmentInfo.find("i").remove();
                        employmentInfo.append(htmlContent);
                    }else{ employmentInfo.find("i").remove(); }    
                }   
            });
            $("#frmEditPayrollData input[type=text], #frmEditPayrollData select").change(function(){
                var change_notif = $("#change_payroll_info").val(1);
                if(typeof payrollInfo != "undefined" && payrollInfo.length == 1){
                    if(change_notif = 1){
                        payrollInfo.find("i").remove();
                        payrollInfo.append(htmlContent);
                    }else{ payrollInfo.find("i").remove(); }    
                }  
            });
        }, 3000);
    });
</script>

<?php
    $this->load->view('modals/add_performance_rating');
    $this->load->view('modals/update_performance_rating');
    $this->load->view('modals/performance_rating_remarks');
?>


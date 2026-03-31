<script src="<?php echo base_url('assets/plugins/fileupload/js/vendor/jquery.ui.widget.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/fileupload/js/jquery.iframe-transport.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/daterangepicker.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/fileupload/js/jquery.fileupload.js'); ?>"></script>
<script src="https://www.google.com/recaptcha/api.js"></script>

<?php
    setcookie ("public_user_id", rand(10,1000), time()+ (10 * 365 * 24 * 60 * 60));
    if(!isset($_COOKIE['public_user_id'])){
?>
    <script>location.reload();</script>
<?php
    }
?>

<style>
    /* progress steps */
    .signup-step-container{
        padding: 0px 0px;
        /* padding-bottom: 60px; */
    }
    .wizard .nav-tabs {
        position: relative;
        margin-bottom: 0;
        border-bottom-color: transparent;
    }
    .wizard > div.wizard-inner {
        position: relative;
        margin-bottom: 0px;
        text-align: center;
    }
    .connecting-line {
        height: 2px;
        background: #e0e0e0;
        position: absolute;
        width: 80%;
        margin: 0 auto;
        left: 0;
        right: 0;
        top: 40px;
        z-index: 1;
    }
    .wizard .nav-tabs > li.active > div, .wizard .nav-tabs > li.active > div:hover, .wizard .nav-tabs > li.active > div:focus {
        color: #555555;
        cursor: default;
        border: 0;
        border-bottom-color: transparent;
    }
    span.round-tab {
        width: 35px;
        height: 34px;
        line-height: 33px;
        top: 3px;
        display: inline-block;
        border-radius: 50%;
        background: #fff;
        z-index: 2;
        position: absolute;
        left: 0;
        text-align: center;
        font-size: 16px;
        color: #0e214b;
        font-weight: 500;
        border: 1px solid #ddd;
    }

    .disabled span.round-tab {
        /* width: 35px;
        height: 34px;
        line-height: 33px;
        top: 3px; */
    }

    span.round-tab i{
        color:#555555;
    }
    .wizard li.active span.round-tab {
        background: #0db02b;
        color: #fff;
        border-color: #0db02b;
        transform: scale(1.3);
    }
    .wizard li.active span.round-tab i{
        color: #5bc0de;
    }
    .wizard .nav-tabs > li.active > div i{
        color: #0db02b;
    }
    .wizard .nav-tabs > li > div.active i{
        color: #0db02b;
    }
    .wizard li div.active span.round-tab {
        background: #0db02b;
        color: #fff;
        border-color: #0db02b;
    }
    .wizard li.disabled.done span.round-tab {
        background: #36a3f7;
        color: #fff;
        border-color: #36a3f7;
    }
    .wizard li.disabled.done span.round-tab i{
        color: #36a3f7;
    }
    .wizard li.disabled span.round-tab {
        background: #c4c5d6;
        color: #fff;
        border-color: #c4c5d6;
    }
    .wizard li.disabled span.round-tab i{
        color: #c4c5d6;
    }
    .wizard .nav-tabs > li {
        width: 20%;
    }
    .wizard li:after {
        content: " ";
        position: absolute;
        left: 46%;
        opacity: 0;
        margin: 0 auto;
        bottom: 0px;
        border: 5px solid transparent;
        border-bottom-color: red;
        transition: 0.1s ease-in-out;
    }
    .wizard .nav-tabs > li div {
        width: 30px;
        height: 30px;
        margin: 20px auto;
        border-radius: 100%;
        padding: 0;
        background-color: transparent;
        position: relative;
        top: 0;
    }
    .wizard .nav-tabs > li div i{
        /* position: absolute;
        top: -15px;
        font-style: normal;
        font-weight: 400;
        white-space: nowrap;
        left: 50%;
        transform: translate(-20%, -50%);
        font-size: 12px;
        font-weight: 700;
        color: #000; */
        position: absolute;
        top: 60px;
        font-style: normal;
        font-weight: 400;
        white-space: nowrap;
        left: 0%;
        transform: translate(-40%, -50%);
        font-size: 13px;
        font-weight: 700;
        color: #000;
    }
    .wizard .nav-tabs > li div:hover {
        background: transparent;
    }
    .wizard .tab-pane {
        position: relative;
        padding-top: 20px;
    }
    .wizard h3 {
        margin-top: 0;
    }
    /* progress steps */

    /* form */
    .login-box{
        margin-top: 25px
    }
    .prev-step, .next-step, .skip-step, .final-btn{
        font-size: 13px;
        padding: 8px 24px;
        border: none;
        border-radius: 4px;
        margin-top: 10px;
    }
    .next-step{
        /* background-color: #0db02b; */
        background-color: #0d77b0;
    }
    .final-btn{
        background-color: #0db02b;
    }
    .skip-btn{
        background-color: #cec12d;
    }
    .step-head{
        font-size: 20px;
        text-align: center;
        font-weight: 500;
        margin-bottom: 20px;
    }
    .term-check{
        font-size: 14px;
        font-weight: 400;
    }
    .custom-file {
        position: relative;
        display: inline-block;
        width: 100%;
        height: 40px;
        margin-bottom: 0;
    }
    .custom-file-input {
        position: relative;
        z-index: 2;
        width: 100%;
        height: 40px;
        margin: 0;
        opacity: 0;
    }
    .custom-file-label {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1;
        height: 40px;
        padding: .375rem .75rem;
        font-weight: 400;
        line-height: 2;
        color: #495057;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: .25rem;
    }
    .custom-file-label::after {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        z-index: 3;
        display: block;
        height: 38px;
        padding: .375rem .75rem;
        line-height: 2;
        color: #495057;
        content: "Browse";
        background-color: #e9ecef;
        border-left: inherit;
        border-radius: 0 .25rem .25rem 0;
    }
    .footer-link{
        margin-top: 30px;
    }
    .all-info-container{
    }
    .list-content{
        margin-bottom: 10px;
    }
    .list-content a{
        padding: 10px 15px;
        width: 100%;
        display: inline-block;
        background-color: #f5f5f5;
        position: relative;
        color: #565656;
        font-weight: 400;
        border-radius: 4px;
    }
    .list-content a[aria-expanded="true"] i{
        transform: rotate(180deg);
    }
    .list-content a i{
        text-align: right;
        position: absolute;
        top: 15px;
        right: 10px;
        transition: 0.5s;
    }
    .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
        background-color: #fdfdfd;
    }
    .list-box{
        padding: 10px;
    }
    .signup-logo-header .logo_area{
        width: 200px;
    }
    .signup-logo-header .nav > li{
        padding: 0;
    }
    .signup-logo-header .header-flex{
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .list-inline li{
        display: inline-block;
    }
    .pull-right{
        float: right;
    }
    /*-----------custom-checkbox-----------*/
    /*----------Custom-Checkbox---------*/
    input[type="checkbox"]{
        position: relative;
        display: inline-block;
        margin-right: 5px;
    }
    input[type="checkbox"]::before,
    input[type="checkbox"]::after {
        position: absolute;
        content: "";
        display: inline-block;   
    }
    input[type="checkbox"]::before{
        height: 16px;
        width: 16px;
        border: 1px solid #999;
        left: 0px;
        top: 0px;
        background-color: #fff;
        border-radius: 2px;
    }
    input[type="checkbox"]::after{
        height: 5px;
        width: 9px;
        left: 4px;
        top: 4px;
    }
    input[type="checkbox"]:checked::after{
        content: "";
        border-left: 1px solid #fff;
        border-bottom: 1px solid #fff;
        transform: rotate(-45deg);
    }
    input[type="checkbox"]:checked::before{
        background-color: #18ba60;
        border-color: #18ba60;
    }
    #registration label{
        font-weight: 600;
        margin-bottom: 1px;
    }
    button{
        cursor: pointer
    }
    .help-block.form-error{
        color: rgb(185, 74, 72);
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #444;
        line-height: 10px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 26px;
        position: absolute;
        top: 15px !important;
        right: 1px;
        width: 20px;
    }
    /* .select2.select2-container.select2-container--default.select2-container--below{
        width: 274px !important
    } */
    #step1 .select2.select2-container.select2-container--default.select2-container--below {
        width: 377px !important;
    }
    .daterangepicker.ltr.show-calendar.opensright{
        z-index: 999999 !important;
    }

    #registration .file_label {
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
        padding: 5px 10px;
    }
    #registration .file_label {
        background-color: #0d6efd;
        color: white;
        padding: 0.5rem;
        border-radius: 0.3rem;
        cursor: pointer;
        /* margin-top: 1rem; */
    }
    .nav.nav-pills, .nav.nav-tabs {
        margin-bottom: 65px !important;
    }

    #step5 p{
        text-transform: uppercase;
    }

    .select2-container--default .select2-selection--single, .select2-container--default.select2-container--focus .select2-selection--multiple, .select2-container--default .select2-selection--multiple{
        border: 1px solid #ebedf2 !important;
    }
    .daterangepicker.ltr.show-calendar.opensright{
        display: none;
    }
    #new_regestration_preview .m-widget4__text{
        line-break: anywhere;
    }

    .m-mobile{
        display: none
    }

    @media screen and (max-width: 480px){
        .m-mobile{
            display: block;
        }

        .wizard .m-mobile.nav-tabs > li{
            width: 100%;    
        }
        
        .m-mobile.nav.nav-tabs li.disabled {
            display: none;
        }

        .wizard .nav-tabs > li div i {
            /* position: absolute; */
            /* top: -15px; */
            /* font-style: normal; */
            /* font-weight: 400; */
            /* white-space: nowrap; */
            /* left: 50%; */
            /* transform: translate(-20%, -50%); */
            /* font-size: 12px; */
            /* font-weight: 700; */
            /* color: #000; */
            position: absolute;
            top: 0px;
            font-style: normal;
            font-weight: 400;
            white-space: nowrap;
            left: 0%;
            transform: translate(-40%, -50%);
            font-size: 16px;
            font-weight: 700;
            color: #000;
        }

        .m-web.nav.nav-tabs li i {
            display: none;
        }

        .nav.nav-pills, .nav.nav-tabs {
            margin-bottom: 40px !important;
        }

        .m-mobile .nav-tabs li {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div id="registration" class="m-content">
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;"> 
        <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </symbol>
        <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
        </symbol>
        <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </symbol>
    </svg>
    <div class="container-fluid py-5 px-0">
        <div class="col-lg-12 pb-4">
            <h1 class="text-center font-weight-bold">GC&C CRS APPLICATION</h1>
        </div>
        <div class="col-lg-12">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Application Form
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <section class="signup-step-container">
                        <div class="row d-flex justify-content-center">
                            <div class="col-md-12">
                                <div class="wizard">
                                    <div class="wizard-inner">
                                        <div class="connecting-line"></div>
                                        <ul class="m-web nav nav-tabs" role="tablist">
                                            <li role="presentation" id="step1" class="active">
                                                <div data-toggle="m-tooltip" title="Step 1">
                                                    <span class="round-tab">1 </span> 
                                                    <i><span class="flaticon-file-1 mr-2"></span> Application Details</i>
                                                </div>
                                            </li>
                                            <li role="presentation" id="step2" class="disabled">
                                                <div data-toggle="m-tooltip" title="Step 2">
                                                    <span class="round-tab">2 </span> 
                                                    <i><span class="flaticon-user mr-2"></span> Personal Information</i>
                                                </div>
                                            </li>
                                            <li role="presentation" id="step3" class="disabled">
                                                <div data-toggle="m-tooltip" title="Step 3">
                                                    <span class="round-tab">3 </span> 
                                                    <i><span class="flaticon-add mr-2"></span> Additional Information</i>
                                                </div>
                                            </li>
                                            <li role="presentation" id="step4" class="disabled">
                                                <div data-toggle="m-tooltip" title="Step 4">
                                                    <span class="round-tab">4 </span> 
                                                    <i><span class="flaticon-book mr-2"></span> Other Information</i>
                                                </div>
                                            </li>
                                            <li role="presentation" id="step5" class="disabled">
                                                <div data-toggle="m-tooltip" title="Step 5">
                                                    <span class="round-tab">5 </span> 
                                                    <i><span class="flaticon-interface mr-2"></span> Review and Submit</i>
                                                </div>
                                            </li>
                                        </ul>
                                        <ul class="m-mobile nav nav-tabs" role="tablist">
                                            <li role="presentation" id="step1" class="active">
                                                <div data-toggle="m-tooltip" title="Step 1">
                                                    <i><span class="flaticon-file-1 mr-2"></span> Application Details</i>
                                                </div>
                                            </li>
                                            <li role="presentation" id="step2" class="disabled">
                                                <div data-toggle="m-tooltip" title="Step 2">
                                                    <i><span class="flaticon-user mr-2"></span> Personal Information</i>
                                                </div>
                                            </li>
                                            <li role="presentation" id="step3" class="disabled">
                                                <div data-toggle="m-tooltip" title="Step 3">
                                                    <i><span class="flaticon-add mr-2"></span> Additional Information</i>
                                                </div>
                                            </li>
                                            <li role="presentation" id="step4" class="disabled">
                                                <div data-toggle="m-tooltip" title="Step 4">
                                                    <i><span class="flaticon-book mr-2"></span> Other Information</i>
                                                </div>
                                            </li>
                                            <li role="presentation" id="step5" class="disabled">
                                                <div data-toggle="m-tooltip" title="Step 5">
                                                    <i><span class="flaticon-interface mr-2"></span> Review and Submit</i>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div id="login-box" class="login-box">
                                    <form id="crs-application" role="form" class="form-group" enctype="multipart/form-data">
                                        <div class="tab-content" id="main_form">
                                            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>" autocomplete="off">
                                            <input type="hidden" name="public_user_id" value="<?php echo $_COOKIE['public_user_id']; ?>">
                                            <template v-if="count > 0">
                                                <input type="hidden" name="document_body_id" :value="row.body_id ? row.body_id : 0">
                                                <input type="hidden" name="info_id" :value="row.info_id">
                                            </template>
                                            <div class="tab-pane active" role="tabpanel" id="step1">
                                                <div class="m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded">
                                                    <div class="m-portlet__head">
                                                        <div class="m-portlet__head-caption">
                                                            <div class="m-portlet__head-title">
                                                                <h3 class="m-portlet__head-text">
                                                                    <b>Application Details</b>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="m-portlet__body">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">First Name *</label> 
                                                                    <input class="form-control" type="text" name="app_firstname" placeholder="" data-validation="required"> 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Last Name *</label> 
                                                                    <input class="form-control" type="text" name="app_lastname" placeholder="" data-validation="required"> 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Middle Name</label> 
                                                                    <input class="form-control" type="text" name="app_middlename" placeholder="( Optional )"> 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Suffix</label> 
                                                                    <input class="form-control" type="text" name="app_suffix" placeholder="( Optional )"> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="required">Recruitment Source *</label> 
                                                                    <select id="Recruitment" name="recruitment" class="form-control" data-validation="required" autocomplete="off">
                                                                        <option value="">Select an Option</option>
                                                                        <option value="Mynimo">MYNIMO</option>
                                                                        <option value="Jobstreet">JOBSTREET</option>
                                                                        <option value="Facebook">FACEBOOK</option>
                                                                        <option value="Linkedin">LINKEDIN</option>
                                                                        <option value="REFERRAL">REFERRAL</option>
                                                                        <option value="INDEED">INDEED</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="required">School</label> 
                                                                    <br>
                                                                    <select id="School" name="schools[]" multiple="multiple" class="form-control">
                                                                        <option value=""></option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="required">Course</label> 
                                                                    <br>
                                                                    <select id="Course" name="courses[]" multiple="multiple" class="form-control">
                                                                        <option value=""></option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="required">Desired Position *</label> 
                                                                    <select id="Position" name="positions[]" multiple="multiple" class="form-control" data-validation="required" autocomplete="off">
                                                                        <option value=""></option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="required">Date of Application *</label> 
                                                                    <input id="dateOfApplication" name="applied_dt" type="text" class="form-control" data-validation="required" autocomplete="off">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Contact No. *</label> 
                                                                    <input class="form-control" type="text" name="contact_no" data-validation="required"> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row justify-content-end px-3">
                                                            <button type="submit" id="btn-step1" class="btn btn-success next-step">Next <i class="fa fa-arrow-right"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" role="tabpanel" id="step2">
                                                <div class="m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded">
                                                    <div class="m-portlet__head">
                                                        <div class="m-portlet__head-caption">
                                                            <div class="m-portlet__head-title">
                                                                <h3 class="m-portlet__head-text">
                                                                    <b>PERSONAL INFORMATION</b>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="m-portlet__body">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">First Name *</label> 
                                                                    <input class="form-control" type="hidden" name="firstname" :value="row.fname" data-validation="required"> 
                                                                    <input class="form-control" type="text" :value="strFormat(row.fname)"> 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Last Name *</label> 
                                                                    <input class="form-control" type="hidden" name="lastname" :value="row.lname" data-validation="required">
                                                                    <input class="form-control" type="text" :value="strFormat(row.lname)">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Middle Name</label> 
                                                                    <input class="form-control" type="hidden" name="middlename" :value="row.mname" placeholder="( Optional )"> 
                                                                    <input class="form-control" type="text" :value="strFormat(row.mname)">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Suffix</label> 
                                                                    <input class="form-control" type="hidden" name="suffix" :value="row.suff" placeholder="( Optional )"> 
                                                                    <input class="form-control" type="text" :value="strFormat(row.suff)">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Telephone No.</label> 
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon">
                                                                            <i class="la la-chain"></i>
                                                                        </span>
                                                                        <input type="text" name="telephone_no" :value="row.telephone_no" class="form-control m-input" maxlength="12" size="12" autocomplete="off" placeholder="( Optional )">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Mobile No.</label> 
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon">
                                                                            <i class="la la-chain"></i>
                                                                        </span>
                                                                        <input type="text" name="mobile_no" :value="row.mobile_no" class="form-control m-input" maxlength="12" size="12" autocomplete="off" placeholder="( Optional )">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="required">Email Address</label> 
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon">
                                                                            <i class="la la-envelope"></i>
                                                                        </span>
                                                                        <input type="text" name="email" class="form-control m-input" autocomplete="off" placeholder="( Optional )">
                                                                    </div>
                                                                    <small><span class="text-danger">Note:</span> We'll never share your email with anyone else</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="required">Current Address *</label> 
                                                                    <input class="form-control" type="text" name="curr_addr" placeholder="" data-validation="required"> 
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class="m-checkbox-inline">
																		<label class="m-checkbox" style="padding: 1px !important">
																			<input type="checkbox" name="same-to-permanent" true-value="1" false-value="0" id="same-to-permanent">
																			Same as Permanent Address
																			<span></span>
																		</label>
																	</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="required">Permanent Address *</label> 
                                                                    <input class="form-control" type="text" name="prov_addr" placeholder="" data-validation="required"> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="required">Gender *</label> 
                                                                    <div class="m-checkbox-inline">
                                                                        <label class="m-checkbox">
                                                                            <input id="gender" type="radio" name="gender" data-validation="required" value="Male" data-validation="required">
                                                                            Male<span></span>
                                                                        </label>
                                                                        <label class="m-checkbox">
                                                                            <input id="gender" type="radio" name="gender" data-validation="required" value="Female" data-validation="required">
                                                                            Female<span></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Civil Status *</label> 
                                                                    <select id="civil_stat" class="form-control select2" name="civil_stat" data-validation="required">
                                                                        <option value="">Select an Option</option>
                                                                        <option value="Single">Single</option>
                                                                        <option value="Married">Married</option>
                                                                        <option value="Separated">Separated</option>
                                                                        <option value="Divorced">Divorced</option>
                                                                        <option value="Annulled">Annulled</option>
                                                                        <option value="Widowed">Widowed</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Citizenship *</label> 
                                                                    <input class="form-control" type="text" name="citizenship" placeholder="" data-validation="required"> 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Religion *</label> 
                                                                    <input class="form-control" type="text" name="religion" placeholder="" data-validation="required"> 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Birth Date *</label> 
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon">
                                                                            <i class="la la-calendar"></i>
                                                                        </span>
                                                                        <input id="bday" type="text" name="bday" class="form-control m-input" maxlength="12" size="12" autocomplete="off" data-validation="required">
                                                                    </div>
                                                                    <small><span class="text-danger">Date Format:</span> YYYY-MM-DD</small>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <div class="form-group">
                                                                    <label class="required">Place of Birth *</label> 
                                                                    <input class="form-control" type="text" name="birthplace" placeholder="" data-validation="required"> 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="form-group">
                                                                    <label class="required">Languages *</label> 
                                                                    <input class="form-control" type="text" name="languages" placeholder="" data-validation="required"> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="required">Height *</label> 
                                                                    <input class="form-control" type="text" name="height" placeholder="" data-validation="required">
                                                                    <small><span class="text-danger">Format:</span> Feet Inches</small>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="required">Weight *</label> 
                                                                    <input class="form-control" type="text" name="weight" placeholder="" data-validation="required"> 
                                                                    <small><span class="text-danger">Format:</span> Kilograms</small>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Complexion *</label> 
                                                                    <input class="form-control" type="text" name="complexion" placeholder="" data-validation="required">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Hair Color *</label> 
                                                                    <input class="form-control" type="text" name="hair_color" placeholder="" data-validation="required"> 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Blood Type *</label> 
                                                                    <input class="form-control" type="text" name="bloodtype" placeholder="" data-validation="required"> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row justify-content-end px-3">
                                                            <button type="button" class="default-btn prev-step mr-2">Back</button>
                                                            <button type="submit" id="btn-step2" class="btn btn-success next-step">Next <i class="fa fa-arrow-right"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" role="tabpanel" id="step3">
                                                <div class="m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded">
                                                    <div class="m-portlet__head">
                                                        <div class="m-portlet__head-caption">
                                                            <div class="m-portlet__head-title">
                                                                <h3 class="m-portlet__head-text">
                                                                    <b>ADDITIONAL INFORMATION</b>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="m-portlet__body">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">TIN #</label> 
                                                                    <input class="form-control" type="text" id="tin_no" name="tin" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Tax Status</label> 
                                                                    <select id="tax_status" class="form-control m-input select2" name="tax_status" placeholder="Select an option">
                                                                        <option value="">Select an Option</option>
                                                                        <option value="S">Single</option>
                                                                        <option value="M">Married</option>
                                                                        <option value="S1">S1</option>
                                                                        <option value="S2">S2</option>
                                                                        <option value="S3">S3</option>
                                                                        <option value="S4">S4</option>
                                                                        <option value="M1">M1</option>
                                                                        <option value="M2">M2</option>
                                                                        <option value="M3">M3</option>
                                                                        <option value="M4">M4</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Philhealth #</label> 
                                                                    <input class="form-control" type="text" id="phealth_no" name="phil" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Pag-ibig #</label> 
                                                                    <input class="form-control" type="text" id="pagibig_no" name="hdmf" placeholder="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <!-- <div class="col-md-1">
                                                                <div class="form-group">
                                                                    <label class="required">UMID #</label> 
                                                                    <br>
                                                                    <div class="d-flex align-items-center" style="gap: 6px">
                                                                        <span class="m-switch m-switch--sm">
                                                                            <label>
                                                                                <input type="checkbox" id="umid_no" data-identifier="umid_no-detail" true-value="1" false-value="0"> <span class="m-0"></span>
                                                                            </label>
                                                                        </span>
                                                                        <span class="flaticon-exclamation" data-toggle="m-tooltip" title="Switch on only if typing UMID number."></span>
                                                                    </div>
                                                                </div>
                                                            </div> -->
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">SSS #</label> 
                                                                    <input class="form-control" type="text" name="sss" id="sss_no" placeholder="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row mt-4 mb-2">
                                                            <div class="col-md-12">
                                                                <h5 style="color: #575962">Father's Details</h5>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Full Name</label> 
                                                                    <input class="form-control" type="text" name="father_name" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Contact No.</label> 
                                                                    <input class="form-control" type="text" name="father_no" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="required">Current Address</label> 
                                                                    <input class="form-control" type="text" name="father_address" placeholder="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="required">Company</label> 
                                                                    <input class="form-control" type="text" name="father_company" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="required">Occupation</label> 
                                                                    <input class="form-control" type="text" name="father_occupation" placeholder="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-1">
                                                                <div class="form-group">
                                                                    <label class="required">Deceased</label> 
                                                                    <br>
                                                                    <span class="m-switch m-switch--sm">
                                                                        <label>
                                                                            <input type="checkbox" id="father_deceased" name="father_deceased" data-identifier="father_is_deceased" true-value="1" false-value="0"> 
                                                                            <span></span>
                                                                        </label>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row mt-4 mb-2">
                                                            <div class="col-md-12">
                                                                <h5 style="color: #575962">Mother's Details</h5>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Full Name</label> 
                                                                    <input class="form-control" type="text" name="mother_name" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Contact No.</label> 
                                                                    <input class="form-control" type="text" name="mother_no" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="required">Current Address</label> 
                                                                    <input class="form-control" type="text" name="mother_address" placeholder="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="required">Company</label> 
                                                                    <input class="form-control" type="text" name="mother_company" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="required">Occupation</label> 
                                                                    <input class="form-control" type="text" name="mother_occupation" placeholder="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-1">
                                                                <div class="form-group">
                                                                    <label class="required">Deceased</label> 
                                                                    <br>
                                                                    <span class="m-switch m-switch--sm">
                                                                        <label>
                                                                            <input type="checkbox" id="mother_deceased" name="mother_deceased" data-identifier="mother_is_deceased" true-value="1" false-value="0"> 
                                                                            <span></span>
                                                                        </label>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row mt-4 mb-2">
                                                            <div class="col-md-12">
                                                                <h5 style="color: #575962">Partner's Details</h5>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <div class="m-checkbox-inline">
                                                                    <label class="m-checkbox">
                                                                        <input id="pt_married" type="radio" name="partner_type" data-identifier="has--partners_detail" value="1" 
                                                                        class="m-input--partners_detail m--partner_switch">Married<span></span>
                                                                    </label> 
                                                                    <label class="m-checkbox">
                                                                        <input id="pt_partner" type="radio" name="partner_type" data-identifier="has--partners_detail" value="2" 
                                                                        class="m-input--partners_detail m--partner_switch"> Partner<span></span>
                                                                    </label> 
                                                                    <label class="m-checkbox">
                                                                        <input id="pt_single" type="radio" name="partner_type" data-identifier="has--partners_detail" value="0" 
                                                                        class="m-input--partners_detail m--partner_switch">Single<span></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Full Name</label> 
                                                                    <input class="form-control" type="text" name="partner_name" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Contact No.</label> 
                                                                    <input class="form-control" type="text" name="partner_no" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="required">Current Address</label> 
                                                                    <input class="form-control" type="text" name="partner_address" placeholder="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="required">Company</label> 
                                                                    <input class="form-control" type="text" name="partner_company" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="required">Occupation</label> 
                                                                    <input class="form-control" type="text" name="partner_occupation" placeholder="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-1">
                                                                <div class="form-group">
                                                                    <label class="required">Deceased</label> 
                                                                    <br>
                                                                    <span class="m-switch m-switch--sm">
                                                                        <label>
                                                                            <input type="checkbox" id="partner_deceased" name="partner_deceased" data-identifier="partner_is_deceased" true-value="1" false-value="0"> 
                                                                            <span></span>
                                                                        </label>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row mt-4 mb-2">
                                                            <div class="col-md-12">
                                                                <h5 style="color: #575962">Emergency Details</h5>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Contact Person *</label> 
                                                                    <input class="form-control" type="text" name="emer_name" placeholder="" data-validation="required">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="required">Contact No. *</label> 
                                                                    <input class="form-control" type="text" name="emer_contact" placeholder="" data-validation="required">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="required">Address *</label> 
                                                                    <input class="form-control" type="text" name="emer_addr" placeholder="" data-validation="required">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row justify-content-end px-3">
                                                            <button type="button" class="default-btn prev-step mr-2">Back</button>
                                                            <!-- <button type="button" class="btn btn-warning skip-step skip-btn mr-2">Skip</button> -->
                                                            <button type="submit" id="btn-step3" class="btn btn-success next-step">Next</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" role="tabpanel" id="step4">
                                                <div class="m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded">
                                                    <div class="m-portlet__head">
                                                        <div class="m-portlet__head-caption">
                                                            <div class="m-portlet__head-title">
                                                                <h3 class="m-portlet__head-text">
                                                                    <b>OTHER INFORMATION</b>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="m-portlet__body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                    <div class="m-portlet__head">
                                                                        <div class="m-portlet__head-caption">
                                                                            <div class="m-portlet__head-title">
                                                                                <h3 class="m-portlet__head-text">
                                                                                    Dependents
                                                                                </h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="m-portlet__head-tools">
                                                                            <button type="button" class="btn btn-sm btn-success mb-2 btnNew btnDependents"><i class="la la-plus mr-1"></i>Add</button>
                                                                        </div>                                                                            
                                                                    </div>
                                                                    <div class="m-portlet__body">
                                                                        <table width="100%" class="table" id="tbl-dependents_list">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Name</th>
                                                                                    <th>Age</th>
                                                                                    <th>Relation</th>
                                                                                    <th>Birth Date</th>
                                                                                    <th>Action</th>
                                                                                </tr>
                                                                            </thead>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                    <div class="m-portlet__head">
                                                                        <div class="m-portlet__head-caption">
                                                                            <div class="m-portlet__head-title">
                                                                                <h3 class="m-portlet__head-text">
                                                                                    Educational Background
                                                                                </h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="m-portlet__head-tools">
                                                                            <button type="button" class="btn btn-sm btn-success mb-2 btnNew btnAddEducation"><i class="la la-plus mr-1"></i>Add</button>
                                                                        </div>                                                                            
                                                                    </div>
                                                                    <div class="m-portlet__body">
                                                                        <table width="100%" class="table" id="tbl-educational_background_list">
                                                                            <thead>
                                                                                <th>Level</th>
                                                                                <th>Degree</th>
                                                                                <th>School</th>
                                                                                <th>Honors</th>
                                                                                <th>From Date</th>
                                                                                <th>To Date</th>
                                                                                <th>Action</th>
                                                                            </thead>
                                                                            <tbody></tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                    <div class="m-portlet__head">
                                                                        <div class="m-portlet__head-caption">
                                                                            <div class="m-portlet__head-title">
                                                                                <h3 class="m-portlet__head-text">
                                                                                    Licensure Exams and Certifications
                                                                                </h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="m-portlet__head-tools">
                                                                            <button type="button" class="btn btn-sm btn-success mb-2 btnNew btnAddLicense"><i class="la la-plus mr-1"></i>Add</button>
                                                                        </div>                                                                            
                                                                    </div>
                                                                    <div class="m-portlet__body">
                                                                        <table width="100%" class="table" id="tbl-licensure_exams_list">
                                                                            <thead>
                                                                                <th>License/Exam Type</th>
                                                                                <th>Exam Place</th>
                                                                                <th>Rating</th>
                                                                                <th>Released Date</th>
                                                                                <th>Exam Date</th>
                                                                                <th>License No</th>
                                                                                <th>Action</th>
                                                                            </thead>
                                                                            <tbody></tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                    <div class="m-portlet__head">
                                                                        <div class="m-portlet__head-caption">
                                                                            <div class="m-portlet__head-title">
                                                                                <h3 class="m-portlet__head-text">
                                                                                    Driver's License
                                                                                </h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="m-portlet__head-tools">
                                                                            <button type="button" class="btn btn-sm btn-success mb-2 btnNew btnAddDL"><i class="la la-plus mr-1"></i>Add</button>
                                                                        </div>                                                                            
                                                                    </div>
                                                                    <div class="m-portlet__body">
                                                                        <table width="100%" class="table" id="tbl-driverlicense">
                                                                            <thead>
                                                                                <th>Restriction</th>
                                                                                <th>License No</th>
                                                                                <th>Exp Date</th>
                                                                                <th>Action</th>
                                                                            </thead>
                                                                            <tbody></tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                    <div class="m-portlet__head">
                                                                        <div class="m-portlet__head-caption">
                                                                            <div class="m-portlet__head-title">
                                                                                <h3 class="m-portlet__head-text">
                                                                                    Work Experiences
                                                                                </h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="m-portlet__head-tools">
                                                                            <button type="button" class="btn btn-sm btn-success mb-2 btnNew btnAddWork"><i class="la la-plus mr-1"></i>Add</button>
                                                                        </div>                                                                            
                                                                    </div>
                                                                    <div class="m-portlet__body">
                                                                        <table width="100%" class="table" id="tbl-work_experiences_list">
                                                                            <thead>
                                                                                <th>Company</th>
                                                                                <th>From</th>
                                                                                <th>To</th>
                                                                                <th>Position</th>
                                                                                <!-- <th>ID No</th> -->
                                                                                <th>Status</th>
                                                                                <th>Reason For Leaving</th>
                                                                                <th>Action</th>
                                                                            </thead>
                                                                            <tbody></tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                    <div class="m-portlet__head">
                                                                        <div class="m-portlet__head-caption">
                                                                            <div class="m-portlet__head-title">
                                                                                <h3 class="m-portlet__head-text">
                                                                                    Awards and Achievements
                                                                                </h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="m-portlet__head-tools">
                                                                            <button type="button" class="btn btn-sm btn-success mb-2 btnNew btnAddAward"><i class="la la-plus mr-1"></i>Add</button>
                                                                        </div>                                                                            
                                                                    </div>
                                                                    <div class="m-portlet__body">
                                                                        <table width="100%" class="table" id="tbl-awards_list">
                                                                            <thead>
                                                                                <th>Awards/Achievement</th>
                                                                                <th>Institution</th>
                                                                                <th>Given Date</th>
                                                                                <th>Action</th>
                                                                            </thead>
                                                                            <tbody></tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                    <div class="m-portlet__head">
                                                                        <div class="m-portlet__head-caption">
                                                                            <div class="m-portlet__head-title">
                                                                                <h3 class="m-portlet__head-text">
                                                                                    Organization
                                                                                </h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="m-portlet__head-tools">
                                                                            <button type="button" class="btn btn-sm btn-success mb-2 btnNew btnAddOrg"><i class="la la-plus mr-1"></i>Add</button>
                                                                        </div>                                                                            
                                                                    </div>
                                                                    <div class="m-portlet__body">
                                                                        <table width="100%" class="table" id="tbl-organizations_list">
                                                                            <thead>
                                                                                <th>Institution</th>
                                                                                <th>Membership Title</th>
                                                                                <th>From</th>
                                                                                <th>To</th>
                                                                                <th>Action</th>
                                                                            </thead>
                                                                            <tbody></tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                    <div class="m-portlet__head">
                                                                        <div class="m-portlet__head-caption">
                                                                            <div class="m-portlet__head-title">
                                                                                <h3 class="m-portlet__head-text">
                                                                                    Training and Seminars
                                                                                </h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="m-portlet__head-tools">
                                                                            <button type="button" class="btn btn-sm btn-success mb-2 btnNew btnAddTraining"><i class="la la-plus mr-1"></i>Add</button>
                                                                        </div>                                                                            
                                                                    </div>
                                                                    <div class="m-portlet__body">
                                                                        <table width="100%" class="table" id="tbl-trainings_list">
                                                                            <thead>
                                                                                <th>Training</th>
                                                                                <th>From</th>
                                                                                <th>To</th>
                                                                                <th>Institution</th>
                                                                                <th>Conductor</th>
                                                                                <th>Venue</th>
                                                                                <th>Action</th>
                                                                            </thead>
                                                                            <tbody></tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                    <div class="m-portlet__head">
                                                                        <div class="m-portlet__head-caption">
                                                                            <div class="m-portlet__head-title">
                                                                                <h3 class="m-portlet__head-text">
                                                                                    Personal References
                                                                                </h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="m-portlet__head-tools">
                                                                            <button type="button" class="btn btn-sm btn-success mb-2 btnNew btnAddPersonal"><i class="la la-plus mr-1"></i>Add</button>
                                                                        </div>                                                                            
                                                                    </div>
                                                                    <div class="m-portlet__body">
                                                                        <table width="100%" class="table" id="tbl-personal_references_list">
                                                                            <thead>
                                                                                <th>Name</th>
                                                                                <th>Contact No.</th>
                                                                                <th>Address</th>
                                                                                <th>Action</th>
                                                                            </thead>
                                                                            <tbody></tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                    <div class="m-portlet__head">
                                                                        <div class="m-portlet__head-caption">
                                                                            <div class="m-portlet__head-title">
                                                                                <h3 class="m-portlet__head-text">
                                                                                    Medical History/Records
                                                                                </h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="m-portlet__head-tools">
                                                                            <button type="button" class="btn btn-sm btn-success mb-2 btnNew btnAddMedical"><i class="la la-plus mr-1"></i>Add</button>
                                                                        </div>                                                                            
                                                                    </div>
                                                                    <div class="m-portlet__body">
                                                                        <table width="100%" class="table" id="tbl-medical_records_list">
                                                                            <thead>
                                                                                <th>Details</th>
                                                                                <th>Med. No.</th>
                                                                                <th>Date</th>
                                                                                <th>Venue</th>
                                                                                <th>Physician</th>
                                                                                <th>Findings</th>
                                                                                <th>Remarks</th>
                                                                                <th>Action</th>
                                                                            </thead>
                                                                            <tbody></tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                    <div class="m-portlet__head">
                                                                        <div class="m-portlet__head-caption">
                                                                            <div class="m-portlet__head-title">
                                                                                <h3 class="m-portlet__head-text">
                                                                                    Skills
                                                                                </h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="m-portlet__head-tools">
                                                                            <button type="button" class="btn btn-sm btn-success mb-2 btnNew btnAddSkills"><i class="la la-plus mr-1"></i>Add</button>
                                                                        </div>                                                                            
                                                                    </div>
                                                                    <div class="m-portlet__body">
                                                                        <table width="100%" class="table" id="tbl-skills-list">
                                                                            <thead>
                                                                                <th>Skills</th>
                                                                                <th>Action</th>
                                                                            </thead>
                                                                            <tbody></tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row justify-content-end px-3">
                                                            <button type="button" class="default-btn prev-step mr-2">Back</button>
                                                            <!-- <button type="button" class="btn btn-warning next-step skip-btn mr-2">Skip</button> -->
                                                            <button type="button" class="btn btn-success next-step" href="#step5">Next</button>
                                                        </div>
                                                        <div class="row justify-content-end px-3">
                                                            <!-- <span class="text-danger">Note:</span> You can go to the next step even no information given. -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </form>
                                    <!-- cutted to seperated saving of uploaded resume -->
                                            <div class="tab-pane" role="tabpanel" id="step5">
                                                <div class="m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded">
                                                    <div class="m-portlet__head">
                                                        <div class="m-portlet__head-caption">
                                                            <div class="m-portlet__head-title">
                                                                <h3 class="m-portlet__head-text">
                                                                    <b>REVIEW YOUR INFORMATIONS</b>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="m-portlet__body">
                                                        <template v-if="count > 0">
                                                            <div class="application-details">
                                                                <div class="row mt-4 mb-2">
                                                                    <div class="col-md-12">
                                                                        <h5 style="color: #575962">Application Details</h5>
                                                                    </div>
                                                                </div>
                                                                <!-- <div class="row">
                                                                    <div class="col-md-3">
                                                                        <label for="">Firstname</label>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="">Lastname</label>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="">Middlename</label>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="">Suffix</label>
                                                                    </div>
                                                                </div> -->
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <label for="">Recruitment Source</label>
                                                                        <p>{{ row.recruitment }} </p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">School</label>
                                                                        <p>{{ removeComma(row.school) }} </p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Course</label>
                                                                        <p>{{ removeComma(row.course) }} </p>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <label for="">Desired Position</label>
                                                                        <p>{{ removeComma(row.position) }} </p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Date of Application</label>
                                                                        <p>{{ row.applied_dt }} </p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Contact No.</label>
                                                                        <p>{{ row.contact_no }} </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="personal-information">
                                                                <div class="row mt-4 mb-2">
                                                                    <div class="col-md-12">
                                                                        <h5 style="color: #575962">Personal Information</h5>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <label for="">First Name</label>
                                                                        <p>{{ strFormat(row.fname) }}</p>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="">Last Name</label>
                                                                        <p>{{ strFormat(row.lname) }}</p>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="">Middle Name</label>
                                                                        <p>{{ strFormat(row.mname) ? strFormat(row.mname) : "---" }}</p>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="">Suffix</label>
                                                                        <p>{{ strFormat(row.suff) ? strFormat(row.suff) : "---" }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <label for="">Telephone No.</label>
                                                                        <p>{{ strFormat(row.telephone_no) ? strFormat(row.telephone_no) : "---" }}</p>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="">Mobile No.</label>
                                                                        <p>{{ strFormat(row.mobile_no) ? strFormat(row.mobile_no) : "---" }}</p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Email Address</label>
                                                                        <p>{{ strFormat(row.email) ? strFormat(row.email) : "---" }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-5">
                                                                        <label for="">Current Address</label>
                                                                        <p>{{ strFormat(row.curr_addr) ? strFormat(row.curr_addr) : "---" }}</p>
                                                                    </div>
                                                                    <div class="col-md-5">
                                                                        <label for="">Permanent Address</label>
                                                                        <p>{{ strFormat(row.prov_addr) ? strFormat(row.prov_addr) : "---" }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <label for="">Gender</label>
                                                                        <p>{{ row.gender ? row.gender : "---" }}</p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Civil Status</label>
                                                                        <p>{{ row.civil_stat ? row.civil_stat : "---" }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-5">
                                                                        <label for="">Place of Birth</label>
                                                                        <p>{{ strFormat(row.birthplace) ? strFormat(row.birthplace) : "---" }}</p>
                                                                    </div>
                                                                    <div class="col-md-5">
                                                                        <label for="">Languages</label>
                                                                        <p>{{ strFormat(row.languages) ? strFormat(row.languages) : "---" }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <label for="">Height</label>
                                                                        <p>{{ row.height ? row.height : "---" }}</p>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="">Weight</label>
                                                                        <p>{{ row.weight ? row.weight : "---" }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <label for="">Complexion</label>
                                                                        <p>{{ strFormat(row.complexion) ? strFormat(row.complexion) : "---" }}</p>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="">Hair Color</label>
                                                                        <p>{{ strFormat(row.hair_color) ? strFormat(row.hair_color) : "---" }}</p>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="">Blood Type</label>
                                                                        <p>{{ strFormat(row.bloodtype) ? strFormat(row.bloodtype) : "---" }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="additional-information">
                                                                <div class="row mt-4 mb-2">
                                                                    <div class="col-md-12">
                                                                        <h5 style="color: #575962">Additional Information</h5>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-2">
                                                                        <label for="">TIN #</label>
                                                                        <p>{{ row.tin_no ? row.tin_no : '---' }}</p>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label for="">Tax Status</label>
                                                                        <p>{{ row.tax_status ? row.tax_status : '---' }}</p>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label for="">Philhealth #</label>
                                                                        <p>{{ row.phealth_no ? row.phealth_no : '---' }}</p>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label for="">Pag Ibig #</label>
                                                                        <p>{{ row.pagibig_no ? row.pagibig_no : '---' }}</p>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label for="">SSS #</label>
                                                                        <p>{{ row.sss_no ? row.sss_no : '---' }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <div class="card" style="height: 100%">
                                                                            <div class="card-header">
                                                                                <h5 class="m-0" style="color: #575962">Father's Details</h5>
                                                                            </div>
                                                                            <div class="card-body">
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Full Name:</label>
                                                                                        <p>{{ strFormat(row.fat_name) ? strFormat(row.fat_name) : "---" }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Contact No.:</label>
                                                                                        <p>{{ row.fat_contact ? row.fat_contact : "---" }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Current Address:</label>
                                                                                        <p>{{ strFormat(row.fat_addr) ? strFormat(row.fat_addr) : "---" }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Company:</label>
                                                                                        <p>{{ strFormat(row.fat_company) ? strFormat(row.fat_company) : "---" }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Occupation:</label>
                                                                                        <p>{{ strFormat(row.fat_occupation) ? strFormat(row.fat_occupation) : "---" }}</p>
                                                                                    </div>
                                                                                    <template v-if="row.fat_deceased == 1">
                                                                                        <div class="col-md-12">
                                                                                            <p><b>Deceased</b></p>
                                                                                        </div>
                                                                                    </template>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="card" style="height: 100%">
                                                                            <div class="card-header">
                                                                                <h5 class="m-0" style="color: #575962">Mother's Details</h5>
                                                                            </div>
                                                                            <div class="card-body">
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Full Name:</label>
                                                                                        <p>{{ strFormat(row.mot_name) ? strFormat(row.mot_name) : "---" }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Contact No.:</label>
                                                                                        <p>{{ row.mot_contact ? row.mot_contact : "---" }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Current Address:</label>
                                                                                        <p>{{ strFormat(row.mot_addr) ? strFormat(row.mot_addr) : "---" }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Company:</label>
                                                                                        <p>{{ strFormat(row.mot_company) ? strFormat(row.mot_company) : "---" }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Occupation:</label>
                                                                                        <p>{{ strFormat(row.mot_occupation) ? strFormat(row.mot_occupation) : "---" }}</p>
                                                                                    </div>
                                                                                    <template v-if="row.mot_deceased == 1">
                                                                                        <div class="col-md-12">
                                                                                            <p><b>Deceased</b></p>
                                                                                        </div>
                                                                                    </template>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="card" style="height: 100%">
                                                                            <div class="card-header">
                                                                                <h5 class="m-0" style="color: #575962">Partner's Details</h5>
                                                                            </div>
                                                                            <div class="card-body">
                                                                                <div class="row">
                                                                                    <!-- <div class="col-md-12">
                                                                                        <b><template v-if="row.partner_type == 1"><p>Married</p></template>
                                                                                        <template v-else-if="row.partner_type == 2"><p>Partner</p></template>
                                                                                        <template v-else><p>Single</p></template></b>
                                                                                    </div> -->
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Full Name:</label>
                                                                                        <p>{{ strFormat(row.partner_name) ? strFormat(row.partner_name) : "---" }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Contact No.:</label>
                                                                                        <p>{{ row.mpartner_contact ? row.partner_contact : "---" }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Current Address:</label>
                                                                                        <p>{{ strFormat(row.partner_addr) ? strFormat(row.partner_addr) : "---" }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Company:</label>
                                                                                        <p>{{ strFormat(row.partner_company) ? strFormat(row.partner_company) : "---" }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Occupation:</label>
                                                                                        <p>{{ strFormat(row.partner_occupation) ? strFormat(row.partner_occupation) : "---" }}</p>
                                                                                    </div>
                                                                                    <template v-if="row.partner_deceased == 1">
                                                                                        <div class="col-md-12">
                                                                                            <p><b>Deceased</b></p>
                                                                                        </div>
                                                                                    </template>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="card" style="height: 100%">
                                                                            <div class="card-header">
                                                                                <h5 class="m-0" style="color: #575962">Emergency Details</h5>
                                                                            </div>
                                                                            <div class="card-body">
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Contact Person:</label>
                                                                                        <p>{{ strFormat(row.emer_name) ? strFormat(row.emer_name) : "---" }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Contact No.:</label>
                                                                                        <p>{{ row.emer_contact ? row.emer_contact : "---" }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label for="">Address:</label>
                                                                                        <p>{{ strFormat(row.emer_addr) ? strFormat(row.emer_addr) : "---" }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="other-information">
                                                                <div class="row mt-4 mb-2">
                                                                    <div class="col-md-12">
                                                                        <h5 style="color: #575962">Other Information</h5>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                            <div class="m-portlet__head">
                                                                                <div class="m-portlet__head-caption">
                                                                                    <div class="m-portlet__head-title">
                                                                                        <h3 class="m-portlet__head-text">
                                                                                            Dependents
                                                                                        </h3>
                                                                                    </div>
                                                                                </div>                                                                           
                                                                            </div>
                                                                            <div class="m-portlet__body">
                                                                                <table width="100%" class="table" id="tbl-dependents">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Name</th>
                                                                                            <th>Age</th>
                                                                                            <th>Relation</th>
                                                                                            <th>Birth Date</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                            <div class="m-portlet__head">
                                                                                <div class="m-portlet__head-caption">
                                                                                    <div class="m-portlet__head-title">
                                                                                        <h3 class="m-portlet__head-text">
                                                                                            Educational Background
                                                                                        </h3>
                                                                                    </div>
                                                                                </div>                                                                           
                                                                            </div>
                                                                            <div class="m-portlet__body">
                                                                                <table width="100%" class="table" id="tbl-educational_background">
                                                                                    <thead>
                                                                                        <th>Level</th>
                                                                                        <th>Degree</th>
                                                                                        <th>School</th>
                                                                                        <th>Honors</th>
                                                                                        <th>From Date</th>
                                                                                        <th>To Date</th>
                                                                                    </thead>
                                                                                    <tbody></tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                            <div class="m-portlet__head">
                                                                                <div class="m-portlet__head-caption">
                                                                                    <div class="m-portlet__head-title">
                                                                                        <h3 class="m-portlet__head-text">
                                                                                            Licensure Exams and Certifications
                                                                                        </h3>
                                                                                    </div>
                                                                                </div>                                                                           
                                                                            </div>
                                                                            <div class="m-portlet__body">
                                                                                <table width="100%" class="table" id="tbl-licensure_exams">
                                                                                    <thead>
                                                                                        <th>License/Exam Type</th>
                                                                                        <th>Exam Place</th>
                                                                                        <th>Rating</th>
                                                                                        <th>Released Date</th>
                                                                                        <th>Exam Date</th>
                                                                                        <th>License No</th>
                                                                                    </thead>
                                                                                    <tbody></tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                            <div class="m-portlet__head">
                                                                                <div class="m-portlet__head-caption">
                                                                                    <div class="m-portlet__head-title">
                                                                                        <h3 class="m-portlet__head-text">
                                                                                            Driver's License
                                                                                        </h3>
                                                                                    </div>
                                                                                </div>                                                                            
                                                                            </div>
                                                                            <div class="m-portlet__body">
                                                                                <table width="100%" class="table" id="tbl-dl">
                                                                                    <thead>
                                                                                        <th>Restriction</th>
                                                                                        <th>License No</th>
                                                                                        <th>Exp Date</th>
                                                                                    </thead>
                                                                                    <tbody></tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                            <div class="m-portlet__head">
                                                                                <div class="m-portlet__head-caption">
                                                                                    <div class="m-portlet__head-title">
                                                                                        <h3 class="m-portlet__head-text">
                                                                                            Work Experiences
                                                                                        </h3>
                                                                                    </div>
                                                                                </div>                                                                           
                                                                            </div>
                                                                            <div class="m-portlet__body">
                                                                                <table width="100%" class="table" id="tbl-work_experiences">
                                                                                    <thead>
                                                                                        <th>Company</th>
                                                                                        <th>From</th>
                                                                                        <th>To</th>
                                                                                        <th>Position</th>
                                                                                        <th>Status</th>
                                                                                        <th>Reason For Leaving</th>
                                                                                    </thead>
                                                                                    <tbody></tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                            <div class="m-portlet__head">
                                                                                <div class="m-portlet__head-caption">
                                                                                    <div class="m-portlet__head-title">
                                                                                        <h3 class="m-portlet__head-text">
                                                                                            Awards and Achievements
                                                                                        </h3>
                                                                                    </div>
                                                                                </div>                                                                         
                                                                            </div>
                                                                            <div class="m-portlet__body">
                                                                                <table width="100%" class="table" id="tbl-awards">
                                                                                    <thead>
                                                                                        <th>Awards/Achievement</th>
                                                                                        <th>Institution</th>
                                                                                        <th>Given Date</th>
                                                                                    </thead>
                                                                                    <tbody></tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                            <div class="m-portlet__head">
                                                                                <div class="m-portlet__head-caption">
                                                                                    <div class="m-portlet__head-title">
                                                                                        <h3 class="m-portlet__head-text">
                                                                                            Organization
                                                                                        </h3>
                                                                                    </div>
                                                                                </div>                                                                           
                                                                            </div>
                                                                            <div class="m-portlet__body">
                                                                                <table width="100%" class="table" id="tbl-organizations">
                                                                                    <thead>
                                                                                        <th>Institution</th>
                                                                                        <th>Membership Title</th>
                                                                                        <th>From</th>
                                                                                        <th>To</th>
                                                                                    </thead>
                                                                                    <tbody></tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                            <div class="m-portlet__head">
                                                                                <div class="m-portlet__head-caption">
                                                                                    <div class="m-portlet__head-title">
                                                                                        <h3 class="m-portlet__head-text">
                                                                                            Training and Seminars
                                                                                        </h3>
                                                                                    </div>
                                                                                </div>                                                                          
                                                                            </div>
                                                                            <div class="m-portlet__body">
                                                                                <table width="100%" class="table" id="tbl-trainings">
                                                                                    <thead>
                                                                                        <th>Training</th>
                                                                                        <th>From</th>
                                                                                        <th>To</th>
                                                                                        <th>Institution</th>
                                                                                        <th>Conductor</th>
                                                                                        <th>Venue</th>
                                                                                    </thead>
                                                                                    <tbody></tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                            <div class="m-portlet__head">
                                                                                <div class="m-portlet__head-caption">
                                                                                    <div class="m-portlet__head-title">
                                                                                        <h3 class="m-portlet__head-text">
                                                                                            Personal References
                                                                                        </h3>
                                                                                    </div>
                                                                                </div>                                                                           
                                                                            </div>
                                                                            <div class="m-portlet__body">
                                                                                <table width="100%" class="table" id="tbl-personal_references">
                                                                                    <thead>
                                                                                        <th>Name</th>
                                                                                        <th>Contact No.</th>
                                                                                        <th>Address</th>
                                                                                    </thead>
                                                                                    <tbody></tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                            <div class="m-portlet__head">
                                                                                <div class="m-portlet__head-caption">
                                                                                    <div class="m-portlet__head-title">
                                                                                        <h3 class="m-portlet__head-text">
                                                                                            Medical History/Records
                                                                                        </h3>
                                                                                    </div>
                                                                                </div>                                                                          
                                                                            </div>
                                                                            <div class="m-portlet__body">
                                                                                <table width="100%" class="table" id="tbl-medical_records">
                                                                                    <thead>
                                                                                        <th>Details</th>
                                                                                        <th>Med. No.</th>
                                                                                        <th>Date</th>
                                                                                        <th>Venue</th>
                                                                                        <th>Physician</th>
                                                                                        <th>Findings</th>
                                                                                        <th>Remarks</th>
                                                                                    </thead>
                                                                                    <tbody></tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm">
                                                                            <div class="m-portlet__head">
                                                                                <div class="m-portlet__head-caption">
                                                                                    <div class="m-portlet__head-title">
                                                                                        <h3 class="m-portlet__head-text">
                                                                                            Skills
                                                                                        </h3>
                                                                                    </div>
                                                                                </div>                                                                           
                                                                            </div>
                                                                            <div class="m-portlet__body">
                                                                                <table width="100%" class="table" id="tbl-skills">
                                                                                    <thead>
                                                                                        <th>Skills</th>
                                                                                    </thead>
                                                                                    <tbody></tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="send-resume mt-3">
                                                                <form method="POST" id="submit-resume" onsubmit="saveResumeInfo(this, event); return false;" enctype="multipart/form-data">
                                                                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>" autocomplete="off">
                                                                    <input type="hidden" name="public_user_id" value="<?php echo $_COOKIE['public_user_id']; ?>">
                                                                    <template v-if="count > 0">
                                                                        <input type="hidden" name="document_body_id" :value="row.body_id ? row.body_id : 0">
                                                                    </template>
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <div class="custom-file d-flex align-items-center" style="gap: 15px">
                                                                                <input id="temp_fileupload" name="files" type="file" hidden multiple data-validation="required"  accept = "application/pdf">
                                                                                <label class="file_label" for="temp_fileupload">Attach Resume</label>
                                                                                <span style="font-size: 30px"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                                                                                <input type="hidden" name="attachments[]" id="attachment">
                                                                            </div>
                                                                            <div id="captcha" class="g-recaptcha" data-sitekey="6LfYy9QlAAAAAHNzof6dl4R4O_GkFrFeyRc42zdU"></div>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <div id="new_regestration_preview" class="m-widget2 row">
                                                                                <template v-if="counts">
                                                                                    <div class="m-portlet m-portlet--rounded col-lg-12">
                                                                                        <div class="m-portlet__head">
                                                                                            <div class="m-portlet__head-caption">
                                                                                                <div class="m-portlet__head-title">
                                                                                                    <h3 class="m-portlet__head-text">
                                                                                                        Attachments
                                                                                                    </h3>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="m-portlet__body">
                                                                                            <template v-for="(item, index) in rows">
                                                                                                <div v-bind:class="getClass(item.filename)">
                                                                                                    <div class="m-widget4__item m-0 p-0">
                                                                                                        <div class="m-widget4__img m-widget4__img--icon">
                                                                                                            <img v-bind:src="getExtension(item.filename)" alt="" height="50" width="50">
                                                                                                        </div>
                                                                                                        <div class="m-widget4__info">
                                                                                                            <span class="m-widget4__text" v-text="item.filename"></span>
                                                                                                        </div>
                                                                                                        <div class="m-widget2__actions">
                                                                                                            <a href="javascript:void(0)" class="m-nav__link" v-on:click="fileDelete(item.id, <?php echo $_COOKIE['public_user_id']; ?>)" style="text-decoration: none">
                                                                                                                <span class="m-nav__link-text">
                                                                                                                    Remove
                                                                                                                </span>
                                                                                                            </a>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </template>
                                                                                        </div>
                                                                                    </div>
                                                                                </template>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row justify-content-end px-3">
                                                                        <button type="button" onclick="backStep()" class="default-btn prev-step mr-2">Back</button>
                                                                        <button type="submit" name="save_resume" class="btn btn-success final-btn">Submit</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
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

<div class="modal fade" id="document-modal-container" tabindex="-1" role="dialog">
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

<script>
    var _csrf_hash = '<?=$this->security->get_csrf_hash() ?>';
    var tableDependents = $("#tbl-dependents_list");
    var tableEducationalBackground = $("#tbl-educational_background_list");
    var tableLicensure = $("#tbl-licensure_exams_list");
    var tableDriverLicense = $("#tbl-driverlicense");
    var tableWorkExperience = $("#tbl-work_experiences_list");
    var tableAwards = $("#tbl-awards_list");
    var tableOrganization = $("#tbl-organizations_list");
    var tableTrainings = $("#tbl-trainings_list");
    var tablePersonalReference = $("#tbl-personal_references_list");
    var tableMedicalHistory = $("#tbl-medical_records_list");
    var tblSkills = $("#tbl-skills-list");
    var tempDataId = 0;

    var modalTempContent = $("#modalTempContent");
    var modalTempContentLg = modalTempContent.clone().prop("id", "modalTempContentLg").appendTo(".m-content");
    modalTempContentLg.find(".modal-dialog").addClass("modal-lg");

    $(document).ready(function () {     
        var umidSwitch = $(".tab-pane").find("#umid_no");
        umidSwitch.prop("checked", false);

        var tinNumberMask = $(".tab-pane").find("#tin_no");
        var philhealthMask = $(".tab-pane").find("#phealth_no");
        var pagibigMask = $(".tab-pane").find("#pagibig_no");
        var sssNoMask = $(".tab-pane").find("#sss_no");

        tinNumberMask.inputmask("mask", { "mask": "999-999-999" });
        philhealthMask.inputmask("mask", { "mask": "99-999999999-9" });
        pagibigMask.inputmask("mask", { "mask": "9999-9999-9999" });
        sssNoMask.inputmask("mask", { "mask": "99-9999999-9" });

        umidSwitch.on('change', function(){
            if(umidSwitch.is(':checked')){
                var sssNo = sssNoMask.val();
                if(sssNo !== "undefined" && sssNo !== null && sssNo !== ""){
                    var tempSplitSssNo = sssNo.split("-", 3);
                    var firstSplit = tempSplitSssNo[0].length;
                    console.log(tempSplitSssNo);
                    if (firstSplit > 1) {
                        umidSwitch.prop("checked", true);
                    }
                }
            }
        });

        setTimeout(function () {
            umidSwitch.trigger("change");
            updateUmidTypeSwitch();
        }, 500);

        $("#Recruitment").select2({
            width: '100%',
            placeholder: "Select an Option",
        });
        
        $("#School").select2({
            width: '100%',
            ajax: {
                url: '<?php echo base_url("crs/online_registration/get_school"); ?>',
                global: false,
                processResults: function (data) {
                    return data;
                },
                delay: 500
            },
        });

        $("#Course").select2({
            width: '100%',
            ajax: {
                url: '<?php echo base_url("crs/online_registration/get_course"); ?>',
                global: false,
                processResults: function (data) {
                    return data;
                },
                delay: 500
            },
        });

        $("#Position").select2({
            width: '100%',
            ajax: {
                url: '<?php echo base_url("crs/online_registration/get_position"); ?>',
                global: false,
                processResults: function (data) {
                    return data;
                },
                delay: 500
            },
        });

        $("#dateOfApplication").datepicker({
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'bottom left',
            todayBtn: 'linked',
            format: 'mm/dd/yyyy',
            forceParse: false
        });
        // application details

        // personal information
        $("#civil_stat").select2({
            placeholder: "Select an Option",
        });

        $("#bday").datepicker({
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            },
            format: "yyyy-mm-dd",
            autoclose: true,
            todayBtn: 'linked',
        }).on('changeDate', function(e){
            var currentDt = moment(e.date).format("YYYY-MM-DD");
            var self = $(e.target);
            self.validate();
        });
        // personal information

        // additional information
        $("#tax_status").select2({
            width: '100%',
            'placeholder' : "Select an Option"
        });
        // additional information
        
        $(".next-step").click(function (e) {
            var active = $('.wizard .nav-tabs li.active');
            var current_tabId = active.attr('id');
            var next_tabId = active.next().attr('id');
            var current_tab = $(".tab-content #"+current_tabId);
            var next_tab = $(".tab-content #"+next_tabId);
            var url = '';

            var submit_btn = $(this).attr('id');
            if(submit_btn == 'btn-step1'){
                url = '<?php echo base_url("crs/online_registration/insert_application"); ?>';
            }

            if(submit_btn == 'btn-step2'){
                url = '<?php echo base_url("crs/online_registration/insert_information"); ?>'
            }

            if(submit_btn == 'btn-step3'){
                url = '<?php echo base_url("crs/online_registration/insert_additional"); ?>';
            }

            if(submit_btn == 'btn-step5'){
                url = '<?php echo base_url("crs/online_registration/insert_resume"); ?>';
            }

            if($(this).attr('type') == 'submit'){
                $.validate({
                    form: '#crs-application',
                    lang: 'en',
                    onSuccess: function(form){
                        var data = form.serialize();
                        $.ajax({
                            url : url,
                            dataType: "JSON",
                            type: "POST",
                            data: data,
                            success: function(response){
                                if(response.success){
                                    current_tab.removeClass('active').addClass('disabled');
                                    active.addClass('disabled').removeClass('active');

                                    if(submit_btn == 'btn-step3'){
                                        dtDependents();
                                        dtEducation();
                                        dtLicense();
                                        dtDrivers();
                                        dtWork();
                                        dtAwards();
                                        dtOrg();
                                        dtTraining();
                                        dtPersonal();
                                        dtMedical();
                                        dtSkill();
                                    }

                                    applicantData();
    
                                    active.next().removeClass('disabled').addClass('active');
                                    next_tab.addClass('active').removeClass('disabled');

                                    active.addClass('done');
                                    if(active.hasClass('done')){
                                        var done = $('.wizard .nav-tabs li.disabled.done div span.round-tab');
                                        done.html('<span class="fa fa-check"></span>');
                                    }
                                }else{
                                    toastr.warning(response.message, 'CRS Application');
                                }
                            }
                        });
                        
                        return false;
                    },
                    onError: function(){
                        toastr.warning('Please fill-up the Required field.', 'CRS Application');
                    }
                });
            }else{
                var info_id = $("input[name=info_id]").val();

                current_tab.removeClass('active').addClass('disabled');
                active.addClass('disabled').removeClass('active');

                active.addClass('done');
                if(active.hasClass('done')){
                    var done = $('.wizard .nav-tabs li.disabled.done div span.round-tab');
                    done.html('<span class="fa fa-check"></span>');
                }

                setTimeout( function(){
                    dtDependents(next_tabId);
                    dtEducation(next_tabId);
                    dtLicense(next_tabId);
                    dtDrivers(next_tabId);
                    dtWork(next_tabId);
                    dtAwards(next_tabId);
                    dtOrg(next_tabId);
                    dtTraining(next_tabId);
                    dtPersonal(next_tabId);
                    dtMedical(next_tabId);
                    dtSkill(next_tabId);

                    temp_images();
                }, 500);

                active.next().removeClass('disabled').addClass('active');
                next_tab.addClass('active').removeClass('disabled');

                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;

                // $.ajax({
                //     url: '<?php //echo base_url("crs/online_registration/check_personal_reference"); ?>',
                //     type: "GET",
                //     dataType: "JSON",
                //     data:{
                //         id: info_id
                //     },
                //     success: function(data){
                //         if(data.response){
                //             current_tab.removeClass('active').addClass('disabled');
                //             active.addClass('disabled').removeClass('active');

                //             setTimeout( function(){
                //                 dtDependents(next_tabId);
                //                 dtEducation(next_tabId);
                //                 dtLicense(next_tabId);
                //                 dtDrivers(next_tabId);
                //                 dtWork(next_tabId);
                //                 dtAwards(next_tabId);
                //                 dtOrg(next_tabId);
                //                 dtTraining(next_tabId);
                //                 dtPersonal(next_tabId);
                //                 dtMedical(next_tabId);
                //                 dtSkill(next_tabId);
                //             }, 500);

                //             active.next().removeClass('disabled').addClass('active');
                //             next_tab.addClass('active').removeClass('disabled');
                //         }else{
                //             toastr.warning('Please fill-up the Personal Reference.', 'CRS Application');
                //         }
                //     }

                // });
            }

            // application details
            $("#Recruitment").select2({
                width: '100%',
                placeholder: "Select an Option",
            });

            $("#School").select2({
                width: '100%',
                ajax: {
                    url: '<?php echo base_url("crs/online_registration/get_school"); ?>',
                    global: false,
                    processResults: function (data) {
                        return data;
                    },
                    delay: 500
                },
            });

            $("#Course").select2({
                width: '100%',
                ajax: {
                    url: '<?php echo base_url("crs/online_registration/get_course"); ?>',
                    global: false,
                    processResults: function (data) {
                        return data;
                    },
                    delay: 500
                },
            });

            $("#Position").select2({
                width: '100%',
                ajax: {
                    url: '<?php echo base_url("crs/online_registration/get_position"); ?>',
                    global: false,
                    processResults: function (data) {
                        return data;
                    },
                    delay: 500
                },
            });

            $("#dateOfApplication").datepicker({
                todayHighlight: true,
                autoclose: true,
                pickerPosition: 'bottom left',
                todayBtn: 'linked',
                format: 'mm/dd/yyyy',
                forceParse: false
            });
            // application details

            // personal information
            $("#civil_stat").select2({
                width: '100%',
                placeholder: "Select an Option",
            });

            $("#bday").datepicker({
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                },
                format: "yyyy-mm-dd",
                autoclose: true,
                todayBtn: 'linked',
            }).on('changeDate', function(e){
                var currentDt = moment(e.date).format("YYYY-MM-DD");
                var self = $(e.target);
                self.validate();
            });
            // personal information

            // additional information
            $("#tax_status").select2({
                width: '100%',
                'placeholder' : "Select an Option"
            });

            var umidSwitch = $(".tab-pane").find("#umid_no");
            umidSwitch.prop("checked", false);
            var tinNumberMask = $(".tab-pane").find("#tin_no");
            var philhealthMask = $(".tab-pane").find("#phealth_no");
            var pagibigMask = $(".tab-pane").find("#pagibig_no");
            var sssNoMask = $(".tab-pane").find("#sss_no");

            tinNumberMask.inputmask("mask", { "mask": "999-999-999" });
            philhealthMask.inputmask("mask", { "mask": "99-999999999-9" });
            pagibigMask.inputmask("mask", { "mask": "9999-9999-9999" });
            sssNoMask.inputmask("mask", { "mask": "99-9999999-9" });

            umidSwitch.on('change', function(){
                if(umidSwitch.is(':checked')){
                    var sssNo = sssNoMask.val();
                    if(sssNo !== "undefined" && sssNo !== null && sssNo !== ""){
                        var tempSplitSssNo = sssNo.split("-", 3);
                        var firstSplit = tempSplitSssNo[0].length;
                        console.log(tempSplitSssNo);
                        if (firstSplit > 1) {
                            umidSwitch.prop("checked", true);
                        }
                    }
                }
            });

            setTimeout(function () {
                umidSwitch.trigger("change");
            }, 500);
        });

        $(".skip-step").click(function(e){
            var active = $('.wizard .nav-tabs li.active');
            var current_tabId = active.attr('id');
            var next_tabId = active.next().attr('id');
            var current_tab = $(".tab-content #"+current_tabId);
            var next_tab = $(".tab-content #"+next_tabId);

            current_tab.removeClass('active').addClass('disabled');
            active.addClass('disabled').removeClass('active');

            active.next().removeClass('disabled').addClass('active');
            next_tab.addClass('active').removeClass('disabled');

            if(next_tabId == 'step4'){
                dtDependents();
                dtEducation();
                dtLicense();
                dtDrivers();
                dtWork();
                dtAwards();
                dtOrg();
                dtTraining();
                dtPersonal();
                dtMedical();
                dtSkill();
            }

            var umidSwitch = $(".tab-pane").find("#umid_no");
            umidSwitch.prop("checked", false);
            var tinNumberMask = $(".tab-pane").find("#tin_no");
            var philhealthMask = $(".tab-pane").find("#phealth_no");
            var pagibigMask = $(".tab-pane").find("#pagibig_no");
            var sssNoMask = $(".tab-pane").find("#sss_no");

            tinNumberMask.inputmask("mask", { "mask": "999-999-999" });
            philhealthMask.inputmask("mask", { "mask": "99-999999999-9" });
            pagibigMask.inputmask("mask", { "mask": "9999-9999-9999" });
            sssNoMask.inputmask("mask", { "mask": "99-9999999-9" });

            umidSwitch.on('change', function(){
                if(umidSwitch.is(':checked')){
                    var sssNo = sssNoMask.val();
                    if(sssNo !== "undefined" && sssNo !== null && sssNo !== ""){
                        var tempSplitSssNo = sssNo.split("-", 3);
                        var firstSplit = tempSplitSssNo[0].length;
                        console.log(tempSplitSssNo);
                        if (firstSplit > 1) {
                            umidSwitch.prop("checked", true);
                        }
                    }
                }
            });

            setTimeout(function () {
                umidSwitch.trigger("change");
                updateUmidTypeSwitch();
            }, 500);
        });

        $(".prev-step").click(function(e){
            var active = $('.wizard .nav-tabs li.active');
            var current_tabId = active.attr('id');
            var prev_tabId = active.prev().attr('id');
            var current_tab = $(".tab-content #"+current_tabId);
            var prev_tab = $(".tab-content #"+prev_tabId);

            current_tab.removeClass('active').addClass('disabled');
            active.addClass('disabled').removeClass('active');

            active.prev().removeClass('disabled').removeClass('done').addClass('active');
            prev_tab.addClass('active').removeClass('disabled');

            if(active.prev().hasClass('active')){
                var this_tab = active.prev().attr('id');
                var tab = $('.wizard .nav-tabs li.active div span.round-tab');
                
                if(this_tab == 'step1'){
                    tab.html('1');
                }else if(this_tab == 'step2'){
                    tab.html('2');
                }else if(this_tab == 'step3'){
                    tab.html('3');
                }else{
                    tab.html('4');
                }
            }

            // application details
            $("#Recruitment").select2({
                width: '100%',
                placeholder: "Select an Option",
            });

            $("#School").select2({
                width: '100%',
                ajax: {
                    url: '<?php echo base_url("crs/online_registration/get_school"); ?>',
                    global: false,
                    processResults: function (data) {
                        return data;
                    },
                    delay: 500
                },
            });

            $("#Course").select2({
                width: '100%',
                ajax: {
                    url: '<?php echo base_url("crs/online_registration/get_course"); ?>',
                    global: false,
                    processResults: function (data) {
                        return data;
                    },
                    delay: 500
                },
            });

            $("#Position").select2({
                width: '100%',
                ajax: {
                    url: '<?php echo base_url("crs/online_registration/get_position"); ?>',
                    global: false,
                    processResults: function (data) {
                        return data;
                    },
                    delay: 500
                },
            });

            $("#dateOfApplication").datepicker({
                todayHighlight: true,
                autoclose: true,
                pickerPosition: 'bottom left',
                todayBtn: 'linked',
                format: 'mm/dd/yyyy',
                forceParse: false
            });
            // application details

            // personal information
            $("#civil_stat").select2({
                width: '100%',
                placeholder: "Select an Option",
            });

            $("#bday").datepicker({
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                },
                format: "yyyy-mm-dd",
                autoclose: true,
                todayBtn: 'linked',
            }).on('changeDate', function(e){
                var currentDt = moment(e.date).format("YYYY-MM-DD");
                var self = $(e.target);
                self.validate();
            });
            // personal information

            // additional information
            $("#tax_status").select2({
                width: '100%',
                'placeholder' : "Select an Option"
            });

            var umidSwitch = $(".tab-pane").find("#umid_no");
            umidSwitch.prop("checked", false);
            var tinNumberMask = $(".tab-pane").find("#tin_no");
            var philhealthMask = $(".tab-pane").find("#phealth_no");
            var pagibigMask = $(".tab-pane").find("#pagibig_no");
            var sssNoMask = $(".tab-pane").find("#sss_no");

            tinNumberMask.inputmask("mask", { "mask": "999-999-999" });
            philhealthMask.inputmask("mask", { "mask": "99-999999999-9" });
            pagibigMask.inputmask("mask", { "mask": "9999-9999-9999" });
            sssNoMask.inputmask("mask", { "mask": "99-9999999-9" });

            umidSwitch.on('change', function(){
                if(umidSwitch.is(':checked')){
                    var sssNo = sssNoMask.val();
                    if(sssNo !== "undefined" && sssNo !== null && sssNo !== ""){
                        var tempSplitSssNo = sssNo.split("-", 3);
                        var firstSplit = tempSplitSssNo[0].length;
                        console.log(tempSplitSssNo);
                        if (firstSplit > 1) {
                            umidSwitch.prop("checked", true);
                        }
                    }
                }
            });

            setTimeout(function () {
                umidSwitch.trigger("change");
                updateUmidTypeSwitch();
            }, 500);
        });

        $("#same-to-permanent").on('change', function(){
            if($("#same-to-permanent:checked").val() == 'on' && typeof $("#same-to-permanent:checked").val() !== 'undefined'){
                var permanent = $("input[name=curr_addr]").val();
                $("input[name=prov_addr]").val(permanent);
            }
        });

        applicantData();

        $('table')
        .on('click', '.btnEditDependents', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_edit_modal") ?>',
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "crs/modals/edit_dependent",
                    function_name: "getDependentInfo",
                    model: "registration_model",
                    formData: {id}
                },
                success: function (response) {
                    initRegularEditDialog(response);

                    $.validate({
                        form: "#employee-data-update-dependent",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Employee dependent updated.",
                                            5000
                                        );
                                        currentForm.reset();
                                        $("#document-modal-container").modal("hide");
                                        dtDependents();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating employee dependent!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            });
        })
        .on('click', '.btnRemoveDependents', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_confirm_modal") ?>',
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-trash mr-2'></i>Confirm Remove",
                        message: "Are you sure to remove this record?",
                        action: "crs/online_registration/remove_dependent/" + id,
                        color: "btn-danger"
                    },
                    path: "ams/confirmation_dialog",
                    function_name: "passDataToDialog"
                },
                success: function (modal) {
                    const _modal = $("#document-modal-container");
                    _modal.html(modal);
                    _modal.modal("show");

                    $("#confirmation-dialog").submit( function(e){
                        e.preventDefault();

                        const url = $(this).attr('action');

                        $.ajax({
                            url: '<?=base_url() ?>' + url,
                            type: "GET",
                            dataType: "JSON",
                            success: function(data){
                                if(data.response){
                                    dtDependents();
                                    toastr.success(data.message, 'Dependents');
                                    _modal.modal('hide');
                                }
                            }
                        });
                    });
                }
            });
        })
        .on('click', '.btnEditEducational', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_edit_modal") ?>',
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "crs/modals/edit_educational_background",
                    function_name: 'getEducationalInfo',
                    model: 'registration_model',
                    formData: {id},
                    init_modal_data_function: 'getEducationalInfo'
                },
                success: function (response) {
                    initEditEducationalBackground(response);

                    $.validate({
                        form: "#employee-data-update-educational-background",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Educational background has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        $("#document-modal-container").modal("hide");
                                        // dtEducationalBg.ajax.reload();
                                        dtEducation();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating educational background!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            });
        })
        .on('click', '.btnRemoveEducational', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_confirm_modal") ?>',
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-trash mr-2'></i>Confirm Remove",
                        message: "Are you sure to remove this record?",
                        action: "crs/online_registration/remove_educational_background/" + id,
                        color: "btn-danger"
                    },
                    path: "ams/confirmation_dialog",
                    function_name: "passDataToDialog"
                },
                success: function (modal) {
                    const _modal = $("#document-modal-container");
                    _modal.html(modal);
                    _modal.modal("show");

                    $("#confirmation-dialog").submit( function(e){
                        e.preventDefault();

                        const url = $(this).attr('action');

                        $.ajax({
                            url: '<?=base_url() ?>' + url,
                            type: "GET",
                            dataType: "JSON",
                            success: function(data){
                                if(data.response){
                                    dtEducation();
                                    toastr.success(data.message, 'Educational Background');
                                    _modal.modal('hide');
                                }
                            }
                        });
                    });
                }
            });
        })
        .on('click', '.btnEditLicensure', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_edit_modal") ?>',
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "crs/modals/edit_licensure",
                    function_name: 'getLicensure',
                    model: 'registration_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);

                    $.validate({
                        form: "#employee-data-update-licensure",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Licensure Exams and certification has been updated.",
                                            5000
                                        );
                                        currentForm.reset();
                                        $("#document-modal-container").modal("hide");
                                        // dtLicensureExam.ajax.reload();
                                        dtLicense();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating licensure Exams and certification!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            });
        })
        .on('click', '.btnRemoveLicensure', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_confirm_modal") ?>',
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-trash mr-2'></i>Confirm Remove",
                        message: "Are you sure to remove this record?",
                        action: "crs/online_registration/remove_licensure/" + id,
                        color: "btn-danger"
                    },
                    path: "ams/confirmation_dialog",
                    function_name: "passDataToDialog"
                },
                success: function (modal) {
                    const _modal = $("#document-modal-container");
                    _modal.html(modal);
                    _modal.modal("show");

                    $("#confirmation-dialog").submit( function(e){
                        e.preventDefault();

                        const url = $(this).attr('action');

                        $.ajax({
                            url: '<?=base_url() ?>' + url,
                            type: "GET",
                            dataType: "JSON",
                            success: function(data){
                                if(data.response){
                                    dtLicense();
                                    toastr.success(data.message, 'Licensure Exams and Certifications');
                                    _modal.modal('hide');
                                }
                            }
                        });
                    });
                }
            });
        })
        .on('click', '.btnEditDriverLicense', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_edit_modal") ?>',
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "crs/modals/edit_driverlicense",
                    function_name: 'getDriverLicense',
                    model: 'registration_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);

                    $.validate({
                        form: "#employee-data-update-driverlicense",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Employee Driver's License has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        $("#document-modal-container").modal("hide");
                                        dtDrivers();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating employee driver's license!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            });
        })
        .on('click', '.btnRemoveDriverLicense', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_confirm_modal") ?>',
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-trash mr-2'></i>Confirm Remove",
                        message: "Are you sure to remove this record?",
                        action: "crs/online_registration/remove_driverlicense/" + id,
                        color: "btn-danger"
                    },
                    path: "ams/confirmation_dialog",
                    function_name: "passDataToDialog"
                },
                success: function (modal) {
                    const _modal = $("#document-modal-container");
                    _modal.html(modal);
                    _modal.modal("show");

                    $("#confirmation-dialog").submit( function(e){
                        e.preventDefault();

                        const url = $(this).attr('action');

                        $.ajax({
                            url: '<?=base_url() ?>' + url,
                            type: "GET",
                            dataType: "JSON",
                            success: function(data){
                                if(data.response){
                                    dtDrivers();
                                    toastr.success(data.message, 'Driver License');
                                    _modal.modal('hide');
                                }
                            }
                        });
                    });
                }
            });
        })
        .on('click', '.btnEditWorkExperience', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url:  '<?=base_url("crs/online_registration/open_edit_modal") ?>',
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "crs/modals/edit_work_experience",
                    function_name: 'getWorkExperience',
                    model: 'registration_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);

                    $.validate({
                        form: "#employee-date-update-work-experience",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Work experience has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        $("#document-modal-container").modal("hide");
                                        dtWork();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating employee work experience!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            });
        })
        .on('click', '.btnRemoveWorkExperience', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_confirm_modal") ?>',
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-trash mr-2'></i>Confirm Remove",
                        message: "Are you sure to remove this record?",
                        action: "crs/online_registration/remove_worK_experience/" + id,
                        color: "btn-danger"
                    },
                    path: "ams/confirmation_dialog",
                    function_name: "passDataToDialog"
                },
                success: function (modal) {
                    const _modal = $("#document-modal-container");
                    _modal.html(modal);
                    _modal.modal("show");

                    $("#confirmation-dialog").submit( function(e){
                        e.preventDefault();

                        const url = $(this).attr('action');

                        $.ajax({
                            url: '<?=base_url() ?>' + url,
                            type: "GET",
                            dataType: "JSON",
                            success: function(data){
                                if(data.response){
                                    dtWork();
                                    toastr.success(data.message, 'Work Experiences');
                                    _modal.modal('hide');
                                }
                            }
                        });
                    });
                }
            });
        })
        .on('click', '.btnEditAwards', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_edit_modal") ?>',
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "crs/modals/edit_award",
                    function_name: 'getAward',
                    model: 'registration_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);

                    $.validate({
                        form: "#employee-data-update-award",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Award and achievement has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        $("#document-modal-container").modal("hide");
                                        dtAwards();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating award and achievement!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            });
        })
        .on('click', '.btnRemoveAwards', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_confirm_modal") ?>',
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-trash mr-2'></i>Confirm Remove",
                        message: "Are you sure to remove this record?",
                        action: "crs/online_registration/remove_award/" + id,
                        color: "btn-danger"
                    },
                    path: "ams/confirmation_dialog",
                    function_name: "passDataToDialog"
                },
                success: function (modal) {
                    const _modal = $("#document-modal-container");
                    _modal.html(modal);
                    _modal.modal("show");

                    $("#confirmation-dialog").submit( function(e){
                        e.preventDefault();

                        const url = $(this).attr('action');

                        $.ajax({
                            url: '<?=base_url() ?>' + url,
                            type: "GET",
                            dataType: "JSON",
                            success: function(data){
                                if(data.response){
                                    dtAwards();
                                    toastr.success(data.message, 'Awards and Achievements');
                                    _modal.modal('hide');
                                }
                            }
                        });
                    });
                }
            });
        })
        .on('click', '.btnEditOrganization', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_edit_modal") ?>',
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "crs/modals/edit_organization",
                    function_name: 'getOrganization',
                    model: 'registration_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);

                    $.validate({
                        form: "#employee-data-update-organization",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Organization has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        $("#document-modal-container").modal("hide");
                                        // dtOrganization.ajax.reload();
                                        dtOrg();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating organization!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            });
        })
        .on('click', '.btnRemoveOrganization', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_confirm_modal") ?>',
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-trash mr-2'></i>Confirm Remove",
                        message: "Are you sure to remove this record?",
                        action: "crs/online_registration/remove_organization/" + id,
                        color: "btn-danger"
                    },
                    path: "ams/confirmation_dialog",
                    function_name: "passDataToDialog"
                },
                success: function (modal) {
                    const _modal = $("#document-modal-container");
                    _modal.html(modal);
                    _modal.modal("show");

                    $("#confirmation-dialog").submit( function(e){
                        e.preventDefault();

                        const url = $(this).attr('action');

                        $.ajax({
                            url: '<?=base_url() ?>' + url,
                            type: "GET",
                            dataType: "JSON",
                            success: function(data){
                                if(data.response){
                                    dtOrg();
                                    toastr.success(data.message, 'Organization');
                                    _modal.modal('hide');
                                }
                            }
                        });
                    });
                }
            });
        })
        .on('click', '.btnEditTrainings', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_edit_modal") ?>',
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "crs/modals/edit_training",
                    function_name: 'getTraningsAndSeminars',
                    model: 'registration_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);

                    $.validate({
                        form: "#employee-data-update-training",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success( 
                                            json.toastr_msg,
                                            "Training and seminar has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        $("#document-modal-container").modal("hide");
                                        // dtTrainings.ajax.reload();
                                        dtTraining();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating training and seminar!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            });
        })
        .on('click', '.btnRemoveTrainings', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_confirm_modal") ?>',
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-trash mr-2'></i>Confirm Remove",
                        message: "Are you sure to remove this record?",
                        action: "crs/online_registration/remove_training/" + id,
                        color: "btn-danger"
                    },
                    path: "ams/confirmation_dialog",
                    function_name: "passDataToDialog"
                },
                success: function (modal) {
                    const _modal = $("#document-modal-container");
                    _modal.html(modal);
                    _modal.modal("show");

                    $("#confirmation-dialog").submit( function(e){
                        e.preventDefault();

                        const url = $(this).attr('action');

                        $.ajax({
                            url: '<?=base_url() ?>' + url,
                            type: "GET",
                            dataType: "JSON",
                            success: function(data){
                                if(data.response){
                                    dtTraining();
                                    toastr.success(data.message, 'Training and Seminars');
                                    _modal.modal('hide');
                                }
                            }
                        });
                    });
                }
            });
        })
        .on('click', '.btnEditPersonalReference', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_edit_modal") ?>',
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "crs/modals/edit_personal_references",
                    function_name: 'getPersonalReference',
                    model: 'registration_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);

                    $.validate({
                        form: "#employee-data-update-personal-references",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Personal reference has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        $("#document-modal-container").modal("hide");
                                        // dtPersonalReference.ajax.reload();
                                        dtPersonal();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating personal reference!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            });
        })
        .on('click', '.btnRemovePersonalReference', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_confirm_modal") ?>',
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-trash mr-2'></i>Confirm Remove",
                        message: "Are you sure to remove this record?",
                        action: "crs/online_registration/remove_personal_reference/" + id,
                        color: "btn-danger"
                    },
                    path: "ams/confirmation_dialog",
                    function_name: "passDataToDialog"
                },
                success: function (modal) {
                    const _modal = $("#document-modal-container");
                    _modal.html(modal);
                    _modal.modal("show");

                    $("#confirmation-dialog").submit( function(e){
                        e.preventDefault();

                        const url = $(this).attr('action');

                        $.ajax({
                            url: '<?=base_url() ?>' + url,
                            type: "GET",
                            dataType: "JSON",
                            success: function(data){
                                if(data.response){
                                    dtPersonal();
                                    toastr.success(data.message, 'Personal Reference');
                                    _modal.modal('hide');
                                }
                            }
                        });
                    });
                }
            });
        })
        .on('click', '.btnEditMedicalHistory', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_edit_modal") ?>',
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "crs/modals/edit_medical_history",
                    function_name: 'getMedicalHistory',
                    model: 'registration_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);

                    $.validate({
                        form: "#employee-update-medical-record",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Medical history/record has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        $("#document-modal-container").modal("hide");
                                        dtMedical();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating medical history/record!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            });
        })
        .on('click', '.btnRemoveMedicalHistory', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_confirm_modal") ?>',
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-trash mr-2'></i>Confirm Remove",
                        message: "Are you sure to remove this record?",
                        action: "crs/online_registration/remove_medical_record/" + id,
                        color: "btn-danger"
                    },
                    path: "ams/confirmation_dialog",
                    function_name: "passDataToDialog"
                },
                success: function (modal) {
                    const _modal = $("#document-modal-container");
                    _modal.html(modal);
                    _modal.modal("show");

                    $("#confirmation-dialog").submit( function(e){
                        e.preventDefault();

                        const url = $(this).attr('action');

                        $.ajax({
                            url: '<?=base_url() ?>' + url,
                            type: "GET",
                            dataType: "JSON",
                            success: function(data){
                                if(data.response){
                                    dtMedical();
                                    toastr.success(data.message, 'Medical History');
                                    _modal.modal('hide');
                                }
                            }
                        });
                    });
                }
            });
        })
        .on('click', '.btnEditSkill', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_edit_modal") ?>',
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "crs/modals/edit_skill",
                    function_name: 'getSkill',
                    model: 'registration_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);

                    $.validate({
                        form: "#employee-update-skill",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Skill has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        $("#document-modal-container").modal("hide");
                                        dtSkill();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating Skill!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            });
        })
        .on('click', '.btnRemoveSkill', function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: '<?=base_url("crs/online_registration/open_confirm_modal") ?>',
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-trash mr-2'></i>Confirm Remove",
                        message: "Are you sure to remove this record?",
                        action: "crs/online_registration/remove_skill/" + id,
                        color: "btn-danger"
                    },
                    path: "ams/confirmation_dialog",
                    function_name: "passDataToDialog"
                },
                success: function (modal) {
                    const _modal = $("#document-modal-container");
                    _modal.html(modal);
                    _modal.modal("show");

                    $("#confirmation-dialog").submit( function(e){
                        e.preventDefault();

                        const url = $(this).attr('action');

                        $.ajax({
                            url: '<?=base_url() ?>' + url,
                            type: "GET",
                            dataType: "JSON",
                            success: function(data){
                                if(data.response){
                                    dtSkill();
                                    toastr.success(data.message, 'Skill');
                                    _modal.modal('hide');
                                }
                            }
                        });
                    });
                }
            });
        });
    });

    function backStep(){
        var active = $('.wizard .nav-tabs li.active');
        var current_tabId = active.attr('id');
        var prev_tabId = active.prev().attr('id');
        var current_tab = $(".tab-content #"+current_tabId);
        var prev_tab = $(".tab-content #"+prev_tabId);

        current_tab.removeClass('active').addClass('disabled');
        active.addClass('disabled').removeClass('active');

        active.prev().removeClass('disabled').addClass('active');
        prev_tab.addClass('active').removeClass('disabled');

        dtDependents();
        dtEducation();
        dtLicense();
        dtDrivers();
        dtWork();
        dtAwards();
        dtOrg();
        dtTraining();
        dtPersonal();
        dtMedical();
        dtSkill();
    }

    var updateUmidTypeSwitch = function () {
        var _formAdditionalInformation = $("form#crs-application");
        if (typeof _formAdditionalInformation !== "undefined") {
            var umidNoSwitch = _formAdditionalInformation.find("#umid_no");
            var sssNoMask = _formAdditionalInformation.find("#sss_no");
            umidNoSwitch.on("change", function () {
                var radioThis = $(this);
                var isPropChecked = radioThis.prop("checked");
                if (isPropChecked == true) {
                    sssNoMask.inputmask("mask", {"mask": "9999-9999999-9"});
                } else {
                    sssNoMask.inputmask("mask", {"mask": "99-9999999-9"});
                }
            });
        }
    }

    function applicantData(){
        $.ajax({
            url: "<?php echo base_url("crs/online_registration/get_data"); ?>",
            dataType: "JSON",
            type: "GET",
            data: {
                public_user_id: '<?php echo $_COOKIE['public_user_id']; ?>'
            },
            success: function(response){
                if(response[0]){
                    vmData.row = response[0];
                    vmData.count = response.length;
                    tempDataId = response[0].info_id;
                }else{
                    vmData.row = Object.assign({}, {body_id: null, info_id: null});
                }
            }
        });
    }
    // other application
    var dtDependents = function(id = null) {
        $.ajax({
            url: '<?=base_url("crs/online_registration/get_employee_dependents") ?>',
            type: "post",
            dataType: "json",
            data : {
                csrf_token : _csrf_hash,
                applicant_id : tempDataId
            },
            success: function(json){
                var dpTable = '';
                let _columns = '';
                if(id){
                    dpTable = $("#tbl-dependents");
                    _columns = [
                        { data: 'dep_name' },
                        { data: 'dep_age' },
                        { data: 'dep_relation' },
                        { data: 'dep_birthdate' },
                    ];
                }else{
                    dpTable = $("#tbl-dependents_list");
                    _columns = [
                        { data: 'dep_name' },
                        { data: 'dep_age' },
                        { data: 'dep_relation' },
                        { data: 'dep_birthdate' },
                        { 
                            data: "id" ,
                            render: function(data){
                                return dependentsDataTableActions(data);
                            }
                        }
                    ];
                }

                dpTable.DataTable({
                    'destroy': true,
                    "searching": false,
                    "bPaginate": false,
                    "bLengthChange" : false,
                    "bInfo":false,
                    "order": [[3, 'desc']],
                    data: json['data'],
                    columns: _columns
                });
            }
        });
    };

    function dependentsDataTableActions($id) {
        if ($id) {
            var _actionButton = "";

            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditDependents' data-id='" + $id + "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnRemoveDependents' data-id='" + $id + "'><i class='la la-trash'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $(document).on("click", ".btnDependents", function () {
        $.ajax({
            url: '<?=base_url('crs/online_registration/get_modal/') ?>' + tempDataId + '/' + 'dependents',
            dataType: "json",
            success: function (json) {
                var modalContent = modalTempContent.find('.modal-content');
                if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                    modalContent.empty();
                    modalContent.append(json.html);
                    modalTempContent.modal("show");

                    var dtPickerDepBirthDate = modalContent.find("#dep_birthdate").datepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "yyyy-mm-dd",
                        autoclose: true
                    })
                        .on("changeDate", function (e) {
                            var currentDt = moment(e.date).format("YYYY-MM-DD");
                            var self = $(e.target);
                            self.validate();
                        });

                    $.validate({
                        form: "#form-dependents",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Employee dependent saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalTempContent.modal("hide");
                                        // dtDependents.ajax.reload();
                                        dtDependents();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating employee dependent!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            }
        });
    });

    var dtEducation = function(id = null){
        $.ajax({
            url: '<?=base_url("crs/online_registration/get_employee_educational_bg") ?>',
            type: "post",
            dataType: "json",
            data : {
                csrf_token : _csrf_hash,
                applicant_id : tempDataId 
            },
            success: function(json){
                var ebTable = '';
                let _columns = '';
                if(id){
                    ebTable = $("#tbl-educational_background");
                    _columns = [
                        { data: 'educ_level_type' },
                        { data: 'educ_degree' },
                        { data: 'educ_school' },
                        { data: 'educ_honors' },
                        { data: 'educ_from' },
                        { data: 'educ_to' },
                    ];
                }else{
                    ebTable = $("#tbl-educational_background_list");
                    _columns = [
                        { data: 'educ_level_type' },
                        { data: 'educ_degree' },
                        { data: 'educ_school' },
                        { data: 'educ_honors' },
                        { data: 'educ_from' },
                        { data: 'educ_to' },
                        { 
                            data: "id" ,
                            render: function(data){
                                return educationalDataTableActions(data);
                            }
                        }
                    ];
                }

                ebTable.DataTable({
                    'destroy': true,
                    "searching": false,
                    "bPaginate": false,
                    "bLengthChange" : false,
                    "bInfo":false,
                    data: json['data'],
                    columns: _columns
                });
            }
        });
    }

    function educationalDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditEducational' data-id='" + $id + "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnRemoveEducational' data-id='" + $id + "'><i class='la la-trash'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $(document).on('click', '.btnAddEducation', function(){
        $.ajax({
            url: '<?=base_url('crs/online_registration/get_modal/') ?>' + tempDataId + '/' + 'education',
            dataType: "json",
            success: function (json) {
                var modalContent = modalTempContent.find(".modal-content");
                if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                    modalContent.empty();
                    modalContent.append(json.html);
                    modalContent.find("#educ_level_type").select2({
                        width: "100%",
                        placeholder: "Select an option",
                        dropdownParent: modalTempContent
                    });
                    modalTempContent.modal("show");

                    var dtPickerEducationFromDate = modalContent.find("#educ_from").datepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "yyyy",
                        viewMode: "years",
                        minViewMode: "years",
                        autoclose: true
                    })
                        .on("changeDate", function (e) {
                            var currentDt = moment(e.date).format("YYYY");
                            var self = $(e.target);
                            self.validate();
                        });

                    var dtPickerEducationToDate = modalContent.find("#educ_to").datepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "yyyy",
                        viewMode: "years",
                        minViewMode: "years",
                        autoclose: true
                    })
                        .on("changeDate", function (e) {
                            var currentDt = moment(e.date).format("YYYY");
                            var self = $(e.target);
                            self.validate();
                        });

                    $.validate({
                        form: "#form-education",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Employee educational background has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalTempContent.modal("hide");
                                        // dtEducationalBg.ajax.reload();
                                        dtEducation();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating employee educational background!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            }
        });
    });

    var dtLicense = function(id = null) {
        $.ajax({
            url: '<?=base_url("crs/online_registration/get_employee_licensure") ?>',
            type: "post",
            dataType: "json",
            data : {
                csrf_token : _csrf_hash,
                applicant_id : tempDataId
            },
            success: function(json){

                var license = '';
                let _columns = '';
                if(id){
                    license = $("#tbl-licensure_exams");
                    _columns = [
                        { data: "license_type", title: "License/Exam Type" },
                        { data: "exam_place", title: "Exam Place" },
                        { data: "rating", title: "Rating" },
                        { data: "release_date", title: "Released Date" },
                        { data: "exam_date", title: "Exam Date" },
                        { data: "license_no", title: "License No" },
                    ];
                }else{
                    license = $("#tbl-licensure_exams_list");
                    _columns = [
                        { data: "license_type", title: "License/Exam Type" },
                        { data: "exam_place", title: "Exam Place" },
                        { data: "rating", title: "Rating" },
                        { data: "release_date", title: "Released Date" },
                        { data: "exam_date", title: "Exam Date" },
                        { data: "license_no", title: "License No" },
                        { 
                            data: "id" ,
                            render: function(data){
                                return licensureDataTableActions(data);
                            }
                        }
                    ];
                }

                license.DataTable({
                    'destroy': true,
                    "searching": false,
                    "bPaginate": false,
                    "bLengthChange" : false,
                    "bInfo":false,
                    data: json['data'],
                    columns: _columns
                });
            }
        });
    };

    function licensureDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditLicensure' data-id='" + $id + "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnRemoveLicensure' data-id='" + $id + "'><i class='la la-trash'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $(document).on("click", ".btnAddLicense", function () {
        $.ajax({
            url: '<?=base_url('crs/online_registration/get_modal/') ?>' + tempDataId + '/' + 'licensure',
            dataType: "json",
            success: function (json) {
                var modalContent = modalTempContent.find(".modal-content");
                if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                    modalContent.empty();
                    modalContent.append(json.html);
                    modalTempContent.modal("show");

                    var dtPickerLicensureReleaseDate = modalContent.find("#release_date").datepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "yyyy-mm-dd",
                        autoclose: true,
                        parentEl: "#modalTempContent #form-licensure"
                    }).on("changeDate", function (e) {
                        var currentDt = moment(e.date).format("YYYY-MM-DD");
                        var self = $(e.target);
                        self.validate();
                    });

                    console.log(modalContent.find(':last-child'));
                    var dtPickerLicensureExamDate = modalContent.find("#exam_date").daterangepicker({
                        buttonClasses: 'm-btn btn',
                        applyClass: 'btn-primary',
                        cancelClass: 'btn-secondary',
                        showDropdowns: true,
                        autoUpdateInput: false,
                        parentEl: "#modalTempContent .modal-content",
                        locale: {
                            cancelLabel: 'Clear'
                        },
                        alwaysShowCalendars: false
                    }).on("apply.daterangepicker", function (e, picker) {
                        picker.element.val(picker.startDate.format(picker.locale.format) + ' - ' + picker.endDate.format(picker.locale.format));

                        var self = $(e.target);
                        self.validate();
                    });

                    $.validate({
                        form: "#form-licensure",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Employee licensure Exams and certification has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalTempContent.modal("hide");
                                        // dtLicensureExam.ajax.reload();
                                        dtLicense();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating employee licensure Exams and certification!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            }
        });
    });

    var dtDrivers = function(id = null){
        $.ajax({
            url: '<?=base_url("crs/online_registration/get_employee_driverlicense") ?>',
            type: "post",
            dataType: "json",
            data : {
                csrf_token : _csrf_hash,
                applicant_id : tempDataId
            },
            success: function(json){
                var dl = '';
                let _columns = '';
                if(id){
                    dl = $("#tbl-dl");
                    _columns = [
                        { data: "restriction", title: "Restriction" },
                        { data: "license_no", title: "License No." },
                        { data: "expiration_date", title: "Exp Date" },
                    ];
                }else{
                    dl = $("#tbl-driverlicense");
                    _columns = [
                        { data: "restriction", title: "Restriction" },
                        { data: "license_no", title: "License No." },
                        { data: "expiration_date", title: "Exp Date" },
                        { 
                            data: "id" ,
                            render: function(data){
                                return driverlicenseDataTableActions(data);
                            }
                        }
                    ];
                }

                dl.DataTable({
                    'destroy': true,
                    "searching": false,
                    "bPaginate": false,
                    "bLengthChange" : false,
                    "bInfo":false,
                    data: json['data'],
                    columns: _columns
                });
            }
        });
    }

    function driverlicenseDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditDriverLicense' data-id='" + $id + "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnRemoveDriverLicense' data-id='" + $id + "'><i class='la la-trash'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $(document).on("click", ".btnAddDL", function () {
        $.ajax({
            url: '<?=base_url('crs/online_registration/get_modal/') ?>' + tempDataId + '/' + 'driverlicense',
            dataType: "json",
            success: function (json) {
                var modalContent = modalTempContent.find(".modal-content");
                if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                    modalContent.empty();
                    modalContent.append(json.html);
                    modalTempContent.modal("show");

                    var dtPickerLicensureReleaseDate = modalContent.find("#expiration_date").datepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "yyyy-mm-dd",
                        autoclose: true
                    })
                        .on("changeDate", function (e) {
                            var currentDt = moment(e.date).format("YYYY-MM-DD");
                            var self = $(e.target);
                            self.validate();
                        });

                    $.validate({
                        form: "#form-driverlicense",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Employee Driver's License has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalTempContent.modal("hide");
                                        // dtLicensure.ajax.reload();
                                        dtDrivers();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating employee driver's license!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            }
        });
    });

    var dtWork = function(id = null){
        $.ajax({
            url: '<?=base_url("crs/online_registration/get_employee_work_experience") ?>',
            type: "post",
            dataType: "json",
            data : {
                csrf_token : _csrf_hash,
                applicant_id : tempDataId 
            },
            success: function(json){
                var work = '';
                let _columns = '';
                if(id){
                    work = $("#tbl-work_experiences");
                    _columns = [
                        { data: "work_company", title: "Company" },
                        { data: "work_from", title: "From" },
                        { data: "work_to", title: "To" },
                        { data: "work_position", title: "Position" },
                        { data: "work_status", title: "Status" },
                        { data: "work_reason", title: "Reason For Leaving" },
                    ];
                }else{
                    work = $("#tbl-work_experiences_list");
                    _columns = [
                        { data: "work_company", title: "Company" },
                        { data: "work_from", title: "From" },
                        { data: "work_to", title: "To" },
                        { data: "work_position", title: "Position" },
                        // { data: "old_idno", title: "ID No" },
                        { data: "work_status", title: "Status" },
                        { data: "work_reason", title: "Reason For Leaving" },
                        { 
                            data: "id" ,
                            render: function(data){
                                return workExperienceDataTableActions(data);
                            }
                        }
                    ];
                }

                work.DataTable({
                    'destroy': true,
                    "searching": false,
                    "bPaginate": false,
                    "bLengthChange" : false,
                    "bInfo":false,
                    data: json['data'],
                    columns: _columns
                });
            }
        });
    }

    function workExperienceDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditWorkExperience' data-id='" + $id + "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnRemoveWorkExperience' data-id='" + $id + "'><i class='la la-trash'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $(document).on("click", ".btnAddWork", function () {
        $.ajax({
            url: '<?=base_url('crs/online_registration/get_modal/') ?>' + tempDataId + '/' + 'work_experience',
            dataType: "json",
            success: function (json) {
                var modalContent = modalTempContent.find(".modal-content");
                if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);
                        modalTempContent.modal("show");

                        var dtPickerWorkFromDate = modalContent.find("#work_from").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy",
                            viewMode: "years",
                            minViewMode: "years",
                            autoclose: true
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY");
                                var self = $(e.target);
                                self.validate();
                            });

                        var dtPickerWorkToDate = modalContent.find("#work_to").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy",
                            viewMode: "years",
                            minViewMode: "years",
                            autoclose: true
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY");
                                var self = $(e.target);
                                self.validate();
                            });

                        $.validate({
                            form: "#form-work_experience",
                            lang: "en",
                            onSuccess: function (form) {
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();

                                $.ajax({
                                    url: formUrl,
                                    type: "post",
                                    dataType: "json",
                                    data: formData,
                                    beforeSend: function () {
                                        $(currentForm)
                                            .find(".btn-submit")
                                            .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                    },
                                    success: function (json) {
                                        if (json.response) {
                                            toastr.success(
                                                json.toastr_msg,
                                                "Employee work experience has been saved.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalTempContent.modal("hide");
                                            // dtWorkExperience.ajax.reload();
                                            dtWork();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating employee work experience!",
                                                5000
                                            );
                                        }

                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
            }
        });
    });

    var dtAwards = function(id = null){
        $.ajax({
            url: '<?=base_url("crs/online_registration/get_employee_awards") ?>',
            type: "post",
            dataType: "json",
            data : {
                csrf_token : _csrf_hash,
                applicant_id : tempDataId 
            },
            success: function(json){
                var award = '';
                let _columns = '';
                if(id){
                    award = $("#tbl-awards");
                    _columns = [
                        { data: "award", title: "Awards/Achievement" },
                        { data: "award_institution", title: "Instutution" },
                        { data: "award_date", title: "Given Date", width: "10%" },
                    ];
                }else{
                    award = $("#tbl-awards_list");
                    _columns = [
                        { data: "award", title: "Awards/Achievement" },
                        { data: "award_institution", title: "Instutution" },
                        { data: "award_date", title: "Given Date", width: "10%" },
                        { 
                            data: "id" ,
                            render: function(data){
                                return awardsDataTableActions(data);
                            }
                        }
                    ];
                }

                award.DataTable({
                    'destroy': true,
                    "searching": false,
                    "bPaginate": false,
                    "bLengthChange" : false,
                    "bInfo":false,
                    data: json['data'],
                    columns: _columns
                });
            }
        });
    }

    function awardsDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditAwards' data-id='" + $id + "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnRemoveAwards' data-id='" + $id + "'><i class='la la-trash'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $(document).on("click", ".btnAddAward", function () {
        $.ajax({
            url: '<?=base_url('crs/online_registration/get_modal/') ?>' + tempDataId + '/' + 'awards',
            dataType: "json",
            success: function (json) {
                var modalContent = modalTempContent.find(".modal-content");
                if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                    modalContent.empty();
                    modalContent.append(json.html);
                    modalTempContent.modal("show");

                    var dtPickerAwardDate = modalContent.find("#award_date").datepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "yyyy-mm-dd",
                        autoclose: true
                    })
                        .on("changeDate", function (e) {
                            var currentDt = moment(e.date).format("YYYY-MM-DD");
                            var self = $(e.target);
                            self.validate();
                        });

                    $.validate({
                        form: "#form-awards",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Employee award and achievement has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalTempContent.modal("hide");
                                        // dtAwards.ajax.reload();
                                        dtAwards();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating employee award and achievement!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            }
        });
    });

    var dtOrg = function(id = null){
        $.ajax({
            url: '<?=base_url("crs/online_registration/get_employee_organization") ?>',
            type: "post",
            dataType: "json",
            data : {
                csrf_token : _csrf_hash,
                applicant_id : tempDataId 
            },
            success: function(json){
                var org = '';
                let _columns = '';
                if(id){
                    org = $("#tbl-organizations");
                    _columns = [
                        { data: "org_institution", title: "Instutution" },
                        { data: "org_membership_title", title: "Membership title" },
                        { 
                            data: "org_from", title: "From" ,
                            render: function (data) {
                                return moment(data).format("YYYY");
                            }
                        },
                        { 
                            data: "org_to", 
                            title: "To",
                            render: function (data) {
                                return moment(data).format("YYYY");
                            }
                        },
                    ];
                }else{
                    org = $("#tbl-organizations_list");
                    _columns = [
                        { data: "org_institution", title: "Instutution" },
                        { data: "org_membership_title", title: "Membership title" },
                        { 
                            data: "org_from", title: "From" ,
                            render: function (data) {
                                return moment(data).format("YYYY");
                            }
                        },
                        { 
                            data: "org_to", 
                            title: "To",
                            render: function (data) {
                                return moment(data).format("YYYY");
                            }
                        },
                        { 
                            data: "id" ,
                            render: function(data){
                                return organizationDataTableActions(data);
                            }
                        }
                    ];
                }

                org.DataTable({
                    'destroy': true,
                    "searching": false,
                    "bPaginate": false,
                    "bLengthChange" : false,
                    "bInfo":false,
                    data: json['data'],
                    columns: _columns
                });
            }
        });
    }

    function organizationDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditOrganization' data-id='" + $id + "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnRemoveOrganization' data-id='" + $id + "'><i class='la la-trash'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $(document).on("click", ".btnAddOrg", function () {
        $.ajax({
            url: '<?=base_url('crs/online_registration/get_modal/') ?>' + tempDataId + '/' + 'organization',
            dataType: "json",
            success: function (json) {
                var modalContent = modalTempContent.find(".modal-content");
                if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                    modalContent.empty();
                    modalContent.append(json.html);
                    modalTempContent.modal("show");

                    var dtPickerOrgFromDate = modalContent.find("#org_from").datepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "yyyy",
                        viewMode: "years",
                        minViewMode: "years",
                        autoclose: true
                    })
                        .on("changeDate", function (e) {
                            var currentDt = moment(e.date).format("YYYY");
                            var self = $(e.target);
                            self.validate();
                        });

                    var dtPickerOrgToDate = modalContent.find("#org_to").datepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "yyyy",
                        viewMode: "years",
                        minViewMode: "years",
                        autoclose: true
                    })
                        .on("changeDate", function (e) {
                            var currentDt = moment(e.date).format("YYYY");
                            var self = $(e.target);
                            self.validate();
                        });

                    $.validate({
                        form: "#form-organization",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Employee organization has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalTempContent.modal("hide");
                                        // dtOrganization.ajax.reload();
                                        dtOrg();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating employee organization!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            }
        });
    });

    var dtTraining = function(id = null){
        $.ajax({
            url: '<?=base_url("crs/online_registration/get_employee_training") ?>',
            type: "post",
            dataType: "json",
            data : {
                csrf_token : _csrf_hash,
                applicant_id : tempDataId 
            },
            success: function(json){
                var trainings = '';
                let _columns = '';
                if(id){
                    trainings = $("#tbl-trainings");
                    _columns = [
                        { data: "training", title: "Training" },
                        { data: "train_from", title: "From" },
                        { data: "train_to", title: "To" },
                        { data: "train_institution", title: "Institution" },
                        { data: "train_conductor", title: "Conductor" },
                        { data: "train_venue", title: "Venue" },
                    ];
                }else{
                    trainings = $("#tbl-trainings_list");
                    _columns = [
                        { data: "training", title: "Training" },
                        { data: "train_from", title: "From" },
                        { data: "train_to", title: "To" },
                        { data: "train_institution", title: "Institution" },
                        { data: "train_conductor", title: "Conductor" },
                        { data: "train_venue", title: "Venue" },
                        { 
                            data: "id" ,
                            render: function(data){
                                return trainingsDataTableActions(data);
                            }
                        }
                    ];
                }

                trainings.DataTable({
                    'destroy': true,
                    "searching": false,
                    "bPaginate": false,
                    "bLengthChange" : false,
                    "bInfo":false,
                    data: json['data'],
                    columns: _columns
                });
            }
        });
    }

    function trainingsDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditTrainings' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnRemoveTrainings' data-id='" + $id + "'><i class='la la-trash'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $(document).on("click", ".btnAddTraining", function () {
        $.ajax({
            url: '<?=base_url('crs/online_registration/get_modal/') ?>' + tempDataId + '/' + 'training',
            dataType: "json",
            success: function (json) {
                var modalContent = modalTempContent.find(".modal-content");
                if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                    modalContent.empty();
                    modalContent.append(json.html);
                    modalTempContent.modal("show");

                    var dtPickertrainingFromDate = modalContent.find("#train_from").datepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "yyyy-mm-dd",
                        autoclose: true
                    }).on("changeDate", function (e) {
                        var currentDt = moment(e.date).format("YYYY-MM-DD");
                        var self = $(e.target);
                        self.validate();
                    });

                    var dtPickerTrainingToDate = modalContent.find("#train_to").datepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "yyyy-mm-dd",
                        autoclose: true
                    }).on("changeDate", function (e) {
                        var currentDt = moment(e.date).format("YYYY-MM-DD");
                        var self = $(e.target);
                        self.validate();
                    });

                    $.validate({
                        form: "#form-trainings",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Employee training and seminar has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalTempContent.modal("hide");
                                        // dtTrainings.ajax.reload();
                                        dtTraining();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating employee training and seminar!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            }
        });
    });

    var dtPersonal = function(id = null){
        $.ajax({
            url: '<?=base_url("crs/online_registration/get_employee_personal_reference") ?>',
            type: "post",
            dataType: "json",
            data : {
                csrf_token : _csrf_hash,
                applicant_id : tempDataId
            },
            success: function(json){
                var personal_references = '';
                let _columns = '';
                if(id){
                    personal_references = $("#tbl-personal_references");
                    _columns = [
                        { data: "ref_name", title: "Name" },
                        { data: "ref_contact_no", title: "Contact No" },
                        { data: "ref_address", title: "Address" },
                    ];
                }else{
                    personal_references = $("#tbl-personal_references_list");
                    _columns = [
                        { data: "ref_name", title: "Name" },
                        { data: "ref_contact_no", title: "Contact No" },
                        { data: "ref_address", title: "Address" },
                        { 
                            data: "id" ,
                            render: function(data){
                                return personalReferenceDataTableActions(data);
                            }
                        }
                    ];
                }

                personal_references.DataTable({
                    'destroy': true,
                    "searching": false,
                    "bPaginate": false,
                    "bLengthChange" : false,
                    "bInfo":false,
                    data: json['data'],
                    columns: _columns
                });
            }
        });
    }

    function personalReferenceDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditPersonalReference' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnRemovePersonalReference' data-id='" + $id + "'><i class='la la-trash'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $(document).on("click", ".btnAddPersonal", function () {
        $.ajax({
            url: '<?=base_url('crs/online_registration/get_modal/') ?>' + tempDataId + '/' + 'personal_references',
            dataType: "json",
            success: function (json) {
                var modalContent = modalTempContent.find(".modal-content");
                if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                    modalContent.empty();
                    modalContent.append(json.html);
                    modalTempContent.modal("show");

                    $.validate({
                        form: "#form-references",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Employee personal reference has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalTempContent.modal("hide");
                                        // dtPersonalReference.ajax.reload();
                                        dtPersonal();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating employee personal reference!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            }
        });
    });

    var dtMedical = function(id = null){
        $.ajax({
            url: '<?=base_url("crs/online_registration/get_employee_medical_history") ?>',
            type: "post",
            dataType: "json",
            data : {
                csrf_token : _csrf_hash,
                applicant_id : tempDataId 
            },
            success: function(json){
                var medical_records = '';
                let _columns = '';
                if(id){
                    medical_records = $("#tbl-medical_records");
                    _columns = [
                        { data: "med_details", title: "Details" },
                        { data: "med_no", title: "Med. No" },
                        { data: "med_date", title: "Date" },
                        { data: "med_venue", title: "Venue" },
                        { data: "med_physician", title: "Physician" },
                        { data: "med_findings", title: "Findings" },
                        { data: "remarks", title: "Remarks" },
                    ];
                }else{
                    medical_records = $("#tbl-medical_records_list");
                    _columns = [
                        { data: "med_details", title: "Details" },
                        { data: "med_no", title: "Med. No" },
                        { data: "med_date", title: "Date" },
                        { data: "med_venue", title: "Venue" },
                        { data: "med_physician", title: "Physician" },
                        { data: "med_findings", title: "Findings" },
                        { data: "remarks", title: "Remarks" },
                        { 
                            data: "id" ,
                            render: function(data){
                                return medicalHistoryDataTableActions(data);
                            }
                        }
                    ];
                }

                medical_records.DataTable({
                    'destroy': true,
                    "searching": false,
                    "bPaginate": false,
                    "bLengthChange" : false,
                    "bInfo":false,
                    data: json['data'],
                    columns: _columns
                });
            }
        });
    }

    function medicalHistoryDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditMedicalHistory' data-id='" + $id + "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnRemoveMedicalHistory' data-id='" + $id + "'><i class='la la-trash'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $(document).on("click", ".btnAddMedical", function () {
        $.ajax({
            url: '<?=base_url('crs/online_registration/get_modal/') ?>' + tempDataId + '/' + 'medical_history',
            dataType: "json",
            success: function (json) {
                var modalContent = modalTempContent.find(".modal-content");
                if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                    modalContent.empty();
                    modalContent.append(json.html);
                    modalTempContent.modal("show");

                    var dtPickerMedicalDate = modalContent.find("#med_date").datepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "yyyy-mm-dd",
                        autoclose: true
                    }).on("changeDate", function (e) {
                        var currentDt = moment(e.date).format("YYYY-MM-DD");
                        var self = $(e.target);
                        self.validate();
                    });

                    $.validate({
                        form: "#form-medical_history",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Employee medical history/record has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalTempContent.modal("hide");
                                        // dtMedicalHistory.ajax.reload();
                                        dtMedical();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating employee medical history/record!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            }
        });
    });

    var dtSkill = function(id = null){
        $.ajax({
            url: '<?=base_url("crs/online_registration/get_employee_skills") ?>',
            type: "post",
            dataType: "json",
            data : {
                csrf_token : _csrf_hash,
                applicant_id : tempDataId 
            },
            success: function(json){
                var skills = '';
                let _columns = '';
                if(id){
                    skills = $("#tbl-skills");
                    _columns = [
                        { data: "skills", title: "Skills", width: "80%" },
                    ];
                }else{
                    skills = $("#tbl-skills-list");
                    _columns = [
                        { data: "skills", title: "Skills", width: "80%" },
                        { 
                            data: "id" ,
                            render: function(data){
                                return skillTableAction(data);
                            }
                        }
                    ];
                }

                skills.DataTable({
                    'destroy': true,
                    "searching": false,
                    "bPaginate": false,
                    "bLengthChange" : false,
                    "bInfo":false,
                    data: json['data'],
                    columns: _columns
                });
            }
        });
    }

    function skillTableAction($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' " +
                "         class='btn btn-default m-btn m-btn--hover-accent m-btn--icon " +
                "                m-btn--icon-only m-btn--pill btnEditSkill' " +
                "         data-id='" + $id + "'>" +
                "           <i class='la la-edit'></i>" +
                " </button>";
            _actionButton +=
                " <button type='button' " +
                "         class='btn btn-default m-btn m-btn--hover-warning " +
                "                m-btn--icon m-btn--icon-only m-btn--pill btnRemoveSkill' " +
                "         data-id='" + $id + "'>" +
                "           <i class='la la-trash'></i>" +
                " </button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $(document).on('click', '.btnAddSkills', function() {
        $.ajax({
            url: '<?=base_url('crs/online_registration/get_modal/') ?>' + tempDataId + '/' + 'skill',
            dataType: "json",
            success: function(json){
                var modalContent = modalTempContent.find(".modal-content");
                if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                    modalContent.empty();
                    modalContent.append(json.html);
                    modalTempContent.modal("show");

                    $.validate({
                        form: "#form-skill",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Skill has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalTempContent.modal("hide");
                                        // dtPersonalReference.ajax.reload();
                                        dtSkill();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating Skill!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            }
        });
    });
    // other application

    function initRegularEditDialog(response) {
        const html = response.html;
        const _modal = $('#document-modal-container');
        _modal.empty();
        _modal.append(html);

        _modal.find("input.date")
            .datepicker({
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                },
                format: "yyyy-mm-dd",
                autoclose: true
            });

        _modal.find(".date-range")
            .daterangepicker({
                buttonClasses: 'm-btn btn',
                applyClass: 'btn-primary',
                cancelClass: 'btn-secondary',
                showDropdowns: true
            }, function (start, end, label) {
                $('.date-range .form-control')
                    .val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
            });

        _modal.find(".date-year")
            .datepicker({
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                },
                format: "yyyy",
                viewMode: "years",
                minViewMode: "years",
                autoclose: true
            });
        _modal.modal('show');
    }

    function initEditEducationalBackground(response) {
        const html = response.html;
        const data = response.info.data;
        const _modal = $('#document-modal-container');
        _modal.empty();
        _modal.append(html);

        const educ_level_type = _modal.find("#educ_level_type");
        const _educ_level_type = data.educ_level_type;
        educ_level_type.val(_educ_level_type).trigger('change');
        educ_level_type
            .select2({
                placeholder: "Select Education Type",
                width: "100%",
                dropdownParent: $("#document-modal-container")
            });
        _modal.find(".date")
            .datepicker({
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                },
                format: "yyyy",
                viewMode: "years",
                minViewMode: "years",
                autoclose: true
            });
        _modal.modal('show');
    }

    var array_file_ext = ["doc", "pdf", "jpg", 'png'];
    var temp_images = function () {
        var url = '<?php echo base_url("crs/online_registration/add_temp_file"); ?>';
        var body_id = $("input[name=document_body_id]").val();

        $("#temp_fileupload").fileupload({
            url: url,
            dataType: "json",
            formData: {
                csrf_token: '<?php echo $this->security->get_csrf_hash(); ?>',
                public_user_id: '<?php echo $_COOKIE['public_user_id']; ?>',
                body_id: body_id
            },
            done: function (e, data) {
                $("#progress").removeClass("d-none");
                $("#progress").show();
                var result = data.result;
                $("#attachment").val(JSON.stringify(result));
                if(result == 0){
                    alert("File is not supported! File is not uploaded!");
                }
                vmData.counts = result.length;
                vmData.rows = Object.assign({}, vmData.rows, result);  
            },
            progressall: function (e, data) {
                var progress = parseInt((data.loaded / data.total) * 100, 10);
                var progressTotal = 0;

                var steps = setInterval(function () {
                    progressTotal += 10;
                    $("#progress .progress-bar").css("width", progressTotal + "%");
                    if (progressTotal == 100) {
                        clearInterval(steps);
                        progressTotal = 0;
                        setTimeout(function () {
                            $("#progress .progress-bar").css("width", progressTotal + "%");
                        }, 1500);
                    }
                }, 10);

                if (progress == 100) {
                    setTimeout(function () {
                        $("#progress").hide();
                    }, 1000);
                }
            }
        })
        .prop("disabled", !$.support.fileInput)
        .parent()
        .addClass($.support.fileInput ? undefined : "disabled");
    };

    var vmData = new Vue({
        el: "#login-box",
        data: { 
            row: {},
            tempId: 0,
            count: 0,
            rows: [],
            className: "",
            counts: 0
        },
        mounted(){
        },
        methods: {
            strFormat: function(str){
                var val = str;
                if(str){
                    val = val.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                        return letter.toUpperCase();
                    });
                }

                return val;
            },
            strLength: function(str){
                var count = str.replace(/ /g, '').length;
                return count;
            },
            removeComma: function(str){
                const [_, ...rest] = str.split(',');
                var newStr = rest.join(', ');
                return newStr;
            },
            getExtension: function(file){
                var type = file.split('.').pop();
                if(array_file_ext.includes(type)){
                    switch(type) {
                    case "doc":
                        return '<?php echo base_url("assets/images/file_icons/doc.svg"); ?>';
                        
                    case "pdf":
                        return '<?php echo base_url("assets/images/file_icons/pdf.svg"); ?>';
                        
                    case "jpg":
                        return '<?php echo base_url("assets/images/file_icons/jpg.svg"); ?>';
                    
                    case "png":
                        return '<?php echo base_url("assets/images/file_icons/jpg.svg"); ?>';
                        
                    default:
                        return '<?php echo base_url("assets/images/file_icons/default.svg"); ?>';
                    }
                }
            },
            getClass: function(classes){
                var type = classes.split('.').pop();
                if(array_file_ext.includes(type)){
                    switch(type) {
                    case "doc":
                        return "m-widget4 m-widget2__item m-widget2__item--primary col-lg-12";
                            
                    case "pdf":
                        return "m-widget4 m-widget2__item m-widget2__item--danger col-lg-12";
                        
                    case "jpg":
                        return "m-widget4 m-widget2__item m-widget2__item--success col-lg-12";
                    
                    case "png":
                        return "m-widget4 m-widget2__item m-widget2__item--success col-lg-12";
                        
                    default:
                        return "m-widget4 m-widget2__item m-widget2__item--default col-lg-12";
                    }
                }
            },
            fileDelete: function(id, public_user_id){
                var body_id = $("input[name=document_body_id]").val();
                $.ajax({
                    url: '<?php echo base_url("crs/online_registration/temp_file_delete"); ?>',
                    type: "post",
                    data: {
                        csrf_token: '<?php echo $this->security->get_csrf_hash(); ?>',
                        id: id,
                        public_user_id: public_user_id,
                        body_id: body_id
                    },
                    dataType: "json",
                    success: function(resp){
                        vmData.rows = Object.assign({});
                        vmData.rows = Object.assign({}, resp);
                        vmData.counts = resp.length;
                    }
                });
            }
        }
    });

    function saveResumeInfo(formElement, e){
        e.preventDefault();
        grecaptcha.ready(() => {
            grecaptcha.render(document.getElementById('captcha'), {
            'sitekey' : '6LfYy9QlAAAAAHNzof6dl4R4O_GkFrFeyRc42zdU'
            });
        });
        const form = $(formElement);
    
        var count = $(".m-widget4__text").text();
        if(form.isValid()){
            if(count){
                const formData = new FormData($("#submit-resume")[0]);
                $.ajax({
                    url: '<?php echo base_url("crs/online_registration/insert_uploaded_resume"); ?>',
                    dataType: "JSON",
                    type: "POST",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(data){
                        if (data.result) {
                            toastr.success(data.message, "New Resume Saved.", 10000);
                            window.location.href = "<?php echo base_url("crs/online_registration/thank_you"); ?>"
                        }else{
                            toastr.error(data.message, "New Resume Not Saved.", 10000);
                        }
                    }
                });
            }else{
                toastr.error("Upload your resume", "Resume Not Saved.", 10000);
            }
        }
    }
</script>
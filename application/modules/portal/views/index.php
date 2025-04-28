<!DOCTYPE html>
<?php $session = $this->session;
$logged_in = $session->userdata("logged_in");
if (!$logged_in) {
    $this->session->sess_destroy();
    $redirect = base_url();
    header("location: {$redirect}");
}

$select = array("id", "idno", "biometricno", "lastname", "firstname", "middlename", "email",
    "position", "employee_status", "curr_addr", "str_addr", "city", "postal", "pic_filename");
$user = json_decode(json_encode($this->authenticate->getUserData($select), TRUE));
$profile_image_path = "uploads/files/images/employee_files/empcode_" . $user->id . "/" . $user->pic_filename;
$avatar = null;
$version = $this->authenticate->getCurrentVersion();

if (file_exists($profile_image_path)) {
    $avatar = $profile_image_path;
} else {
    $avatar = "assets/images/profile/no_image.jpg";
}

// privileges
$privArr = array();
$privAcl = array();
foreach(array_keys($this->core_layout->generatePrivileges()) as $priv){
    $privArr[] = $priv;
}

if(in_array("eforms_loa", $privArr)){
    $loa_module = true;
}
if(in_array("tr_masterfile", $privArr)){
    $tr_module = true;
}
if(in_array("to_masterfile", $privArr)){
    $to_module = true;
}
if(in_array("ship_masterfile", $privArr)){
    $ship_module = true;
}
if(in_array("accountability_masterfile", $privArr)){
    $accountability_module = true;
}
if(in_array("overtime_masterfile", $privArr)){
    $overtime_module = true;
}
if(in_array("ca_masterfile", $privArr)){
    $ca_module = true;
}
if(in_array("eforms_rtw_masterfile", $privArr)){
    $rtw_module = true;
}
if(in_array("borr_masterlist", $privArr)){
    $borrowing_module = true;
}

if(in_array("billing_masterfile", $privArr)){
    $billing_module = true;
}

?>
<html lang="en" class="wf-poppins-n3-active wf-poppins-n6-active 
wf-poppins-n7-active wf-roboto-n3-active wf-poppins-n4-active 
wf-poppins-n5-active wf-roboto-n4-active wf-roboto-n5-active 
wf-roboto-n6-active wf-roboto-n7-active wf-active">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>GC&amp;C | Portal</title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--begin::Web font -->

    <script src="<?php echo base_url('assets/js/vue.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/jquery-3.3.1.min.js'); ?>"></script>

    <!--begin::Base Scripts -->
    <script src="<?php echo base_url("assets/vendors/base/vendors.bundle.js"); ?>" type="text/javascript"></script>
    <script src="<?php echo base_url("assets/demo/demo3/base/scripts.bundle.js"); ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/js/form-validator/jquery.form-validator.min.js'); ?>"></script>

    <!--end::Base Scripts -->
    <script src="<?php echo base_url('assets/plugins/bootstrap/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/jquery.dataTables.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/dataTables.bootstrap4.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/custom.js'); ?>?v=1.0.1"></script>
    <?php echo $this->core_layout->getStoredJs(); ?>

    <!--end::Web font -->
    <link href="<?php echo base_url("assets/fonts/montserrat/montserrat.css"); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url("assets/fonts/roboto/roboto.css"); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url("assets/vendors/custom/datatables/datatables.bundle.css"); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url("assets/plugins/bootstrap/bootstrap.min.css"); ?>" rel="stylesheet"
          type="text/css"/>
    <link href="<?php echo base_url("assets/vendors/base/vendors.bundle.css"); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url("assets/demo/demo3/base/style.bundle.css"); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url("assets/css/custom.css"); ?>" rel="stylesheet" type="text/css"/>
    <?php echo $this->core_layout->getStoredCss(); ?>
    
    <link rel="shortcut icon" type="image/png" sizes="32x32" href="<?php echo base_url("assets/favicon.ico"); ?>">
    <script>
        setBaseUrl("<?php echo base_url(); ?>");
        setSiteUrl("<?php echo site_url(); ?>");
        setCrfSecurityToken("<?php echo $this->security->get_csrf_token_name(); ?>", "<?php echo $this->security->get_csrf_hash(); ?>");
        const idleTimerState = <?php echo json_encode($this->core_layout->getIdleTimerState()); ?>;
    </script>
    <style type="text/css">
        .im-caret {
            -webkit-animation: 1s blink step-end infinite;
            animation: 1s blink step-end infinite;
        }

        @keyframes blink {
            from, to {
                border-right-color: black;
            }
            50% {
                border-right-color: transparent;
            }
        }

        @-webkit-keyframes blink {
            from, to {
                border-right-color: black;
            }
            50% {
                border-right-color: transparent;
            }
        }

        .im-static {
            color: grey;
        }

        .icon {
            position: absolute;
            right: 15px;
            top: 0px;
            font-size: 6.5rem;
        }

        .icon .custom-large_icon {
            font-size: inherit;
            color: rgba(0, 0, 0, 0.15);
        }

        .row.row-navigation_icon a {
            text-decoration: none;
        }

        .row.row-navigation_icon .m-portlet__body {
            color: #FFFFFF !important;
        }

        .m-portlet.m-portlet--fit.m-portlet--skin-dark.m--bg-disabled {
            background-color: #C6C6C6 !important;
        }

        .m-widget1{
            padding-top: 10px !important;
        }

        .m-widget1__item{
            color: rgba(208, 192, 227, 0.11) !important;
        }

        .m-widget1__desc{
            font-size: 12px;
            white-space: nowrap;
        }

        .m-widget1__title{
            white-space: nowrap;
        }

        #portal_notifications a{
            text-decoration: none !important
        }

        #portal_notifications .m-widget1__item:hover{
            
        }
        .company-section:hover{
          background: #efebeb
        }

        @media (min-width: 280px) and (max-width: 1280px){
            .m-widget1__title {
                white-space: normal !important;
            }
            .m-widget1__desc {
                white-space: normal !important;
            }
        }
        
        @media screen and (max-width: 420px){
            .flex-wrap{
                flex-wrap: wrap;
            }
            .d-one{
                display: flex;
                flex-wrap: wrap;
                margin-right: 0 !important;
                margin-top: 20px !important;
            }
            .d-one #col1{
                flex: 0 0 30%;
                max-width: 30%;
            }
            .d-one #col2{
                flex: 0 0 100%;
                max-width: 100%;
                margin: 0 !important;
            }

            .d-two{
                display: flex;
                justify-content: end;
            }

            .d-two #col0{
                margin-right: 0 !important;
            }

            .d-two #col1{
                margin-right: 0 !important;
            }

        }

        .d-two{
          display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
        }

        @media (min-width: 375px) and (max-width: 600px){
            .m-subheader__daterange-date{
                margin-left: 20px;
            }
        }


        .m-portlet__head-text, .m-portlet__head-icon{
            color: #233e6b !important;
        }
        
        .m-subheader .m-subheader__title {
            display: inline-block;
            border-right: 1px solid #e2e5ec;
        }
    </style>
</head>
<!-- end::Head -->
<!-- end::Body -->
<body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside--offcanvas-default">
<!-- begin:: Page -->
<div class="m-grid m-grid--hor m-grid--root m-page">
    <!-- begin::Header -->
    <header class="m-grid__item m-header" data-minimize-offset="200" data-minimize-mobile-offset="200">
        <div class="m-container m-container--fluid m-container--full-height">
            <div class="m-stack m-stack--ver m-stack--desktop">
                <!-- BEGIN: Brand -->
                <div class="m-stack__item m-brand  m-brand--skin-dark ">
                    <div class="m-stack m-stack--ver m-stack--general">
                        <div class="m-stack__item m-stack__item--middle m-stack__item--center m-brand__logo">
                            <a href="<?php echo site_url(); ?>" class="m-brand__logo-wrapper">
                                <img alt="" src="<?php echo base_url('assets/logo.png') ?>" width="66px" width="44px"/>
                            </a>
                        </div>
                        <div class="m-stack__item m-stack__item--middle m-brand__tools">
                            <!-- BEGIN: Responsive Aside Left Menu Toggler -->
                            <a href="javascript:;" id="m_aside_left_offcanvas_toggle"
                               class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-tablet-and-mobile-inline-block">
                                <span></span>
                            </a>
                            <!-- END -->
                            <!-- BEGIN: Responsive Header Menu Toggler -->
                            <a id="m_aside_header_menu_mobile_toggle" href="javascript:;"
                               class="m-brand__icon m-brand__toggler m--visible-tablet-and-mobile-inline-block">
                                <span style="display: none;"></span>
                            </a>
                            <!-- END -->
                            <!-- BEGIN: Topbar Toggler -->
                            <a id="m_aside_header_topbar_mobile_toggle" href="javascript:;"
                               class="m-brand__icon m--visible-tablet-and-mobile-inline-block">
                                <i class="flaticon-more"></i>
                            </a>
                            <!-- BEGIN: Topbar Toggler -->
                        </div>
                    </div>
                </div>

                <div id="m_header_nav" class="m-topbar m-stack m-stack--ver m-stack--general">
                    <div class="m-stack__item m-topbar__nav-wrapper">
                        <div class="m-topbar__nav m-nav m-nav--inline">
                            <!-- notification for cash advance -->
                            <?php //$has_previ = (isset($this->core_layout->generateNotifPrivilegeAction()['ca_masterfile'])) ? $this->core_layout->generateNotifPrivilegeAction()['ca_masterfile'] : $this->core_layout->generateNotifPrivilegeAction()   ?>
                            <?php 
                                $has_previ = $this->core_layout->personal_roles_for_notif();
                                $target = array('ca_acctg_notif', 'ca_approval_notif', 'ca_acctg_fo_notif', 'ca_payroll_notif'); 
                            ?>
                            <?php if(count(array_intersect($has_previ, $target)) > 0): ?>
                                <?php $this->load->view('core/templates/ca_notif/notif_client') ?>
                            <?php endif; ?>
                            <?php //if($this->core_layout->getDeptFinance() > 0 || $this->authenticate->getUserId() == 1): ?>
                                <?php //$this->load->view('core/templates/ca_notif/acctg_notif') ?>
                            <?php //endif; ?>
                            <!-- notification for cash advance -->
                            <li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img m-dropdown
                                   m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right
                                   m-dropdown--mobile-full-width m-dropdown--skin-light"
                                data-dropdown-toggle="click" aria-expanded="true">
                                <a href="#" class="m-nav__link m-dropdown__toggle">
                                    <span class="m-topbar__userpic">
                                        <div class="custom-top-bar-avatar custom-top-bar-avatar--sm"
                                             style="background-image: url('<?= site_url($avatar) ?>')"></div>
                                    </span>
                                    <span class="m-topbar__username m--hide"><?= $user->firstname ?></span>
                                </a>

                                <!-- DROPDOWN -->
                                <?php $this->load->view("core/templates/user_profile/dropdown_info",
                                    array("avatar" => $avatar, "user" => $user), FALSE); ?>
                            </li>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- end::Header -->
    <!-- begin::Body -->
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
        <div id="m_aside_left" class="m-grid__item	m-aside-left  m-aside-left--skin-dark ">
            <div
                id="m_ver_menu"
                class="m-aside-menu m-aside-menu--skin-dark m-aside-menu--submenu-skin-dark m-aside-menu--dropdown "
                data-menu-vertical="true"
                data-menu-dropdown="true" data-menu-scrollable="true" data-menu-dropdown-timeout="500">
                <?php
                    echo $this->portal_model->getPortalSideNav();
                ?>
            </div>
        </div>
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <div class="m-subheader ">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="d-one mr-auto">
                        <h3 id="col1" class="m-subheader__title m-subheader__title--separator">
                            <span class="m--font-boldest">
                            WELCOME
                            </span>
                        </h3>
                        <ul id="col2" class="m-subheader__breadcrumbs m-nav m-nav--inline">
                            <li class="m-nav__item m-nav__item--home">
                                <a href="#" class="m-nav__link m-nav__link--icon">
                                    <i class="m-nav__link-icon m--font-boldest la la-user"></i>
                                </a>
                            </li>
                            <li class="m-nav__separator">
                                -
                            </li>
                            <li class="m-nav__item">
                                <a href="#" class="m-nav__link">
                                    <span class="m-nav__link-text" style="text-transform: uppercase;">
                                        <span class="m--font-boldest">
                                        <?php $user_data = $this->session->userdata('logged_in');
                                            echo $user_data['firstname']." ".$user_data['lastname']
                                        ?>
                                        </span>
                                        <img src="<?php echo base_url('assets/handwave2.png') ?>" height="20px" style="margin-bottom: 8px"></img>
                                    </span>
                                </a>
                            </li>
                        </ul>

                        <!-- <ul class="m-subheader__breadcrumbs m-nav m-nav--inline ml-2">
                            <span class="m-nav__link-text" style="text-transform: uppercase;">
                                <h5 class="m--font-boldest">
                                    <a href="<?=base_url('ticket/index') ?>" target="__blank" class="m-nav__link">
                                      <i class="fa fa-ticket"></i> 
                                        New Ticket
                                    </a>
                                </h5>
                            </span>
                        </ul> -->
                    </div>
                    <div class="d-two">
                      <span id="col0" class="m-subheader__daterange mr-2" >
                        <span class="m-subheader__daterange-label " style="font-weight:  800 !important; padding-left: 5px">
                        <a href="<?=base_url('ticket/index') ?>" target="__blank" style="text-decoration: none;">
                                      <i class="fa fa-ticket"></i> 
                                        New Ticket
                                    </a>
                        </span>
                      </span>
                      <span id="col2" class="m-subheader__daterange mr-2" id="m_dashboard_daterangepicker">
                            <span class="m-subheader__daterange-label">
                                <span class="m-subheader__daterange-title" style="font-weight:  800 !important;">TODAY:</span>
                                <span class="m-subheader__daterange-date m--font-brand" style="font-weight:  800 !important;"><?php echo date("M d, Y") ?> </span>
                            </span>
                        </span>
                        <span id="col1" class="m-subheader__daterange" style="background: transparent;">
                            <ul class="m-portlet__nav mb-0" style="padding-left: 0;">
                                <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
                                    <a href="#" class="m-portlet__nav-link m-dropdown__toggle dropdown-toggle btn btn--sm m-btn--pill btn-secondary m-btn m-btn--label-brand" style="font-weight:  800;">
                                        Quick Actions
                                    </a>
                                    <div class="m-dropdown__wrapper">
                                        <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 40.5547px;"></span>
                                        <div class="m-dropdown__inner">
                                            <div class="m-dropdown__body">
                                                <div class="m-dropdown__content">
                                                    <ul class="m-nav">
                                                        <li class="m-nav__section m-nav__section--first">
                                                            <span class="m-nav__section-text">
                                                                Eforms
                                                            </span>
                                                        </li>
                                                        <?php if(isset($loa_module) && $loa_module): ?>
                                                            <li class="m-nav__item">
                                                                <a href="<?=base_url('eforms/loa/new_loa') ?>" target="__blank" class="m-nav__link">
                                                                    <i class="m-nav__link-icon fa fa-calendar-check-o"></i>
                                                                    <span class="m-nav__link-text">
                                                                        New Leave of Absence
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        
                                                        <?php if(isset($overtime_module) && $overtime_module): ?>
                                                            <li class="m-nav__item">
                                                                <a href="<?=base_url('eforms/overtime/new_overtime') ?>" target="__blank" class="m-nav__link">
                                                                    <i class="m-nav__link-icon fa fa-clock-o"></i>
                                                                    <span class="m-nav__link-text">
                                                                        New Overtime
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if(isset($ca_module) && $overtime_module): ?>
                                                            <li class="m-nav__item">
                                                                <a href="<?=base_url('eforms/cash_advance/new_cash_advance') ?>" target="__blank" class="m-nav__link">
                                                                    <i class="m-nav__link-icon fa fa-money"></i>
                                                                    <span class="m-nav__link-text">
                                                                        New Cash Advance
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if(isset($to_module) && $to_module): ?>
                                                            <li class="m-nav__item">
                                                                <a href="<?=base_url('eforms/travel_order/new_travel_order') ?>" target="__blank" class="m-nav__link">
                                                                    <i class="m-nav__link-icon fa fa-car"></i>
                                                                    <span class="m-nav__link-text">
                                                                        New Travel Order
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if(isset($accountability_module) && $accountability_module): ?>
                                                            <li class="m-nav__item">
                                                                <a href="<?=base_url('eforms/accountability/new_accountability') ?>" target="__blank" class="m-nav__link">
                                                                    <i class="m-nav__link-icon fa fa-book"></i>
                                                                    <span class="m-nav__link-text">
                                                                        New Accountability
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if(isset($borrowing_module) && $borrowing_module): ?>
                                                            <li class="m-nav__item">
                                                                <a href="<?=base_url('eforms/borrowing/new_borrowing') ?>" target="__blank" class="m-nav__link">
                                                                    <i class="m-nav__link-icon fa fa-handshake-o"></i>
                                                                    <span class="m-nav__link-text">
                                                                        New Borrowing
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if(isset($tr_module) && $tr_module): ?>
                                                            <li class="m-nav__item">
                                                                <a href="<?=base_url('eforms/transmittal/new_transmittal') ?>" target="__blank" class="m-nav__link">
                                                                    <i class="m-nav__link-icon fa fa-file-text"></i>
                                                                    <span class="m-nav__link-text">
                                                                        New Transmittal
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if(isset($ship_module) && $ship_module): ?>
                                                            <li class="m-nav__item">
                                                                <a href="<?=base_url('eforms/shipping/new_shipping') ?>" target="__blank" class="m-nav__link">
                                                                    <i class="m-nav__link-icon fa fa-truck"></i>
                                                                    <span class="m-nav__link-text">
                                                                        New Shipping Advice
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if(isset($billing_module) && $billing_module): ?>
                                                            <li class="m-nav__item">
                                                                <a href="<?=base_url('eforms/billing/create_payment') ?>" target="__blank" class="m-nav__link">
                                                                    <i class="m-nav__link-icon la la-tint"></i>
                                                                    <span class="m-nav__link-text">
                                                                        New Payment
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <!-- <li class="m-nav__separator m-nav__separator--fit"></li>
                                                        <li class="m-nav__item">
                                                            <a href="<?=base_url('ticket/index') ?>" target="__blank" class="m-nav__link">
                                                                <i class="m-nav__link-icon fa fa-ticket"></i>
                                                                <span class="m-nav__link-text">
                                                                    New Ticket
                                                                </span>
                                                            </a>
                                                        </li> -->
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </span>
                    </div>
                </div>
            </div>
            <div class="m-content" id="portal_notifications">
                <div class="row">
                    <!-- LOA -->
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-3" style="padding: 0px;">
                    <?php if(isset($loa_module) && $loa_module) { ?>
                    <div class="col-md-12 col-lg-12 col-xl-12">
                        <div class="m-portlet m-portlet--head-sm">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <span class="m-portlet__head-icon">
                                            <i class="fa fa-calendar-check-o"></i>
                                        </span>
                                        <h4 class="m-portlet__head-text">LEAVE OF ABSENCE</h4>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools">
                                    <a href="<?= base_url("eforms/loa/masterfile")?>" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg m-btn--icon-only" style="float: right;"><i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="m-portlet__body m-portlet__body-sm m-portlet__body--no-padding" id="loa">
                            <div class="m-widget1" v-if="!vm_tab1.show">
                            <div class="m-widget1__item">
                            <div class="row align-items-center">
                                            <div class="col">
                                                <button class="btn" id="showLoa" style="width: 100%; background-color: #564ec0;  background-color: #564ec0; "  ><h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                  <h3 class="m-widget1__title" style="font-size: 10px !important; color: white; color: white;">
                                                    SHOW DATA
                                                  </h3>
                                                </button>
                                            </div>
                                        </div>
                            </div>
                            </div>    
                            <div class="m-widget1" v-else>
                                    <div class="m-widget1__item">
                                    <div class="row align-items-center" v-if="true">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                  NO PENDING LOA FOR APPROVAL
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('eforms/loa/masterfile').'?status=pending' ?>">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                        PENDING APPROVAL
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            LOA FOR APPROVAL
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_tab1.employee_count}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_tab1.progress_loa_pending"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_tab1.scroll_width}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <!-- Overtime -->
                    <?php if(isset($overtime_module) && $overtime_module) { ?>
                    <div class="col-md-12 col-lg-12 col-xl-12">
                        <div class="m-portlet m-portlet--head-sm">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <span class="m-portlet__head-icon">
                                            <i class="fa fa-clock-o"></i>
                                        </span>
                                        <h4 class="m-portlet__head-text">OVERTIME</h4>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools">
                                    <a href="<?= base_url("eforms/overtime/masterfile")?>" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg m-btn--icon-only" style="float: right;"><i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="m-portlet__body m-portlet__body--no-padding" id="overtime">
                            <div class="m-widget1" v-if="!vm_overtime.show">
                            <div class="m-widget1__item">
                            <div class="row align-items-center">
                                            <div class="col">
                                            <button class="btn" id="showOT" style="width: 100%; background-color: #564ec0;  background-color: #564ec0; "  ><h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                  <h3 class="m-widget1__title" style="font-size: 10px !important; color: white; color: white;">
                                                    SHOW DATA
                                                  </h3>
                                                </button>
                                            </div>
                              </div>
                            </div>
                            </div> 
                                <div class="m-widget1" v-else>
                                    <div class="m-widget1__item">
                                        <div class="row align-items-center" v-if="vm_overtime.for_approval == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                  NO PENDING OVERTIME FOR APPROVAL
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('eforms/overtime/masterfile').'?status=pending' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                            PENDING APPROVAL
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            OVERTIME FOR APPROVAL
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_overtime.for_approval}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_overtime.progress_ot"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_overtime.pending_count}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php } ?>
                    </div>
                    <!-- Cash Advance -->
                    <?php if(isset($ca_module) && $ca_module) { ?>
                    <template>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-3">
                        <div class="m-portlet m-portlet--head-sm m-portlet--full-height">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <span class="m-portlet__head-icon">
                                            <i class="fa fa-money"></i>
                                        </span>
                                        <h4 class="m-portlet__head-text">CASH ADVANCE</h4>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools">
                                    <a href="<?= base_url("eforms/cash_advance/masterfile")?>" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg m-btn--icon-only" style="float: right;"><i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="m-portlet__body m-portlet__body--no-padding" id="cash_advance">
                            <div class="m-widget1" v-if="!vm_ca.show">
                            <div class="m-widget1__item">
                            <div class="row align-items-center">
                                            <div class="col">
                                                
                                                <button class="btn" id="showCA" style="width: 100%; background-color: #564ec0;   "  ><h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                <h3 class="m-widget1__title" style="font-size: 10px !important; color: white;">
                                                SHOW DATA
                                                </h3>
                                                </button>
                                                
                                            </div>
                                        </div>
                            </div>
                            </div> 
                                <div class="m-widget1" v-else>
                                    <div class="m-widget1__item">
                                        <div class="row align-items-center" v-if="vm_ca.cash_advance_count == 0" style="padding-top: 3px !important;">
                                            <div class="col">
                                                <h5 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING FOR SUPERVISOR RECOMMENDATION
                                                </h5>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('/eforms/cash_advance/masterfile').'?status=sup_recoomendation' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                            SUPERVISOR RECOMMENDATION
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            CA FOR SUPERVISOR RECOMMENDATION
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_ca.cash_advance_count}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_ca.progress_ca_approval"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_ca.ca_approval_count}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item" v-if="vm_ca.ca_hr_note_priv">
                                        <div class="row align-items-center" v-if="vm_ca.hr_note == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING FOR PAYROLL BALANCE PENDING
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('/eforms/cash_advance/masterfile').'?status=payroll_balance_pending' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                            PAYROLL BALANCE PENDING
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            CA FOR PAYROLL APPROVAL
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_ca.hr_note}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_ca.progress_ca_hr_note"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_ca.ca_hr_note_count}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item">
                                        <div class="row align-items-center" v-if="vm_ca.acct_note == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING FOR ACCTG. BALANCE PENDING
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('/eforms/cash_advance/masterfile').'?status=accounting_balance_pending' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                            ACCTG. BALANCE PENDING
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            CA FOR ACCOUNTING APPROVAL
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_ca.acct_note}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_ca.progress_ca_acct_note"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_ca.ca_acct_note_count}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item">
                                        <div class="row align-items-center" v-if="vm_ca.awaiting_approval == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING FOR AWAITING APPROVAL
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('/eforms/cash_advance/masterfile').'?status=awaiting_approval' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                        AWAITING APPROVAL
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            CA FOR APPROVAL
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_ca.awaiting_approval}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_ca.progress_ca_awaiting_approval"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_ca.ca_awaiting_approval_count}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item">
                                        <div class="row align-items-center" v-if="vm_ca.for_posting == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING FOR POSTING
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('/eforms/cash_advance/masterfile').'?status=for_posting' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                        FOR POSTING
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            CA FOR POSTING
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_ca.for_posting}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_ca.progress_ca_for_posting"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_ca.ca_for_posting_count}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item">
                                        <div class="row align-items-center" v-if="vm_ca.posted == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING TO POSTED
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('/eforms/cash_advance/masterfile').'?status=posted' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                        POSTED
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            CA POSTED
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_ca.posted}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_ca.progress_ca_posted"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_ca.ca_posted_count}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item">
                                        <div class="row align-items-center" v-if="vm_ca.final_approval == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING TO FOR FINAL APPROVAL
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('/eforms/cash_advance/masterfile').'?status=for_final_approval' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                        FOR FINAL APPROVAL
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            CA FOR FINAL APPROVAL
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_ca.final_approval}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_ca.progress_ca_final_approval"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_ca.ca_final_approval_count}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>




                            </div>
                        </div>
                    </div>
                    </template>
                    <?php } ?>
                    <!-- travel order -->
                    <?php if(isset($to_module) && $to_module){ ?>
                    <template>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-3">
                        <div class="m-portlet m-portlet--head-sm m-portlet--full-height">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <span class="m-portlet__head-icon">
                                            <i class="fa fa-car"></i>
                                        </span>
                                        <h4 class="m-portlet__head-text">TRAVEL ORDER</h4>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools">
                                    <a href="<?= base_url("eforms/travel_order/masterfile")?>" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg m-btn--icon-only" style="float: right;"><i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="m-portlet__body m-portlet__body--no-padding" id="travel_order">
                                
                            <div class="m-widget1" v-if="!vm_travel_order.show">
                            <div class="m-widget1__item">
                            <div class="row align-items-center">
                                            <div class="col">
                                                <button class="btn" id="showTO" style="width: 100%; background-color: #564ec0;  font-size: 10px !important; backgroud-color: #564ec0;">
                                                <h3 class="m-widget1__title" style="font-size: 10px !important; color: white;">
                                                SHOW DATA
                                                </h3>
                                              </button>
                                                
                                            </div>
                                        </div>
                            </div>
                            </div>
                            <div class="m-widget1" v-else>
                                    <div class="m-widget1__item">
                                        <div class="row align-items-center" v-if="vm_travel_order.recommendation == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING FOR RECOMMENDATION
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('eforms/travel_order/masterfile').'?status=pending' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title" style="white-space: nowrap;">
                                                        SUPERVISOR RECOMMENDATION
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            TRAVEL ORDER RECOMMENDATION
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_travel_order.recommendation}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_travel_order.progress_to_recommendation"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_travel_order.to_recommendation_count}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item">
                                        <div class="row align-items-center" v-if="vm_travel_order.for_approval == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING FOR APPROVAL
                                                </h3>
                                            </div>
                                        </div><
                                        <div v-else>
                                            <a href="<?=base_url('eforms/travel_order/masterfile').'?status=recommend_approved' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                        PENDING APPROVAL
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            TRAVEL ORDER APPROVAL
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_travel_order.for_approval}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_travel_order.progress_to_approval"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                        {{ vm_travel_order.to_approval_count }} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item">
                                        <div class="row align-items-center" v-if="vm_travel_order.for_accomplishment == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING FOR ACCOMPLISHMENT
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('eforms/travel_order/masterfile').'?status=approved' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                        ACCOMPLISHMENT
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            TRAVEL ORDER FOR ACCOMPLISHMENT
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_travel_order.for_accomplishment}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_travel_order.progress_to_accomplishment"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{ vm_travel_order.to_accomplishment_count }} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </template>
                    <?php } ?>
                    <!-- Accountability -->
                    <?php if(isset($accountability_module) && $accountability_module) { ?>
                    <template>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-3">
                        <div class="m-portlet m-portlet--head-sm m-portlet--full-height">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <span class="m-portlet__head-icon">
                                            <i class="fa fa-book"></i>
                                        </span>
                                        <h4 class="m-portlet__head-text">ACCOUNTABILITY</h4>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools">
                                    <a href="<?= base_url("eforms/accountability/masterfile")?>" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg m-btn--icon-only" style="float: right;"><i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="m-portlet__body m-portlet__body--no-padding" id="accountability">
                            <div class="m-widget1" v-if="!vm_acct.show">
                            <div class="m-widget1__item">
                            <div class="row align-items-center">
                                            <div class="col">
                                                <button class="btn" id="showACCT" style="width: 100%; background-color: #564ec0;   "  ><h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                <h3 class="m-widget1__title" style="font-size: 10px !important; color: white;">
                                                SHOW DATA
                                                </h3>
                                                </button>

                                            </div>
                                        </div>
                            </div>
                            </div>
                                <div class="m-widget1" style="max-height: 600px; overflow-y: auto;" v-else>
                                    <div class="m-widget1__item" style="background: none;">
                                        <div class="row align-items-center" v-if="vm_acct.acct_note == null || vm_acct.acct_note == 0 || vm_acct.acct_note == ''">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING FOR ACCTG NOTE
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                        <div class="col text-center">
                                          <h3 class="m-widget1__title">
                                          ACCOUNTING NOTES
                                          </h3>
                                          </div>
                                          
                                            <div v-for="(companyData, companyName) in vm_acct.acct_note" :key="companyName" v-if="companyData.accountability_acct_note_count > 0" class="company-section">
                                            <a :href="`${baseUrl('eforms/accountability/masterfile')}?status=Pending_Accounting_Notes&company=${companyData.company}`">
                                            <div>
                                            <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                            {{ companyName }}
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            ACCOUNTABILITY FOR ACCTG. NOTES
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{ companyData.acct_note }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="companyData.progress_accountability_acct_note"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{ companyData.accountability_acct_note_count }} %
                                                </span>
                                                </div>
                                                </a>
                                            </div>
                                          
                                        </div>
                                    </div>
                                    <div class="m-widget1__item"  style="background: none;">
                                        <div class="row align-items-center" v-if="vm_acct.hr_note == null || vm_acct.hr_note == 0 || vm_acct.hr_note == ''">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING FOR HR NOTE
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                        <div class="col text-center">
                                          <h3 class="m-widget1__title">
                                          PENDING HR NOTES
                                          </h3>
                                          </div>
                                            <div v-for="(companyData, companyName) in vm_acct.hr_note" :key="companyName" v-if="companyData.accountability_hr_note_count > 0" class="company-section">
                                            <a :href="`${baseUrl('eforms/accountability/masterfile')}?status=Pending_Payroll_Notes&?company=${companyData.company}`">      
                                            <div>
                                            <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                        {{ companyName }}
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            ACCOUNTABILITY FOR HR NOTES
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                        {{ companyData.hr_note }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="companyData.progress_accountability_hr_note"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                {{ companyData.accountability_hr_note_count }} %
                                                </span>
                                                </div>
                                              </a>
                                          </div>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item"  style="background: none;">
                                        <div class="row align-items-center" v-if="vm_acct.acct_release == 0 || vm_acct.acct_release == null || vm_acct.acct_release == ''">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING FOR RELEASING
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                          <div class="col text-center">
                                          <h3 class="m-widget1__title">
                                          FOR RELEASING
                                          </h3>
                                          </div>

                                          <div v-for="(companyData, companyName) in vm_acct.acct_release" :key="companyName" v-if="companyData.accountability_releasing_count > 0" class="company-section">
                                          <a :href="`${baseUrl('eforms/accountability/masterfile')}?status=For_Releasing&?company=${companyData.company}`">   
                                          <div>
                                          <div class="row m-row--no-padding align-items-center">
                                                <div class="col">
                                          <h3 class="m-widget1__title">
                                              {{companyName}}
                                          </h3>
                                          <span class="m-widget1__desc">
                                              ACCOUNTABILITY FOR RELEASING
                                          </span>
                                      </div>
                                      <div class="col m--align-right">
                                          <span class="m-widget1__number m--font-brand">
                                              {{ companyData.acct_release }}
                                          </span>
                                      </div>
                                  </div>
                                  <div class="progress m-progress--sm" v-html="companyData.progress_accountability_releasing"></div>
                                  <span class="m--font-bolder m--font-metal">
                                      {{ companyData.accountability_releasing_count }} %
                                  </span>  
                                  </div>
                                        </a>
                                          </div> 

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </template>
                    <?php } ?>

                    <!-- Borrowing -->
                    <?php if(isset($borrowing_module) && $borrowing_module) { ?>
                    <template>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-3">
                        <div class="m-portlet m-portlet--head-sm m-portlet--full-height">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <span class="m-portlet__head-icon">
                                            <i class="fa fa-handshake-o"></i>
                                        </span>
                                        <h4 class="m-portlet__head-text">BORROWING</h4>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools">
                                    <a href="<?= base_url("eforms/borrowing/masterfile")?>" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg m-btn--icon-only" style="float: right;"><i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="m-portlet__body m-portlet__body--no-padding" id="borrowing">
                            <div class="m-widget1" v-if="!vm_borrowing.show">
                            <div class="m-widget1__item">
                            <div class="row align-items-center">
                                            <div class="col">
                                                <button class="btn" id="showBORR" style="width: 100%; background-color: #564ec0;   "  ><h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                <h3 class="m-widget1__title" style="font-size: 10px !important; color: white;">
                                                SHOW DATA
                                                </h3>
                                                </button>
                                            </div>
                                        </div>
                            </div>
                            </div> 
                                <div class="m-widget1" v-else>
                                    <div class="m-widget1__item" v-if="vm_borrowing.approval_priv">
                                        <div class="row align-items-center" v-if="vm_borrowing.for_approval == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING FOR APPROVAL
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('eforms/accountability/masterfile').'?status=pending' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                        PENDING APPROVAL
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            BORROWING FORM FOR APPROVAL
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_borrowing.for_approval}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_borrowing.progress_borr_approval"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_borrowing.borr_approval_count}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item" v-if="vm_borrowing.acct_release_priv">
                                        <div class="row align-items-center" v-if="vm_borrowing.acct_release == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING FOR RELEASING
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('eforms/accountability/masterfile').'?status=approved' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                        RELEASING
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            BORROWING FORM FOR RELEASING
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_borrowing.acct_release}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_borrowing.progress_borr_releasing"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_borrowing.borr_releasing_count}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item" v-if="vm_borrowing.acct_return_priv">
                                        <div class="row align-items-center" v-if="vm_borrowing.acct_return == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO UNRETURNED BORROWING
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('eforms/borrowing/masterfile')?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                        UNRETURNED BORROWING
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            FOR RETURN BORROWING
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_borrowing.acct_return}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_borrowing.progress_borr_return"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_borrowing.borr_return_count}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    </template>
                    <?php } ?>
                    <!-- Transmittal -->
                    <?php if(isset($tr_module) && $tr_module){ ?>
                    <template>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-3">
                        <div class="m-portlet m-portlet--head-sm m-portlet--full-height">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <span class="m-portlet__head-icon">
                                            <i class="fa fa-file-text"></i>
                                        </span>
                                        <h4 class="m-portlet__head-text">TRANSMITTAL</h4>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools">
                                    <a href="<?= base_url("eforms/transmittal/masterfile")?>" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg m-btn--icon-only" style="float: right;"><i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="m-portlet__body m-portlet__body--no-padding" id="transmittal">

                            <div class="m-widget1" v-if="!vm_transmittal.show">
                            <div class="m-widget1__item">
                            <div class="row align-items-center">
                                            <div class="col">
                                                <button class="btn" id="showTRANS" style="width: 100%; background-color: #564ec0;   "  ><h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                <h3 class="m-widget1__title" style="font-size: 10px !important; color: white;">
                                                SHOW DATA
                                                </h3>
                                                </button>
                                            </div>
                                        </div>
                            </div>
                            </div>

                                <div class="m-widget1" v-else>
                                    <div class="m-widget1__item">
                                        <div class="row align-items-center" v-if="vm_transmittal.for_approval == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                  NO PENDING TRANSMITTAL FOR APPROVAL
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('eforms/transmittal/masterfile').'?status=pending' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                        PENDING APPROVAL
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            TRANSMITTAL FOR APPROVAL
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_transmittal.for_approval}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_transmittal.progress_tr">
                                                </div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_transmittal.pending_count}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item" v-if="vm_transmittal.tr_receive_priv == true">
                                        <a href="<?=base_url('eforms/transmittal/masterfile').'?status=approved' ?>">
                                            <div class="row m-row--no-padding align-items-center">
                                                <div class="col">
                                                    <h3 class="m-widget1__title">
                                                        RECEIVE
                                                    </h3>
                                                    <span class="m-widget1__desc">
                                                        TRANSMITTAL TO RECEIVE
                                                    </span>
                                                </div>
                                                <div class="col m--align-right">
                                                    <span class="m-widget1__number m--font-brand">
                                                        {{vm_transmittal.receive}}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="progress m-progress--sm" v-html="vm_transmittal.progress_tr_rec"></div>
                                            <span class="m--font-bolder m--font-metal">
                                                {{vm_transmittal.receive_count}} %
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>    
                    </template>
                    <?php } ?>
                    <!-- Shipping -->
                    <?php if(isset($ship_module) && $ship_module) { ?>
                    <template>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-3">
                        <div class="m-portlet m-portlet--head-sm m-portlet--full-height">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <span class="m-portlet__head-icon">
                                            <i class="fa fa-truck"></i>
                                        </span>
                                        <h4 class="m-portlet__head-text">SHIPPING ADVICE</h4>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools">
                                    <a href="<?= base_url("eforms/shipping/masterfile")?>" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg m-btn--icon-only" style="float: right;"><i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="m-portlet__body m-portlet__body--no-padding" id="shipping">
                            
                            <div class="m-widget1" v-if="!vm_shipping.show">
                            <div class="m-widget1__item">
                            <div class="row align-items-center">
                                            <div class="col">
                                                <button class="btn" id="showSA" style="width: 100%; background-color: #564ec0;   "  ><h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                <h3 class="m-widget1__title" style="font-size: 10px !important; color: white;">
                                                SHOW DATA
                                                </h3>
                                                </button>
                                            </div>
                                        </div>
                            </div>
                            </div>
                            
                            <div class="m-widget1" v-else>
                                    <div class="m-widget1__item">
                                        <div class="row align-items-center" v-if="vm_shipping.for_approval == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING FOR APPROVAL
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('eforms/shipping/masterfile').'?status=pending' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                        PENDING APPROVAL
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            SHIPPING ADVICE FOR APPROVAL
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_shipping.for_approval}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_shipping.progress_sa_pending"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_shipping.pending_count}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item" v-if="vm_shipping.sa_receive_priv">
                                        <div class="row align-items-center" v-if="vm_shipping.receive == 0">
                                            <div class="col">
                                                <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;">
                                                    NO PENDING FOR RECEIVING
                                                </h3>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <a href="<?=base_url('eforms/shipping/masterfile').'?status=approved' ?>">
                                                <div class="row m-row--no-padding align-items-center">
                                                    <div class="col">
                                                        <h3 class="m-widget1__title">
                                                            RECEIVE
                                                        </h3>
                                                        <span class="m-widget1__desc">
                                                            SHIPPING ADVICE TO RECEIVE
                                                        </span>
                                                    </div>
                                                    <div class="col m--align-right">
                                                        <span class="m-widget1__number m--font-brand">
                                                            {{vm_shipping.receive}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress m-progress--sm" v-html="vm_shipping.progress_sa_receive"></div>
                                                <span class="m--font-bolder m--font-metal">
                                                    {{vm_shipping.receive_count}} %
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </template>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- </div> -->
    </div>
    <!-- end::Body -->
    <!-- begin::Footer -->
    <!-- <footer class="m-grid__item m-footer ">
        <div class="m-container m-container--responsive m-container--l m-container--full-height m-page__container">
            <div class="m-footer__wrapper">
                <div class="m-stack m-stack--flex-tablet-and-mobile m-stack--ver m-stack--desktop">
                    <div class="m-stack__item m-stack__item--left m-stack__item--middle m-stack__item--last">
								<span class="m-footer__copyright">2020 &copy; All Rights Reserved</span>
                               
                    </div>
                    <div class="m-stack__item m-stack__item--right m-stack__item--middle m-stack__item--first">
                        <ul class="m-footer__nav m-nav m-nav--inline m--pull-right">
                            <li class="m-nav__item">
                                
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer> -->
    <!-- end::Footer -->
</div>
<!-- end:: Page -->
<div class="m-scroll-top m-scroll-top--skin-top" data-toggle="m-scroll-top" data-scroll-offset="500"
     data-scroll-speed="300">
    <i class="la la-arrow-up"></i>
</div>
<?php if($version["response"] == true): ?>
<div class="modal fade" id="modal-version" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <input type="hidden" id="removeModule" value="0" />
            <div class="modal-header">
                <h5 class="modal-title">System Update and Version</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <?php $results = $version["data"]; ?>
                <?php foreach ($results as $key => $value) { ?>
                    <h3><?php echo "V".$value->version; ?>
                    <?php echo ($key == 0)? "<small>Current Version</small>":"" ?>
                    </h3>
                    <div><?php echo $value->description; ?></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $this->load->view("users/modal/change_password_dialog") ?>
<?php $this->load->view("users/modal/change_pin_dialog") ?>
<?php $this->load->view("users/modal/forget_pin_dialog") ?>
<?php $this->load->view("gcctime/attendance/modals/attendance_log") ?>
<?php $this->load->view("users/modal/password_reminder_dialog") ?>
<?php $this->load->view("users/modal/force_change_pass") ?>
<!-- end::Body -->
<script src="<?php echo base_url('assets/js/portal/portal_script.js'); ?>"></script>

<!-- global cash advance notification -->
<!-- script src="<?=base_url('node_modules/socket.io/client-dist/socket.io.js') ?>"></script -->
<?php /*** optimize loading time commet temporarily
$for_ca_notif = $this->core_layout->personal_roles_for_notif(); 
 optimize loading time commet temporarily ***/ ?>
<script>
    //var socket = io('http://'+window.location.hostname+':3000');
    //var _for_ca_actions = <?//=json_encode($for_ca_notif) ?>;
    /*** var company_id = <?=$this->session->userdata("logged_in")['company'] ? $this->session->userdata("logged_in")['company'] : 0;  ?>; ***/
</script>
<!-- <script src="<?php //echo base_url('assets/js/global_notification.js'); ?>"></script> -->
<!-- global cash advance notification -->

</body>
</html>
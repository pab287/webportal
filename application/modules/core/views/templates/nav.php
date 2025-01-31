<?php $config = ""; ?>
<?php $route = ""; ?>
<?php $Subheader = ""; ?>
<?php
    $select = array("id", "idno", "biometricno", "lastname", "firstname", "middlename", "email",
        "position", "employee_status", "curr_addr", "str_addr", "city", "postal", "pic_filename");
    $user = json_decode(json_encode($this->authenticate->getUserData($select), TRUE));
    $profile_image_path = "uploads/files/images/employee_files/empcode_" . $user->id . "/" . $user->pic_filename;
    $avatar = null;

    if (file_exists($profile_image_path)) {
        $avatar = $profile_image_path;
    } else {
        $avatar = "assets/images/profile/no_image.jpg";
    }
?>
    <!-- begin:: Page -->
    <div class="m-grid m-grid--hor m-grid--root m-page">
    <!-- BEGIN: Header -->
    <header class="m-grid__item    m-header " data-minimize-offset="200" data-minimize-mobile-offset="200">
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
                                <em class="flaticon-more"></em>
                            </a>
                            <!-- BEGIN: Topbar Toggler -->
                        </div>
                    </div>
                </div>
                <!-- END: Brand -->
                <div id="m_header_nav" class="m-topbar m-stack m-stack--ver m-stack--general">
                    <div class="m-stack__item m-topbar__nav-wrapper">
                        <ul class="m-topbar__nav m-nav m-nav--inline">
                            <?php
                            if($this->authenticate->getCurrentModuleId() == 13){
                            //     $modules = "eforms";
                            //     $moduleResource = array("eforms-borrowing");
                            //     if($this->authenticate->getPrivilegeChecker($moduleResource, $modules)){
                            //         $this->load->view("core/templates/notification");
                                    
                            //     }
                            }elseif($this->uri->segment(1) == "hris"){
                            //     $this->load->view("hris/masterfile/employee/notification/bell");
                                $this->load->view("hris/masterfile/employee/notification/settings");
                            }
                            ?> <!-- notification for borrowing -->

                            <!-- notification for cash advance -->
                            <?php 
                                //$has_previ = (isset($this->core_layout->generateNotifPrivilegeAction()['ca_masterfile'])) ? $this->core_layout->generateNotifPrivilegeAction()['ca_masterfile'] : $this->core_layout->generateNotifPrivilegeAction()
                                // $has_previ = $this->core_layout->personal_roles_for_notif();
                                // $target = array('ca_acctg_notif', 'ca_approval_notif', 'ca_acctg_fo_notif', 'ca_payroll_notif'); 
                            ?>
                            <?php //if(count(array_intersect($has_previ, $target)) > 0): ?>
                                <?php //$this->load->view('core/templates/ca_notif/notif_client') ?>
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
                            <?php /*** $this->load->view("core/templates/more_actions"); ***/ ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- END: Header -->
<?php $this->load->view("users/modal/change_pin_dialog") ?>
<?php $this->load->view("users/modal/change_password_dialog") ?>
<?php $this->load->view("users/modal/forget_pin_dialog") ?>
<?php $this->load->view("users/modal/two_factor_dialog") ?>
<?php $mBodyClass = (isset($no_sidenav) && $no_sidenav)? "m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body": "m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body"; ?>
    <!-- begin::Body -->
    <div class="<?php echo $mBodyClass; ?>">
    <?php if(isset($no_sidenav) && $no_sidenav): ?>
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver m-container m-container--responsive m-container--l m-page__container">
    <?php else: ?>
    <!-- BEGIN: Left Aside -->
    <button class="m-aside-left-close m-aside-left-close--skin-dark" id="m_aside_left_close_btn">
        <em class="la la-close"></em>
    </button>
    <div id="m_aside_left" class="m-grid__item	m-aside-left  m-aside-left--skin-dark ">
        <!-- BEGIN: Aside Menu -->
        <div
            id="m_ver_menu"
            class="m-aside-menu m-aside-menu--skin-dark m-aside-menu--submenu-skin-dark m-aside-menu--dropdown "
            data-menu-vertical="true"
            data-menu-dropdown="true" data-menu-scrollable="true" data-menu-dropdown-timeout="500">
            <?php
                $includes = array("id", "name", "label", "url", "icon", "identifier");
                echo $this->core_layout->getSidebarNavigation($includes, 1);
            ?>
        </div>
        <!-- END: Aside Menu -->
    </div>
    <!-- END: Left Aside -->
    <?php endif; ?>
    <div class="m-grid__item m-grid__item--fluid m-wrapper">
<?php
$loggedSession = (object) $this->session->userdata("logged_in");
if($loggedSession->module_id){
    $roleId = $this->authenticate->getRoleId();
    //var_dump($loggedSession->module_id);
}
?>
<div id="m_header_menu" class="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas m-header-menu--skin-light m-header-menu--submenu-skin-light m-aside-header-menu-mobile--skin-dark m-aside-header-menu-mobile--submenu-skin-dark ">
    <ul class="m-menu__nav  m-menu__nav--submenu-arrow ">
        <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel" data-menu-submenu-toggle="click"
            data-redirect="true">
            <a href="javascript:;" class="m-menu__link m-menu__toggle">
                <span class="m-menu__link-text">Module Navigation</span>
                <i class="m-menu__hor-arrow la la-angle-down"></i>
                <i class="m-menu__ver-arrow la la-angle-right"></i>
            </a>
            <div class="m-menu__submenu  m-menu__submenu--fixed m-menu__submenu--left" style="width:1000px">
                <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                <div class="m-menu__subnav">
                    <ul class="m-menu__content">
                        <li class="m-menu__item">
                            <h3 class="m-menu__heading m-menu__toggle">
                                <span class="m-menu__link-text">
                                    Finance Reports
                                </span>
                                <i class="m-menu__ver-arrow la la-angle-right"></i>
                            </h3>
                            <ul class="m-menu__inner">
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-icon flaticon-map"></i>
                                        <span class="m-menu__link-text">
                                            Annual Reports
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-icon flaticon-user"></i>
                                        <span class="m-menu__link-text">
                                            HR Reports
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-icon flaticon-clipboard"></i>
                                        <span class="m-menu__link-text">
                                            IPO Reports
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-icon flaticon-graphic-1"></i>
                                        <span class="m-menu__link-text">
                                            Finance Margins
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-icon flaticon-graphic-2"></i>
                                        <span class="m-menu__link-text">
                                            Revenue Reports
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="m-menu__item">
                            <h3 class="m-menu__heading m-menu__toggle">
                                <span class="m-menu__link-text">
                                    Project Reports
                                </span>
                                <i class="m-menu__ver-arrow la la-angle-right"></i>
                            </h3>
                            <ul class="m-menu__inner">
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-bullet m-menu__link-bullet--line">
                                            <span></span>
                                        </i>
                                        <span class="m-menu__link-text">
                                            Coca Cola CRM
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-bullet m-menu__link-bullet--line">
                                            <span></span>
                                        </i>
                                        <span class="m-menu__link-text">
                                            Delta Airlines Booking Site
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-bullet m-menu__link-bullet--line">
                                            <span></span>
                                        </i>
                                        <span class="m-menu__link-text">
                                            Malibu Accounting
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-bullet m-menu__link-bullet--line">
                                            <span></span>
                                        </i>
                                        <span class="m-menu__link-text">
                                            Vineseed Website Rewamp
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-bullet m-menu__link-bullet--line">
                                            <span></span>
                                        </i>
                                        <span class="m-menu__link-text">
                                            Zircon Mobile App
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-bullet m-menu__link-bullet--line">
                                            <span></span>
                                        </i>
                                        <span class="m-menu__link-text">
                                            Mercury CMS
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="m-menu__item">
                            <h3 class="m-menu__heading m-menu__toggle">
                                <span class="m-menu__link-text">
                                    HR Reports
                                </span>
                                <i class="m-menu__ver-arrow la la-angle-right"></i>
                            </h3>
                            <ul class="m-menu__inner">
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="m-menu__link-text">
                                            Staff Directory
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="m-menu__link-text">
                                            Client Directory
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="m-menu__link-text">
                                            Salary Reports
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="m-menu__link-text">
                                            Staff Payslips
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="m-menu__link-text">
                                            Corporate Expenses
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="m-menu__link-text">
                                            Project Expenses
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="m-menu__item">
                            <h3 class="m-menu__heading m-menu__toggle">
                                <span class="m-menu__link-text">
                                    Reporting Apps
                                </span>
                                <i class="m-menu__ver-arrow la la-angle-right"></i>
                            </h3>
                            <ul class="m-menu__inner">
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <span class="m-menu__link-text">
                                            Report Adjusments
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <span class="m-menu__link-text">
                                            Sources & Mediums
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <span class="m-menu__link-text">
                                            Reporting Settings
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <span class="m-menu__link-text">
                                            Conversions
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <span class="m-menu__link-text">
                                            Report Flows
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="inner.html" class="m-menu__link ">
                                        <span class="m-menu__link-text">
                                            Audit & Logs
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </li>
    </ul>
</div>
<style type="text/css">
    #dropdown-login .m-dropdown__header {
        background-color: #0c5e9e !important;
    }

    #dropdown-login .m-dropdown__arrow {
        color: #0c5e9e !important;
    }
</style>
<div class="m-dropdown__wrapper" id="dropdown-login">
    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"
          style="left: auto; right: 12.5px;">
    </span>
    <div class="m-dropdown__inner">
        <div class="m-dropdown__header m--align-center">
            <div class="m-card-user m-card-user--skin-dark">
                <div class="m-card-user__pic">
                    <div class="custom-top-bar-avatar custom-top-bar-avatar--md"
                         style="background-image: url('<?= site_url($avatar) ?>')"></div>
                </div>
                <div class="m-card-user__details">
                    <span class="m-card-user__name m--font-weight-500" style="text-transform: uppercase;">
                        <?= $user->firstname ?>
                    </span>
                    <a href="" class="m-card-user__email m--font-weight-300">
                        <?= empty($user->email) ? "No email account." : $user->email ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="m-dropdown__body">
            <div class="m-dropdown__content">
                <ul class="m-nav m-nav--skin-light">
                    <li class="m-nav__section m--hide">
                <span class="m-nav__section-text">
                    Section
                </span>
                    </li>
                    <li class="m-nav__item">
                        <a href="<?= base_url('core/profile') ?>" class="m-nav__link">
                            <i class="m-nav__link-icon flaticon-profile-1"></i>
                            <span class="m-nav__link-title">
                        <span class="m-nav__link-wrap">
                            <span class="m-nav__link-text">
                                My Profile
                            </span>
                        </span>
                    </span>
                        </a>
                    </li>
                    <li class="m-nav__item">
                        <a href="" class="m-nav__link" onclick="event.preventDefault(); openChangePassword();">
                            <i class="m-nav__link-icon flaticon-lock-1"></i>
                            <span class="m-nav__link-text">
                        Change Password
                    </span>
                        </a>
                    </li>
                    <li class="m-nav__item">
                        <a href="" class="m-nav__link" onclick="event.preventDefault(); openChangePin();">
                            <i class="m-nav__link-icon flaticon-warning-2"></i>
                            <span class="m-nav__link-text">
                                Change Pin
                            </span>
                        </a>
                    </li>
                    <li class="m-nav__item">
                        <a href="" class="m-nav__link" onclick="event.preventDefault(); forgetPin();">
                            <i class="m-nav__link-icon flaticon-lock"></i>
                            <span class="m-nav__link-text">
                                Forgot Pin
                            </span>
                        </a>
                    </li>
                    <li class="m-nav__item">
                        <a href="" class="m-nav__link" onclick="openAttendanceLog(); return false;">
                            <i class="m-nav__link-icon flaticon-clock-1"></i>
                            <span class="m-nav__link-text">
                                Attendance Log
                             </span>
                        </a>
                    </li>
                    <li class="m-nav__item">
                        <a href="" class="m-nav__link" onclick="passwordChangeReminder(); return false;">
                            <i class="m-nav__link-icon flaticon-clock-1"></i>
                            <span class="m-nav__link-text">
                                TEst
                             </span>
                        </a>
                    </li>
                    <?php
                    $twoFactorAuth = isset($this->session->userdata("logged_in")['TwoFactorAuth']) ? $this->session->userdata("logged_in")['TwoFactorAuth'] : 0;
                    ?>
                    <li class="m-nav__item">
                        <a href="javascript:void(0);" class="m-nav__link" onclick="activate2FA(<?php echo $twoFactorAuth; ?>); return false;">
                            <i class="m-nav__link-icon flaticon-user-ok"></i>
                            <span class="m-nav__link-text 2FA">
                                <?php echo ($twoFactorAuth == 1) ? 'Disable 2FA' : 'Enable 2FA'; ?>
                            </span>
                        </a>
                    </li>
                    <li class="m-nav__separator m-nav__separator--fit"></li>
                    <li class="m-nav__item">
                        <a href="<?= site_url('login/logout') ?>"
                           class="btn m-btn--pill btn-secondary m-btn m-btn--custom m-btn--label-brand m-btn--bolder">
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
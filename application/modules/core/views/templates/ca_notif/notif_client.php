<?php 
    $has_previ = $this->core_layout->personal_roles_for_notif();
    if(in_array('ca_payroll_notif', $has_previ)){
        $type = 'payroll';
    }else if(in_array('ca_acctg_notif', $has_previ) || in_array('ca_acctg_fo_notif', $has_previ)){
        $type = 'acctg';
    }else{
        $type = 'approval';
    }

    $is_fo = in_array('ca_acctg_fo_notif', $has_previ) ? "getCA('acctg', '".$this->session->userdata("logged_in")['company']."')" : "getCA('acctg')";
?>
<li id="for_notif" class="m-nav__item m-topbar__notifications m-topbar__notifications--img m-dropdown m-dropdown--large m-dropdown--header-bg-fill m-dropdown--arrow m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light" data-dropdown-toggle="click" data-dropdown-persistent="true" aria-expanded="true">
    <a href="javascript:void(0)" onclick="getCA('<?=$type; ?>')" class="m-nav__link m-dropdown__toggle">
        <template v-if="total > 0">
            <span id="ca_notifnum" class="m-nav__link-badge m-badge m-badge--danger m-animate-blink">{{total}}</span>
            <span class="m-nav__link-icon m-animate-shake" id="for_animate">
                <i class="flaticon-music-2"></i>
            </span>
        </template>
        <template v-else>
            <span id="ca_notifnum" class="m-nav__link-badge"></span>
            <span class="m-nav__link-icon" id="for_animate">
                <i class="flaticon-music-2"></i>
            </span>
        </template>
    </a>
    <!-- DROPDOWN NOTIF -->
    <div class="m-dropdown__wrapper" id="ca_notif">
        <template>
            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
            <div class="m-dropdown__inner">
                <div class="m-dropdown__header m--align-center">
                    <span class="m-dropdown__header-title">
                        <span>{{total}}</span> New <br> 
                        <template v-if="total > 1">
                            <small>Notifications</small>
                        </template>
                        <template v-else>
                            <small>Notification</small>
                        </template>
                    </span>
                </div>
                <div class="m-dropdown__body">
                    <div class="m-dropdown__content">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--brand" role="tablist"> 
                            <?php if(in_array('ca_payroll_notif', $has_previ)): ?>
                                <li class="nav-item m-tabs__item">
                                    <a class="nav-link m-tabs__link <?=$type == 'payroll'?"active":"" ?>" onclick="getCA('payroll')" id="payroll_tab" data-toggle="tab" href="#topbar_notifications_notifications" role="tab" aria-expanded="true" style="font-size: 11px">
                                        PAYROLL BALANCE PENDING 
                                        <template v-if="payroll > 0">
                                            <span id="ca_notif_num" class="m-nav__link-badge m-badge m-badge--danger">{{payroll}}</span>
                                        </template>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(in_array('ca_acctg_notif', $has_previ) || in_array('ca_acctg_fo_notif', $has_previ) ): ?>
                                <li class="nav-item m-tabs__item">
                                    <a class="nav-link m-tabs__link <?=$type == 'acctg'?"active":"" ?>" onclick="<?=$is_fo ?>" id="payroll_tab" data-toggle="tab" href="#acctg_topbar_notifications_notifications" role="tab" aria-expanded="true" style="font-size: 11px">
                                        ACCOUNTING BALANCE PENDING
                                        <template v-if="acctg > 0">
                                            <span id="ca_notif_num" class="m-nav__link-badge m-badge m-badge--danger">{{acctg}}</span>
                                        </template>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(in_array('ca_approval_notif', $has_previ)): ?>
                                <li class="nav-item m-tabs__item">
                                    <a class="nav-link m-tabs__link <?=$type == 'approval'?"active":"" ?>" onclick="getCA('approval')" id="payroll_tab" data-toggle="tab" href="#approval_topbar_notifications_notifications" role="tab" aria-expanded="true" style="font-size: 11px">
                                        AWAITING APPROVAL
                                        <template v-if="approval > 0">
                                            <span id="ca_notif_num" class="m-nav__link-badge m-badge m-badge--danger">{{approval}}</span>
                                        </template>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                        <div style="height: 250px; display: none; justify-content: center; align-items: center;" class="spinner text-center">
                            <div class="row">
                                <div class="col-12">
                                    <p class="spinner-border spinner-border-sm loader" role="status" aria-hidden="true"></p>
                                </div>
                                <div class="col-12">
                                    <p>Please Wait...</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane <?=$type == 'payroll'?"active":"" ?>" id="topbar_notifications_notifications" role="tabpanel" aria-expanded="false">
                                <div class="m-scrollable mCustomScrollbar _mCS_2 mCS-autoHide" data-scrollable="true" data-max-height="250" data-mobile-max-height="200" style="position: relative; overflow: visible;">
                                    <div id="mCSB_2" class="mCustomScrollBox mCS-minimal-dark mCSB_vertical mCSB_outside" style="max-height: none;" tabindex="0">
                                        <div id="mCSB_2_container" class="mCSB_container" style="position:relative; top:0; left:0;" dir="ltr">
                                            <template v-if="payroll > 0">
                                                <div class="m-list-timeline m-list-timeline--skin-light">
                                                    <div class="m-list-timeline__items" id="ca_items">
                                                        <template v-for="row in rows" :key="row.ca_id">
                                                            <a class="m-list-timeline__item" href="javascript:void(0)" v-on:click="redirectViewUrl(row.ca_id, 'pyrll')" id="for_notif" target="_blank">
                                                                <span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
                                                                <span class="m-list-timeline__text">
                                                                    <p class="my-0" style="font-weight: 600">{{row.fullname}}</p>
                                                                    <small style="font-size: 8px">{{row.dept}} <br> {{row.pst}}</small>
                                                                </span>
                                                                <span class="m-list-timeline__time">
                                                                    <p class="my-0">Date Applied: </p>
                                                                    <p>{{ getToDate(row.created) }}</p>
                                                                </span>
                                                            </a>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div id="ca_see_more">
                                                    <a href="<?=site_url('/eforms/cash_advance/pending_balance?type=pyrll'); ?>">See All</a>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <div id="no_notif" class="text-center">
                                                    No New Notification
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <div id="mCSB_2_scrollbar_vertical" class="mCSB_scrollTools mCSB_2_scrollbar mCS-minimal-dark mCSB_scrollTools_vertical" style="display: none;">
                                        <div class="mCSB_draggerContainer">
                                            <div id="mCSB_2_dragger_vertical" class="mCSB_dragger" style="position: absolute; min-height: 50px; top: 0px; display: block; height: 207px; max-height: 230px;">
                                                <div class="mCSB_dragger_bar" style="line-height: 50px;"></div>
                                            </div>
                                            <div class="mCSB_draggerRail"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane <?=$type == 'acctg'?"active":"" ?>" id="acctg_topbar_notifications_notifications" role="tabpanel" aria-expanded="false">
                                <div class="m-scrollable mCustomScrollbar _mCS_2 mCS-autoHide" data-scrollable="true" data-max-height="250" data-mobile-max-height="200" style="position: relative; overflow: visible;">
                                    <div id="mCSB_2" class="mCustomScrollBox mCS-minimal-dark mCSB_vertical mCSB_outside" style="max-height: none;" tabindex="0">
                                        <div id="mCSB_2_container" class="mCSB_container" style="position:relative; top:0; left:0;" dir="ltr">
                                            <template v-if="acctg > 0">
                                                <div class="m-list-timeline m-list-timeline--skin-light">
                                                    <div class="m-list-timeline__items" id="ca_items">
                                                        <template v-for="row in rows" :key="row.ca_id">
                                                            <a class="m-list-timeline__item" href="javascript:void(0)" v-on:click="redirectViewUrl(row.ca_id, 'acctg')" id="for_notif" target="_blank">
                                                                <span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
                                                                <span class="m-list-timeline__text">
                                                                    <p class="my-0" style="font-weight: 600">{{row.fullname}}</p>
                                                                    <small style="font-size: 8px">{{row.dept}} <br> {{row.pst}}</small>
                                                                </span>
                                                                <span class="m-list-timeline__time">
                                                                    <p class="my-0">Date Applied: </p>
                                                                    <p>{{ getToDate(row.created) }}</p>
                                                                </span>
                                                            </a>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div id="ca_see_more">
                                                    <a href="<?=site_url('/eforms/cash_advance/pending_balance?type=acctg'); ?>">See All</a>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <div id="no_notif" class="text-center">
                                                    No New Notification
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <div id="mCSB_2_scrollbar_vertical" class="mCSB_scrollTools mCSB_2_scrollbar mCS-minimal-dark mCSB_scrollTools_vertical" style="display: none;">
                                        <div class="mCSB_draggerContainer">
                                            <div id="mCSB_2_dragger_vertical" class="mCSB_dragger" style="position: absolute; min-height: 50px; top: 0px; display: block; height: 207px; max-height: 230px;">
                                                <div class="mCSB_dragger_bar" style="line-height: 50px;"></div>
                                            </div>
                                            <div class="mCSB_draggerRail"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane <?=$type == 'approval'?"active":"" ?>" id="approval_topbar_notifications_notifications" role="tabpanel" aria-expanded="false">
                                <div class="m-scrollable mCustomScrollbar _mCS_2 mCS-autoHide" data-scrollable="true" data-max-height="250" data-mobile-max-height="200" style="position: relative; overflow: visible;">
                                    <div id="mCSB_2" class="mCustomScrollBox mCS-minimal-dark mCSB_vertical mCSB_outside" style="max-height: none;" tabindex="0">
                                        <div id="mCSB_2_container" class="mCSB_container" style="position:relative; top:0; left:0;" dir="ltr">
                                            <template v-if="approval > 0">
                                                <div class="m-list-timeline m-list-timeline--skin-light">
                                                    <div class="m-list-timeline__items" id="ca_items">
                                                        <template v-for="row in rows" :key="row.ca_id">
                                                            <a class="m-list-timeline__item" href="javascript:void(0)" v-on:click="redirectViewUrl(row.ca_id, 'approval')" id="for_notif" target="_blank">
                                                                <span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
                                                                <span class="m-list-timeline__text">
                                                                    <p class="my-0" style="font-weight: 600">{{row.fullname}}</p>
                                                                    <small style="font-size: 8px">{{row.dept}} <br> {{row.pst}}</small>
                                                                </span>
                                                                <span class="m-list-timeline__time">
                                                                    <p class="my-0">Date Applied: </p>
                                                                    <p>{{ getToDate(row.created) }}</p>
                                                                </span>
                                                            </a>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div id="ca_see_more">
                                                    <a href="<?=site_url('/eforms/cash_advance/pending_balance?type=approval'); ?>">See All</a>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <div id="no_notif" class="text-center">
                                                    No New Notification
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <div id="mCSB_2_scrollbar_vertical" class="mCSB_scrollTools mCSB_2_scrollbar mCS-minimal-dark mCSB_scrollTools_vertical" style="display: none;">
                                        <div class="mCSB_draggerContainer">
                                            <div id="mCSB_2_dragger_vertical" class="mCSB_dragger" style="position: absolute; min-height: 50px; top: 0px; display: block; height: 207px; max-height: 230px;">
                                                <div class="mCSB_dragger_bar" style="line-height: 50px;"></div>
                                            </div>
                                            <div class="mCSB_draggerRail"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</li>

<style type="text/css">
    .loader {
        border: 5px solid #f3f3f3;
        border-radius: 50%;
        border-top: 5px solid #c5c9cb;
        width: 50px;
        height: 50px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
        position: relative;
        left: 53px;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .to_active{
        display: block !important;
    }
    #ca_notif .m-dropdown__header{
        background-color: #0c5e9e !important;
    }
    #ca_items a:hover{
        background: #eee;
    }
    #payroll_tab{
        position: relative;
    }
    #payroll_tab #ca_notif_num{
        position: absolute;
        top: 0;
    }
    #ca_items .m-list-timeline__item{
        margin: 5px 4px !important;
    }
    #ca_items .m-list-timeline__item:first-child:before, #ca_items .m-list-timeline__item:last-child:before{
        left: -1px !important;
    }
    #ca_items .m-list-timeline__item .m-list-timeline__badge:before{
        left: -4px !important;
    }
    #ca_notif .m-dropdown__arrow{
        color: #0c5e9e !important;
    }
    #for_notif, #ca_see_more a{
        text-decoration: none
    }
    #for_notif:hover{
        color: #0C5E9E;
    }
    #ca_items .m-list-timeline__item:last-child{
        margin-bottom: 15px
    }
    #ca_see_more{
        position: fixed;
        margin: 0 auto;
        right: 0;
        left: 0;
        text-align: center;
        bottom: 5px;
    }
    #no_notif{
        transform: translate(0, 50%);
        height: 170px;
    }
    .m-dropdown.m-dropdown--large .m-dropdown__wrapper {
        width: 620px;
    }
    .m-list-timeline__items .m-list-timeline__item .m-list-timeline__time {
        width: 100px;
    }
    @media screen and (max-width: 575px){
        .m-dropdown.m-dropdown--large .m-dropdown__wrapper {
            width: 87% !important;
        }
        .m-topbar .m-topbar__nav.m-nav > .m-nav__item > .m-nav__link .m-nav__link-badge {
            margin-left: 3px;
            top: -5px;
        }
    }
</style>
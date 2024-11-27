<li id="acctg_for_notif" class="m-nav__item m-topbar__notifications m-topbar__notifications--img m-dropdown m-dropdown--large m-dropdown--header-bg-fill m-dropdown--arrow m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light" data-dropdown-toggle="click" data-dropdown-persistent="true" aria-expanded="true">
    <a href="javascript:void(0)" onclick="getCA('acctg')" class="m-nav__link m-dropdown__toggle btnCa_notification" id="m_topbar_notification_icon">
        <span id="acctg_notifnum" class="m-nav__link-badge m-badge m-badge--danger" style="display: none"></span>
        <span class="m-nav__link-icon m-animate-shake">
            <i class="flaticon-music-2"></i>
        </span>
    </a>
    <!-- DROPDOWN NOTIF -->
    <div class="m-dropdown__wrapper" id="acctg_ca_notif">
        <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
        <div class="m-dropdown__inner">
            <div class="m-dropdown__header m--align-center">
                <span class="m-dropdown__header-title"><span id="acctg_notif_num1"></span> New <br> <small id="acctg_text"></small></span>
            </div>
            <div class="m-dropdown__body">
                <div class="m-dropdown__content">
                    <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--brand" role="tablist">
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link active" id="payroll_tab" data-toggle="tab" href="#acctg_topbar_notifications_notifications" role="tab" aria-expanded="true">
                                Accounting Balance Pending <span id="acctg_notif_num" class="m-nav__link-badge m-badge m-badge--danger"></span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="acctg_topbar_notifications_notifications" role="tabpanel" aria-expanded="false">
                            <div class="m-scrollable mCustomScrollbar _mCS_2 mCS-autoHide" data-scrollable="true" data-max-height="250" data-mobile-max-height="200" style="position: relative; overflow: visible;">
                                <div id="mCSB_2" class="mCustomScrollBox mCS-minimal-dark mCSB_vertical mCSB_outside" style="max-height: none;" tabindex="0">
                                    <div id="mCSB_2_container" class="mCSB_container" style="position:relative; top:0; left:0;" dir="ltr">
                                        <div class="m-list-timeline m-list-timeline--skin-light">
                                            <div class="m-list-timeline__items" id="acctg_items"></div>
                                        </div>
                                        <div id="acctg_no_notif" class="text-center">
                                            No New Notification
                                        </div>
                                        <div id="ca_see_more">
                                            <a href="<?=site_url('/eforms/cash_advance/pending_balance?type=acctg'); ?>">See All</a>
                                        </div>
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
    </div>
</li>

<style type="text/css">
    #acctg_ca_notif .m-dropdown__header {
        background-color: #0c5e9e !important;
    }
    #acctg_items a:hover{
        background: #eee;
    }
    #payroll_tab{
        position: relative;
    }
    #payroll_tab #acctg_notif_num, #payroll_tab #qne_notif_num{
        position: absolute;
        top: 0;
    }
    #acctg_items .m-list-timeline__item{
        margin: 5px 4px !important;
    }
    #acctg_items .m-list-timeline__item:first-child:before, #acctg_items .m-list-timeline__item:last-child:before {
        left: -1px !important;
    }
    #acctg_items .m-list-timeline__item .m-list-timeline__badge:before {
        left: -4px !important;
    }
    #acctg_ca_notif .m-dropdown__arrow {
        color: #0c5e9e !important;
    }
    #acctg_for_notif, #ca_see_more a{
        text-decoration: none
    }
    #acctg_for_notif:hover{
        color: #0C5E9E;
    }
    #acctg_items .m-list-timeline__item:last-child{
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
    #acctg_no_notif{
        transform: translate(0, 50%);
        height: 170px;
        display: none;
    }
</style>
<li id="notification" class="m-nav__item m-topbar__notifications m-topbar__notifications--img m-dropdown 
    m-dropdown--large m-dropdown--header-bg-fill m-dropdown--arrow m-dropdown--align-right 
    m-dropdown--mobile-full-width" 
    data-dropdown-toggle="click" 
    data-dropdown-persistent="true">
    <a href="#" class="m-nav__link m-dropdown__toggle" id="m_topbar_notification_icon">
        <span id="notifBar" class="m-nav__link-badge m-badge m-badge--danger"></span>
        <span class="m-nav__link-icon">
            <em class="flaticon-alert-2"></em>
        </span>
    </a>
    <div class="m-dropdown__wrapper">
        <span class="m-dropdown__arrow m-dropdown__arrow--right"></span>
        <div class="m-dropdown__inner">
            <div class="m-dropdown__header m--align-center" 
                style="background: url('<?php echo base_url("assets/app/media/img/misc/notification_bg.jpg"); ?>'); background-size: cover;">
                <span class="m-dropdown__header-title">
                    9 New
                </span>
                <span class="m-dropdown__header-subtitle">
                    User Notifications
                </span>
            </div>
            <div class="m-dropdown__body">
                <div class="m-dropdown__content">
                    <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--brand" role="tablist">
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#topbar_notifications_overdue" role="tab">
                                Overdue
                                <span id="notifBadge" class="badge badge-pill badge-danger"></span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="topbar_notifications_overdue" role="tabpanel">
                            <div class="m-scrollable" data-scrollable="true" data-max-height="300" style="max-height: 300px; min-height: 100px;">
                                <div class="m-list-timeline m-list-timeline--skin-light">
                                    <div id="notif_area" class="m-list-timeline__items">
                                        <div class="m-list-timeline__item">
                                            <template v-if = "count > 0">
                                                <template v-for = "(item, index) in rows">
                                                    <div :id="'notif-'+index" class="notif m-list-timeline__item" :value="item.reference_no">
                                                        <span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
                                                        <span class="m-list-timeline__text">{{ item.firstname }} {{ item.lastname }} has an overdue borrowed product {{ item.asset_name }}</span>
                                                        <span class="m-list-timeline__time">since {{ getToDate(item.date_borrowed) }}</span>
                                                    </div>
                                                    <template v-if = "index == 9">
                                                        <div class="seeAll text-center">
                                                            <a href="<?php echo base_url("eforms/borrowing/overdue_borrowing")?>">see all</a>
                                                        </div>
                                                    </template>
                                                </template>
                                            </template>
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
</li>
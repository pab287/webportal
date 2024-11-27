<div class="m-content">
	<div class="alert-notice">
		<!-- div class="alert alert-success alert-dismissible fade show" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
			<strong>Well done!</strong>
			You successfully read this important alert message.
		</div -->
	</div>
    <div id="task-content" class="row">
        <div class="col-md-3">
            <div class="m-portlet m-portlet--head-sm m-portlet--head-solid-bg m-portlet--bordered m-portlet--rounded">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                <span v-text="row.description">&nbsp;</span>
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools"></div>
                </div>
                <div class="m-portlet__body">
                    <div id="tree_task_item-list"></div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div id="task-preview"></div>
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="m-portlet m-portlet--head-sm m-portlet--rounded">
                                <div class="m-portlet__head">
                                    <div class="m-portlet__head-caption">
                                        <div class="m-portlet__head-title">
                                            <span class="m-portlet__head-icon">
                                                <i class="flaticon-menu-button"></i>
                                            </span>
                                            <h3 class="m-portlet__head-text">
                                                REMARKS
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="m-portlet__head-tools">
                                        <ul class="m-portlet__nav">
                                            <li class="m-portlet__nav-item">
                                                <a href="javascript:void(0);" class="m-portlet__nav-link m-portlet__nav-link--icon btnEdit" v-on:click="updateRemarks(row.unit_id)">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="m-portlet__body">
                                    <template v-if="row.sf_remarks">
                                    <div class="m--font-transform-u" v-text="row.sf_remarks">&nbsp;</div>
                                    </template>
                                    <template v-else>
                                        <div class="alert m-alert--default" role="alert">
                                            <strong>
                                                No Preview, 
                                            </strong>
                                            Remarks not found!
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="m-portlet m-portlet--tabs">
                                <div class="m-portlet__head">
                                    <div class="m-portlet__head-tools">
                                        <ul class="nav nav-tabs m-tabs-line m-tabs-line--danger m-tabs-line--2x" role="tablist">
                                            <li class="nav-item m-tabs__item">
                                                <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs--contractors" role="tab" aria-expanded="false">
                                                    <i class="flaticon-users"></i>
                                                    Contractors
                                                </a>
                                            </li>
                                            <li class="nav-item m-tabs__item">
                                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_tabs--punchlist_logs" role="tab" aria-expanded="true">
                                                    <i class="flaticon-edit"></i>
                                                    Punchlist Logs
                                                </a>
                                            </li>
                                            <li class="nav-item m-tabs__item">
                                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_tabs--activities" role="tab" aria-expanded="false">
                                                    <i class="flaticon-calendar"></i>
                                                    Activities
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="m-portlet__body">
                                    <div class="tab-content">
                                    <div class="tab-pane active" id="m_tabs--contractors" role="tabpanel" aria-expanded="false">
                                            <div class="m-scrollable" data-scrollable="true" style="max-height: 350px; min-height: auto;">
                                                <div id="m-widget4--content">
                                                <template v-if="contractor_count > 0">
                                                        <div class="m-widget4 fadeIn animated">
                                                            <div class="m-widget4__item" v-for="(item, index) in contractors">
                                                                <div class="m-widget4__info">
                                                                    <p class="m-widget4__title m--marginless">{{item.contractor}}</p>
                                                                </div>
                                                                <div class="m-widget4__ext">
                                                                    <template v-if="item.status === '1'">
                                                                        <a href="javascript:void(0);" class="m-btn m-btn--pill m-btn--hover-warning btn btn-sm btn-warning btnView">ACTIVE</a>
                                                                    </template>
                                                                    <template v-if="item.status === '2'">
                                                                        <a href="javascript:void(0);" class="m-btn m-btn--pill m-btn--hover-primary btn btn-sm btn-primary btnView">COMPLETED</a>
                                                                    </template>
                                                                    <template v-if="item.status === '3'">
                                                                        <a href="javascript:void(0);" class="m-btn m-btn--pill m-btn--hover-danger btn btn-sm btn-secondary danger">TERMINATED</a>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </div>
                                                </template>
                                                <template v-else>
                                                    <div class="alert m-alert--default" role="alert">
                                                        <strong>
                                                            No Preview, 
                                                        </strong>
                                                        Contractor not found!
                                                    </div>
                                                </template>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="m_tabs--punchlist_logs" role="tabpanel" aria-expanded="true">
                                            <div class="m-scrollable" data-scrollable="true" style="max-height: 350px; min-height: auto;">
                                                <div id="m-widget3--content">
                                                <template v-if="punchlist_log_count > 0">
                                                    <div class="m-widget3">
                                                        <div class="m-widget3__item" v-for="(item, index) in punchlist_log">
                                                            <div class="m-widget3__header">
                                                                <div class="m-widget3__user-img">
                                                                    <img class="m-widget3__img" src="<?php echo base_url('assets/images/profile/no_image.jpg'); ?>" alt="">
                                                                </div>
                                                                <div class="m-widget3__info">
                                                                    <p class="m-widget3__username m--marginless" v-text="item.user_name">&nbsp;</p>
                                                                    <span class="m-widget3__time" v-text="item.created_at">&nbsp;</span>
                                                                </div>
                                                                <span class="m-widget3__status">
                                                                    <template v-if="item.status == '0'">
                                                                        <a href="javascript:void(0);" @click="previewPunchlistLog(item.id)" class="m-btn m-btn--pill m-btn--hover-dark btn btn-sm btn-default m--font-dark btnEdit">COMPLETED</a>
                                                                    </template>
                                                                    <template v-if="item.status == '1'">
                                                                        <a href="javascript:void(0);" @click="previewPunchlistLog(item.id)" class="m-btn m-btn--pill m-btn--hover-primary btn btn-sm btn-primary primary btnEdit">PUNCHLIST</a>
                                                                    </template>
                                                                    <template v-if="item.status == '2'">
                                                                        <a href="javascript:void(0);" @click="previewPunchlistLog(item.id)" class="m-btn m-btn--pill m-btn--hover-success btn btn-sm btn-success success btnEdit">PUNCHLISTED</a>
                                                                    </template>
                                                                </span>
                                                            </div>
                                                            <div class="m-widget3__body">
                                                                <p class="m-widget3__text" v-text="item.remarks">&nbsp;</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="alert m-alert--default" role="alert">
                                                        <strong>
                                                            No Preview, 
                                                        </strong>
                                                        Task Punchlist Logs!
                                                    </div>
                                                </template>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="m_tabs--activities" role="tabpanel" aria-expanded="false">
                                            <div class="m-scrollable" data-scrollable="true" style="max-height: 350px; min-height: auto;">
                                                <div id="m-widget4--content">
                                                <template v-if="activity_count > 0">
                                                        <div class="m-widget4 fadeIn animated">
                                                            <div class="m-widget4__item" v-for="(item, index) in activity">
                                                                <div class="m-widget4__info">
                                                                    <p class="m-widget4__title m--marginless">{{item.user_name}}</p>
                                                                    <p class="m-widget4__sub">{{item.created_at}}</p>
                                                                    <p class="m-widget4__text m-widget4__text--overflow_limit">{{item.description}}</p>
                                                                    <p class="m--marginless"><strong><span class="m-widget4__sub">{{item.task}}</span></strong></p>
                                                                    <p class="m-widget4__sub m--marginless">{{item.contractor}}</p>
                                                                </div>
                                                                <div class="m-widget4__ext">
                                                                    <!--a href="javascript:void(0);" class="m-btn m-btn--pill m-btn--hover-brand btn btn-sm btn-secondary btnView">
                                                                        Preview
                                                                    </a -->
                                                                    &nbsp;
                                                                </div>
                                                            </div>
                                                        </div>
                                                </template>
                                                <template v-else>
                                                    <div class="alert m-alert--default" role="alert">
                                                        <strong>
                                                            No Preview, 
                                                        </strong>
                                                        Activity not found!
                                                    </div>
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
                <div class="col-md-4">
                    <div class="m-portlet m-portlet--head-sm m-portlet--rounded">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <span class="m-portlet__head-icon">
                                        <i class="flaticon-clipboard"></i>
                                    </span>
                                    <h3 class="m-portlet__head-text">
                                        Status
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools">
                                <ul class="m-portlet__nav">
                                    <li class="m-portlet__nav-item">
                                        <a href="javascript:void(0);" class="m-portlet__nav-link m-portlet__nav-link--icon btnEdit" v-on:click="updateStatus(row.unit_id)">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <h4 class="text-center m--margin-bottom-0" v-text="updateTempStatus" v-bind:class="updateClassStatus">&nbsp;</h4>
                            <ul class="m-nav m-nav--hover-bg m-portlet-fit--sides">
                                <li class="m-nav__separator m-nav__separator--fit m--margin-bottom-15"></li>
                                <li class="m-nav__item" v-if="punchlist_count > 0">
                                    <a href="javascript:void(0);" class="m-nav__link btnEdit" v-on:click="previewPunchlistTask(row.unit_id)">
                                        <i class="m-nav__link-icon flaticon-edit"></i>
                                        <span class="m-nav__link-title">
                                            <span class="m-nav__link-wrap">
                                                <span class="m-nav__link-text">
                                                TASK PUNCHLIST 
                                                </span>
                                                <span class="m-menu__link-badge">
                                                    <span class="m-badge m-badge--danger" v-text="punchlist_count">0</span>
                                                </span>
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="m-nav__item">
                                    <a href="javascript:void(0);" class="m-nav__link btnBack" v-on:click="setRedirectAction(row)">
                                        <i class="m-nav__link-icon flaticon-list"></i>
                                        <span class="m-nav__link-title">
                                            <span class="m-nav__link-wrap">
                                                <span class="m-nav__link-text">
                                                BACK TO UNIT LIST
                                                </span>
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet m-portlet--head-sm m-portlet--rounded" v-if="todo_count > 0">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <span class="m-portlet__head-icon">
                                        <i class="flaticon-notes"></i>
                                    </span>
                                    <h3 class="m-portlet__head-text">
                                        TO DO
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools">
                                <ul class="m-portlet__nav">
                                    <li class="m-portlet__nav-item">
                                        <a href="javascript:void(0);" 
                                        class="m-btn btnEdit m-btn--pill m-btn--hover-success btn btn-sm btn-success" 
                                        v-on:click="previewTodoTask(row.unit_id)">PREVIEW</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="m-widget4">
                                <div class="m-widget4__item" v-for="(item, index) in todo">
                                    <div class="m-widget4__ext">
                                        <span class="m-widget4__icon m--font-brand">
                                            <i class="flaticon-exclamation-1"></i>
                                        </span>
                                    </div>
                                    <div class="m-widget4__info">
                                        <span class="m-widget4__title" :class="item.font_color">{{item.label}}</span>
                                        <br>
                                        <span class="m-widget4__sub">
                                            CURRENT {{item.label}} TASK
                                        </span>
                                    </div>
                                    <span class="m-widget4__ext">
                                        <span class="m-widget4__number" :class="item.font_color">{{item.count}}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <template v-if="pastdue_count > 0">
                        <div class="m-portlet m-portlet--danger m-portlet--head-sm m-portlet--head-solid-bg m-portlet--bordered m-portlet--rounded bounceIn animated">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <span class="m-portlet__head-icon">
                                            <i class="flaticon-calendar"></i>
                                        </span>
                                        <h3 class="m-portlet__head-text"> Past Due</h3>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools"></div>
                            </div>
                            <div class="m-portlet__body">
                                <div class="m-scrollable" data-scrollable="true" style="max-height: 250px; min-height: auto;">
                                    <div id="m-widget4--content" class="fadeIn animated">
                                        <div class="m-widget4">
                                            <div class="m-widget4__item" v-for="(item, index) in pastdue">
                                                <div class="m-widget4__info">
                                                    <p class="m-widget4__title m--marginless">{{item.label}}</p>
                                                    <p class="m-widget4__text m--marginless">DUE DATE : {{item.due_date}}</p>
                                                </div>
                                                <div class="m-widget4__ext">
                                                    <template v-if="item.is_extended === true"><span class="m-badge m-badge--wide m--bg-danger m--font-light">EXTENDED</span></template>
                                                    <template v-else>&nbsp;</template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <!-- div class="col-md-8">
                    <div class="m-portlet m-portlet--head-sm m-portlet--bordered m-portlet--rounded m-portlet--success m-portlet--head-solid-bg">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">
                                        Other Information
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools"></div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="row">
                                <div class="col-md-12">&nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div -->
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-form_task" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <?php echo $this->load->view("pms/task/modal_content/add_form_task", null, true); ?>
            </div>
        </div>
    </div>
	<div class="modal fade" id="modalTempContainer" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content"></div>
        </div>
    </div>
	<div class="modal fade" id="modalTempContainerLg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content"></div>
        </div>
    </div>
    <style>div#selectedItems.has-error { margin-top: 10px !important; }</style>
</div>
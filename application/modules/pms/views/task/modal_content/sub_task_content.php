<div class="modal-header">
<h5 class="modal-title" id="exampleModalLabel" v-text="row.task_name">&nbsp;</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
</button>
</div>
<div class="modal-body">
    <div class="m-widget5">
        <div class="m-widget5__item m--marginless">
            <div class="m-widget5__content">
                <p>
                    <span class="m-widget5__title">Contractor: </span><span class="m-widget5__info" v-text="row.contractor"></span>
                </p>
                <p class="text-justify">
                    <span class="m-widget5__info">DESCRIPTION: </span>
                    <span class="m-widget5__desc" v-text="row.description"></span>
                <p>
                <span class="m-widget5__title">STATUS: </span>
                <span class="m-badge m-badge--wide " v-text="row.state_description" v-bind:class="row.state"></span>
                <a href="javascript:void(0);" class="m-portlet__nav-link btnEdit btn btn-sm btn-outline-success m-btn m-btn--pill m-btn--air pull-right" @click="getSubtaskData(row.id, $event)" v-if="row.is_punchlisted === false">
                    <i class="la la-pencil"></i> Edit Task
                </a>
                </p>
            </div>
        </div>
    </div> 
    <div class="m-portlet m-portlet--head-sm m-portlet--bordered-semi m-portlet--full-height m-portlet--rounded">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="flaticon-calendar"></i>
                    </span>
                    <h3 class="m-portlet__head-text">Activity</h3>
                </div>
            </div>
            <div class="m-portlet__head-tools"></div>
        </div>
        <form id="frmAddActivity" method="post" action="<?php echo site_url("pms/task/set_modal_subtask_activity"); ?>" v-if="row.is_punchlisted === false">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="unit_id" v-model="row.unit_id" />
        <input type="hidden" name="timeline_id" v-model="row.id" />
        <div class="m-portlet__body">
            <div class="form-group m-form__group">
                <textarea class="form-control m-input form-control-sm" name="description" rows="3" data-validation="required"></textarea>
            </div>
            <button type="submit" class="btn btn-success btn-sm m-btn m-btn--custom m-btn--bolder m-btn--uppercase">Save</button>
        </div>
        </form>
        <div id="activityWidget">
            <template v-if="activity_count > 0">
                <div class="m-portlet__foot">
                    <div class="m-widget3">
                        <div class="m-widget3__item" v-for="(item, index) in activity">
                            <div class="m-widget3__header">
                                <div class="m-widget3__user-img">
                                    <img class="m-widget3__img" src="<?php echo base_url("assets/images/profile/no_image.jpg"); ?>" alt="">
                                </div>
                                <div class="m-widget3__info">
                                    <span class="m-widget3__username">{{item.display_name}}</span>
                                    <br>
                                    <span class="m-widget3__time">{{item.created_at}}</span>
                                </div>
                                <div class="m-widget3__status m--font-success">
                                    <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
                                        <a href="#" class="m-dropdown__toggle">
                                            <i class="la la-ellipsis-h"></i>
                                        </a>
                                        <div class="m-dropdown__wrapper">
                                            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 14.7032px;"></span>
                                            <div class="m-dropdown__inner">
                                                <div class="m-dropdown__body">
                                                    <div class="m-dropdown__content">
                                                        <ul class="m-nav">
                                                            <li class="m-nav__item">
                                                                <a href="" class="m-nav__link">
                                                                    <i class="m-nav__link-icon flaticon-share"></i>
                                                                    <span class="m-nav__link-text">
                                                                        Activity
                                                                    </span>
                                                                </a>
                                                            </li>
                                                            <li class="m-nav__item">
                                                                <a href="" class="m-nav__link">
                                                                    <i class="m-nav__link-icon flaticon-chat-1"></i>
                                                                    <span class="m-nav__link-text">
                                                                        Messages
                                                                    </span>
                                                                </a>
                                                            </li>
                                                            <li class="m-nav__item">
                                                                <a href="" class="m-nav__link">
                                                                    <i class="m-nav__link-icon flaticon-info"></i>
                                                                    <span class="m-nav__link-text">
                                                                        FAQ
                                                                    </span>
                                                                </a>
                                                            </li>
                                                            <li class="m-nav__item">
                                                                <a href="" class="m-nav__link">
                                                                    <i class="m-nav__link-icon flaticon-lifebuoy"></i>
                                                                    <span class="m-nav__link-text">
                                                                        Support
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="m-widget3__body">
                                <p class="m-widget3__text">{{item.description}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="m-portlet__foot">
                    <p>No Activities made!</p>
                </div>
            </template>
        </div>
    </div>
</div>
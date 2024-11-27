<div class="modal-header">
<h5 class="modal-title" id="exampleModalLabel">Punchlist Log Details</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="m-portlet m-portlet--unair m-portlet--mobile m-portlet--bordered">
        <div class="m-portlet__body">
            <div id="tree-punchlist_items"></div>
        </div>
    </div>
    <div class="m-widget3">
        <div class="m-widget3__item">
            <div class="m-widget3__header">
                <div class="m-widget3__user-img">
                    <img class="m-widget3__img" src="<?php echo base_url('assets/images/profile/no_image.jpg'); ?>" alt="">
                </div>
                <div class="m-widget3__info">
                    <p class="m-widget3__username m--marginless" v-text="row.logged_by">&nbsp;</p>
                    <span class="m-widget3__time" v-text="row.created_at">&nbsp;</span>
                </div>
                <template v-if="row.status === '1'">
                    <span class="m-widget3__status m--font-primary m--font-boldest" v-text="row.log_status">&nbsp;</span>
                </template>
                <template v-else-if="row.status === '2'">
                    <span class="m-widget3__status m--font-success m--font-boldest" v-text="row.log_status">&nbsp;</span>
                </template>
                <template v-else>
                    <span class="m-widget3__status m--font-dark m--font-boldest" v-text="row.log_status">&nbsp;</span>
                </template>
            </div>
            <div class="m-widget3__body">
                <p class="m-widget3__text"v-text="row.remarks">&nbsp;</p>
            </div>
        </div>
    </div>
</div>
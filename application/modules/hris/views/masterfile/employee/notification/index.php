<div class="m-content">
    <div class="col-lg-3">
        <div class="m-portlet">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text">
                            Nearing One Month Employees
                        </h3>
                    </div>
                </div>
            </div>
            <div class="m-portlet__body">
                <div id="nearingonemonthpage" class="m-widget4">
                    <template v-if = "count > 0">
                        <template v-for = "(item, index) in rows">                   
                            <div class="m-widget4__item">
                                <div class="m-widget4__img m-widget4__img--pic">
                                    <img class="m--img-rounded" :alt="item.fullname" :src="item.profile_pic" title="">
                                </div>
                                <div class="m-widget4__info">
                                    <span class="m-widget4__title">
                                    {{ item.fullname }}
                                    </span>
                                    <br>
                                    <span class="m-widget4__sub">
                                    {{ item.position }}
                                    </span>
                                </div>
                                <div class="m-widget4__ext">
                                    <a :href="redirectViewUrl(item.id)" target="_blank" class="m-btn m-btn--pill  m-btn--hover-info btn btn-sm btn-secondary">
                                        Open
                                    </a>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
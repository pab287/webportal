<div class="modal-header">
<h5 class="modal-title" id="exampleModalLabel">TO DO <small>TASK</small></h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
</button>
</div>
<div class="modal-body">
    <div class="m-portlet__body m-portlet__body--no-padding">
        <div class="row m-row--no-padding m-row--col-separator-xl">
            <div class="col-md-3 col-lg-3 col-xl-3">
                <div class="m-widget24">
                    <div class="m-widget24__item">
                        <div class="m-widget24__title"><h3>AWAITING</h3></div>
                    </div>
                </div>
                <!--begin:: Widgets/Stats2-1 -->
                <div class="m-widget1">
                    <template v-for="(item, index) in rows" v-if="index === '0'">
                        <div class="m-widget1__item" v-for="(vv, ii) in item.data">
                            <a href="javascript:void(0);" class="m-widget1__link" @click="redirectToTask(ii)">
                                <div class="row m-row--no-padding align-items-center">
                                    <div class="col">
                                        <h3 class="m-widget1__title">{{vv.parent}}</h3>
                                        <p class="m--marginless m--font-dark m--font-bolder" v-for="(v, i) in vv.child">{{v}}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </template>
                </div>
                <!--end:: Widgets/Stats2-1 -->
            </div>
            <div class="col-md-3 col-lg-3 col-xl-3">
                <div class="m-widget24">
                    <div class="m-widget24__item">
                        <div class="m-widget24__title"><h3>IN PROGRESS</h3></div>
                    </div>
                </div>
                <!--begin:: Widgets/Stats2-1 -->
                <div class="m-widget1">
                    <template v-for="(item, index) in rows" v-if="index === '1'">
                        <div class="m-widget1__item" v-for="(vv, ii) in item.data">
                            <a href="javascript:void(0);" class="m-widget1__link" @click="redirectToTask(ii)">
                                <div class="row m-row--no-padding align-items-center">
                                    <div class="col">
                                        <h3 class="m-widget1__title">{{vv.parent}}</h3>
                                        <p class="m--marginless m--font-brand m--font-bolder" v-for="(v, i) in vv.child">{{v}}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </template>
                </div>
                <!--end:: Widgets/Stats2-1 -->
            </div>
            <div class="col-md-3 col-lg-3 col-xl-3">
                <div class="m-widget24">
                    <div class="m-widget24__item">
                        <div class="m-widget24__title"><h3>BACKLOG</h3></div>
                    </div>
                </div>
                <!--begin:: Widgets/Stats2-1 -->
                <div class="m-widget1">
                    <template v-for="(item, index) in rows" v-if="index === '2'">
                        <div class="m-widget1__item" v-for="(vv, ii) in item.data">
                            <a href="javascript:void(0);" class="m-widget1__link" @click="redirectToTask(ii)">
                                <div class="row m-row--no-padding align-items-center">
                                    <div class="col">
                                        <h3 class="m-widget1__title">{{vv.parent}}</h3>
                                        <p class="m--marginless m--font-danger m--font-bolder" v-for="(v, i) in vv.child">{{v}}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </template>
                </div>
                <!--end:: Widgets/Stats2-1 -->
            </div>
            <div class="col-md-3 col-lg-3 col-xl-3">
                <div class="m-widget24">
                    <div class="m-widget24__item">
                        <div class="m-widget24__title"><h3>DEFERRED</h3></div>
                    </div>
                </div>
                <!--begin:: Widgets/Stats2-1 -->
                <div class="m-widget1">
                    <template v-for="(item, index) in rows" v-if="index === '3'">
                        <div class="m-widget1__item" v-for="(vv, ii) in item.data">
                            <a href="javascript:void(0);" class="m-widget1__link" @click="redirectToTask(ii)">
                                <div class="row m-row--no-padding align-items-center">
                                    <div class="col">
                                        <h3 class="m-widget1__title">{{vv.parent}}</h3>
                                        <p class="m--marginless m--font-success m--font-bolder" v-for="(v, i) in vv.child">{{v}}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </template>
                </div>
                <!--end:: Widgets/Stats2-1 -->
            </div>
        </div>
    </div>
</div>
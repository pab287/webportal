<div id="sequenceFormGrid">
    <div class="m-form m-form--label-align-right m--margin-bottom-30">
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">&nbsp;</div>
            <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search Block / Lot" id="generalSearch">
                    <span class="m-input-icon__icon m-input-icon__icon--left">
                        <span>
                            <i class="la la-search"></i>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="m-portlet m-portlet--unair">
        <div class="m-scrollable" data-scrollbar-shown="true" data-scrollable="true" data-max-height="500">
            <div class="m-portlet__body m-portlet__body--no-padding">
                <template v-if="rowCount > 0">
                <div class="row" id="my-grid">
                    <div class="col-3 col-md-3 col-lg-3 col-xl-3" v-for="(row, index) in updateRow">
                        <a class="m-redirect--portlet__link" href="javascript:void(0);" v-on:click="renderForm(row.id)" @mouseover="mouseOver" @mouseleave="mouseLeave">
                        <div class="m-portlet m-portlet--bordered-semi m-portlet--full-height " :class="row.bg_color">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <h3 class="m-portlet__head-text" :class="row.text_color">{{row.description}}</h3>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools" :class="row.text_color">
                                <strong>{{row.temp_status}}</strong>
                                </div>
                            </div>
                            <div class="m-portlet__body" :class="row.text_color">
                                <template v-if="row.remarks">
                                <h6>REMARKS</h6>
                                <p><small>{{row.remarks}}</small></p>
                                </template>
                            </div>
                        </div>
                        </a>
                    </div>
                </div>
                </template>
            </div>
        </div>
    </div>
</div>
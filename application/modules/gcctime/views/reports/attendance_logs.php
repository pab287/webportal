<style>
    .m-list-timeline .m-list-timeline__items:before{
        background-color: transparent !important;
    }
</style>

<div class="m-content" id="attendance_logs">
    <div class="row">
        <div class="col-md-4 col-lg-4 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">Filter By</h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <template v-if="!isEmpty(devices)">
                        <div class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="400" style="height: 400px; overflow: visible; max-height: 400px; position: relative;">
                            <div class="m-list-timeline m-list-timeline--skin-light">
                                <div class="m-list-timeline__items">
                                    <div class="m-widget4">
                                        <template v-for="(item, index) in devices">
                                            <div class="m-widget4__item">
                                                <div class="m-widget4__ext">							 
                                                    <span class="m-widget4__icon m--font-brand">
                                                        <i class="flaticon-list-2"></i>
                                                    </span>
                                                </div>
                                                <div class="m-widget4__info">
                                                    <span class="m-widget4__title"> {{ item.name ? toUpperCase(item.name) : toUpperCase('No Device Name') }} </span><br> 
                                                    <span class="m-widget4__sub" v-if="item.device_id != 'all'"> <strong>LOCATION NAME:</strong> {{ item.location ? toUpperCase(item.location) : toUpperCase('No Device Location') }} </span>							 		 
                                                </div>
                                                <div class="m-widget4__ext">
                                                    <a href="javascript:void(0)" class="m-btn m-btn--pill m-btn--hover-brand btn btnView btn-sm btn-secondary" @click="filterByDevice(item.device_id, item.location)">View</a>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="text-center">
                            <h5>NO DEVICE(S) FOUND.</h5>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-lg-8 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">Attendance Logs</h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <button class="btn btn-brand  btn-sm m-btn m-btn--icon" id="attendance-report-date-range-picker">
                            <span>
                                <em class="fa fa-calendar"></em>
                                <span class="selected-filter pl-3 pr-2">Today</span>
                            </span>
                        </button>
                        <button class="btn btn-warning m-btn btn-sm m-btn--icon text-white" id="clear-options" @click="clearFilter">
                            <span>
                                <em class="fa fa-refresh"></em>
                                <span class="selected-filter pl-3 pr-2">Reset Filter</span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-0">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <span v-if="loc_name">LOCATION: <strong> {{ toUpperCase(loc_name) }}</strong></span>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span> <i class="la la-search"></i> </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
                        <table class="table table-striped table-bordered" id="table-logs" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>Employee</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog"
     id="more_details_custom-shift-schedule-modal">
    <div class="modal-dialog modal-custom-shift-schedule modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Details Customize Shift Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="custom-shift_content">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 form-group" style="min-height: 76px;">
                        <label for="">Has Shift Schedule</label>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <i class="fa" :class="row.has_shift ==='1'? 'fa-check-square-o': 'fa-square-o'" style="font-size: 18px; position: relative; top: 2.5px; margin-right: 5px; color: #21242DB0;"></i> YES
                            <i class="fa" :class="row.has_shift ==='1'? 'fa-square-o': 'fa-check-square-o'" style="font-size: 18px; position: relative; top: 2.5px; margin-right: 5px; margin-left: 15px; color: #21242DB0;"></i> NO
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 form-group" style="min-height: 76px;">
                        <label for="">Date</label>
                        <div class="input-group date" id="date-schedule">
                            <span class="input-group-addon">
                                <i class="la la-calendar-check-o"></i>
                            </span>
                            <p class="form-control m--marginless" disabled>{{row.scheduled_date}}</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 form-group" style="min-height: 76px;">
                        <label for="">Weekday</label>
                        <p class="form-control m--marginless" disabled>{{getWeekdayFormat(row.scheduled_date)}}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-0" style="min-height: 76px;">
                        <label for="">AM Start</label>
                        <div class="input-group timepicker">
                            <p class="form-control m--marginless" disabled>{{row.shift_am_start !== ''? updateTimeFormat(row.shift_am_start): '--:--'}}</p>
                            <span class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-0" style="min-height: 76px;">
                        <label for="">AM End</label>
                        <div class="input-group timepicker">
                        <p class="form-control m--marginless" disabled>{{row.shift_am_end !== ''? updateTimeFormat(row.shift_am_end): '--:--'}}</p>
                            <span class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-0" style="min-height: 76px;">
                        <label for="">PM Start</label>
                        <div class="input-group timepicker">
                        <p class="form-control m--marginless" disabled>{{row.shift_pm_start !== ''? updateTimeFormat(row.shift_pm_start): '--:--'}}</p>
                            <span class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-0" style="min-height: 76px;">
                        <label for="">PM End</label>
                        <div class="input-group timepicker">
                        <p class="form-control m--marginless" disabled>{{row.shift_pm_end !== ''? updateTimeFormat(row.shift_pm_end): '--:--'}}</p>
                            <span class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group m-form__group" v-if="getResourceCount('shift') > 0">
                    <label for="">Shift Schedule</label>
                    <div class="form-control m--height-auto m--padding-5" disabled>
                        <template v-for="(item, index) in row.shift_resource">
                            <span class="m-badge m-badge--metal m-badge--wide m-badge--rounded m--font-boldest m--margin-right-5">{{item}}</span>
                        </template>
                    </div>
                </div>
                <div class="form-group m-form__group" v-if="getResourceCount('employee') > 0">
                    <label for="">Included Employee(s)</label>
                    <div class="form-control m--height-auto m--padding-5" disabled>
                        <template v-for="(item, index) in row.employee_resource">
                            <span class="m-badge m-badge--metal m-badge--wide m-badge--rounded m--font-boldest m--margin-right-5">{{item}}</span>
                        </template>
                    </div>
                </div>
                <div class="form-group m-form__group">
                    <label for="">Remarks</label>
                    <p class="form-control m--marginless m--height-auto" disabled style="min-height: 125px;">{{row.remarks}}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
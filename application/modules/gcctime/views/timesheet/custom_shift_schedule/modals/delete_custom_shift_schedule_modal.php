<div class="modal fade" tabindex="-1" role="dialog"
     id="delete-custom-shift-schedule-modal">
    <div class="modal-dialog modal-delete-custom-shift-schedule modal-lg" role="document">
        <div class="modal-content" id="delete_custom-shift_content">
            <div class="modal-header">
                <h5 class="modal-title">Remove Customize Shift Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--font-boldest m--margin-bottom-5">
                        <label for="">Date:</label> {{row.scheduled_date}}
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--font-boldest m--margin-bottom-5">
                        <label for="">Weekday: </label> {{getWeekdayFormat(row.scheduled_date)}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-5 m--font-boldest">
                        <label for="">AM Start: </label> {{row.shift_am_start !== ''? updateTimeFormat(row.shift_am_start): '--:--'}}
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-5 m--font-boldest">
                        <label for="">AM End: </label> {{row.shift_am_end !== ''? updateTimeFormat(row.shift_am_end): '--:--'}}
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-5 m--font-boldest">
                        <label for="">PM Start: </label> {{row.shift_pm_start !== ''? updateTimeFormat(row.shift_pm_start): '--:--'}}
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-5 m--font-boldest">
                        <label for="">PM End: </label> {{row.shift_pm_end !== ''? updateTimeFormat(row.shift_pm_end): '--:--'}}
                    </div>
                </div>
                <div class="form-group m-form__group">
                    <label for="" class="m--font-boldest">Shift Schedule: </label>
                    <div class="m--height-auto">
                        <template v-for="(item, index) in row.shift_resource">
                            <span class="m-badge m-badge--metal m-badge--wide m-badge--rounded m--font-boldest m--margin-right-5">{{item}}</span>
                        </template>
                    </div>
                </div>
                <div class="form-group m-form__group">
                    <label for="" class="m--font-boldest">Remarks: </label>
                    <p class="m--marginless m--height-auto m--font-boldest">{{row.remarks}}</p>
                </div>
                <div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="m-alert__icon">
                        <i class="flaticon-exclamation-1"></i>
                        <span></span>
                    </div>
                    <div class="m-alert__text">
                        <strong>
                            Remove custom shift!
                        </strong>
                        are you sure you want to remove this shift schedule?
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btnDelete" @click="removeCurrentShiftSchedule">YES</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">NO</button>
            </div>
        </div>
    </div>
</div>
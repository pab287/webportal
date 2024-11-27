<div class="modal fade" tabindex="-1" role="dialog"
     id="custom-shift-schedule-modal">
    <div class="modal-dialog modal-custom-shift-schedule modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customize Shift Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frmAddCustomShift" method="post" action="<?php echo site_url("gcctime/timesheet/set_custom_shift_schedule"); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body" id="tempCustomShiftContent">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 form-group" style="min-height: 76px;">
                            <label>Has Shift Schedule</label>
                            <div class="m-checkbox-inline">
                                <label class="m-checkbox">
                                    <input type="radio" name="has_shift" value="1" checked @change="prop_shift_schedule(event)">YES <span></span>
                                </label>
                                <label class="m-checkbox">
                                    <input type="radio" name="has_shift" value="0" @change="prop_shift_schedule(event)">NO <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 form-group" style="min-height: 76px;">
                            <label for="" class="required">Date</label>
                            <div class="input-group date" id="date-schedule">
                                <span class="input-group-addon">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                                <input id="scheduled_date" name="scheduled_date" type="text" class="form-control" data-validation="required" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-0" style="min-height: 76px;">
                            <label for="">AM Start</label>
                            <div class="input-group timepicker">
                                <input name="shift[am_start]" type="text" class="form-control check-atleast_one" autocomplete="off" id="am-start" :disabled="prop_shift === false"  />
                                <span class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-0" style="min-height: 76px;">
                            <label for="">AM End</label>
                            <div class="input-group timepicker">
                                <input name="shift[am_end]" type="text" class="form-control check-atleast_one" autocomplete="off" id="am-end" :disabled="prop_shift === false" />
                                <span class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-0" style="min-height: 76px;">
                            <label for="">PM Start</label>
                            <div class="input-group timepicker">
                                <input name="shift[pm_start]" type="text" class="form-control check-atleast_one" autocomplete="off" id="pm-start" :disabled="prop_shift === false" />
                                <span class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-0" style="min-height: 76px;">
                            <label for="">PM End</label>
                            <div class="input-group timepicker">
                                <input name="shift[pm_end]" type="text" class="form-control check-atleast_one" autocomplete="off" id="pm-end" :disabled="prop_shift === false" />
                                <span class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-12 text-left" style="margin-top: -15px; margin-bottom: 10px;" v-if="prop_shift === true"><input type="hidden" id="atleast_one" data-validation="atleast_one" /></div>
                    </div>
                    <div class="form-group m-form__group">
                        <label for="">Shift Schedule</label>
                        <select class="form-control select2-multiple-custom" 
                            name="shift_id[]" 
                            id="shift_id" 
                            multiple>
                        </select>
                    </div>
                    <div class="form-group m-form__group">
                        <label for="">Included Employee(s)</label>
                        <select class="form-control select2-multiple-custom" 
                            name="employee_id[]" 
                            id="employee_id" 
                            multiple>
                        </select>
                    </div>
                    <div class="form-group m-form__group">
                        <label for="" class="required">Remarks</label>
                        <textarea name="remarks" class="form-control" data-validation="required" rows="7"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnSave">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
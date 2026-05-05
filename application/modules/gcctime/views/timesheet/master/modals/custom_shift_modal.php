<div class="modal fade" tabindex="-1" role="dialog" id="modal-custom-shift-schedule">
    <div class="modal-dialog modal-custom-shift-schedule modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Shift Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frmAddCustomShift" method="post" action="<?php echo site_url("gcctime/timesheet/set_timesheet_custom_shift_schedule"); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body" id="tempCustomShiftContent">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Shift Schedule List</label>
                                <select class="form-control select2" id="shift-schedule-list">
                                    <option value="">Select Shift Schedule</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-0" style="min-height: 76px;">
                            <label for="">AM Start</label>
                            <div class="input-group timepicker">
                                <input name="shift[am_start]" type="text" class="form-control check-atleast_one" autocomplete="off" id="_am-start" />
                                <span class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </span>
                            </div> 
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-0" style="min-height: 76px;">
                            <label for="">AM End</label>
                            <div class="input-group timepicker">
                                <input name="shift[am_end]" type="text" class="form-control check-atleast_one" autocomplete="off" id="_am-end" />
                                <span class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-0" style="min-height: 76px;">
                            <label for="">PM Start</label>
                            <div class="input-group timepicker">
                                <input name="shift[pm_start]" type="text" class="form-control check-atleast_one" autocomplete="off" id="_pm-start" />
                                <span class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 form-group m--margin-bottom-0" style="min-height: 76px;">
                            <label for="">PM End</label>
                            <div class="input-group timepicker">
                                <input name="shift[pm_end]" type="text" class="form-control check-atleast_one" autocomplete="off" id="_pm-end" />
                                <span class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-12 text-left" style="margin-top: -15px; margin-bottom: 10px;">
                            <input type="hidden" id="atleast_one" data-validation="atleast_one" />
                        </div>
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
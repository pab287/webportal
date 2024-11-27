<div class="modal fade" tabindex="-1" role="dialog" id="edit-time-adjustment-modal">
    <form action="" id="frm-edit-time-adjustment-modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Time Adjustment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="temp-create-adjustment-content">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <p class="m--regular-font-size-lg3 m--font-bolder mb-0" id="employee-name"></p>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 d-flex flex-row justify-content-end align-items-center">
                            <p class="m--regular-font-size-lg3 m--font-bolder mb-0" id="record-date"></p>
                        </div>
                    </div>
                    <hr/>

                    <div id="to-container" class="mt-4 m--hide">
                        <div class="form-group">
                            <label for="to-list" class="m--font-bolder">
                                <span>SELECT TRAVEL ORDER REFERENCE No.</span>
                                <span class="m--font-danger">*</span>
                            </label>
                            <select name="to[]" id="to-list"
                                    class="form-control select2-multiple-custom select2-multiple-custom--remove-right"
                                    multiple>
                            </select>
                        </div>
                        <hr class="mt-5 mb-3">
                    </div>

                    <div id="loa-container" class="mt-4 m--hide">
                        <div class="form-group">
                            <label for="loa-list" class="m--font-bolder">
                                <span>SELECT LOA REFERENCE No.</span>
                                <span class="m--font-danger">*</span>
                            </label>
                            <select name="loa[]" id="loa-list"
                                    class="form-control select2-multiple-custom select2-multiple-custom--remove-right"
                                    multiple></select>
                        </div>
                        <hr class="mt-5 mb-3">
                    </div>

                    <div id="overtime-container" class="m--hide">
                        <div class="mt-4" id="list-container">
                            <p class="m--regular-font-size-lg3 m--font-bolder">OVERTIME</p>
                            <table class="table table-bordered" id="tbl-overtime">
                                <thead>
                                <tr>
                                    <th style="vertical-align: top;">DETAILS</th>
                                    <th width="15%" class="text-center">
                                        <span data-toggle="m-tooltip"
                                              data-skin="dark"
                                              data-original-title="Regular Overtime Hours"
                                              data-delay='{"show": 600}'
                                              style="cursor: pointer;">
                                        REG. <br> OT HRS.
                                        </span>
                                    </th>
                                    <th width="15%" class="text-center">
                                        <span data-toggle="m-tooltip"
                                              data-skin="dark"
                                              data-original-title="Regular Overtime Accredited Hours"
                                              data-delay='{"show": 600}'
                                              style="cursor: pointer;">
                                            REG. OT ACC. HRS.
                                        </span>
                                    </th>
                                    <th width="15%" class="text-center">
                                        <span data-toggle="m-tooltip"
                                              data-skin="dark"
                                              data-original-title="Night Differential Overtime Hours"
                                              data-delay='{"show": 600}'
                                              style="cursor: pointer;">
                                        N-DIFF. OT HRS.
                                        </span>
                                    </th>
                                    <th width="15%" class="text-center">
                                        <span data-toggle="m-tooltip"
                                              data-skin="dark"
                                              data-original-title="Night Differential Accredited Hours"
                                              data-delay='{"show": 600}'
                                              style="cursor: pointer;">
                                            N-DIFF. OT ACC. HRS.
                                        </span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <hr class="mt-5 mb-3">
                    </div>
                    
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <p class="m--regular-font-size-lg1 m--font-bolder">Morning</p>
                            <div class="row">
                                <div class="form-group m--font-bolder col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="am_in">In</label>
                                        </div>
                                        <div class="col-6 d-flex flex-row justify-content-end">
                                            <i class="fa fa-undo icon-button mr-3"
                                               data-toggle="m-tooltip"
                                               data-original-title="Reset"
                                               data-skin="dark"
                                               data-placement="top"
                                               data-delay='{"show": 500}'
                                               onclick="resetField('am_in')">
                                            </i>
                                            <i class="fa fa-plus icon-button"
                                               data-toggle="m-tooltip"
                                               data-original-title="Manual Entry"
                                               data-skin="dark"
                                               data-placement="top"
                                               data-delay='{"show": 500}'
                                               onclick="openManualEntryModal('am_in', 'Morning Time in')">
                                            </i>
                                        </div>
                                    </div>
                                    <select id="am_in" class="form-control"></select>
                                </div>
                                <div class="form-group m--font-bolder col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="am_out">Out</label>
                                        </div>
                                        <div class="col-6 d-flex flex-row justify-content-end">
                                            <i class="fa fa-undo icon-button mr-3"
                                               data-toggle="m-tooltip"
                                               data-original-title="Reset"
                                               data-skin="dark"
                                               data-placement="top"
                                               data-delay='{"show": 500}'
                                               onclick="resetField('am_out')">
                                            </i>
                                            <i class="fa fa-plus icon-button"
                                               data-toggle="m-tooltip"
                                               data-original-title="Manual Entry"
                                               data-skin="dark"
                                               data-placement="top"
                                               data-delay='{"show": 500}'
                                               onclick="openManualEntryModal('am_out', 'Morning Time out')"></i>
                                        </div>
                                    </div>
                                    <select id="am_out" class="form-control"></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <p class="m--regular-font-size-lg1 m--font-bolder">Afternoon</p>
                            <div class="row">
                                <div class="form-group m--font-bolder col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="pm_in">In</label>
                                        </div>
                                        <div class="col-6 d-flex flex-row justify-content-end">
                                            <i class="fa fa-undo icon-button mr-3"
                                               data-toggle="m-tooltip"
                                               data-original-title="Reset"
                                               data-skin="dark"
                                               data-placement="top"
                                               data-delay='{"show": 500}'
                                               onclick="resetField('pm_in')">
                                            </i>
                                            <i class="fa fa-plus icon-button"
                                               data-toggle="m-tooltip"
                                               data-original-title="Manual Entry"
                                               data-skin="dark"
                                               data-placement="top"
                                               data-delay='{"show": 500}'
                                               onclick="openManualEntryModal('pm_in', 'Afternoon Time in')"></i>
                                        </div>
                                    </div>
                                    <select id="pm_in" class="form-control"></select>
                                </div>
                                <div class="form-group m--font-bolder col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="pm_in">Out</label>
                                        </div>
                                        <div class="col-6 d-flex flex-row justify-content-end">
                                            <i class="fa fa-undo icon-button mr-3"
                                               data-toggle="m-tooltip"
                                               data-original-title="Reset"
                                               data-skin="dark"
                                               data-placement="top"
                                               data-delay='{"show": 500}'
                                               onclick="resetField('pm_out')">
                                            </i>
                                            <i class="fa fa-plus icon-button"
                                               data-toggle="m-tooltip"
                                               data-original-title="Manual Entry"
                                               data-skin="dark"
                                               data-placement="top"
                                               data-delay='{"show": 500}'
                                               onclick="openManualEntryModal('pm_out', 'Afternoon Time out')"></i>
                                        </div>
                                    </div>
                                    <select id="pm_out" class="form-control"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group m--font-bolder col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-0">
                            <label for="">Allow Late Time Adjustment</label>
                        </div>
                        <div class="form-group m--font-bolder col-xl-8 col-lg-8 col-md-8 col-sm-12  mb-0">
                            <div class="m-checkbox-inline">
                                <label class="m-checkbox">
                                    <input type="radio" name="adjustment_override" value="1" v-model="override_adjustment" /> YES<span></span>
                                </label>
                                <label class="m-checkbox">
                                    <input type="radio" name="adjustment_override" value="0" v-model="override_adjustment" /> NO<span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php /*** temporarily remove manaul overtime request 
                    <template v-if="overtime.has_manual_overtime === true">
                    <hr class="mt-2">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="row">
                                <div class="col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                    <p class="m--regular-font-size-lg1 m--font-bolder">Overtime</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group m--font-bolder col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="am_in">In</label>
                                            <div class="input-group date">
                                                <p id="temp_overtime_in" class="form-control m-input m--marginless" disabled>{{overtime.ot_in}}</p>
                                                <span class="input-group-addon m--bg-success" style="border-color: #34bfa3;"
                                                    data-toggle="m-tooltip" 
                                                    data-original-title="Edit" 
                                                    data-skin="dark" 
                                                    data-placement="top" 
                                                    data-delay='{"show": 500}' 
                                                    onclick="openManualOvertimeEntryModal('overtime_in', 'Manual Overtime In')">
                                                    <i class="la la-calendar text-white"></i>
                                                </span>
                                            </div>
                                            <input type="hidden" id="overtime_in" name="overtime_in" v-model="overtime.ot_in" />
                                        </div>
                                        <div class="col-6">
                                            <label for="am_out">Out</label>
                                            <div class="input-group date">
                                                    <p id="temp_overtime_out" class="form-control m-input m--marginless" disabled>{{overtime.ot_out}}</p>
                                                    <span class="input-group-addon m--bg-success" style="border-color: #34bfa3;" 
                                                        data-toggle="m-tooltip" 
                                                        data-original-title="Edit" 
                                                        data-skin="dark" 
                                                        data-placement="top" 
                                                        data-delay='{"show": 500}' 
                                                        onclick="openManualOvertimeEntryModal('overtime_out', 'Manual Overtime Out')">
                                                        <i class="la la-calendar text-white"></i>
                                                    </span>
                                                </div>
                                                <input type="hidden" id="overtime_out" name="overtime_out" v-model="overtime.ot_out" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group m--font-bolder col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="temp-reg_ot">REG. OT</label>
                                            <input type="number" class="form-control m-input" name="overtime_reg_hrs" v-model="overtime.reg_ot" />
                                        </div>
                                        <div class="col-6">
                                            <label for="temp-ndiff_ot">N-DIFF. OT</label>
                                            <input type="number" class="form-control m-input" name="overtime_ndiff_hrs" v-model="overtime.ndiff_ot" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group m--font-bolder col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <label>REQUESTED BY</label>
                                    <select id="requested_by" class="form-control m-input" name="overtime_requested_by"></select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group m--font-bolder col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                    <label >PURPOSE</label>
                                    <textarea class="form-control m-input" rows="5" name="overtime_purpose" data-validation="required">{{overtime.purpose}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-4">
                    </template> ***/ ?>
                    <div class="form-group mt-4">
                        <div class="row">
                            <div class="col-6">
                                <label for="remarks">
                                    <span>Remarks</span>
                                    <span class="m--font-danger m--font-boldest mr-2">*</span>
                                    <span class="m--font-bolder text-muted">(SHIFT + ENTER TO SUBMIT)</span>
                                </label>
                            </div>
                        </div>
                        <textarea name="remarks" id="remarks" rows="5" class="form-control" data-validation="required"></textarea>
                    </div>
                    <hr class="mt-4">
                    <div id="shift-schedule">
                        <div class="mt-4">
                            <p class="m--regular-font-size-lg3 m--font-bolder">SHIFT SCHEDULE</p>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <p class="m--regular-font-size-lg1 m--font-bolder">Morning</p>
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group m--font-bolder">
                                            <label for="shift_am_start">Start</label>
                                            <input type="time" class="form-control" id="shift_am_start" name=""
                                                   disabled>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group m--font-bolder">
                                            <label for="shift_am_end">End</label>
                                            <input type="time" class="form-control" id="shift_am_end" name="" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <p class="m--regular-font-size-lg1 m--font-bolder">Afternoon</p>
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group m--font-bolder">
                                            <label for="shift_pm_start">Start</label>
                                            <input type="time" class="form-control" id="shift_pm_start" name=""
                                                   disabled>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group m--font-bolder">
                                            <label for="shift_pm_end">End</label>
                                            <input type="time" class="form-control" id="shift_pm_end" name="" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="manual-shift-schedule" class="m--hide">
                        <hr class="mt-5">
                        <div class="mt-4">
                            <p class="m--regular-font-size-lg3 m--font-bolder">ADD SHIFT SCHEDULE</p>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <p class="m--regular-font-size-lg1 m--font-bolder">Morning</p>
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group m--font-bolder">
                                            <label for="am_start">Start</label>
                                            <input type="time" class="form-control" id="am_start" name="am_start">
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group m--font-bolder">
                                            <label for="am_end">End</label>
                                            <input type="time" class="form-control" id="am_end" name="am_end">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <p class="m--regular-font-size-lg1 m--font-bolder">Afternoon</p>
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group m--font-bolder">
                                            <label for="pm_start">Start</label>
                                            <input type="time" class="form-control" id="pm_start" name="pm_start">
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group m--font-bolder">
                                            <label for="pm_end">End</label>
                                            <input type="time" class="form-control" id="pm_end" name="pm_end">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="message-container"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnUpdate">Save Changes</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>
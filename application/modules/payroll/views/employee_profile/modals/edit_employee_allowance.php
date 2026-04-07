<div class="modal fade" id="edit-employee-allowance-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="frm-edit-allowance" action="<?php echo site_url("payroll/employee/update_employee_allowance"); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="emp_id" value="<?php echo $data->id; ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Allowance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="allowance" class="form-control-label">
                            Allowance
                        </label>
                        <input type="text" class="form-control" id="allowance-name"
                               data-validation="required" autocomplete="off" disabled/>
                    </div>

                    <div class="form-group mt-4">
                        <div class="row">
                            <label class="form-control-label col-6 required">
                                Amount
                            </label>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <input type="number" name="rate" class="form-control" step="0.01"
                                       data-validation="required" autocomplete="off"/>
                            </div>
                        </div>
                    </div>

                    <div class="m-form__group form-group mt-4">
                        <label for="" class="required">Frequency</label>
                        <div class="m-radio-inline">
                            <label class="m-radio">
                                <input type="radio" name="frequency" value="day" checked>
                                DAILY
                                <span></span>
                            </label>
                            <label class="m-radio">
                                <input type="radio" name="frequency" value="month">
                                MONTHLY
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="">Active ?</label>
                        <div>
                            <span class="m-switch m-switch--icon">
                                <label>
                                    <input type="checkbox" name="is_active">
                                    <span></span>
                                </label>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="editAllowanceApprovalOption">
                        <template v-if="approving_authority">
                            <button type="submit" class="btn btn-success btn-submit btnUpdate"><i class="la la-thumbs-up mr-2"></i>Save And Approve</button>
                        </template>
                        <template v-else>
                            <button type="submit" class="btn btn-primary btn-submit btnUpdate">Save Changes</button>
                        </template>
                    </div>
                    <button class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
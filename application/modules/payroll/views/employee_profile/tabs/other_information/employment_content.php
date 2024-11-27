<div class="m-portlet__body">
    <div class="form-group m-form__group row">
        <div class="col-12 ml-auto">
            <h4 class="m-form__header m-form__section">Other Information</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div id="accordionOtherEmploymentData" class="accordion" role="tablist" aria-multiselectable="true">
                <div class="card">
                    <div id="headingCashAdvance"
                         class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm"
                         role="tab">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherEmploymentData"
                                   href="#collapseCashAdvance" aria-expanded="false" aria-controls="collapseCashAdvance">
                                    <h5 class="m-portlet__head-text">
                                        <span>Cash Advance</span>
                                        <i class="la pull-right la-angle-down"></i>
                                    </h5>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="collapseCashAdvance" class="collapse" role="tabpanel" aria-labelledby="headingCashAdvance"
                         data-parent="#accordionOtherEmploymentData" style="">
                        <div class="card-body m-portlet__body--custom">
                            <table id="tbl-cash_advance_list" class="table display table-bordered table-striped" width="100%"></table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="headingSalaryHistory"
                         class="card-header m-portlet m-portlet--bordered m-portlet--unair m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm"
                         role="tab">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherEmploymentData"
                                   href="#collapseSalaryHistory" aria-expanded="false" aria-controls="collapseSalaryHistory">
                                    <h5 class="m-portlet__head-text">
                                        <span>Salary History</span>
                                        <i class="la pull-right la-angle-down"></i>
                                    </h5>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="collapseSalaryHistory" class="collapse" role="tabpanel" aria-labelledby="headingSalaryHistory"
                         data-parent="#accordionOtherEmploymentData" style="">
                        <div class="card-body m-portlet__body--custom">
                            <table id="tbl-salary-history" class="table display table-bordered table-striped" width="100%"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="add-salary-history-modal">
        <form id="frm-add-salary-history">
            <input type="hidden" name="csrf_token"
                   value="<?= $this->security->get_csrf_hash() ?>">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Salary History</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="emp_id" id="add-salary-emp-id">

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-1">
                                <div class="form-group">
                                    <label for="">Date</label>
                                    <div class="input-group date sal_date_container" id="">
                                        <input type="text" class="form-control"
                                               data-validation="required" name="sal_date" autocomplete="off">
                                        <span class="input-group-addon">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-1">
                                <div class="form-group">
                                    <label for="">Rate</label>
                                    <input type="text" class="form-control money text-right"
                                           data-validation="required" name="sal_rate" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="form-group pt-3">
                            <label for="">Position</label>
                            <select name="sal_position" id="" class="form-control"></select>
                        </div>

                        <div class="form-group pt-3">
                            <label for="">Remarks</label>
                            <textarea class="form-control" data-validation="required" name="sal_remarks" autocomplete="off"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnSave">Save</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="edit-salary-history-modal">
        <form id="frm-edit-salary-history">
            <input type="hidden" name="csrf_token"
                   value="<?= $this->security->get_csrf_hash() ?>">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Salary History</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-1">
                                <div class="form-group">
                                    <label for="">Date</label>
                                    <div class="input-group date sal_date_container">
                                        <input type="text" class="form-control"
                                               data-validation="required" name="sal_date" autocomplete="off">
                                        <span class="input-group-addon">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-1">
                                <div class="form-group">
                                    <label for="">Rate</label>
                                    <input type="text" class="form-control money text-right"
                                           data-validation="required" name="sal_rate" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="form-group pt-3">
                            <label for="">Position</label>
                            <select name="sal_position" id="" class="form-control" data-validation="required"></select>
                        </div>

                        <div class="form-group pt-3">
                            <label for="">Remarks</label>
                            <textarea class="form-control" data-validation="required" name="sal_remarks" autocomplete="off"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnUpdate">Save Changes</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog"
     id="monthly-employees-list-modal">
    <div class="modal-dialog modal-monthly-employee-list modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">MONTHLY EMPLOYEES</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="m-form" id="frm-monthly_employee">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="modal-body">
                <div class="row">
                    <div class="col-10 col-md-10 col-lg-10 col-xl-10 col-sm-12">
                        <div class="form-group m-form__group">
                            <label for="monthlyEmployeeSelect2">Employee Name</label>
                            <select class="form-control m-input" id="monthlyEmployeeSelect2" name="emp_id" data-validation="required">
                                <option value="">&nbsp;</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-2 col-md-2 col-lg-2 col-xl-2 col-sm-12">
                        <button type="submit" class="btn btnNew btn-success m--margin-top-25">Include</button>
                    </div>
                </div>
            </div>
            </form>
            <div class="modal-body">
                <div class="row">
                <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-search"></i>
                            </div>
                            <input type="text" id="searchMonthlyPaidEmployees" class="form-control" placeholder="Search here..." autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
                    <table class="table table-bordered table-hover" id="tbl-monthly-employees" width="100%">
                        <colgroup>
                            <col width="*" />
                            <col width="45%" />
                            <col width="8%" />
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Created By</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
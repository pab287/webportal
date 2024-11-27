<div class="m-content">
    <div class="row">

        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Filter
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <form action="" class="m-form" onsubmit="event.preventDefault(); filterEmployeesOfSalaryRange(this)">
                        <div class="form-group m-form__group">
                            <label for="">Employee Status</label>
                            <select name="employee_status" id="employee_status" class="form-control" data-validation="required">
                                <option value=""></option>
                                <option value="Active">Active</option>
                                <option value="Awol">Awol</option>
                                <option value="Resign">Resigned</option>
                                <option value="Black Listed">Black Listed</option>
                                <option value="End of Contract">End of Contract</option>
                                <option value="Terminated">Terminated</option>
                                <option value="Retired">Retired</option>
                            </select>
                        </div>

                        <div class="form-group m-form__group">
                            <label for="">Company</label>
                            <select name="company" id="company" class="form-control">
                            <option></option>
                            </select>
                        </div>

                        <div class="form-group m-form__group">
                            <label for="">Department</label>
                            <select name="department" id="department" class="form-control">
                            <option></option>
                            </select>
                        </div>

                        <div class="form-group m-form__group">
                            <label for="">Position</label>
                            <select name="position" id="position" class="form-control">
                               <option></option>
                            </select>
                        </div>

                        <div class="row py-4">
                            <div class="col-xl-6">
                                <div class="form-group m-form__group">
                                    <label for="">From</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">&#8369;</span>
                                        <input name="sal_range_from" type="text" class="form-control text-right money" value="0.00"
                                               data-validation="required">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="form-group m-form__group">
                                    <label for="">To</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">&#8369;</span>
                                        <input name="sal_range_to"
                                               type="text" class="form-control text-right money" value="0.00"
                                               data-validation="required">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-row justify-content-end pt-4">
                            <button class="btn btn-primary">Go</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-9 col-md-8 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Employee's Salary Range
                                <small>
                                    reports
                                </small>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="table-responsive">
                        <table id="table-employee-salary-range" width="100%" class="table table-hover table-bordered">
                            <thead>
                            <tr>
                                <th>Company</th>
                                <th>Department</th>
                                <th>Employee</th>
                                <th>Position</th>
                                <th>Salary Rate</th>
                                <th>Remarks</th>
                                <th>Date Hired</th>
                                <th>Tenureship</th>
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
<style>
    .btn.btn-default:hover, .btn.btn-default.active,
    .btn.btn-default:active, .btn.btn-default:focus, .show > .btn.btn-default.dropdown-toggle,
    .btn.btn-secondary:hover, .btn.btn-secondary.active, .btn.btn-secondary:active,
    .btn.btn-secondary:focus, .show > .btn.btn-secondary.dropdown-toggle {
        background-color: #716aca;
        border-color: #716aca;
        color: #fff;
    }

    .custom-fullname a {
        color: #1b1b1b;
    }
</style>
<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Employee Masterfile
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">

                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-15">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1 d-flex flex-row">
                                <div class="flex-grow-0 flex-shrink-0 mr-3">
                                    <a href="<?= base_url('payroll/employee/add_employee') ?>"
                                       class="btn btn-success m-btn m-btn--icon btnNew">
                                    <span>
                                        <i class="fa fa-plus"></i>
                                        <span>NEW EMPLOYEE</span>
                                    </span>
                                    </a>
                                </div>
                                <div class="flex-grow-0 flex-shrink-0">
                                    <div class="btn-group m-btn-group btn-group-toggle m--margin-bottom-25"
                                         data-toggle="buttons">
                                        <label class="btn btn-default">
                                            <input type="radio" value="All" name="filter" autocomplete="off"
                                                   onchange="filterEmployees(this);">All
                                        </label>
                                        <label class="btn btn-default active">
                                            <input type="radio" value="Active" name="filter" autocomplete="off"
                                                   onchange="filterEmployees(this);" checked>Active
                                        </label>
                                        <label class="btn btn-default">
                                            <input type="radio" value="Inactive" name="filter" autocomplete="off"
                                                   onchange="filterEmployees(this);">Inactive
                                        </label>
                                        <label class="btn btn-default">
                                            <input type="radio" value="Resign" name="filter" autocomplete="off"
                                                   onchange="filterEmployees(this);">Resign
                                        </label>
                                        <label class="btn btn-default">
                                            <input type="radio" value="Terminated" name="filter" autocomplete="off"
                                                   onchange="filterEmployees(this);">Terminated
                                        </label>
                                        <label class="btn btn-default">
                                            <input type="radio" value="Awol" name="filter" autocomplete="off"
                                                   onchange="filterEmployees(this);">Awol
                                        </label>
                                        <label class="btn btn-default">
                                            <input type="radio" value="Black Listed" name="filter" autocomplete="off"
                                                   onchange="filterEmployees(this);">Black
                                            Listed
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid"
                                           placeholder="Search..." id="generalSearch">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
										<span>
											<i class="la la-search"></i>
										</span>
									</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
                        <table class="table table-striped table-bordered" id="table-employee" width="100%">
                            <thead>
                            <tr>
                                <th>Image</th>
                                <th>Employee</th>
                                <th>201 File Status</th>
                                <th>Work Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

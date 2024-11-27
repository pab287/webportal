<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        EXCLUDED EMPLOYEES
                        <small>
                            FROM TIMESHEET
                        </small>
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="row">
                <div class="col-xl-7 col-lg-7 col-md-7 col-sm-12 order-2 order-xl-1">
                    <button type="button" class="btn btn-danger m-btn m-btn--icon btnMass_delete"
                            id="btn-mass-delete" onclick="openConfirmationModal('massDelete')" disabled>
                        <span>
                            <i class="fa fa-trash"></i>
                            <span>Delete</span>
                        </span>
                    </button>
                    <button type="button" class="btn btn-success m-btn m-btn--icon btnNew"
                            data-toggle="modal" data-target="#add-excluded-employee-from-timesheet-modal">
                        <span>
                            <i class="fa fa-plus"></i>
                            <span>Add Employee</span>
                        </span>
                    </button>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 offset-xl-2 offset-lg-2 offset-md-2 offset-sm2 order-1">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-search"></i>
                            </div>
                            <input type="text" id="search" class="form-control" placeholder="Search here...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive-sm mt-2">
                <table class="table table-bordered table-hover"
                       id="tbl-excluded-employees" width="100%">
                    <thead>
                    <tr>
                        <th>
                            <label class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                                <input type="checkbox" id="cb-select-all">
                                <span></span>
                            </label>
                        </th>
                        <th class="no-sort"></th>
                        <th>Name</th>
                        <th>Reason</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <?php
        $this->load->view("modals/add_employee_modal");
        $this->load->view("modals/edit_excluded_employee_modal");
        $this->load->view("../modals/confirmation_modal");
        $this->load->view('../modals/alert_modal');
    ?>
</div>
<div class="modal fade" tabindex="-1" role="dialog"
     id="excluded-employees-list-modal">
    <div class="modal-dialog modal-excluded-employee-list" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">EXCLUDED EMPLOYEES FROM TIMESHEET</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 order-2 order-xl-1">
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
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 order-1">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-search"></i>
                                </div>
                                <input type="text" id="search" class="form-control" placeholder="Search here..." autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive-sm">
                    <table class="table table-bordered table-hover"
                           id="tbl-excluded-employees" width="100%">
                        <thead>
                        <tr>
                            <th>
                                <label class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                                    <input type="checkbox" id="cb-select-all-excluded">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
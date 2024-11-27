<div class="row">
    <div class="col-xl-8 order-2 order-xl-1">
        <button class="btn btn-success m-btn--pill btnAdd_maintenance mb-1" type="button"
                data-toggle="modal" data-target="#create-maintenance-template-log">
            <i class="fa fa-plus"></i>
            <span class="ml-1">Create Template Log</span>
        </button>
        <button class="btn btn-danger m-btn--pill btnAdd_maintenance mb-1" type="button"
                onclick="openConfirmDeleteMaintenanceItem(0, 1)">
            <i class="fa fa-trash-o"></i>
            <span class="ml-1">Delete Log</span>
        </button>
    </div>
    <div class="col-xl-4 order-1 order-xl-2">
        <div class="form-group m-form__group pb-0">
            <div class="m-input-icon m-input-icon--left">
                <span class="m-input-icon__icon m-input-icon__icon--left" style="z-index: 5;">
                    <span>
                        <i class="la la-binoculars"></i>
                    </span>
                </span>
                <div class="input-group">
                    <input type="search" class="form-control" placeholder="Search Here..."
                           style="height: auto;" id="search-vehicle-maintenance" autocomplete="off">
                    <span class="input-group-btn">
                        <button class="btn btn-secondary btnNew tab-clear-search" type="button"
                                title="Clear Search" data-placement="bottom" style="border-color: #cdcdcd;">
                            <i class="la la-close"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll mt-3 table-responsive">
    <table class="table table-striped table-bordered" id="tab-maintenance-table" width="100%">
        <thead>
        <tr>
            <th>DESCRIPTION</th>
            <th>LAST ODO VALUE</th>
            <th>LAST SCHEDULE</th>
            <th>NEXT ODO VALUE</th>
            <th>DAYS</th>
            <th>NEXT SCHEDULE</th>
            <th>ACTIONS</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
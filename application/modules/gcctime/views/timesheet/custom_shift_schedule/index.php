<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        CUSTOM SHIFT SCHEDULE
                        <small>FOR TIMESHEET</small>
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">&nbsp;</div>
        </div>
        <div class="m-portlet__body">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 d-flex flex-row">
                    <button type="button" class="btn btn-success btnMass_action" data-toggle="modal" data-target="#custom-shift-schedule-modal">
                        <i class="fa fa-plus"></i>
                        <span>Add Shift</span>
                    </button>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 d-flex flex-row justify-content-end">&nbsp;</div>
            </div>
            <div class="table-responsive-sm mt-2">
                <table id="tbl-time-custom_shift_schedule" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Weekday</th>
                        <th>Shift In</th>
                        <th>Shift Out</th>
                        <th>Shift In</th>
                        <th>Shift Out</th>
                        <th>Assigned Shift</th>
                        <th>Included Employee(s)</th>
                        <th>Assigned Employee(s)</th>
                        <th>Shift</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <?php $this->load->view('modals/custom_shift_schedule_modal'); ?>
    <?php $this->load->view('modals/edit_custom_shift_schedule_modal'); ?>
    <?php $this->load->view('modals/delete_custom_shift_schedule_modal'); ?>
    <?php $this->load->view('modals/more_details_custom_shift_schedule_modal'); ?>
</div>
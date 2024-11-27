<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        TIME ADJUSTMENTS
                        <small>
                            LIST OF REQUESTS
                        </small>
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <div class="m-checkbox-inline cb-statuses-container">
                    <label class="m-checkbox m-checkbox--bold m--font-boldest">
                        <input type="checkbox" value="0" checked>
                        Pending
                        <span></span>
                    </label>
                    <label class="m-checkbox m-checkbox--bold m--font-boldest">
                        <input type="checkbox" value="1">
                        Approved
                        <span></span>
                    </label>
                    <label class="m-checkbox m-checkbox--bold m--font-boldest">
                        <input type="checkbox" value="2">
                        Declined
                        <span></span>
                    </label>
                    <label class="m-checkbox m-checkbox--bold m--font-boldest">
                        <input type="checkbox" value="3">
                        Cancelled
                        <span></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 d-flex flex-row">
                <?php $currentAction = $this->core_layout->getCurrentActions(); ?>
                <?php if(in_array("approve_action", $currentAction)): ?>
                    <div class="btn-group mass-actions">
                        <button type="button" disabled class="btn btn-success btnApprove_action"
                                onclick="openConfirmationModal(true, 1)">
                            Approve
                        </button>
                        <button type="button" disabled
                                class="btn btn-success dropdown-toggle dropdown-toggle-split btnMass_action"
                                data-toggle="dropdown" style="background-color: #2ca189;"
                                aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">
                                Toggle Dropdown
                            </span>
                        </button>
                        <div class="dropdown-menu" x-placement="bottom-start"
                             style="position: absolute; transform: translate3d(93px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                            <a class="dropdown-item btnApprove_action" href="javascript:void(0)"
                               onclick="openConfirmationModal(true, 0)">
                                <i class="fa fa-undo m--font-warning"></i>
                                <span>UNDO</span>
                            </a>
                            <a class="dropdown-item btnApprove_action" href="javascript:void(0)"
                               onclick="openConfirmationModal(true, 2)">
                                <i class="fa fa-thumbs-o-down m--font-danger"></i>
                                <span>DECLINE</span>
                            </a>
                            <a class="dropdown-item btnApprove_action" href="javascript:void(0)"
                               onclick="openConfirmationModal(true, 3)">
                                <i class="fa fa-ban m--font-metal"></i>
                                <span>CANCEL</span>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 d-flex flex-row justify-content-end">
                    <button class="btn btn-primary m-btn m-btn--icon btnAdvance_search"
                            onclick="openFilterModal()">
                        <span>
                            <i class="fa fa-filter"></i>
                            <span>FILTER</span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="table-responsive-sm mt-2">
                <table id="tbl-time-adjustments" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Employee</th>
                        <th>
                            <label class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                                <input type="checkbox" id="cb-select-all"><span></span>
                            </label>
                        </th>
                        <th>&nbsp;</th>
                        <th>Date</th>
                        <th>Day</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Shift In</th>
                        <th>Shift Out</th>
                        <th>Shift In</th>
                        <th>Shift Out</th>
                        <th>Late</th>
                        <th>
                            <span data-toggle="m-tooltip" style="cursor: pointer;"
                                  data-original-title="UNDERTIME" data-skin="dark"
                                  data-delay='{"show": 300}'>
                                        UT
                            </span>
                        </th>
                        <th>
                            <span data-toggle="m-tooltip" style="cursor: pointer;"
                                  data-original-title="REGULAR HOURS" data-skin="dark"
                                  data-delay='{"show": 300}'>
                                        REG. HRS.
                            </span>
                        </th>
                        <th>
                            <span data-toggle="m-tooltip" style="cursor: pointer;"
                                  data-original-title="OVERTIME" data-skin="dark"
                                  data-delay='{"show": 300}'>
                                        OT
                            </span>
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php
        $this->load->view('../modals/confirmation_modal_with_remarks');
        $this->load->view('../modals/alert_modal');
        $this->load->view('../modals/container_modal');
        $this->load->view('modals/edit_time_adjustment_modal');
        $this->load->view('modals/edit_time_adjustment_time_manual_entry_modal');
        $this->load->view('timesheet/master/modals/overtime_manual_entry_modal');
        $this->load->view('timesheet/master/modals/time_manual_overtime_entry_modal');
        $this->load->view('modals/filter_modal', array("companies" => $companies));
    ?>
</div>
<div class="modal-header">
    <h5 class="modal-title">TIME ADJUSTMENT DETAILS</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <p class="m--regular-font-size-lg3 m--font-bolder mb-0">
                <?= $row->employee_name ?>
            </p>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 d-flex flex-row justify-content-end align-items-center">
            <p class="m--regular-font-size-lg3 m--font-bolder mb-0">
                <?= date("m/d/Y, D", strtotime($row->date)) ?>
            </p>
        </div>
    </div>
    <hr/>

    <div class="row mt-4">
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
            <div class="m--font-bolder text-muted">STATUS</div>
            <div class="mt-1">
                <?php
                    switch (intval($row->status)) {
                        case 1:
                            echo "<span style='border-radius: 3em;' class='m-badge m-badge--success m--font-boldest m-badge--custom-large'>Approved</span>";
                            break;
                        case 2:
                            echo "<span style='border-radius: 3em;' class='m-badge m-badge--danger m--font-boldest m-badge--custom-large'>Declined</span>";
                            break;
                        case 3:
                            echo "<span style='border-radius: 3em;' class='m-badge m-badge--metal m--font-boldest m-badge--custom-large'>Cancelled</span>";
                            break;
                        default:
                            echo "<span style='border-radius: 3em;' class='m-badge m-badge--warning m--font-boldest m-badge--custom-large'>Pending</span>";
                            break;
                    }
                ?>
            </div>
        </div>
        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="m--font-bold text-muted">CREATED BY</div>
                    <div class="m--font-bolder m--regular-font-size-sm1">
                        <?= $creator ?> <span class="text-muted">AT</span> <?= date('M d, Y h:i:s A', strtotime($row->created_at)) ?>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-4">
                    <?php if (intval($row->status) > 0) { ?>
                        <div class="m--font-bolder text-muted">
                            <?php
                                switch (intval($row->status)) {
                                    case 1: echo "APPROVED BY"; break;
                                    case 2: echo "DECLINED BY"; break;
                                    default: echo "CANCELLED BY"; break;
                                } ?>
                        </div>

                        <div class="m--font-bolder m--regular-font-size-sm1 mt-1">
                            <?= $confirmed_by ?> AT <?= date("M d,Y h:i:s A", strtotime($row->confirmed_at)) ?>
                        </div>
                        <div class="m--font-bolder m--regular-font-size-sm1 mt-1">
                            <div class="line-clamp line-clamp-2"
                                data-toggle="m-tooltip" data-original-title="Click to expand."
                                data-skin="dark" onclick="expandConfirmationRemarks(this)"
                                data-delay='{"show": 600}' style="cursor: pointer;">
                                <?= $row->confirmation_remarks ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive-sm <?= sizeof($selected_loa) <= 0 ? ' m--hide' : '' ?>">
        <hr class="mt-4">
        <div class="d-inline-block">
            <i class="fa fa-calendar" style="font-size: 14px;"></i>
            <span class="m--font-bolder ml-2">LEAVE OF ABSENCE REFERENCES</span>
        </div>
        <table class="table table-striped table-bordered mt-1">
            <thead>
            <tr>
                <th width="50%">REFERENCE No.</th>
                <th>REASON</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($selected_loa as $loa): ?>
                <tr>
                    <td>
                        <div class="m--font-boldest"><?= $loa->reference_no ?></div>
                        <div class="d-inline">
                            <div class="d-inline m--regular-font-size-sm1 m--font-bolder text-muted">
                                <?php
                                    switch (intval($loa->type)) {
                                        case 1:
                                            echo "Undertime, ";
                                            break;
                                        case 2:
                                            echo "Half Day, ";
                                            break;
                                        case 3:
                                            echo "Whole Day, ";
                                            break;
                                        default:
                                            echo "Other, ";
                                            break;
                                    }
                                ?>
                            </div>
                            <div class="d-inline m--regular-font-size-sm1 m--font-boldest">
                                <?php
                                    if (intval($loa->type) === 1 || intval($loa->type) === 2) {
                                        echo date("M d,Y h:i A", strtotime($loa->date_from)) . " - " . date("h:i A", strtotime($loa->date_to));
                                    } else if (intval($loa->type) === 3) {
                                        echo date("F d,Y", strtotime($loa->date_from));
                                    } else {
                                        echo date("M d,Y h:i A", strtotime($loa->date_from)) . " - " . date("M d,Y h:i A", strtotime($loa->date_to));
                                    }
                                ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="m--font-bolder m--regular-font-size-sm1"><?= $loa->reason ?></p>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="table-responsive-sm <?= sizeof($selected_travel_order) <= 0 ? ' m--hide' : '' ?>">
        <hr class="mt-4">
        <div class="d-inline-block">
            <i class="fa fa-car"></i>
            <span class="m--font-bolder ml-2">TRAVEL ORDER REFERENCES</span>
        </div>
        <table class="table table-striped table-hover table-bordered mt-1">
            <thead>
            <tr>
                <th width="20%">REFERENCE NO.</th>
                <th>DESTINATION & PURPOSE</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($selected_travel_order as $travel_order): ?>
                <tr>
                    <td>
                        <div class="m--font-bolder"><?= $travel_order->reference_no ?></div>
                    </td>
                    <td>
                        <?php
                            $destinations = explode(",", $travel_order->destination);
                            $purposes = explode(",", $travel_order->purpose);
                            foreach ($destinations as $key => $destination) { ?>
                                <div class="mb-2">
                                    <div class="m--font-bolder"><?= $destination ?></div>
                                    <div class="m--regular-font-size-sm1"><span class="text-muted">PURPOSE:</span>
                                    <?php if(isset($purposes[$key]) && $purposes[$key]): ?>
                                    <span class="m--font-bolder"><?= $purposes[$key]; ?></span>
                                    <?php else: ?>
                                        <span class="m--font-bolder">No purpose stated.</span>
                                    <?php endif; ?>
                                    </div>
                                </div>
                            <?php } ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div id="overtime-container" class="<?= sizeof($overtime) <= 0 ? 'm--hide' : '' ?>">
        <hr class="mt-4">
        <div id="list-container">
            <p class="m--regular-font-size-lg3 m--font-bolder">OVERTIME</p>
            <table class="table table-bordered" id="tbl-overtime">
                <thead>
                <tr>
                    <th style="vertical-align: top;">DETAILS</th>
                    <th width="15%" class="text-center">
                                    <span data-toggle="m-tooltip"
                                            data-skin="dark"
                                            data-original-title="Regular Overtime Hours"
                                            data-delay='{"show": 600}'
                                            style="cursor: pointer;">
                                    REG. <br> OT HRS.
                                    </span>
                    </th>
                    <th width="15%" class="text-center">
                                    <span data-toggle="m-tooltip"
                                            data-skin="dark"
                                            data-original-title="Regular Overtime Accredited Hours"
                                            data-delay='{"show": 600}'
                                            style="cursor: pointer;">
                                        REG. OT ACC. HRS.
                                    </span>
                    </th>
                    <th width="15%" class="text-center">
                                    <span data-toggle="m-tooltip"
                                            data-skin="dark"
                                            data-original-title="Night Differential Overtime Hours"
                                            data-delay='{"show": 600}'
                                            style="cursor: pointer;">
                                    N-DIFF. OT HRS.
                                    </span>
                    </th>
                    <th width="15%" class="text-center">
                                    <span data-toggle="m-tooltip"
                                            data-skin="dark"
                                            data-original-title="Night Differential Accredited Hours"
                                            data-delay='{"show": 600}'
                                            style="cursor: pointer;">
                                        N-DIFF. OT ACC. HRS.
                                    </span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($overtime as $_overtime): ?>
                    <tr>
                        <td rowspan="2">
                            <div>
                                <span class="mr-2 text-muted">REFERENCE #:</span>
                                <span class="m--font-boldest"><?= isset($_overtime->reference_no) && $_overtime->reference_no? $_overtime->reference_no: "---" ?></span>
                                <span class="mr-2 text-muted">REQUESTOR:</span>
                                <span class="m--font-bolder"><?= $_overtime->requestor ?></span>
                            </div>
                            <div>
                                <span class="mr-2 text-muted">DATE:</span>
                                <?php
                                    $date_from = new DateTime($_overtime->date_from);
                                    $date_to = new DateTime($_overtime->date_to);
                                    $highlightROT = doubleval($_overtime->total_hrs) !== doubleval($_overtime->adj_value) ? 'm--font-danger' : '';
                                    $highlightNDOT = doubleval($_overtime->ndiff_hrs) !== doubleval($_overtime->ndiff_adj_value) ? 'm--font-danger' : '';

                                    if ($date_from->format("Y-m-d") === $date_to->format("Y-m-d")) {
                                        echo '<span class="m--font-bolder">' . $date_from->format("M d,Y h:i A") . '-' . $date_to->format("h:i A") . '</span>';
                                    } else {
                                        echo '<span class="m--font-bolder">' . $date_from->format("M d,Y h:i A") . '-' . $date_to->format("M d,Y h:i A") . '</span>';
                                    }
                                ?>
                            </div>
                            <div class="mt-2">
                                <div class="mr-2 text-muted m--regular-font-size-sm1">PURPOSE</div>
                                <div class="m--font-bolder" style="text-align: justify;"><?= $_overtime->purpose ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" disabled=""
                                        class="form-control form-control--table"
                                        value="<?= $_overtime->total_hrs ?>">
                                <p class="m--regular-font-size-sm3 text-center mb-0 mt-1 m--font-boldest text-muted">
                                    <?= number_format(($_overtime->total_hrs * 60), 2, '.', '') ?> Mins.
                                </p>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <?php
                                    $reg_ot_acc_hrs_diff = ceil($_overtime->adj_value) - floor($_overtime->adj_value);
                                    $reg_ot_acc_hrs = doubleval($reg_ot_acc_hrs_diff) > 0 ? number_format($_overtime->adj_value, 2, ".", "") : $_overtime->adj_value;
                                ?>
                                <input type="text" disabled=""
                                        class="form-control form-control--table  <?= $highlightROT ?>"
                                        value="<?= $reg_ot_acc_hrs ?>">
                                <p class="m--regular-font-size-sm3 text-center mb-0 mt-1 m--font-boldest text-muted">
                                    <?= number_format(($_overtime->adj_value * 60), 2, '.', '') ?> Mins.
                                </p>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" disabled=""
                                        class="form-control form-control--table"
                                        value="<?= $_overtime->ndiff_hrs ?>">
                                <p class="m--regular-font-size-sm3 text-center mb-0 mt-1 m--font-boldest text-muted">
                                    <?= number_format(($_overtime->ndiff_hrs * 60), 2, '.', '') ?> Mins.
                                </p>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <?php
                                    $n_diff_acc_ot_hrs_diff = ceil($_overtime->ndiff_adj_value) - floor($_overtime->ndiff_adj_value);
                                    $n_diff_acc_ot_hrs = doubleval($n_diff_acc_ot_hrs_diff) > 0 ? number_format($_overtime->ndiff_adj_value, 2, ".", "") : $_overtime->ndiff_adj_value;
                                ?>
                                <input type="text" disabled=""
                                        class="form-control form-control--table <?= $highlightNDOT ?>"
                                        value="<?= $n_diff_acc_ot_hrs ?>">
                                <p class="m--regular-font-size-sm3 text-center mb-0 mt-1 m--font-boldest text-muted">
                                    <?= number_format(($_overtime->ndiff_adj_value * 60), 2, '.', '') ?> Mins.
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-0 text-center">
                                        <label class="m--font-bolder">Overtime IN</label>
                                        <input type="text" disabled="" class="form-control text-center"
                                                value="<?= isset($_overtime->adj_ot_in_value) && $_overtime->adj_ot_in_value ? date("Y-m-d h:i A", strtotime($_overtime->adj_ot_in_value)): "0000-00-00 00:00"; ?>" 
                                                style="font-weight: 600;">
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-0 text-center">
                                        <label  class="m--font-bolder">Overtime OUT</label>
                                        <input type="text" disabled="" class="form-control text-center"
                                                value="<?= isset($_overtime->adj_ot_out_value) && $_overtime->adj_ot_out_value ? date("Y-m-d h:i A", strtotime($_overtime->adj_ot_out_value)): "0000-00-00 00:00"; ?>" 
                                                style="font-weight: 600;">
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="shift-details m-form">
        <hr class="mt-4">
        <div class="row">
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                <p class="m--regular-font-size-lg1 m--font-bolder">Morning</p>
                <div class="row">
                    <div class="form-group m--font-bolder col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <label>In</label>
                        <input type="time" disabled
                               class="form-control <?= intval($row->am_in_is_requested) === 1 ? 'm--font-boldest m--font-danger' : '' ?>"
                               value="<?= $row->am_in ?>">
                        <span class="m--font-bolder m--font-dark m--regular-font-size-sm1">
                            <?= intval($row->am_in_is_requested) === 1 ? empty($row->am_in_prev) ? 'No previous value' : 'Previous: <span class="m--font-boldest">' . date('h:i A', strtotime($row->am_in_prev)) . "</span>" : "" ?>
                    </div>
                    <div class="form-group m--font-bolder col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <label>Out</label>
                        <input type="time" disabled
                               class="form-control <?= intval($row->am_out_is_requested) === 1 ? 'm--font-boldest m--font-danger' : '' ?>"
                               value="<?= $row->am_out ?>">
                        <span class="m--font-bolder m--font-dark m--regular-font-size-sm1">
                            <?= intval($row->am_out_is_requested) === 1 ? empty($row->am_out_prev) ? 'No previous value' : 'Previous: <span class="m--font-boldest">' . date('h:i A', strtotime($row->am_out_prev)) . "</span>" : "" ?>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                <p class="m--regular-font-size-lg1 m--font-bolder">Afternoon</p>
                <div class="row">
                    <div class="form-group m--font-bolder col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <label>
                            <span>In</span>
                        </label>
                        <input type="time" disabled
                               class="form-control <?= intval($row->pm_in_is_requested) === 1 ? 'm--font-boldest m--font-danger' : '' ?>"
                               value="<?= $row->pm_in ?>">
                        <span class="m--font-bolder m--font-dark m--regular-font-size-sm1">
                            <?= intval($row->pm_in_is_requested) === 1 ? empty($row->pm_in_prev) ? 'No previous value' : 'Previous: <span class="m--font-boldest">' . date('h:i A', strtotime($row->pm_in_prev)) . "</span>" : "" ?>
                        </span>
                    </div>
                    <div class="form-group m--font-bolder col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <label>Out</label>
                        <input type="time" disabled
                               class="form-control <?= intval($row->pm_out_is_requested) === 1 ? 'm--font-boldest m--font-danger' : '' ?>"
                               value="<?= $row->pm_out ?>">
                        <span class="m--font-bolder m--font-dark m--regular-font-size-sm1">
                            <?= intval($row->pm_out_is_requested) === 1 ? empty($row->pm_out_prev) ? 'No previous value' : 'Previous: <span class="m--font-boldest">' . date('h:i A', strtotime($row->pm_out_prev)) . "</span>" : "" ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group m--font-bolder col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-0">
                <label>Allow Late Time Adjustment</label>
            </div>
            <div class="form-group m--font-bolder col-xl-8 col-lg-8 col-md-8 col-sm-12  mb-0">
                <i class="fa <?php echo intval($row->adjustment_override) == 1 ? "fa-check-square-o":"fa-square-o"; ?> fa--custom-2x" 
                    style="margin-right: 5px; margin-left: 5px;"></i> <span>YES</span>
                <i class="fa <?php echo intval($row->adjustment_override) == 0 ? "fa-check-square-o":"fa-square-o"; ?> fa--custom-2x" 
                    style="margin-right: 5px; margin-left: 5px;"></i> <span>NO</span>
            </div>
        </div>
        <?php if($row->has_manual_overtime == true): ?>
        <hr class="mt-4">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <p class="m--regular-font-size-lg1 m--font-bolder">Overtime</p>
                <div class="row">
                    <div class="form-group m--font-bolder col-xl-8 col-lg-8 col-md-8 col-sm-12">
                        <div class="row">
                            <div class="col-6">
                                <label for="am_in">In</label>
                                <div class="input-group">
                                    <p id="temp-ot_in" class="form-control m-input m--marginless" disabled><?php echo $row->overtime_in; ?></p>
                                    <span class="input-group-addon">
                                        <i class="la la-calendar-check-o glyphicon-th"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="am_out">Out</label>
                                <div class="input-group">
                                    <p class="form-control m-input m--marginless" disabled><?php echo $row->overtime_in; ?></p>
                                    <span class="input-group-addon">
                                        <i class="la la-calendar-check-o glyphicon-th"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group m--font-bolder col-xl-4 col-lg-4 col-md-4 col-sm-12">
                        <div class="row">
                            <div class="col-6">
                                <label for="temp-reg_ot">REG. OT</label>
                                <p class="form-control m-input m--marginless" disabled><?php echo $row->regular_hrs; ?></p>
                            </div>
                            <div class="col-6">
                                <label for="temp-ndiff_ot">N-DIFF. OT</label>
                                <p class="form-control m-input m--marginless" disabled><?php echo $row->ndiff_hrs; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group m--font-bolder col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <label>REQUESTED BY</label>
                        <p class="form-control m-input m--marginless" disabled><?php echo $row->requestor_name; ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group m--font-bolder col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <label >PURPOSE</label>
                        <p class="form-control m-input m--marginless m--height-auto" disabled><?php echo $row->purpose; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <hr class="mt-4">
        <?php endif; ?>
        
        <div class="form-group m-form__group">
            <label for="">Remarks</label>
            <textarea disabled class="form-control"><?= $row->remarks ?></textarea>
        </div>

        <div id="shift-schedule">
            <hr class="mt-4">
            <div class="mt-4">
                <p class="m--regular-font-size-lg3 m--font-bolder">
                    SHIFT SCHEDULE
                    <small class="m--font-danger m--font-boldest ml-2"><?= intval($row->has_shift) === 0 ? "MANUAL ENTRY" : "" ?></small>
                </p>
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <p class="m--regular-font-size-lg1 m--font-bolder">Morning</p>
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group m--font-bolder">
                                <label for="">Start</label>
                                <input type="time" class="form-control <?= intval($row->has_shift) === 0 ? 'm--font-boldest m--font-danger' : '' ?>"
                                       id="" name="" disabled value="<?= $row->am_start ?>">
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group m--font-bolder">
                                <label for="shift_am_end">End</label>
                                <input type="time" class="form-control <?= intval($row->has_shift) === 0 ? 'm--font-boldest m--font-danger' : '' ?>"
                                       id="" name="" disabled value="<?= $row->am_end ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <p class="m--regular-font-size-lg1 m--font-bolder">Afternoon</p>
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group m--font-bolder">
                                <label for="">Start</label>
                                <input type="time" class="form-control <?= intval($row->has_shift) === 0 ? 'm--font-boldest m--font-danger' : '' ?>"
                                       id="" name="" disabled value="<?= $row->pm_start ?>">
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group m--font-bolder">
                                <label for="">End</label>
                                <input type="time" class="form-control <?= intval($row->has_shift) === 0 ? 'm--font-boldest m--font-danger' : '' ?>"
                                       id="" name="" disabled value="<?= $row->pm_end ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <?php if (intval($row->status) === 0): ?>
    <?php $currentAction = $this->core_layout->getCurrentActions(); ?>
        <?php if(in_array("approve_action", $currentAction)): ?>
        <div class="btn-group mass-actions">
            <button type="button" class="btn btn-success btnApprove_action"
                    onclick="openConfirmationModal(false, 1, <?= $row->time_adjustment_id ?>, '#container-modal')">
                Approve
            </button>
            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split"
                    data-toggle="dropdown" style="background-color: #2ca189;"
                    aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">
                                Toggle Dropdown
                            </span>
            </button>
            <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-start"
                 style="position: absolute; transform: translate3d(93px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                <a class="dropdown-item btnApprove_action" href="javascript:void(0)"
                   onclick="openConfirmationModal(false, 2, <?= $row->time_adjustment_id ?>, '#container-modal')">
                    <i class="fa fa-thumbs-o-down m--font-danger"></i>
                    <span>DECLINE</span>
                </a>
                <a class="dropdown-item btnApprove_action" href="javascript:void(0)"
                   onclick="openConfirmationModal(false, 3, <?= $row->time_adjustment_id ?>, '#container-modal')">
                    <i class="fa fa-ban m--font-metal"></i>
                    <span>CANCEL</span>
                </a>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
</div>
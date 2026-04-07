<div class="modal-header">
    <h5 class="modal-title">More Details</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body" style="border-bottom: 1px solid #e9ecef;">
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <p class="m--regular-font-size-lg3 m--font-bolder mb-0">
                <?= $employee->employee_name ?>
            </p>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 d-flex flex-row justify-content-end align-items-center">
            <p class="m--regular-font-size-lg3 m--font-bolder mb-0">
                <?= date("m/d/Y, D", strtotime($date)) ?>
            </p>
        </div>
    </div>
</div>
<div class="modal-body">
    <div id="holiday-container" class="table-responsive-sm m--hide">
        <i class="fa fa-flag mr-2" style="font-size: 14px;"></i>
        <span class="mb-2 m--font-bolder">HOLIDAY</span>
        <table class="table table-striped table-bordered mt-1">
            <colgroup>
                <col style="width: 60%;" />
                <col style="width: 40%;" />
            </colgroup>
            <thead>
            <tr>
                <th>Description</th>
                <th>Classification</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="m--font-boldest"><?= $holiday_references->description ?></div>
                        <div class="d-inline">
                            <div class="d-inline m--regular-font-size-sm1 m--font-bolder text-muted">
                                Start Date: <?php echo date("Y/m/d", strtotime($holiday_references->start_date)); ?>
                            </div>
                            -
                            <div class="d-inline m--regular-font-size-sm1 m--font-bolder text-muted">
                                End Date: <?php echo date("Y/m/d", strtotime($holiday_references->end_date)); ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="m--font-bolder m--regular-font-size-sm1"><?= $holiday_references->classification; ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr class="mt-4">
    </div>

    <div id="loa-container" class="table-responsive-sm m--hide">
        <i class="flaticon-event-calendar-symbol mr-2" style="font-size: 14px;"></i>
        <span class="mb-2 m--font-bolder">APPLICABLE LEAVE OF ABSENCES</span>

        <table class="table table-striped table-bordered mt-1">
            <colgroup>
                <col style="width: 50%;" />
                <col style="width: 50%;" />
            </colgroup>
            <thead>
            <tr>
                <th>Reference No</th>
                <th>Reason</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($loa_references as $loa): ?>
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
                                    } elseif (intval($loa->type) === 3) {
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
        <hr class="mt-4">
    </div>

    <div id="travelorder-container" class="table-responsive-sm m--hide">
        <i class="fa fa-car mr-2"></i>
        <span class="mb-2 m--font-bolder">APPLICABLE TRAVEL ORDERS</span>

        <table class="table table-striped table-bordered mt-1">
            <colgroup>
                <col style="width: 30%;" />
                <col style="width: 70%;" />
            </colgroup>
            <thead>
            <tr>
                <th>Reference No</th>
                <th>DESTINATION & PURPOSE</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($to_references as $travel_order): ?>
                <tr>
                    <td>
                        <div class="m--font-bolder"><?= $travel_order->reference_no ?></div>
                    </td>
                    <td>
                        <?php
                            $destinations = explode("||", $travel_order->destination);
                            $purposes = explode("||", $travel_order->purpose);
                            $dates = explode("||", $travel_order->to_dates);
                            foreach ($destinations as $key => $destination) { ?>
                                <?= $key > 0 ? "<hr class='mt-3' />" : "" ?>
                                <div class="mb-2">
                                    <div class="m--font-bolder"><?= $destination ?></div>
                                    <div class="m--regular-font-size-sm1">
                                        <span class="text-muted">PURPOSE:</span>
                                        <?php if (isset($purposes[$key])): ?>
                                            <span class="m--font-bolder"><?= $purposes[$key] ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">NO PURPOSE STATED.</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="mt-2">
                                <div class="m--font-boldest">TRAVEL ORDER DATE &amp; TIME</div>
                                <div class="m--regular-font-size-sm1">
                                    <span class="m--font-boldest"><?= isset($dates[$key]) && $dates[$key] ? $dates[$key]: "---" ?></span>
                                </div>
                            </div>
                            <?php } ?>
                            
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <hr class="mt-4">
    </div>

    <div id="overtime-container" class="m--hide">
        <div class="mt-4" id="list-container">
            <p class="m--regular-font-size-lg3 m--font-bolder">OVERTIME</p>
            <table class="table table-bordered" id="tbl-overtime">
                <colgroup>
                    <col style="width: 40%;" />
                    <col style="width: 15%;" />
                    <col style="width: 15%;" />
                    <col style="width: 15%;" />
                    <col style="width: 15%;" />
                </colgroup>
                <thead>
                <tr>
                    <th style="vertical-align: top;">DETAILS</th>
                    <th class="text-center">
                        <span data-toggle="m-tooltip"
                                data-skin="dark"
                                data-original-title="Regular Overtime Hours"
                                data-delay='{"show": 600}'
                                style="cursor: pointer;">
                        REG. <br> OT HRS.
                        </span>
                    </th>
                    <th class="text-center">
                        <span data-toggle="m-tooltip"
                                data-skin="dark"
                                data-original-title="Regular Overtime Accredited Hours"
                                data-delay='{"show": 600}'
                                style="cursor: pointer;">
                            REG. OT ACC. HRS.
                        </span>
                    </th>
                    <th class="text-center">
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
                    <?php
                        $strtotimeFrom = strtotime(date("Y-m-d", strtotime($_overtime->date_from)));
                        $strtotimeTo = strtotime(date("Y-m-d", strtotime($_overtime->date_to)));
                        $currDate = strtotime($date);
                        ?>
                    <?php if($currDate >= $strtotimeFrom && $currDate <= $strtotimeTo): ?>
                    <tr>
                        <td rowspan="2">
                            <div>
                                <span class="mr-2 text-muted">REFERENCE #:</span>
                                <span class="m--font-boldest"><?= $_overtime->reference_no; ?></span>
                            </div>
                            <div>
                                <span class="mr-2 text-muted">REQUESTOR:</span>
                                <span class="m--font-bolder"><?= $_overtime->requestor ?></span>
                            </div>
                            <div>
                                <span class="mr-2 text-muted">DATE:</span>
                                <?php
                                    $date_from = new DateTime($_overtime->date_from);
                                    $date_to = new DateTime($_overtime->date_to);

                                    if ($date_from->format("Y-m-d") === $date_to->format("Y-m-d")) {
                                        echo '<span class="m--font-bolder">' . $date_from->format("M d,Y h:i A") . '-' . $date_to->format("h:i A") . '</span>';
                                    } else {
                                        echo '<span class="m--font-bolder">' . $date_from->format("M d,Y h:i A") . '-' . $date_to->format("M d,Y h:i A") . '</span>';
                                    }
                                ?>
                            </div>
                            <div class="mt-2">
                                <div class="mr-2 text-muted m--regular-font-size-sm1">PURPOSE</div>
                                <div class="m--font-bolder"
                                     style="text-align: justify;"><?= $_overtime->purpose ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" disabled=""
                                       class="form-control form-control--table"
                                       value="<?= isset($_overtime->total_hrs) ? $_overtime->total_hrs : '0.00' ?>">
                                <p class="m--regular-font-size-sm3 text-center mb-0 mt-1 m--font-boldest text-muted">
                                    <?= isset($overtime->total_hrs) ? number_format(($_overtime->total_hrs * 60), 2, '.', '') : '0.00' ?> Mins.
                                </p>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" disabled=""
                                       class="form-control form-control--table"
                                       value="<?= isset($_overtime->accredited_hrs) ? $_overtime->accredited_hrs: '0.00' ?>">
                                <p class="m--regular-font-size-sm3 text-center mb-0 mt-1 m--font-boldest text-muted">
                                    <?= isset($_overtime->accredited_hrs) ? number_format(($_overtime->accredited_hrs * 60), 2, '.', '') : '0.00' ?> Mins.
                                </p>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" disabled=""
                                       class="form-control form-control--table"
                                       value="<?= isset($_overtime->ndiff_hrs) ? $_overtime->ndiff_hrs: '0.00' ?>">
                                <p class="m--regular-font-size-sm3 text-center mb-0 mt-1 m--font-boldest text-muted">
                                    <?= isset($_overtime->ndiff_hrs) ? number_format(($_overtime->ndiff_hrs * 60), 2, '.', ''): '0.00' ?> Mins.
                                </p>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" disabled=""
                                       class="form-control form-control--table"
                                       value="<?= isset($_overtime->accredited_ndiff_hrs) ? $_overtime->accredited_ndiff_hrs : '0.00' ?>">
                                <p class="m--regular-font-size-sm3 text-center mb-0 mt-1 m--font-boldest text-muted">
                                    <?= isset($_overtime->accredited_ndiff_hrs) ? number_format(($_overtime->accredited_ndiff_hrs * 60), 2, '.', ''): '0.00' ?> Mins.
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-0 text-center">
                                        <label for="" class="m--font-bolder">Overtime IN</label>
                                        <input type="text" disabled="" class="form-control text-center"
                                                value="<?= isset($_overtime->overtime_in) ? date("Y-m-d h:i A", strtotime($_overtime->overtime_in)): date("Y-m-d h:i A", strtotime($_overtime->date_from)) ?>"
                                                style="font-weight: 600;">
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-0 text-center">
                                        <label for="" class="m--font-bolder">Overtime OUT</label>
                                        <input type="text" disabled="" class="form-control text-center"
                                                value="<?= isset($_overtime->overtime_out) ? date("Y-m-d h:i A", strtotime($_overtime->overtime_out)) : date("Y-m-d h:i A", strtotime($_overtime->date_to)) ?>"
                                                style="font-weight: 600;">
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <hr class="mt-4">
    </div>
    
    <div class="shift-details">
        <div class="row">
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                <p class="m--regular-font-size-lg3 m--font-bolder">MORNING</p>

                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                        <label for="" class="m--font-bolder">IN</label>
                        <input type="time" disabled class="form-control"
                            value="<?= empty($timesheet) ? null : ($timesheet->am_in && $timesheet->am_in !== null ? $timesheet->am_in: NULL ); ?>">
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                        <label for="" class="m--font-bolder">OUT</label>
                        <input type="time" disabled class="form-control"
                            value="<?= empty($timesheet) ? null : ($timesheet->am_out && $timesheet->am_out !== null ? $timesheet->am_out: NULL ); ?>">
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                <p class="m--regular-font-size-lg3 m--font-bolder">AFTERNOON</p>

                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                        <label for="" class="m--font-bolder">IN</label>
                        <input type="time" disabled class="form-control"
                            value="<?= empty($timesheet) ? null : ($timesheet->pm_in && $timesheet->pm_in !== null ? $timesheet->pm_in: NULL); ?>">
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                        <label for="" class="m--font-bolder">OUT</label>
                        <input type="time" disabled class="form-control"
                            value="<?= empty($timesheet) ? null : ($timesheet->pm_out && $timesheet->pm_out !== null? $timesheet->pm_out: NULL); ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- JP05 | Biometric Location Start -->
        <style>
            .site_punch_wrap {
                gap: 20px;
            }

            .site_punch_wrap .site_item {
                background: #f4f5f8;
                border-radius: 5px;
                border: 1px solid #dfe0e2;
                padding: 10px;
            }

            .site_title {
                font-size: 10px;
                letter-spacing: 0.5px;
            }

            .punch_wrap span:not(:last-child) {
                margin: 0 0 3px;
            }

            .site_item.col-12 { flex: 100% 0 0; max-width: max-content; }
            .site_item.col-6 { flex: 50% 0 0; }
            .site_item.col-4-3 {flex: 31.5% 0 0;}
            .site_item.col-4 { flex: 23% 0 0; }
        </style>

        <?php if (!empty($devices)) : ?>
            
        <div class="attendance-location my-4">
            <p class="m--regular-font-size-lg3 m--font-bolder">ATTENDANCE LOCATIONS</p>
     
            <div class="row mx-0 site_punch_wrap">
                <?php 
                    $count = count($devices);

                    $class = 'col-4-3'; // default (4 items)

                    if ($count === 1) {
                        $class = 'col-12';
                    } elseif ($count === 2) {
                        $class = 'col-6';
                    } elseif ($count === 3) {
                        $class = 'col-4-3';
                    }

                    foreach ($devices as $site => $entry) : ?>
                        <div class="site_item <?= $class; ?>">
                            <h6 class="site_title"><?= $site; ?></h6>

                            <div class="punch_wrap">
                                <?php foreach ($entry as $log) : ?>
                                    <span class="m-badge m-badge--success m-badge--wide text-white"><?= date("h:i A", strtotime($log['datetime'])); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                <?php 
                    endforeach;
                ?>
            </div>
        </div>
        <?php endif; ?>
        <!-- JP05 | Biometric Location End -->

        <div class="mt-4">
            <p class="m--regular-font-size-lg3 m--font-bolder mt-3">SHIFT SCHEDULE</p>
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                            <label for="" class="m--font-bolder">START</label>
                            <?php $tempAmIn = empty($timesheet) ? null : ($timesheet->shift_am_start && $timesheet->shift_am_start !== null? $timesheet->shift_am_start: NULL); ?>
                            <?php 
                                $tempAmIn = (is_null($tempAmIn) == true && $shift_count == 1)? 
                                ($shift_schedule->am_start && $shift_schedule->am_start !== null)? $shift_schedule->am_start: NULL 
                                : $tempAmIn;
                            ?>
                            <input type="time" disabled class="form-control"
                                value="<?= $tempAmIn; ?>">
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                            <label for="" class="m--font-bolder">END</label>
                            <?php $tempAmOut = empty($timesheet) ? null : ($timesheet->shift_am_end && $timesheet->shift_am_end !== null? $timesheet->shift_am_end: NULL); ?>
                            <?php 
                                $tempAmOut = (is_null($tempAmOut) == true && $shift_count == 1)? 
                                ($shift_schedule->am_end && $shift_schedule->am_end !== null)? $shift_schedule->am_end: NULL 
                                : $tempAmOut;
                            ?>
                            <input type="time" disabled class="form-control"
                                value="<?= $tempAmOut; ?>">
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="m--regular-font-size-lg1">
                            <span class="m--font-bolder text-muted mr-2">LATE</span>
                            <span class="m--font-boldest"
                                style="text-transform: none;"><?= empty($timesheet) ? 0 : $timesheet->am_late ?> mins.</span>
                        </div>
                        <div class="m--regular-font-size-lg1">
                            <span class="m--font-bolder text-muted mr-2">UNDERTIME</span>
                            <span class="m--font-boldest"
                                style="text-transform: none;"><?= empty($timesheet) ? 0 : $timesheet->am_ut ?> mins.</span>
                        </div>
                        <div class="m--regular-font-size-lg1">
                            <span class="m--font-bolder text-muted mr-2">HRS. WORKED</span>
                            <span class="m--font-boldest"
                                style="text-transform: none;">
                        <?php
                            if (empty($timesheet)) {
                                echo 0;
                            } else {
                                $hrs = $timesheet->am_time_rendered / 60;
                                $diff = $hrs - floor($hrs);
                                $decimals = $diff > 0 ? 2 : 0;
                                $hrs = number_format($hrs, $decimals, '.', ',');
                                echo $hrs;
                            }
                        ?> hrs.</span>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                            <label for="" class="m--font-bolder">START</label>
                            <?php $tempPmIn = empty($timesheet) ? null : ($timesheet->shift_pm_start && $timesheet->shift_pm_start !== null? $timesheet->shift_pm_start: NULL); ?>
                            <?php 
                                $tempPmIn = (is_null($tempPmIn) == true && $shift_count == 1)? 
                                ($shift_schedule->pm_start && $shift_schedule->pm_start !== null)? $shift_schedule->pm_start: NULL 
                                : $tempPmIn;
                            ?>
                            <input type="time" disabled class="form-control"
                                value="<?= $tempPmIn; ?>">
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                            <label for="" class="m--font-bolder">END</label>
                            <?php $tempPmOut = empty($timesheet) ? null : ($timesheet->shift_pm_end && $timesheet->shift_pm_end !== null? $timesheet->shift_pm_end: NULL); ?>
                            <?php 
                                $tempPmOut = (is_null($tempPmOut) == true && $shift_count == 1)? 
                                ($shift_schedule->pm_end && $shift_schedule->pm_end !== null)? $shift_schedule->pm_end: NULL 
                                : $tempPmOut;
                            ?>
                            <input type="time" disabled class="form-control"
                                value="<?= $tempPmOut; ?>">
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="m--regular-font-size-lg1">
                            <span class="m--font-bolder text-muted mr-2">LATE</span>
                            <span class="m--font-boldest"
                                style="text-transform: none;"><?= empty($timesheet) ? 0 : $timesheet->pm_late ?> mins.</span>
                        </div>
                        <div class="m--regular-font-size-lg1">
                            <span class="m--font-bolder text-muted mr-2">UNDERTIME</span>
                            <span class="m--font-boldest"
                                style="text-transform: none;"><?= empty($timesheet) ? 0 : $timesheet->pm_ut ?> mins.</span>
                        </div>
                        <div class="m--regular-font-size-lg1">
                            <span class="m--font-bolder text-muted mr-2">HRS. WORKED</span>
                            <span class="m--font-boldest"
                                style="text-transform: none;">
                        <?php
                            if (empty($timesheet)) {
                                echo 0;
                            } else {
                                $hrs = $timesheet->pm_time_rendered / 60;
                                $diff = $hrs - floor($hrs);
                                $decimals = $diff > 0 ? 2 : 0;
                                $hrs = number_format($hrs, $decimals, '.', ',');
                                echo $hrs;
                            }
                        ?> hrs.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="total-rendered_time">
            <hr class="mt-4">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 m--regular-font-size-lg1">
                    <span class="m--font-bolder text-muted mr-2">TOTAL LATE</span>
                    <span class="m--font-boldest"
                        style="text-transform: none;"><?= empty($timesheet) ? 0 : $timesheet->total_late ?> mins.</span>
                </div>
                <?php if (isset($timesheet->total_ndiff_rendered) && floatval($timesheet->total_ndiff_rendered) > 0): ?>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 m--regular-font-size-lg1">
                    <span class="m--font-bolder text-muted mr-2">TOTAL REG.NDIFF HRS. WORKED</span>
                    <span class="m--font-boldest" style="text-transform: none;">
                        <?php
                            if (empty($timesheet)) {
                                echo 0;
                            } else {
                                $total_ndiff_hrs = $timesheet->total_ndiff_rendered / 60;
                                $diff2 = $total_ndiff_hrs - floor($total_ndiff_hrs);
                                $decimals2 = $diff2 > 0 ? 2 : 0;
                                $total_ndiff_hrs = number_format($total_ndiff_hrs, $decimals2, '.', ',');
                                echo $total_ndiff_hrs;
                            }
                        ?> hrs.
                    </span>
                </div>
                <?php endif; ?>
            </div>
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 m--regular-font-size-lg1">
                    <span class="m--font-bolder text-muted mr-2">TOTAL UNDERTIME</span>
                    <span class="m--font-boldest"
                        style="text-transform: none;"><?= empty($timesheet) ? 0 : $timesheet->total_ut ?> mins.</span>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 m--regular-font-size-lg1">
                    <span class="m--font-bolder text-muted mr-2">TOTAL HRS. WORKED</span>
                    <span class="m--font-boldest" style="text-transform: none;">
                        <?php
                            if (empty($timesheet)) {
                                echo 0;
                            } else {
                                $total_hrs = $timesheet->total_time_rendered / 60;
                                $diff2 = $total_hrs - floor($total_hrs);
                                $decimals2 = $diff2 > 0 ? 2 : 0;
                                $total_hrs = number_format($total_hrs, $decimals2, '.', ',');
                                echo $total_hrs;
                            }
                        ?> hrs.
                    </span>
                </div>
            </div>
            <?php if((isset($timesheet->last_updated_by_name) && $timesheet->last_updated_by_name) || (isset($timesheet->verified_by_name) && $timesheet->verified_by_name)): ?>
                <hr class="mt-4">
                <div class="row">
                    <?php if($timesheet->verified_by_name): ?>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 m--regular-font-size-lg1">
                        <div>
                            <span class="m--font-bolder text-muted mr-2">VERIFIED BY: </span>
                            <span class="m--font-boldest" style="text-transform: none;"><?= strtoupper($timesheet->verified_by_name); ?></span>
                        </div>
                        <div>
                            <span class="m--font-bolder text-muted mr-2">VERIFIED DATE: </span>
                            <span class="m--font-bolder" style="text-transform: none;"><?= strtoupper(date('M. d, Y h:i A', strtotime($timesheet->verified_at))); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($timesheet->last_updated_by_name): ?>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 m--regular-font-size-lg1">
                        <div>
                            <span class="m--font-bolder text-muted mr-2">LAST UPDATED BY: </span>
                            <span class="m--font-boldest" style="text-transform: none;"><?= strtoupper($timesheet->last_updated_by_name); ?></span>
                        </div>
                        <div>
                            <span class="m--font-bolder text-muted mr-2">LAST UPDATED DATE: </span>
                            <span class="m--font-bolder" style="text-transform: none;"><?= strtoupper(date('M. d, Y h:i A', strtotime($timesheet->last_updated_at))); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>
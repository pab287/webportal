<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Daily Overtime Summary</title>
        <style>
            @page {
                margin: 10mm 1mm 10mm 1mm;
            }
            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 12px;
            }

            .container {
                max-width: 100%;
                padding: 30px 0px
            }

            h2 {
                text-align: center;
                margin: 5px 0;
                letter-spacing: 1px;
            }

            .header-info {
                text-align: center;
                font-size: 11px;
                margin-bottom: 10px;
            }

            .date-row {
                margin-bottom: 8px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th, td {
                border: 1px solid #000;
                padding: 4px;
                vertical-align: middle;
                text-align: center;
            }

            th {
                text-align: center;
                font-weight: bold;
                background-color: #eaeaea;
                font-size: 10px;
            }

            td {
                height: 28px;
                font-size: 10px;
                text-transform: uppercase;
            }

            .text-center {
                text-align: center;
            }

            .remarks {
                margin-top: 10px;
            }

            .checkbox-group {
                display: flex;
                justify-content: space-around;
            }

            .row {
                display: flex;
                flex-wrap: wrap;
                justify-content: start;
                align-items: center;
                margin-top: 120px;
            }

            .col-md-3 {
                flex: 0 0 25%;
                max-width: 25%;
            }

            .mb-2 {
                margin-bottom: 0.5rem !important;
            }

            @media print {
                @page {
                    size: 'landscape';
                    margin: 0mm 1mm 0mm 1mm;
                }

                td {
                    font-size: 11px;
                }

                .page-break {
                    page-break-after: always;
                    break-after: page;
                    margin: 0;
                    padding: 0;
                    height: 0;
                    border: none;
                }

                .page-break:last-child {
                    page-break-after: auto;
                    break-after: auto;
                }
                
                /* Prevent the last page from having an extra blank page */
                .page-break:last-child {
                    page-break-after: auto;
                    break-after: auto;
                }

                .page-break + .content {
                    margin-top: 10mm;
                }

                table {
                    page-break-inside: avoid;
                }
            }

            @media screen {
                .page-break {
                    margin: 20px 0;
                    border-bottom: 2px dashed #ccc;
                    padding-bottom: 20px;
                }
            }
        </style>
    </head>
    <body>

    <div class="container">
        <?php foreach($data as $k => $row): ?>
            <div class="content">
                <div style="display: flex; flex-wrap: wrap; justify-content: center; align-items: center">
                    <div class="location-row" style="flex: 0 0 10%; max-width: 10%;">
                        <strong><?=$location; ?></strong> 
                    </div>
                    <div class="header-info" style="flex: 0 0 81%; max-width: 81%;">
                        <h2>DAILY OVERTIME SUMMARY</h2>
                        GCCI-FM-HRM-032 &nbsp;&nbsp; Rev 4 &nbsp;&nbsp; 08/07/2023
                    </div>
                    <div class="location-row" style="flex: 0 0 9%; max-width: 9%;"></div>
                </div>
        
                <div class="date-row">
                    <strong>DATE: <?=date('F d, Y', strtotime($k)); ?></strong> 
                </div>
        
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2" width="5%">Biometric Number</th>
                            <th rowspan="2" width="12%">Employee Name</th>
                            <th rowspan="2" width="11%">Designation</th>
                            <th rowspan="2" width="7%">Payroll Schedule</th>
                            <th rowspan="2" width="10%">Regular Shift</th>
                            <th rowspan="2" width="20%">Task / Activities</th>
                            <th rowspan="2" width="5%">Actual Time IN</th>
                            <th rowspan="2" width="5%">Actual (IN/OUT)</th>
                            <th colspan="2" width="7%">GPS Tracking</th>
                            <th rowspan="2" width="4%">Total OT Hours</th>
                            <th rowspan="2" width="8%">Meal Allowance Received</th>
                        </tr>
                        <tr>
                            <th width="25px">IN</th>
                            <th>OUT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($row as $key => $rs): ?>
                            <tr>
                                <td><?=$rs->biometricno; ?></td>
                                <td><?=$rs->employee_name; ?></td>
                                <td><?=$rs->position; ?></td>
                                <td><?=$rs->payroll_sched;  ?></td>
                                <td><?=$rs->regular_shift;  ?></td>
                                <td><?=$rs->purpose;  ?></td>
                                <td><?=$rs->actual_in ? date('H:i', strtotime($rs->actual_in)) : ''; ?></td>
                                <td><?=$rs->actual_out ? date('H:i', strtotime($rs->actual_out)) : ''; ?></td>
                                <td></td>
                                <td></td>
                                <td><?=$rs->total_hrs > 0 ? $rs->total_hrs : ''; ?></td>
                                <td>
                                    <div class="checkbox-group">
                                        <label>☐ YES</label>
                                        <label>☐ NO</label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
        
                <div class="remarks">
                    <strong>Remarks:</strong>
                </div>
    
            </div>
            <?php if($k !== array_key_last($data)): ?>
                <div class="page-break"></div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if (count($signatory) > 0): ?>
            <div class="row">
                <?php foreach($signatory as $key => $rs): ?>
                    <?php if ($rs['is_active']): ?>
                        <div class="col-md-3 mb-2">
                            <p style="font-weight: bold; margin-left: 10px;"><?=$rs['label'] ?></p>
                            <p class="signatory-value" style="font-weight: 600; margin-left: 10px; margin-right: 50px; margin-top: 50px; padding-top: 10px; border-top: 1px solid rgb(0, 0, 0);">
                                <?=$rs['value'] ?>
                            </p>
                        </div>
                    <?php endif; ?>
                <?php endforeach ;?>
            </div>
        <?php endif; ?>
    </div>

    </body>
</html>

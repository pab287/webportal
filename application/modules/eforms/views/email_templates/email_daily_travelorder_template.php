<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
        <meta name="viewport" content="width=600,initial-scale = 2.3,user-scalable=no">
        <!--[if !mso]> -->
        <link href='https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700' rel="stylesheet">
        <link href='https://fonts.googleapis.com/css?family=Quicksand:300,400,700' rel="stylesheet">
        <title>Travel Order Daily Report</title>
        <style>
            .row{
                display: flex;
                flex-wrap: wrap;
            }
            .justify-content-center{
                justify-content: center;
            }
            .col-10{
                flex: 0 0 80%;
                max-width: 80%;
            }
            .text-center{
                text-align: center;
            }
            body{
                text-transform: uppercase;
                font-family: 'Montserrat';
                font-family: Montserrat;
                font-size: 13px;
                width: 100%;
                background-color: #ffffff;
                margin: 0;
                padding: 0;
                -webkit-font-smoothing: antialiased;
                mso-margin-top-alt: 0px;
                mso-margin-bottom-alt: 0px;
                mso-padding-alt: 0px 0px 0px 0px;
            }
            #table-header{
                margin: 30px auto;
            }
            #table-content{
                border-collapse: collapse;
            }
            #table-content, #table-content td, #table-content th{
                border: 1px solid #6d6d6d;
                font-weight: 400px
            }
            #table-content td, #table-content th{
                padding: 10px;
                vertical-align: top;
            }
            ul{
                padding-left: 15px;
                margin: 0;
            }
            #wrapper{
                margin: 20px 0 50px;
            }
            p{
                margin: 0;
            }
            p:not(:last-child){
                margin-bottom: 3px;
            }
        </style>
    </head>
    <body>
        <div id="wrapper">
            <table id="table-header" align="center" width="80%">
                <thead>
                    <th>
                        <h3 style="margin: 0;"><?=date('M d, Y'); ?></h3>
                        <h1 style="margin: 0" class="text-center">Travel Order Daily Report</h1>
                    </th>
                </thead>
            </table>
            <table id="table-content" border="0" align="center" width="90%">
                <thead>
                    <th width="17%">Details</th>
                    <th width="14%">Driver & Vehicle</th>
                    <th width="14%">Personnel(s)</th>
                    <th width="18%">Destination(s)</th>
                    <th width="20%">Date & Time</th>
                    <th>Purpose</th>
                </thead>
                <tbody>
                    <?php foreach($result as $row): ?>
                        <tr>
                            <td>
                                <p><small><b>Reference no:</b> <?=$row->reference_no ?></small></p>
                                <p><small><b>File Under:</b> <?=$row->company ?></small></p>
                                <p><small><b>Date Created:</b> <?=$row->created_dt ?></small></p>
                            </td>
                            <td>
                                <?php
                                    if($row->driver != '0' && $row->driver != ''){
                                        echo "<p>".$row->driver."</p>";
                                        if($row->vehicle_plate != '' && $row->vehicle_description != ''){
                                            echo "<p><b>".$row->vehicle_plate."</b></p><p><b>".$row->vehicle_description."</b></p>";
                                        }
                                    }
                                ?>
                                <?php
                                    if($row->is_hitch != '0' && $row->is_hitch != ''){
                                        echo "<p>Hitch</p>";
                                        if($row->vehicle_plate != '' && $row->vehicle_description != ''){
                                            echo "<p><b>".$row->vehicle_plate."</b></p><p><b>".$row->vehicle_description."</b></p>";
                                        }
                                    }
                                ?>
                                <?=($row->is_personal != '0' && $row->is_personal != '') ? "<p>Personal</p><p><b>Vehicle<b></p>" : ''; ?>
                                <?=($row->is_commute != '0' && $row->is_commute != '') ? "<p>Commute</p>" : ''; ?>
                                <?=($row->is_others != '0' && $row->is_others != '') ? "<p>".$row->others_remarks."</p>" : ''; ?>
                            </td>
                            <td>
                                <ul>
                                    <?php foreach($row->personnels as $persons): ?>
                                        <li><?=$persons ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td>
                                <ul>
                                    <?php foreach($row->destination as $dest): ?>
                                        <li><?=$dest ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td>
                                <p><?=date('M d, Y h:i A', strtotime($row->date_from)).' - '.date('M d, Y h:i A', strtotime($row->date_to)) ?></p>
                            </td>
                            <td>
                                <p><?=$row->purpose ?></p>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
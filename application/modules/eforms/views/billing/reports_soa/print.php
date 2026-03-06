<?php
    $r_type = $data['report_type'];

    $tbl_foot_payment = array();
    $tbl_foot_billing = array();
    $tbl_reading = array();
    $tbl_ledger = array();

    // For payment table footer
    $bill = 0;
    $tbl_foot_total_penalty = 0;
    $covered = 0;
    $net = 0;
    $received = 0;
    
    // tracker to avoid accumulate value with the same bill
    $processed_bills = array(); 

    // For billing table footer
    $_total_usage = 0;
    $_total_charges = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
    <meta name="viewport" content="width=600,initial-scale = 2.3,user-scalable=no">
    <!--[if !mso]> -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link href='https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700' rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Quicksand:300,400,700' rel="stylesheet">
    <!-- <![endif]-->
    <title>Hydra Billing | SOA</title>
    <style type="text/css">
        body {
            width: 100%;
            max-width: 890px;
            background-color: #ffffff;
            margin: 0 auto;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            mso-margin-top-alt: 0px;
            mso-margin-bottom-alt: 0px;
            mso-padding-alt: 0px 0px 0px 0px;
        }

        * {
            margin: 0;
            font-size: 10px;
            font-family: Quicksand, Calibri, sans-serif; color:#343434;
        }

        h1, h2, h3, h4, h5, h6 {
            margin: 0;
            line-height: 1;
        }

        html {
            width: 100%;
        }

        table {
            border-collapse: collapse;
        }

        tbody tr:nth-child(odd) {
            background-color: rgb(242, 242, 242);
        }

        h3 {
            font-size: 12px;
        }

        .print-divider {
            border-top: 1px solid #000;
            margin-bottom: 15px;
        }

        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }

            .watermark{
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-40deg);
                opacity: 0.10!important;
                font-size: 60px!important;
            }

            .print-divider {
                border-top: 1px solid #000;
                margin-bottom: 15px;
            }

            * {
                font-size: 8px!important;
            }

            h3 {
                font-size: 10px!important;
            }

            .tbl-header {
                background: #90e0ef;
            }

            tbody tr:nth-child(odd) {
                background-color: rgb(242, 242, 242);
            }

            .tbl-footer-row p {
                font-size: 8px!important;
            }
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .group {
            display: flex;
        }

        .group p {
            font-weight: 900; 
            line-height: 1.6;
        }

        .user_details .group p:nth-child(1) {
            flex: 0 0 20%;
        }

        .user_details .group p:nth-child(2) {
            font-family: 'Roboto', sans-serif;
        }

        .balances .group p {
            flex: 1;
        }

        .balances .group p {
            flex: 1;
        }

        .balances .group p:nth-child(1) {
            max-width: 80%;
        }

        .balances .group p:nth-child(2) {
            max-width: 20%;
            font-weight: 900;
            font-family: 'Roboto', sans-serif;
        }

        .tbl-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            background: #90e0ef;
        }

        .tbl-header p {
            font-family: 'Roboto', sans-serif;
            font-weight: 900;
            letter-spacing: 0.1px;
            padding: 5px 0;
        }

        .tbl-body-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .tbl-body-row p {
            font-family: 'Roboto', sans-serif;
            letter-spacing: 0.5px;
            padding: 8px 0;
        }

        .tbl-footer-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            background: #90e0ef;
        }

        .tbl-footer-row p {
            font-family: 'Roboto', sans-serif;
            letter-spacing: 0.1px;
            padding: 8px 0;
            font-weight: 900;
            font-size: 10px;
        }

        /* Payment Start */
        .tbl-header.tbl-payment p,
        .tbl-body-row.tbl-body-payment p,
        .tbl-footer-row.tbl-footer-payment p {
            flex: 0 0 11.11%;
            max-width: 11.11%;
        }
        /* Payment End */

        /* Billing Start */
        .tbl-header.tbl-billing p,
        .tbl-body-row.tbl-body-billing p,
        .tbl-footer-row.tbl-footer-billing p {
            flex: 0 0 20%;
            max-width: 20%;
        }
        /* Billing End */

        /* Reading Start */
        .tbl-header.tbl-reading p,
        .tbl-body-row.tbl-body-reading p  {
            flex: 0 0 33.33%;
            max-width: 33.33%;
        }
        /* Reading End */

        /* Ledger Start */
        .tbl-header.tbl-ledger p,
        .tbl-body-row.tbl-body-ledger p,
        .tbl-footer-row.tbl-footer-ledger p {
            flex: 0 0 20%;
            max-width: 20%;
        }

        tfoot {
            display: table-row-group;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;

            transform: translate(-50%, -50%) rotate(-40deg);

            font-family: "Arial Black", Arial, sans-serif;
            font-size: 80px;
            font-weight: 900;
            color: black;
            opacity: 0.15;

            white-space: nowrap;
            pointer-events: none;
            z-index: 9999;
        }
    </style>
</head>

<body>
    <table id="main_page" border="0" width="100%" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td align="left">
                    <img style="height: 106px;" src="../../assets/images/hydra_billing_logo.png">
                </td>

                <td align="right">
                    <h3 style="margin-bottom: 5px;">BACOLOD HYDRA</h3>

                    <h3><small style="line-height: 1.4;">Carlos Hilado Ave., Circumferential Road <br> Brgy. Bata, Bacolod City <br> Office Tel. No.: (034) 441-2409-11 *Fax. No. (034) 441-0693</small></h3>
                </td>
            </tr>

            <tr><td colspan="2">&nbsp;</td></tr>

            <tr>
                <td align="center" colspan="2"><h3 style="margin-bottom: 15px; font-weight: bold;"><?=strtoupper($r_type) . " | "; ?> STATEMENT OF ACCOUNT</h3></td>
            </tr>

            <tr>
                <td colspan="2">
                    <div class="print-divider"></div>
                </td>
            </tr>

            <tr>
                <td align="left" width="50%" class="user_details">
                    <div class="group">
                        <p>NAME:</p>
                        <p><?=strtoupper($data['customer_name']);?></p>
                    </div>

                    <div class="group">
                        <p>MODEL:</p>
                        <p><?=ucwords($data['model'] . ', BLOCK ' . $data['block'] . ', LOT ' . $data['lot']);?></p>
                    </div>

                    <div class="group">
                        <p>ACC. #:</p>
                        <p><?=$data['accountno'];?></p>
                    </div>

                    <div class="group">
                        <p>METER #:</p>
                        <p><?=$data['meterno'];?></p>
                    </div>
                </td>
                
                <td align="right" width="50%" class="balances">
                    <div class="group">
                        <p>OVERDUE BALANCE:</p>
                        <p><?="₱ ". number_format($overdue_charges < 0 ? 0 : $overdue_charges, 2);?></p>
                    </div>

                    <div class="group">
                        <p>TOTAL PENALTY:</p>
                        <p><?="₱ ". number_format($total_penalty, 2);?></p>
                    </div>

                    <div class="group">
                        <p>OVERPAYMENT BALANCE:</p>
                        <p style="border-bottom: 1px solid;"><?="₱ ". number_format($overpayment, 2);?></p>
                    </div>

                    <div class="group">
                        <p>TOTAL BALANCE:</p>
                        <p>
                            <?php 
                                $_total = $overdue_charges + $total_penalty;
                                echo "₱ ". number_format($_total < 0 ? 0 : $_total, 2); 
                            ?>
                        </p>
                    </div>
                </td>
            </tr>

            <tr><td colspan="2">&nbsp;</td></tr>

            <tr>
                <td colspan="2">
                    <?php if ($r_type === 'payment'): ?>
                        <div class="tbl-header tbl-payment">
                            <p class="text-center">Date Paid</p>
                            <p class="text-center">Pay. Ref. #</p>
                            <p class="text-center">Bill. Ref. #</p>
                            <p class="text-center">Type</p>
                            <p class="text-center">Bill</p>
                            <p class="text-center">Penalty</p>
                            <p class="text-center">Covered</p>
                            <p class="text-center">Net</p>
                            <p class="text-center">Received</p>
                        </div>
                    <?php endif; ?> <!-- payment -->

                    <?php if ($r_type === 'billing'): ?>
                        <div class="tbl-header tbl-billing">
                            <p class="text-center">Ref #</p>
                            <p class="text-center">Billing From</p>
                            <p class="text-center">Billing To</p>
                            <p class="text-center">Usage</p>
                            <p class="text-center">Total Charges</p>
                        </div>
                    <?php endif; ?> <!-- billing -->

                    <?php if ($r_type === 'reading'): ?>
                        <div class="tbl-header tbl-reading">
                            <p class="text-center">Ref #</p>
                            <p class="text-center">Reading Date</p>
                            <p class="text-center">Reading</p>
                        </div>
                    <?php endif; ?> <!-- reading -->

                    <?php if ($r_type === 'ledger'): ?>
                        <div class="tbl-header tbl-ledger">
                            <p class="text-center">DUE DATE</p>
                            <p class="text-center">REF #</p>
                            <p class="text-right">DEBIT</p>
                            <p class="text-right">CREDIT</p>
                            <p class="text-right">BALANCE</p>
                        </div>
                    <?php endif; ?> <!-- ledger -->
                </td>
            </tr>
        </thead>

        <tbody>
            <?php if ($r_type === 'payment'): ?>
                <?php
                    $bgcolor = "#efefef";

                    $this->db->select("p.ref_no, b.ref_no as bill_ref_no, b.id as bill_id, b.total_charges, p.created_date, p.payment_date, p.payment_type, p.payment_details, p.received_amount, p.balance_covered, p.sub_total, p.net_payment, p.is_archive, p.penalties");
                    $this->db->from("hydra_billing.payments as p");
                    $this->db->join("hydra_billing.bills as b", "b.id = p.bill_id", "LEFT");
                    $this->db->where("p.account_id", $data['id']);
                    $this->db->where("is_archive", 0);

                    if ($data['selectedDate'] != 'all' AND $data['selectedDate'] != "") { 
                        $this->db->where("year(created_date)",$data['selectedDate']); 
                    } else {
                        if ($data['startDate'] != "" && $data['endDate'] != "") {
                            $start_date = date("Y-m-d", strtotime($data['startDate']));
                            $end_date = date("Y-m-d", strtotime($data['endDate'])); 

                            $this->db->where("created_date >=",$start_date);
                            $this->db->where("created_date <=",$end_date);
                        }
                    }  

                    $this->db->order_by('created_date', 'ASC');
                    $query = $this->db->get();
                    $bill_total_penalty = 0;

                    if($query->num_rows() > 0):
                        foreach($query->result_array() as $_query):
                            $penalty_ser = unserialize($_query['penalties']);
                            
                            // penalty for this row
                            $row_penalty = (is_array($penalty_ser) && isset($penalty_ser[0]['overdue'])) ? (float) $penalty_ser[0]['overdue'] : 0;

                            // accumulate
                            $covered += $_query["balance_covered"];
                            $net += $_query["net_payment"];
                            $received += $_query["received_amount"];

                            // ONLY accumulate bill related values once per bill
                            $bill_ref = $_query["bill_id"];

                            if (!isset($processed_bills[$bill_ref])) {
                                $bill += $_query["total_charges"];
                                $tbl_foot_total_penalty += $row_penalty;

                                $processed_bills[$bill_ref] = true;
                            }

                            // row color toggle
                            $bgcolor = ($bgcolor == "#efefef") ? "#ffffff" : "#efefef";
                ?>
                            <tr>
                                <td colspan="2">
                                    <div class="tbl-body-row tbl-body-payment">
                                        <p class="text-center"><?=date('M d, Y', strtotime($_query["payment_date"]));?></p>
                                        <p class="text-center"><?=strtoupper($_query["ref_no"]);?></p>
                                        <p class="text-center"><?=strtoupper($_query["bill_ref_no"]);?></p>
                                        <p class="text-center"><?=strtoupper($_query["payment_type"]);?></p>
                                        <p class="text-right"><?='₱ '. number_format($_query["total_charges"], 2);?></p>
                                        <p class="text-right"><?='₱ '. number_format($row_penalty, 2);?></p>
                                        <p class="text-right"><?='₱ '. number_format($_query["balance_covered"], 2);?></p>
                                        <p class="text-right"><?='₱ '. number_format($_query["net_payment"],2);?></p>
                                        <p class="text-right"><?='₱ '. number_format($_query["received_amount"],2);?></p>
                                    </div>
                                </td>
                            </tr>
                <?php
                        endforeach;

                        $tbl_foot_payment['total_bill'] = $bill;
                        $tbl_foot_payment['total_penalty'] = $tbl_foot_total_penalty;
                        $tbl_foot_payment['total_covered'] = $covered;
                        $tbl_foot_payment['total_net'] = $net;
                        $tbl_foot_payment['total_received'] = $received;
                    else: 
                ?>
                    <tr><td align="center" colspan="2" style="padding: 6px; font-size: 12px; font-weight: 900">NO MATCHING RECORDS FOUND</td></tr>
                <?php 
                    endif;
                ?>
            <?php endif; ?> <!-- payment -->

            <?php if ($r_type === 'billing'): ?>
                <?php
                    $bgcolor="#efefef";
                    $this->db->select("created_at, billing_from, billing_to, usage, total_charges, ref_no");
                    $this->db->from("hydra_billing.bills");
                    $this->db->where("account_id", $data['id']);
                    $this->db->where("status", 1);

                    if($data['selectedDate'] != 'all' AND $data['selectedDate'] != "") {
                        $this->db->where("year(created_at)",$data['selectedDate']); 
                    } else {
                        if($data['startDate'] != "" && $data['endDate'] != ""){
                            $start_date = date("Y-m-d", strtotime($data['startDate']));
                            $end_date = date("Y-m-d", strtotime($data['endDate'])); 
            
                            $this->db->where("billing_from >=",$start_date);
                            $this->db->where("billing_to <=",$end_date);
                        }
                    }  

                    $this->db->order_by('created_at', 'desc');
                    $query = $this->db->get();
                    $bill_total_penalty = 0;

                    if($query->num_rows() > 0):
                        foreach($query->result_array() as $_query):
                            $_total_usage += $_query["usage"];
                            $_total_charges += $_query['total_charges'];
                ?>
                            <tr>
                                <td colspan="2">
                                    <div class="tbl-body-row tbl-body-billing">
                                        <p class="text-center"><?=$_query["ref_no"];?></p>
                                        <p class="text-center"><?=date('M d, Y', strtotime($_query["billing_from"]));?></p>
                                        <p class="text-center"><?=date('M d, Y', strtotime($_query["billing_to"]));?></p>
                                        <p class="text-center"><?=$_query["usage"];?></p>
                                        <p class="text-center"><?="₱ " . number_format($_query['total_charges'], 2);?></p>
                                    </div>
                                </td>
                            </tr>
                <?php
                        endforeach;

                        $tbl_foot_billing['total_usage'] = $_total_usage;
                        $tbl_foot_billing['total_charges'] = $_total_charges;

                    else:
                ?>
                    <tr><td align="center" colspan="2" style="padding: 6px; font-size: 12px; font-weight: 900">NO MATCHING RECORDS FOUND</td></tr>
                <?php endif; ?>
            <?php endif; ?> <!-- billing -->

            <?php if ($r_type === 'reading'): ?>
                <?php
                    $total_reading = 0;
                    $bgcolor = "#efefef";
                    $this->db->select("ref_no, reading_date, reading");
                    $this->db->from("hydra_billing.readings");
                    $this->db->where("account_id", $data['id']);
                    $this->db->where("is_archived", 0);
            
                    if($data['selectedDate'] != 'all' AND $data['selectedDate'] != "") {
                        $this->db->where("year(reading_date)", $data['selectedDate']); 
                    } else {
                        if($data['startDate'] != "" && $data['endDate'] != "") {
                            $start_date = date("Y-m-d", strtotime($data['startDate']));
                            $end_date = date("Y-m-d", strtotime($data['endDate'])); 
            
                            $this->db->where("reading_date >=", $start_date);
                            $this->db->where("reading_date <=", $end_date);
                        }
                    }  

                    $this->db->order_by("reading_date","ASC");
                    $query = $this->db->get();

                    if($query->num_rows() > 0):
                        foreach($query->result_array() as $_query):
                            $total_reading += $_query['reading']
                ?>
                            <tr>
                                <td colspan="2">
                                    <div class="tbl-body-row tbl-body-reading">
                                        <p class="text-center"><?=$_query["ref_no"];?></p>
                                        <p class="text-center"><?=date("M d, Y", strtotime($_query["reading_date"]));?></p>
                                        <p class="text-center"><?=strtoupper($_query["reading"]);?></p>
                                    </div>
                                </td>
                            </tr>
                <?php
                        endforeach;
                    else:
                ?>
                        <tr><td align="center" colspan="2" style="padding: 6px; font-size: 12px; font-weight: 900">NO MATCHING RECORDS FOUND</td></tr>
                <?php endif; ?>
            <?php endif; ?> <!-- reading -->

            <?php if ($r_type === 'ledger'): ?>
                <?php
                    $current_date   = date("Y-m-d");
                    $temp_penalties = $this->db->get_where("hydra_billing.penalties")->row_array();

                    $this->db->select("bill.id, bill.ref_no, bill.total_charges, bill.created_at, bill.due_date, bill.is_paid, payment.sub_total, payment.received_amount, payment.balance_covered, payment.payment_date, payment.reconnection_fee, payment.is_penalty, payment.created_date");
                    $this->db->from("hydra_billing.bills bill");
                    $this->db->join("hydra_billing.payments payment", "payment.bill_id = bill.id AND payment.is_archive = 0", "LEFT");
                    $this->db->where("bill.account_id", $data['id']);
                    $this->db->where("bill.status", 1);

                    if ($data['selectedDate'] === 'custom') {
                        if (!empty($data['startDate']) && !empty($data['endDate'])) {
                            $start_date = date("Y-m-d", strtotime($data['startDate']));
                            $end_date   = date("Y-m-d", strtotime($data['endDate']));

                            $this->db->where("bill.due_date >=", $start_date);
                            $this->db->where("bill.due_date <=", $end_date);
                        }
                    } elseif ($data['selectedDate'] !== 'all') {
                        // Filter by specific YEAR
                        $this->db->where("YEAR(bill.due_date)", $data['selectedDate']);
                    }

                    $this->db->order_by("bill.due_date asc, payment.created_date asc");
                    $query = $this->db->get();

                    $results = $query->result_array();
                    $current_bill_id  = null;
                    $running_balance  = 0;

                    if($query->num_rows() > 0):
                        foreach ($results as $_query):
                            // Compute base charges + penalties
                            $base_charge  = floatval($_query["total_charges"]);
                            $total_charges = $base_charge;

                            // Default penalty
                            $penalty_amount = 0;

                            // Compute penalties
                            if ($_query['payment_date']) {
                                // Payment exists
                                if ($_query['payment_date'] > $_query['due_date']) {
                                    $penalty_amount = $base_charge * ($temp_penalties['amount'] / 100);
                                    $total_charges += $penalty_amount + $_query['reconnection_fee'];
                                } else {
                                    $penalty_amount = 0;
                                }
                            } 

                            // New Bill? → Reset running balance
                            if ($current_bill_id !== $_query['id']) {
                                $current_bill_id = $_query['id'];
                                $running_balance = $total_charges;   // Full amount before payments
                            }

                            // Compute credit and new balance

                            $rec = floatval($_query["received_amount"]  ?? 0);
                            $cov = floatval($_query["balance_covered"] ?? 0);

                            $credit = $rec + $cov;
                            $debit  = $running_balance;

                            $new_balance = $running_balance - $credit;
                            $running_balance = $new_balance; // update for next payment

                            //Render HTML Row
                ?>
                            <tr>
                                <td colspan="2">
                                    <div class="tbl-body-row tbl-body-ledger">
                                        <p class="text-center"><?=date("M d, Y", strtotime($_query["due_date"]));?></p>
                                        <p class="text-center"><?=$_query["ref_no"];?></p>
                                        <p class="text-right">
                                            <span class="m--text-muted" style="font-size: 9px;">
                                                <small>
                                                    ₱ <?=number_format($base_charge, 2) ?> 
                                                    + ₱ <?=number_format($penalty_amount, 2) ?>
                                                    + ₱ <?=number_format($_query['reconnection_fee'], 2) ?>
                                                </small>
                                            </span><br>
                                            ₱ <?=number_format($debit, 2); ?>
                                        </p>
                                        <p class="text-right">
                                            <span class="m--text-muted" style="font-size: 11px;">
                                                <small>
                                                    <?=$_query['payment_date'] ? 'PD: '.date("M d, Y", strtotime($_query['payment_date'])) : "--" ?>
                                                </small>
                                            </span><br>
                                            ₱ <?=number_format($credit, 2); ?>
                                        </p>
                                        <p class="text-right">₱ <?=number_format($new_balance, 2); ?></p>
                                    </div>
                                </td>
                            </tr>
                <?php
                        endforeach;
                    else: 
                ?>
                    <tr><td align="center" colspan="2" style="padding: 6px; font-size: 12px; font-weight: 900">NO MATCHING RECORDS FOUND</td><tr>
                <?php endif; ?>
            <?php endif; ?> <!-- ledger -->    
        </tbody>

        <tfoot>
            <tr>
                <td colspan="2">
                    <?php if($r_type === 'payment'): ?>

                        <div class="tbl-footer-row tbl-footer-payment">
                            <p></p>
                            <p></p>
                            <p></p>
                            <p class="text-center">TOTAL</p>
                            <p class="text-right"><?='₱ '. number_format($tbl_foot_payment['total_bill'], 2);?></p>
                            <p class="text-right"><?='₱ '. number_format($tbl_foot_payment['total_penalty'], 2);?></p>
                            <p class="text-right"><?='₱ '. number_format($tbl_foot_payment['total_covered'], 2);?></p>
                            <p class="text-right"><?='₱ '. number_format($tbl_foot_payment['total_net'], 2);?></p>
                            <p class="text-right"><?='₱ '. number_format($tbl_foot_payment['total_received'], 2);?></p>
                        </div>
                            
                    <?php endif; ?><!-- payment -->

                    <?php if($r_type === 'billing'): ?>

                        <div class="tbl-footer-row tbl-footer-billing">
                            <p></p>
                            <p></p>
                            <p class="text-center">TOTAL</p>
                            <p class="text-center"><?=$tbl_foot_billing['total_usage'];?></p>
                            <p class="text-center"><?='₱ '. number_format($tbl_foot_billing['total_charges'], 2);?></p>
                        </div>

                    <?php endif; ?><!-- billing -->

                    <?php if($r_type === 'reading'): ?>

                    <?php endif; ?><!-- reading -->

                    <?php if($r_type === 'ledger'): ?>

                        <div class="tbl-footer-row tbl-footer-ledger">
                            <p></p>
                            <p></p>
                            <p></p>
                            <p class="text-right">TOTAL</p>
                            <p class="text-right"><?='₱ '. number_format($overdue_charges, 2);?></p>
                        </div>

                    <?php endif; ?><!-- ledger -->
                </td>
            </tr>
        </tfoot>
    </table>

    <?php if($r_type === 'ledger'): ?>
    <div class="watermark">--FOR DISCONNECTION--</div>
    <?php endif; ?><!-- Watermark -->
</body>
</html>
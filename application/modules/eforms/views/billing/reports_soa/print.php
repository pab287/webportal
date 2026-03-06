<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
    <meta name="viewport" content="width=600,initial-scale = 2.3,user-scalable=no">
    <!--[if !mso]> -->
    <link href='https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700' rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Quicksand:300,400,700' rel="stylesheet">
    <!-- <![endif]-->
    <title>Hydra Billing | SOA</title>
    <style type="text/css">
        body {
            width: 100%;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            mso-margin-top-alt: 0px;
            mso-margin-bottom-alt: 0px;
            mso-padding-alt: 0px 0px 0px 0px;
        }

        p, h1, h2, h3, h4 {
            margin-top: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
        }

        span.preheader {
            display: none;
            font-size: 1px;
        }

        html {
            width: 100%;
        }

        table {
            font-size: 12px;
            border: 0;
        }
        table td, table th{
            font-size: 10px;
        }
        /* ----------- responsivity ----------- */

        @media only screen and (max-width: 640px) {
              /*------ top header ------ */
            .main-header {
                font-size: 20px !important;
            }
            .main-section-header {
                font-size: 28px !important;
            }
            .show {
                display: block !important;
            }
            .hide {
                display: none !important;
            }
            .align-center {
                text-align: center !important;
            }
            .no-bg {
                background: none !important;
            }
            /*----- main image -------*/
            .main-image img {
                width: 440px !important;
                height: auto !important;
            }
            /* ====== divider ====== */
            .divider img {
                width: 440px !important;
            }
            /*-------- container --------*/
            .container980 {
                width: 440px !important;
            }
            .container580 {
                width: 400px !important;
            }
            .main-button {
                width: 220px !important;
            }
            /*-------- secions ----------*/
            .section-img img {
                width: 320px !important;
                height: auto !important;
            }
            .team-img img {
                width: 100% !important;
                height: auto !important;
            }
        }

        @media only screen and (max-width: 479px) {
            /*------ top header ------ */
            .main-header {
                font-size: 18px !important;
            }
            .main-section-header {
                font-size: 26px !important;
            }
            /* ====== divider ====== */
            .divider img {
                width: 280px !important;
            }
            /*-------- container --------*/
            .container980 {
                width: 280px !important;
            }
            .container980 {
                width: 280px !important;
            }
            .container980 {
                width: 260px !important;
            }
            /*-------- secions ----------*/
            .section-img img {
                width: 280px !important;
                height: auto !important;
            }
        }
        @media print {
            .head{
                position: fixed;
                top: 0;
            }

            .container980 {
                width: 100% !important;
            }

            .content{
                height: 295px;
            }

            .content, td {
                page-break-inside: avoid;
            }
        }
        .watermark {
            opacity: 0.3;
            color: BLACK;
            position: fixed;
            top: auto;
            left: 20%;
            right: 20%;
            top: 42%;
            font-size: 35px;
            font-weight: 'bold';
            rotate: 320deg;
        }
    </style>
</head>

<body class="respond" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <!-- header -->
    <table border="0" width="100%" cellpadding="0" cellspacing="0">
        <tr class="head">
            <td align="center">
                <table border="0" align="center" width="890" cellpadding="0" cellspacing="0" class="container980" bgcolor="ffffff">
                    <tr>
                        <td colspan="2" height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
                    </tr>

                    <tr>
                        <td width="200"><img style="height: 106px;" src="../../assets/images/hydra_billing_logo.png"></td>
                        <td width="200" align="center">
                            <table border="0" align="center" width="100%" cellpadding="0" cellspacing="0" class="container980">
                                <tr>
                                    <td align="right" height="30">
                                        <h3 style="font-family: Quicksand, Calibri, sans-serif; color:#343434;">BACOLOD HYDRA</h3>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td align="right" height="30">
                                        <h3 style="font-family: Quicksand, Calibri, sans-serif; color:#343434;"><small>Carlos Hilado Ave., Circumferential Road <br > Brgy. Bata, Bacolod City <br > Office Tel. No.: (034) 441-2409-11 *Fax. No. (034) 441-0693</small></h3>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr><td colspan="2">&nbsp;</td></tr>

                    <tr>
                        <td align="center" colspan="2" height="25" style="font-family: arial; color:#343434; font-weight: bold;">STATEMENT OF ACCOUNT</td>
                    </tr>

                    <tr>
                        <td align="center" colspan="2"  style="font-family: arial; color:#343434; font-weight: bold;"><hr style="height: 3px;border: none;background: #000;"></td>
                    </tr>

                    <tr style="font-family: arial; color:#343434;">
                        <td style="font-weight: bold;" align="left">
                            <table border="0" align="right" width="100%" cellpadding="0" cellspacing="0" class="container980">
                                <tr>
                                    <td align="left" style="padding: 3px; font-weight: 600;"><strong><?php echo strtoupper($data['customer_name']); ?></strong></td>
                                </tr>

                                <tr>
                                    <td align="left" style="padding: 3px;"><?php echo ucwords('MODEL '.$data['model'].' BLOCK '.$data['block'].' LOT '.$data['lot']); ?></td>
                                </tr>

                                <tr>
                                    <td align="left" style="padding: 3px; font-weight: 600;"><b>ACCT NO.:</b> <?php echo $data['accountno']; ?></td>
                                </tr>

                                <tr>
                                    <td align="left" style="padding: 3px; font-weight: 600;"><b>METER NO.:</b> <?php echo $data['meterno']; ?></td>
                                </tr>
                            </table>
                        </td>

                        <td align="right" valign="top">
                            <table border="0" align="right" width="60%" cellpadding="0" cellspacing="0" class="container980">
                                <tr>
                                    <td align="right" style="padding: 3px; font-weight: 600;">OVERDUE BALANCE: </td>
                                    <td align="left" style="padding: 3px; font-weight: 600;">P <?= number_format($overdue_charges, 2); ?></td>
                                </tr>

                                <tr>
                                    <td align="right" style="padding: 3px; font-weight: 600;">TOTAL PENALTY:</td>
                                    <td align="left" style="padding: 3px; font-weight: 600;">P <?= number_format($total_penalty, 2); ?></td>
                                </tr>

                                <tr>
                                    <td align="right" style="padding: 3px; font-weight: 600;">OVER PAYMENT BALANCE:</td>
                                    <td align="left" style="padding: 3px; font-weight: 600;">P <?= number_format($overPayment, 2); ?></td>
                                </tr>
                                
                                <tr>
                                    <td align="right" style="padding: 3px; font-weight: 600;"><i>TOTAL BALANCE:</i></td>
                                    <td align="left" style="padding: 3px; font-weight: 600; border-top: 1px solid black;"><i>P <?= number_format(($overdue_charges + $total_penalty), 2); ?></i></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                </table>

                <?php if(isset($data['report_type']) && $data['report_type'] == "payment") { ?>

                    <table border="0" width="890" cellpadding="0" cellspacing="0" class="container980" align="center">
                        <tr style="background-color: #bad3f1">
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">DATE</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">REFERENCE NO.</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">PAYMENT TYPE</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">BILL AMOUNT</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">PENALTIES</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">OP BALANCE COVERED</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">NET PAY</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">PAYMENT</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">ACCT BALANCE</th>
                        </tr>
                    </table>

                <?php } elseif(isset($data['report_type']) && $data['report_type'] == "billing") { ?>

                    <table border="0" width="890" cellpadding="0" cellspacing="0" class="container980" align="center">
                        <tr style="background-color: #bad3f1">
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">REFERENCE NO.</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">BILLING FROM</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">BILLING TO</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">USAGE</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">TOTAL CHARGES</th>
                        </tr>
                    </table>

                <?php } elseif(isset($data['report_type']) && $data['report_type'] == "reading"){ ?>

                    <table border="0" width="890" cellpadding="0" cellspacing="0" class="container980" align="center">
                        <tr style="background-color: #bad3f1">
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="296" align="right">REFERENCE NO.</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="296" align="right">READING DATE</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="296" align="right">READING </th>
                        </tr>
                    </table>

                <?php } else { ?>

                    <table border="0" width="890" cellpadding="0" cellspacing="0" class="container980" align="center">
                        <tr style="background-color: #bad3f1">
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="296" align="right">DUE DATE</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="296" align="right">REFERENCE NO.</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="296" align="right">DEBIT</th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="296" align="right">CREDIT </th>
                            <th align="center" style="font-family: arial; color:#343434; padding: 6px;" width="296" align="right">BALANCE </th>
                        </tr>
                    </table>

                <?php } ?>
            </td>
        </tr>
    </table>

    <table class="content" border="0" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <?php if(isset($data['report_type']) && $data['report_type'] == 'payment') { ?>

                <table border="0" align="center" width="890" cellpadding="0" cellspacing="0" class="container980">
                    <?php
                        $subTotal = 0;
                        $netPayment = 0;
                        $balanceTotal = 0;
                        $paymentTotal = 0;
                        $AccbalanceTotal = 0;
                        $bgcolor="#efefef";

                        $this->db->select("created_date, payment_type, payment_details, received_amount, balance_covered, sub_total, net_payment, ref_no, balance, penalties");
                        $this->db->from("hydra_billing.payments");
                        $this->db->where("account_id",$data['id']);
                        $this->db->where("is_archive","0");

                        if($data['selectedDate'] != 'all' AND $data['selectedDate'] != "") { 
                            $this->db->where("year(created_date)",$data['selectedDate']); 
                        } else {
                            if($data['startDate'] != "" && $data['endDate'] != ""){
                                $this->db->where("created_date >=",$data['startDate']);
                                $this->db->where("created_date <=",$data['endDate']);
                            }
                        }

                        $this->db->order_by('created_date', 'desc');
                        $query = $this->db->get();
                        $bill_total_penalty = 0;

                        if($query->num_rows() > 0) {
                            foreach($query->result_array() as $_query) {
                                if ($bgcolor=="#efefef") $bgcolor="#ffffff";
                                elseif ($bgcolor=="#ffffff") $bgcolor="#efefef";

                                $subTotal = $subTotal + $_query["sub_total"];
                                $netPayment = $netPayment + $_query["net_payment"];
                                $balanceTotal = $balanceTotal + $_query["balance_covered"];
                                $paymentTotal = $paymentTotal + $_query["received_amount"];
                                $AccbalanceTotal = $AccbalanceTotal + $_query["balance"];
                                $penalty = unserialize($_query['penalties']);
                                $bill_penalty= 0;

                                foreach($penalty as $penalties) {
                                    $bill_penalty = $penalties['overdue'];
                                    $bill_total_penalty -= $bill_penalty;
                                }

                    ?>
                                <tr style="background-color: <?php echo $bgcolor ?>">
                                    <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                        <?php echo date('Y-m-d', strtotime($_query["created_date"])); ?>
                                    </td>

                                    <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                        <?php echo strtoupper($_query["ref_no"]); ?>
                                    </td>

                                    <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                        <strong><?php echo strtoupper($_query["payment_type"]); ?></strong>
                                    </td>

                                    <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                        <?php echo number_format($_query["sub_total"],2) ?>
                                    </td>

                                    <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                        <?php echo number_format($bill_penalty, 2) ?>
                                    </td>

                                    <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                        <?php echo number_format($_query["balance_covered"],2); ?>
                                    </td>

                                    <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                        <?php echo number_format($_query["net_payment"],2) ?>
                                    </td>

                                    <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                        <?php echo number_format($_query["received_amount"],2) ?>
                                    </td>

                                    <td align="right" style="font-family: arial; color:#343434; font-weight: bold; padding: 6px;" width="180" align="right">
                                        <?php echo number_format($_query["balance"],2); ?>
                                    </td>
                                </tr>
                    <?php 
                            } 
                        } else {
                    ?>
                          <tr><td align="center" colspan="6" style="padding: 6px;">NO MATCHING RECORDS FOUND</td></tr>
                    <?php } ?>

                      <tr class="footer" style="background-color: #bad3f1;">
                          <td align="left" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right"></td>
                          <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right"></td>
                          <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right"><strong>TOTAL</strong></td>
                          <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                              <strong><?php echo '₱ '.number_format($subTotal,2); ?></strong>
                          </td>

                          <td align="center" style="font-family: arial; color:#343434; font-weight: bold; padding: 6px;" width="180" align="right">
                              <strong><?php echo '₱ '.number_format($bill_total_penalty,2); ?></strong>
                          </td>

                          <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                              <strong><?php echo '₱ '.number_format($balanceTotal,2); ?></strong>
                          </td>

                          <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                              <strong><?php echo '₱ '.number_format($netPayment,2); ?></strong>
                          </td>

                          <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                              <strong><?php echo '₱'.number_format($paymentTotal,2); ?></strong>
                          </td>

                          <td align="right" style="font-family: arial; color:#343434; font-weight: bold; padding: 6px;" width="180" align="right">
                              <strong><?php echo '₱ '.number_format($AccbalanceTotal,2); ?></strong>
                          </td>
                      </tr>
                </table>

            <?php } elseif(isset($data['report_type']) && $data['report_type'] == 'billing') { ?>

                <table border="0" align="center" width="890" cellpadding="0" cellspacing="0" class="container980">
                    <?php
                        $total_usage = 0;
                        $total_charges = 0;
                        $bgcolor="#efefef";
                        $this->db->select("created_at, billing_from, billing_to, usage, total_charges, ref_no");
                        $this->db->from("hydra_billing.bills");
                        $this->db->where("account_id",$data['id']);

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

                        if($query->num_rows() > 0) {
                            foreach($query->result_array() as $_query) {
                                $total_usage += $_query["usage"];
                                $total_charges += $_query['total_charges'];
                      ?>

                              <tr style="background-color: <?= $bgcolor ?>">
                                  <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                      <?php //echo date('Y-m-d g:i A', strtotime($_query["created_date"])); ?>
                                      <?= $_query["ref_no"]; ?>
                                  </td>

                                  <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                      <?= strtoupper($_query["billing_from"]); ?>
                                  </td>

                                  <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                      <?= strtoupper($_query["billing_to"]); ?>
                                  </td>

                                  <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                      <?= $_query["usage"] ?>
                                  </td>

                                  <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                      <?= number_format($_query['total_charges'], 2) ?>
                                  </td>
                              </tr>
                    <?php 
                            } 
                        } else {
                    ?>
                              <tr><td align="center" colspan="6" style="padding: 6px;">NO MATCHING RECORDS FOUND</td></tr>
                    <?php } ?>

                    <tr class="footer" style="background-color: #bad3f1;">
                        <td align="left" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right"></td>
                        <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right"></td>
                        <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right"><strong>TOTAL</strong></td>
                        <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                            <strong><?= $total_usage; ?></strong>
                        </td>
                        <td align="right" style="font-family: arial; color:#343434; font-weight: bold; padding: 6px;" width="180" align="right">
                            <strong><?= '₱ '.number_format($total_charges,2); ?></strong>
                        </td>
                    </tr>
                </table>

            <?php } elseif(isset($data['report_type']) && $data['report_type'] == 'reading'){ ?> 

                <table border="0" align="center" width="890" cellpadding="0" cellspacing="0" class="container980">
                    <?php
                        $total_reading = 0;
                        $bgcolor="#efefef";
                        $this->db->select("ref_no, reading_date, reading");
                        $this->db->from("hydra_billing.readings");
                        $this->db->where("account_id",$data['id']);
                
                        if($data['selectedDate'] != 'all' AND $data['selectedDate'] != ""){ 
                            $this->db->where("year(reading_date)",$data['selectedDate']); 
                        }else{
                            if($data['startDate'] != "" && $data['endDate'] != ""){
                                $start_date = date("Y-m-d", strtotime($data['startDate']));
                                $end_date = date("Y-m-d", strtotime($data['endDate'])); 
                
                                $this->db->where("reading_date >=",$start_date);
                                $this->db->where("reading_date <=",$end_date);
                            }
                        }  
                        $this->db->order_by("reading_date","ASC");
                        $query = $this->db->get();
                        if($query->num_rows() > 0){
                            foreach($query->result_array() as $_query){
                                $total_reading += $_query['reading']
                    ?>
                                <tr style="background-color: <?= $bgcolor ?>">
                                    <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                        <?= $_query["ref_no"]; ?>
                                    </td>
                                    <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                        <?= strtoupper($_query["reading_date"]); ?>
                                    </td>
                                    <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right">
                                        <?= strtoupper($_query["reading"]); ?>
                                    </td>
                                </tr>
                    <?php 
                            } 
                        } else {
                    ?>
                                <tr><td align="center" colspan="6" style="padding: 6px;">NO MATCHING RECORDS FOUND</td></tr>

                    <?php } ?>

                    <tr class="footer" style="background-color: #bad3f1;">
                        <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right"></td>
                        <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right"><strong>TOTAL</strong></td>
                        <td align="center" style="font-family: arial; color:#343434; font-weight: bold; padding: 6px;" width="180" align="right">
                            <strong><?= $total_reading; ?></strong>
                        </td>
                    </tr>
                </table>
            
            <?php } else { ?>

                <table id="ledger_table" border="0" align="center" width="890" cellpadding="0" cellspacing="0" class="container980">
									<tbody>
                    <?php
                        $current_date   = date("Y-m-d");
                        $temp_penalties = $this->db->get_where("hydra_billing.penalties")->row_array();

                        $this->db->select("
                            bill.id,
                            bill.ref_no,
                            bill.total_charges,
                            bill.created_at,
                            bill.due_date,
                            bill.is_paid,
                            payment.sub_total,
                            payment.received_amount,
                            payment.balance_covered,
                            payment.payment_date,
                            payment.reconnection_fee,
                            payment.is_penalty,
                            payment.created_date
                        ");

                        $this->db->from("hydra_billing.bills bill");
                        $this->db->join("hydra_billing.payments payment", "payment.bill_id = bill.id AND payment.is_archive = 0", "LEFT");
                        $this->db->where("bill.account_id", $data['id']);
                        $this->db->where("bill.status", 1);

                        // --- FILTERING --- //
                        if ($data['selectedDate'] === 'all') {

                            // No date filtering

                        } elseif ($data['selectedDate'] !== 'custom') {

                            // Filter by specific YEAR
                            $this->db->where("YEAR(bill.due_date)", $data['selectedDate']);

                        } else {

                            // Custom range filtering
                            if (!empty($data['startDate']) && !empty($data['endDate'])) {

                                $start_date = date("Y-m-d", strtotime($data['startDate']));
                                $end_date   = date("Y-m-d", strtotime($data['endDate']));

                                $this->db->where("bill.due_date >=", $start_date);
                                $this->db->where("bill.due_date <=", $end_date);
                            }
                        }


                        $this->db->order_by("bill.due_date asc, payment.created_date asc");
                        $query = $this->db->get();

                        $results = $query->result_array();
                        $current_bill_id  = null;
                        $running_balance  = 0;

                        if($query->num_rows() > 0){
                          foreach ($results as $_query) {

                            // ---------------------------------------
                            // 1. Compute base charges + penalties
                            // ---------------------------------------

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

                            // ---------------------------------------
                            // 2. New Bill? → Reset running balance
                            // ---------------------------------------
                            if ($current_bill_id !== $_query['id']) {
                                $current_bill_id = $_query['id'];
                                $running_balance = $total_charges;   // Full amount before payments
                            }

                            // ---------------------------------------
                            // 3. Compute credit and new balance
                            // ---------------------------------------

                            $rec = floatval($_query["received_amount"]  ?? 0);
                            $cov = floatval($_query["balance_covered"] ?? 0);

                            $credit = $rec + $cov;
                            $debit  = $running_balance;

                            $new_balance = $running_balance - $credit;
                            $running_balance = $new_balance; // update for next payment

                            // ---------------------------------------
                            // 4. Render HTML Row
                            // ---------------------------------------
                    ?>

                      <tr style="background-color: #efefef">
                          <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180">
                              <?= date("Y-m-d", strtotime($_query["due_date"])); ?>
                          </td>

                          <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180">
                            <?= $_query["ref_no"]; ?>
                          </td>

                          <td align="right" style="font-family: arial; color:#343434; padding: 6px;" width="180">
                              <span class="m--text-muted" style="font-size: 9px;">
                                  <small>
                                      ₱ <?= number_format($base_charge, 2) ?> 
                                      + ₱ <?= number_format($penalty_amount, 2) ?>
                                      + ₱ <?= number_format($_query['reconnection_fee'], 2) ?>
                                  </small>
                              </span><br>
                              ₱ <?= number_format($debit, 2); ?>
                          </td>

                          <td align="right" style="font-family: arial; color:#343434; padding: 6px;" width="180">
                              <span class="m--text-muted" style="font-size: 11px;">
                                  <small>
                                      <?= $_query['payment_date'] ? 'PD: '.date("M d, Y", strtotime($_query['payment_date'])) : "--" ?>
                                  </small>
                              </span><br>
                              ₱ <?= number_format($credit, 2); ?>
                          </td>

                          <td align="right" style="font-family: arial; color:#343434; padding: 6px;" width="180">
                              ₱ <?= number_format($new_balance, 2); ?>
                          </td>
                      </tr>
                      
                    <?php 
                        } // End foreach
                    ?>
                        <tr>
                          <td colspan="6">
                            <!-- <div class="watermark"><?//= number_format(($total_balance + $total_penalty) - $overPayment, 2) > 0 ? "--FOR DISCONNECTION--" : "--FOR RECONNECTION--" ; ?></div> -->
                             <div class="watermark">--FOR DISCONNECTION--</div>
                          </td>
                        </tr>
              <?php 
                      } else { 
              ?>
                      <tr><td align="center" colspan="6" style="padding: 6px;">NO MATCHING RECORDS FOUND</td></tr>
              <?php 
                      } // end else
              ?>

                      <tr class="footer" style="background-color: #bad3f1;">
                          <td></td>
                          <td></td>
                          <td align="right"><small>Total Charges + Penalties + RF = Debit</small></td>
                          <td align="center" style="font-family: arial; color:#343434; padding: 6px;" width="180" align="right"></td>
                          <td align="center"><strong>₱ <?= number_format($overdue_charges, 2);  ?></strong></td>
                      </tr>
                    </tbody>
                </table>
            <?php } ?>
            <table border="0" align="center" width="890" cellpadding="0" cellspacing="0" class="container980">
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left" width="100%"><strong><i>This is a computer-generated document. No signature is required.</i></strong></td>
                </tr>
            </table>
        </tr>
    </table>
</body>
</html>

<script>
    $(document).ready(function(){
        var id = $('#ledger_table tbody').find('td:first').text();
        console.log(id); 
    });
</script>
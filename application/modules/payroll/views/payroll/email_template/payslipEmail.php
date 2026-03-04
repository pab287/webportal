<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
    <meta name="viewport" content="width=600,initial-scale = 2.3,user-scalable=no">
    <!--[if !mso]><!-- -->
    <link href='https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700' rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Quicksand:300,400,700' rel="stylesheet">
    <!-- <![endif]-->
    <title>PAYROLL</title>
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

        p,
        h1,
        h2,
        h3,
        h4 {
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
            font-size: 7px;
            border: 0;
        }
        /* ----------- responsivity ----------- */


    </style>
    <!-- [if gte mso 9]><style type=”text/css”>
        body { font-family: arial, sans-serif!important; }
        </style>
    <![endif]-->
</head>
<body class="respond" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" >
    <!-- header -->
    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">
        <tr>
            <td align="center">
                <table border="0" align="center" cellpadding="0" cellspacing="0" witdth="280px">

                    <tr>
                        <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table border="0" align="center" cellpadding="0" cellspacing="0">
								<tr>
                                    <td align="center" height="70" style="height:70px;">
										<h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434;"><?php echo $result->data->company_description?></h1>
                                        <h1 style="font-family: Quicksand, Calibri, sans-serif; margin-top: 15px; color:#343434; text-transform: uppercase;">Payslip Information</h2>
                                        <?php $serverName = $_SERVER['SERVER_NAME']; ?>
                                        <?php if($serverName == "localhost" || $serverName == "dev.gccph.com" || $serverName == "192.168.7.96"): ?>
                                        <h4 style="font-family: Quicksand, Calibri, sans-serif; margin-top: 15px; color:#343434; text-transform: uppercase;">Please disregard, this is a test sending email template</h4>
                                        <?php endif; ?>
									</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <!-- end header -->
    <!--  50% image -->
    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">
        <tr>
          <td align = "center">
            <table border="0" align="center" cellpadding="0" cellspacing="0" width="280px">
              <tr>
              <td style="text-align: left; "><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;">Pay Period</h1></td>
              <td style="text-align: right; "><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;"><?php echo $result->data->date_start." - ".$result->data->date_end;?></h1></td>
              </tr>
              <tr>
       <td colspan="2" style="background-color: #000; height: 2px;"></td>
   </tr>
              <tr style="text-align: center;">
              <td colspan="2">
                <h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 10px;"><?php echo $result->data->employee_name ?></h1>
                <h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 10px;"><?php echo $result->data->idno ?></h1>
                <h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 10px;"><?php echo strtoupper($result->data->position_description) ?></h1>
                <h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 10px;"><?php echo strtoupper($result->data->department_description) ?></h1>
              </td>
              </tr>
              <tr><td colspan="2" style="background-color: #000; height: 2px;"></td></tr>
              <tr>
                <?php
                if (intval($result->data->is_bonus) == 1 && $result->data->bonus_code) {
                  echo "<td><h1 style='font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;'>BONUS / {$result->data->bonus_code}</h1></td>";
                } else {
                  echo "<td style='text-align: left;'><h1 style='font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;'>Basic Pay</h1></td>";
                  echo "<td style='text-align: right;'><h1 style='font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;'>{$result->data->target_payrate}</h1></td>";
                }
              ?>
              </tr>
              <tr style='text-align: center;'>
                <td><h1 style='font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;' >REG DAYS: <?php echo $result->data->ewd_decimal; ?></h1></td>
                <td><h1 style='font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;'>REG HOURS: <?php echo $result->data->target_hours; ?></h1></td>
              </tr>
              <?php if(floatval($result->data->total_unrendered_amount) > 0): ?>
              <tr><td colspan="2" style="background-color: #ccc; height: 1px;"></td></tr>
              <tr>
                <td style='text-align: left;'><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;">Lates/Absences</h1></td>
                <td style='text-align: right;'><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;"> (<?php echo $result->data->total_unrendered_amount; ?>)</h1></td>
              </tr>
              <tr style='text-align: center;'>
                <td><h1 style='font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;'>Absent Hrs: <?php echo $result->data->absent_hours; ?></h1></td>
                <td><h1 style='font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;'>UT Hrs: <?php echo $result->data->undertime_hours; ?></h1></td>
              </tr>
              <?php endif; ?>
              <?php if(floatval($result->data->unpaid_holiday_amount) > 0): ?>
              <tr><td colspan="2" style="background-color: #ccc; height: 1px;"></td></tr>
              <tr>
                <td style='text-align: left;'><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;">Unpaid Holiday</h1></td>
                <td style='text-align: right;'><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;"> (<?php echo $result->data->unpaid_holiday_amount; ?>)</h1></td>
              </tr>
              <tr style='text-align: center;'>
                <td><h1 style='font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;'>Days: <?php echo floatval($result->data->unpaid_holiday_hours) / 8; ?></h1></td>
                <td><h1 style='font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;'>Hrs: <?php echo $result->data->unpaid_holiday_hours; ?></h1></td>
              </tr>
              <?php endif; ?>
              <?php if(floatval($result->data->total_allowances) > 0): ?>
              <tr><td colspan="2" style="background-color: #ccc; height: 1px;"></td></tr>
              <tr>
                <td style='text-align: left;'><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;">Allowances</h1></td>
                <td style='text-align: right;'><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;"> <?php echo $result->data->total_allowances?></h1></td>
              </tr>
              <?php endif; ?>

              <tr><td colspan="2" style="background-color: #000; height: 2px;"></td></tr>
              <?php if ($result->data->ot_amount > 0) : ?>
              <tr>
              <td style='text-align: left;'><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;">Overtime</h1></td>
              <?php if ($result->data->ot_ndiff_amount > 0) : ?>
                <td style='text-align: right;'><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;"><?php echo number_format($result->data->ot_amount + $result->data->ot_ndiff_amount, 2); ?></h1></td>
              <?php else : ?>
                <td style='text-align: right;'><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;"><?php echo $result->data->ot_amount; ?></h1></td>
                <?php endif; ?>
              </tr>
              <tr style='text-align: center;'>
                <td><h1 style='font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;'>OT Hrs: <?php echo number_format($result->data->ot_hours,2); ?></h1></td>
                <td><h1 style='font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;'>NDiff hrs: <?php echo number_format($result->data->ot_ndiff_hours, 2); ?></h1></td>
              </tr>
              <tr><td colspan="2" style="background-color: #ccc; height: 1px;"></td></tr>
              <?php endif; ?>
              <tr>
                <td style='text-align: left;'><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;">Gross Pay</h1></td>
                <td style='text-align: right;'><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;"> <?php echo $result->data->gross_pay?></h1></td>
              </tr>
              <?php if (intval($result->data->is_bonus)==1 && ($result->data->adjustment_d_count && count($result->data->adjustment_d_count) > 0)):?> <!--Start 1 -->
                <tr><td colspan="2" style="background-color: #ccc; height: 1px;"></td></tr>
                <tr><td style="text-align: left;" ><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;">Deductions</h1></td></tr>
              
                <?php endif; ?> <!--End 1 -->
                <?php if(intval($result->data->is_bonus) == 0): ?> <!--Start 2 -->
                  <tr><td colspan="2" style="background-color: #ccc; height: 1px;"></td></tr>
                  <?php if(floatval($result->data->sss) != 0 || floatval($result->data->sss_prov) != 0 || floatval($result->data->ph) != 0 || floatval($result->data->hdmf) != 0 || floatval($result->data->tax) != 0 || floatval($result->data->total_loans) != 0 || floatval($result->data->sss_loan) != 0 || floatval($result->data->hdmf_loan) != 0) { ?> <!--Start 3 --> 
                    <tr><td style="text-align: left;" ><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;">Deductions</h1></td></tr>
                    <?php } ?> <!--End 3 -->

                      <?php
                      if ($result->data->sss_prov && floatval($result->data->sss_prov) > 0) {
                      echo '<tr><td style="text-align: right;" colspan="2"><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px; margin-right:50px;">SSS PROVIDENT: ' . $result->data->sss_prov . '</h1></td></tr>';
                      }
                      ?>

                      <?php
                      if ($result->data->ph && floatval($result->data->ph) > 0) {
                      echo '<tr><td style="text-align: right;" colspan="2"><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px; margin-right:50px;">PHILHEALTH: ' . $result->data->ph . '</h1></td></tr>';
                      }
                      ?>

                      <?php
                      if ($result->data->hdmf && floatval($result->data->hdmf) > 0) {
                      echo '<tr><td style="text-align: right;" colspan="2"><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px; margin-right:50px;">HDMF: ' . $result->data->hdmf . '</h1></td></tr>';
                      }
                      ?>
                      
                      <?php
                      if ($result->data->tax && floatval($result->data->tax) > 0) {
                      echo '<tr><td style="text-align: right;" colspan="2"><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px; margin-right:50px;">TAX: ' . $result->data->tax . '</h1></td></tr>';
                      }
                      ?>
                    <?php if($result->data->total_loans && (floatval($result->data->total_loans) > 0 || floatval($result->data->sss_loan) > 0 || floatval($result->data->hdmf_loan) > 0 || count($result->data->loans) > 0)): ?>
                      <?php foreach ($result->data->loans as $kk => $vv): ?>
                        <tr>
                            <?php if($vv->amount_due > 0){ ?>
                              <span>
                                <tr>                             
                                <td style="text-align: right;" colspan="2">
                                  <h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px; margin-right:50px;"><?php echo $vv->loan_name; ?>: <?php echo $vv->amount_due; ?></h1>
                                </td>
                                </tr> 
                              </span>
                            <?php } ?>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?> 
                  <?php endif; ?> <!--End 2 -->
                  <?php if($result->data->adjustment_d_count && count($result->data->adjustment_d_count) > 0): ?>
                    <?php foreach ($result->data->adjustment_deductions as $kk => $vv): ?>
                    <tr>
                      <td style="text-align: right;"><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px;"><?php echo strtoupper($vv->label); ?><?php echo " ".$vv->display_value; ?></h1></td>
                    </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  <?php if(intval($result->data->is_bonus) == 0): ?>
                    <?php if(floatval($result->data->deductions) > 0) { ?>
                      <tr><td colspan="2" style="background-color: white; height: 10px;"></td></tr>
                      <tr><td colspan="2" style="background-color: #ccc; height: 1px;"></td></tr>
                      <tr><td colspan="2" style="background-color: white; height: 10px;"></td></tr>
                      <tr>
                <td style='text-align: left;'><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px; ">Total Loans & Deductions</h1></td>
                <td style='text-align: right;'><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434; font-size: 12px; "> (<?php echo $result->data->deductions?>)</h1></td>
              </tr>
                      <?php } ?>
                  <?php endif; ?>
                  <tr><td colspan="2" style="background-color: white; height: 10px;"></td></tr>
                  <tr>
   <td colspan="2" style="border: 2px solid #343434; padding: 5px; ">
       <table style="width: 100%;">
           <tr>
               <td style="text-align: left;"><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434;">Net Pay</h1></td>
               <td style="text-align: right;"><h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434;"><span>&#8369; </span><?php echo $result->data->net_pay?></h1></td>
           </tr>
       </table>
   </td>
</tr>
<?php if (intval($result->data->is_bonus) == 1): ?>
          <tr>
          <td colspan="2" style="text-align: center; padding-top: 15px;"> <h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434;">[ BONUS / 13TH MONTH ]</h1></td>
          </tr>                 
                            <?php endif; ?>


            </table>

            
          </td>
         
          
          
      </tr>    
             
      

    </table>

    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" style="border-top: 2px dashed #e0e0e0; margin-top: 10px">
        <tr>
            <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
        </tr>
        <tr>
            <td align="center">
                <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
                    <tr>
                        <td>
                            <table border="0" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"
                                class="container980">
                                <tr>
                                    <td align="left" style="color: #aaaaaa; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                                        <div style="line-height: 24px;">
                                            <span style="color: #333333;">GC&amp;C - PAYROLL</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <table border="0" align="left" width="5" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"
                                class="container980">
                                <tr>
                                    <td height="20" width="5" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
                                </tr>
                            </table>
                            <table border="0" align="right" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"
                                class="container980">
                                <tr>
                                    <td align="center">
                                        <table align="center" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
        </tr>
    </table>
    
    <!-- end footer ====== -->
</body>
</html>
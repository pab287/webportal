<style>
table, th, td {
    text-transform: uppercase;
}
@media print {
  footer {page-break-after: always;}
}
h3,h2,h4,p {margin: 0 auto;}
p,span{font-size: 12px;}

#form-input_content table td{font-size: 10px;}
#charges td,
#charges td, #dues td{font-size: 10px;}

.watermark img { width: 100%; }
.watermark { position: relative; }
.watermark::after {
  content: "PAID";
  position: absolute;
  top: 300;
  bottom: 0;
  right: 150;
  opacity: 0.2;
  font-size: 5.5em;
  color: black;
}
</style>

<div style="max-width: 500px;padding: 10px;border: 1px solid #000; float: left; margin-left: 20px; margin-bottom: 100px;" >
    <?php if($data["is_paid"] == 1){ ?>
      <div class="watermark"></div>
    <?php } ?> 
    <div class="form-header">
        <table width="500">
            <tbody>
                <tr>
                    <td style="vertical-align: top;">
                        <div class="form-title-container" id="printableArea">
                            <table width="100%">
                                <thead>
                                    <tr>
                                        <td align="center">
                                            <h3>BACOLOD HYDRA</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            <p><small>Carlos Hilado Ave., Circumferential Road, Brgy. Bata, Bacolod City <br > Office Tel. No.: (034) 441-2409-11 *Fax. No. (034) 441-0693</small></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="line-height: 5px; height: 5px;">&nbsp;</td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        
                        <div class="form-title-container">
                            <table width="100%">
                                <thead>
                                    <tr>
                                        <td align="center">
                                            <h4>BILLING STATEMENT</h4>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            <p><small>BH-FM-FIN-001 REV 0 01/20/2020</small></p>
                                        </td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="form-body">
    <table width="500">
        <tbody>
            <tr>
                <td align="center">
                    <table id="form-input_content" width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td align="center">
                                    <table id="form-input_content" width="100%" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <table width="100%" cellpadding="4" cellspacing="0">
                                                        <tbody>
                                                        <tr>
                                                            <td style="width: 100px;"><strong>BHBS #:</strong></td>
                                                            <td style="border-bottom: 1px solid; width: 300px;"> <?php echo $data["ref_no"];?></td>
                                                            <?php
                                                                if($data["ar"] !== '') {
                                                            ?>
                                                                    <td style="width: 100px;"><strong>AR #:</strong></td>
                                                                    <td style="border-bottom: 1px solid; width: 300px;"> <?php echo $data["ar"];?></td>
                                                            <?php
                                                                }
                                                            ?>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 100px;"><strong>Customer:</strong></td>
                                                            <td style="border-bottom: 1px solid; width: 300px;"> <?php echo $data["firstname"] . " ".$data["lastname"];?></td>
                                                            <td style="width: 10px;"><strong>Meter:<strong></td>
                                                            <td style="border-bottom: 1px solid;width: 100px;"> <?php echo $data["meterno"];?></td>
                                                        </tr>
                                                        <tr id="marginTop">
                                                            <td><strong>Account:</strong></td>
                                                            <td style="border-bottom: 1px solid;"> <?php echo $data["accountno"];?></td>
                                                            <td colspan="2" style="padding-right: 0px;position:relative;"><div style="border: 1px solid; padding: 5px; height:50%;position:absolute;display:flex;align-items:center; width: 90%;"><strong>DUE DATE:<strong> <span><?php echo $data["due_date"];?></span></div></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="display:flex;justify-content:left;flex-direction:row;"><div style="display:flex;justify-content:left;flex-direction:row;width: 80px;"><div style="font-weight: bold;margin-right:5px;">Block:</div> <div style="border-bottom: 1px solid; width: 15%;padding:0px 20%;"> <?php echo $data["block"];?></div></div></td>
                                                            <td colspan="2" style="display:flex;justify-content:left;flex-direction:row;"><div style="font-weight: bold;margin-right:5px;">Lot:</div><div style="border-bottom: 1px solid; width: 15%;padding:0px 20%;"><?php echo $data["lot"];?></div></div></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="1"><strong>Billing Address</strong></td>
                                                            <td colspan="3" style="border-bottom: 1px solid;"><?php echo $data["street"]. ", " .$data["brgy"]. ", " .$data["city"]. ", " .$data["province"];?> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="1"><strong>Billing Period</strong></td>
                                                            <td colspan="2"> <?php echo "<span style='border-bottom: 1px solid;padding: 0px 10px;'>".$data["billing_from"]."</span> to <span style='border-bottom: 1px solid;padding: 0px 10px;'>".$data["billing_to"]."</span>";?>  </td>
                                                            <td colspan="1" style="padding-right: 20px;position:relative; width: 300px;">
                                                                <div style="border: 1px solid; padding: 10px; height:50%;position:absolute;display:flex;align-items:center; width: 90%;">
                                                                    <strong style="border: 2px solid #000;padding: 2px;"><?php if($data["usage"] >= 11.46){echo "&nbsp;&nbsp;";}else{echo "x";}?></strong>
                                                                    <span style="text-align: center;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Minimum Usage &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(11.46 cu.m)</span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <table width="500">
        <tbody>
            <tr>
                <td align="center">
                    <table id="form-input_content" width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td align="center">
                                    <table id="form-input_content" width="100%" style="margin: 25px 0 0 0;" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <table width="100%" cellpadding="2" cellspacing="5">
                                                        <tbody>
                                                        <tr>
                                                            <td style="width: 70px;"><strong>Reading:</strong></td>
                                                            <td colspan="1" style="text-align: center;"><strong>Previous</strong></td>
                                                            <td colspan="1" style="text-align: center;"><strong>Current</strong></td>
                                                            <td colspan="1" style="text-align: center;"><strong>Usage</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td ><strong>&nbsp;</strong></td>
                                                            <td style="border-bottom: 1px solid; text-align: center;"> <?php echo number_format($data["previous"],2,".",",");?></td>
                                                            <td style="border-bottom: 1px solid; text-align: center;"> <?php echo number_format($data["current"],2,".",",");?></td>
                                                            <td style="border-bottom: 1px solid; text-align: center;"> <?php echo number_format($data["usage"],2,".",",");?></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <table cellpadding="2" id="form-input_content" style="margin: 0 auto;max-width: 300px;">
        <thead>
            <tr>
                <td colspan="4" style="text-align: center;">
                    <h4>BILLING CHARGES</h4>
                </td>
            </tr>
        </thead>
        <tbody id="charges">
            <tr>
                <td colspan="3" style="width: 300px;"><strong>Balance from last bill:</strong></td>
                <td colspan="1" style="border-bottom: 1px solid;padding-left: 5px; width: 100px; text-align: right;">
                    <?php
                        $balance = $data["balanceLastBill"];
                        echo number_format($balance < 0 ? 0 : $balance, 2, ".", ",");
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="width: 300px;"><strong>Over Payment Balance:</strong></td>
                <td colspan="1" style="border-bottom: 1px solid;padding-left: 5px; width: 100px; text-align: right;"><?php echo '('.number_format($data["balance"],2,".",",").')' ?></td>
            </tr>
            <tr>
                <td colspan="3" style="width: 300px;"><strong>Current due (Incl. of VAT):</strong></td>
                <td colspan="1" style="border-bottom: 1px solid;padding-left: 5px; width: 100px; text-align: right;"><?php echo number_format($data["current_due"],2,".",",");?></td>
            </tr>
            <tr>
                <td colspan="3" style="width: 300px;"><strong>Connect:</strong></td>
                <td colspan="1" style="border-bottom: 1px solid;padding-left: 5px; width: 100px; text-align: right;"><?php echo number_format($data["disconnection_fee"],2,".",",") ?></td>
            </tr>
            <tr>
                <td colspan="3" style="width: 300px;"><strong>12 VAT:</strong></td>
                <td colspan="1" style="border-bottom: 1px solid;padding-left: 5px; width: 100px; text-align: right;">0.00</td>
            </tr>
            <tr>
                <td colspan="3" style="width: 300px;"><strong>Others Charges:</strong></td>
                <td colspan="1" style="border-bottom: 1px solid;padding-left: 5px; width: 100px; text-align: right;">0.00</td>
            </tr>
            <tr>
                <td colspan="3" style="width: 300px;"><strong>HOA DUES:</strong></td>
                <td colspan="1" style="border-bottom: 1px solid;padding-left: 5px; width: 100px; text-align: right;">0.00</td>
            </tr>                                   
        </tbody>
    </table>
    <table cellpadding="2" style="margin: 0 auto;max-width: 200px;padding-top: 10px;">
        <tbody id="dues"  style="padding-bottom: 10px;">
            <tr>
                <td width="12%"></td>
                <td width="14%"><strong>Monthly Due:</strong></td>
                <td style="border-bottom: 1px solid;padding-left: 5px; text-align: right;">0.00</td>
                <td width="12%"></td>
            </tr>
            <tr>
                <td width="12%"></td>
                <td width="14%"><strong>Fines/Penalties:</strong></td>
                <td style="border-bottom:1px solid;padding-left: 5px;"><div style="width: 100%; text-align: right;"><?php echo number_format($data["overdue"],2,".",",") ?></div></td>
                <td width="12%"></td>
            </tr>
            <tr>
                <td width="12%"></td>
                <td width="14%"><strong>Arrears:</strong></td>
                <td style="border-bottom: 1px solid;padding-left: 5px; text-align: right;">0.00</td>
                <td width="12%"></td>
            </tr>
            <tr>
                <td width="12%"></td>
                <td width="14%"><strong>Total HOA Dues:</strong></td>
                <td style="border-bottom:double;padding-left: 5px; text-align: right;">0.00</td>
                <td width="12%"></td>
            </tr>                                 
        </tbody>
    </table>
    <table width="500">
        <tbody style="padding-bottom: 10px;">
            <tr>
                <td colspan="4" style="padding: 5px 10px;border: 2px solid;">
                    <div style="width: 80%;float: left;">
                        <strong>Total Charges: </strong>
                    </div> 

                    <div style="text-align: center;width: 20%;float:right;text-align: right;">
                        <strong><?php echo number_format((($data["balanceLastBill"] + $data['current_due'] + $data['overdue'] + $data['disconnection_fee']) - $data["balance"]),2,".",","); ?></strong>
                    </div>
                    
                    <div style="clear:both;"></div>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding-top: 10px;">
                    <p style="font-style:italic;text-transform:none;margin-bottom: 5px;font-size: 8px;">Note: Always bring this statement when paying.</p>
                    <p style="font-style:italic;text-transform:none;margin-bottom: 5px;font-size: 8px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Past due accounts shall be disconnected without prior notice.</p>
                    <p style="font-style:italic;text-transform:none;margin-bottom: 5px;font-size: 8px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Reconnection fee of P<?php echo number_format($data["reconnection_fee"],2,".",","); ?> must be paid to resume your service.</p>
                    <p style="font-style:italic;text-transform:none;margin-bottom: 5px;font-size: 8px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;If payment has already been made, please disregard the balance appears above.</p>
                    <p style="margin: 20px 0 0 0;text-align: center;"><<<<<  This is a system generated bill  >>>>></p>
                </td>
            </tr>
        </tbody>
    </table>

    </div>
</div>


<link href="<?php echo base_url("assets/vendors/base/vendors.bundle.css") ?>" rel="stylesheet" type="text/css" />

<style type="text/css">
  @page{
   margin: 0.50cm;
  }
  @media print {
    body { margin: 0 auto !important; }
    h1{margin:0;}
    tr{height:20px;}
    td{margin:0;}
    .row .col-6.col-md-6.col-lg-6.col-xl-6.col-sm-12 {
      width: 50%;
      display: inline-block;
      float: left;
      position: relative;
      margin-bottom: 6px;
    }
  }
  .row .col-6.col-md-6.col-lg-6.col-xl-6.col-sm-12 {
    width: 50%;
    display: inline-block;
    float: left;
    position: relative;
    margin-bottom: 6px;
  }
</style>

<?php $this->load->model('eforms/billing_m'); ?>

<div class="row">
  <?php if(isset($selectedPayment) && $selectedPayment):
    foreach($selectedPayment as $kk => $vv): 
      $account = $this->crud->load(array("id"=>$vv["account_id"]),"hydra_billing.accounts");
      $bill = $this->crud->load(array("id"=>$vv["bill_id"]),"hydra_billing.bills");
      //$reconnection_fee = $this->crud->load("hydra_billing.reconnection_fee");

      $customer_name = ucfirst($account["firstname"]).' '.ucfirst($account["middlename"][0]).'. '.ucfirst($account["lastname"]);

      /**
       * Ni comment ko muna yung code kasi my buang dito na nag lagay ng code na to taz d rin ginamit at nag cause ng error sa staging
       */
      // $employee = $this->crud->load(array("id"=>$vv["created_by"]),"gccmaster.tblemployees");
      // if ($employee["suffix"] == "" || $employee["suffix"] == null || $employee["suffix"] == "N/A" || $employee["suffix"] == "NONE") {
      //     $fullname = $employee["firstname"].' '.$employee["middlename"][0].'. '.$employee["lastname"];
      // } else {
      //     $fullname = $employee["firstname"].' '.$employee["middlename"][0].'. '.$employee["lastname"].' '.$employee["suffix"];
      // }

      $penalty = 0;
      if($vv["is_penalty"] == '1'){
          foreach(unserialize($vv["penalties"]) as $value){
            $penalty = $penalty + $value['overdue'];
          }
      }
  ?>
  <div class="col-6 col-md-6 col-lg-6 col-xl-6 col-sm-12">
    <div style="max-width: 450px;padding: 6px;border: 1px solid #000; margin-bottom: 160px;">
      <div class="form-header">
        <div class="row" style="margin-bottom: 10px;">
          <div class="col-12 col-md-12 col-lg-12 col-sm-12">
            <table style="font-size:small;" width="100%" border="0">
              <tr>
                <td><h1 style="font: 14px arial; text-align:center; padding-bottom:6px;"><strong>BACOLOD HYDRA INC.</strong><h1></td>
              </tr>
              <tr>
                <td><h1 style="font: 10px arial; text-align:center; text-transform: uppercase; padding-bottom:6px;">Carlos Hilado Ave., Circumferential Road, Brgy. Bata, Bacolod City<br>Office Tel. No.: (034)441-2409-11 *Fax, No. (034)441-0693</h1></td>
              </tr>
              <tr>
                <td><h1 style="font: 14px arial; text-align:center;"><strong>ACKNOWLEDGEMENT RECEIPT</strong><h1></td>
              </tr>
              <!-- <tr>
                <td><h1 style="font: 8px arial; text-align:center;">BH-FM-FIM-002 Rev 0 01/20/2020</h1></td>
              </tr> -->
            </table>
          </div>
        </div>

        <div class="col-11 col-md-11 col-lg-11 col-sm-11" style="margin-bottom: 15px;">
          <table style="font-size:small;" width="100%" border="0">
            <tr>
              <td><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;"><strong>Customer:</strong><h1></td>
              <td style="font: 12px arial; border-bottom: 1px solid;width: 180px; text-transform: uppercase;"><h1 style="font: 12px arial;"><?php echo $customer_name ?><h1></td>
              <td><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;"><strong>&nbsp;&nbsp;AR:</strong><h1></td>
              <td style="font: 12px arial; border-bottom: 1px solid;width: 100px; text-transform: uppercase;"><?php echo $vv["acknowledgement_receipt"] ?></td>
            </tr>
            <tr>
              <td><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;"><strong>Account:</strong><h1></td>
              <td style="font: 12px arial; border-bottom: 1px solid;width: 180px; text-transform: uppercase;"><?php echo $account['accountno'] ?></td>
              <td><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;"><strong>&nbsp;&nbsp;Date:</strong><h1></td>
              <td style="font: 12px arial; border-bottom: 1px solid;width: 100px; text-transform: uppercase;"><?php echo $vv["payment_date"] ?></td>
            </tr>
          </table>
        </div>

        <div class="row" style="margin-bottom: 15px;">
            <div class="col-10 col-md-10 col-lg-10 col-sm-10">
                <table width="90%" border="0" style="margin-bottom: 15px;">
                  <tr>
                    <td colspan="2"><h1 style="font: 12px arial; text-align:center;"><strong>BILLING CHARGES</strong><h1></td>
                  </tr>
                  <!-- <tr>
                    <td v-align="bottom"><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;">Balance from Last Bill:<h1></td>
                    <td style="font: 12px arial; border-bottom: 1px dashed;width: 170px; text-align:right;"><?php echo number_format((float)$vv["balance"], 2, '.', '') ?></td>
                  </tr> -->
                  <tr>
                    <td><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;">Current due:<h1></td>
                    <td style="font: 12px arial; border-bottom: 1px dashed;width: 170px; text-align:right;"><?php echo number_format((float)$bill["total_charges"], 2, '.', '') ?></td>
                  </tr>
                  <tr>
                    <td><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;">Reconnection fee:<h1></td>
                    <td style="font: 12px arial; border-bottom: 1px dashed;width: 170px; text-align:right;"><?php echo number_format((float)$vv["reconnection_fee"], 2, '.', '') ?></td>
                  </tr>
                  <tr>
                    <td><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;">12% VAT:<h1></td>
                    <td style="font: 12px arial; border-bottom: 1px dashed;width: 170px; text-align:right;">0.00</td>
                  </tr>
                  <tr>
                    <td><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;">Other Charges:<h1></td>
                    <td style="font: 12px arial; border-bottom: 1px dashed;width: 170px; text-align:right;">0.00</td>
                  </tr>
                </table>
                <table width="90%" border="0" style="margin-bottom: 15px;">
                  <tr>
                    <td colspan="2"><h1 style="font: 12px arial; text-align:center; margin-top:6px;"><strong>HOA DUES</strong><h1></td>
                  </tr>
                  <tr>
                    <td><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;">Monthly Due:<h1></td>
                    <td style="font: 12px arial; border-bottom: 1px dashed;width: 170px; text-align:right;">0.00</td>
                  </tr>
                  <tr>
                    <td><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;">Fines/Penalties:<h1></td>
                    <td style="font: 12px arial; border-bottom: 1px dashed;width: 170px; text-align:right;"><?php echo number_format((float)$penalty, 2, '.', '') ?></td>
                  </tr>
                  <tr>
                    <td><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;">Arrears:<h1></td>
                    <td style="font: 12px arial; border-bottom: 1px dashed;width: 170px; text-align:right;">0.00</td>
                  </tr>
                  <tr>
                    <td><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;">Total HOA Dues:<h1></td>
                    <td style="font: 12px arial; border-bottom: 1px dashed;width: 170px; text-align:right;">0.00</td>
                  </tr>
                  <tr>
                    <td><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;">Balance Covered<h1></td>
                    <td style="font: 12px arial; border-bottom: 1px dashed;width: 170px; text-align:right;"><?php echo "(".number_format((float)$vv["balance_covered"], 2, '.', '').")" ?></td>
                  </tr>
                </table>
                <table width="90%" border="0">
                  <tr>
                    <td><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;"><strong>Total Charges:</strong><h1></td>
                    <td style="font: 12px arial; border-bottom: 1px dashed;width: 170px; text-align:right;"><?php echo '₱ '.number_format((float)$vv["net_payment"], 2, '.', '') ?></td>
                  </tr>
                </table>
            </div>
        </div>

        <div class="row">
          <div class="col-11 col-md-11 col-lg-11 col-sm-11">

            <div class="row" style="border: 1px solid black; padding-bottom:3px;">
              <table width="100%" border="0">
                    <tr>
                      <td colspan="2"><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;"><strong>Amount Paid:</strong><h1></td>
                      <td style="font: 12px arial; border-bottom: 1px solid;width: 200px;"><?php echo $this->billing_m->numbersToWords(number_format((float)$vv["received_amount"], 2, '.', '')); ?></td>
                      <td colspan="2"><h1 style="font: 12px arial; text-align:right;"><strong>₱</strong><h1></td>
                      <td style="font: 12px arial; border-bottom: 1px solid;width: 100px;"><?php echo number_format((float)$vv["received_amount"], 2, '.', '') ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="row" style="border: 1px solid black; padding-bottom:3px;">
              <table width="100%" border="0">
                  <tr>
                    <td><h1 style="font: 12px arial; text-align:left; text-transform: uppercase;"><strong>Payment/s made in:</strong><h1></td>
                    <td>
                      <div style="text-align:center; display: flex; float:right;">
                        <?php if($vv["payment_type"]=='cash'){ ?> 
                          <input type="checkbox" checked style="font-color: 'black' !important;">
                        <?php }else{ ?>
                          <input type="checkbox">
                        <?php } ?>
                        <label><strong><h1 style="font: 12px arial; margin-top:2px; margin-left:2px; text-transform: uppercase;">Cash</strong><h1></label>
                      </div>
                    </td>
                    <td>
                      <div style="text-align:center; display: flex; float:right;">
                        <?php if($vv["payment_type"]=='check'){ ?>
                          <input type="checkbox" checked>
                        <?php }else{ ?>
                          <input type="checkbox">
                        <?php } ?>
                        <label><strong><h1 style="font: 12px arial; margin-top:2px; margin-left:2px; text-transform: uppercase;">Check no.</strong><h1></label>
                      </div>
                    </td>
                    <td style="font: 12px arial; border-bottom: 1px solid;width: 100px;"><?php echo $vv["payment_type"]=='check' ? $vv["payment_details"] : '' ?></td>
                  </tr>
              </table>
              <table width="100%" style="margin-top:3px;" border="0">
                  <tr>
                    <td style="text-align:right;"><label><h1 style="font: 12px arial; text-align:right; text-transform: uppercase;"><strong>Bank:</strong><h1></label></td>
                    <td style="font: 12px arial; border-bottom: 1px solid;width: 100px;"><?php echo $vv["payment_type"]=='bank' ? $vv["payment_details"] : '' ?></td>
                  </tr>
              </table>
            </div>
          </div>
        </div>
        <div class="row" style="margin-top:3px;">
          <div class="col-12 col-md-12 col-lg-12 col-sm-12">
            <h1 style="font: 10px arial; font-style:italic;">Note: This serves as an OFFICIAL RECEIPT when machine validated or signed by Bacolod Hydra Inc. authorized Collection Staff.<h1>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; endif; ?>
</div>

<!-- 8.5 by 11 -->
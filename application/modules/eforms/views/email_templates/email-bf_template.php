<!DOCTYPE html>
<html xmlns:v="urn:schemas-microsoft-com:vml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
    <meta name="viewport" content="width=600,initial-scale = 2.3,user-scalable=no">
    <!--[if !mso]><!-- -->
    <link href='https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700' rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Quicksand:300,400,700' rel="stylesheet">
    <!-- <![endif]-->
    <title>eForms V1.0</title>
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
            font-size: 14px;
            border: 0;
        }
		
		table td a{
			text-decoration: none !important;
			color: #343434 !important; 
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
            .container580 {
                width: 260px !important;
            }
            /*-------- secions ----------*/
            .section-img img {
                width: 280px !important;
                height: auto !important;
            }
        }
    </style>
    <!-- [if gte mso 9]><style type=”text/css”>
        body {
        font-family: arial, sans-serif!important;
        }
        </style>
    <![endif]-->
</head>
<body class="respond" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <!-- header -->
    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">
        <tr>
            <td align="center">
                <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">

                    <tr>
                        <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
								<tr>
                                    <td align="center" height="70" style="height:70px;">
                                        <h1>GC&amp;C - eForms</h1>
                                        <h4 style="margin-top: 15px;">Borrowing Form</h4>
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
    <?php
        $ship_to = "";
        $num_con = 0;
        $det = $this->borrowing->get_by_id($id);
        $con = $this->borrowing->get_borrowed_items($id);
        //$email_add = $this->Users_m->check_user($det->created_id); 
        //$email_add2 = $this->Users_m->check_user($det->last_edited_id); 
        $emp = $this->borrowing->emp_details($det->borrower);
        $fullname = $emp->display_name;
        /*if ($emp->suffix == "" || $emp->suffix == "N/A") {
            $fullname = $emp->firstname.' '.substr($emp->middlename,0,1).'. '.$emp->lastname;
        } else {
            $fullname = $emp->firstname.' '.substr($emp->middlename,0,1).'. '.$emp->lastname.' '.$emp->suffix;
        }*/
    ?>
    <!--  50% image -->
    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">
        <tr>
            <td align="center">
                <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
                    <col width="245">
                    <col width="735">
                    <tbody>
                        <tr>
                            <td align="right" valign="top" style="color: #333333; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; padding-right: 20px;">Borrowing Form No. :</td>
                            <td align="left" style="color: #343434; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px; text-transform: uppercase;"><?php echo $det->reference_no; ?></td>
                        </tr>
                        <tr>
                            <td align="right" valign="top" style="color: #333333; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; padding-right: 20px;">Company :</td>
                            <td align="left" style="color: #343434; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px; text-transform: uppercase;">
                            <?php 
                                if(is_numeric(trim($det->company))){
                                    $company = $this->borrowing->getCompany($det->company);
                                }else{
                                    $company = $det->company;
                                }
                                echo $company;
                            ?></td>
                        </tr>
                        <tr>
                            <td align="right" valign="top" style="color: #333333; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; padding-right: 20px;">Department :</td>
                            <td align="left" style="color: #343434; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px; text-transform: uppercase;">
                            <?php 
                                if(is_numeric(trim($det->department))){
                                    $department = $this->borrowing->getDepartment($det->department);
                                }else{
                                    $department = $det->department;
                                }
                                echo $department;
                            ?></td>
                        </tr>
                        <tr>
                            <td align="right" valign="top" style="color: #333333; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; padding-right: 20px;">Created by :</td>
                            <td align="left" style="color: #343434; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px; text-transform: uppercase;"><?php echo $det->created_by.' on '.date("M d, Y",strtotime($det->created_dt)); ?></td>
                        </tr>
                        <tr>
                            <td align="right" valign="top" style="color: #333333; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; padding-right: 20px;">Borrowed by :</td>
                            <td align="left" style="color: #343434; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px; text-transform: uppercase;"><?php echo $fullname; ?></td>
                        </tr>
                        <tr>
                            <td align="right" valign="top" style="color: #333333; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; padding-right: 20px;">Position :</td>
                            <td align="left" style="color: #343434; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px; text-transform: uppercase;">
                            <?php 
                                if(is_numeric(trim($det->position))){
                                    $position = $this->borrowing->getPosition($det->position);
                                }else{
                                    $position = $det->position;
                                }
                                echo $position;
                            ?></td>
                        </tr>
                        <tr>
                            <td align="right" valign="top" style="color: #333333; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; padding-right: 20px;">Date Needed :</td>
                            <td align="left" style="color: #343434; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px; text-transform: uppercase;"><?php echo $det->date_needed; ?></td>
                        </tr>
                        <tr>
                            <td align="right" valign="top" style="color: #333333; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; padding-right: 20px;">Purpose :</td>
                            <td align="left" style="color: #343434; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px; text-transform: uppercase;"><?php echo $det->purpose; ?></td>
                        </tr>
                        <tr>
                            <td align="right" valign="top" style="color: #333333; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; padding-right: 20px;">Status :</td>
                            <td align="left" style="color: #343434; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px; text-transform: uppercase;"><?php echo '<font color="green"> '.$det->status.'</font>'; ?></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
         <tr>
            <td align="center">
                <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
                    <thead>
                        <tr>
                            <th colspan="4"><p style="color: #333333; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: center; border-bottom: 1px solid #ccc; margin-bottom: 5px; padding-bottom: 10px;">CONTENTS</p></th>
                        </tr>
                        <tr>
                            <th align="left" style="color: #333333; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: left;">QTY</th>
                            <th align="left" style="color: #333333; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: left;">ASSET CODE</th>
                            <th align="left" style="color: #333333; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: left;">NAME</th>
                            <th align="left" style="color: #333333; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: left;">REMARKS</th>
                        </tr>
                   </thead>
                   <tbody>
                    <?php if(isset($con) && $con):  foreach($con as $arr): $getAsset = $this->crud->load(array("id"=>$arr->asset_id),"gccasset.assets");?>
                       <tr>
                            <td align="left" style="color: #343434; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 0px; text-transform: uppercase;"><?php echo $arr->quantity.' '.$arr->uom; ?></td>
                            <td align="left" style="color: #343434; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 0px; text-transform: uppercase;"><?php  echo $arr->asset_code; ?></td>
                            <td align="left" style="color: #343434; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 0px; text-transform: uppercase;"><?php echo $arr->asset_name; ?></  td>
                            <td align="left" style="color: #343434; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 0px; text-align: left; text-transform: uppercase;"><?php echo (isset($arr->remarks) && $arr->remarks)? $arr->remarks: "---"; ?></td>
                        </tr>
                        <?php endforeach; endif;?>
                   </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td height="80" style="font-size: 80px; line-height: 80px;">&nbsp;</td>
        </tr>
        <tr>
            <td align="center"><p><strong>-- THIS IS A SYSTEM GENERATED MESSAGE --</strong></p></td>
        </tr>
		<tr>
            <td height="40" style="font-size: 40px; line-height: 40px;">&nbsp;</td>
        </tr>
    </table>
    <!-- end section -->
    <!-- contact section -->
    <!-- end section -->
    <!-- footer ====== -->
    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">
        <tr>
            <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
        </tr>
		<tr>
            <td height="25" style="border-top: 1px solid #e0e0e0;font-size: 25px; line-height: 25px;">&nbsp;</td>
        </tr>
        <tr>
            <td align="center">
                <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
                    <tr>
                        <td>
                            <table border="0" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="container980">
                                <tr>
                                    <td align="left" style="color: #333333; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                                        <div style="line-height: 24px;">

                                            <span style="color: #333333;">GC&amp;C eForms - Borrowing Form</span>

                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <table border="0" align="left" width="5" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="container980">
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
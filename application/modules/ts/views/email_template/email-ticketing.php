<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
            background-COLOR: #ffffff;
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
            font-size: 13px;
            border: 0;
        }
		
        table.table-panel{
            border: 1px solid #343434 !important;
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
                width: 768px !important;
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
            table.table-panel{
                padding: 1.5px !important;
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
                width: 580px !important;
            }
            .container580 {
                width: 260px !important;
            }
            /*-------- secions ----------*/
            .section-img img {
                width: 280px !important;
                height: auto !important;
            }
            table.table-panel{
                padding: 1.5px !important;
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
                                    <td align="center" height="70" style="color: #343434; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; text-align: center;">
                                    <h1 align="center">Ticketing System</h1>
										<h4 align="center" style="margin-top: 15px;"><?php if($module){ echo $type."<br> (".$module.")"; }else{ echo $type; }?></h4>
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
            <td align="center">
                <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
                    <tbody>
                        <tr>
							<td align="left" style="color: #343434; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px;"class="main-header">
								<div style="line-height: 35px"><strong>Good day!</strong></div>
							</td>
						</tr>
                        <?php if($status == "Open"){ ?>
                            <tr>
                                <td align="left" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                                <div style="line-height: 24px">A new <strong><?php echo $type;?></strong> request has been created for <b><?php echo $module; ?></b> module on the Ticketing system that needs your attention today, <?php echo ($date)? date("F d, Y", strtotime($date)): date("F d, Y"); ?>. The details are as follows:  </div> 
                                </td>
                            </tr>
                            <tr>
                                <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                            </tr>
                        <?php }else{ ?>
                            <tr>
                                <td align="left" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                                <div style="line-height: 24px">Your Ticket # <?= $id ?> is <strong><?= strtoupper($status); ?>.</strong>
                                </td>
                            </tr>   
                            <tr>
                                <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="left" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                                <div style="line-height: 24px">Remarks: <?php echo $remarks ?> </div> 
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if($status == 'Open'){ ?>
                            <tr>
                                <td align="left" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                                <div style="line-height: 24px">Issue Summary: <?php echo $issue ?> </div> 
                                </td>
                            </tr>
                            <tr>
                                <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                            </tr>
                        <?php }else if($status == 'Onhold'){ ?>
                            <tr>
                                <td align="left" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                                <div style="line-height: 24px">Onhold Reason: <?php echo $onhold ?> </div> 
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if($status == 'Open') { ?>
                        <tr>
							<td align="left" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                            <div style="line-height: 24px">Requested by: <?php $name = $this->ticketing->requested_by($employee_name); echo $name; ?> </div> 
							</td>
						</tr>
                        <?php } ?>
                        <tr>
                            <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                        </tr>
                        <tr>
							<td align="left" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                            <div style="line-height: 24px">Date Needed: <?php echo $need_dt ?> </div> 
							</td>
						</tr>
                        <tr>
                            <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                        </tr>
                        <tr>
							<td align="left" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                            <div style="line-height: 24px">Kindly acknowledge this request. <br>Reminder: Please Login to Ticketing to Use Link <a href="<?php echo site_url("ts/ticketing/service?id=").$id?>">Click to Proceed to the Request.</a> Thank you!</div> 
							</td>
						</tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td height="80" style="font-size: 80px; line-height: 80px;">&nbsp;</td>
        </tr>
        <tr>
            <td align="center"><p><strong> -- THIS IS A SYSTEM GENERATED MESSAGE -- </strong></p></td>
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
                                    <td align="left" style="color: #333333; font-size: 13px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                                        <div style="line-height: 24px;">
                                            <span style="color: #333333;">GC&amp;C eForms - Ticketing System</span>
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
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
            font-size: 13px;
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
                    <?php $date = date("y-m-d"); ?>
                        <td align="center">
                            <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
								<tr>
                                    <td align="center" height="70" style="color: #343434; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; text-align: center;">
										<h1 align="center">GC&amp;C - eForms</h1>
										<h4 align="center" style="margin-top: 15px;">Travel Order - Summary(<?php echo date("F d, Y", strtotime($date . '+1 day')); ?>)</h4>
                                        <?php $serverName = $_SERVER['SERVER_NAME']; ?>
                                        <?php if($serverName == "localhost" || $serverName == "dev.gccph.com"): ?>
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
            <td align="center">
                <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
                    <tbody>
                        <tr>
							<td align="left" style="color: #343434; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px;"class="main-header">
								<p style="line-height: 35px">Good day!</p>
							</td>
						</tr>
						<tr>
							<td align="left" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
								<div style="line-height: 24px">The following <strong>TRAVEL ORDER (TO)</strong> transactions are dispatched for travel tomorrow, <?php echo date("F d, Y", strtotime($date . '+1 day')); ?>. Kindly secure any documents/assets to be sent to the travel order destination.</div>
							</td>
						</tr>
						<tr>
						<td height="30" style="font-size: 30px; line-height: 30px;">&nbsp;</td>
					</tr>
                    </tbody>
                </table>
            </td>
        </tr>
		<?php if($data): ?>
		<?php foreach($data as $key => $value): ?>
		<?php
		$vehicle_name = "---";
		$driver_name = "---";
		if($value->is_service == 1):
			$vehicle_name = "<p>".strtoupper($value->vehicle_name)."</p>";
			$vehicle_name .= "<p><small><strong>".strtoupper($value->plateno)."</strong></small></p>";
			$driver_name = "<p>".strtoupper($value->display_name)."</p>";
			$driver_name .= "<p><small><strong>".strtoupper($value->position)."</strong></small></p>";
		endif;
		if($value->is_commute == 1):
			$vehicle_name = "<p> COMMUTE </p>";
		endif;
		if($value->is_personal == 1):
			$vehicle_name = "<p> PERSONAL VEHICLE </p>";
		endif;
		if($value->is_others == 1):
			$vehicle_name = "<p>".strtoupper($value->others_remarks)."</p>";
		endif;
		?>
        <tr>
            <td algin="center">
                <table align="center" border="0" width="980" cellpadding="0" cellspacing="0" bgcolor="ffffff">
					<col width="245">
					<col width="245">
					<col width="245">
					<col width="245">
                    <thead>
                        <tr bgcolor="#0000FF">
                            <th align="left" width="245" style="color: #ffffff; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 18px; border: 1px solid #CCCCCC; padding: 4px;">Travel Order #</th>
                            <th align="left" width="245" style="color: #ffffff; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 18px; border: 1px solid #CCCCCC; padding: 4px;">Official Station</th>
                            <th align="left" width="245" style="color: #ffffff; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 18px; border: 1px solid #CCCCCC; padding: 4px;">Vehicle</th>
                            <th align="left" width="245" style="color: #ffffff; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 18px; border: 1px solid #CCCCCC; padding: 4px;">Driver</th>
                        </tr>
						<tr>
							<td align="left" width="245" style="color: #343434; font-size: 13px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 13px; border: 1px solid #CCCCCC; padding: 8px;">
								<?php echo strtoupper($value->reference_no); ?>
							</td>
							<td align="left" width="245" style="color: #343434; font-size: 13px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 13px; border: 1px solid #CCCCCC; padding: 8px;">
								<?php echo strtoupper($value->station); ?>
							</td>
							<td align="left" width="245" style="color: #343434; font-size: 13px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 13px; border: 1px solid #CCCCCC; padding: 8px;">
								<?php echo $vehicle_name; ?>
							</td>
							<td align="left" width="245" style="color: #343434; font-size: 13px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 13px; border: 1px solid #CCCCCC; padding: 8px;">
								<?php echo $driver_name; ?>
							</td>
						</tr>
                   </thead>
                   <tbody>
						<tr bgcolor="#00b0f0">
                            <th align="left" width="245" style="color: #ffffff; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 18px; border: 1px solid #CCCCCC; padding: 4px;">Personnel</th>
                            <th align="left" width="735" colspan="3" style="color: #ffffff; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 18px; border: 1px solid #CCCCCC; padding: 4px;">Destination</th>
                        </tr>
						<tr>
							<td align="left" width="245" class="personnel-cell" style="vertical-align: top; border: 1px solid #CCCCCC; padding: 6px 6px 6px 0px;">
								<ul class="personnel-list">
							<?php if(isset($value->personnels) && $value->personnels): ?>
								<?php foreach($value->personnels as $personnel): ?>
									<li style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 22px;"><?php echo strtoupper($personnel); ?></li>
								<?php endforeach; ?>
							<?php else: ?>
									<li style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 22px;">No Assigned Personnel</li>
							<?php endif; ?>
								</ul>
							</td>
							<td align="left" width="735" colspan="3" style="vertical-align: top; border: 1px solid #CCCCCC; padding: 6px 6px 6px 0px;">
								<ul class="destination-list">
								<?php if(isset($value->destinations) && $value->destinations): ?>
								<?php foreach($value->destinations as $temp): ?>
									<li style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 14px; margin-bottom: 15px;">
										<p style="margin: 5px 0px;"><?php echo strtoupper($temp->destination); ?></p>
										<p style="margin: 5px 0px;"><small><strong><?php echo strtoupper($temp->purpose); ?></strong></small></p>
										<p style="margin: 15px 0px 0px 0px;"><small><strong>REQUESTED BY: <?php echo strtoupper($temp->requested_by); ?> [ <?php echo $temp->duration; ?> ]</strong></small></p>
										<?php if($temp->remarks): ?>
										<p style="margin: 5px 0px;"><small><strong>REMARKS: <?php echo strtoupper($temp->remarks); ?></strong></small></p>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
								<?php else: ?>
									<li style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 14px; margin-bottom: 15px;">No Assigned Destination</li>
								<?php endif; ?>
								</ul>
							</td>
						</tr>
				   </tbody>
                </table>
            </td>
        </tr>
		<tr>
            <td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td>
        </tr>
		<?php endforeach; ?>
		<?php endif; ?>
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

                                            <span style="color: #333333;">GC&amp;C eForms - Travel Order</span>

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
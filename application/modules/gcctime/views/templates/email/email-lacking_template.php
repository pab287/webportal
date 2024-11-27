<!DOCTYPE html>
<html lang="en">
<?php
$dateToday = date("F d, Y", strtotime("-1 days"));
?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
    <meta name="viewport" content="width=600,initial-scale = 2.3,user-scalable=no">
    <!--[if !mso]><!-- -->
    <link href='https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700' rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Quicksand:300,400,700' rel="stylesheet">
    <!-- <![endif]-->
    <title>GCCTIME - Lacking and Double Entry Report</title>
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
            .container980 {
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
    </style>
    <!-- [if gte mso 9]><style type=”text/css”>
        body {
        font-family: arial, sans-serif!important;
        }
        </style>
    <![endif]-->
</head>
<body class="respond" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
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
										<h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434;">GC&amp;C - GCCTIME</h1>
										<h2 style="font-family: Quicksand, Calibri, sans-serif; margin-top: 15px; color:#343434; text-transform: uppercase;">Lacking and Double Entry Report</h2>
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
    <!-- big image section -->
    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color">
        <tr>
            <td align="center">
                <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
                    <tr>
                        <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
                    </tr>
					<tr>
                        <td align="left" style="color: #343434; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px;"class="main-header">
                            <p style="line-height: 35px">Good day!</p>
                        </td>
                    </tr>
					<tr>
						<td align="left" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
						<?php if(isset($lacking_entry, $double_entry) && ($lacking_entry || $double_entry)): ?>
						<div style="line-height: 24px">The following employees have <strong style="font-size: 18px; color: #ff0000;">LACKING</strong> and <strong style="font-size: 18px; color: #ff0000;">DOUBLE</strong> time log entries yesterday, <?php echo $dateToday; ?></div>
						<?php else: ?>
						<div style="line-height: 24px">There are no employees with <strong style="font-size: 18px; color: #ff0000;">LACKING</strong> and <strong style="font-size: 18px; color: #ff0000;">DOUBLE</strong> time log etries as of yesterday, <?php echo $dateToday; ?></div> 
						<?php endif; ?>
						</td>
					</tr>
                    <tr>
                        <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="40" style="font-size: 40px; line-height: 40px;">&nbsp;</td>
        </tr>
		<tr>
			<td align="center">
			<table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
				<tr>
					<td align="left" style="color: #343434; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px;"class="main-header">
						<p style="line-height: 35px">DOUBLE ENTRY LIST</p>
					</td>
				</tr>
				<tr>
					<td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td align="center">
				<table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980 table table-striped" style="width: 980px;">
					<col width="145">
					<col width="245">
					<col width="500">
					<col width="90">
					<thead>
						<tr>
							<th align="center" style="color: #333333; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: center; padding: 0 12.5px; border-bottom: 1px solid #333333;">BIOMETRIC ID</th>
							<th align="left" style="color: #333333; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: left; padding: 0 12.5px; border-bottom: 1px solid #333333;">EMPLOYEE NAME</th>
							<th align="left" style="color: #333333; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: left; padding: 0 12.5px; border-bottom: 1px solid #333333;">TIME IN/OUT</th>
							<th align="center" style="color: #333333; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: center;  padding: 0 12.5px; border-bottom: 1px solid #333333;">COUNT</th>
						</tr>
					</thead>
				   <tbody>
				<?php if(isset($double_entry) && $double_entry): ?>
				   <?php foreach($double_entry as $entry): ?>
					<tr>
						<td align="center" style="color: #888888; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 12.5px; border-bottom: 1px solid #888888;"><?php echo (isset($entry["biometric_id"]) && $entry["biometric_id"])? $entry["biometric_id"]: "No Id"; ?></td>
						<td align="left" style="color: #888888; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 12.5px; border-bottom: 1px solid #888888;"><?php echo (isset($entry["employee_name"]) && $entry["employee_name"])? strtoupper($entry["employee_name"]): "No Name"; ?></td>
						<td align="left" style="color: #888888; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 12.5px; border-bottom: 1px solid #888888;"><?php echo (isset($entry["logged_time"]) && $entry["logged_time"])? $entry["logged_time"]: "---"; ?></td>
						<td align="center" style="color: #888888; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 12.5px; border-bottom: 1px solid #888888;"><?php echo (isset($entry["time_count"]) && $entry["time_count"])? $entry["time_count"]: "0"; ?></td>
					</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td align="center" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 12.5px; border-bottom: 1px solid #888888;" colspan="4">No employee data found.</td>
					</tr>
				<?php endif; ?>
				   </tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td height="60" style="font-size: 60px; line-height: 60px;">&nbsp;</td>
		</tr>
		<tr>
			<td align="center">
			<table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
				<tr>
					<td align="left" style="color: #343434; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px;"class="main-header">
						<p style="line-height: 35px">LACKING ENTRY LIST</p>
					</td>
				</tr>
				<tr>
					<td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td align="center">
				<table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980 table table-striped" style="width: 980px;">
					<col width="145">
					<col width="245">
					<col width="155">
					<col width="345">
					<col width="90">
					<thead>
						<tr>
							<th align="center" style="color: #333333; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: center; padding: 5 12.5px; border-bottom: 1px solid #333333;">BIOMETRIC ID</th>
							<th align="left" style="color: #333333; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: left; padding: 5 12.5px; border-bottom: 1px solid #333333;">EMPLOYEE NAME</th>
							<th align="left" style="color: #333333; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: left; padding: 5 12.5px; border-bottom: 1px solid #333333;">TO REFERENCE #</th>
							<th align="left" style="color: #333333; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: left; padding: 5 12.5px; border-bottom: 1px solid #333333;">TIME IN/OUT</th>
							<th align="center" style="color: #333333; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: center;  padding: 5 12.5px; border-bottom: 1px solid #333333;">COUNT</th>
						</tr>
					</thead>
				   <tbody>
					<?php if(isset($lacking_entry) && $lacking_entry): ?>
					<?php foreach($lacking_entry as $entry): ?>
					<tr>
						<td align="center" style="color: #888888; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 12.5px; border-bottom: 1px solid #888888;"><?php echo (isset($entry["biometric_id"]) && $entry["biometric_id"])? $entry["biometric_id"]: "No Id"; ?></td>
						<td align="left" style="color: #888888; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 12.5px; border-bottom: 1px solid #888888;"><?php echo (isset($entry["employee_name"]) && $entry["employee_name"])? strtoupper($entry["employee_name"]): "No Name"; ?></td>
						<td align="left" style="color: #888888; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 12.5px; border-bottom: 1px solid #888888;"><?php echo (isset($entry["reference_no"]) && $entry["reference_no"])? $entry["reference_no"]: "N/A"; ?></td>
						<td align="left" style="color: #888888; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 12.5px; border-bottom: 1px solid #888888;"><?php echo (isset($entry["logged_time"]) && $entry["logged_time"])? $entry["logged_time"]: "---"; ?></td>
						<td align="center" style="color: #888888; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 12.5px; border-bottom: 1px solid #888888;"><?php echo (isset($entry["time_count"]) && $entry["time_count"])? $entry["time_count"]: "0"; ?></td>
					</tr>
					<?php endforeach; ?>
					<?php else: ?>
					<tr>
						<td align="center" style="color: #888888; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 12.5px; border-bottom: 1px solid #888888;" colspan="4">No employee data found.</td>
					</tr>
					<?php endif; ?>
				   </tbody>
				</table>
			</td>
		</tr>
		<tr>
            <td height="40" style="font-size: 40px; line-height: 40px;">&nbsp;</td>
        </tr>
		<tr>
            <td height="40" style="font-size: 40px; line-height: 40px;">&nbsp;</td>
        </tr>
		<tr>
            <td align="center">
				<h5 class="color:#343434;"> --THIS IS A SYSTEM GENERATED MESSAGE-- </h5>
			</td>
        </tr>
		<tr>
            <td height="40" style="font-size: 40px; line-height: 40px;">&nbsp;</td>
        </tr>
    </table>
    <!-- end section -->
	<!-- footer ====== -->
		<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color">
			<tr>
				<td height="25" style="border-top: 1px solid #e0e0e0;font-size: 25px; line-height: 25px;">&nbsp;</td>
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
												<span style="color: #333333;">GC&amp;C - GCCTIME</span>
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
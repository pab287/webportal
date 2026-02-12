<!DOCTYPE html>
<html lang="en">
<?php
$dateToday = date("h:i:s A, F d, Y", strtotime($date_absent));
$ampm = $meridiem;
$station_title = (isset($station_title) && $station_title) ? $station_title : "";
?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
    <meta name="viewport" content="width=600,initial-scale = 2.3,user-scalable=no">
    <!--[if !mso]><!-- -->
    <link href='https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700' rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Quicksand:300,400,700' rel="stylesheet">
    <!-- <![endif]-->
    <title>GCCTIME - Absentee Report</title>
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
                        <td align="center" style="font-family: Quicksand, Calibri, sans-serif;">
                            <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
								<tr>
                                    <td align="center" height="70" style="height:70px;">
										<h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434;">GC&amp;C - GCCTIME</h1>
										<h2 style="font-family: Quicksand, Calibri, sans-serif; margin-top: 15px; color:#343434; text-transform: uppercase;">
                                            <?= $station_title; ?> | Late Report - <?= $meridiem; ?>
                                        </h2>
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
						<?php if(isset($data) && $data): ?>
						<div style="line-height: 24px">The names listed below were unable to login to the biometric device and are considered <strong style="font-size: 18px; color: #ff0000;">ABSENT</strong> today as of <?php echo $dateToday; ?></div>
						<?php else: ?>
						<div style="line-height: 24px">There is no <strong style="font-size: 18px; color: #ff0000;">ABSENT</strong> employee as of <?php echo $dateToday; ?></div> 
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
		<?php if(isset($data) && $data): ?>
			<?php if(isset($meridiem) && $meridiem && $ampm == "PM"): ?>
			<tr>
				<td align="center">
                <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
					<tr>
                        <td align="left" style="color: #343434; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700; line-height: 35px;"class="main-header">
                            <p style="line-height: 35px">AFTERNOON ABSENTEE LIST</p>
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
                    <!-- 1st foreach start -->
                    <?php foreach($data as $station => $employess): ?>
                        <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980 table table-striped" style="width: 980px;border-spacing: 0px 15px;">
                            <col width="145">
                            <col width="320">
                            <col width="230">
                            <col width="100">
                            <col width="145">
                            <thead>
                                <tr>
                                    <th align="left" style="color: #333333; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; text-align: center; line-height: 1.4;">BIOMETRIC #</th>
                                    <th align="left" style="color: #333333; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; text-align: left; line-height: 1.4;">EMPLOYEE NAME</th>
                                    <th align="center" style="color: #333333; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 1.4; text-align: left;">DEPARTMENT</th>
                                    <th align="center" style="color: #333333; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 1.4; text-align: center;">AM/PM</th>
                                    <th align="center" style="color: #333333; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 1.4; text-align: center; ">LOA/TO REF#</th>
                                </tr>
                            </thead>

                            <tbody>

                            <!-- 2nd foreach start -->
                            <?php foreach ($employess as $biometric_id => $emp_info) :?>
                                <tr>
                                    <td align="center" style="vertical-align: top;">
                                        <span style="color: #888888;font-weight:600; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 1;">
                                            <?php echo (isset($emp_info["biometricno"]) && $emp_info["biometricno"])? $emp_info["biometricno"]: "N/A"; ?>
                                        </span>
                                    </td>

                                    <td align="left" style="vertical-align: top;">
                                        <p style="color: #888888;font-weight:600; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 1.4;">
                                            <?php echo (isset($emp_info["name"]) && $emp_info["name"])? strtoupper($emp_info["name"]): strtoupper("No Name"); ?> 
                                        </p>

                                        <p style="font-size: 10px; color: #888888; font-weight:400; font-family: 'Work Sans', Calibri, sans-serif; line-height: 1.4;">
                                            <?php echo (isset($emp_info["position"]) && $emp_info["position"])? strtoupper($emp_info["position"]): strtoupper("No Position"); ?>
                                        </p>
                                    </td>

                                    <td align="left" style="vertical-align: top;">
                                        <span style="color: #888888;font-weight:600; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 1;">
                                            <?php echo (isset($emp_info["department"]) && $emp_info["department"])? strtoupper($emp_info["department"]): strtoupper("No Department"); ?>
                                        </span>
                                    </td>

                                    <td align="center" style="vertical-align: top;">
                                        <span style="color: #888888;font-weight:600; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 1;">
                                            <?php echo (isset($emp_info["mrdn"]) && $emp_info["mrdn"])? strtoupper($emp_info["mrdn"]): strtoupper("---"); ?>
                                        </span>
                                    </td>

                                    <td align="center" style="vertical-align: top;">
                                        <span style="color: #888888;font-weight:600; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 1;">
                                            <?php echo (isset($emp_info["content"]) && $emp_info["content"])? strtoupper($emp_info["content"]): strtoupper("N/A"); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <!-- 2nd foreach End -->
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                    <!-- 1st foreach End -->
				</td>
			</tr>
			<?php endif; ?>
			<?php if(isset($meridiem) && $meridiem && $ampm == "AM"): ?>
			<tr>
				<td align="center">
                <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
					<tr>
                        <td align="left" style="color: #343434; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700; line-height: 35px;"class="main-header">
                            <p style="line-height: 35px">MORNING ABSENTEE LIST</p>
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
                    <!-- 1st foreach start -->
                    <?php foreach($data as $station => $employess): ?>
                        <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980 table table-striped" style="width: 980px;border-spacing: 0px 15px;">
                            <col width="145">
                            <col width="320">
                            <col width="230">
                            <col width="100">
                            <col width="145">
                            <thead>
                                <tr>
                                    <th align="left" style="color: #333333; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; text-align: center; line-height: 1.4;">BIOMETRIC #</th>
                                    <th align="left" style="color: #333333; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; text-align: left; line-height: 1.4;">EMPLOYEE NAME</th>
                                    <th align="center" style="color: #333333; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 1.4; text-align: left;">DEPARTMENT</th>
                                    <th align="center" style="color: #333333; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 1.4; text-align: center;">AM/PM</th>
                                    <th align="center" style="color: #333333; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 1.4; text-align: center; ">LOA/TO REF#</th>
                                </tr>
                            </thead>

                            <tbody>

                            <!-- 2nd foreach start -->
                            <?php foreach ($employess as $biometric_id => $emp_info) :?>
                                <tr>
                                    <td align="center" style="vertical-align: top;">
                                        <span style="color: #888888;font-weight:600; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 1;">
                                            <?php echo (isset($emp_info["biometricno"]) && $emp_info["biometricno"])? $emp_info["biometricno"]: "N/A"; ?>
                                        </span>
                                    </td>

                                    <td align="left" style="vertical-align: top;">
                                        <p style="color: #888888;font-weight:600; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 1.4;">
                                            <?php echo (isset($emp_info["name"]) && $emp_info["name"])? strtoupper($emp_info["name"]): strtoupper("No Name"); ?> 
                                        </p>

                                        <p style="font-size: 10px; color: #888888; font-weight:400; font-family: 'Work Sans', Calibri, sans-serif; line-height: 1.4;">
                                            <?php echo (isset($emp_info["position"]) && $emp_info["position"])? strtoupper($emp_info["position"]): strtoupper("No Position"); ?>
                                        </p>
                                    </td>

                                    <td align="left" style="vertical-align: top;">
                                        <span style="color: #888888;font-weight:600; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 1;">
                                            <?php echo (isset($emp_info["department"]) && $emp_info["department"])? strtoupper($emp_info["department"]): strtoupper("No Department"); ?>
                                        </span>
                                    </td>

                                    <td align="center" style="vertical-align: top;">
                                        <span style="color: #888888;font-weight:600; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 1;">
                                            <?php echo (isset($emp_info["mrdn"]) && $emp_info["mrdn"])? strtoupper($emp_info["mrdn"]): strtoupper("---"); ?>
                                        </span>
                                    </td>

                                    <td align="center" style="vertical-align: top;">
                                        <span style="color: #888888;font-weight:600; font-size: 12px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 1;">
                                            <?php echo (isset($emp_info["content"]) && $emp_info["content"])? strtoupper($emp_info["content"]): strtoupper("N/A"); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <!-- 2nd foreach End -->
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                    <!-- 1st foreach End -->
				</td>
			</tr>
			<?php endif; ?>
		<?php endif; ?>
		<tr>
            <td height="80" style="font-size: 80px; line-height: 80px;">&nbsp;</td>
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
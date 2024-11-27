<!DOCTYPE html>
<html lang="en">
<?php
$dateToday = date("F d, Y", strtotime($date));
?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
    <meta name="viewport" content="width=600,initial-scale = 2.3,user-scalable=no">
    <!--[if !mso]><!-- -->
    <link href='https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700' rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Quicksand:300,400,700' rel="stylesheet">
    <!-- <![endif]-->
    <title>GCCTIME - Punch Out Report</title>
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
										<h2 style="font-family: Quicksand, Calibri, sans-serif; margin-top: 15px; color:#343434; text-transform: uppercase;">Daily Punch Out Report</h2>
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
							<div style="line-height: 24px">These are the following employee(s) who punched out between 5:00PM-5:15PM as of <strong style="font-size: 18px; color: #343434;"><?php echo $dateToday; ?></strong>.</div>
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
        <tr>
            <td align="center">
                <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980 table table-striped" style="width: 980px;">
                    <col width="373">
                    <col width="517">
                    <col width="90">
                    <thead>
                        <tr>
                            <th align="left" style="color: #333333; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: left; padding: 0 12.5px;">EMPLOYEE NAME</th>
                            <th align="center" style="color: #333333; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: center;  padding: 0 12.5px;">DEPARTMENT</th>
                            <th align="right" style="color: #333333; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; font-weight: 600; mso-line-height-rule: exactly; line-height: 23px; text-align: right; padding: 0 12.5px;">TIME</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($data as $item): ?>
                    <tr>
                        <td align="left" style="color: #888888; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 12.5px;"><?php echo (isset($item->employee_name) && $item->employee_name)? strtoupper($item->employee_name): strtoupper("No name"); ?></td>
                        <td align="center" style="color: #888888; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 12.5px;"><?php echo (isset($item->department) && $item->department)? $item->department: strtoupper("No assigned department"); ?></td>
                        <td align="right" style="color: #888888; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 18px; padding: 5px 12.5px;"><?php echo (isset($item->datetime) && $item->datetime)? date("h:i A", strtotime($item->datetime)): "---"; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <?php endif; ?>
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
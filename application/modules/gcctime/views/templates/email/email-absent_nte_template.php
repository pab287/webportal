<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
<meta name="viewport" content="width=600,initial-scale = 2.3,user-scalable=no">
<!--[if !mso]><!-- -->
<link href='https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700' rel="stylesheet" />
<link href='https://fonts.googleapis.com/css?family=Quicksand:300,400,700' rel="stylesheet" />
<!-- <![endif]-->
<title>GCCTIME V2 - ABSENTEE NTE</title>
<style type="text/css">
html { width: 100%; }
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
p, h1, h2, h3, h4 { margin-top: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; color:#343434; }
table { border: 0; font-family: "Calibri"; font-size: 16px; }

#footer-content p {
    font-size: 12px;
    font-family: calibri;
}

/* ----------- responsivity ----------- */

@media only screen and (max-width: 620px) {
	/*------ top header ------ */
	.main-header { font-size: 20px !important; }
	.main-section-header { font-size: 28px !important; }
	.show { display: block !important; }
	.hide { display: none !important; }
	.align-center { text-align: center !important; }
	.no-bg { background: none !important; }
	/*----- main image -------*/
	.main-image img { width: 440px !important; height: auto !important; }
	/* ====== divider ====== */
	.divider img { width: 440px !important; }
	/*-------- container --------*/
	.container620 { width: 440px !important; }
	.container620 { width: 400px !important; }
	.main-button { width: 220px !important; }
	/*-------- secions ----------*/
	.section-img img { width: 320px !important; height: auto !important; }
	.team-img img { width: 100% !important; height: auto !important; }
}

@media only screen and (max-width: 479px) {
	/*------ top header ------ */
	.main-header { font-size: 18px !important; }
	.main-section-header { font-size: 26px !important; }
	/* ====== divider ====== */
	.divider img { width: 280px !important; }
	/*-------- container --------*/
	.container620 { width: 280px !important; }
	.container620 { width: 280px !important; }
	.container620 { width: 260px !important; }
	/*-------- secions ----------*/
	.section-img img { width: 280px !important; height: auto !important; }
}
</style>
</head>
<?php
$image = (isset($path_to_image) && $path_to_image)? $path_to_image: base_url("assets/img/logo.png");
$employeeName = (isset($employee_name) && $employee_name)? strtoupper($employee_name): strtoupper("No Employee Name");
$currentDate = (isset($set_date) && $set_date)? $set_date: date("F d, Y");
/*** $effectivityDate = (isset($effectivity_date) && $effectivity_date)? $effectivity_date: date("F d, Y"); ***/
$effectivityDate = date("F d, Y", strtotime("2018-06-15"));
$companyName = (isset($company_name) && $company_name)? $company_name: "GC&C Group of Companies";
?>
<body class="respond" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">
        <tr>
            <td align="center">
                <table border="0" align="center" width="620" cellpadding="0" cellspacing="0" class="container620">
					<tr>
                        <td height="50" style="font-size: 50px; line-height: 50px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table border="0" align="center" width="620" cellpadding="0" cellspacing="0" class="container620">
								<tr>
                                    <td align="center">
										<img src="<?php echo $image; ?>" style="max-width: 235px;">
									</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <!-- end header -->
	<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">
        <tr>
            <td align="center">
                <table border="0" align="center" width="620" cellpadding="0" cellspacing="0" class="container620">
                    <tr>
                        <td align="center">
                            <table border="0" align="center" width="620" cellpadding="0" cellspacing="0" class="container620">
								<tr>
                                    <td align="center" height="70" style="height:70px;">
										<h3 style="font-family: 'Calibri';"><span style="border-bottom: 1px solid #000;">NOTICE TO EXPLAIN</span></h3>
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
                        <td align="center">
                            <table border="0" align="center" width="620" cellpadding="0" cellspacing="0" class="container620">
								<tr>
                                    <td style="font-family: 'Calibri'; font-size: 16px;" width="80">To</td>
									<td style="font-family: 'Calibri'; font-size: 16px;" width="50">:</td>
									<td style="font-family: 'Calibri'; font-size: 16px; font-weight: bold;" width="*"><?php echo $employeeName; ?></td>
                                </tr>
								<tr>
                                    <td style="font-family: 'Calibri'; font-size: 16px;" width="80">From</td>
									<td style="font-family: 'Calibri'; font-size: 16px;" width="50">:</td>
									<td style="font-family: 'Calibri'; font-size: 16px;" width="*">Human Resource Department</td>
                                </tr>
								<tr>
                                    <td style="font-family: 'Calibri'; font-size: 16px;" width="80">Subject</td>
									<td style="font-family: 'Calibri'; font-size: 16px;" width="50">:</td>
									<td style="font-family: 'Calibri'; font-size: 16px; font-weight: bold;" width="*">Unauthorized Absence or Absence without Permission</td>
                                </tr>
								<tr>
                                    <td style="font-family: 'Calibri'; font-size: 16px;" width="80">Date</td>
									<td style="font-family: 'Calibri'; font-size: 16px;" width="50">:</td>
									<td style="font-family: 'Calibri'; font-size: 16px;" width="*"><?php echo $currentDate; ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td>
                    </tr>
					<tr>
                        <td><p style="border-bottom: 1px solid #000; height: 0px; padding-top: 5px; margin-bottom: 5px;">&nbsp;</p></td>
                    </tr>
					<tr>
                        <td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <!-- big image section -->
    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color">
        <tr>
            <td align="center">
                <table border="0" align="center" width="620" cellpadding="0" cellspacing="0" class="container620">
                    <tr>
                        <td height="5" style="font-size: 5px; line-height: 5px;">&nbsp;</td>
                    </tr>
					<tr>
                        <td>
							<p style="font-family: 'Calibri'; font-size: 16px;">Report forwarded to Human Resource Department that you were absent without notification <br>on <?php echo $currentDate; ?>.</p>
                        </td>
                    </tr>
					<tr>
                        <td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td>
                    </tr>
					<tr>
                        <td>
							<p style="font-family: 'Calibri'; font-size: 16px;">Based on the amended policy on Absence Without Permission stated in MEMORANDUM NO. 2018-025 HRD effective <?php echo $effectivityDate; ?>, violation of the said policy is sanctioned as follows:</p>
						</td>
                    </tr>
					<tr>
                        <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
                    </tr>
					<tr>
						<td align="center">
							<table border="1" width="70%" cellpadding="5" cellspacing="0" bgcolor="ffffff" class="bg_color" style="border-collapse: collapse;">
								<thead>
								<tr style="border: 1px solid #000000ad;">
									<th style="border: 1px solid #000000ad; padding: 0px 5px;">Frequency</th>
									<th style="border: 1px solid #000000ad; padding: 0px 5px;">Sanction/Penalty</th>
								</tr>
								</thead>
								<tbody>
									<tr style="border: 1px solid #000000ad;">
										<td style="border: 1px solid #000000ad; padding: 0px 5px;">1<sup>st</sup> until 3<sup>rd</sup> Offense</td>
										<td style="border: 1px solid #000000ad; padding: 0px 5px;">Written Warning</td>
									</tr>
									<tr style="border: 1px solid #000000ad;">
										<td style="border: 1px solid #000000ad; padding: 0px 5px;">4<sup>th</sup> Offense</td>
										<td style="border: 1px solid #000000ad; padding: 0px 5px;">1 Day  Suspension Without Pay</td>
									</tr>
									<tr style="border: 1px solid #000000ad;">
										<td style="border: 1px solid #000000ad; padding: 0px 5px;">5<sup>th</sup> Offense</td>
										<td style="border: 1px solid #000000ad; padding: 0px 5px;">2 Days Suspension Without Pay</td>
									</tr>
									<tr style="border: 1px solid #000000ad;">
										<td style="border: 1px solid #000000ad; padding: 0px 5px;">6<sup>th</sup> Offense</td>
										<td style="border: 1px solid #000000ad; padding: 0px 5px;">3 Days Suspension Without Pay</td>
									</tr>
									<tr style="border: 1px solid #000000ad;">
										<td style="border: 1px solid #000000ad; padding: 0px 5px;">7<sup>th</sup> Offense</td>
										<td style="border: 1px solid #000000ad; padding: 0px 5px;">4 Days Suspension Without Pay</td>
									</tr>
									<tr style="border: 1px solid #000000ad;">
										<td style="border: 1px solid #000000ad; padding: 0px 5px;">8<sup>th</sup> Offense</td>
										<td style="border: 1px solid #000000ad; padding: 0px 5px;">Dismissal</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
                    <tr>
                        <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
                    </tr>
					<tr>
                        <td>
							<p style="font-family: 'Calibri'; font-size: 16px;">You are hereby required to give an explanation within seventy two (72) hours as to why disciplinary actions should not be taken against you. 
							Should you fail to give an explanation with the period stipulated, it shall be deemed that you don’t have a reasonable explanation to offer and as such, 
							<?php echo $companyName; ?> shall proceed to take disciplinary action deemed necessary and appropriate.</p>
						</td>
                    </tr>
                </table>
            </td>
        </tr>
		<tr>
            <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
        </tr>
		<tr>
            <td align="center">
				<table border="0" align="center" width="620" cellpadding="0" cellspacing="0" class="container620">
					<tr>
						<td>
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td style="font-family: 'Times New Roman'; font-size: 16px;">Issued By:</td>
										<td style="font-family: 'Times New Roman'; font-size: 16px;">Noted By:</td>
									</tr>
									<tr>
										<td height="30" style="font-size: 30px; line-height: 30px;">&nbsp;</td>
									</tr>
									<tr>
										<td style="font-family: 'Times New Roman'; font-size: 16px; font-weight: bold;">APRIL MAE C. PABLO</td>
										<td style="font-family: 'Times New Roman'; font-size: 16px; font-weight: bold;">JESUS FERDINAND B. NEMENZO, MAEd</td>
									</tr>
									<tr>
										<td style="font-family: 'Times New Roman'; font-size: 16px;">HR Generalist</td>
										<td style="font-family: 'Times New Roman'; font-size: 16px;">Human Resources Manager</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</table>
			</td>
        </tr>
		<tr>
            <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
        </tr>
		<tr>
            <td align="center">
				<table border="0" align="center" width="620" cellpadding="0" cellspacing="0" class="container620">
					<tr>
						<td>
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td width="100" style="font-family: 'Times New Roman'; font-size: 16px;">Received By:</td>
										<td width="200"><span style="border-bottom: 1px solid #000; width: 100%; display: block;">&nbsp;</span></td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td width="100">&nbsp;</td>
										<td width="200" align="center" style="font-family: 'Times New Roman'; font-size: 16px;">(Signature over Printed name)</td>
										<td>&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table width="100%">
								<tbody>
									<tr>
										<td width="100" style="font-family: 'Times New Roman'; font-size: 16px;">Date Received:</td>
										<td width="200"><span style="border-bottom: 1px solid #000; width: 100%; display: block;">&nbsp;</span></td>
										<td>&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</table>
			</td>
        </tr>
		<tr>
            <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
        </tr>
    </table>
    <!-- end section -->
	<!-- footer ====== -->
	<table id="footer-content" border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color">
		<tr>
			<td align="center">
				<table border="0" align="center" width="620" cellpadding="0" cellspacing="0" class="container620">
					<tr>
						<td align="center">
							<table width="100%">
								<tbody>
									<tr>
										<td align="center">
											<p>Carlos Hilado Avenue, Circumferential Road, Brgy. Bata, Bacolod City, Negros Occidental 6100, Philippines</p>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td align="center">
							<table width="100%" cellpadding="0" border="0">
								<tbody>
									<tr>
										<td align="right" width="50%">
											<p style="padding: 0 25px;">T +63.34.441.2409 to 11</p>
										</td>
										<td align="left" width="50%">
											<p style="padding: 0 25px;">F +63.34.441.0693</p>
										</td>
									</tr>
								</tbody>
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
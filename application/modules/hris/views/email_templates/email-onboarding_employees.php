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
    <title>EFORMS V1.0</title>
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
        body { font-family: arial, sans-serif!important; }
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
										<h1 style="font-family: Quicksand, Calibri, sans-serif; color:#343434;">GC&amp;C - HRIS</h1>
                                        <?php if($is_weekly): ?>
                                            <h2 style="font-family: Quicksand, Calibri, sans-serif; margin-top: 15px; color:#343434; text-transform: uppercase;">Hired Employees - Weekly</h2>
                                        <?php else: ?>
                                            <h2 style="font-family: Quicksand, Calibri, sans-serif; margin-top: 15px; color:#343434; text-transform: uppercase;">Hired Employees - Monthly</h2>
                                        <?php endif; ?>
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
			<td align="center">
				<table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
					<tr>
						<td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
					</tr>
					<tr>
						<td align="center">
                        <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
                            <tbody>
                                <tr>
                                    <td align="left" style="color: #343434; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px;"class="main-header">
                                        <p style="line-height: 35px">Good day!</p>
                                    </td>
                                </tr>
                                <?php if(isset($data, $count) && $data && $count > 0): ?>
                                <tr>
                                    <td align="left" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                                        <div style="line-height: 24px">The following <strong style="color: #000000;">Hired Employee(s)</strong> as of <strong style="color: #ff0000;"><?php echo (isset($dateStart) && $dateStart)? date("F d, Y", strtotime($dateStart)): date("F d, Y"); ?></strong> up to <strong style="color: #ff0000;"><?php echo (isset($dateEnd) && $dateEnd)? date("F d, Y", strtotime($dateEnd)): date("F d, Y"); ?></strong>. <br>A total of <strong>(<?php echo $count; ?>) employee(s)</strong> has been hired.</div>
                                    </td>
                                </tr>
                                <tr>
                                <td height="30" style="font-size: 30px; line-height: 30px;">&nbsp;</td>
                                </tr>
                                <?php else: ?>
                                <tr>
                                    <td align="left" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                                        <div style="line-height: 24px"><strong>No Hired Employee(s)</strong> as of <strong style="color: #ff0000;"><?php echo (isset($dateStart) && $dateStart)? date("F d, Y", strtotime($dateStart)): date("F d, Y"); ?></strong> up to <strong style="color: #ff0000;"><?php echo (isset($dateEnd) && $dateEnd)? date("F d, Y", strtotime($dateEnd)): date("F d, Y"); ?></strong>.</div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
						</td>
					</tr>
					<?php if(isset($data, $count) && $data && $count > 0): ?>
					<tr>
						<td align="center">
							<table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
								<col width="95">
								<col width="215">
								<col width="135">
								<col width="135">
								<col width="130">
								<col width="165">
								<col width="105">
								<thead>
									<tr>
										<th align="left" style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px;">ID #</th>
										<th align="left" style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px;">EMPLOYEE NAME</th>
										<th align="center" style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px;">COMPANY</th>
										<th align="left" style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px;">DEPARTMENT</th>
										<th align="left" style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px;">POSITION</th>
										<th align="left" style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px;">DEPT.HEAD</th>
										<th align="right" style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px;">HIRED DATE</th>
									</tr>
								</thead>
								<tbody>
                                    <?php foreach ($data as $key => $employee): ?>
                                        <tr>
                                            <td align="left" width="95" v-align="top" style="vertical-align: text-top; color: #343434; font-size: 14px; font-family: 
                                                'Work Sans', Calibri, sans-serif; line-height: 18px; font-weight: 500;"><?php echo $employee->idno; ?></td>
                                            <td align="left" width="215" v-align="top" style="vertical-align: text-top; color: #343434; font-size: 14px; font-family: 
                                                'Work Sans', Calibri, sans-serif; line-height: 18px; font-weight: 500;"><?php echo $employee->employee_name; ?></td>
                                            <td align="center" width="135" v-align="top" style="vertical-align: text-top; color: #343434; font-size: 14px; font-family: 
                                                'Work Sans', Calibri, sans-serif; line-height: 18px; font-weight: 500;"><?php echo $employee->company; ?></td>  
                                            <td align="left" width="135" v-align="top" style="vertical-align: text-top; color: #343434; font-size: 14px; font-family: 
                                                'Work Sans', Calibri, sans-serif; line-height: 18px; font-weight: 500;"><?php echo $employee->department; ?></td>  
                                            <td align="left" width="130" v-align="top" style="vertical-align: text-top; color: #343434; font-size: 14px; font-family: 
                                                'Work Sans', Calibri, sans-serif; line-height: 18px; font-weight: 500;"><?php echo $employee->position; ?></td>
                                            <td align="left" width="165" v-align="top" style="vertical-align: text-top; color: #343434; font-size: 14px; font-family: 
                                                'Work Sans', Calibri, sans-serif; line-height: 18px; font-weight: 500;"><?php echo $employee->department_head; ?></td>
                                            <td align="right" width="105" v-align="top" style="vertical-align: text-top; color: #343434; font-size: 14px; font-family: 
                                                'Work Sans', Calibri, sans-serif; line-height: 18px; font-weight: 500;"><?php echo $employee->date_start; ?></td>   
                                        </tr>
                                    <?php endforeach; ?>
								</tbody>
							</table>
						</td>
					</tr>

					<?php endif; ?>
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
				<h5 class="color:#343434;"> --THIS IS A SYSTEM GENERATED MESSAGE-- </h5>
			</td>
        </tr>
		<tr>
			<td height="40" style="font-size: 40px; line-height: 40px;">&nbsp;</td>
		</tr>
    </table>
    <!-- end section -->
    <!-- footer ====== -->
    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" style="border-top: 1px solid #e0e0e0;">
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
                                            <span style="color: #333333;">GC&amp;C - HRIS</span>
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
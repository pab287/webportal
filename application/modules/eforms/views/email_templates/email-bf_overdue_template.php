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
                        <td align="center">
                            <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
								<tr>
                                    <td align="center" height="70" style="color: #343434; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; text-align: center;">
										<h1 align="center">GC&amp;C - eForms</h1>
										<h4 align="center" style="margin-top: 15px;">BORROWING FORM - OVERDUE</h4>
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
                    <tbody>
                        <tr>
							<td align="left" style="color: #343434; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px;"class="main-header">
								<p style="line-height: 35px">Good day!</p>
							</td>
						</tr>
						<tr>
							<td align="left" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
								<div style="line-height: 24px">The following <strong>Borrowed Item(s)</strong> has been tagged <strong style="color: #ff0000;">OVERDUE</strong> as of <?php echo (isset($date) && $date)? date("F d, Y", strtotime($date)): date("F d, Y"); ?></div>
							</td>
						</tr>
						<tr>
						<td height="30" style="font-size: 30px; line-height: 30px;">&nbsp;</td>
					</tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td algin="center">
                <table align="center" border="0" width="980" cellpadding="0" cellspacing="0" bgcolor="ffffff">
					<col width="180">
					<col width="450">
					<col width="160">
					<col width="105">
					<col width="85">
                    <thead>
                        <tr class="cleared-items">
                            <td colspan="5">
                                <h4 style="background-color: #efefef; padding: 10px 5px; font-weight: bold; font-family: Quicksand, Calibri, sans-serif; text-align: center; margin-bottom: 10px;">OVERDUE ITEMS</h4>
                            </td>
                        </tr>
                        <tr>
                            <th align="left" style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px; margin-bottom: 10px;">REFERENCE #</th>
                            <th align="left" style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px; margin-bottom: 10px;">ITEM NAME</th>
                            <th align="right" style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px; margin-bottom: 10px;">DATE DUE</th>
                            <th align="center" style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px; margin-bottom: 10px;">QTY</th>
                            <th align="center" style="color: #343434; font-size: 14px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px; margin-bottom: 10px;">DAYS</th>
                        </tr>
                   </thead>
                   <tbody>
                        <?php if(isset($data) && $data): ?>
                        <?php foreach ($data as $key => $value): ?>
                        <tr>
                            <td align="left" width="180" v-align="top" style="vertical-align: text-top; color: #343434; font-size: 14px; font-family: 
                                'Work Sans', Calibri, sans-serif; line-height: 18px; font-weight: 500;">
                                <?php echo $value->reference_no; ?>
                            </td>    
                            <td align="left" width="450" v-align="top" style="vertical-align: text-top; color: #343434; font-size: 14px; font-family: 
                                'Work Sans', Calibri, sans-serif; line-height: 18px; font-weight: 500;">
                                <?php echo (isset($value->asset_name) && trim($value->asset_name))? trim($value->asset_name): "---"; ?>
                            </td>    
                            <td align="right" width="160" v-align="top" style="vertical-align: text-top; color: #343434; font-size: 14px; font-family: 
                                'Work Sans', Calibri, sans-serif; line-height: 18px; font-weight: 500;">
                                <?php echo date("M d, Y", strtotime($value->date_due)); ?>
                            </td>    
                            <td align="center" width="105" v-align="top" style="vertical-align: text-top; color: #343434; font-size: 14px; font-family: 
                                'Work Sans', Calibri, sans-serif; line-height: 18px; font-weight: 500;">
                                <?php echo $value->quantity; ?>
                            </td>    
                            <td align="center" width="85" v-align="top" style="vertical-align: text-top; color: #343434; font-size: 14px; font-family: 
                                'Work Sans', Calibri, sans-serif; line-height: 18px; font-weight: 500;">
                                <?php echo $value->days; ?>
                            </td>    
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
				   </tbody>
				   <tfoot>
                    <tr>
                        <td height="10" style="font-size: 10px; line-height: 10px; border-bottom: 5px solid #efefef;" colspan="5">&nbsp;</td>
                    </tr>
				   </tfoot>
                </table>
            </td>
        </tr>
		<tr>
            <td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td>
        </tr>
        <?php if($count > 0): ?>
		<tr>
            <td align="center">
                <table border="0" align="center" width="980" cellpadding="0" cellspacing="0" class="container980">
                    <tbody>
                        <tr>
							<td align="center" style="color: #343434; font-size: 16px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 0px; line-height: 35px;"class="main-header">
								<p style="line-height: 35px">-- NOTE --</p>
							</td>
						</tr>
						<tr>
							<td align="center" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
								<div style="line-height: 24px">
                                    Currently, there are <strong><?php echo $count; ?></strong> overdue items on the system. Please view the borrowing form overdue tab on the webportal. <br> Thank you
                                </div>
							</td>
						</tr>
						<tr>
						<td height="30" style="font-size: 30px; line-height: 30px;">&nbsp;</td>
					    </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td height="10" style="font-size: 10px; line-height: 10px; border-bottom: 5px solid #efefef;">&nbsp;</td>
                        </tr>
				   </tfoot>
                </table>
            </td>
        </tr>
		<tr>
            <td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td>
        </tr>
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
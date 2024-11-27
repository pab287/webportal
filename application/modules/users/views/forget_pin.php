<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
    <meta name="viewport" content="width=600,initial-scale = 2.3,user-scalable=no">
    
    <link href="https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:300,400,700" rel="stylesheet">

    <title>FORGET PIN</title>

</head>
<body class="respond" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<style>
	table {
		  border-collapse: collapse;
		}
		table td:empty{display:none;}
		table tr:empty{display:none;}
		br {
display: none;
}
	</style>
		<div class="container" style="display:block;width:100%;max-width:480px;white-space:initial;margin: 0 auto;" >
				<table valign="center" stlye="border:none;margin:0px;border-collapse:collapse;padding:0px;width:100%" width="100%" cellpadding="0" cellspacing="0">
					<tbody>
						<tr style="display:block">
							<td valign="middle" style="text-align:center;"><img style="text-align:center; margin: 0 auto; width: 100%; max-width: 50px !important;" src="<?php base_url('assets/logo.png')?>"></td>
						</tr>
                        <?php //if(isset($data)){
                           // foreach ($data as $key => $value){ ?>
						<tr style="display:block">
							<td style="border: none;margin: 0px;padding: 0px 0px 5px;font-family: sans-serif;font-weight: 200;text-align: left;text-decoration: none;color: rgb(97,100,103);font-size: 14px;">Hi <strong><?= strtoupper($name); ?></strong>,</td>
						</tr>
						<tr style="display:block">
							<td style="border: none;margin: 0px;padding: 0px 0px 5px;font-family: sans-serif;font-weight: 200;text-align: left;text-decoration: none;color: rgb(97,100,103);font-size: 14px;">You requested to reset your PIN. Your reset PIN is <strong><?= $reset_pin; ?></strong>.</td>
						</tr>
						<tr style="display:block">
							<td style="border: none;margin: 0px;padding: 0px 0px 5px;font-family: sans-serif;font-weight: 200;text-align: left;text-decoration: none;color: rgb(97,100,103);font-size: 14px;line-height: 20px;">Please do not share this to anyone. Kindly update your pin once you recieve this message.</td>
						</tr>
						<tr style="display:block">
							<td style="padding: 0px 0px 20px; border: none;margin: 0px;font-family: sans-serif;font-weight: 200;text-align: left;text-decoration: none;color: rgb(97,100,103);font-size: 14px;line-height: 20px;">Thank you,</td>
						</tr>
						<tr style="display:block">
							<td style="border: none;margin: 0px;padding: 0px 0px 10px;font-family: sans-serif;font-weight: 200;text-align: left;text-decoration: none;color: rgb(97,100,103);font-size: 14px;line-height: 20px;">GC & C Dev Team</td>
						</tr>
                        <?php //} }?>
					</tbody>
				</table>
		</div>
		</body>
</html>
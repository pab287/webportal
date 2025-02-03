<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
    <meta name="viewport" content="width=600,initial-scale = 2.3,user-scalable=no">
    <link href='https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700' rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Quicksand:300,400,700' rel="stylesheet">
    <title>Two Factor Authentication</title>
    <style type="text/css">
            body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
    }
    .logo-container {
      width: 100%;
      text-align: center;
      margin-bottom: 30px;
    }
    .logo-placeholder {
      font-size: 24px;
      color: #666;
      padding: 2px;
      background: #f5f5f5;
      display: inline-block;
    }
    .greeting {
      font-size: 20px;
      margin-bottom: 20px;
      color: #333;
      text-align: center;
    }
    .employee-name {
      font-weight: bold;
    }
    </style>
</head>
<body>
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
        <td class="logo-container">
            <div class="logo-placeholder"><img src="<?php echo base_url('assets/logo.png') ?>" alt="php logo" width="20%"></div>
        </td>
        </tr>
    </table>
  
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td class="greeting">
        Good day! <span class="employee-name">{Employee Name}</span>,
      </td>
    </tr>
  </table>
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td class="greeting">
      Someone is trying to log in into GC&C with a new device
      </td>
    </tr>
  </table>
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td>Location: </td><td>{location}</td>
        <td>Device: </td><td>{device}</td>
        <td>Browser: </td><td>{browser}</td>
        <td>IP Address: </td><td>{ip_address}</td>
    </tr>
  </table>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            color: #333;
        }
        .verification-code {
            background-color: #f5f5f5;
            padding: 10px;
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .details {
            margin: 20px 0;
        }
        .details-row {
            display: flex;
            margin-bottom: 10px;
        }
        .details-label {
            font-weight: bold;
            width: 120px;
        }
        .warning {
            color: #d32f2f;
            font-style: italic;
        }
        .troubleshooting {
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Hello <?php echo $data['first_name'] ?? $data['employee_name'] ?? 'User'; ?>,</h2>
    
    <p>Someone is trying to log in to your Conyx account with a new device</p>
    
    <p>Please Enter the following code to complete login:</p>
    
    <div class="verification-code"><?php echo $data['key_code']; ?></div>
    
    <p><em>Please note that this code will expire on <?php echo $data['expiry']; ?> PST.</em></p>
    
    <p>If you did not initiate this log-in, you should immediately change your Conyx password to ensure account security. You can contact the IT department <a href="#">here</a>.</p>
    
    <div class="troubleshooting">
        <p><strong>If the code does not work</strong>, please request a new verification code and try these troubleshooting steps:</p>
        <ul>
            <li>Use an incognito browser tab or a different browser</li>
            <li>Clear your browser cache and cookies, and disable any browser add-ons or extensions</li>
        </ul>
    </div>
    
    <p><em>(This is system-generated e-mail please do not reply)</em></p>

    <div class="footer">
        <p>DISCLAIMER: This message (and any attachment hereto) may contain privileged and/or confidential information and is meant solely for the perusal and use of the intended recipient.</p>
        <p>If you are not the addressee of this message, you may not copy, disclose, distribute or disseminate this message (and any attachment hereto) to anyone. Instead, please destroy this message (and any attachment hereto), delete the same from your computer, and notify the sender by reply e-mail.</p>
        <p>Unauthorized access, reproduction, disclosure and circulation of this e-mail and/or any of its attachments or any information contained therein by any unauthorized persons are strictly prohibited and are punishable under R.A. No. 8792, otherwise known as E-Commerce Act, and other related laws.</p>
    </div>
</body>
</html>
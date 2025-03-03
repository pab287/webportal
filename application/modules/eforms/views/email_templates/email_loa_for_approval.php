<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Approval Email</title>
    <style>
        /* Reset styles for email clients */
        body {
            margin: 0;
            padding: 0;
            min-width: 100%;
            font-family: Arial, sans-serif;
            line-height: 1.5;
            background-color: #f6f6f6;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        
        table {
            border-spacing: 0;
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        
        td {
            padding: 0;
            vertical-align: top;
        }
        
        img {
            border: 0;
            -ms-interpolation-mode: bicubic;
        }

        /* Main styles */
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f6f6f6;
            padding-bottom: 40px;
        }
        
        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-spacing: 0;
            font-family: Arial, sans-serif;
            color: #333333;
        }
        
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
        }
        
        .content {
            padding: 30px;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
        }
        
        .leave-details {
            background-color: #f8f9fa;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }
    </style>

    
</head>
<body>
    <div class="wrapper">
        <table class="main" width="100%">
            <!-- Header -->
            
            <!-- Content -->
            <tr>
                <td class="content">
                    <h1 style="margin-top: 0; color: #333333;">Leave of Absence Request Notice</h1>
                    <p>Hi! <?php echo strtoupper($data['supervisor']); ?>,</p>

                    <p>Employee <?php echo $data['employeeDisplayName']?> filed a leave of absence request. Please find the details below:</p>

                    <div class="leave-details">
                        <p style="margin: 5px 0;"><strong>Reference Number:</strong> <?php echo $data['referenceNumber']; ?></p>
                        <p style="margin: 5px 0;"><strong>Nature:</strong> <?php echo $data['nature']; ?></p>
                        <p style="margin: 5px 0;"><strong>Type:</strong> <?php echo $data['leaveType'] ?></p>
                        <p style="margin: 5px 0;"><strong></strong> <?php echo $data['loa_date'] ?></p>
                        <p style="margin: 5px 0;"><strong>Reason:</strong> <?php echo $data['reason']; ?></p>
                        <p style="margin: 5px 0;"><strong>Address:</strong> <?php echo $data['address']; ?></p>
                        <p style="margin: 5px 0;"><strong>Contact No:</strong> <?php echo $data['phone']; ?></p>
                    </div>
                    <p><a href="<?php echo $data['url']; ?>" class="button"> Click here to view the leave request</a></p>

                </td>
            </tr>
            
            <!-- Footer -->
            <tr>
                <td class="footer">
                    <p style="margin: 2px; font-size: 12px; color: #666666; text-align: justify; text-justify: inter-word;">DISCLAIMER: This message (and any attachment hereto) may contain privileged and/or confidential information and is meant solely for the perusal and use of the intended recipient. 
                        If you are not the addressee of this message, you may not copy, disclose, distribute or disseminate this message (and any attachment hereto) to anyone. 
                        Instead, please destroy this message (and any attachment hereto), delete the same from your computer, and notify the sender by reply e-mail. 
                        Unauthorized access, reproduction, disclosure and circulation of this e-mail and/or any of its attachments or any information contained therein by any unauthorized persons are strictly prohibited and are punishable under R.A. No. 8792, otherwise known as E-Commerce Act, and other related laws.
                    </p><br/>
                    <p style="margin: 0; font-size: 12px; color: #666666; ">
                        <strong>This is an automated message. Please do not reply directly to this email.</strong><br/><br/>
                        © <?php echo date('Y'); ?> All Rights Reserved.
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
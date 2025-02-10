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
        
        .button {
            background-color: #28a745;
            border-radius: 4px;
            color: #ffffff;
            display: inline-block;
            font-size: 16px;
            font-weight: bold;
            line-height: 1.2;
            padding: 12px 30px;
            text-decoration: none;
            text-align: center;
            margin: 20px 0;
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
            <tr>
                <td class="header">
                    <img src="/api/placeholder/200/50" alt="Company Logo" width="200" height="50">
                </td>
            </tr>
            
            <!-- Content -->
            <tr>
                <td class="content">
                    <h1 style="margin-top: 0; color: #333333;">Leave of Absence Approval Notice</h1>
                    
                    <p>Dear <?php echo $name; ?>,</p>
                    
                    <p>Your <?php echo strtolower($type); ?> request has been approved. Please find the details below:</p>
                    
                    <div class="leave-details">
                        <p style="margin: 5px 0;"><strong>Reference Number:</strong> <?php echo $reference_no; ?></p>
                        <p style="margin: 5px 0;"><strong>Nature:</strong> <?php echo $nature; ?></p>
                        <p style="margin: 5px 0;"><strong>Type:</strong> <?php echo $type; ?></p>
                        <p style="margin: 5px 0;"><strong>Date From:</strong> <?php echo date('F d, Y', strtotime($date_from)); ?></p>
                        <p style="margin: 5px 0;"><strong>Date To:</strong> <?php echo date('F d, Y', strtotime($date_to)); ?></p>
                    </div>
                    
                    <p>Please ensure that:</p>
                    <p>• All pending work is properly handed over to your designated backup</p>
                    <p>• Your out-of-office message is set up before your departure</p>
                    <p>• You update your team calendar accordingly</p>
                    
                    <p>If you need to make any changes to these dates or have any questions, please contact the HR team or your immediate supervisor.</p>
                    
                    <p>Best regards,<br>
                    HR Department</p>
                </td>
            </tr>
            
            <!-- Footer -->
            <tr>
                <td class="footer">
                    <p>DISCLAIMER: This message (and any attachment hereto) may contain privileged and/or confidential information and is meant solely for the perusal and use of the intended recipient.</p>
                    <p>If you are not the addressee of this message, you may not copy, disclose, distribute or disseminate this message (and any attachment hereto) to anyone. Instead, please destroy this message (and any attachment hereto), delete the same from your computer, and notify the sender by reply e-mail.</p>
                    <p>Unauthorized access, reproduction, disclosure and circulation of this e-mail and/or any of its attachments or any information contained therein by any unauthorized persons are strictly prohibited and are punishable under R.A. No. 8792, otherwise known as E-Commerce Act, and other related laws.</p>
                    <p style="margin: 0; font-size: 12px; color: #666666;">
                        This is an automated message. Please do not reply directly to this email.<br>
                        © <?php echo date('Y'); ?> All Rights Reserved.
                    </p>
                </td>
            </tr>
            <div class="footer">
                </div>
        </table>
    </div>
</body>
</html>
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
<?php
function get_leave_type($type_value) {
    $leave_types = [
        1 => 'Undertime',
        2 => 'Half Day',
        3 => 'Whole Day',
        4 => 'Custom'
    ];
    
    return isset($leave_types[$type_value]) ? $leave_types[$type_value] : 'Unknown';
}

// Usage
$type_display = get_leave_type($data['type']);

function format_time($time_str) {
    // Extract time portion
    $time_parts = explode(' ', $time_str);
    if (count($time_parts) !== 2) {
        return $time_str; // Return original string if format is invalid
    }
    
    // Create DateTime object
    $time = DateTime::createFromFormat('H:i:s', $time_parts[1]);
    if (!$time instanceof DateTime) {
        return $time_str; // Return original string if parsing fails
    }
    
    return $time->format('h:i A');
}

function display_date_time($data) {
    switch ($data['type']) {
        case 1: // Undertime
            echo '<p style="margin: 5px 0;"><strong>Date:</strong> ' . date('F d, Y', strtotime($data['date_from'])) . '</p>';
            echo '<p style="margin: 5px 0;"><strong>From:</strong> ' . format_time($data['date_from']) . '</p>';
            echo '<p style="margin: 5px 0;"><strong>To:</strong> ' . format_time($data['date_to']) . '</p>';
            break;
            
        case 2: // Half Day
            echo '<p style="margin: 5px 0;"><strong>Date:</strong> ' . date('F d, Y', strtotime($data['date_from'])) . '</p>';
            echo '<p style="margin: 5px 0;"><strong>From:</strong> ' . format_time($data['date_from']) . '</p>';
            echo '<p style="margin: 5px 0;"><strong>To:</strong> ' . format_time($data['date_to']) . '</p>';
            break;
        
        case 3: // Whole Day
            echo '<p style="margin: 5px 0;"><strong>Date:</strong> ' . date('F d, Y', strtotime($data['date_from'])) . '</p>';
            break;
        default: // Custom
            echo '<p style="margin: 5px 0;"><strong>Date From:</strong> ' . date('F d, Y', strtotime($data['date_from'])) . '</p>';
            echo '<p style="margin: 5px 0;"><strong>Date To:</strong> ' . date('F d, Y', strtotime($data['date_to'])) . '</p>';
            break;
    }
}
?>
    <div class="wrapper">
        <table class="main" width="100%">
            <!-- Header -->
            
            <!-- Content -->
            <tr>
                <td class="content">
                    <h1 style="margin-top: 0; color: #333333;">Leave of Absence Approval Notice</h1>
                    
                    <p>Dear <?php echo $data['fullname']; ?>,</p>
                    
                    <p>Your leave request has been approved. Please find the details below:</p>
                    
                    <div class="leave-details">
                        <p style="margin: 5px 0;"><strong>Reference Number:</strong> <?php echo $data['reference_no']; ?></p>
                        <p style="margin: 5px 0;"><strong>Nature:</strong> <?php echo $data['nature']; ?></p>
                        <p style="margin: 5px 0;"><strong>Type:</strong> <?php echo $type_display ?></p>
                        <?php display_date_time($data); ?>
                        <p style="margin: 5px 0;"><strong>Reason:</strong> <?php echo $data['reason']; ?></p>
                        <p style="margin: 5px 0;"><strong>Approved By:</strong> <?php echo $data['approve_by']; ?></p>
                        <p style="margin: 5px 0;"><strong>Remarks:</strong> <?php echo $data['approved_remarks']; ?></p>
                    </div>
                                        
                    <p>If you need to make any changes to these dates or have any questions, please contact <?php echo $data['contact_person']?>.</p>
                    
                    
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
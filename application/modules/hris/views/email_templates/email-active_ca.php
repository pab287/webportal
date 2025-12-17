<?php
$data = $data ?? [];

// helpers to avoid undefined index notices
function val($arr, $key, $default = 'N/A') {
    return isset($arr[$key]) && $arr[$key] !== '' ? htmlspecialchars($arr[$key]) : $default;
}

$workStations = isset($data['work_station']) && is_array($data['work_station'])
    ? implode(', ', $data['work_station'])
    : 'N/A';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee Status Update</title>
    <style>
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f6f8; padding:20px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:6px; overflow:hidden;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background:#1f3c88; color:#ffffff; padding:20px;">
                            <h2 style="margin:0;">Employee Status Notification</h2>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:20px; color:#333;">
                            <p>Good day,</p>

                            <p>
                                Please be informed that the following employee record has been updated with the details below:
                            </p>

                            <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; font-size:14px;">
                                <tr>
                                    <td width="35%" style="border:1px solid #ddd;"><strong>Employee ID No.</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'idno'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>Biometric No.</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'biometricno'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>Employee Status</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'employee_status'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>Work Status</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'work_status'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>Level</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'level'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>Payroll Type</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'payroll_type'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>Work Mode</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'work_mode'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>Department ID</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'department_id'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>Position ID</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'position'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>Date Started</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'date_start'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>Date End</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'date_end'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>Resignation Reason</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'resign_reason'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>Work Station</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo htmlspecialchars($workStations); ?></td>
                                </tr>
                            </table>

                            <p style="margin-top:20px;">
                                If you find any discrepancies in the information above, please coordinate with the HR department immediately.
                            </p>

                            <p>
                                Thank you.
                            </p>

                            <p style="margin-top:30px;">
                                <strong>Human Resources Department</strong><br>
                                <em>This is a system-generated email. Please do not reply.</em>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f0f0f0; padding:10px; text-align:center; font-size:12px; color:#777;">
                            © <?php echo date('Y'); ?> GC & C Group Of Companies
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
    <div class="footer">
        <p>DISCLAIMER: This message (and any attachment hereto) may contain privileged and/or confidential information and is meant solely for the perusal and use of the intended recipient.</p>
        <p>If you are not the addressee of this message, you may not copy, disclose, distribute or disseminate this message (and any attachment hereto) to anyone. Instead, please destroy this message (and any attachment hereto), delete the same from your computer, and notify the sender by reply e-mail.</p>
        <p>Unauthorized access, reproduction, disclosure and circulation of this e-mail and/or any of its attachments or any information contained therein by any unauthorized persons are strictly prohibited and are punishable under R.A. No. 8792, otherwise known as E-Commerce Act, and other related laws.</p>
    </div>
</body>
</html>
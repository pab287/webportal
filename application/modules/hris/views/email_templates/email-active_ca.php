<?php
$data = $data ?? [];
$loans = $loans ?? [];
function val($item, $key, $default = 'N/A') {
    if (is_array($item)) {
        return isset($item[$key]) && $item[$key] !== '' ? htmlspecialchars($item[$key]) : $default;
    } elseif (is_object($item)) {
        return isset($item->$key) && $item->$key !== '' ? htmlspecialchars($item->$key) : $default;
    }
    return $default;
}
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
                <table width="1000" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:6px; overflow:hidden;">
                    
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
                                Please be informed that the following employee record has been set to inactive/updated resignation date:
                            </p>

                            <p>
                                <?php
                                    $date = val($data, 'resignation_effective_date');
                                    if (!empty($date) && $date !== '0000-00-00' && $date !== 'N/A') {
                                        echo 'Effective Resignation Date: ' . date('F d, Y', strtotime($date));
                                    }
                                ?>
                            </p>

                            <table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse; font-size:14px;">
                                <tr>
                                    <td width="35%" style="border:1px solid #ddd;"><strong>EMPLOYEE NAME</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'fullname'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>COMPANY</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'company'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>DEPARTMENT</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'department'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>POSITION</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'position'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong>LEVEL</strong></td>
                                    <td style="border:1px solid #ddd;"><?php echo val($data, 'level'); ?></td>
                                </tr>
                            </table>

                            <?php if (!empty($loans)): ?>
                                <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                                    <p style="margin: 0 0 10px 0; font-weight: bold; color: #856404;">
                                        IMPORTANT: This employee has active/unpaid loans
                                    </p>
                                </div>

                                <h3 style="margin-top: 25px; margin-bottom: 15px; color: #1f3c88;">Active Loans</h3>

                                <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; font-size:13px; table-layout:fixed;">
                                    <thead>
                                        <tr style="background:#1f3c88; color:#ffffff;">
                                            <th style="border:1px solid #1f3c88; text-align:left; width:34%;">Loan Name</th>
                                            <th style="border:1px solid #1f3c88; text-align:right; width:16%;">Loan Amount</th>
                                            <th style="border:1px solid #1f3c88; text-align:right; width:16%;">Amount Paid</th>
                                            <th style="border:1px solid #1f3c88; text-align:right; width:16%;">Balance</th>
                                            <th style="border:1px solid #1f3c88; text-align:left; width:18%;">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $totalAmount = 0;
                                        $totalPaid = 0;
                                        $totalBalance = 0;

                                        foreach ($loans as $loan): 
                                            $amount   = floatval(val($loan, 'amount', 0));
                                            $paid     = floatval(val($loan, 'total_amount_paid', 0));
                                            $active   = intval(val($loan, 'active', 0));
                                            $remarks  = val($loan, 'remarks', '');
                                            $balance  = $amount - $paid;

                                            if ($balance <= 0) {
                                                continue;
                                            }

                                            $loanName = (intval(val($loan, 'loan_code')) === 1)
                                                ? val($loan, 'ref')
                                                : val($loan, 'loan_name');

                                            $totalAmount  += $amount;
                                            $totalPaid    += $paid;
                                            $totalBalance += $balance;

                                            switch ($active) {
                                                case 1: 
                                                    $badgeBg   = "#17a2b8";
                                                    $badgeText = "Active";
                                                    break;
                                                case 2: 
                                                    $badgeBg   = "#28a745";
                                                    $badgeText = "Paid";
                                                    break;
                                                default:
                                                    $badgeBg   = "#ffc107";
                                                    $badgeText = "Suspended";
                                                    break;
                                            }

                                        ?>
                                        <tr>

                                            <td style="border:1px solid #ddd; text-transform:uppercase; white-space:normal; word-wrap:break-word;">
                                                <strong><?php echo htmlspecialchars($loanName); ?></strong>
                                                <br>
                                                <span style="
                                                    display:inline-block;
                                                    padding:3px 8px;
                                                    font-size:11px;
                                                    font-weight:bold;
                                                    color:#ffffff;
                                                    background-color:<?php echo $badgeBg; ?>;
                                                    border-radius:4px;
                                                    margin-top:4px;
                                                ">
                                                    <?php echo $badgeText; ?>
                                                </span>
                                            </td>

                                            <td style="border:1px solid #ddd; text-align:right;">
                                                ₱ <?php echo number_format($amount, 2); ?>
                                            </td>
                                            <td style="border:1px solid #ddd; text-align:right;">
                                                ₱ <?php echo number_format($paid, 2); ?>
                                            </td>
                                            <td style="border:1px solid #ddd; text-align:right; font-weight:bold; color:#dc3545;">
                                                ₱ <?php echo number_format($balance, 2); ?>
                                            </td>
                                            <td style="border:1px solid #ddd; text-transform:uppercase; white-space:normal; word-wrap:break-word;">
                                                <?php echo !empty($remarks) ? htmlspecialchars($remarks) : '-'; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <p style="margin-top: 15px; font-size: 13px; color: #666; font-style: italic;">
                                    Note: Please ensure proper settlement arrangements are made for outstanding loan balances before finalizing the employee's separation.
                                </p>
                            <?php endif; ?>

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
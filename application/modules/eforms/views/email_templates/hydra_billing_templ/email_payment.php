<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Payment Receipt – Bacolod Hydra</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        .body-wrap {
            background-color: #eef3f7;
            font-family: 'DM Sans', sans-serif;
            color: #1a2b3c;
            padding: 40px 16px;
        }

        .email-wrapper {
            max-width: 580px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            text-align: center;
            padding: 32px 0 24px;
        }

        .logo-mark {
            width: 100%;
            max-width: max-content;
            margin: 0 auto;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
        }

        .logo-text {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #0c5ea8;
        }

        /* Card */
        .card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(12, 94, 168, 0.08);
        }

        /* Hero band */
        .hero-band {
            background: linear-gradient(135deg, #0c5ea8 0%, #1a8fe3 100%);
            padding: 32px 40px;
            color: #fff;
        }

        .hero-band .label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            opacity: 0.75;
            margin-bottom: 6px;
        }

        .hero-band h1 {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .hero-band .subtitle {
            margin-top: 6px;
            font-size: 14px;
            opacity: 0.85;
        }

        /* Body */
        .body {
            padding: 36px 40px;
        }

        .greeting {
            font-size: 17px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #1a2b3c;
        }

        .intro {
            font-size: 14.5px;
            line-height: 1.7;
            color: #3f5566;
        }

        /* Amount highlight */
        .amount-box {
            margin: 28px 0;
            background: #f0f7ff;
            border: 1.5px solid #c3dcf7;
            border-radius: 12px;
            padding: 24px 28px;
            gap: 16px;
            width: 100%;
        }

        .amount-box .amount-label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #0c5ea8;
        }

        .amount-box .amount-value {
            font-family: 'DM Mono', monospace;
            font-size: 32px;
            font-weight: 500;
            color: #0c5ea8;
            letter-spacing: -1px;
            text-align: right;
        }

        .amount-box .status-badge {
            background: #d1fae5;
            color: #065f46;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            border-radius: 20px;
            padding: 6px 14px;
            white-space: nowrap;
        }

        /* Detail rows */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }

        .details-table tr td {
            padding: 11px 0;
            font-size: 14px;
            border-bottom: 1px solid #edf2f7;
        }

        .details-table tr:last-child td {
            border-bottom: none;
        }

        .details-table .detail-key {
            color: #7a95ab;
            font-weight: 500;
            width: 45%;
        }

        .details-table .detail-val {
            /* color: #1a2b3c; */
            color: #0c5ea8;
            font-weight: 600;
            text-align: right;
            font-family: 'DM Mono', monospace;
            font-size: 13.5px;
        }

        .details-table .detail-val.ar {
            color: #0c5ea8;
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 1.5px solid #edf2f7;
            margin: 8px 0 24px;
        }

        /* Note */
        .note {
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            border-radius: 0 8px 8px 0;
            padding: 14px 16px;
            font-size: 13px;
            color: #7a5c00;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        /* Sign-off */
        .signoff {
            font-size: 14px;
            color: #3f5566;
            line-height: 1.7;
        }

        .signoff strong {
            display: block;
            margin-top: 8px;
            color: #0c5ea8;
            font-weight: 700;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 28px 16px 8px;
            font-size: 12px;
            color: #8fa8bb;
            line-height: 1.8;
        }

        .footer a {
            color: #0c5ea8;
            text-decoration: none;
        }

        .footer .address {
            margin-top: 8px;
            font-size: 11.5px;
        }
    </style>
</head>

<body>
    <div class="body-wrap">
        <div class="email-wrapper">
            <!-- Logo Header -->
            <div class="header">
                <table class="logo-mark">
                    <tr>
                        <td width="50">
                             <img src="https://conyxph.com/web/assets/images/hydra/hydra_icon.png" alt="Bacolod Hydra" width="50">
                        </td>

                        <td>
                            <span class="logo-text">Bacolod Hydra</span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Main Card -->
            <div class="card">
                <!-- Hero -->
                <div class="hero-band">
                    <div class="label">Acknowledgment Receipt</div>
                    <h1>Payment Confirmed</h1>
                    <div class="subtitle">Your water bill payment has been successfully processed.</div>
                </div>

                <!-- Body -->
                <div class="body">
                    <p class="greeting">Hi <?php echo htmlspecialchars($full_name); ?>,</p>

                    <p class="intro">
                        Thank you for your water bill payment to <strong>Bacolod Hydra</strong>.
                        This email confirms that your payment has been received.
                    </p>

                    <!-- Amount Highlight -->
                    <table class="amount-box">
                       <tr>
                            <td width="25%" class="amount-label">Amount Paid</td>
                            <td wdith="75%" class="amount-value">₱ <?php echo htmlspecialchars($received); ?></td>
                       </tr>
                    </table>

                    <!-- Transaction Details -->
                    <table class="details-table">
                        <tr>
                            <td class="detail-key">Payment Date</td>
                            <td class="detail-val"><?php echo htmlspecialchars($payment_date); ?></td>
                        </tr>

                        <tr>
                            <td class="detail-key">Billing Period</td>
                            <td class="detail-val"><?php echo htmlspecialchars($billing_period); ?></td>
                        </tr>

                        <tr>
                            <td class="detail-key">Billing Reference No.</td>
                            <td class="detail-val"><?php echo htmlspecialchars($billing_ref); ?></td>
                        </tr>

                        <tr>
                            <td class="detail-key">AR No.</td>
                            <td class="detail-val ar"><?php echo htmlspecialchars($ar_no); ?></td>
                        </tr>
                    </table>

                    <!-- <hr class="divider" /> -->

                    <!-- Automated note -->
                    <div class="note">
                        ⚠️ Please do not reply to this email. This is an automated message sent by Bacolod Hydra's billing system.
                    </div>

                    <!-- Sign-off -->
                    <div class="signoff">
                        Thank you for your continued support.
                        <strong>Bacolod Hydra</strong>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <!-- <div class="footer"> -->
                <!-- <p>© <?php //echo date('Y'); ?> Bacolod Hydra. All rights reserved.</p> -->
                <!-- <p>© 2026 Bacolod Hydra. All rights reserved.</p>
                <p class="address">
                Bacolod City, Negros Occidental, Philippines<br/>
                For inquiries, contact our <a href="#">support team</a>.
                </p>
            </div> -->

        </div>
      </div>
</body>
</html>

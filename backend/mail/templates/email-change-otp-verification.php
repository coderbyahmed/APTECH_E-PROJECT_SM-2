<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Change Verification</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f7; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f7;">
        <tr>
            <td align="center" style="padding: 40px 20px;">

                <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%); padding: 32px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;">
                                SOUND Group
                            </h1>
                            <p style="margin: 6px 0 0; color: rgba(255,255,255,0.8); font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 1.5px;">
                                Admin Panel
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">

                            <h2 style="margin: 0 0 8px; color: #1a1a2e; font-size: 20px; font-weight: 700;">
                                Email Change Verification
                            </h2>

                            <p style="margin: 0 0 24px; color: #6b7280; font-size: 15px; line-height: 1.6;">
                                Hello <strong style="color: #1a1a2e;"><?php echo htmlspecialchars($adminName); ?></strong>,
                            </p>

                            <p style="margin: 0 0 24px; color: #6b7280; font-size: 15px; line-height: 1.6;">
                                We received a request to change the email address on your admin account. Use the 4-digit verification code below to confirm your new email address:
                            </p>

                            <p style="margin: 0 0 24px; color: #9ca3af; font-size: 13px; line-height: 1.6;">
                                Requested at: <strong style="color: #4b5563;"><?php echo htmlspecialchars($requestedAt); ?></strong>
                                &nbsp;|&nbsp; This is the only code that will work. Older codes expire immediately when a new one is requested.
                            </p>

                            <!-- OTP Box -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 0 0 24px;">
                                        <div style="background-color: #f8f7ff; border: 2px solid #ede9fe; border-radius: 12px; padding: 20px 0; display: inline-block; min-width: 260px;">
                                            <p style="margin: 0 0 8px; color: #7c3aed; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px;">
                                                Your Verification Code
                                            </p>
                                            <p style="margin: 0; color: #1a1a2e; font-size: 36px; font-weight: 700; letter-spacing: 8px; font-family: 'Courier New', monospace;">
                                                <?php echo htmlspecialchars($otp); ?>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Info Box -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background-color: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 0 8px 8px 0; padding: 16px 20px; margin-bottom: 24px;">
                                        <p style="margin: 0; color: #92400e; font-size: 14px; line-height: 1.5;">
                                            <strong>Important:</strong> This verification code expires in <strong>3 minutes</strong>. Do not share this code with anyone.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 24px 0 0; color: #9ca3af; font-size: 13px; line-height: 1.6;">
                                If you did not request an email change, your account may be at risk. Please contact support immediately.
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 24px 40px; border-top: 1px solid #f3f4f6;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px; text-align: center; line-height: 1.5;">
                                &copy; <?php echo date('Y'); ?> SOUND Group. All rights reserved.<br>
                                This is an automated message. Please do not reply.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>

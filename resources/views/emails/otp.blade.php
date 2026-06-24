<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Code</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f5; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 480px; background-color: #ffffff; border-radius: 8px; padding: 32px;">
                    <tr>
                        <td>
                            <h1 style="margin: 0 0 8px; font-size: 22px; color: #18181b;">
                                @if ($purpose === 'password_reset')
                                    Reset Your Password
                                @else
                                    Verify Your Email
                                @endif
                            </h1>
                            <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6; color: #52525b;">
                                @if ($purpose === 'password_reset')
                                    Use the code below to reset your password. This code expires in 10 minutes.
                                @else
                                    Use the code below to verify your email address. This code expires in 10 minutes.
                                @endif
                            </p>
                            <div style="background-color: #f4f4f5; border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 24px;">
                                <span style="font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #18181b;">{{ $otp }}</span>
                            </div>
                            <p style="margin: 0; font-size: 13px; line-height: 1.6; color: #71717a;">
                                If you did not request this code, you can safely ignore this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

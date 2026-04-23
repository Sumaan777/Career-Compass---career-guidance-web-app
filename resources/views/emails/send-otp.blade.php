<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f8f9fa; padding:30px;">

    <div style="max-width:500px; margin:auto; background:white; padding:25px; border-radius:12px;">
        
        <h2 style="text-align:center; color:#0d6efd;">
            Your Verification Code
        </h2>

        <p style="font-size:16px;">
            Use the following code to verify your email address:
        </p>

        <h1 style="text-align:center; font-size:48px; letter-spacing:6px;">
            {{ $otp }}
        </h1>

        <p style="font-size:15px; color:#6c757d;">
            This code will expire in 10 minutes.
        </p>

        <p style="font-size:16px;">
            Regards,<br>
            <strong>CareerCompass Team</strong>
        </p>

    </div>

</body>
</html>

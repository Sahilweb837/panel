<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome and Verify Your Email</title>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; line-height: 1.6; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #ff5532; margin: 0; font-size: 28px; }
        .credentials { background: #f1f5f9; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .credentials p { margin: 10px 0; font-size: 16px; }
        .btn-container { text-align: center; margin-top: 30px; }
        .btn { display: inline-block; background: #ff5532; color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 8px; font-weight: bold; font-size: 16px; }
        .footer { text-align: center; margin-top: 40px; font-size: 14px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Netcoder ERP!</h1>
        </div>
        <p>Hello <strong>{{ $user->name }}</strong>,</p>
        <p>Your account has been successfully created. Below are your login credentials. Please keep them secure.</p>
        
        <div class="credentials">
            <p><strong>User ID / Email:</strong> {{ $user->email }}</p>
            <p><strong>Password:</strong> {{ $rawPassword }}</p>
        </div>

        <p>Before you can log in, you must verify your email address by clicking the button below:</p>

        <div class="btn-container">
            <a href="{{ $verificationUrl }}" class="btn">Verify My Email</a>
        </div>

        <div class="footer">
            <p>If you did not request this, please ignore this email.</p>
            <p>&copy; {{ date('Y') }} Netcoder ERP. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

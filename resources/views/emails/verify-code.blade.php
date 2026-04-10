<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Verify Your Email — {{ config('app.name') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f4f5; color: #18181b; -webkit-font-smoothing: antialiased; }
        .wrapper { max-width: 560px; margin: 40px auto; padding: 0 16px 40px; }
        .card { background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #b45309 0%, #d97706 100%); padding: 40px 40px 32px; text-align: center; }
        .logo-row { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
        .logo-icon { width: 44px; height: 44px; background: rgba(255,255,255,0.2); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; }
        .logo-text { font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px; }
        .header-title { font-size: 26px; font-weight: 800; color: #ffffff; line-height: 1.2; margin-bottom: 8px; }
        .header-sub { font-size: 14px; color: rgba(255,255,255,0.85); }
        .body { padding: 36px 40px; }
        .greeting { font-size: 16px; color: #3f3f46; margin-bottom: 16px; }
        .message { font-size: 15px; color: #52525b; line-height: 1.7; margin-bottom: 32px; }
        .otp-label { font-size: 11px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #a1a1aa; text-align: center; margin-bottom: 14px; }
        .otp-wrapper { display: flex; justify-content: center; gap: 10px; margin-bottom: 12px; }
        .otp-digit { display: inline-block; width: 56px; height: 64px; background: linear-gradient(145deg, #fffbeb, #fef3c7); border: 2px solid #fcd34d; border-radius: 14px; font-size: 32px; font-weight: 800; color: #b45309; text-align: center; line-height: 60px; letter-spacing: -1px; }
        .expiry-note { text-align: center; font-size: 13px; color: #a1a1aa; margin-bottom: 32px; }
        .expiry-note strong { color: #ef4444; }
        .divider { height: 1px; background: #f4f4f5; margin: 0 0 28px; }
        .security { background: #f9fafb; border-radius: 12px; padding: 16px 20px; font-size: 13px; color: #71717a; line-height: 1.6; border: 1px solid #e4e4e7; }
        .security strong { color: #3f3f46; }
        .footer { padding: 24px 40px; background: #fafafa; border-top: 1px solid #f4f4f5; text-align: center; }
        .footer-brand { font-size: 13px; font-weight: 700; color: #b45309; letter-spacing: 0.5px; margin-bottom: 6px; }
        .footer-text { font-size: 12px; color: #a1a1aa; line-height: 1.6; }
        @media only screen and (max-width: 480px) {
            .wrapper { margin: 0 auto; padding: 0 8px 24px; }
            .card { border-radius: 16px; }
            .header { padding: 28px 20px 24px; }
            .header-title { font-size: 22px; }
            .header-sub { font-size: 13px; }
            .logo-text { font-size: 19px; }
            .logo-icon { width: 36px; height: 36px; }
            .body { padding: 24px 20px; }
            .greeting { font-size: 15px; }
            .message { font-size: 14px; margin-bottom: 24px; }
            .otp-wrapper { gap: 6px; margin-bottom: 10px; }
            .otp-digit { width: 42px; height: 50px; font-size: 24px; line-height: 46px; border-radius: 10px; }
            .expiry-note { font-size: 12px; margin-bottom: 24px; }
            .security { padding: 14px 16px; font-size: 12px; }
            .footer { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <div class="logo-row">
                    <span class="logo-icon">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </span>
                    <span class="logo-text">{{ config('app.name') }}</span>
                </div>
                <div class="header-title">Verify Your Email</div>
                <div class="header-sub">Bakery Ordering System</div>
            </div>

            <div class="body">
                <p class="greeting">Hi, <strong>{{ $user->name }}</strong>!</p>
                <p class="message">
                    Welcome to <strong>{{ config('app.name') }}</strong>. To finish setting up your account, please verify
                    your email address by entering the 6-digit code below.
                </p>

                <div class="otp-label">Your Verification Code</div>

                <div class="otp-wrapper">
                    @foreach (str_split($code) as $digit)
                        <span class="otp-digit">{{ $digit }}</span>
                    @endforeach
                </div>

                <p class="expiry-note">
                    This code expires in <strong>10 minutes</strong>.
                </p>

                <div class="divider"></div>

                <div class="security">
                    <strong>Didn't create an account?</strong> If you didn't register with {{ config('app.name') }},
                    you can safely ignore this email. Someone may have entered your email address by mistake.
                    No account will be created without verification.
                </div>
            </div>

            <div class="footer">
                <div class="footer-brand">{{ config('app.name') }}</div>
                <div class="footer-text">
                    Bakery Ordering System<br />
                    This is an automated message — please do not reply to this email.
                </div>
            </div>
        </div>

        <p style="text-align:center; font-size:12px; color:#a1a1aa; margin-top:20px;">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</body>
</html>

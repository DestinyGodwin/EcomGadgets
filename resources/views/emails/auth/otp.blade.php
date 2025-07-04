<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verify Your Strapre Account</title>
    <style>
      body {
        margin: 0;
        padding: 0;
        background-color: #f4f4f5;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        color: #1f2937;
      }
      .container {
        max-width: 600px;
        margin: auto;
        background-color: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
      }
      .header {
        background-color: #dc2626;
        padding: 40px 30px;
        text-align: center;
      }
      .header h1 {
        color: #ffffff;
        font-size: 28px;
        margin: 0;
        letter-spacing: 1px;
      }
      .body {
        padding: 40px 30px;
        text-align: center;
      }
      .body h2 {
        font-size: 22px;
        margin-bottom: 10px;
        font-weight: 600;
      }
      .otp-code {
        background-color: #fef2f2;
        display: inline-block;
        padding: 16px 32px;
        font-size: 32px;
        letter-spacing: 10px;
        font-weight: bold;
        color: #dc2626;
        border-radius: 8px;
        margin: 24px 0;
      }
      .info {
        color: #6b7280;
        font-size: 15px;
        line-height: 1.6;
      }
      .button {
        display: inline-block;
        background-color: #dc2626;
        color: #ffffff;
        text-decoration: none;
        padding: 14px 28px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        margin-top: 28px;
      }
      .highlight {
        color: #111827;
        font-weight: 600;
      }
      .footer {
        background-color: #f3f4f6;
        text-align: center;
        font-size: 12px;
        color: #9ca3af;
        padding: 20px 30px;
      }
      .support a {
        color: #dc2626;
        text-decoration: underline;
        font-weight: 500;
      }
      @media (max-width: 600px) {
        .otp-code {
          font-size: 26px;
          letter-spacing: 6px;
          padding: 12px 24px;
        }
        .body {
          padding: 30px 20px;
        }
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="header">
        <h1>Strapre</h1>
      </div>
      <div class="body">
        <h2>One-Time Password (OTP)</h2>
        <p class="info">
          Hello <span class="highlight">{{ ($user->email ?? 'Strapre user') }}</span>,<br /><br />
          To complete your login or secure action, please use the OTP code below. This helps us keep your account safe and verify that it's really you.
        </p>
        <div class="otp-code">{{ $otp }}</div>
        <p class="info">
          This code is valid for the next <strong>10 minutes</strong>. Do not share it with anyone—even if they claim to be from Strapre.
        </p>
        <p class="info" style="margin-top: 36px;">
          If you didn't request this code, please ignore this email or
          <a href="mailto:support@strapre.com" style="color: #dc2626;">contact support</a> immediately.
        </p>
      </div>
      <div class="footer">
        You're receiving this email because you registered at Strapre.<br />
        &copy; {{ date('Y') }} Strapre. All rights reserved.
      </div>
    </div>
  </body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $title }}</title>
  <style>
    body { margin: 0; padding: 0; background-color: #f9fafb; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1f2937; }
    .container { max-width: 620px; margin: auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); }
    .header { background-color: #dc2626; padding: 40px 30px; text-align: center; }
    .header h1 { color: #ffffff; font-size: 28px; margin: 0; }
    .body { padding: 40px 30px; text-align: left; }
    .body h2 { font-size: 22px; font-weight: 700; color: #111827; }
    .body p { font-size: 15px; color: #4b5563; line-height: 1.7; }
    .cta-button { background-color: #dc2626; color: #fff; padding: 12px 20px; text-decoration: none; font-weight: bold; border-radius: 8px; display: inline-block; margin-top: 20px; }
    .footer { text-align: center; font-size: 13px; color: #6b7280; padding: 30px 20px; }
    .footer a { color: #dc2626; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>{{ $title }}</h1>
    </div>
    <div class="body">
      <h2>Hello {{ $storeOwner }},</h2>
      <p>{!! $body !!}</p>
      <a href="{{ $reviewUrl }}" class="cta-button">{{ $actionText }}</a>
    </div>
    <div class="footer">
      &copy; {{ now()->year }} Strapre. All rights reserved. <br />
      <a href="https://strapre.com">www.strapre.com</a>
    </div>
  </div>
</body>
</html>

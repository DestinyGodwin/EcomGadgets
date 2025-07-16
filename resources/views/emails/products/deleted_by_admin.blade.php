<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Product Removed</title>
  <style>
    /* Same styles as your provided template */
    body { background-color: #f9fafb; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1f2937; }
    .container { max-width: 620px; margin: auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); }
    .header { background-color: #dc2626; padding: 40px 30px; text-align: center; }
    .header h1 { color: #ffffff; font-size: 26px; margin: 0; }
    .body { padding: 40px 30px; }
    .body h2 { font-size: 22px; font-weight: 700; margin-bottom: 14px; color: #111827; }
    .body p { color: #4b5563; font-size: 15px; line-height: 1.8; margin-bottom: 20px; }
    .highlight { color: #dc2626; font-weight: 600; }
    .cta-button { background-color: #dc2626; color: #ffffff; padding: 14px 24px; font-size: 15px; font-weight: 600; text-decoration: none; border-radius: 8px; display: inline-block; margin: 20px 0; }
    .footer { background-color: #f9fafb; padding: 30px; font-size: 13px; color: #6b7280; text-align: center; }
    .footer a { color: #dc2626; text-decoration: underline; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Product Removed</h1>
    </div>
    <div class="body">
      <h2>Hello {{ $storeOwner }},</h2>
      <p>We wanted to inform you that your product <strong>{{ $productName }}</strong> has been removed by the admin team.</p>
      <p><strong>Reason:</strong> <span class="highlight">{{ $reason }}</span></p>
      <p>If you believe this was a mistake, feel free to reach out to our support team.</p>
      <a href="{{ $ctaUrl }}" class="cta-button">View Your Products</a>
      <p style="margin-top: 30px;">Thanks for your understanding.</p>
    </div>
    <div class="footer">
      &copy; 2025 Strapre. All rights reserved.<br />
      <a href="https://strapre.com">www.strapre.com</a>
    </div>
  </div>
</body>
</html>

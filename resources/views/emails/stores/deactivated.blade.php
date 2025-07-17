<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Store Deactivated</title>
  <style>
    body { margin: 0; padding: 0; background-color: #f9fafb; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1f2937; }
    .container { max-width: 620px; margin: auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); }
    .header { background-color: #dc2626; padding: 40px 30px; text-align: center; }
    .header h1 { color: #ffffff; font-size: 30px; margin: 0; }
    .body { padding: 40px 30px; text-align: left; }
    .body h2 { font-size: 22px; font-weight: 700; margin-bottom: 14px; color: #111827; }
    .body p { color: #4b5563; font-size: 15px; line-height: 1.8; margin-bottom: 20px; }
    .highlight { color: #dc2626; font-weight: 600; }
    .footer { background-color: #f9fafb; padding: 30px; font-size: 13px; color: #6b7280; text-align: center; }
    .footer a { color: #dc2626; text-decoration: underline; }
    @media (max-width: 600px) {
      .body { padding: 25px 20px; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Store Deactivated ⚠️</h1>
    </div>
    <div class="body">
      <h2>Hello, {{ $storeOwner }}!</h2>
      <p>We regret to inform you that your store <span class="highlight">"{{ $store->store_name }}"</span> has been deactivated by our review team.</p>
      <p><strong>Reason:</strong> {{ $reason }}</p>
      <p>Please ensure that your store complies with our community guidelines and submit accurate and complete documentation when required.</p>
      <p>If you believe this was a mistake or need further clarification, kindly contact our support team.</p>

      <p style="margin-top: 30px;">
        📧 Email: <a href="mailto:support@strapre.com" class="highlight">support@strapre.com</a><br />
        🔒 Visit: <strong>strapre.com</strong>
      </p>
    </div>
    <div class="footer">
      &copy; 2025 Strapre. All rights reserved.<br />
      123 Tech Street, Lagos, Nigeria<br />
      <a href="https://strapre.com">www.strapre.com</a>
    </div>
  </div>
</body>
</html>

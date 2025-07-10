<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Store Approved</title>
  <style>
    body { margin: 0; padding: 0; background-color: #f9fafb; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1f2937; }
    .container { max-width: 620px; margin: auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); }
    .header { background-color: #dc2626; padding: 40px 30px; text-align: center; }
    .header h1 { color: #ffffff; font-size: 30px; margin: 0; }
    .body { padding: 40px 30px; text-align: left; }
    .body h2 { font-size: 22px; font-weight: 700; margin-bottom: 14px; color: #111827; }
    .body p { color: #4b5563; font-size: 15px; line-height: 1.8; margin-bottom: 20px; }
    .highlight { color: #dc2626; font-weight: 600; }
    .cta-button { background-color: #dc2626; color: #ffffff; padding: 14px 24px; font-size: 15px; font-weight: 600; text-decoration: none; border-radius: 8px; display: inline-block; margin: 20px 0; }
    .footer { background-color: #f9fafb; padding: 30px; font-size: 13px; color: #6b7280; text-align: center; }
    .footer a { color: #dc2626; text-decoration: underline; }
    @media (max-width: 600px) {
      .body { padding: 25px 20px; }
      .cta-button { width: 90%; text-align: center; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Store Approved ✅</h1>
    </div>
    <div class="body">
      <h2>Congratulations, {{ $storeOwner }}!</h2>
      <p>Your store on <span class="highlight">Strapre</span> has been successfully approved by our team.</p>
      <p>You can now start posting your products and reaching thousands of buyers across Nigeria.</p>
      <div>
        <a href="{{ $ctaUrl }}" class="cta-button">Post Your First Product</a>
      </div>
      <p style="margin-top: 30px;">If you need help uploading products or customizing your store, our support team is always ready to assist.</p>
      <p style="margin-top: 10px;">
        📧 Email us at <a href="mailto:support@strapre.com" class="highlight">support@strapre.com</a><br />
        🔒 Make sure to always log in via <strong>strapre.com</strong>
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

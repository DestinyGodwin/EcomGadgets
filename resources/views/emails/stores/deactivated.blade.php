<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Store Deactivated</title>
    <style>
        body { font-family: sans-serif; background: #f9fafb; color: #1f2937; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 10px; }
        .cta { background: #dc2626; color: white; padding: 12px 20px; border-radius: 6px; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>
<div class="container">
    <h2>Hi {{ $storeOwner }},</h2>
    <p>We regret to inform you that your store <strong>"{{ $store->store_name }}"</strong> has been deactivated.</p>
    <p><strong>Reason:</strong> {{ $reason }}</p>
    <p>If you believe this was a mistake or you need assistance, please <a href="{{ $supportUrl }}">contact support</a>.</p>
    <p>Thank you for your understanding.</p>
</div>
</body>
</html>

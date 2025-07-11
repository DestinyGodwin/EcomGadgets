<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Store Reactivated</title>
    <style>
        body { font-family: sans-serif; background: #f9fafb; color: #1f2937; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 10px; }
        .cta { background: #dc2626; color: white; padding: 12px 20px; border-radius: 6px; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>
<div class="container">
    <h2>Hi {{ $storeOwner }},</h2>
    <p>Your store <strong>"{{ $store->store_name }}"</strong> has been successfully reactivated.</p>
    <p><strong>Admin message:</strong> {{ $message }}</p>
    <a href="{{ $ctaUrl }}" class="cta">Manage Your Store</a>
    <p>We’re glad to have you back on board!</p>
</div>
</body>
</html>

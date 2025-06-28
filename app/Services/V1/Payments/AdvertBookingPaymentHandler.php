<?php

namespace App\Services\V1\Payments;

use App\Models\AdvertBooking;

class AdvertBookingPaymentHandler implements PaymentHandlerInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    
    public function handleSuccessfulPayment(array $meta): void
    {
        AdvertBooking::create([
            'user_id' => $meta['user_id'],
            'store_id' => $meta['store_id'],
            'state_id' => $meta['state_id'],
            'plan_id' => $meta['plan_id'],
            'starts_at' => now(),
            'ends_at' => now()->addDays($meta['duration_days']),
        ]);
    }
}

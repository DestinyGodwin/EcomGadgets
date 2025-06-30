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
            'store_id' => $meta['store_id'],
            'amount'  => $meta['amount'],
            'state_id' => $meta['state_id'],
            'plan_id' => $meta['plan_id'],
            'reference' => $meta['reference'],
            'starts_at' => now(),
            'ends_at' => now()->addDays($meta['duration_days']),
        ]);
    }
}

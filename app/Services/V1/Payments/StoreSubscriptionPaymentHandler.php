<?php

namespace App\Services\V1\Payments;

use Carbon\Carbon;
use App\Models\Store;
use App\Models\StoreSubscription;

class StoreSubscriptionPaymentHandler implements PaymentHandlerInterface
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
        $store = Store::findOrFail($meta['store_id']);

        $currentExpiry = $store->subscription_expires_at;

        if ($currentExpiry && !($currentExpiry instanceof Carbon)) {
            $currentExpiry = Carbon::parse($currentExpiry);
        }

        $startsAt = ($currentExpiry && $currentExpiry->isFuture()) ? $currentExpiry : now();
        $endsAt = $startsAt->copy()->addDays($meta['duration_days']);

        // StoreSubscription::create([
        //     'store_id' => $store->id,
        //     'plan_id' => $meta['plan_id'],
        //     'starts_at' => $startsAt,
        //     'ends_at' => $endsAt,
        // ]);

        StoreSubscription::create([
            'store_id'       => $store->id,
            'plan_id'        => $meta['plan_id'],
            'amount'         => $meta['amount'],
            'transaction_id' => $meta['transaction_id'],
            'reference'      => $meta['reference'],
            'starts_at'      => $startsAt,
            'ends_at'        => $endsAt,
            'status'         => 'active',
        ]);
        $store->update([
            'subscription_expires_at' => $endsAt,
            'is_active' => true, 
        ]);
    }
}

<?php
namespace App\Services\V1\Payments;

use App\Models\AdvertBooking;
use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

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
        DB::transaction(function () use ($meta) {
            // Lock advert bookings for the same state
            $start = Carbon::parse($meta['starts_at'])->startOfDay();
            $end   = Carbon::parse($meta['starts_at'])->addDays($meta['duration_days'])->endOfDay();

            $conflictCount = AdvertBooking::where('state_id', $meta['state_id'])
                ->where('is_dummy', false)
                ->where(function ($query) use ($start, $end) {
                    $query->whereBetween('starts_at', [$start, $end])
                        ->orWhereBetween('ends_at', [$start, $end])
                        ->orWhere(function ($q) use ($start, $end) {
                            $q->where('starts_at', '<=', $start)
                                ->where('ends_at', '>=', $end);
                        });
                })
                ->lockForUpdate()
                ->count();

            if ($conflictCount >= 5) {
                Transaction::where('reference', $meta['reference'])
                    ->update(['status' => 'failed']);

                throw new Exception('No available slots for the advert time window.');
            }

            AdvertBooking::create([
                'store_id'       => $meta['store_id'],
                'state_id'       => $meta['state_id'],
                'plan_id'        => $meta['plan_id'],
                'reference'      => $meta['reference'],
                'transaction_id' => $meta['transaction_id'] ?? null,
                'amount'         => $meta['amount'],
                'starts_at'      => $start,
                'ends_at'        => $end,
                'title'          => $meta['title'] ?? null,
                'link'           => $meta['link'] ?? null,
                'image'          => $meta['image'] ?? null,
            ]);
        });
    }
}

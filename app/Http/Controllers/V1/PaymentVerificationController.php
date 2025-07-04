<?php

namespace App\Http\Controllers\V1;

use Exception;
use App\Models\AdvertPlan;
use App\Models\Transaction;
use App\Models\SubscriptionPlan;
use App\Models\FeaturedProductPlan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Services\V1\Payments\PaymentHandlerInterface;
use App\Services\V1\Payments\AdvertBookingPaymentHandler;
use App\Services\V1\Payments\FeaturedProductPaymentHandler;
use App\Services\V1\Payments\StoreSubscriptionPaymentHandler;

class PaymentVerificationController extends Controller
{
    public function verify($reference)
    {
        $transaction = Transaction::where('reference', $reference)->first();

        if (! $transaction) {
            return response()->json(['error' => 'Transaction not found.'], 404);
        }

        if ($transaction->status === 'success') {
            return response()->json(['message' => 'Transaction already processed and was successful.']);
        }

        $response = Http::withToken(config('services.paystack.secret_key'))
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        if (! $response->ok()) {
            return response()->json(['error' => 'Unable to verify transaction.'], 500);
        }

        $data = $response->json()['data'];

        if ($data['status'] !== 'success') {
            return response()->json(['message' => 'Transaction not successful.']);
        }

        $meta = $data['metadata'] ?? [];
        $planId = $meta['plan_id'] ?? null;
        $type = $meta['payment_type'] ?? null;
        $amount = $data['amount'] / 100;

        // Check amount consistency
        $expectedAmount = $this->getExpectedAmount($type, $planId);
        if ($amount != $expectedAmount) {
            return response()->json(['error' => 'Amount mismatch.'], 400);
        }

        // Update transaction
        $transaction->update(['status' => 'success']);

        // Add extra metadata for handler
        $meta['amount'] = $amount;
        $meta['reference'] = $reference;
        $meta['transaction_id'] = $data['id'];
        $meta['duration_days'] = $this->getDurationDays($type, $planId);

        $handler = $this->getHandler($type);
        if ($handler instanceof PaymentHandlerInterface) {
            $handler->handleSuccessfulPayment($meta);
        }

        return response()->json(['message' => 'Transaction handled successfully.']);
    }

    protected function getExpectedAmount(string $type, string $planId): float
    {
    return match ($type) {
            'SUB' => SubscriptionPlan::findOrFail($planId)->price,
            'FEAT' => FeaturedProductPlan::findOrFail($planId)->price,
            'ADV' => AdvertPlan::findOrFail($planId)->price,
            default => throw new Exception("Invalid payment type: $type"),
        };
    }

    protected function getDurationDays(string $type, string $planId): int
    {
        return match ($type) {
            'SUB' => SubscriptionPlan::findOrFail($planId)->duration_days,
            'FEAT' => FeaturedProductPlan::findOrFail($planId)->duration_days,
            'ADV' => AdvertPlan::findOrFail($planId)->duration_days,
            default => 0,
        };
    }

    protected function getHandler(string $type): ?PaymentHandlerInterface
    {
        return match ($type) {
            'SUB' => new StoreSubscriptionPaymentHandler(),
            'FEAT' => new FeaturedProductPaymentHandler(),
            'ADV' => new AdvertBookingPaymentHandler(),
            default => null,
        };
    }
}

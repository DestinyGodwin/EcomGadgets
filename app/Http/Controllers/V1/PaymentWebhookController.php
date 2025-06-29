<?php

namespace App\Http\Controllers\V1;

use App\Models\AdvertPlan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\FeaturedProductPlan;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\V1\Payments\PaymentHandlerInterface;
use App\Services\V1\Payments\AdvertBookingPaymentHandler;
use App\Services\V1\Payments\FeaturedProductPaymentHandler;
use App\Services\V1\Payments\StoreSubscriptionPaymentHandler;

class PaymentWebhookController extends Controller
{
   public function handle(Request $request)
    {
        $signature = $request->header('X-Paystack-Signature');
        $secret = config('services.paystack.secret_key');
        $computed = hash_hmac('sha512', $request->getContent(), $secret);

        if ($signature !== $computed) {
            Log::warning('Invalid Paystack signature.');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->data;
        $meta = $data['metadata'];
        $reference = $meta['reference'];
        $type = $meta['payment_type'];

        if ($request->event !== 'charge.success') {
            return response()->json(['ignored' => true]);
        }

        $transaction = Transaction::where('reference', $reference)->first();

        if (!$transaction || $transaction->status === 'success') {
            return response()->json(['message' => 'Already handled']);
        }

        // Validate payment amount matches plan
        $expectedAmount = $this->getExpectedAmount($type, $meta['plan_id']);

        if (($data['amount'] / 100) != $expectedAmount) {
            Log::warning('Amount mismatch', [
                'reference' => $reference,
                'expected' => $expectedAmount,
                'received' => $data['amount'] / 100,
            ]);
            return response()->json(['error' => 'Amount mismatch'], 400);
        }

        // Mark as success
        $transaction->update(['status' => 'success']);

        // Execute handler
        $handler = $this->getHandler($type);
        if ($handler instanceof PaymentHandlerInterface) {
            $meta['duration_days'] = $this->getDurationDays($type, $meta['plan_id']);
            $handler->handleSuccessfulPayment($meta);
        }

        return response()->json(['status' => 'success']);
    }

    protected function getExpectedAmount(string $type, string $planId): float
    {
        return match ($type) {
            'SUB' => SubscriptionPlan::findOrFail($planId)->price,
            'FEAT' => FeaturedProductPlan::findOrFail($planId)->price,
            'ADV' => AdvertPlan::findOrFail($planId)->price,
            default => throw new \Exception("Invalid payment type: $type"),
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

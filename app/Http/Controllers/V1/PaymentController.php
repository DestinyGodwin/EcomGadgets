<?php

namespace App\Http\Controllers\V1;

use App\Models\AdvertPlan;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use App\Models\FeaturedProductPlan;
use App\Services\V1\PaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\V1\Advert\AdvertBookingRequest;
use App\Http\Requests\V1\Product\FeaturedProductRequest;
use App\Http\Requests\V1\Stores\StoreSubscriptionRequest;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function subscribe(StoreSubscriptionRequest $request): JsonResponse
    {
        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        $payment = $this->paymentService->initialize([
            'store_id' => $user->store->id,
            'email' => $user->email,
            'amount' =>$plan->price,
            'type' => 'SUB',
            'metadata' => [
                'user_id' => $user->id,
                'store_id' => $request->store_id,
                'plan_id' => $plan->id,
            ],
        ]);

        return response()->json($payment);
    }

    public function featureProduct(FeaturedProductRequest $request): JsonResponse
    {
        $user = $request->user();
        $plan = FeaturedProductPlan::findOrFail($request->plan_id);

        $payment = $this->paymentService->initialize([
            'user_id' => $user->id,
            'email' => $user->email,
            'amount' => $plan->price,
            'type' => 'FEAT',
            'metadata' => [
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'plan_id' => $plan->id,
            ],
        ]);

        return response()->json($payment);
    }

    public function bookAdvert(AdvertBookingRequest $request): JsonResponse
    {
        $user = Auth::user();
        $plan = AdvertPlan::findOrFail($request->plan_id);

        $payment = $this->paymentService->initialize([
            'user_id' => $user->id,
            'email' => $user->email,
            'amount' => $plan->price,
            'type' => 'ADV',
            'metadata' => [
                'user_id' => $user->id,
                'store_id' => $request->store_id,
                'state_id' => $request->state_id,
                'plan_id' => $plan->id,
            ],
        ]);

        return response()->json($payment);
    }
}

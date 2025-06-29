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
        $store = Auth::user()->store;
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        $payment = $this->paymentService->initialize([
            'store_id' => $store->id,
            'email' => $store->email,
            'amount' =>$plan->price,
            'type' => 'SUB',
            'metadata' => [
                'store_id' => $store->id,
                'plan_id' => $plan->id,
            ],
        ]);

        return response()->json($payment);
    }

    public function featureProduct(FeaturedProductRequest $request): JsonResponse
    {
        $store = Auth::user()->store;
        $plan = FeaturedProductPlan::findOrFail($request->plan_id);

        $payment = $this->paymentService->initialize([
            'store_id' => $store->id,
            'email' => $store->email,
            'amount' => $plan->price,
            'type' => 'FEAT',
            'metadata' => [
                'store_id' => $store->id,
                'product_id' => $request->product_id,
                'plan_id' => $plan->id,
            ],
        ]);

        return response()->json($payment);
    }

    public function bookAdvert(AdvertBookingRequest $request): JsonResponse
    {
        $store = Auth::user()->store;
        $plan = AdvertPlan::findOrFail($request->plan_id);

        $payment = $this->paymentService->initialize([
            'store_id' => $store->id,
            'email' => $store->email,
            'amount' => $plan->price,
            'type' => 'ADV',
            'metadata' => [
                'store_id' => $store->id,
                'state_id' => $request->state_id,
                'plan_id' => $plan->id,
            ],
        ]);

        return response()->json($payment);
    }
}

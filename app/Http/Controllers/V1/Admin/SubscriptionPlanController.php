<?php

namespace App\Http\Controllers\V1\Admin;

use App\Models\SubscriptionPlan;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SubscriptionPlanResource;
use App\Http\Requests\V1\Admin\StoreSubscriptionPlanRequest;
use App\Http\Requests\V1\Admin\UpdateSubscriptionPlanRequest;

class SubscriptionPlanController extends Controller
{
     public function index()
    {
         $plans = SubscriptionPlan::orderBy('duration_days')->get();
        return SubscriptionPlanResource::collection($plans);
    }

    public function store(StoreSubscriptionPlanRequest $request)
    {
            $plan = SubscriptionPlan::create($request->validated());
        return  new SubscriptionPlanResource($plan);
    }

    public function show(SubscriptionPlan $subscription_plan)
    {
        return new SubscriptionPlanResource($subscription_plan);
    }

    public function update(UpdateSubscriptionPlanRequest $request, SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->update($request->validated());
        return response()->json([
            'message' => 'Subscription plan updated successfully',
            'plan' => new SubscriptionPlanResource($subscriptionPlan),
        ]);
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();
        return response()->json(['message' => 'Deleted']);
    }
}

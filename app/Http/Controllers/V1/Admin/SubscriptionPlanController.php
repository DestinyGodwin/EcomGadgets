<?php

namespace App\Http\Controllers\V1\Admin;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Admin\StoreSubscriptionPlanRequest;
use App\Http\Requests\V1\Admin\UpdateSubscriptionPlanRequest;

class SubscriptionPlanController extends Controller
{
     public function index()
    {
        return response()->json(SubscriptionPlan::latest()->get());
    }

    public function store(StoreSubscriptionPlanRequest $request)
    {
        $plan = SubscriptionPlan::create($request->validated());
        return response()->json($plan, 201);
    }

    public function show(SubscriptionPlan $subscriptionPlan)
    {
        return response()->json($subscriptionPlan);
    }

    public function update(UpdateSubscriptionPlanRequest $request, SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->update($request->validated());
        return response()->json($subscriptionPlan);
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();
        return response()->json(['message' => 'Deleted']);
    }
}

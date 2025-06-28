<?php

namespace App\Http\Controllers\V1\Admin;

use App\Models\AdvertPlan;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AdvertPlanResource;
use App\Http\Requests\V1\Admin\StoreAdvertPlanRequest;
use App\Http\Requests\V1\Admin\UpdateAdvertPlanRequest;

class AdvertPlanController extends Controller
{
     public function index()
    {
        $plans = AdvertPlan::orderBy('duration_days')->get();
        return AdvertPlanResource::collection($plans);
    }

    public function store(StoreAdvertPlanRequest $request)
    {
        $plan = AdvertPlan::create($request->validated());
        return new AdvertPlanResource($plan);
    }

    public function show(AdvertPlan $advert_plan)
    {
        return new AdvertPlanResource($advert_plan);
    }

    public function update(UpdateAdvertPlanRequest $request, AdvertPlan $advertPlan)
    {
        $advertPlan->update($request->validated());

        return response()->json([
            'message' => 'Advert plan updated successfully',
            'plan' => new AdvertPlanResource($advertPlan),
        ]);
    }

    public function destroy(AdvertPlan $advertPlan)
    {
        $advertPlan->delete();
        return response()->json(['message' => 'Deleted']);
    }
}

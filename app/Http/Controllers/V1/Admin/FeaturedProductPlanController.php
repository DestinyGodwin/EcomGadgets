<?php

namespace App\Http\Controllers\V1\Admin;

use App\Models\FeaturedProductPlan;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\FeaturedProductPlanResource;
use App\Http\Requests\V1\Admin\StoreFeaturedProductPlanRequest;
use App\Http\Requests\V1\Admin\UpdateFeaturedProductPlanRequest;

class FeaturedProductPlanController extends Controller
{
    public function index()
    {
        $plans = FeaturedProductPlan::orderBy('duration_days')->get();
        return FeaturedProductPlanResource::collection($plans);
    }

    public function store(StoreFeaturedProductPlanRequest $request)
    {
        $plan = FeaturedProductPlan::create($request->validated());
        return new FeaturedProductPlanResource($plan);
    }

    public function show(FeaturedProductPlan $featured_product_plan)
    {
        return new FeaturedProductPlanResource($featured_product_plan);
    }

    public function update(UpdateFeaturedProductPlanRequest $request, FeaturedProductPlan $featuredProductPlan)
    {
        $featuredProductPlan->update($request->validated());

        return response()->json([
            'message' => 'Featured product plan updated successfully',
            'plan' => new FeaturedProductPlanResource($featuredProductPlan),
        ]);
    }

    public function destroy(FeaturedProductPlan $featuredProductPlan)
    {
        $featuredProductPlan->delete();
        return response()->json(['message' => 'Deleted']);
    }
}

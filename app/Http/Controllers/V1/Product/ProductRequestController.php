<?php

namespace App\Services\V1\Product;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Product\StoreProductRequestRequest;

class ProductRequestController extends Controller
{
   public function __construct(protected ProductRequestService $service) {}

    public function store(StoreProductRequestRequest $request): JsonResponse
    {
        $productRequest = $this->service->create($request->validated());

        return response()->json([
            'message' => 'Product request submitted successfully.',
            'data' => $productRequest,
        ], 201);
    }
}
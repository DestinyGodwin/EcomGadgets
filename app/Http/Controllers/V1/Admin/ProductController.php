<?php

namespace App\Http\Controllers\V1\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\V1\Product\ProductService;
use App\Http\Resources\V1\Product\ProductResource;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService, )
    {
        $this->productService = $productService;
    }

    public function index()
    {
        return ProductResource::collection($this->productService->all());
    }

    public function show(string $product)
    {
        return new ProductResource($this->productService->findOne($product));
    }

    public function filter(Request $request)
    {
        return ProductResource::collection(
            $this->productService->filter($request->only('state_id', 'lga_id', 'is_featured'))
        );
    }

}

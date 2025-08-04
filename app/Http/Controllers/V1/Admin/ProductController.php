<?php

namespace App\Http\Controllers\V1\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Services\V1\Product\ProductService;
use App\Http\Resources\V1\Product\ProductResource;
use App\Mail\V1\Products\ProductDeletedByAdminMail;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService )
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

    public function destroy(Request $request, Product $product)
{
    $request->validate(['reason' => ['required', 'string', 'max:5000'],]);
    $reason = $request->input('reason');
    $owner = $product->store->user;
    Mail::to($owner->email)->queue(new ProductDeletedByAdminMail($product, $reason));
    $product->delete();
    return response()->json([
        'message' => 'Product deleted and vendor notified.',
    ]);
}

}

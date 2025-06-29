<?php

namespace App\Http\Controllers\V1\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\V1\Product\ProductService;
use App\Http\Resources\V1\Product\ProductResource;
use App\Http\Requests\V1\Product\StoreProductRequest;
use App\Http\Requests\V1\Product\SearchProductRequest;
use App\Http\Requests\V1\Product\UpdateProductRequest;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->getAll();
        return ProductResource::collection($products);
    }

    public function show(Product $product)
    {
        return new ProductResource($product->load('images', 'store'));
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create($request->validated());
        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product = $this->productService->update($product, $request->validated());
        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $this->productService->delete($product);
        return response()->json(['message' => 'Product deleted successfully.']);
    }

       public function byCategory(Request $request, string $categoryId)
{
    $stateId = $request->query('state_id');
    $lgaId = $request->query('lga_id');

    $products = $this->productService->getByCategory($categoryId, $stateId, $lgaId);

    return ProductResource::collection($products);
}

    public function byBrand(string $brand)
    {
        $products = $this->productService->getByBrand($brand);
        return ProductResource::collection($products);
    }
    public function search(SearchProductRequest $request)
    {
        $filters = $request->validated();
        $products = $this->productService->search($filters);

        return ProductResource::collection($products);
    }
    public function userState()
    {
        $products = $this->productService->getByUserState();
        return ProductResource::collection($products);
    }

    public function userLga()
    {
        $products = $this->productService->getByUserLga();
        return ProductResource::collection($products);
    }
    public function byState(Request $request)
{
    $request->validate([
        'state_id' => 'required|exists:states,id',
    ]);

    $products = $this->productService->getByState($request->state_id);
    return ProductResource::collection($products);
}

public function byLga(Request $request)
{
    $request->validate([
        'lga_id' => 'required|exists:lgas,id',
    ]);

    $products = $this->productService->getByLga($request->lga_id);
    return ProductResource::collection($products);
}

}

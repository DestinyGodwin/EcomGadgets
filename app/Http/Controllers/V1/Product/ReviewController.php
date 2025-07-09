<?php
namespace App\Http\Controllers\V1\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\StoreReviewRequest;
use App\Http\Requests\V1\Products\UpdateReviewRequest;
use App\Http\Resources\V1\Product\ReviewResource;
use App\Services\V1\Product\ReviewService;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService)
    {}

    public function store(StoreReviewRequest $request)
    {
        $review = $this->reviewService->store($request);
        return new ReviewResource($review);
    }

    public function byProduct($productId)
    {
        return ReviewResource::collection(
            $this->reviewService->getByProduct($productId)
        );
    }
    public function update(UpdateReviewRequest $request, $id)
    {
        $review = $this->reviewService->update($id, $request);
        return new ReviewResource($review);
    }

    public function destroy($id)
    {
        $this->reviewService->delete($id);
        return response()->json(['message' => 'Review deleted successfully.']);
    }
}

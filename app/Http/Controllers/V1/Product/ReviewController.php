<?php

namespace App\Http\Controllers\V1\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\V1\Product\ReviewService;
use App\Http\Requests\V1\Products\StoreReviewRequest;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService) {}
    
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
    
        public function destroy($id)
        {
            $this->reviewService->delete($id);
            return response()->json(['message' => 'Review deleted successfully.']);
        }
}

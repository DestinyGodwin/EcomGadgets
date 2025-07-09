<?php

namespace App\Http\Controllers\V1\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

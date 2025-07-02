<?php
namespace App\Http\Controllers\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\V1\AdvertBookingService;
use App\Http\Requests\V1\Advert\UpdateAdvertBookingRequest;

class AdvertBookingController extends Controller
{
  protected AdvertBookingService $advertService;

    public function __construct(AdvertBookingService $advertService)
    {
        $this->advertService = $advertService;
    }

    public function myBookings(): JsonResponse
    {
        $bookings = $this->advertService->getStoreBookings();
        return response()->json(['data' => AdvertBookingResource::collection($bookings)]);
    }

    public function show($id): JsonResponse
    {
        $advert = $this->advertService->getStoreAdvert($id);
        return response()->json(['data' => new AdvertBookingResource($advert)]);
    }

    public function update(UpdateAdvertBookingRequest $request, $id): JsonResponse
    {
        $result = $this->advertService->updateAdvert($request, $id);

        if ($result['error']) {
            return response()->json(['message' => $result['message']], 403);
        }

        return response()->json([
            'message' => 'Updated',
            'data' => new AdvertBookingResource($result['advert'])
        ]);
    }

    public function getDummyAdverts(): JsonResponse
    {
        $ads = $this->advertService->getDummyAdverts();
        return response()->json(['data' => AdvertBookingResource::collection($ads)]);
    }

    public function getAdvertsByState($stateId): JsonResponse
    {
        $ads = $this->advertService->getAdvertsByStateWithFallback($stateId);
        return response()->json(['data' => AdvertBookingResource::collection($ads)]);
    }

    public function getUserStateAdverts(): JsonResponse
    {
        $stateId = auth()->user()->state_id;
        $ads = $this->advertService->getAdvertsByStateWithFallback($stateId);
        return response()->json(['data' => AdvertBookingResource::collection($ads)]);
    }

    public function getAdvertsFromUserState(): JsonResponse
    {
        $stateId = auth()->user()->state_id;
        $ads = $this->advertService->getAdvertsByStateWithFallback($stateId);
        return response()->json(['data' => AdvertBookingResource::collection($ads)]);
    }
}

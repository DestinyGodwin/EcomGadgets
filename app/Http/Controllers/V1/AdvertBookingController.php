<?php
namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Advert\UpdateAdvertBookingRequest;
use App\Models\AdvertBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdvertBookingController extends Controller
{
    public function myBookings()
    {
        $storeId = Auth::user()->store->id;
        return AdvertBooking::where('store_id', $storeId)->latest()->get();
    }

    public function show($id)
    {
        $storeId = Auth::user()->store->id;
        $advert  = AdvertBooking::where('id', $id)->where('store_id', $storeId)->firstOrFail();

        return response()->json($advert);
    }

    public function update(UpdateAdvertBookingRequest $request, $id)
    {
        $store  = Auth::user()->store;
        $advert = AdvertBooking::where('id', $id)->where('store_id', $store->id)->firstOrFail();

        if ($advert->ends_at && now()->gt($advert->ends_at)) {
            return response()->json(['message' => 'This advert has expired and cannot be updated'], 403);
        }

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('advert_images', 'public');
        }

        $advert->update($data);

        return response()->json(['message' => 'Updated', 'data' => $advert]);
    }

    public function getDummyAdverts(): JsonResponse
    {
        $ads = AdvertBooking::where('is_dummy', true)
            ->whereDate('ends_at', '>=', now())
            ->orderBy('starts_at')
            ->get();

        return response()->json(['data' => $ads]);
    }
    public function getAdvertsByState($stateId): JsonResponse
    {
        $activeAds = AdvertBooking::where('state_id', $stateId)
            ->whereDate('ends_at', '>=', now())
            ->where('is_dummy', false)
            ->orderBy('starts_at')
            ->get();

        if ($activeAds->count() < 5) {
            $remaining = 5 - $activeAds->count();
            $dummyAds  = AdvertBooking::where('state_id', $stateId)
                ->where('is_dummy', true)
                ->whereDate('ends_at', '>=', now())
                ->orderBy('starts_at')
                ->take($remaining)
                ->get();

            $ads = $activeAds->concat($dummyAds);
        } else {
            $ads = $activeAds;
        }

        return response()->json(['data' => $ads]);
    }

    public function getUserStateAdverts(): JsonResponse
{
    $user = Auth::user();
    $stateId = $user->state_id;


    $activeAds = AdvertBooking::where('state_id', $stateId)
        ->whereDate('ends_at', '>=', now())
        ->where('is_dummy', false)
        ->orderBy('starts_at')
        ->get();

    if ($activeAds->count() < 5) {
        $remaining = 5 - $activeAds->count();

        $dummyAds = AdvertBooking::where('state_id', $stateId)
            ->where('is_dummy', true)
            ->whereDate('ends_at', '>=', now())
            ->orderBy('starts_at')
            ->take($remaining)
            ->get();

        $ads = $activeAds->concat($dummyAds);
    } else {
        $ads = $activeAds;
    }

    return response()->json(['data' => $ads]);
}


public function getAdvertsFromUserState(): JsonResponse
{
  
    $user = Auth::user();
    $stateId = $user->state_id;
    $adLimit = 5;
    $realAds = AdvertBooking::where('state_id', $stateId)
        ->where('is_dummy', false)
        ->whereDate('ends_at', '>=', now())
        ->orderBy('starts_at')
        ->get();
    if ($realAds->count() < $adLimit) {
        $remaining = $adLimit - $realAds->count();

        $dummyAds = AdvertBooking::where('state_id', $stateId)
            ->where('is_dummy', true)
            ->whereDate('ends_at', '>=', now())
            ->orderBy('starts_at')
            ->take($remaining)
            ->get();

        $ads = $realAds->concat($dummyAds);
    } else {
        $ads = $realAds;
    }
    return response()->json([
        'data' => $ads
    ]);
}
}

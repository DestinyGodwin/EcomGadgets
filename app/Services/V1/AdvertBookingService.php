<?php
namespace App\Services\V1;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\AdvertBooking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdvertBookingService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function prepareBookingMetadata(array $data): array
    {
        $plan  = $data['plan'];
        $start = Carbon::parse($data['starts_at'])->startOfDay();
        $end   = (clone $start)->addDays($plan->duration_days)->endOfDay();

        $conflicts = AdvertBooking::where('state_id', $data['state_id'])
            ->where('is_dummy', false)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('starts_at', [$start, $end])
                    ->orWhereBetween('ends_at', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('starts_at', '<=', $start)
                            ->where('ends_at', '>=', $end);
                    });
            })
            ->count();

        if ($conflicts >= 5) {
            throw ValidationException::withMessages([
                'starts_at' => ['No available slots for this date range in the selected state.'],
            ]);
        }
        $imagePath = null;
        if (! empty($data['image']) && $data['image']->isValid()) {
            $imagePath = $data['image']->storeAs(
                'advert_images',
                Str::uuid() . '.' . $data['image']->getClientOriginalExtension(),
                'public'
            );
        }

        return [
            'store_id'      => $data['store_id'],
            'state_id'      => $data['state_id'],
            'plan_id'       => $plan->id,
            'starts_at'     => $start->toDateString(),
            'duration_days' => $plan->duration_days,
            'title'         => $data['title'],
            'link'          => $data['link'],
            'image'         => $imagePath,
        ];
    }

    public function getStoreBookings()
    {
        $storeId = Auth::user()->store->id;
        return AdvertBooking::where('store_id', $storeId)->latest()->get();
    }

    public function getStoreAdvert($id)
    {
        $storeId = Auth::user()->store->id;
        return AdvertBooking::where('id', $id)->where('store_id', $storeId)->firstOrFail();
    }

    public function updateAdvert($request, $id)
    {
        $store = Auth::user()->store;
        $advert = AdvertBooking::where('id', $id)->where('store_id', $store->id)->firstOrFail();

        if ($advert->ends_at && now()->gt($advert->ends_at)) {
            return ['error' => true, 'message' => 'This advert has expired and cannot be updated'];
        }

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('advert_images', 'public');
        }

        $advert->update($data);
        return ['error' => false, 'advert' => $advert];
    }

    public function getDummyAdverts()
    {
        return AdvertBooking::where('is_dummy', true)
            ->whereDate('ends_at', '>=', now())
            ->orderBy('starts_at')
            ->get();
    }

    public function getAdvertsByStateWithFallback($stateId, $limit = 5)
    {
        $realAds = AdvertBooking::where('state_id', $stateId)
            ->where('is_dummy', false)
            ->whereDate('ends_at', '>=', now())
            ->orderBy('starts_at')
            ->get();

        if ($realAds->count() < $limit) {
            $remaining = $limit - $realAds->count();

            $dummyAds = AdvertBooking::where('state_id', $stateId)
                ->where('is_dummy', true)
                ->whereDate('ends_at', '>=', now())
                ->orderBy('starts_at')
                ->take($remaining)
                ->get();

            return $realAds->concat($dummyAds);
        }

        return $realAds;
    }
}

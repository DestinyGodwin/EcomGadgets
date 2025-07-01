<?php
namespace App\Services\V1;

use App\Models\AdvertBooking;
use Carbon\Carbon;
use Illuminate\Support\Str;
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
}

<?php

namespace App\Http\Controllers\V1\Admin;

use Illuminate\Support\Str;
use App\Models\AdvertBooking;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Admin\DummyAdvertResource;
use App\Http\Requests\V1\Admin\StoreDummyAdvertRequest;
use App\Http\Requests\V1\Admin\UpdateDummyAdvertRequest;

class DummyAdvertBookingController extends Controller
{
    public function index()
    {
        return DummyAdvertResource::collection(
            AdvertBooking::where('is_dummy', true)->latest()->get()
        );
    }

    public function store(StoreDummyAdvertRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->storeAs(
                'advert_images',
                Str::uuid() . '.' . $request->file('image')->getClientOriginalExtension(),
                'public'
            );
        }

        $data['is_dummy'] = true;
        $advert = AdvertBooking::create($data);

        return new DummyAdvertResource($advert);
    }

    public function show(AdvertBooking $dummy_advert_booking)
    {
        abort_unless($dummy_advert_booking->is_dummy, 404);
        return new DummyAdvertResource($dummy_advert_booking);
    }

    public function update(UpdateDummyAdvertRequest $request, AdvertBooking $dummy_advert_booking)
    {
        abort_unless($dummy_advert_booking->is_dummy, 404);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->storeAs(
                'advert_images',
                Str::uuid() . '.' . $request->file('image')->getClientOriginalExtension(),
                'public'
            );
        }

        $dummy_advert_booking->update($data);
        return new DummyAdvertResource($dummy_advert_booking);
    }

    public function destroy(AdvertBooking $dummy_advert_booking)
    {
        abort_unless($dummy_advert_booking->is_dummy, 404);
        $dummy_advert_booking->delete();
        return response()->json(['message' => 'Deleted']);
    }
}

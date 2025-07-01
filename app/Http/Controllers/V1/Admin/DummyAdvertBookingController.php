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

    public function show(AdvertBooking $advertBooking)
    {
        abort_unless($advertBooking->is_dummy, 404);
        return new DummyAdvertResource($advertBooking);
    }

    public function update(UpdateDummyAdvertRequest $request, AdvertBooking $advertBooking)
    {
        abort_unless($advertBooking->is_dummy, 404);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->storeAs(
                'advert_images',
                Str::uuid() . '.' . $request->file('image')->getClientOriginalExtension(),
                'public'
            );
        }

        $advertBooking->update($data);
        return new DummyAdvertResource($advertBooking);
    }

    public function destroy(AdvertBooking $advertBooking)
    {
        abort_unless($advertBooking->is_dummy, 404);
        $advertBooking->delete();
        return response()->json(['message' => 'Deleted']);
    }
}

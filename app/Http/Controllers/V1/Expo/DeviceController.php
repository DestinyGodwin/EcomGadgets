<?php

namespace App\Http\Controllers\V1\Expo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Expo\StoreDeviceRequest;

class DeviceController extends Controller
{
    public function store(StoreDeviceRequest $request)
    {
        $user = $request->user();

        $user->devices()->updateOrCreate(
            ['device_id' => $request->device_id],
            ['expo_token' => $request->token],
        );

        return response()->json(['message' => 'Device registered']);
    }
}

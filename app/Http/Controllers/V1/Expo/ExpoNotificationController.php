<?php

namespace App\Http\Controllers\V1\Expo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use NotificationChannels\Expo\ExpoPushToken;

class ExpoNotificationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'device_id'  => ['required', 'string', 'max:255'],
            'expo_token' => ['required', ExpoPushToken::rule()],
        ]);

        $request->user()->devices()->updateOrCreate(
            ['device_id' => $data['device_id']],
            ['expo_token' => $data['expo_token']]
        );

        return response()->noContent();
    }
}

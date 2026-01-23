<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use NotificationChannels\Expo\ExpoPushToken;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Device extends Model
{
    use HasUuids;

      protected $fillable = [
        'user_id',
        'device_id',
        'expo_token',
    ];

    protected function casts(): array
    {
        return [
            'expo_token' => ExpoPushToken::class,
        ];
    }

}

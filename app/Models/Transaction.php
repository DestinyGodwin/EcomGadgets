<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Transaction extends Model
{
       use HasUuids, SoftDeletes;
       protected $fillable = [
        'reference',
        'user_id',
        'type',
        'amount',
        'status',
        'channel',
        'meta',
    ];
}

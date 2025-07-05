<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
       use HasUuids, SoftDeletes;
       protected $fillable = [
        'reference',
        'store_id',
        'type',
        'amount',
        'status',
        'channel',
        'meta',
    ];
     

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    protected $casts = [
        'meta' => 'array',
        'amount' => 'decimal:2',
   
    ];

}



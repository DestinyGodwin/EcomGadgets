<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvertBooking extends Model
{
     use HasUuids, SoftDeletes;

       protected $fillable = ['store_id', 
       'state_id',
        'plan_id', 'amount', 'starts_at', 'status', 'reference', 'ends_at', 'transaction_id'];

       public function store():BelongsTo
       {
        return $this->belongs(Store::class);
       }

       public function state():BelongsTo
       {
        return $this->belongsTo(State::class);
       }
}

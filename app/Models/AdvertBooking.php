<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AdvertBooking extends Model
{
     use HasUuids, SoftDeletes;

       protected $fillable = ['store_id', 'state_id', 'plan_id', 'amount', 'starts_at', 'status', 'reference', 'ends_at', 'transaction_id'];

}

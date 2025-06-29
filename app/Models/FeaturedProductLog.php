<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FeaturedProductLog extends Model
{
   use HasUuids, SoftDeletes;

   protected $fillable = ['product_id', 'plan_id', 'store_id', 'reference', 'starts_at', 'ends_at'];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FeaturedProduct extends Model {
    use HasUuids;

      protected $fillable = ['product_id', 'plan_id', 'expires_at'];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function plan() {
        return $this->belongsTo(FeaturedProductPlan::class, 'plan_id');
    }
}

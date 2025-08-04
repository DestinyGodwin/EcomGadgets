<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FeaturedProduct extends Model {
    use HasUuids;

      protected $fillable = [
        'product_id',
        'store_id',
        'plan_id',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function product() { return $this->belongsTo(Product::class); }

    public function plan() { return $this->belongsTo(FeaturedProductPlan::class); }

    public function store() { return $this->belongsTo(Store::class); }

}

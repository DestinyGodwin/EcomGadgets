<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FeaturedProductSubscription;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeaturedProductPlan extends Model {
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name', 'price', 'description', 'duration_days',    'max_products', 'refresh_interval_minutes','featured_limit',

    ];

    //     public function featuredProducts() {
    //     return $this->hasMany(FeaturedProduct::class);
    // }
    //   public function subscriptions(): HasMany
    // {
    //     return $this->hasMany(FeaturedProductSubscription::class, 'plan_id');
    // }

    // public function activeSubscriptions(): HasMany
    // {
    //     return $this->subscriptions()->where('is_active', true)
    //         ->where(function ($query) {
    //             $query->whereNull('ends_at')
    //                   ->orWhere('ends_at', '>', now());
    //         });
    // }

    // public static function getPlansOrderedByPrice()
    // {
    //     return self::orderByDesc('price')->get();
    // }

        public function subscriptions()
    {
        return $this->hasMany(FeaturedProductSubscription::class);
    }
}


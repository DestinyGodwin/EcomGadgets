<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeaturedProductSubscription extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'store_id',
        "plan_id",
        'featured_product_plan_id',
        'reference',
        'starts_at',
        'ends_at',
        'is_active',
        'last_refreshed_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'last_refreshed_at' => 'datetime'
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(FeaturedProductPlan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function featuredProducts()
    {
        return $this->hasMany(FeaturedProduct::class);
    }

    public function isActive(): bool
    {
        return $this->is_active && $this->ends_at?->isFuture();
    }

    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            FeaturedProduct::class,
            'featured_product_subscription_id',
            'id',
            'id',
            'product_id'
        );
    }

    public function availableSlots(): int
    {
        $maxProducts = $this->plan->max_products ?? 0;
        $currentCount = $this->featuredProducts()->count();

        return max(0, $maxProducts - $currentCount);
    }
}

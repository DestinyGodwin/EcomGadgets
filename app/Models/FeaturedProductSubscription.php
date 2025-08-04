<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FeaturedProductSubscription extends Model
{
   use HasUuids, SoftDeletes;

    protected $fillable = [
        'store_id',
        'plan_id',
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
        return $this->belongsTo(FeaturedProductPlan::class, 'plan_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'featured_subscription_products', 'subscription_id', 'product_id')
                    ->withTimestamps()
                    ->withPivot('added_at');
    }

    public function isActive(): bool
    {
        return $this->is_active && 
               ($this->ends_at === null || $this->ends_at->isFuture());
    }

    public function needsRefresh(): bool
    {
        if (!$this->last_refreshed_at || !$this->plan) {
            return true;
        }

        return $this->last_refreshed_at
                    ->addHours($this->plan->refresh_interval_hours)
                    ->isPast();
    }

    public function canAddMoreProducts(): bool
    {
        return $this->products()->count() < $this->plan->max_products;
    }

    public function availableSlots(): int
    {
        return $this->plan->max_products - $this->products()->count();
    }
}

<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Store extends Model

{
    use HasUuids, SoftDeletes, HasSlug, HasFactory;

    protected $perPage = 16;
    
    protected $fillable = [
        'store_name',
        'slug',
        'store_description',
        'store_image',
        'phone',
        'email',
        'state_id',
        'lga_id',
        'address',
        'subscription_expires_at',
        'is_active',
    ];

    protected $hidden = ['deleted_at'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(StoreSubscription::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('store_name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function lga(): BelongsTo
    {
        return $this->belongsTo(Lga::class);
    }
}

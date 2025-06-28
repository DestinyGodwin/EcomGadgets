<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Models\Scopes\ActiveStoreScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasUuids, SoftDeletes, HasSlug ;

    protected $fillable = ['category_id', 'name', 'slug', 'description', 'specifications', 'brand', 'price', 'wholesale_price', 'is_featured', 'featured_expires_at'];
    public function store(): BelongsTo{
        return $this->belongsTo(Store::class);
    }
    
     public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

     public function getRouteKeyName()
    {
        return 'slug';
    }

      public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    protected $casts = [
    'specifications' => 'array',
];
protected static function booted(): void
{
    static::addGlobalScope(new ActiveStoreScope);
}
}

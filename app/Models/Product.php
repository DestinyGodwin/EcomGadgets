<?php
namespace App\Models;

use App\Models\Scopes\ActiveStoreScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasUuids, HasSlug;
    protected $perPage  = 16;
    protected $fillable = ['category_id',
        'name', 'slug', 'description',
        'specifications', 'brand', 'price',
        'wholesale_price', 'is_featured',
        'featured_expires_at'];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function getSlugOptions(): SlugOptions
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

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    protected $casts = [
        'specifications'      => 'array',
        'featured_expires_at' => 'datetime',

    ];
    protected static function booted(): void
    {
        static::addGlobalScope(new ActiveStoreScope);
    }
    public function featuredLogs()
    {
        return $this->hasMany(FeaturedProductLog::class);
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }

    public function views()
    {
        return $this->hasMany(ProductView::class);
    }

    public function wishlists()
    {
        return $this->hasMany(ProductWishlist::class);
    }

    public function viewCount()
    {
        return $this->views()->count();
    }

    public function wishlistCount()
    {
        return $this->wishlists()->count();
    }


    public function isWishlistedBy(User $user)
    {
        return $this->wishlists()->where('user_id', $user->id)->exists();
    }


}

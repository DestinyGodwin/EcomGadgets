<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FeaturedSubscriptionProduct extends Model {
    use HasUuids;

    public function products() {
        return $this->belongsToMany( Product::class, 'featured_subscription_products' )
        ->using( FeaturedSubscriptionProduct::class )
        ->withPivot( 'id', 'added_at' )
        ->withTimestamps();
    }

    public function featuredProducts() {
        return $this->hasMany( FeaturedSubscriptionProduct::class, 'subscription_id' );
    }

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'featured_subscription_products';

    protected $fillable = [
        'product_id',
        'subscription_id',
        'added_at',
    ];

}

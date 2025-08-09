<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FeaturedProduct extends Model {
    use HasUuids;

    protected $fillable = [ 'product_id', 'featured_product_subscription_id', 'expires_at' ];

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function subscription() {
        return $this->belongsTo( FeaturedProductSubscription::class, 'featured_product_subscription_id' );
    }
}

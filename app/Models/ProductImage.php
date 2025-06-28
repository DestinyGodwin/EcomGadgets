<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProductImage extends Model
{
    use HasUuids, SoftDeletes;
    
      protected $fillable = [
        'product_id',
        'image_path',
    ];

   

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

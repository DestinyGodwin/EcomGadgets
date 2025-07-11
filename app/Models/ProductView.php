<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class ProductView extends Model
{
   use HasUuids;

    protected $fillable = [
        'product_id', 'user_id', 'ip_address', 'user_agent',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

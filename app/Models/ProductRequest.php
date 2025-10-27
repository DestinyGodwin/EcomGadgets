<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductRequest extends Model
{
    use HasUuids;

     protected $fillable = ['user_id', 'name', 'description', 'image'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

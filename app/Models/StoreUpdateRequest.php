<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreUpdateRequest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'store_id',
        'new_data',
        'status',
        'admin_feedback',
    ];

    protected $casts = [
        'new_data' => 'array',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}

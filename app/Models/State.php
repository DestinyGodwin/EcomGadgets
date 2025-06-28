<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class State extends Model
{
    use HasUuids,  HasSlug;

    protected $fillable = [
        'name' , 'slug'
    ];
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

    public function lgas(): HasMany{
        return $this->hasMany(Lga::class);
    }
}

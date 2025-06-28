<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
       use HasUuids, SoftDeletes, HasSlug, HasFactory;

        protected $fillable = [
      'name' ,'slug' ,
    ];

    public function products(){
        return $this->hasMany(Product::class);
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
}

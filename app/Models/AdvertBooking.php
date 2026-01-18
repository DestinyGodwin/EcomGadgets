<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Image\Enums\Fit;


class AdvertBooking extends Model  implements HasMedia

{
     use HasUuids, SoftDeletes, InteractsWithMedia;

       protected $fillable = ['store_id', 
       'state_id',
        'plan_id', 
        'amount',
         'starts_at', 'link', 'title', 'image', 'status', 'reference', 'is_dummy', 'ends_at', 'transaction_id'];

       public function store():BelongsTo
       {
        return $this->belongs(Store::class);
       }

       public function state():BelongsTo
       {
        return $this->belongsTo(State::class);


       }

         public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('images')
            ->useDisk('public');
            
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->fit(Fit::Crop, 400, 400)
            ->performOnCollections('images')
            ->queued();
    }
}

<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasApiTokens, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'profile_picture',
        'state_id',
        'lga_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'deleted_at',
        'updated_at',
        'otp',
        'otp_expires_at',
        'email_verified_at',
        'role',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
    public function store()
    {
        return $this->hasOne(Store::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function lga(): BelongsTo
    {
        return $this->belongsTo(Lga::class);
    }

    public function isVendor()
    {
        return $this->role === 'vendor';
    }
    public function subscriptions(): HasManyThrough
    {
        return $this->through('store')->has('subscriptions');
    }
    public function products()
    {
        return $this->hasManyThrough(Product::class, Store::class);
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    /**
     * Route notifications for FCM channel.
     * Must return string or array of tokens.
     */
    public function routeNotificationForFcm()
    {
        return $this->devices()->pluck('device_token')->toArray();
    }

     public function conversations()
    {
        return $this->belongsToMany(Conversation::class)
            ->withTimestamps();
    }

   
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

     public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('images')
            ->useDisk('public');
            
    }

//     public function registerMediaConversions(?Media $media = null): void
// {
//     $this
//         ->addMediaConversion('optimized')
//         ->fit(Fit::Max, 400, 400) 
//         ->optimize()             
//         ->performOnCollections('images')
//         ->queued();
// }
public function registerMediaConversions(?Media $media = null): void
{
    $this
        ->addMediaConversion('optimized')
        ->fit(Fit::Max, 400, 400)
        ->optimize()
        ->performOnCollections('profile_pictures')
        ->queued();

    $this
        ->addMediaConversion('thumb')
        ->fit(Fit::Max, 100, 100)
        ->performOnCollections('profile_pictures')
        ->queued();
}

}

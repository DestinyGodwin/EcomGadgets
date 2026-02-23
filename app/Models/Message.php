<?php

namespace App\Models;

use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class Message extends Model implements HasMedia
{

    use HasUuids, InteractsWithMedia;
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
        'type',
    ];
    protected $appends = ['decrypted_body'];

    protected $hidden = ['body'];

    public function setBodyAttribute(?string $value): void
    {
        $this->attributes['body'] = $value
            ? Crypt::encryptString($value)
            : null;
    }

    public function getDecryptedBodyAttribute(): ?string
    {
        if (!$this->body) {
            return null;
        }

        try {
            return Crypt::decryptString($this->body);
        } catch (Throwable $e) {
            Log::warning('Message decryption failed', [
                'message_id' => $this->id,
            ]);

            return null;
        }
    }
      public function registerMediaCollections(): void
    {
        $this->addMediaCollection('chat_media')
            ->useDisk('public')
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
                'video/mp4',
            ]);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media && str_starts_with($media->mime_type, 'image/')) {
            $this
                ->addMediaConversion('optimized')
                ->fit(Fit::Max, 1200, 1200)
                ->optimize()
                ->queued();

            $this
                ->addMediaConversion('thumb')
                ->fit(Fit::Crop, 300, 300)
                ->queued();
        }
    }



    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

   

   
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}

<?php

namespace App\Models;

use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Message extends Model
{

    use HasUuids;
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


    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function media()
    {
        return $this->hasMany(MessageImage::class);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}

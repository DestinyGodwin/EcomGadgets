<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MessageImage extends Model
{
     use HasUuids;
    
      protected $fillable = [
        'message_id',
        'image_path',
    ];

   

    public function product()
    {
        return $this->belongsTo(Message::class);
    }
}

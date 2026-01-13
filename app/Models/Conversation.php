<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasUuids;

     protected $fillable = ['last_message_at'];

     protected $casts = [
    'last_message_at' => 'datetime',
];


    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

   

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}

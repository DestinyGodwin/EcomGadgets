<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Message;

class MessagePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id
            || $message->conversation
                ->users()
                ->where('users.id', $user->id)
                ->exists();
    }
}

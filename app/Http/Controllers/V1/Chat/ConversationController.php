<?php

namespace App\Http\Controllers\V1\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
     public function index(Request $request)
    {
        $userId = $request->user()->id;

        return $request->user()
            ->conversations()
            ->with([
                'users:id,name',
                'lastMessage.sender:id,name',
            ])
            ->orderByDesc('last_message_at')
            ->paginate(20);
    }
}

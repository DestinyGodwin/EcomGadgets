<?php

namespace App\Http\Controllers\V1\Chat;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Chat\ConversationResource;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return ConversationResource::collection(
            $user->conversations()
                ->with([
                    'users:id,first_name,last_name,profile_picture',
                    'lastMessage.sender:id,first_name',
                ])
                ->orderByDesc('last_message_at')
                ->paginate(20)
        );
    }
}

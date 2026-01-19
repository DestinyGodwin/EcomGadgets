<?php

namespace App\Http\Controllers\V1\Chat;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\V1\Chat\ConversationResource;

class ConversationController extends Controller
{
    // public function index(Request $request)
    // {
    //     $user = $request->user();

    //     return ConversationResource::collection(
    //         $user->conversations()
    //             ->with([
    //                 'users:id,first_name,last_name,profile_picture',
    //                 'lastMessage.sender:id,first_name',
    //             ])
    //             ->orderByDesc('last_message_at')
    //             ->paginate(20)
    //     );
    // }

    public function index(Request $request)
{
    $user = $request->user();

    return ConversationResource::collection(
        $user->conversations()
            ->with([
                'users:id,first_name,last_name,profile_picture',
                'users.store', // eager load store
                'lastMessage.sender:id,first_name,last_name,profile_picture',
                'lastMessage.sender.store',
            ])
            ->orderByDesc('last_message_at')
            ->paginate(20)
    );
}


    public function markAsRead(Request $request, Conversation $conversation)
    {
            Gate::authorize('view', $conversation);

        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $request->user()->id)
            ->update(['read_at' => now()]);

        return response()->noContent();
    }

    public function unreadCount(Request $request)
    {
        $count = Message::whereNull('read_at')
            ->where('sender_id', '!=', $request->user()->id)
            ->whereHas('conversation.users', fn ($q) =>
                $q->where('users.id', $request->user()->id)
            )
            ->count();

        return response()->json(['count' => $count]);
    }

    public function conversationUnreadCount(Request $request, Conversation $conversation)
    {
            Gate::authorize('view', $conversation);

        $count = $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $request->user()->id)
            ->count();

        return response()->json(['count' => $count]);
    }
}

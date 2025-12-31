<?php

namespace App\Http\Controllers\V1\Chat;

use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\V1\Chat\ChatService;
use App\Http\Requests\V1\Chat\SendMessageRequest;

class MessageController extends Controller
{
     public function __construct(private readonly ChatService $chat) {}

    public function store(SendMessageRequest $request)
    {
        $user = $request->user();

        $conversation = $this->chat->getOrCreateConversation(
            $user->id,
            $request->receiver_id
        );

        $message = $this->chat->sendMessage(
            $conversation,
            $user->id,
            $request->body,
            $request->file('files', [])
        );

        return response()->json($message->load('media'), 201);
    }

    public function index(Conversation $conversation)
    {
        // $this->authorize('view', $conversation);

        return $conversation->messages()
            ->with('media', 'sender')
            ->latest()
            ->paginate(30);
    }
}

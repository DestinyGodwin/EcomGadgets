<?php

namespace App\Http\Controllers\V1\Chat;

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Services\V1\Chat\ChatService;
use App\Http\Resources\V1\Chat\MessageResource;
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
            $request->file('images', [])
        );

        return new MessageResource($message->load('media', 'sender.store'));
    }

    public function index(Request $request, Conversation $conversation)
    {
        $messages = $conversation->messages()
            ->with(['sender:id,first_name,last_name,profile_picture', 'sender.store'])
            ->latest()
            ->paginate(30);

        return MessageResource::collection($messages)
            ->additional(['auth_user_id' => $request->user()->id]);
    }

    public function betweenUsers(Request $request, User $user)
    {
        $authUser = $request->user();

        $conversation = Conversation::whereHas('users', fn ($q) =>
                $q->whereIn('users.id', [$authUser->id, $user->id])
            )
            ->withCount('users')
            ->having('users_count', 2)
            ->with(['messages.sender.store'])
            ->firstOrFail();

        Gate::authorize('view', $conversation);

        return MessageResource::collection(
            $conversation->messages()
                ->with('sender:id,first_name,last_name,profile_picture', 'sender.store')
                ->latest()
                ->paginate(30)
        );
    }
}

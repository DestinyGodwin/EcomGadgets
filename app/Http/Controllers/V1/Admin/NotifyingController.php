<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\V1\Admin\NotificationService;

class NotifyingController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function send(SendNotificationRequest $request)
    {
        $data = $request->validated();

        $result = $this->notificationService->sendToUsers(
            $data['user_ids'] ?? null,
            $data['subject'],
            $data['message']
        );

        return response()->json([
            'message' => 'Notification sent successfully',
            'details' => $result,
        ]);
    }
}

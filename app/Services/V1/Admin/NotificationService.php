<?php

namespace App\Services\V1\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\V1\Admin\GeneralNotification;

class NotificationService
{
    /**
     * Send a general notification to selected or all users using chunking.
     *
     * @param array|null $userIds  Specific user IDs to target, or null for all users
     * @param string     $subject  Notification subject
     * @param string     $message  Notification message
     * @return array               Status and count of users notified
     */
    public function sendToUsers(?array $userIds, string $subject, string $message): array
    {
        $query = User::query();

        if ($userIds) {
            $query->whereIn('id', $userIds);
        }

        $notifiedCount = 0;

        $query->chunk(100, function ($users) use (&$notifiedCount, $subject, $message) {
            Notification::send($users, new GeneralNotification($subject, $message));
            $notifiedCount += $users->count();
        });

        return [
            'status' => 'success',
            'count'  => $notifiedCount,
        ];
    }
}

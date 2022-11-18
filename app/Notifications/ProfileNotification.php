<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProfileNotification extends Notification implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels, Dispatchable, InteractsWithQueue;

    public function __construct(
        private readonly mixed   $data = null,
        private readonly string  $topic = "",
        private readonly string  $message = ""
    )
    {
    }

    public function join(User $user, string $profileID): bool
    {
        return $user->profile->id == $profileID;
    }

    public function toArray(): array
    {
        return [
            "data" => $this->data,
            "subject" => $this->topic,
            "message" => $this->message
        ];
    }

    public function via(): array
    {
        return ["broadcast"];
    }
}

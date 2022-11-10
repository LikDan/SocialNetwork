<?php

namespace App\Events;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProfileEvent implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels, Dispatchable, InteractsWithQueue;

    public function __construct(
        private readonly Profile $profile,
        private readonly mixed   $data = null,
        private readonly string  $topic = "",
        private readonly string  $message = ""
    )
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('profiles.' . $this->profile->id);
    }

    public function broadcastWith(): array
    {
        return [
            "data" => $this->data,
            "subject" => $this->topic,
            "message" => $this->message
        ];
    }


    public function join(User $user, string $profileID): bool
    {
        return $user->profile->id == $profileID;
    }
}

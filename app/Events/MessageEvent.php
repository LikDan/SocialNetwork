<?php

namespace App\Events;

use App\Http\Resources\Api\v1\MessageResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MessageEvent implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels, Dispatchable, InteractsWithQueue;

    public function __construct(public Message $message)
    {
    }

    private function buildChannelName(): string {
        return 'message.' . $this->message["from_profile_id"] . '.' . $this->message["to_profile_id"];
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel($this->buildChannelName());
    }

    public function broadcastWithK(): array
    {
        return MessageResource::make($this->message)->resolve();
    }

    private function isUserSubscribed(User $user, string $profileID): bool {
        return !is_null($user->profile->subscribedProfiles()->wherePivot("to_profile_id", $profileID)->first());
    }

    public function join(User $user, string $fromProfileID, string $toProfileID): bool
    {
        return $user->profile->id == $toProfileID && $this->isUserSubscribed($user, $fromProfileID);
    }
}

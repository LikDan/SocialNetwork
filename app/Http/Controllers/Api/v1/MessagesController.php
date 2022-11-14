<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\ProfileEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\CreateMessageRequest;
use App\Http\Requests\Api\v1\MessagesIndexRequest;
use App\Http\Resources\Api\v1\MessageResource;
use App\Models\Message;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MessagesController extends Controller
{
    public function index(MessagesIndexRequest $request, string $toProfileID)
    {
        $profile = $request->user()->profile;
        $request = $request->validated();

        $messages = Message::query()
            ->where(fn(Builder $builder) => $builder
                ->where(["from_profile_id" => $profile->id, "to_profile_id" => $toProfileID])
                ->orWhere(["to_profile_id" => $profile->id, "from_profile_id" => $toProfileID])
            )
            ->paginateBy($request, true);

        return MessageResource::collection($messages);
    }

    public function store(CreateMessageRequest $request, string $toProfileID): MessageResource
    {
        $profile = $request->user()->profile;

        $message = $request->validated();
        $message["from_profile_id"] = $profile->id;

        $toProfile = $profile->subscribedProfiles()
            ->wherePivot("to_profile_id", $toProfileID)
            ->firstOrFail();

        $message = $toProfile
            ->messagesToMe()
            ->create($message);

        $message = MessageResource::make($message);
        $profile->notify(new ProfileEvent($message->resolve(), "new_message", "New message"));
        return $message;
    }

    public function destroy(Request $request, string $toProfileID, string $messageID): Application|ResponseFactory|Response
    {
        $profile = $request->user()->profile;

        Message::query()
            ->where("from_profile_id", $profile->id)
            ->where("to_profile_id", $toProfileID)
            ->findOrFail($messageID)
            ->delete();

        $profile->notify(new ProfileEvent($messageID, "delete_message", "Delete message"));
        return response("", 204);
    }
}

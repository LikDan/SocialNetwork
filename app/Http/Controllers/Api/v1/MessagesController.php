<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\ProfileEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\CreateMessageRequest;
use App\Http\Resources\Api\v1\MessageResource;
use App\Models\Message;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MessagesController extends Controller
{
    public function index(Request $request, string $toProfileID)
    {
        $profile = $request->user()->profile;

        $messages = Message::query()
            ->where(["from_profile_id" => $profile->id, "to_profile_id" => $toProfileID])
            ->orWhere(["to_profile_id" => $profile->id, "from_profile_id" => $toProfileID])
            ->orderByDesc("created_at")
            ->paginate();

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
        event(new ProfileEvent($toProfile, $message->resolve(), "new_message", "New message"));

        return MessageResource::make($message);
    }

    public function delete(Request $request, string $toProfileID, string $messageID)
    {
        $profile = $request->user()->profile;

        Message::query()
            ->where("from_profile_id", $profile->id)
            ->where("to_profile_id", $toProfileID)
            ->findOrFail($messageID)
            ->delete();

        return response()->json(["status" => "ok"]);
    }
}

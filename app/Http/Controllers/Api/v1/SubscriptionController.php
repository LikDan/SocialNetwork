<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StatusQueryRequest;
use App\Http\Resources\Api\v1\SubscriptionResource;
use App\Models\Profile;
use App\Models\SubscriptionStatus;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubscriptionController extends Controller
{
    public function subscriptions(StatusQueryRequest $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $status = $request->validated();

        $subscriptions = $user->profile->subscriptions()
            ->when($status->status, fn(Builder $query, string $status) => $query->where("status", $status))
            ->with('toProfile')->paginate();
        return SubscriptionResource::collection($subscriptions);
    }

    public function subscribers(StatusQueryRequest $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $status = $request->validated();

        $subscriptions = $user->profile->subscribers()
            ->when($status->status, fn(Builder $query, string $status) => $query->where("status", $status))
            ->with('fromProfile')->paginate();
        return SubscriptionResource::collection($subscriptions);
    }


    public function subscribe(Request $request, Profile $profile): SubscriptionResource
    {
        $user = $request->user();

        $status = $profile->is_private ? SubscriptionStatus::Pending->value : SubscriptionStatus::Approved->value;
        $subscription = $user->profile->subscriptions()->firstOrCreate(["to_profile_id" => $profile->id], ["status" => $status]);

        return SubscriptionResource::make($subscription);
    }

    public function unsubscribe(Request $request, Profile $profile): JsonResponse
    {
        $user = $request->user();

        $user->profile->subscriptions()->where("to_profile_id", $profile->id)->firstOrFail()->delete();
        return response()->json(["status" => "ok"]);
    }

    public function updateStatus(StatusQueryRequest $request, $id)
    {
        $user = $request->user();
        $status = $request->validated();

        $user->profile->subscribers()->where("to_profile_id", $id)->firstOrFail()->update($status);
        return $user->profile->subscribers()->where("to_profile_id", $id)->firstOrFail();
    }

    private function removeSubscriber(Request $request) {
        $user = $request->user();
    }
}

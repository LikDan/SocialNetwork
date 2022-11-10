<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StatusQueryRequest;
use App\Http\Resources\Api\v1\SubscriptionResource;
use App\Models\Profile;
use App\Models\SubscriptionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubscriptionController extends Controller
{
    public function subscriptions(StatusQueryRequest $request): AnonymousResourceCollection
    {
        $status = $request->validated();

        $subscriptions = $request
            ->user()
            ->profile
            ->subscriptions()
            ->when($status["status"] ?? null, fn(Builder $query, string $status) => $query
                ->where("status", $status)
            )
            ->with('toProfile')->paginate();
        return SubscriptionResource::collection($subscriptions);
    }

    public function subscribers(StatusQueryRequest $request): AnonymousResourceCollection
    {
        $status = $request->validated();

        $subscriptions = $request
            ->user()
            ->profile
            ->subscribers()
            ->when($status["status"] ?? null, fn(Builder $query, string $status) => $query
                ->where("status", $status)
            )
            ->with('fromProfile')->paginate();

        return SubscriptionResource::collection($subscriptions);
    }


    public function subscribe(Request $request, Profile $profile): SubscriptionResource
    {
        $user = $request->user();

        $status = $profile->is_private ? SubscriptionStatus::Pending->value : SubscriptionStatus::Approved->value;
        $subscription = $user
            ->profile
            ->subscriptions()
            ->firstOrCreate(["to_profile_id" => $profile->id], ["status" => $status]);

        return SubscriptionResource::make($subscription);
    }

    public function unsubscribe(Request $request, Profile $profile): JsonResponse
    {
        $request
            ->user()
            ->profile
            ->subscriptions()
            ->where(["to_profile_id" => $profile->id])
            ->firstOrFail()
            ->delete();

        return response()->json(["status" => "ok"]);
    }

    public function updateStatus(StatusQueryRequest $request, string $id)
    {
        $request
            ->user()
            ->profile
            ->subscribers()
            ->where("from_profile_id", $id)
            ->firstOrFail()
            ->update($request["status"]);

        return $request["status"];
    }

    private function removeSubscriber(Request $request, string $id) {
        $request
            ->user()
            ->profile
            ->subscribers()
            ->where(["from_profile_id" => $id])
            ->firstOrFail()
            ->delete();

        return response()->json(["status" => "ok"]);
    }
}

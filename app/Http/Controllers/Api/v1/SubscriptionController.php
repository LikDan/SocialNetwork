<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StatusQueryRequest;
use App\Http\Resources\Api\v1\SubscriptionResource;
use App\Models\Profile;
use App\Models\SubscriptionStatus;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SubscriptionController extends Controller
{
    public function subscriptions(StatusQueryRequest $request): AnonymousResourceCollection
    {
        $subscriptions = $request
            ->user()
            ->profile
            ->subscriptions()
            ->when($request["status"] ?? null, fn(Builder $query, string $status) => $query
                ->where("status", $status)
            )
            ->with('toProfile')->paginateBy($request);
        return SubscriptionResource::collection($subscriptions);
    }

    public function subscribers(StatusQueryRequest $request): AnonymousResourceCollection
    {
        $subscriptions = $request
            ->user()
            ->profile
            ->subscribers()
            ->when($request["status"] ?? null, fn(Builder $query, string $status) => $query
                ->where("status", $status)
            )
            ->with('fromProfile')->paginateBy($request);

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

    public function unsubscribe(Request $request, Profile $profile): Application|ResponseFactory|Response
    {
        $request
            ->user()
            ->profile
            ->subscriptions()
            ->where(["to_profile_id" => $profile->id])
            ->firstOrFail()
            ->delete();

        return response("", 204);
    }

    public function updateStatus(StatusQueryRequest $request, string $id)
    {
        $request
            ->user()
            ->profile
            ->subscribers()
            ->where("from_profile_id", $id)
            ->firstOrFail()
            ->update(["status" => $request["status"]]);

        return response()->json($request["status"]);
    }

    public function removeSubscriber(Request $request, string $id): Application|ResponseFactory|Response
    {
        $request
            ->user()
            ->profile
            ->subscribers()
            ->where(["from_profile_id" => $id])
            ->firstOrFail()
            ->delete();

        return response("", 204);
    }
}

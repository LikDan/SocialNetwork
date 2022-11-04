<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\AvatarRequest;
use App\Http\Requests\Api\v1\ProfilesIndexRequest;
use App\Http\Requests\Api\v1\ProfileUpdateRequest;
use App\Http\Resources\Api\v1\ProfileResource;
use App\Http\Resources\Api\v1\ShortProfileResource;
use App\Models\Profile;
use App\Models\SubscriptionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function addPicture(AvatarRequest $request): JsonResponse
    {
        $user = $request->user();
        $avatar = $request->file("avatar");

        $file = Storage::putFile('avatars', $avatar);
        $user->profile()->update(["picture_path" => $file]);

        return response()->json(["url" => Storage::url($file)]);
    }

    public function updateProfile(ProfileUpdateRequest $request): ProfileResource
    {
        $user = $request->user();
        $profileParams = $request->validated();

        $user->profile()->update($profileParams);


        if (!$profileParams["is_private"])
            $user->profile->subscribers()
                ->where("status", SubscriptionStatus::Pending->value)
                ->update(["status" => SubscriptionStatus::Approved->value]);


        return ProfileResource::make($user->profile);
    }

    public function index(ProfilesIndexRequest $request)
    {
        $profile = $request->user()->profile;

        $perPage = $request->per_page ?? 20;
        $profiles = Profile::query()
            ->whereDoesntHave('subscriptions', fn(Builder $query) => $query
                ->where('from_profile_id', $profile->id)
            )
            ->paginate($perPage)
            ->appends($request->validated());

        return ShortProfileResource::collection($profiles);
    }
}

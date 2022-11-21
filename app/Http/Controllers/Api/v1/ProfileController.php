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
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    public function addPicture(AvatarRequest $request): JsonResponse
    {
        $profile = $request->user()->profile();
        $image = $request->file("avatar");

        $img = Image::make($image->path());
        $profiles = $img->getCore()->getImageProfiles("icc", true);

        $img->getCore()->stripImage();

        if (!empty($profiles))
            $img->getCore()->profileImage("icc", $profiles['icc']);

        $random_hex = bin2hex(random_bytes(18));
        $path = "avatars/" . $random_hex . '.' . $image->getClientOriginalExtension();
        Storage::put($path, $img->stream());

        $profile->update(["picture_path" => $path]);

        return response()->json(["url" => Storage::url($path)]);
    }

    public function update(ProfileUpdateRequest $request, string $profileID): ProfileResource
    {
        $user = $request->user();
        $profileParams = $request->validated();

        $user->profile()->findOrFail($profileID)->update($profileParams);

        if (!$profileParams["is_private"])
            $user
                ->profile
                ->subscribers()
                ->where("status", SubscriptionStatus::Pending->value)
                ->update(["status" => SubscriptionStatus::Approved->value]);


        return ProfileResource::make($user->profile);
    }

    public function index(ProfilesIndexRequest $request)
    {
        $profileId = $request->user()->profile->id;

        $profiles = Profile::query()
            ->where(fn(Builder $builder) => $builder
                ->where("id", "!=", $profileId)
                ->whereDoesntHave('subscriptions', fn(Builder $query) => $query
                    ->where('from_profile_id', $profileId)
                )
            )
            ->paginateBy($request);

        return ShortProfileResource::collection($profiles);
    }

    public function show(Request $request, string $toProfileID)
    {
        $profile = $request->user()->profile;
        if ($toProfileID == "self") return ProfileResource::make($profile);

        $toProfile = Profile::query()
            ->where(fn(Builder $builder) => $builder
                ->where("is_private", false)
                ->orWhereHas('subscribers', fn(Builder $query) => $query
                    ->where('from_profile_id', $profile->id)
                    ->where('status', SubscriptionStatus::Approved->value)
                )
            )
            ->findOrFail($toProfileID);

        return ProfileResource::make($toProfile);
    }
}

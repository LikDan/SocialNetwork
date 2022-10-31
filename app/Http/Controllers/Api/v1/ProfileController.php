<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\ProfilesIndexRequest;
use App\Http\Requests\Api\v1\ProfileUpdateRequest;
use App\Http\Resources\Api\v1\ProfileResource;
use App\Models\Profile;
use App\Models\SubscriptionStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function addPicture(Request $request): JsonResponse
    {
        $user = $request->user();

        $file = Storage::putFile('avatars', $request->avatar);
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

    public function index(ProfilesIndexRequest $request): AnonymousResourceCollection
    {
        $perPage = $request->per_page ?? 20;

        $profiles = Profile::paginate($perPage)->appends($request->validated());
        return ProfileResource::collection($profiles);
    }
}

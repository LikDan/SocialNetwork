<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\ShortProfileResource;
use App\Models\Post;
use App\Models\PostType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LikesController extends Controller
{
    public function index(Request $request, string $profileId, string $postId): AnonymousResourceCollection
    {
        $myProfile = $request->user()->profile;
        $profileId = (int)$profileId;

        $profile = $myProfile->id === $profileId
            ? $myProfile
            : $myProfile->subscribedProfiles()->findOrFail($profileId);

        $likedProfiles = $profile->posts()
            ->when(
                $profile->id !== $myProfile->id,
                fn(Builder $query) => $query->where('type', PostType::Published->value)
            )->findOrFail($postId)
            ->likedProfiles()
            ->paginate();

        return ShortProfileResource::collection($likedProfiles);
    }

    public function toggle(Request $request, string $profileId, string $postId): JsonResponse
    {
        $myProfile = $request->user()->profile;

        $post = Post::availablePosts()->whereProfileId($profileId)->findOrFail($postId);

        $status = (bool) $post->likedProfiles()->toggle($myProfile->id)["attached"];
        return response()->json(["is_liked" => $status]);
    }
}

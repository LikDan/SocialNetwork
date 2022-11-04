<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\PostCreateRequest;
use App\Http\Requests\Api\v1\PostTypeRequest;
use App\Http\Requests\Api\v1\PostUpdateRequest;
use App\Http\Resources\Api\v1\PostResource;
use App\Models\Post;
use App\Models\PostType;
use App\Models\Subscription;
use App\Models\SubscriptionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostsController extends Controller
{
    public function index(PostTypeRequest $request, string $profileId): AnonymousResourceCollection
    {
        $myProfile = $request->user()->profile;
        $type = $request->validated();
        $profileId = (int)$profileId;

        $profileId = $myProfile->id === $profileId
            ? $myProfile
            : $myProfile->subscribedProfiles()->findOrFail($profileId);

        $posts = $profileId->posts()->when(
            $profileId->id !== $myProfile->id,
            fn(Builder $query) => $query->where('type', PostType::Published->value)
        )->when(
            $profileId->id === $myProfile->id && $type["type"] ?? false,
            fn(Builder $query) => $query->where('type', $type["type"])
        )->paginate();

        return PostResource::collection($posts);
    }

    public function feed(Request $request): AnonymousResourceCollection
    {
        $profile = $request->user()->profile;
        $posts = Subscription::select("posts.*")
            ->join('profiles', 'profiles.id', '=', 'subscriptions.to_profile_id')
            ->join("posts", "profiles.id", "=", "posts.profile_id")
            ->where("from_profile_id", $profile["id"])
            ->where("status", SubscriptionStatus::Approved->value)
            ->where("posts.type", PostType::Published->value)
            ->orderBy("posts.created_at")
            ->paginate();
        return PostResource::collection($posts);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function show(Request $request, string $profileId, string $postId): PostResource
    {
        $post = Post::availablePosts()->where("profile_id", $profileId)->findOrFail($postId);

        $post->load('attachments');
        $post->loadCount('likedProfiles');
        $post->loadCount('likedCurrentProfiles');
        return PostResource::make($post);
    }

    public function store(PostCreateRequest $request, string $profileId): PostResource
    {
        $profile = $request->user()->profile()->findOrFail($profileId);
        $postParams = $request->validated();

        $post = $profile->posts()->create($postParams);

        return PostResource::make($post);
    }

    public function update(PostUpdateRequest $request, string $profileId, string $postId): PostResource
    {
        $profile = $request->user()->profile()->findOrFail($profileId);
        $postParams = $request->validated();

        $post = $profile->posts()->findOrFail($postId);

        $post->update($postParams);

        $post->load('attachments');
        return PostResource::make($post->refresh());
    }

    public function delete(Request $request, string $profileId, string $postId): JsonResponse
    {
        $profile = $request->user()->profile()->findOrFail($profileId);
        $profile->posts()->findOrFail($postId)->delete();

        return response()->json(["status" => "ok"]);
    }
}

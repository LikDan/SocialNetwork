<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\PostCreateRequest;
use App\Http\Requests\Api\v1\PostTypeRequest;
use App\Http\Requests\Api\v1\PostUpdateRequest;
use App\Http\Resources\Api\v1\PostResource;
use App\Models\Post;
use App\Models\PostType;
use App\Models\Profile;
use App\Models\SubscriptionStatus;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostsController extends Controller
{
    public function index(PostTypeRequest $request, string $profileId): AnonymousResourceCollection
    {
        $posts = Post::availablePosts()
            ->where(["profile_id" => $profileId])
            ->where(["type" => $request["type"] ?? PostType::Published->value])
            ->with(['attachments', 'owner'])
            ->withCount(['likedProfiles', 'likedCurrentProfiles'])
            ->paginateBy($request->validated());

        return PostResource::collection($posts);
    }

    public function feed(Request $request)
    {
        $profile = $request->user()->profile;
        $posts = Post::query()
            ->rightJoin("subscriptions", "subscriptions.to_profile_id", "posts.profile_id")
            ->where([
                "from_profile_id" => $profile->id,
                "subscriptions.status" => SubscriptionStatus::Approved->value,
                "type" => PostType::Published->value,
            ])
            ->orderBy("created_at")
            ->with(["attachments", "owner"])
            ->withCount(["likedProfiles", "likedCurrentProfiles"])
            ->paginateBy($request);

        return PostResource::collection($posts);
    }

    public function show(Request $request, string $profileId, string $postId): PostResource
    {
        $post = Post::availablePosts()
            ->where(["profile_id" => $profileId])
            ->with(['attachments', 'owner'])
            ->withCount(['likedProfiles', 'likedCurrentProfiles'])
            ->findOrFail($postId);

        return PostResource::make($post);
    }

    public function store(PostCreateRequest $request, string $profileId): PostResource
    {
        $profile = $request->user()->profile()->findOrFail($profileId);
        $postParams = $request->validated();

        $post = $profile->posts()->create($postParams);

        $attachments = $postParams["attachments"] ?? null;
        if (!is_null($attachments)) {
            $profile->attachments()->whereIn("id", $attachments)->update([
                "attachable_type" => $post->getMorphClass(),
                "attachable_id" => $post->id
            ]);
            $post->load('attachments');
        }

        return PostResource::make($post);
    }

    public function update(PostUpdateRequest $request, string $profileId, string $postId): PostResource
    {
        $profile = $request->user()->profile()->findOrFail($profileId);
        $postParams = $request->validated();

        $post = $profile->posts()->with('attachments')->findOrFail($postId);
        $post->update($postParams);

        $attachments = $postParams["attachments"] ?? null;
        if (!is_null($attachments)) {
            $profile->attachments()->whereIn("id", $attachments)->update([
                "attachable_type" => $post->getMorphClass(),
                "attachable_id" => $post->id
            ]);

            $post->attachments()->whereNotIn("id", $attachments)->update([
                "attachable_type" => null,
                "attachable_id" => null
            ]);
        }

        return PostResource::make($post->refresh());
    }

    public function destroy(Request $request, string $profileId, string $postId): Application|ResponseFactory|Response
    {
        $profile = $request->user()->profile()->findOrFail($profileId);
        $post = $profile->posts()->findOrFail($postId);

        $post->attachments()->update([
            "attachable_type" => null,
            "attachable_id" => null
        ]);

        $post->delete();

        return response("", 204);
    }
}

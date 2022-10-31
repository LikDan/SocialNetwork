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
use App\Models\Subscription;
use App\Models\SubscriptionStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostsController extends Controller
{

    /**
     * @throws AuthorizationException
     */
    public function profilePosts(Request $request, Profile $profile)
    {
        $userProfile = $request->user()->profile;

        $subscription = $userProfile->subscriptions()
            ->where(["from_profile_id" => $userProfile["id"], "to_profile_id" => $profile["id"]])
            ->first();

        if ($userProfile["id"] != $profile["id"] && $profile["is_private"] && $subscription["status"] != SubscriptionStatus::Approved->value)
            throw new AuthorizationException();

        $posts = $profile->posts()->where("type", PostType::Published)->paginate();
        return PostResource::collection($posts);
    }

    public function index(PostTypeRequest $request)
    {
        $type = $request->validated();
        $posts = $request->user()->profile->posts()
            ->when($type["type"] ?? null, fn(Builder $query, string $type) => $query->where("type", $type))
            ->where("type", PostType::Published->value)->paginate();
        return PostResource::collection($posts);
    }

    public function feed(Request $request)
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
     * @throws AuthorizationException
     */
    public function get(PostUpdateRequest $request, Post $post)
    {
        $profile = $request->user()->profile;
        if ($profile["id"] != $post["profile_id"] && Profile::where("id", $post["profile_id"])->first()["is_private"]) {
            $subscription = $profile->subscriptions()
                ->where(["from_profile_id" => $profile["id"], "to_profile_id" => $post["profile_id"]])
                ->first();
            if ($subscription["status"] ?? null != SubscriptionStatus::Approved->value)
                throw new AuthorizationException();
        }

        return PostResource::make($post);
    }

    public function create(PostCreateRequest $request)
    {
        $user = $request->user();
        $postParams = $request->validated();

        $postParams["profile_id"] = $user->profile->id;
        $post = Post::create($postParams);

        return PostResource::make($post);
    }

    public function update(PostUpdateRequest $request)
    {
        $profile = $request->user()->profile;
        $postParams = $request->validated();

        $query = Post::where(["id" => $postParams["id"], "profile_id" => $profile->id]);
        if (!$query->update($postParams)) {
            throw new NotFoundHttpException("Post not found");
        }

        return PostResource::make($query->first());
    }

    public function delete(Request $request, $id)
    {
        $profile = $request->user()->profile;

        if (!Post::where(["id" => $id, "profile_id" => $profile->id])->delete()) {
            throw new NotFoundHttpException("Post not found");
        }

        return response()->json(["status" => "ok"]);
    }
}

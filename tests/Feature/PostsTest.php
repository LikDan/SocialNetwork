<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostType;
use App\Models\Profile;
use App\Models\Subscription;
use App\Models\SubscriptionStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Utils;

class PostsTest extends TestCase
{
    use RefreshDatabase;
    public function test_post_index()
    {
        $auth = Utils::auth($this);

        $fromProfile = User::factory()->has(Profile::factory()->state(["is_private" => false]))->create()->profile;
        $posts = Post::factory(20)->state([
            "profile_id" => $fromProfile->id,
            "type" => PostType::Published->value,
        ])->create();

        $response = $this->get('api/v1/profiles/' . $fromProfile->id . '/posts', $auth["headers"]);
        $response->assertJson($posts->toArray());
    }

    public function test_post_feed()
    {
        $auth = Utils::auth($this);
        $profile = $auth["user"]->profile;

        $fromProfile = User::factory()->has(Profile::factory())->create()->profile;
        $posts = Post::factory(20)->state([
            "profile_id" => $fromProfile->id,
            "type" => PostType::Published->value,
        ])->create();

        Subscription::create([
            "from_profile_id" => $profile->id,
            "to_profile_id" => $fromProfile->id,
            "status" => SubscriptionStatus::Approved->value
        ]);

        $response = $this->get('api/v1/profiles/' . $fromProfile->id . '/posts', $auth["headers"]);
        $response->assertJson($posts->toArray());
    }

    public function test_post_show()
    {
        $auth = Utils::auth($this);

        $fromProfile = User::factory()->has(Profile::factory()->state(["is_private" => false]))->create()->profile;
        $post = Post::factory()->state([
            "profile_id" => $fromProfile->id,
            "type" => PostType::Published->value,
        ])->create()->toArray();

        $response = $this->get('api/v1/profiles/' . $fromProfile->id . '/posts/' . $post["id"], $auth["headers"]);
        $response->assertJson($post);
    }

    public function test_post_store()
    {
        $auth = Utils::auth($this);
        $profile = $auth["user"]->profile;

        $post = Post::factory()->state(["profile_id" => $profile->id])->make()->toArray();

        $response = $this->post('api/v1/profiles/' . $profile->id . '/posts/', $post, $auth["headers"]);

        $response->assertJson($post);
    }

    public function test_post_update()
    {
        $auth = Utils::auth($this);
        $profile = $auth["user"]->profile;

        $post = Post::factory()->state(["profile_id" => $profile->id])->create()->toArray();
        $newPost = Post::factory()->state(["profile_id" => $profile->id])->make()->toArray();

        $response = $this->put('api/v1/profiles/' . $profile->id . '/posts/' . $post["id"], $newPost, $auth["headers"]);
        $response->assertJson($newPost);
    }

    public function test_post_destroy()
    {
        $auth = Utils::auth($this);
        $profile = $auth["user"]->profile;

        $post = Post::factory()->state(["profile_id" => $profile->id])->create()->toArray();

        $response = $this->delete('api/v1/profiles/' . $profile->id . '/posts/' . $post["id"], [], $auth["headers"]);

        self::assertNull(Post::find($post["id"]));
        $response->assertStatus(204);
    }
}

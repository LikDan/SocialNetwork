<?php

namespace Tests\Feature;

use App\Http\Resources\Api\v1\ShortProfileResource;
use App\Models\Post;
use App\Models\PostType;
use App\Models\Profile;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Utils;

class LikesTest extends TestCase
{
    use RefreshDatabase;

    public function test_toggle()
    {
        $auth = Utils::auth($this);

        $fromProfile = User::factory()->has(Profile::factory()->state(["is_private" => false]))->create()->profile;
        $post = Post::factory()->state([
            "profile_id" => $fromProfile->id,
            "type" => PostType::Published->value,
        ])->create()->toArray();


        $toggleURL = 'api/v1/profiles/' . $fromProfile->id . '/posts/' . $post["id"] . '/likes';
        $response = $this->post($toggleURL, [], $auth["headers"]);
        $response->assertJson(["is_liked" => true]);
        $response = $this->post($toggleURL, [], $auth["headers"]);
        $response->assertJson(["is_liked" => false]);
    }

    public function test_index()
    {
        $auth = Utils::auth($this);
        $profile = $auth["user"]->profile;

        $post = Post::factory()->state([
            "profile_id" => $profile->id,
            "type" => PostType::Published->value,
        ])->create()->toArray();

        $profiles = User::factory(5)
            ->has(Profile::factory())->create()
            ->map(fn($user) => ShortProfileResource::make($user->profile)->resolve());
        foreach ($profiles as $pr) {
            DB::table("likes")->insert([
                "post_id" => $post["id"],
                "profile_id" => $pr["id"],
            ]);
        }

        $response = $this->get('api/v1/profiles/' . $profile->id . '/posts/' . $post["id"] . '/likes', $auth["headers"]);
        $response->assertJson($profiles->toArray());
    }
}

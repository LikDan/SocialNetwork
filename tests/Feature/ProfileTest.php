<?php

namespace Tests\Feature;

use App\Http\Resources\Api\v1\ShortProfileResource;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Utils;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_update()
    {
        $auth = Utils::auth($this);
        $profile = $auth["user"]->profile;

        $newProfile = Profile::factory()->make()->toArray();
        $newProfile["birthday"] = $newProfile["birthday"]->format("Y-m-d");

        $response = $this->put("/api/v1/profiles/" . $profile->id, $newProfile, $auth["headers"]);
        $response->assertJson($newProfile);
    }

    public function test_profile_index()
    {
        $auth = Utils::auth($this);

        $profiles = User::factory(20)->has(Profile::factory())->create()
            ->map(fn($user) => ShortProfileResource::make($user->profile)->resolve())
            ->toArray();
        $response = $this->get("/api/v1/profiles", $auth["headers"]);

        $response->assertJson($profiles);
    }

    public function test_profile_show()
    {
        $auth = Utils::auth($this);

        $profile = User::factory()
            ->has(Profile::factory()->state(["is_private" => false]))
            ->create()->profile->toArray();

        $profile["is_private"] = (boolean) $profile["is_private"];
        unset($profile["picture_path"]);

        $response = $this->get("/api/v1/profiles/" . $profile["id"], $auth["headers"]);

        $response->assertJson($profile);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_signup()
    {
        $user = User::factory()->make()->toArray();
        $user["password"] = "password";
        $user["password_confirmation"] = "password";
        $profile = Profile::factory()->make()->toArray();

        $body = [
            "user" => $user,
            "profile" => $profile,
        ];

        $response = $this->post("/api/v1/users/signup", $body);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('user.name', $user["name"])
            ->where('user.email', $user["email"])
            ->where('user.profile.nickname', $profile["nickname"])
            ->where('user.profile.birthday', $profile["birthday"]->format("Y-m-d"))
            ->where('user.profile.is_private', $profile["is_private"])
            ->etc()
        );
    }

    public function test_user_login()
    {
        $user = User::factory()->has(Profile::factory())->create();

        $body = [
            "email" => $user->email,
            "password" => "password"
        ];

        $response = $this->post("/api/v1/users/login", $body);
        $response->assertJsonFragment($user->toArray());
    }
}

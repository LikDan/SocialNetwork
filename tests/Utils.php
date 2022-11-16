<?php

namespace Tests;

use App\Models\Profile;
use App\Models\User;

class Utils
{
    public static function auth(TestCase $tc): array
    {
        $user = User::factory()->has(Profile::factory())->create();

        $body = [
            "email" => $user->email,
            "password" => "password"
        ];

        $response = $tc->post("/api/v1/users/login", $body);
        $token = $response->decodeResponseJson()["token"];

        return [
            "user" => $user,
            "headers" => ["Authorization" => "Bearer " . $token]
        ];
    }
}

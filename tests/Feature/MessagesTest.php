<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Profile;
use App\Models\Subscription;
use App\Models\SubscriptionStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Utils;

class MessagesTest extends TestCase
{
    use RefreshDatabase;
    public function test_store()
    {
        $auth = Utils::auth($this);
        $profile = $auth["user"]->profile;

        $toProfile = User::factory()->has(Profile::factory())->create()->profile;
        Subscription::create([
            "from_profile_id" => $profile->id,
            "to_profile_id" => $toProfile->id,
            "status" => SubscriptionStatus::Approved->value
        ]);

        $message = Message::factory()->state([
            "from_profile_id" => $profile->id,
            "to_profile_id" => $toProfile->id,
        ])->make()->toArray();

        $response = $this->post('api/v1/profiles/' . $toProfile->id . '/messages', $message, $auth["headers"]);
        $response->assertJson($message);
    }

    public function test_destroy()
    {
        $auth = Utils::auth($this);
        $profile = $auth["user"]->profile;

        $toProfile = User::factory()->has(Profile::factory())->create()->profile;
        Subscription::create([
            "from_profile_id" => $profile->id,
            "to_profile_id" => $toProfile->id,
            "status" => SubscriptionStatus::Approved->value
        ]);

        $message = Message::factory()->state([
            "from_profile_id" => $profile->id,
            "to_profile_id" => $toProfile->id,
        ])->create()->toArray();

        $response = $this->delete('api/v1/profiles/' . $toProfile->id . '/messages/' . $message["id"], [], $auth["headers"]);
        $response->assertStatus(204);
    }

    public function test_index()
    {
        $auth = Utils::auth($this);
        $profile = $auth["user"]->profile;

        $toProfile = User::factory()->has(Profile::factory())->create()->profile;
        Subscription::create([
            "from_profile_id" => $profile->id,
            "to_profile_id" => $toProfile->id,
            "status" => SubscriptionStatus::Approved->value
        ]);

        $message = Message::factory(20)->state([
            "from_profile_id" => $profile->id,
            "to_profile_id" => $toProfile->id,
        ])->create()->toArray();

        $message = array_reverse($message);

        $response = $this->get('api/v1/profiles/' . $toProfile->id . '/messages/', $auth["headers"]);
        $response->assertJson($message);
    }
}

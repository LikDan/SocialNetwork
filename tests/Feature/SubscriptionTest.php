<?php

namespace Tests\Feature;

use App\Http\Resources\Api\v1\ShortProfileResource;
use App\Models\Subscription;
use App\Models\SubscriptionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\Utils;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscribe()
    {
        $auth = Utils::auth($this);

        $fromProfile = $auth["user"]->profile;
        $toProfile = User::factory()->has(Profile::factory()->state(["is_private" => false]))->create()->profile;

        $response = $this->post('api/v1/profiles/' . $toProfile->id . '/subscribe', [], $auth["headers"]);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('status', SubscriptionStatus::Approved->value)
            ->where('to_profile_id', $toProfile->id)
            ->where('from_profile_id', $fromProfile->id)
            ->etc()
        );

        $toProfile = User::factory()->has(Profile::factory()->state(["is_private" => true]))->create()->profile;

        $response = $this->post('api/v1/profiles/' . $toProfile->id . '/subscribe', [], $auth["headers"]);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('status', SubscriptionStatus::Pending->value)
            ->where('to_profile_id', $toProfile->id)
            ->where('from_profile_id', $fromProfile->id)
            ->etc()
        );
    }

    public function test_unsubscribe()
    {
        $auth = Utils::auth($this);
        $fromProfile = $auth["user"]->profile;
        $toProfile = User::factory()->has(Profile::factory())->create()->profile;

        $subscription = Subscription::create([
            "from_profile_id" => $fromProfile->id,
            "to_profile_id" => $toProfile->id,
            "status" => SubscriptionStatus::Approved->value
        ]);

        $response = $this->delete('api/v1/profiles/' . $toProfile->id . '/unsubscribe', [], $auth["headers"]);

        self::assertNull(Subscription::find($subscription->id));
        $response->assertStatus(204);
    }

    public function test_remove_subscriber()
    {
        $auth = Utils::auth($this);
        $toProfile = $auth["user"]->profile;
        $fromProfile = User::factory()->has(Profile::factory())->create()->profile;

        $subscription = Subscription::create([
            "to_profile_id" => $toProfile->id,
            "from_profile_id" => $fromProfile->id,
            "status" => SubscriptionStatus::Approved->value
        ]);

        $response = $this->delete('api/v1/subscriptions/' . $fromProfile->id, [], $auth["headers"]);

        self::assertNull(Subscription::find($subscription->id));
        $response->assertStatus(204);
    }

    public function test_subscriptions() {
        $auth = Utils::auth($this);
        $fromProfile = $auth["user"]->profile;

        $subscriptions = [];

        for ($i = 0; $i < 5; $i++) {
            $toProfile = User::factory()->has(Profile::factory())->create()->profile;

            $subscription = Subscription::create([
                "from_profile_id" => $fromProfile->id,
                "to_profile_id" => $toProfile->id,
                "status" => SubscriptionStatus::Approved->value
            ])->toArray();

            $subscription["to_profile"] = ShortProfileResource::make($toProfile)->resolve();

            $subscriptions[] = $subscription;
        }

        $response = $this->get('api/v1/subscriptions', $auth["headers"]);
        $response->assertJson($subscriptions);
    }

    public function test_subscribers() {
        $auth = Utils::auth($this);
        $toProfile = $auth["user"]->profile;

        $subscriptions = [];

        for ($i = 0; $i < 5; $i++) {
            $fromProfile = User::factory()->has(Profile::factory())->create()->profile;

            $subscription = Subscription::create([
                "to_profile_id" => $toProfile->id,
                "from_profile_id" => $fromProfile->id,
                "status" => SubscriptionStatus::Approved->value
            ])->toArray();

            $subscription["from_profile"] = ShortProfileResource::make($fromProfile)->resolve();

            $subscriptions[] = $subscription;
        }

        $response = $this->get('api/v1/subscribers', $auth["headers"]);
        $response->assertJson($subscriptions);
    }
}

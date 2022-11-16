<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class StaticTest extends TestCase
{
    public function test_static()
    {
        $response = $this->get('api/v1/static');

        $response->assertJson(fn(AssertableJson $json) => $json
            ->whereType("subscriptions.statuses", "array")
        );
    }
}

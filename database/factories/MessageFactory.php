<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    public function definition(): array
    {
        $ids = Profile::pluck('id');
        $fromID = fake()->randomElement($ids);
        $ids->forget($fromID);
        $toID = fake()->randomElement($ids);
        return [
            "message" => fake()->text(100),
            "from_profile_id" => $fromID,
            "to_profile_id" => $toID
        ];
    }
}

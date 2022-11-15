<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nickname' => fake()->name(),
            'picture_path' => "avatars/default.png",
            'user_id' => User::factory(),
            'is_private' => fake()->boolean(),
            'birthday' => fake()->dateTime()
        ];
    }
}

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
            'is_private' => fake()->boolean(),
            'birthday' => fake()->dateTime("2009-12-31"),
            'picture_path' => "avatars/default.png"
        ];
    }
}

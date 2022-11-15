<?php

namespace Database\Factories;

use App\Models\PostType;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            "title" => fake()->text(25),
            "text" => fake()->text(),
            "type" => fake()->randomElement(array_map(fn($el) => $el->value, PostType::cases())),
            "profile_id" => fake()->randomElement(Profile::pluck('id')),
        ];
    }
}

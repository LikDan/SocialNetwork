<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class AttachmentFactory extends Factory
{

    public function definition()
    {
        return [
            "path" => 'attachments/' . fake()->image(Storage::path("attachments"), fullPath: false),
            "display_name" => "Image",
            "type" => "png",
            "profile_id" => 70
        ];
    }
}

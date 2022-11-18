<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Message;
use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private function fake() {
        User::factory(500)->has(Profile::factory())->create();
        Post::factory(2500)->create();
        Message::factory(3000)->create();
    }

    public function run(): void
    {
        //$this->fake();
    }
}

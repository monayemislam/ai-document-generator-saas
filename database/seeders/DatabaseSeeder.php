<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create 5 users
        \App\Models\User::factory(5)->create()->each(function ($user) {
            // Create 10 clients for each user
            \App\Models\Client::factory(10)->create([
                'user_id' => $user->id
            ]);
        });
    }
}

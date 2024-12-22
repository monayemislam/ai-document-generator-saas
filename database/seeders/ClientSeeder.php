<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // Get all users or create a test user if none exists
        $users = User::all();
        
        if ($users->isEmpty()) {
            $users = User::factory(1)->create();
        }

        foreach ($users as $user) {
            Client::factory()
                ->count(10)
                ->create([
                    'user_id' => $user->id
                ]);
        }
    }
}

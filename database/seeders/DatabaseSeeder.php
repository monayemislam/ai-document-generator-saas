<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Invoice;
use App\Models\Subscription;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(10)->create()->each(function ($user) {
            Client::factory(3)->create(['user_id' => $user->id])->each(function ($client) {
                Project::factory(2)->create(['client_id' => $client->id])->each(function ($project) {
                    Proposal::factory(1)->create(['project_id' => $project->id]);
                    Invoice::factory(2)->create(['project_id' => $project->id]);
                });
            });
            
            if (rand(0, 1)) {
                Subscription::factory()->create(['user_id' => $user->id]);
            }
        });
    }
}

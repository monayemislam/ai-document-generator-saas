<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Subscription;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'stripe_id' => 'sub_' . $this->faker->md5(),
            'stripe_status' => $this->faker->randomElement(['active', 'canceled', 'past_due']),
            'stripe_plan' => $this->faker->randomElement(['basic', 'pro', 'enterprise']),
            'quantity' => 1,
            'trial_ends_at' => $this->faker->optional()->dateTimeBetween('now', '+14 days'),
            'ends_at' => $this->faker->optional()->dateTimeBetween('+1 month', '+1 year'),
        ];
    }
}

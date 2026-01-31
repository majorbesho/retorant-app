<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserSubscription;
use App\Models\User;
use App\Models\SubscriptionPlan;

class UserSubscriptionFactory extends Factory
{
    protected $model = UserSubscription::class;

    public function definition()
    {
        return [
            'uuid' => \Illuminate\Support\Str::uuid(),
            'user_id' => User::factory(),
            'subscription_plan_id' => SubscriptionPlan::factory(),
            'stripe_subscription_id' => 'sub_' . $this->faker->unique()->sha256,
            'stripe_customer_id' => 'cus_' . $this->faker->unique()->sha256,
            'billing_cycle' => $this->faker->randomElement(['monthly', 'yearly']),
            'current_price' => $this->faker->numberBetween(29, 199),
            'status' => $this->faker->randomElement(['active', 'trial', 'paused', 'past_due']),
            'started_at' => now(),
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
            'trial_ends_at' => $this->faker->boolean(50) ? now()->addDays(7) : null,
            'canceled_at' => null,
            'cancel_reason' => null,
            'next_billing_date' => now()->addMonth(),
            'auto_renew' => true,
            'payment_method_id' => null,
            'active_features' => [
                'ai_chat' => true,
                'order_management' => $this->faker->boolean(),
                'analytics' => $this->faker->boolean(),
            ],
            'usage_stats' => [
                'conversations' => $this->faker->numberBetween(0, 1000),
                'api_calls' => $this->faker->numberBetween(0, 10000),
                'orders' => $this->faker->numberBetween(0, 500),
            ],
            'invoice_count' => $this->faker->numberBetween(0, 5),
            'failed_payment_count' => $this->faker->numberBetween(0, 2),
            'notes' => null,
            'credit_balance' => $this->faker->numberBetween(0, 100),
        ];
    }

    public function active()
    {
        return $this->state([
            'status' => 'active',
            'current_period_end' => now()->addMonth(),
            'trial_ends_at' => null,
            'canceled_at' => null,
        ]);
    }

    public function trial()
    {
        return $this->state([
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
            'canceled_at' => null,
        ]);
    }

    public function canceled()
    {
        return $this->state([
            'status' => 'canceled',
            'canceled_at' => now(),
            'cancel_reason' => $this->faker->randomElement([
                'requested_by_user',
                'payment_failed',
                'no_longer_needed',
                'switching_plan',
            ]),
        ]);
    }

    public function expired()
    {
        return $this->state([
            'status' => 'expired',
            'current_period_end' => now()->subDay(),
        ]);
    }
}

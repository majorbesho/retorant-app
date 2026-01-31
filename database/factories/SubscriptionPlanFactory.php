<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SubscriptionPlan;

class SubscriptionPlanFactory extends Factory
{
    protected $model = SubscriptionPlan::class;

    public function definition()
    {
        $names = ['Starter', 'Professional', 'Enterprise'];
        $slug = $this->faker->unique()->slug;
        $name = $this->faker->randomElement($names);

        return [
            'name' => $name,
            'name_translations' => [
                'ar' => match($name) {
                    'Starter' => 'المبتدئ',
                    'Professional' => 'الاحترافي',
                    'Enterprise' => 'المتقدم',
                },
                'en' => $name,
            ],
            'slug' => $slug,
            'description' => $this->faker->paragraph(),
            'description_translations' => [
                'ar' => $this->faker->paragraph(),
                'en' => $this->faker->paragraph(),
            ],
            'monthly_price' => $this->faker->numberBetween(29, 299),
            'yearly_price' => $this->faker->numberBetween(290, 2990),
            'setup_fee' => $this->faker->numberBetween(0, 50),
            'billing_cycle' => 'monthly',
            'trial_days' => 14,
            'status' => 'active',
            'features' => [
                'ai_chat' => true,
                'order_management' => true,
                'analytics' => $this->faker->boolean(),
                'api_access' => $this->faker->boolean(),
                'priority_support' => $this->faker->boolean(),
                'custom_ai_prompt' => $this->faker->boolean(),
            ],
            'limits' => [
                'max_restaurants' => $this->faker->numberBetween(1, 100),
                'max_conversations' => $this->faker->numberBetween(1000, 100000),
                'max_api_calls' => $this->faker->numberBetween(10000, 1000000),
                'max_orders' => $this->faker->numberBetween(1000, 100000),
            ],
            'stripe_price_id_monthly' => 'price_' . $this->faker->unique()->sha256,
            'stripe_price_id_yearly' => 'price_' . $this->faker->unique()->sha256,
            'stripe_product_id' => 'prod_' . $this->faker->unique()->sha256,
            'is_recommended' => $this->faker->boolean(30),
            'sort_order' => $this->faker->numberBetween(1, 10),
            'included_features' => [
                'email_support',
                'community_access',
                'documentation',
                'basic_analytics',
            ],
        ];
    }

    public function starter()
    {
        return $this->state([
            'name' => 'Starter',
            'name_translations' => ['ar' => 'المبتدئ', 'en' => 'Starter'],
            'slug' => 'starter',
            'monthly_price' => 29,
            'yearly_price' => 290,
            'features' => [
                'ai_chat' => true,
                'order_management' => false,
                'analytics' => false,
                'api_access' => false,
                'priority_support' => false,
            ],
            'limits' => [
                'max_restaurants' => 1,
                'max_conversations' => 1000,
                'max_api_calls' => 10000,
            ],
            'is_recommended' => false,
            'sort_order' => 1,
        ]);
    }

    public function professional()
    {
        return $this->state([
            'name' => 'Professional',
            'name_translations' => ['ar' => 'الاحترافي', 'en' => 'Professional'],
            'slug' => 'professional',
            'monthly_price' => 79,
            'yearly_price' => 790,
            'features' => [
                'ai_chat' => true,
                'order_management' => true,
                'analytics' => true,
                'api_access' => true,
                'priority_support' => false,
            ],
            'limits' => [
                'max_restaurants' => 5,
                'max_conversations' => 10000,
                'max_api_calls' => 100000,
            ],
            'is_recommended' => true,
            'sort_order' => 2,
        ]);
    }

    public function enterprise()
    {
        return $this->state([
            'name' => 'Enterprise',
            'name_translations' => ['ar' => 'المتقدم', 'en' => 'Enterprise'],
            'slug' => 'enterprise',
            'monthly_price' => 199,
            'yearly_price' => 1990,
            'features' => [
                'ai_chat' => true,
                'order_management' => true,
                'analytics' => true,
                'api_access' => true,
                'priority_support' => true,
                'custom_ai_prompt' => true,
            ],
            'limits' => [
                'max_restaurants' => 1000,
                'max_conversations' => 1000000,
                'max_api_calls' => 10000000,
            ],
            'is_recommended' => false,
            'sort_order' => 3,
        ]);
    }
}

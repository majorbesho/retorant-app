<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'name_translations' => ['en' => 'Basic', 'ar' => 'أساسي'],
                'slug' => 'basic',
                'description' => 'Perfect for small restaurants.',
                'description_translations' => ['en' => 'Perfect for small restaurants.', 'ar' => 'مثالي للمطاعم الصغيرة.'],
                'monthly_price' => 49.00,
                'yearly_price' => 490.00,
                'features' => json_encode(['menu_management', 'basic_reporting']),
                'limits' => json_encode(['orders' => 100]),
                'sort_order' => 1,
                'is_recommended' => false,
            ],
            [
                'name' => 'Pro',
                'name_translations' => ['en' => 'Pro', 'ar' => 'محترف'],
                'slug' => 'pro',
                'description' => 'Best for growing businesses.',
                'description_translations' => ['en' => 'Best for growing businesses.', 'ar' => 'الأفضل للأعمال النامية.'],
                'monthly_price' => 99.00,
                'yearly_price' => 990.00,
                'features' => json_encode(['menu_management', 'advanced_reporting', 'ai_auto_reply']),
                'limits' => json_encode(['orders' => 1000]),
                'sort_order' => 2,
                'is_recommended' => true,
            ],
            [
                'name' => 'Enterprise',
                'name_translations' => ['en' => 'Enterprise', 'ar' => 'مؤسسات'],
                'slug' => 'enterprise',
                'description' => 'For large scale operations.',
                'description_translations' => ['en' => 'For large scale operations.', 'ar' => 'للعمليات واسعة النطاق.'],
                'monthly_price' => 199.00,
                'yearly_price' => 1990.00,
                'features' => json_encode(['all_features', 'priority_support']),
                'limits' => json_encode(['orders' => -1]), // Unlimited
                'sort_order' => 3,
                'is_recommended' => false,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}

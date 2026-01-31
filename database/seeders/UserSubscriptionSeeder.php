<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserSubscription;
use App\Models\User;
use App\Models\SubscriptionPlan;

class UserSubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // حذف الاشتراكات السابقة
        UserSubscription::query()->delete();

        // الحصول على الخطط المتاحة
        $plans = SubscriptionPlan::all();

        // الحصول على أو إنشاء المستخدمين
        $users = User::limit(20)->get();

        if ($users->count() == 0) {
            // إنشاء 20 مستخدم إذا لم يكونوا موجودين
            $users = User::factory(20)->create();
        }

        // إنشاء اشتراكات متنوعة
        foreach ($users as $index => $user) {
            // اختيار خطة عشوائية
            $plan = $plans->random();

            // تحديد حالة الاشتراك
            $status = match ($index % 5) {
                0 => 'active',
                1 => 'trial',
                2 => 'paused',
                3 => 'canceled',
                default => 'active',
            };

            // حساب التواريخ
            $startDate = now()->subDays(rand(1, 90));
            $endDate = $startDate->copy()->addMonths($plan->billing_cycle === 'yearly' ? 12 : 1);

            UserSubscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'uuid' => \Illuminate\Support\Str::uuid(),
                'stripe_subscription_id' => 'sub_' . bin2hex(random_bytes(12)),
                'stripe_customer_id' => 'cus_' . bin2hex(random_bytes(12)),
                'billing_cycle' => rand(0, 1) ? 'monthly' : 'yearly',
                'current_price' => rand(0, 1) ? $plan->monthly_price : $plan->yearly_price,
                'status' => $status,
                'started_at' => $startDate,
                'current_period_start' => $startDate,
                'current_period_end' => $endDate,
                'trial_ends_at' => $status === 'trial' ? now()->addDays(7) : null,
                'canceled_at' => $status === 'canceled' ? now()->subDays(rand(1, 30)) : null,
                'cancel_reason' => $status === 'canceled' ? 'Not needed anymore' : null,
                'next_billing_date' => $status === 'active' ? $endDate : null,
                'auto_renew' => true,
                'payment_method_id' => null,
                'active_features' => $plan->features ?? [],
                'usage_stats' => [
                    'conversations_used' => rand(0, $plan->limits['conversations_per_month'] ?? 100),
                    'api_calls_used' => rand(0, $plan->limits['api_calls_per_day'] ?? 100),
                    'users_used' => rand(1, $plan->limits['users'] ?? 1),
                ],
                'credit_balance' => rand(0, 100),
            ]);
        }

        echo "✅ تم إنشاء " . $users->count() . " اشتراك بنجاح!\n";
    }
}

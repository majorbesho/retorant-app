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
        \Illuminate\Support\Facades\DB::table('subscriptions')->delete();

        // الحصول على الخطط المتاحة
        $plans = SubscriptionPlan::all();

        // الحصول على أو إنشاء المستخدمين
        $users = User::limit(20)->get();

        if ($users->count() == 0) {
            // إنشاء 20 مستخدم إذا لم يكونوا موجودين
            $users = User::factory(20)->create();
        }

        // الحصول على المطاعم لربطها بالاشتراكات
        $restaurants = \App\Models\Restaurant::all();

        // إنشاء اشتراكات متنوعة
        foreach ($users as $index => $user) {
            // تخطي بعض المستخدمين ليكون لديهم اشتراك مجاني أو لا شيء
            if ($index > 15) continue;

            // اختيار خطة عشوائية
            $plan = $plans->random();

            // تحديد حالة الاشتراك
            $stripeStatus = match ($index % 5) {
                0 => 'active',
                1 => 'trialing',
                2 => 'past_due',
                3 => 'canceled',
                4 => 'active',
                default => 'active',
            };

            // حساب التواريخ
            $startDate = now()->subDays(rand(1, 90));
            $endDate = $startDate->copy()->addMonths($plan->billing_cycle === 'yearly' ? 12 : 1);
            $trialEndsAt = $stripeStatus === 'trialing' ? now()->addDays(7) : null;

            // محاولة ربط مطعم إذا وجد
            $restaurantId = null;
            if ($restaurants->count() > 0) {
                // ربط عشوائي، ليس دقيقاً بالضرورة لهذا الـ seeder البسيط
                $restaurant = $restaurants->random();
                $restaurantId = $restaurant->id;
            }

            \Illuminate\Support\Facades\DB::table('subscriptions')->insert([
                'user_id' => $user->id,
                'name' => 'default',
                'stripe_id' => 'sub_' . \Illuminate\Support\Str::random(24),
                'stripe_status' => $stripeStatus,
                'stripe_price' => 'price_' . \Illuminate\Support\Str::random(24),
                'quantity' => 1,
                'trial_ends_at' => $trialEndsAt,
                'ends_at' => $stripeStatus === 'canceled' ? now()->subDays(rand(1, 10)) : null,
                'restaurant_id' => $restaurantId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "✅ تم إنشاء اشتراكات تجريبية في جدول subscriptions بنجاح!\n";
    }
}

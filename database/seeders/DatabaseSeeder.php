<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "\n🚀 بدء إنشاء البيانات التجريبية...\n";
        echo "========================================\n\n";

        // 0. إنشاء خطط الاشتراك أولاً
        echo "📋 إنشاء خطط الاشتراك...\n";
        $this->call(SubscriptionPlanSeeder::class);

        // 0.1 إنشاء طرق الدفع
        echo "\n💳 إنشاء طرق الدفع...\n";
        $this->call(PaymentMethodSeeder::class);

        // 0.2 إنشاء اشتراكات المستخدمين
        echo "\n👤 إنشاء اشتراكات المستخدمين...\n";
        $this->call(UserSubscriptionSeeder::class);

        // 1. Create Users
        $users = User::factory()->count(20)->create();

        // 20+ Users
        // Also call RolesAndPermissionsSeeder if needed once
        $this->call(RolesAndPermissionsSeeder::class);

        // 0.3 إنشاء الموظفين
        echo "\n👥 إنشاء الموظفين...\n";
        $this->call(StaffMemberSeeder::class);

        // 2. Create Restaurants
        $restaurants = \App\Models\Restaurant::factory()
            ->count(5)
            ->create();

        foreach ($restaurants as $restaurant) {
            // Assign a random owner from users (or create one specific)
            // For now, let's keep it simple.

            // 3. Menus (Total 5*4 = 20)
            $menus = \App\Models\Menu::factory()
                ->count(4)
                ->for($restaurant)
                ->create();

            foreach ($menus as $menu) {
                // 4. Categories (Total 20*5 = 100)
                $categories = \App\Models\Category::factory()
                    ->count(5)
                    ->for($restaurant)
                    ->for($menu)
                    ->create();

                foreach ($categories as $category) {
                    // 5. Products (Total 100*5 = 500)
                    $products = \App\Models\Product::factory()
                        ->count(5)
                        ->for($restaurant)
                        ->for($category)
                        ->create();

                    // Add variations/addons to some products
                    foreach ($products as $product) {
                        if (rand(0, 1)) {
                            $group = \App\Models\AddonGroup::factory()->for($restaurant)->create();
                            \App\Models\Addon::factory()->count(3)->for($group, 'group')->create();
                        }
                    }
                }
            }

            // 6. Orders (Total 5*20 = 100)
            \App\Models\Order::factory()
                ->count(20)
                ->for($restaurant)
                ->recycle($users)
                ->create()
                ->each(function ($order) use ($restaurant) {
                    // Order Items
                    \App\Models\OrderItem::factory()
                        ->count(rand(1, 5))
                        ->for($order)
                        ->state(function (array $attributes) use ($restaurant) {
                            // Pick a random product from this restaurant
                            // We assume products were created in step 5.
                            // To be safe, we can fetch one.
                            $product = $restaurant->products()->inRandomOrder()->first();

                            // If no product found (rare case if validation fails), make one linked to restaurant
                            if (!$product) {
                                $product = \App\Models\Product::factory()->for($restaurant)->create();
                            }

                            return [
                                'product_id' => $product->id,
                                'product_name' => $product->name, // Ensure consistency
                                'unit_price' => $product->price,
                                'total_price' => $product->price,
                            ];
                        })
                        ->create();
                });

            // 7. Reservations (Total 5*10 = 50)
            \App\Models\Reservation::factory()
                ->count(10)
                ->for($restaurant)
                ->recycle($users)
                ->create();

            // 8. Reviews (Total 5*10 = 50)
            \App\Models\Review::factory()
                ->count(10)
                ->for($restaurant)
                ->recycle($users)
                ->create();

            // 9. AI Agents (Total 5*5 = 25)
            \App\Models\AIAgent::factory()
                ->count(5)
                ->for($restaurant)
                ->create();
        }

        echo "\n========================================\n";
        echo "✅ تم إنشاء جميع البيانات التجريبية بنجاح!\n\n";

        echo "📊 ملخص البيانات المُنشأة:\n";
        echo "   - خطط اشتراك: " . \App\Models\SubscriptionPlan::count() . "\n";
        echo "   - طرق دفع: " . \App\Models\PaymentMethod::count() . "\n";
        echo "   - اشتراكات: " . \App\Models\UserSubscription::count() . "\n";
        echo "   - موظفين: " . \App\Models\StaffMember::count() . "\n";
        echo "   - مستخدمين: " . \App\Models\User::count() . "\n";
        echo "   - مطاعم: " . \App\Models\Restaurant::count() . "\n";
        echo "   - طلبات: " . \App\Models\Order::count() . "\n";
        echo "\n";
    }
}

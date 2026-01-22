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
        // 1. Create Users
        $users = User::factory()->count(20)->create();

        // 20+ Users
        // Also call RolesAndPermissionsSeeder if needed once
        $this->call(RolesAndPermissionsSeeder::class);

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
                                'total_price' => $product->price, // * quantity handled by factory? No factory has fixed quantity logic usually.
                                // We'll let factory generated quantity stand, but updating price might be good.
                                // But factory definition uses random price. 
                                // Let's just set product_id and let factory randoms be random for now, 
                                // or better: sync price with product.
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
    }
}

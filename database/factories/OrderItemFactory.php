<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 5, 50);
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 4),
            'unit_price' => $price,
            'total_price' => $price, // Simplified for factory
            'product_name' => $this->faker->word(),
            'product_name_translations' => [
                'ar' => 'منتج',
                'en' => 'Product',
            ],
        ];
    }
}

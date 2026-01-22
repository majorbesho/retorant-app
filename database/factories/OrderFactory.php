<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $total = $this->faker->randomFloat(2, 20, 200);
        return [
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'restaurant_id' => Restaurant::factory(),
            'user_id' => User::factory(),
            'customer_name' => $this->faker->name(),
            'customer_phone' => $this->faker->phoneNumber(),
            'customer_email' => $this->faker->email(),
            'order_type' => $this->faker->randomElement(['delivery', 'pickup', 'dine_in']),
            'source' => $this->faker->randomElement(['web', 'mobile', 'whatsapp', 'phone', 'walk_in']),
            'total_amount' => $total,
            'subtotal' => $total * 0.9,
            'delivery_fee' => $total * 0.05,
            'tax_amount' => $total * 0.05,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
            'payment_method' => $this->faker->randomElement(['cash', 'card']),
        ];
    }
}

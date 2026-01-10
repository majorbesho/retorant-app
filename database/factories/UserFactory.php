<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'name_translations' => [
                'ar' => $this->faker->name(),
                'en' => $this->faker->name(),
            ],
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '+9715' . $this->faker->randomNumber(7, true),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'password' => bcrypt('password123'),
            'locale' => $this->faker->randomElement(['ar', 'en']),
            'timezone' => 'Asia/Dubai',
            'notification_preferences' => [
                'email' => true,
                'sms' => true,
                'whatsapp' => true,
                'push' => true,
                'order_updates' => true,
                'reservation_reminders' => true,
                'marketing' => false,
            ],
            'is_active' => true,
            'status' => 'active',
            'remember_token' => Str::random(10),
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_super_admin' => true,
            'restaurant_id' => null,
        ]);
    }

    public function restaurantOwner($restaurantId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'is_super_admin' => false,
            'restaurant_id' => $restaurantId,
        ]);
    }

    public function restaurantStaff($restaurantId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'restaurant_id' => $restaurantId,
        ]);
    }

    public function unverifiedPhone(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone_verified_at' => null,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'status' => 'suspended',
        ]);
    }
}
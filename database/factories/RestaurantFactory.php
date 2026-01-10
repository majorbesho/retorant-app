<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition(): array
    {
        $name = $this->faker->company();
        return [
            'uuid' => Str::uuid(),
            'name' => $name,
            'name_translations' => [
                'ar' => $name . ' (عربي)',
                'en' => $name,
            ],
            'slug' => Str::slug($name),
            'type' => $this->faker->randomElement(['restaurant', 'cafe', 'fast_food']),
            'cuisine_type' => $this->faker->randomElement(['Italian', 'Arabic', 'Indian', 'International']),
            'cuisine_tags' => ['halal', 'vegetarian'],
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'city' => 'Dubai',
            'area' => 'Downtown',
            'is_active' => true,
            'status' => 'active',
        ];
    }
}

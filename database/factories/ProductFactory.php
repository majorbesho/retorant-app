<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        return [
            'restaurant_id' => Restaurant::factory(),
            'category_id' => Category::factory(),
            'sku' => strtoupper(Str::random(10)),
            'name' => $name,
            'name_translations' => [
                'ar' => $name . ' (عربي)',
                'en' => $name,
            ],
            'description' => $this->faker->paragraph(),
            'description_translations' => [
                'ar' => 'وصف المنتج بالعربية',
                'en' => $this->faker->paragraph(),
            ],
            'price' => $this->faker->randomFloat(2, 10, 100),
            'images' => [], // Casted to array in model
            'is_active' => true,
            'is_available' => true,
            'has_variations' => false,
        ];
    }
}

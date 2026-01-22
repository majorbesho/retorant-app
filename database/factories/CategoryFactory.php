<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->words(1, true);
        return [
            'restaurant_id' => Restaurant::factory(),
            'menu_id' => Menu::factory(),
            'name' => $name,
            'name_translations' => [
                'ar' => $name . ' (عربي)',
                'en' => $name,
            ],
            'description' => $this->faker->sentence(),
            'description_translations' => [
                'ar' => 'وصف التصنيف بالعربية',
                'en' => $this->faker->sentence(),
            ],
            // 'slug' removed
            'image' => null, // Placeholder or null
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 20),
        ];
    }
}

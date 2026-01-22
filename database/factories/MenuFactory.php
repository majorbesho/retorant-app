<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true);
        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => $name,
            'name_translations' => [
                'ar' => 'قائمة ' . $name,
                'en' => $name,
            ],
            'description' => $this->faker->sentence(),
            'description_translations' => [
                'ar' => 'وصف القائمة باللغة العربية',
                'en' => $this->faker->sentence(),
            ],
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}

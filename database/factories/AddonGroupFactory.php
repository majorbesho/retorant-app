<?php

namespace Database\Factories;

use App\Models\AddonGroup;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddonGroupFactory extends Factory
{
    protected $model = AddonGroup::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true);
        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => $name,
            'name_translations' => [
                'ar' => $name . ' (عربي)',
                'en' => $name,
            ],
            // 'type' removed
            'min_selections' => 0,
            'max_selections' => 5,
            'is_required' => false,
            'is_active' => true,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\Variation;
use Illuminate\Database\Eloquent\Factories\Factory;

class VariationFactory extends Factory
{
    protected $model = Variation::class;

    public function definition(): array
    {
        $name = $this->faker->words(1, true);
        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => $name,
            'name_translations' => [
                'ar' => $name . ' (عربي)',
                'en' => $name,
            ],
            'type' => 'radio', // or checkbox
            'is_required' => true,
            'is_active' => true,
        ];
    }
}

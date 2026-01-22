<?php

namespace Database\Factories;

use App\Models\Variation;
use App\Models\VariationOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class VariationOptionFactory extends Factory
{
    protected $model = VariationOption::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true);
        return [
            'variation_id' => Variation::factory(),
            'name' => $name,
            'name_translations' => [
                'ar' => $name . ' (عربي)',
                'en' => $name,
            ],
            'price' => $this->faker->randomFloat(2, 0, 5),
            'is_active' => true,
        ];
    }
}

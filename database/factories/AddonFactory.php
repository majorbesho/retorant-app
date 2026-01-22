<?php

namespace Database\Factories;

use App\Models\Addon;
use App\Models\AddonGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddonFactory extends Factory
{
    protected $model = Addon::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true);
        return [
            'addon_group_id' => AddonGroup::factory(),
            'name' => $name,
            'name_translations' => [
                'ar' => $name . ' (عربي)',
                'en' => $name,
            ],
            'price' => $this->faker->randomFloat(2, 1, 10),
            'is_active' => true,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\FAQ;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class FAQFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FAQ::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'question_translations' => ['en' => 'Question EN', 'ar' => 'Question AR'],
            'answer_translations' => ['en' => 'Answer EN', 'ar' => 'Answer AR'],
            'is_active' => true,
        ];
    }
}

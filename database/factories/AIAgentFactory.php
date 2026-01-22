<?php

namespace Database\Factories;

use App\Models\AIAgent;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class AIAgentFactory extends Factory
{
    protected $model = AIAgent::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => $this->faker->name() . ' Agent',
            'type' => $this->faker->randomElement(['whatsapp', 'voice']),
            'status' => 'active',
            'ai_provider' => 'openai',
            'ai_model' => 'gpt-4',
            'temperature' => 0.7,
            'greeting_message' => ['ar' => 'مرحبا', 'en' => 'Hello'],
            'fallback_message' => ['ar' => 'عذرا', 'en' => 'Sorry'],
            'voice_provider' => null,
            'voice_id' => null,
        ];
    }
}

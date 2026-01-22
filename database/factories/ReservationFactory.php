<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'user_id' => User::factory(),
            'reservation_number' => 'RES-' . strtoupper(Str::random(10)), // or UUID
            'reservation_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'reservation_time' => $this->faker->time('H:i:s'),
            'shift' => $this->faker->randomElement(['breakfast', 'lunch', 'dinner', 'late_night']),
            'number_of_guests' => $this->faker->numberBetween(2, 10),
            'table_number' => $this->faker->numberBetween(1, 20),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled', 'completed']),
            // 'notes' removed (using special_requests instead)
            'special_requests' => $this->faker->sentence(),
            'customer_name' => $this->faker->name(),
            'customer_phone' => $this->faker->phoneNumber(),
            'customer_email' => $this->faker->email(),
        ];
    }
}

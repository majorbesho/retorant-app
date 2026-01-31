<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Str;

class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition(): array
    {
        $name = $this->faker->company();
        return [
            'uuid' => Str::uuid(),
            'name' => $name,
            'name_translations' => [
                'ar' => $name . ' (عربي)',
                'en' => $name,
            ],
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'description_translations' => [
                'ar' => $this->faker->paragraph(),
                'en' => $this->faker->paragraph(),
            ],
            'type' => $this->faker->randomElement(['restaurant', 'cafe', 'fast_food', 'fine_dining', 'buffet']),
            'cuisine_type' => $this->faker->randomElement(['Arabic', 'Indian', 'Italian', 'Asian', 'International']),
            'cuisine_tags' => ['halal', 'vegetarian', 'vegan'],
            'phone' => $this->faker->phoneNumber(),
            'whatsapp_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->url(),
            'address' => $this->faker->address(),
            'address_translations' => [
                'ar' => $this->faker->address(),
                'en' => $this->faker->address(),
            ],
            'latitude' => $this->faker->latitude(23.0, 25.5),
            'longitude' => $this->faker->longitude(50.5, 57.0),
            'city' => $this->faker->randomElement(['Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman']),
            'area' => $this->faker->word(),
            'social_links' => [
                'facebook' => 'https://facebook.com/' . $this->faker->slug,
                'instagram' => 'https://instagram.com/' . $this->faker->slug,
            ],
            'features' => [
                'delivery' => true,
                'pickup' => true,
                'reservation' => true,
                'dine_in' => true,
            ],
            'payment_methods' => ['cash', 'card', 'apple_pay', 'stc_pay'],
            'logo' => null,
            'gallery' => [],
            'cover_image' => null,
            'settings' => [
                'language' => 'ar',
                'currency' => 'AED',
                'timezone' => 'Asia/Dubai',
            ],
            'working_hours' => [
                'monday' => ['open' => '09:00', 'close' => '23:00'],
                'tuesday' => ['open' => '09:00', 'close' => '23:00'],
                'wednesday' => ['open' => '09:00', 'close' => '23:00'],
                'thursday' => ['open' => '09:00', 'close' => '00:00'],
                'friday' => ['open' => '09:00', 'close' => '00:00'],
                'saturday' => ['open' => '09:00', 'close' => '23:00'],
                'sunday' => ['open' => '09:00', 'close' => '23:00'],
            ],
            'holidays' => [],
            'preparation_time' => $this->faker->numberBetween(15, 60),
            'rating' => $this->faker->numberBetween(3, 5),
            'review_count' => $this->faker->numberBetween(0, 500),
            'order_count' => $this->faker->numberBetween(0, 5000),
            'is_active' => true,
            'is_verified' => $this->faker->boolean(70),
            'status' => 'active',
            'stripe_account_id' => null,
        ];
    }

    public function verified()
    {
        return $this->state([
            'is_verified' => true,
        ]);
    }

    public function withActiveSubscription()
    {
        return $this->state([
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addMonth(),
        ]);
    }

    public function withExpiredSubscription()
    {
        return $this->state([
            'subscription_status' => 'expired',
            'subscription_expires_at' => now()->subDay(),
        ]);
    }
}

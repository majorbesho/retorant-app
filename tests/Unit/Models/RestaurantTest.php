<?php

namespace Tests\Unit\Models;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a restaurant
     */
    public function test_can_create_restaurant(): void
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::create([
            // 'user_id' => $user->id, // Removed as it doesn't exist
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'Test Restaurant',
            'name_translations' => ['en' => 'Test Restaurant', 'ar' => 'مطعم الاختبار'],
            'description' => 'A test restaurant',
            'description_translations' => ['en' => 'A test restaurant', 'ar' => 'مطعم اختبار'],
            'phone' => '+966501234567',
            'email' => 'test@restaurant.com',
            'address' => '123 Main St',
            'city' => 'Riyadh',
            'area' => 'Olaya',
            'is_active' => true,
            'slug' => 'test-restaurant', // Explicit slug to avoid generation issues in basic unit test
            'type' => 'restaurant',
            'cuisine_type' => 'International',
            'cuisine_tags' => [],
        ]);

        $this->assertDatabaseHas('restaurants', [
            'id' => $restaurant->id,
            'name' => 'Test Restaurant',
        ]);
    }

    /**
     * Test restaurant has owner (user)
     */
    public function test_restaurant_has_owner(): void
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create(['restaurant_id' => $restaurant->id]);

        $this->assertTrue($restaurant->owner->is($user));
    }

    /**
     * Test restaurant has many menus
     */
    public function test_restaurant_has_many_menus(): void
    {
        $restaurant = Restaurant::factory()->create();
        $menus = $restaurant->menus()->createMany([
            ['name' => 'Breakfast', 'name_translations' => ['en' => 'Breakfast', 'ar' => 'الإفطار']],
            ['name' => 'Lunch', 'name_translations' => ['en' => 'Lunch', 'ar' => 'الغداء']],
        ]);

        $this->assertCount(2, $restaurant->menus);
        $this->assertTrue($restaurant->menus()->first()->is($menus[0]));
    }

    /**
     * Test restaurant settings casting
     */
    public function test_restaurant_settings_casting(): void
    {
        $settings = [
            'language' => 'ar',
            'timezone' => 'Asia/Riyadh',
            'currency' => 'SAR',
        ];

        $restaurant = Restaurant::factory()->create([
            'settings' => $settings,
        ]);

        $this->assertIsArray($restaurant->settings);
        $this->assertEquals('ar', $restaurant->settings['language']);
    }

    /**
     * Test restaurant is active scope
     */
    public function test_restaurant_active_scope(): void
    {
        Restaurant::factory()->count(3)->create(['is_active' => true]);
        Restaurant::factory()->count(2)->create(['is_active' => false]);

        $active = Restaurant::active()->get();
        $this->assertCount(3, $active);
    }

    /**
     * Test restaurant can update settings
     */
    public function test_restaurant_can_update_settings(): void
    {
        $restaurant = Restaurant::factory()->create([
            'settings' => ['language' => 'en'],
        ]);

        $restaurant->update([
            'settings' => ['language' => 'ar', 'timezone' => 'Asia/Dubai'],
        ]);

        $this->assertEquals('ar', $restaurant->fresh()->settings['language']);
        $this->assertEquals('Asia/Dubai', $restaurant->fresh()->settings['timezone']);
    }
}

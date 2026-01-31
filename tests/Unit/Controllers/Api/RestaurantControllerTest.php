<?php

namespace Tests\Unit\Controllers\Api;

use Tests\TestCase;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RestaurantControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // ========================================
    // GET Tests (Public)
    // ========================================

    public function test_can_get_all_restaurants()
    {
        Restaurant::factory(5)->create();

        $response = $this->getJson('/api/restaurants');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'message',
                'count'
            ])
            ->assertJsonPath('success', true)
            ->assertJsonPath('count', 5);
    }

    public function test_restaurants_pagination_works()
    {
        Restaurant::factory(20)->create();

        $response = $this->getJson('/api/restaurants?per_page=10');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data');
    }

    public function test_can_get_single_restaurant()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->getJson("/api/restaurants/{$restaurant->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $restaurant->id)
            ->assertJsonPath('data.name', $restaurant->name);
    }

    public function test_can_get_active_restaurants()
    {
        Restaurant::factory(3)->create(['is_active' => true]);
        Restaurant::factory(2)->create(['is_active' => false]);

        $response = $this->getJson('/api/restaurants/active');

        $response->assertStatus(200)
            ->assertJsonPath('count', 3);
    }

    public function test_can_get_restaurants_by_city()
    {
        Restaurant::factory(3)->create(['city' => 'الرياض']);
        Restaurant::factory(2)->create(['city' => 'جدة']);

        $response = $this->getJson('/api/restaurants/city/الرياض');

        $response->assertStatus(200)
            ->assertJsonPath('count', 3);
    }

    public function test_can_get_restaurants_by_cuisine()
    {
        Restaurant::factory(3)->create(['cuisine_type' => 'عربي']);
        Restaurant::factory(2)->create(['cuisine_type' => 'آسيوي']);

        $response = $this->getJson('/api/restaurants/cuisine/عربي');

        $response->assertStatus(200)
            ->assertJsonPath('count', 3);
    }

    public function test_can_get_top_rated_restaurants()
    {
        Restaurant::factory()->create(['rating' => 5.0]);
        Restaurant::factory()->create(['rating' => 4.5]);
        Restaurant::factory()->create(['rating' => 3.0]);

        $response = $this->getJson('/api/restaurants/top-rated?limit=2');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_search_restaurants()
    {
        Restaurant::factory()->create(['name' => 'مطعم الذوق']);
        Restaurant::factory()->create(['name' => 'مطعم الرياض']);

        $response = $this->getJson('/api/restaurants/search?q=الذوق');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    // ========================================
    // POST Tests (Protected)
    // ========================================

    public function test_cannot_create_restaurant_without_auth()
    {
        $response = $this->postJson('/api/restaurants', [
            'name' => 'مطعم جديد'
        ]);

        $response->assertStatus(401);
    }

    public function test_can_create_restaurant_with_auth()
    {
        $data = [
            'name' => 'مطعم جديد',
            'name_translations' => [
                'ar' => 'مطعم جديد',
                'en' => 'New Restaurant'
            ],
            'cuisine_type' => 'عربي',
            'country' => 'السعودية',
            'city' => 'الرياض',
            'address' => 'شارع التحلية'
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/restaurants', $data);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'مطعم جديد');
    }

    public function test_create_restaurant_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/restaurants', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'cuisine_type', 'country', 'city', 'address']);
    }

    // ========================================
    // PUT Tests (Protected)
    // ========================================

    public function test_can_update_restaurant()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($this->user)
            ->putJson("/api/restaurants/{$restaurant->id}", [
                'name' => 'مطعم مُحدّث'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'مطعم مُحدّث');

        $this->assertDatabaseHas('restaurants', [
            'id' => $restaurant->id,
            'name' => 'مطعم مُحدّث'
        ]);
    }

    // ========================================
    // DELETE Tests (Protected)
    // ========================================

    public function test_can_delete_restaurant()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/restaurants/{$restaurant->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('restaurants', [
            'id' => $restaurant->id
        ]);
    }

    // ========================================
    // Error Tests
    // ========================================

    public function test_returns_404_for_nonexistent_restaurant()
    {
        $response = $this->getJson('/api/restaurants/99999');

        $response->assertStatus(404);
    }
}

<?php

namespace Tests\Feature\API;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RestaurantAPITest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create(['user_id' => $this->user->id]);
    }

    /**
     * Test can get all restaurants
     */
    public function test_can_get_all_restaurants(): void
    {
        Restaurant::factory()->count(5)->create();

        $response = $this->getJson('/api/restaurants');

        $response->assertStatus(200);
        $response->assertJsonStructure(['*' => ['id', 'name', 'email', 'phone']]);
    }

    /**
     * Test can get active restaurants only
     */
    public function test_can_get_active_restaurants(): void
    {
        Restaurant::factory()->count(3)->create(['is_active' => true]);
        Restaurant::factory()->count(2)->create(['is_active' => false]);

        $response = $this->getJson('/api/restaurants/active');

        $response->assertStatus(200);
    }

    /**
     * Test can get restaurants by city
     */
    public function test_can_get_restaurants_by_city(): void
    {
        Restaurant::factory()->create(['city' => 'Riyadh']);
        Restaurant::factory()->create(['city' => 'Jeddah']);

        $response = $this->getJson('/api/restaurants/city/Riyadh');

        $response->assertStatus(200);
    }

    /**
     * Test can get restaurant by ID
     */
    public function test_can_get_restaurant_by_id(): void
    {
        $response = $this->getJson("/api/restaurants/{$this->restaurant->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('id', $this->restaurant->id);
        $response->assertJsonPath('name', $this->restaurant->name);
    }

    /**
     * Test can create restaurant as authenticated user
     */
    public function test_can_create_restaurant(): void
    {
        Sanctum::actingAs($this->user);

        $data = [
            'name' => 'New Restaurant',
            'description' => 'A new test restaurant',
            'phone' => '+966501234567',
            'email' => 'new@restaurant.com',
            'address' => '123 Main St',
            'city' => 'Riyadh',
        ];

        $response = $this->postJson('/api/restaurants', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('restaurants', ['name' => 'New Restaurant']);
    }

    /**
     * Test can update restaurant
     */
    public function test_can_update_restaurant(): void
    {
        Sanctum::actingAs($this->user);

        $data = ['name' => 'Updated Restaurant Name'];

        $response = $this->patchJson("/api/restaurants/{$this->restaurant->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('restaurants', [
            'id' => $this->restaurant->id,
            'name' => 'Updated Restaurant Name',
        ]);
    }

    /**
     * Test cannot create restaurant without authentication
     */
    public function test_cannot_create_restaurant_without_auth(): void
    {
        $data = [
            'name' => 'Unauthorized Restaurant',
            'phone' => '+966501234567',
            'email' => 'unauthorized@restaurant.com',
        ];

        $response = $this->postJson('/api/restaurants', $data);

        $response->assertStatus(401);
    }

    /**
     * Test cannot update restaurant without ownership
     */
    public function test_cannot_update_restaurant_without_ownership(): void
    {
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);

        $data = ['name' => 'Hacked Name'];
        $response = $this->patchJson("/api/restaurants/{$this->restaurant->id}", $data);

        $response->assertStatus(403);
    }

    /**
     * Test can delete restaurant
     */
    public function test_can_delete_restaurant(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson("/api/restaurants/{$this->restaurant->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('restaurants', ['id' => $this->restaurant->id]);
    }

    /**
     * Test can search restaurants
     */
    public function test_can_search_restaurants(): void
    {
        Restaurant::factory()->create(['name' => 'Pizza Palace']);
        Restaurant::factory()->create(['name' => 'Burger King']);

        $response = $this->getJson('/api/restaurants/search?q=pizza');

        $response->assertStatus(200);
    }

    /**
     * Test can get top rated restaurants
     */
    public function test_can_get_top_rated_restaurants(): void
    {
        $response = $this->getJson('/api/restaurants/top-rated');

        $response->assertStatus(200);
    }
}

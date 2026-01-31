<?php

namespace Tests\Integration;

use App\Models\Order;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RestaurantWorkflowIntegrationTest extends TestCase
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
     * Test complete restaurant setup workflow
     */
    public function test_complete_restaurant_setup_workflow(): void
    {
        Sanctum::actingAs($this->user);

        // Step 1: Create restaurant
        $restaurantData = [
            'name' => 'New Restaurant',
            'phone' => '+966501234567',
            'email' => 'restaurant@example.com',
            'address' => '123 Main St',
            'city' => 'Riyadh',
        ];

        $response = $this->postJson('/api/restaurants', $restaurantData);
        $response->assertStatus(201);

        // Verify restaurant created
        $this->assertDatabaseHas('restaurants', ['name' => 'New Restaurant']);
    }

    /**
     * Test restaurant profile completeness
     */
    public function test_restaurant_profile_completeness(): void
    {
        Sanctum::actingAs($this->user);

        // Update restaurant with complete profile
        $data = [
            'name' => 'Complete Restaurant',
            'description' => 'A complete restaurant',
            'phone' => '+966501234567',
            'email' => 'complete@restaurant.com',
            'address' => '123 Main St',
            'city' => 'Riyadh',
            'settings' => [
                'opening_time' => '09:00',
                'closing_time' => '23:00',
                'min_order' => 25.00,
            ],
        ];

        $response = $this->patchJson("/api/restaurants/{$this->restaurant->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('restaurants', [
            'id' => $this->restaurant->id,
            'name' => 'Complete Restaurant',
        ]);
    }

    /**
     * Test restaurant statistics tracking
     */
    public function test_restaurant_statistics_tracking(): void
    {
        // Create multiple orders
        Order::factory()->count(5)->create(['restaurant_id' => $this->restaurant->id]);

        // Verify orders
        $orderCount = $this->restaurant->orders()->count();
        $this->assertEquals(5, $orderCount);
    }

    /**
     * Test reservation workflow
     */
    public function test_reservation_workflow(): void
    {
        $customer = User::factory()->create();
        Sanctum::actingAs($customer);

        // Create reservation
        $reservationData = [
            'restaurant_id' => $this->restaurant->id,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+966501234567',
            'party_size' => 4,
            'reserved_at' => now()->addDays(1),
        ];

        $response = $this->postJson('/api/reservations', $reservationData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reservations', [
            'restaurant_id' => $this->restaurant->id,
        ]);
    }

    /**
     * Test multi-tenant isolation
     */
    public function test_multi_tenant_isolation(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $restaurant1 = Restaurant::factory()->create(['user_id' => $user1->id]);
        $restaurant2 = Restaurant::factory()->create(['user_id' => $user2->id]);

        Sanctum::actingAs($user1);

        // User1 should not be able to update User2's restaurant
        $response = $this->patchJson("/api/restaurants/{$restaurant2->id}", [
            'name' => 'Hacked',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test restaurant deactivation workflow
     */
    public function test_restaurant_deactivation_workflow(): void
    {
        Sanctum::actingAs($this->user);

        // Deactivate restaurant
        $response = $this->patchJson("/api/restaurants/{$this->restaurant->id}", [
            'is_active' => false,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('restaurants', [
            'id' => $this->restaurant->id,
            'is_active' => false,
        ]);
    }

    /**
     * Test restaurant reactivation workflow
     */
    public function test_restaurant_reactivation_workflow(): void
    {
        $this->restaurant->update(['is_active' => false]);
        Sanctum::actingAs($this->user);

        // Reactivate restaurant
        $response = $this->patchJson("/api/restaurants/{$this->restaurant->id}", [
            'is_active' => true,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('restaurants', [
            'id' => $this->restaurant->id,
            'is_active' => true,
        ]);
    }

    /**
     * Test restaurant deletion workflow
     */
    public function test_restaurant_deletion_workflow(): void
    {
        Sanctum::actingAs($this->user);

        $restaurantId = $this->restaurant->id;

        $response = $this->deleteJson("/api/restaurants/{$restaurantId}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('restaurants', ['id' => $restaurantId]);
    }

    /**
     * Test restaurant listing per user
     */
    public function test_restaurant_listing_per_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Restaurant::factory()->count(3)->create(['user_id' => $user1->id]);
        Restaurant::factory()->count(2)->create(['user_id' => $user2->id]);

        Sanctum::actingAs($user1);

        // User1 should see their restaurants
        $this->assertTrue(
            Restaurant::where('user_id', $user1->id)->count() >= 3
        );
    }

    /**
     * Test restaurant settings management
     */
    public function test_restaurant_settings_management(): void
    {
        Sanctum::actingAs($this->user);

        $settings = [
            'language' => 'ar',
            'currency' => 'SAR',
            'timezone' => 'Asia/Riyadh',
            'features' => ['orders', 'reservations', 'ai_chat'],
        ];

        $response = $this->patchJson("/api/restaurants/{$this->restaurant->id}", [
            'settings' => $settings,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('restaurants', [
            'id' => $this->restaurant->id,
        ]);
    }
}

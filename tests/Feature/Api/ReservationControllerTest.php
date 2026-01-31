<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create();
    }

    // ========================================
    // Complete CRUD Flow Test
    // ========================================

    public function test_complete_reservation_lifecycle()
    {
        // 1. Create reservation
        $createData = [
            'customer_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'party_size' => 4,
            'guest_name' => 'أحمد محمد',
            'guest_phone' => '+966501234567',
            'guest_email' => 'ahmad@example.com',
            'status' => 'pending'
        ];

        $createResponse = $this->actingAs($this->user)
            ->postJson('/api/reservations', $createData);

        $createResponse->assertStatus(201);
        $reservationId = $createResponse->json('data.id');

        // 2. Confirm reservation
        $confirmResponse = $this->actingAs($this->user)
            ->postJson("/api/reservations/{$reservationId}/confirm");

        $confirmResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'confirmed');

        // 3. Check-in
        $checkInResponse = $this->actingAs($this->user)
            ->postJson("/api/reservations/{$reservationId}/check-in");

        $checkInResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'checked_in');

        // 4. Complete
        $completeResponse = $this->actingAs($this->user)
            ->postJson("/api/reservations/{$reservationId}/complete");

        $completeResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'completed');
    }

    // ========================================
    // Validation Tests
    // ========================================

    public function test_create_reservation_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/reservations', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'customer_id',
                'restaurant_id',
                'reservation_date',
                'party_size',
                'guest_name',
                'guest_phone',
                'guest_email',
                'status'
            ]);
    }

    public function test_reservation_date_must_be_in_future()
    {
        $data = [
            'customer_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => now()->subDay()->format('Y-m-d H:i:s'),
            'party_size' => 4,
            'guest_name' => 'أحمد',
            'guest_phone' => '+966501234567',
            'guest_email' => 'test@example.com',
            'status' => 'pending'
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/reservations', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('reservation_date');
    }

    public function test_party_size_must_be_valid()
    {
        $data = [
            'customer_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'party_size' => 0,
            'guest_name' => 'أحمد',
            'guest_phone' => '+966501234567',
            'guest_email' => 'test@example.com',
            'status' => 'pending'
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/reservations', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('party_size');
    }

    // ========================================
    // Filter Tests
    // ========================================

    public function test_can_filter_reservations_by_status()
    {
        Reservation::factory(3)
            ->for($this->user, 'customer')
            ->for($this->restaurant)
            ->create(['status' => 'confirmed']);

        Reservation::factory(2)
            ->for($this->user, 'customer')
            ->for($this->restaurant)
            ->create(['status' => 'pending']);

        $response = $this->actingAs($this->user)
            ->getJson('/api/reservations/status/confirmed');

        $response->assertStatus(200)
            ->assertJsonPath('count', 3);
    }

    public function test_can_get_customer_reservations()
    {
        $customer = User::factory()->create();

        Reservation::factory(4)
            ->for($customer, 'customer')
            ->for($this->restaurant)
            ->create();

        Reservation::factory(2)
            ->for($this->user, 'customer')
            ->for($this->restaurant)
            ->create();

        $response = $this->actingAs($this->user)
            ->getJson("/api/reservations/customer/{$customer->id}");

        $response->assertStatus(200)
            ->assertJsonPath('count', 4);
    }

    public function test_can_get_restaurant_reservations()
    {
        $restaurant1 = $this->restaurant;
        $restaurant2 = Restaurant::factory()->create();

        Reservation::factory(3)
            ->for($this->user, 'customer')
            ->for($restaurant1)
            ->create();

        Reservation::factory(2)
            ->for($this->user, 'customer')
            ->for($restaurant2)
            ->create();

        $response = $this->actingAs($this->user)
            ->getJson("/api/reservations/restaurant/{$restaurant1->id}");

        $response->assertStatus(200)
            ->assertJsonPath('count', 3);
    }

    // ========================================
    // Cancel Test
    // ========================================

    public function test_can_cancel_reservation_with_reason()
    {
        $reservation = Reservation::factory()
            ->for($this->user, 'customer')
            ->for($this->restaurant)
            ->create(['status' => 'pending']);

        $response = $this->actingAs($this->user)
            ->postJson("/api/reservations/{$reservation->id}/cancel", [
                'cancellation_reason' => 'طارئ عائلي'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'cancelled'
        ]);
    }

    // ========================================
    // Update Test
    // ========================================

    public function test_can_update_reservation()
    {
        $reservation = Reservation::factory()
            ->for($this->user, 'customer')
            ->for($this->restaurant)
            ->create();

        $response = $this->actingAs($this->user)
            ->putJson("/api/reservations/{$reservation->id}", [
                'party_size' => 6,
                'special_requests' => 'طاولة بجانب النافذة'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.party_size', 6)
            ->assertJsonPath('data.special_requests', 'طاولة بجانب النافذة');
    }

    // ========================================
    // Authorization Tests
    // ========================================

    public function test_cannot_create_reservation_without_auth()
    {
        $response = $this->postJson('/api/reservations', []);

        $response->assertStatus(401);
    }

    public function test_cannot_cancel_without_reason()
    {
        $reservation = Reservation::factory()
            ->for($this->user, 'customer')
            ->for($this->restaurant)
            ->create();

        $response = $this->actingAs($this->user)
            ->postJson("/api/reservations/{$reservation->id}/cancel", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('cancellation_reason');
    }

    // ========================================
    // Pending Reservations Test
    // ========================================

    public function test_can_get_pending_reservations()
    {
        Reservation::factory(4)
            ->for($this->user, 'customer')
            ->for($this->restaurant)
            ->create(['status' => 'pending']);

        Reservation::factory(2)
            ->for($this->user, 'customer')
            ->for($this->restaurant)
            ->create(['status' => 'confirmed']);

        $response = $this->actingAs($this->user)
            ->getJson('/api/reservations/pending');

        $response->assertStatus(200)
            ->assertJsonPath('count', 4);
    }
}

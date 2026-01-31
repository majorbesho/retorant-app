<?php

namespace Tests\Unit\Controllers\Api;

use Tests\TestCase;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Product;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $restaurant;
    protected $product;
    protected $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create();
        $this->product = Product::factory()->create(['restaurant_id' => $this->restaurant->id]);
        $this->paymentMethod = PaymentMethod::factory()->create();
    }

    // ========================================
    // GET Tests (Protected)
    // ========================================

    public function test_can_get_all_orders()
    {
        Order::factory(5)->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('count', 5);
    }

    public function test_can_get_pending_orders()
    {
        Order::factory(3)->create(['status' => 'pending']);
        Order::factory(2)->create(['status' => 'confirmed']);

        $response = $this->actingAs($this->user)
            ->getJson('/api/orders/pending');

        $response->assertStatus(200)
            ->assertJsonPath('count', 3);
    }

    public function test_can_get_orders_by_status()
    {
        Order::factory(4)->create(['status' => 'delivered']);
        Order::factory(1)->create(['status' => 'pending']);

        $response = $this->actingAs($this->user)
            ->getJson('/api/orders/status/delivered');

        $response->assertStatus(200)
            ->assertJsonPath('count', 4);
    }

    public function test_can_get_customer_orders()
    {
        $customer = User::factory()->create();
        Order::factory(3)->create(['customer_id' => $customer->id]);
        Order::factory(2)->create();

        $response = $this->actingAs($this->user)
            ->getJson("/api/orders/customer/{$customer->id}");

        $response->assertStatus(200)
            ->assertJsonPath('count', 3);
    }

    public function test_can_get_single_order()
    {
        $order = Order::factory()->create();

        $response = $this->actingAs($this->user)
            ->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $order->id);
    }

    // ========================================
    // POST Tests (Protected)
    // ========================================

    public function test_cannot_create_order_without_auth()
    {
        $response = $this->postJson('/api/orders', []);

        $response->assertStatus(401);
    }

    public function test_can_create_order()
    {
        $data = [
            'customer_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'total_amount' => 150.00,
            'discount_amount' => 10.00,
            'tax_amount' => 12.00,
            'delivery_fee' => 5.00,
            'delivery_address' => 'شارع الملك فهد',
            'payment_method_id' => $this->paymentMethod->id,
            'status' => 'pending',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'unit_price' => 50.00
                ]
            ]
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/orders', $data);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $this->user->id,
            'total_amount' => 150.00
        ]);
    }

    public function test_create_order_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/orders', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'customer_id',
                'restaurant_id',
                'total_amount',
                'delivery_address',
                'payment_method_id',
                'status',
                'items'
            ]);
    }

    // ========================================
    // PUT Tests (Protected)
    // ========================================

    public function test_can_update_order()
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->user)
            ->putJson("/api/orders/{$order->id}", [
                'status' => 'confirmed'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'confirmed');
    }

    // ========================================
    // POST Custom Action Tests
    // ========================================

    public function test_can_cancel_order()
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->user)
            ->postJson("/api/orders/{$order->id}/cancel", [
                'cancellation_reason' => 'تم الطلب من مطعم آخر'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled'
        ]);
    }

    // ========================================
    // DELETE Tests (Protected)
    // ========================================

    public function test_can_delete_order()
    {
        $order = Order::factory()->create();

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    // ========================================
    // Error Tests
    // ========================================

    public function test_returns_404_for_nonexistent_order()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/orders/99999');

        $response->assertStatus(404);
    }
}

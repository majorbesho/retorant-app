<?php

namespace Tests\Feature\API;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderAPITest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $restaurant;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create();
        $this->product = Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'price' => 50.00,
            'quantity' => 100,
        ]);
    }

    /**
     * Test can get all orders
     */
    public function test_can_get_all_orders(): void
    {
        Sanctum::actingAs($this->user);
        Order::factory()->count(5)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200);
        $response->assertJsonStructure(['*' => ['id', 'order_number', 'total_amount', 'status']]);
    }

    /**
     * Test can get order by ID
     */
    public function test_can_get_order_by_id(): void
    {
        Sanctum::actingAs($this->user);
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('id', $order->id);
    }

    /**
     * Test can create order
     */
    public function test_can_create_order(): void
    {
        Sanctum::actingAs($this->user);

        $data = [
            'restaurant_id' => $this->restaurant->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'price' => 50.00,
                ]
            ],
            'total_amount' => 100.00,
            'delivery_type' => 'delivery',
            'payment_method' => 'card',
            'delivery_address' => '123 Main St',
        ];

        $response = $this->postJson('/api/orders', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', ['user_id' => $this->user->id]);
    }

    /**
     * Test can update order status
     */
    public function test_can_update_order_status(): void
    {
        Sanctum::actingAs($this->user);
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->patchJson("/api/orders/{$order->id}", [
            'status' => 'confirmed',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'confirmed',
        ]);
    }

    /**
     * Test can cancel order
     */
    public function test_can_cancel_order(): void
    {
        Sanctum::actingAs($this->user);
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/orders/{$order->id}/cancel");

        $response->assertStatus(200);
    }

    /**
     * Test cannot create order with insufficient stock
     */
    public function test_cannot_create_order_with_insufficient_stock(): void
    {
        Sanctum::actingAs($this->user);
        $product = Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'quantity' => 1,
        ]);

        $data = [
            'restaurant_id' => $this->restaurant->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10, // More than available
                ]
            ],
        ];

        $response = $this->postJson('/api/orders', $data);

        $response->assertStatus(422);
    }

    /**
     * Test cannot view other user's orders
     */
    public function test_cannot_view_other_user_orders(): void
    {
        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        Sanctum::actingAs($this->user);
        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(403);
    }

    /**
     * Test order includes items
     */
    public function test_order_includes_items(): void
    {
        Sanctum::actingAs($this->user);
        $order = Order::factory()->create(['user_id' => $this->user->id]);
        OrderItem::factory()->create(['order_id' => $order->id]);

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'items' => ['*' => ['id', 'product_id', 'quantity']]]);
    }

    /**
     * Test can get user's active orders
     */
    public function test_can_get_user_active_orders(): void
    {
        Sanctum::actingAs($this->user);
        Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);
        Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'completed',
        ]);

        $response = $this->getJson('/api/orders?status=pending');

        $response->assertStatus(200);
    }

    /**
     * Test order total amount validation
     */
    public function test_order_total_amount_validation(): void
    {
        Sanctum::actingAs($this->user);

        $data = [
            'restaurant_id' => $this->restaurant->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                ]
            ],
            'total_amount' => -100.00, // Invalid negative amount
        ];

        $response = $this->postJson('/api/orders', $data);

        $response->assertStatus(422);
    }
}

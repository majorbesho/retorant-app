<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating an order
     */
    public function test_can_create_order(): void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
            'order_number' => 'ORD-001',
            'total_amount' => 150.00,
            'status' => 'pending',
            'payment_method' => 'card',
            'delivery_type' => 'delivery',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_number' => 'ORD-001',
        ]);
    }

    /**
     * Test order belongs to user
     */
    public function test_order_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($order->user()->is($user));
    }

    /**
     * Test order belongs to restaurant
     */
    public function test_order_belongs_to_restaurant(): void
    {
        $restaurant = Restaurant::factory()->create();
        $order = Order::factory()->create(['restaurant_id' => $restaurant->id]);

        $this->assertTrue($order->restaurant()->is($restaurant));
    }

    /**
     * Test order has many items
     */
    public function test_order_has_many_items(): void
    {
        $order = Order::factory()->create();
        $product = Product::factory()->create();

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 50.00,
        ]);

        $this->assertCount(1, $order->items);
    }

    /**
     * Test order status tracking
     */
    public function test_order_status_tracking(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $this->assertEquals('pending', $order->status);

        $order->update(['status' => 'confirmed']);
        $this->assertEquals('confirmed', $order->fresh()->status);
    }

    /**
     * Test order payment method
     */
    public function test_order_payment_method(): void
    {
        $order = Order::factory()->create(['payment_method' => 'card']);

        $this->assertEquals('card', $order->payment_method);
    }

    /**
     * Test order delivery type
     */
    public function test_order_delivery_type(): void
    {
        $order = Order::factory()->create(['delivery_type' => 'pickup']);

        $this->assertEquals('pickup', $order->delivery_type);
    }

    /**
     * Test order total amount calculation
     */
    public function test_order_total_amount(): void
    {
        $order = Order::factory()->create(['total_amount' => 250.50]);

        $this->assertEquals(250.50, $order->total_amount);
    }

    /**
     * Test pending orders scope
     */
    public function test_pending_orders_scope(): void
    {
        Order::factory()->create(['status' => 'pending']);
        Order::factory()->create(['status' => 'pending']);
        Order::factory()->create(['status' => 'completed']);

        $pending = Order::where('status', 'pending')->get();
        $this->assertCount(2, $pending);
    }

    /**
     * Test order cancellation
     */
    public function test_order_can_be_cancelled(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $order->update(['status' => 'cancelled']);

        $this->assertEquals('cancelled', $order->fresh()->status);
    }
}

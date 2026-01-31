<?php

namespace Tests\Integration;

use App\Models\Conversation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderToConversationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test complete order workflow with conversation logging
     */
    public function test_complete_order_workflow_with_conversation(): void
    {
        // Setup
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $product = Product::factory()->create([
            'restaurant_id' => $restaurant->id,
            'price' => 45.00,
            'quantity' => 100,
        ]);

        Sanctum::actingAs($user);

        // Step 1: Customer creates conversation (asks about order)
        $conversationData = [
            'restaurant_id' => $restaurant->id,
            'customer_identifier' => 'whatsapp_+966501234567',
            'messages' => [
                ['role' => 'user', 'content' => 'Can I order the grilled chicken?'],
            ],
        ];

        $conversationResponse = $this->postJson('/api/conversations', $conversationData);
        $conversationResponse->assertStatus(201);
        $conversation = $conversationResponse->json();

        // Step 2: Create order
        $orderData = [
            'restaurant_id' => $restaurant->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ]
            ],
            'total_amount' => 90.00,
            'delivery_type' => 'delivery',
            'payment_method' => 'card',
        ];

        $orderResponse = $this->postJson('/api/orders', $orderData);
        $orderResponse->assertStatus(201);
        $order = $orderResponse->json();

        // Step 3: Log order confirmation in conversation
        $messageData = [
            'role' => 'assistant',
            'content' => "Order #{$order['order_number']} confirmed! Total: SAR {$order['total_amount']}",
        ];

        $messageResponse = $this->postJson(
            "/api/conversations/{$conversation['id']}/messages",
            $messageData
        );
        $messageResponse->assertStatus(200);

        // Verify
        $finalConversation = $this->getJson("/api/conversations/{$conversation['id']}")->json();
        $this->assertCount(2, $finalConversation['messages']);
        $this->assertDatabaseHas('conversations', ['id' => $conversation['id']]);
        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
    }

    /**
     * Test restaurant context retrieval for AI
     */
    public function test_restaurant_context_retrieval_for_ai(): void
    {
        $restaurant = Restaurant::factory()->create([
            'name' => 'Elite Pizza',
            'phone' => '+966501234567',
            'settings' => ['language' => 'ar'],
        ]);

        $products = Product::factory()->count(5)->create([
            'restaurant_id' => $restaurant->id,
        ]);

        // Verify context is retrievable
        $this->assertDatabaseHas('restaurants', ['id' => $restaurant->id]);
        $this->assertCount(5, $restaurant->products);
    }

    /**
     * Test multi-step order creation with validation
     */
    public function test_multi_step_order_creation_with_validation(): void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $products = [
            Product::factory()->create([
                'restaurant_id' => $restaurant->id,
                'price' => 30.00,
                'quantity' => 50,
            ]),
            Product::factory()->create([
                'restaurant_id' => $restaurant->id,
                'price' => 25.00,
                'quantity' => 50,
            ]),
        ];

        Sanctum::actingAs($user);

        // Create order with multiple items
        $orderData = [
            'restaurant_id' => $restaurant->id,
            'items' => [
                [
                    'product_id' => $products[0]->id,
                    'quantity' => 1,
                ],
                [
                    'product_id' => $products[1]->id,
                    'quantity' => 2,
                ]
            ],
            'total_amount' => 80.00,
            'delivery_type' => 'delivery',
            'payment_method' => 'card',
        ];

        $response = $this->postJson('/api/orders', $orderData);
        $response->assertStatus(201);

        $order = $response->json();
        $this->assertCount(2, $order['items'] ?? []);
    }

    /**
     * Test inventory management across orders
     */
    public function test_inventory_management_across_orders(): void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $product = Product::factory()->create([
            'restaurant_id' => $restaurant->id,
            'price' => 50.00,
            'quantity' => 5, // Only 5 units available
        ]);

        Sanctum::actingAs($user);

        // First order: 3 units
        $order1Data = [
            'restaurant_id' => $restaurant->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3]
            ],
            'total_amount' => 150.00,
        ];

        $response1 = $this->postJson('/api/orders', $order1Data);
        $response1->assertStatus(201);

        // Second order: 3 units (should fail - only 2 left)
        $order2Data = [
            'restaurant_id' => $restaurant->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3]
            ],
            'total_amount' => 150.00,
        ];

        $response2 = $this->postJson('/api/orders', $order2Data);
        $response2->assertStatus(422); // Should fail due to insufficient stock
    }

    /**
     * Test conversation analytics
     */
    public function test_conversation_analytics(): void
    {
        $restaurant = Restaurant::factory()->create();

        $conversations = Conversation::factory()->count(5)->create([
            'restaurant_id' => $restaurant->id,
        ]);

        // Verify conversations exist
        $this->assertCount(5, $restaurant->conversations);

        // Update sentiments
        $conversations[0]->update(['sentiment' => 'positive']);
        $conversations[1]->update(['sentiment' => 'positive']);
        $conversations[2]->update(['sentiment' => 'negative']);

        $positive = Conversation::where('restaurant_id', $restaurant->id)
            ->where('sentiment', 'positive')
            ->count();

        $this->assertEquals(2, $positive);
    }

    /**
     * Test order to conversation mapping
     */
    public function test_order_to_conversation_mapping(): void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $product = Product::factory()->create(['restaurant_id' => $restaurant->id]);

        Sanctum::actingAs($user);

        // Create order
        $orderResponse = $this->postJson('/api/orders', [
            'restaurant_id' => $restaurant->id,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'total_amount' => 50.00,
        ]);

        $order = $orderResponse->json();

        // Create conversation with order reference
        $conversationResponse = $this->postJson('/api/conversations', [
            'restaurant_id' => $restaurant->id,
            'customer_identifier' => 'order_' . $order['id'],
            'messages' => [['role' => 'user', 'content' => 'Where is my order?']],
        ]);

        $conversation = $conversationResponse->json();

        // Verify relationship
        $this->assertDatabaseHas('conversations', [
            'id' => $conversation['id'],
            'restaurant_id' => $restaurant->id,
        ]);
    }

    /**
     * Test concurrent order handling
     */
    public function test_concurrent_order_handling(): void
    {
        $users = User::factory()->count(3)->create();
        $restaurant = Restaurant::factory()->create();

        $product = Product::factory()->create([
            'restaurant_id' => $restaurant->id,
            'quantity' => 100,
        ]);

        // All users create orders
        foreach ($users as $user) {
            Sanctum::actingAs($user);

            $response = $this->postJson('/api/orders', [
                'restaurant_id' => $restaurant->id,
                'items' => [['product_id' => $product->id, 'quantity' => 1]],
                'total_amount' => 50.00,
            ]);

            $response->assertStatus(201);
        }

        // Verify all orders created
        $this->assertCount(3, Order::all());
    }
}

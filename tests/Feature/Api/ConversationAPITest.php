<?php

namespace Tests\Feature\API;

use App\Models\Conversation;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ConversationAPITest extends TestCase
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
     * Test can create conversation
     */
    public function test_can_create_conversation(): void
    {
        Sanctum::actingAs($this->user);

        $data = [
            'restaurant_id' => $this->restaurant->id,
            'customer_identifier' => 'customer_123',
            'messages' => [
                ['role' => 'user', 'content' => 'Hello'],
            ],
        ];

        $response = $this->postJson('/api/conversations', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('conversations', [
            'customer_identifier' => 'customer_123',
        ]);
    }

    /**
     * Test can get conversation
     */
    public function test_can_get_conversation(): void
    {
        Sanctum::actingAs($this->user);
        $conversation = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);

        $response = $this->getJson("/api/conversations/{$conversation->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('id', $conversation->id);
    }

    /**
     * Test can get conversation messages
     */
    public function test_can_get_conversation_messages(): void
    {
        Sanctum::actingAs($this->user);
        $messages = [
            ['role' => 'user', 'content' => 'What time are you open?'],
            ['role' => 'assistant', 'content' => 'We are open 9 AM to 11 PM'],
        ];

        $conversation = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'messages' => $messages,
        ]);

        $response = $this->getJson("/api/conversations/{$conversation->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'messages' => ['*' => ['role', 'content']]]);
    }

    /**
     * Test can update conversation
     */
    public function test_can_update_conversation(): void
    {
        Sanctum::actingAs($this->user);
        $conversation = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'sentiment' => 'neutral',
        ]);

        $response = $this->patchJson("/api/conversations/{$conversation->id}", [
            'sentiment' => 'positive',
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test can add message to conversation
     */
    public function test_can_add_message_to_conversation(): void
    {
        Sanctum::actingAs($this->user);
        $conversation = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'messages' => [['role' => 'user', 'content' => 'Hi']],
        ]);

        $data = [
            'role' => 'assistant',
            'content' => 'Hello! How can I help?',
        ];

        $response = $this->postJson("/api/conversations/{$conversation->id}/messages", $data);

        $response->assertStatus(200);
    }

    /**
     * Test can list conversations
     */
    public function test_can_list_conversations(): void
    {
        Sanctum::actingAs($this->user);
        Conversation::factory()->count(5)->create([
            'restaurant_id' => $this->restaurant->id,
        ]);

        $response = $this->getJson('/api/conversations');

        $response->assertStatus(200);
        $response->assertJsonStructure(['*' => ['id', 'customer_identifier', 'sentiment']]);
    }

    /**
     * Test can filter conversations by sentiment
     */
    public function test_can_filter_conversations_by_sentiment(): void
    {
        Sanctum::actingAs($this->user);
        Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'sentiment' => 'positive',
        ]);
        Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'sentiment' => 'negative',
        ]);

        $response = $this->getJson('/api/conversations?sentiment=positive');

        $response->assertStatus(200);
    }

    /**
     * Test conversation tracks token count
     */
    public function test_conversation_tracks_token_count(): void
    {
        Sanctum::actingAs($this->user);

        $conversation = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'token_count' => 150,
        ]);

        $response = $this->getJson("/api/conversations/{$conversation->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('token_count', 150);
    }

    /**
     * Test can escalate conversation
     */
    public function test_can_escalate_conversation(): void
    {
        Sanctum::actingAs($this->user);
        $conversation = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'escalation_status' => 'none',
        ]);

        $response = $this->postJson("/api/conversations/{$conversation->id}/escalate");

        $response->assertStatus(200);
    }

    /**
     * Test cannot view other restaurant's conversations
     */
    public function test_cannot_view_other_restaurant_conversations(): void
    {
        $otherRestaurant = Restaurant::factory()->create();
        $conversation = Conversation::factory()->create([
            'restaurant_id' => $otherRestaurant->id,
        ]);

        Sanctum::actingAs($this->user);
        $response = $this->getJson("/api/conversations/{$conversation->id}");

        $response->assertStatus(403);
    }

    /**
     * Test conversation message structure
     */
    public function test_conversation_message_structure(): void
    {
        Sanctum::actingAs($this->user);

        $messages = [
            [
                'role' => 'user',
                'content' => 'Do you have pizza?',
                'timestamp' => now(),
            ],
            [
                'role' => 'assistant',
                'content' => 'Yes, we have several pizza options.',
                'timestamp' => now(),
            ],
        ];

        $conversation = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'messages' => $messages,
        ]);

        $response = $this->getJson("/api/conversations/{$conversation->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'messages' => [
                '*' => ['role', 'content']
            ]
        ]);
    }
}

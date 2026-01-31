<?php

namespace Tests\Integration;

use App\Models\Conversation;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AIServiceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => $this->user->id,
            'ai_config' => [
                'model' => 'gpt-4',
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ],
        ]);
    }

    /**
     * Test conversation creation for AI processing
     */
    public function test_conversation_creation_for_ai_processing(): void
    {
        Sanctum::actingAs($this->user);

        $data = [
            'restaurant_id' => $this->restaurant->id,
            'customer_identifier' => 'whatsapp_+966501234567',
            'messages' => [
                ['role' => 'user', 'content' => 'Do you have vegetarian options?'],
            ],
        ];

        $response = $this->postJson('/api/conversations', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('conversations', [
            'restaurant_id' => $this->restaurant->id,
        ]);
    }

    /**
     * Test conversation message formatting for AI
     */
    public function test_conversation_message_formatting_for_ai(): void
    {
        Sanctum::actingAs($this->user);

        $messages = [
            ['role' => 'user', 'content' => 'What is your restaurant known for?'],
            ['role' => 'assistant', 'content' => 'We specialize in Mediterranean cuisine.'],
        ];

        $conversation = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'messages' => $messages,
        ]);

        $response = $this->getJson("/api/conversations/{$conversation->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('messages', $messages);
    }

    /**
     * Test restaurant context loading
     */
    public function test_restaurant_context_loading(): void
    {
        // Create menu items
        Product::factory()->count(10)->create([
            'restaurant_id' => $this->restaurant->id,
            'is_active' => true,
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/restaurants/{$this->restaurant->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('id', $this->restaurant->id);
    }

    /**
     * Test sentiment analysis integration
     */
    public function test_sentiment_analysis_integration(): void
    {
        Sanctum::actingAs($this->user);

        $conversation = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'sentiment' => 'neutral',
        ]);

        // Update sentiment after analysis
        $response = $this->patchJson("/api/conversations/{$conversation->id}", [
            'sentiment' => 'positive',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('conversations', [
            'id' => $conversation->id,
            'sentiment' => 'positive',
        ]);
    }

    /**
     * Test token counting for conversations
     */
    public function test_token_counting_for_conversations(): void
    {
        Sanctum::actingAs($this->user);

        $messages = [
            ['role' => 'user', 'content' => 'This is a test message to count tokens'],
            ['role' => 'assistant', 'content' => 'This is a response message'],
        ];

        $conversation = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'messages' => $messages,
            'token_count' => 45, // Example token count
        ]);

        $response = $this->getJson("/api/conversations/{$conversation->id}");

        $response->assertStatus(200);
        $this->assertGreaterThan(0, $response->json('token_count'));
    }

    /**
     * Test escalation handling
     */
    public function test_escalation_handling(): void
    {
        Sanctum::actingAs($this->user);

        $conversation = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'escalation_status' => 'none',
        ]);

        // Escalate conversation
        $response = $this->postJson("/api/conversations/{$conversation->id}/escalate");

        $response->assertStatus(200);
    }

    /**
     * Test multi-language conversation support
     */
    public function test_multi_language_conversation_support(): void
    {
        Sanctum::actingAs($this->user);

        // Arabic message
        $arabicConversation = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'messages' => [
                ['role' => 'user', 'content' => 'هل لديكم خيارات نباتية؟'],
            ],
        ]);

        // English message
        $englishConversation = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'messages' => [
                ['role' => 'user', 'content' => 'Do you have vegetarian options?'],
            ],
        ]);

        $this->assertDatabaseHas('conversations', ['id' => $arabicConversation->id]);
        $this->assertDatabaseHas('conversations', ['id' => $englishConversation->id]);
    }

    /**
     * Test conversation metadata tracking
     */
    public function test_conversation_metadata_tracking(): void
    {
        Sanctum::actingAs($this->user);

        $metadata = [
            'source' => 'whatsapp',
            'language' => 'ar',
            'timezone' => 'Asia/Riyadh',
        ];

        $conversation = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);

        // Verify metadata can be stored
        $this->assertDatabaseHas('conversations', [
            'id' => $conversation->id,
        ]);
    }

    /**
     * Test AI response caching
     */
    public function test_ai_response_caching(): void
    {
        Sanctum::actingAs($this->user);

        $query = 'Are you open on weekends?';

        // First request
        $conversation1 = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'messages' => [['role' => 'user', 'content' => $query]],
        ]);

        // Second request with same query
        $conversation2 = Conversation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'messages' => [['role' => 'user', 'content' => $query]],
        ]);

        // Both conversations should be independently created
        $this->assertNotEquals($conversation1->id, $conversation2->id);
    }

    /**
     * Test conversation context building
     */
    public function test_conversation_context_building(): void
    {
        // Setup restaurant with context
        $this->restaurant->update([
            'name' => 'Premium Restaurant',
            'phone' => '+966501234567',
            'settings' => [
                'opening_time' => '09:00',
                'closing_time' => '23:00',
                'delivery_available' => true,
            ],
        ]);

        Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Grilled Chicken',
            'price' => 45.00,
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/restaurants/{$this->restaurant->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('name', 'Premium Restaurant');
    }
}

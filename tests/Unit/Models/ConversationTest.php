<?php

namespace Tests\Unit\Models;

use App\Models\Conversation;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a conversation
     */
    public function test_can_create_conversation(): void
    {
        $restaurant = Restaurant::factory()->create();

        $conversation = Conversation::create([
            'restaurant_id' => $restaurant->id,
            'customer_identifier' => 'customer_123',
            'messages' => [
                ['role' => 'user', 'content' => 'Hello'],
                ['role' => 'assistant', 'content' => 'Hi there!'],
            ],
            'sentiment' => 'positive',
            'token_count' => 50,
        ]);

        $this->assertDatabaseHas('conversations', [
            'id' => $conversation->id,
            'customer_identifier' => 'customer_123',
        ]);
    }

    /**
     * Test conversation belongs to restaurant
     */
    public function test_conversation_belongs_to_restaurant(): void
    {
        $restaurant = Restaurant::factory()->create();
        $conversation = Conversation::factory()->create([
            'restaurant_id' => $restaurant->id,
        ]);

        $this->assertTrue($conversation->restaurant()->is($restaurant));
    }

    /**
     * Test conversation messages casting
     */
    public function test_conversation_messages_casting(): void
    {
        $messages = [
            ['role' => 'user', 'content' => 'What is on the menu?'],
            ['role' => 'assistant', 'content' => 'We have pizza and pasta.'],
        ];

        $conversation = Conversation::factory()->create([
            'messages' => $messages,
        ]);

        $this->assertIsArray($conversation->messages);
        $this->assertCount(2, $conversation->messages);
        $this->assertEquals('user', $conversation->messages[0]['role']);
    }

    /**
     * Test conversation sentiment tracking
     */
    public function test_conversation_sentiment_tracking(): void
    {
        $conversation = Conversation::factory()->create([
            'sentiment' => 'positive',
        ]);

        $this->assertEquals('positive', $conversation->sentiment);

        $conversation->update(['sentiment' => 'negative']);
        $this->assertEquals('negative', $conversation->fresh()->sentiment);
    }

    /**
     * Test conversation token counting
     */
    public function test_conversation_token_counting(): void
    {
        $conversation = Conversation::factory()->create([
            'token_count' => 150,
        ]);

        $this->assertEquals(150, $conversation->token_count);
    }

    /**
     * Test conversation escalation status
     */
    public function test_conversation_escalation_status(): void
    {
        $conversation = Conversation::factory()->create([
            'escalation_status' => 'none',
        ]);

        $this->assertEquals('none', $conversation->escalation_status);

        $conversation->update(['escalation_status' => 'pending']);
        $this->assertEquals('pending', $conversation->fresh()->escalation_status);
    }

    /**
     * Test adding message to conversation
     */
    public function test_add_message_to_conversation(): void
    {
        $conversation = Conversation::factory()->create([
            'messages' => [['role' => 'user', 'content' => 'Hello']],
        ]);

        $messages = $conversation->messages;
        $messages[] = ['role' => 'assistant', 'content' => 'Hi!'];

        $conversation->update(['messages' => $messages]);

        $this->assertCount(2, $conversation->fresh()->messages);
    }

    /**
     * Test conversation customer identifier
     */
    public function test_conversation_customer_identifier(): void
    {
        $identifier = '+966501234567';
        $conversation = Conversation::factory()->create([
            'customer_identifier' => $identifier,
        ]);

        $this->assertEquals($identifier, $conversation->customer_identifier);
    }

    /**
     * Test unresolved conversations scope
     */
    public function test_unresolved_conversations_scope(): void
    {
        Conversation::factory()->create(['escalation_status' => 'none']);
        Conversation::factory()->create(['escalation_status' => 'pending']);
        Conversation::factory()->create(['escalation_status' => 'resolved']);

        $unresolved = Conversation::whereIn(
            'escalation_status',
            ['none', 'pending']
        )->count();

        $this->assertGreaterThan(0, $unresolved);
    }
}

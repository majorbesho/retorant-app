<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Restaurant;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class N8nApiTest extends TestCase
{
    use RefreshDatabase;

    protected $restaurant;
    protected $menu;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup shared resources
        $this->restaurant = Restaurant::factory()->create([
            'name' => 'Test Restaurant',
            'name_translations' => ['en' => 'Test Restaurant'],
            'cuisine_type' => 'Italian',
            'city' => 'Dubai'
        ]);
    }

    public function test_get_restaurant_info_returns_optimized_json()
    {
        $response = $this->getJson("/api/restaurant/{$this->restaurant->id}/info");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'description',
                    'cuisine',
                    'address',
                    'context_str'
                ]
            ]);

        $this->assertStringContainsString('Test Restaurant', $response->json('data.context_str'));
    }

    public function test_get_menu_returns_simplified_structure()
    {
        // Create Menu Structure
        $menu = Menu::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'is_active' => true,
            'name' => 'Main Menu',
            'name_translations' => ['en' => 'Main Menu']
        ]);

        $category = Category::factory()->create([
            'menu_id' => $menu->id,
            'name' => 'Starters',
            'name_translations' => ['en' => 'Starters'],
            'is_active' => true
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Bruschetta',
            'name_translations' => ['en' => 'Bruschetta'],
            'price' => 25,
            'is_active' => true
        ]);

        $response = $this->getJson("/api/restaurant/{$this->restaurant->id}/menu");

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertNotEmpty($data);

        // Check first menu
        $firstMenu = $data[0];
        // Check first category in first menu
        $firstCategory = $firstMenu['categories'][0];

        $this->assertEquals('Starters', $firstCategory['category']);
        $this->assertEquals('Bruschetta', $firstCategory['items'][0]['name']);
    }

    public function test_store_conversation_creates_record()
    {
        // Mock Auth if needed (Sanctum)
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'restaurant_id' => $this->restaurant->id,
            'customer_phone_number' => '+971500000000',
            'message_text' => 'Hello',
            'direction' => 'inbound',
            'session_id' => 'sess_123'
        ];

        $response = $this->postJson('/api/conversations', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'id']);

        $this->assertDatabaseHas('conversations', [
            'restaurant_id' => $this->restaurant->id,
            'session_id' => 'sess_123'
        ]);
    }
}

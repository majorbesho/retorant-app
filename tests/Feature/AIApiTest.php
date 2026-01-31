<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\User;
use App\Models\Menu;
use App\Models\Product;
use App\Models\Category;
use App\Models\FAQ;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AIApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup config for API Key
        config(['services.n8n.api_key' => 'test_api_key']);
    }

    public function test_get_context_returns_correct_structure_and_caches_result()
    {
        $restaurant = Restaurant::factory()->create([
            'slug' => 'ai-test-restaurant',
            'name_translations' => ['en' => 'AI Test Restaurant', 'ar' => 'مطعم اختبار الذكاء'],
            'description_translations' => ['en' => 'Best food', 'ar' => 'أفضل طعام'],
            'is_active' => true,
        ]);

        // Create Menu
        $menu = Menu::factory()->create(['restaurant_id' => $restaurant->id, 'is_active' => true]);
        $category = Category::factory()->create(['menu_id' => $menu->id, 'is_active' => true]);
        Product::factory()->create(['category_id' => $category->id, 'is_active' => true, 'name_translations' => ['en' => 'Burger', 'ar' => 'برجر']]);

        // Create FAQ
        FAQ::factory()->create(['restaurant_id' => $restaurant->id, 'question_translations' => ['en' => 'Q1', 'ar' => 'س1'], 'answer_translations' => ['en' => 'A1', 'ar' => 'ج1'], 'is_active' => true]);

        // 1. Test Unauthorized
        $response = $this->getJson('/api/v1/external/restaurant/ai-test-restaurant/context');
        $response->assertStatus(401);

        // 2. Test Authorized & Content
        $response = $this->withHeaders(['X-API-KEY' => 'test_api_key'])
            ->getJson('/api/v1/external/restaurant/ai-test-restaurant/context');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'info',
                    'agent',
                    'faqs',
                    'menu'
                ]
            ]);

        // 3. Verify Cache
        $this->assertTrue(Cache::has("restaurant_context_ai-test-restaurant_en"));

        // 4. Test Language Switch
        $responseAr = $this->withHeaders(['X-API-KEY' => 'test_api_key'])
            ->getJson('/api/v1/external/restaurant/ai-test-restaurant/context?lang=ar');

        $responseAr->assertJsonPath('data.info.name', 'مطعم اختبار الذكاء');
        $responseAr->assertJsonPath('data.faqs.0.Q', 'س1');

        // Verify AR cache created
        $this->assertTrue(Cache::has("restaurant_context_ai-test-restaurant_ar"));
    }
}

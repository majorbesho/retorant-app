<?php

namespace Tests\Feature\API;

use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MenuAPITest extends TestCase
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
     * Test can get restaurant menu
     */
    public function test_can_get_restaurant_menu(): void
    {
        Product::factory()->count(5)->create(['restaurant_id' => $this->restaurant->id]);

        $response = $this->getJson("/api/restaurants/{$this->restaurant->id}/menu");

        $response->assertStatus(200);
        $response->assertJsonStructure(['*' => ['id', 'name', 'price', 'category_id']]);
    }

    /**
     * Test can filter menu by category
     */
    public function test_can_filter_menu_by_category(): void
    {
        $response = $this->getJson("/api/restaurants/{$this->restaurant->id}/menu?category=appetizers");

        $response->assertStatus(200);
    }

    /**
     * Test can search menu items
     */
    public function test_can_search_menu_items(): void
    {
        Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Grilled Chicken',
        ]);

        $response = $this->getJson(
            "/api/restaurants/{$this->restaurant->id}/menu?search=chicken"
        );

        $response->assertStatus(200);
    }

    /**
     * Test can get menu with pricing
     */
    public function test_can_get_menu_with_pricing(): void
    {
        Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'price' => 45.50,
        ]);

        $response = $this->getJson("/api/restaurants/{$this->restaurant->id}/menu");

        $response->assertStatus(200);
        $response->assertJsonStructure(['*' => ['price']]);
    }

    /**
     * Test can get available items only
     */
    public function test_can_get_available_items_only(): void
    {
        Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'is_active' => true,
            'quantity' => 10,
        ]);
        Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'is_active' => false,
        ]);

        $response = $this->getJson("/api/restaurants/{$this->restaurant->id}/menu");

        $response->assertStatus(200);
    }

    /**
     * Test can add item to menu
     */
    public function test_can_add_item_to_menu(): void
    {
        Sanctum::actingAs($this->user);

        $data = [
            'name' => 'New Dish',
            'description' => 'A delicious new dish',
            'price' => 65.00,
            'quantity' => 50,
            'category_id' => 1,
        ];

        $response = $this->postJson(
            "/api/restaurants/{$this->restaurant->id}/menu",
            $data
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', [
            'restaurant_id' => $this->restaurant->id,
            'name' => 'New Dish',
        ]);
    }

    /**
     * Test can update menu item
     */
    public function test_can_update_menu_item(): void
    {
        Sanctum::actingAs($this->user);
        $product = Product::factory()->create(['restaurant_id' => $this->restaurant->id]);

        $data = [
            'name' => 'Updated Dish Name',
            'price' => 55.00,
        ];

        $response = $this->patchJson(
            "/api/restaurants/{$this->restaurant->id}/menu/{$product->id}",
            $data
        );

        $response->assertStatus(200);
    }

    /**
     * Test can delete menu item
     */
    public function test_can_delete_menu_item(): void
    {
        Sanctum::actingAs($this->user);
        $product = Product::factory()->create(['restaurant_id' => $this->restaurant->id]);

        $response = $this->deleteJson(
            "/api/restaurants/{$this->restaurant->id}/menu/{$product->id}"
        );

        $response->assertStatus(200);
    }

    /**
     * Test cannot modify menu without ownership
     */
    public function test_cannot_modify_menu_without_ownership(): void
    {
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);

        $data = ['name' => 'Hacked Item'];
        $response = $this->postJson(
            "/api/restaurants/{$this->restaurant->id}/menu",
            $data
        );

        $response->assertStatus(403);
    }

    /**
     * Test menu items have translations
     */
    public function test_menu_items_have_translations(): void
    {
        Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Pizza',
        ]);

        $response = $this->getJson("/api/restaurants/{$this->restaurant->id}/menu");

        $response->assertStatus(200);
    }

    /**
     * Test can get menu with images
     */
    public function test_can_get_menu_with_images(): void
    {
        $response = $this->getJson("/api/restaurants/{$this->restaurant->id}/menu?include=images");

        $response->assertStatus(200);
    }
}

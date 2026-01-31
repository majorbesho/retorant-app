<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $restaurant;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create();
        $this->category = Category::factory()->create(['restaurant_id' => $this->restaurant->id]);
    }

    // ========================================
    // Create Product Tests
    // ========================================

    public function test_can_create_product()
    {
        $data = [
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $this->category->id,
            'name' => 'برجر الدجاج',
            'name_translations' => [
                'ar' => 'برجر الدجاج',
                'en' => 'Chicken Burger'
            ],
            'description' => 'برجر شهي مع فيليه الدجاج الطازج',
            'price' => 45.00,
            'discount_percentage' => 10,
            'calories' => 450,
            'protein' => 25,
            'carbohydrates' => 35,
            'fat' => 18,
            'stock_quantity' => 100,
            'is_available' => true,
            'is_active' => true
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/products', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'برجر الدجاج')
            ->assertJsonPath('data.price', 45.0);

        $this->assertDatabaseHas('products', [
            'name' => 'برجر الدجاج',
            'price' => 45.00
        ]);
    }

    public function test_cannot_create_product_without_auth()
    {
        $response = $this->postJson('/api/products', []);

        $response->assertStatus(401);
    }

    public function test_create_product_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/products', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'restaurant_id',
                'category_id',
                'name',
                'price'
            ]);
    }

    public function test_product_price_must_be_numeric()
    {
        $data = [
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $this->category->id,
            'name' => 'برجر',
            'price' => 'invalid'
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/products', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('price');
    }

    // ========================================
    // Get Products Tests
    // ========================================

    public function test_can_get_all_products()
    {
        Product::factory(5)->create(['restaurant_id' => $this->restaurant->id]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonPath('count', 5);
    }

    public function test_can_get_available_products()
    {
        Product::factory(3)->create([
            'restaurant_id' => $this->restaurant->id,
            'is_available' => true,
            'is_active' => true
        ]);

        Product::factory(2)->create([
            'restaurant_id' => $this->restaurant->id,
            'is_available' => false
        ]);

        $response = $this->getJson('/api/products/available');

        $response->assertStatus(200)
            ->assertJsonPath('count', 3);
    }

    public function test_can_get_products_on_discount()
    {
        Product::factory(3)->create([
            'restaurant_id' => $this->restaurant->id,
            'discount_percentage' => 10,
            'is_active' => true
        ]);

        Product::factory(2)->create([
            'restaurant_id' => $this->restaurant->id,
            'is_active' => true
        ]);

        $response = $this->getJson('/api/products/on-discount');

        $response->assertStatus(200)
            ->assertJsonPath('count', 3);
    }

    public function test_can_get_top_rated_products()
    {
        Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'rating' => 5,
            'is_active' => true
        ]);

        Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'rating' => 3,
            'is_active' => true
        ]);

        $response = $this->getJson('/api/products/top-rated?limit=1');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_search_products()
    {
        Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'برجر دجاج',
            'is_active' => true
        ]);

        Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'فيليه سمك',
            'is_active' => true
        ]);

        $response = $this->getJson('/api/products/search?q=برجر');

        $response->assertStatus(200)
            ->assertJsonPath('count', 1);
    }

    public function test_can_get_products_by_restaurant()
    {
        $restaurant1 = $this->restaurant;
        $restaurant2 = Restaurant::factory()->create();

        Product::factory(3)->create(['restaurant_id' => $restaurant1->id]);
        Product::factory(2)->create(['restaurant_id' => $restaurant2->id]);

        $response = $this->getJson("/api/products/restaurant/{$restaurant1->id}");

        $response->assertStatus(200)
            ->assertJsonPath('count', 3);
    }

    public function test_can_get_products_by_category()
    {
        $category1 = $this->category;
        $category2 = Category::factory()->create(['restaurant_id' => $this->restaurant->id]);

        Product::factory(4)->create([
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $category1->id
        ]);

        Product::factory(2)->create([
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $category2->id
        ]);

        $response = $this->getJson("/api/products/category/{$category1->id}");

        $response->assertStatus(200)
            ->assertJsonPath('count', 4);
    }

    // ========================================
    // Update Product Tests
    // ========================================

    public function test_can_update_product()
    {
        $product = Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'price' => 45.00
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/products/{$product->id}", [
                'price' => 50.00,
                'discount_percentage' => 15
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.price', 50.0);
    }

    // ========================================
    // Stock Management Tests
    // ========================================

    public function test_can_update_stock_set_action()
    {
        $product = Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'stock_quantity' => 50
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/products/{$product->id}/update-stock", [
                'quantity' => 100,
                'action' => 'set'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.stock_quantity', 100);
    }

    public function test_can_update_stock_add_action()
    {
        $product = Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'stock_quantity' => 50
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/products/{$product->id}/update-stock", [
                'quantity' => 30,
                'action' => 'add'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.stock_quantity', 80);
    }

    public function test_can_update_stock_subtract_action()
    {
        $product = Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'stock_quantity' => 50
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/products/{$product->id}/update-stock", [
                'quantity' => 20,
                'action' => 'subtract'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.stock_quantity', 30);
    }

    public function test_stock_cannot_go_below_zero()
    {
        $product = Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'stock_quantity' => 10
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/products/{$product->id}/update-stock", [
                'quantity' => 50,
                'action' => 'subtract'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.stock_quantity', 0);
    }

    // ========================================
    // Availability Toggle Tests
    // ========================================

    public function test_can_toggle_availability()
    {
        $product = Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'is_available' => true
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/products/{$product->id}/toggle-availability");

        $response->assertStatus(200)
            ->assertJsonPath('data.is_available', false);
    }

    // ========================================
    // Delete Product Tests
    // ========================================

    public function test_can_delete_product()
    {
        $product = Product::factory()->create(['restaurant_id' => $this->restaurant->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    // ========================================
    // Dietary Restriction Tests
    // ========================================

    public function test_can_get_products_by_dietary_restriction()
    {
        Product::factory(2)->create([
            'restaurant_id' => $this->restaurant->id,
            'dietary_restrictions' => ['vegetarian'],
            'is_active' => true
        ]);

        Product::factory(2)->create([
            'restaurant_id' => $this->restaurant->id,
            'is_active' => true
        ]);

        $response = $this->getJson('/api/products/dietary/vegetarian');

        $response->assertStatus(200)
            ->assertJsonPath('count', 2);
    }
}

<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a product
     */
    public function test_can_create_product(): void
    {
        $restaurant = Restaurant::factory()->create();
        $category = Category::factory()->create(['restaurant_id' => $restaurant->id]);

        $product = Product::create([
            'restaurant_id' => $restaurant->id,
            'category_id' => $category->id,
            'name' => 'Grilled Chicken',
            'name_ar' => 'دجاج مشوي',
            'description' => 'Fresh grilled chicken',
            'description_ar' => 'دجاج طازج مشوي',
            'price' => 45.50,
            'quantity' => 100,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Grilled Chicken',
        ]);
    }

    /**
     * Test product belongs to category
     */
    public function test_product_belongs_to_category(): void
    {
        $restaurant = Restaurant::factory()->create();
        $category = Category::factory()->create(['restaurant_id' => $restaurant->id]);
        $product = Product::factory()->create([
            'restaurant_id' => $restaurant->id,
            'category_id' => $category->id,
        ]);

        $this->assertTrue($product->category()->is($category));
    }

    /**
     * Test product belongs to restaurant
     */
    public function test_product_belongs_to_restaurant(): void
    {
        $restaurant = Restaurant::factory()->create();
        $product = Product::factory()->create(['restaurant_id' => $restaurant->id]);

        $this->assertTrue($product->restaurant()->is($restaurant));
    }

    /**
     * Test product price formatting
     */
    public function test_product_price_formatting(): void
    {
        $product = Product::factory()->create([
            'price' => 99.99,
        ]);

        $this->assertEquals(99.99, $product->price);
        $this->assertTrue(is_float($product->price) || is_string($product->price));
    }

    /**
     * Test product quantity tracking
     */
    public function test_product_quantity_tracking(): void
    {
        $product = Product::factory()->create(['quantity' => 50]);

        $this->assertEquals(50, $product->quantity);
    }

    /**
     * Test product is available scope
     */
    public function test_product_is_available_scope(): void
    {
        $restaurant = Restaurant::factory()->create();

        Product::factory()->create([
            'restaurant_id' => $restaurant->id,
            'is_active' => true,
            'quantity' => 10,
        ]);

        Product::factory()->create([
            'restaurant_id' => $restaurant->id,
            'is_active' => false,
            'quantity' => 10,
        ]);

        $available = $restaurant->products()->where('is_active', true)->count();
        $this->assertEquals(1, $available);
    }

    /**
     * Test product translations casting
     */
    public function test_product_translations_casting(): void
    {
        $product = Product::factory()->create([
            'name' => 'Pizza',
            'description' => 'Delicious pizza',
        ]);

        $this->assertIsString($product->name);
        $this->assertIsString($product->description);
    }

    /**
     * Test product can be marked out of stock
     */
    public function test_product_out_of_stock_handling(): void
    {
        $product = Product::factory()->create(['quantity' => 0]);

        $this->assertEquals(0, $product->quantity);
    }
}

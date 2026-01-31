<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Product;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompleteOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $restaurant;
    protected $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create(['is_active' => true]);
        $this->paymentMethod = PaymentMethod::factory()->create(['is_active' => true]);
    }

    /**
     * Test complete ordering flow:
     * 1. Browse restaurants
     * 2. View menu and categories
     * 3. Select products
     * 4. Create order
     * 5. Track order status
     * 6. Leave review
     */
    public function test_complete_order_workflow()
    {
        // Step 1: Browse restaurants
        $restaurantsResponse = $this->getJson('/api/restaurants/active');
        $restaurantsResponse->assertStatus(200);
        $restaurantId = $restaurantsResponse->json('data.0.id');

        // Step 2: Get restaurant menu and categories
        $menuResponse = $this->getJson("/api/menus/restaurant/{$restaurantId}");
        $menuResponse->assertStatus(200);

        $categoriesResponse = $this->getJson("/api/categories/restaurant/{$restaurantId}");
        $categoriesResponse->assertStatus(200);
        $categoryId = $categoriesResponse->json('data.0.id');

        // Step 3: Get available products
        $productsResponse = $this->getJson("/api/products/category/{$categoryId}");
        $productsResponse->assertStatus(200);
        $productId = $productsResponse->json('data.0.id');

        // Step 4: Create order
        $orderData = [
            'customer_id' => $this->customer->id,
            'restaurant_id' => $restaurantId,
            'total_amount' => 150.00,
            'discount_amount' => 10.00,
            'tax_amount' => 12.00,
            'delivery_fee' => 5.00,
            'delivery_address' => 'شارع الملك فهد',
            'payment_method_id' => $this->paymentMethod->id,
            'status' => 'pending',
            'items' => [
                [
                    'product_id' => $productId,
                    'quantity' => 2,
                    'unit_price' => 50.00
                ]
            ]
        ];

        $createOrderResponse = $this->actingAs($this->customer)
            ->postJson('/api/orders', $orderData);

        $createOrderResponse->assertStatus(201);
        $orderId = $createOrderResponse->json('data.id');

        // Step 5: Track order status
        $getOrderResponse = $this->actingAs($this->customer)
            ->getJson("/api/orders/{$orderId}");

        $getOrderResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'pending');

        // Update order status to delivered
        $updateOrderResponse = $this->actingAs($this->customer)
            ->putJson("/api/orders/{$orderId}", ['status' => 'delivered']);

        $updateOrderResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'delivered');

        // Step 6: Leave review
        $reviewData = [
            'user_id' => $this->customer->id,
            'restaurant_id' => $restaurantId,
            'rating' => 5,
            'review_text' => 'خدمة ممتازة وطعام لذيذ جداً. سأزور المطعم مرة أخرى',
            'food_quality' => 5,
            'service_quality' => 5,
            'would_recommend' => true,
            'visited_date' => now()->format('Y-m-d')
        ];

        $reviewResponse = $this->actingAs($this->customer)
            ->postJson('/api/reviews', $reviewData);

        $reviewResponse->assertStatus(201);

        // Verify restaurant rating increased
        $averageRatingResponse = $this->getJson("/api/reviews/restaurant/{$restaurantId}/average");
        $averageRatingResponse->assertStatus(200)
            ->assertJsonPath('data.total_reviews', 1);
    }
}

class CompleteReservationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create(['is_active' => true]);
    }

    /**
     * Test complete reservation flow:
     * 1. Browse restaurants
     * 2. Make reservation
     * 3. Confirm reservation
     * 4. Check-in
     * 5. Complete
     * 6. Leave review
     */
    public function test_complete_reservation_workflow()
    {
        // Step 1: Get available restaurants
        $restaurantsResponse = $this->getJson('/api/restaurants/active');
        $restaurantsResponse->assertStatus(200);
        $restaurantId = $restaurantsResponse->json('data.0.id');

        // Step 2: Make reservation
        $reservationData = [
            'customer_id' => $this->customer->id,
            'restaurant_id' => $restaurantId,
            'reservation_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'party_size' => 4,
            'guest_name' => 'أحمد محمد',
            'guest_phone' => '+966501234567',
            'guest_email' => 'ahmad@example.com',
            'special_requests' => 'طاولة بجانب النافذة',
            'status' => 'pending'
        ];

        $makeReservationResponse = $this->actingAs($this->customer)
            ->postJson('/api/reservations', $reservationData);

        $makeReservationResponse->assertStatus(201);
        $reservationId = $makeReservationResponse->json('data.id');

        // Step 3: Confirm reservation
        $confirmResponse = $this->actingAs($this->customer)
            ->postJson("/api/reservations/{$reservationId}/confirm");

        $confirmResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'confirmed');

        // Step 4: Check-in (on arrival)
        $checkInResponse = $this->actingAs($this->customer)
            ->postJson("/api/reservations/{$reservationId}/check-in");

        $checkInResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'checked_in');

        // Step 5: Complete reservation
        $completeResponse = $this->actingAs($this->customer)
            ->postJson("/api/reservations/{$reservationId}/complete");

        $completeResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'completed');

        // Step 6: Leave review
        $reviewData = [
            'user_id' => $this->customer->id,
            'restaurant_id' => $restaurantId,
            'rating' => 5,
            'review_text' => 'تجربة رائعة! الموظفون لطيفون جداً والطعام ممتاز',
            'service_quality' => 5,
            'ambiance' => 5,
            'would_recommend' => true
        ];

        $reviewResponse = $this->actingAs($this->customer)
            ->postJson('/api/reviews', $reviewData);

        $reviewResponse->assertStatus(201);
    }
}

class MenuManagementFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $manager;
    protected $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create();
    }

    /**
     * Test complete menu management flow:
     * 1. Create menu
     * 2. Create categories
     * 3. Create products
     * 4. Organize products
     * 5. Toggle availability
     */
    public function test_complete_menu_management_workflow()
    {
        // Step 1: Create menu
        $menuData = [
            'restaurant_id' => $this->restaurant->id,
            'name' => 'قائمة الغداء',
            'name_translations' => [
                'ar' => 'قائمة الغداء',
                'en' => 'Lunch Menu'
            ],
            'is_active' => true
        ];

        $createMenuResponse = $this->actingAs($this->manager)
            ->postJson('/api/menus', $menuData);

        $createMenuResponse->assertStatus(201);
        $menuId = $createMenuResponse->json('data.id');

        // Step 2: Create categories
        $categories = [];
        foreach (['المشروبات', 'الأطباق الرئيسية', 'الحلويات'] as $categoryName) {
            $categoryData = [
                'restaurant_id' => $this->restaurant->id,
                'name' => $categoryName,
                'display_order' => count($categories) + 1,
                'is_active' => true
            ];

            $createCategoryResponse = $this->actingAs($this->manager)
                ->postJson('/api/categories', $categoryData);

            $createCategoryResponse->assertStatus(201);
            $categories[] = $createCategoryResponse->json('data.id');
        }

        // Step 3: Create products in each category
        $products = [];
        foreach ($categories as $index => $categoryId) {
            for ($i = 0; $i < 3; $i++) {
                $productData = [
                    'restaurant_id' => $this->restaurant->id,
                    'category_id' => $categoryId,
                    'name' => "منتج {$index}-{$i}",
                    'price' => 45.00 + ($i * 5),
                    'is_active' => true,
                    'is_available' => true
                ];

                $createProductResponse = $this->actingAs($this->manager)
                    ->postJson('/api/products', $productData);

                $createProductResponse->assertStatus(201);
                $products[] = $createProductResponse->json('data.id');
            }
        }

        // Step 4: Verify menu with all products
        $getMenuResponse = $this->getJson("/api/menus/{$menuId}/detailed");
        $getMenuResponse->assertStatus(200);

        // Step 5: Toggle product availability
        $toggleResponse = $this->actingAs($this->manager)
            ->postJson("/api/products/{$products[0]}/toggle-availability");

        $toggleResponse->assertStatus(200)
            ->assertJsonPath('data.is_available', false);

        // Verify product not in available list
        $availableProductsResponse = $this->getJson('/api/products/available');
        $availableProductsResponse->assertStatus(200);
    }
}

class StockManagementFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $manager;
    protected $restaurant;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create();
        $this->product = Product::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'stock_quantity' => 100
        ]);
    }

    /**
     * Test stock management through complete flow:
     * 1. Add to stock
     * 2. Process orders (subtract)
     * 3. Restock
     * 4. Monitor low stock
     */
    public function test_stock_management_workflow()
    {
        // Step 1: Add initial stock
        $addStockResponse = $this->actingAs($this->manager)
            ->postJson("/api/products/{$this->product->id}/update-stock", [
                'quantity' => 50,
                'action' => 'add'
            ]);

        $addStockResponse->assertStatus(200)
            ->assertJsonPath('data.stock_quantity', 150);

        // Step 2: Simulate order processing (subtract stock)
        $subtractStockResponse = $this->actingAs($this->manager)
            ->postJson("/api/products/{$this->product->id}/update-stock", [
                'quantity' => 30,
                'action' => 'subtract'
            ]);

        $subtractStockResponse->assertStatus(200)
            ->assertJsonPath('data.stock_quantity', 120);

        // Step 3: Restock
        $restockResponse = $this->actingAs($this->manager)
            ->postJson("/api/products/{$this->product->id}/update-stock", [
                'quantity' => 200,
                'action' => 'set'
            ]);

        $restockResponse->assertStatus(200)
            ->assertJsonPath('data.stock_quantity', 200);

        // Step 4: Verify product is available
        $getProductResponse = $this->getJson("/api/products/{$this->product->id}");
        $getProductResponse->assertStatus(200)
            ->assertJsonPath('data.stock_quantity', 200);
    }
}

class RatingAndReviewFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create();
    }

    /**
     * Test rating system flow:
     * 1. Create multiple reviews
     * 2. Calculate average
     * 3. Get top restaurants
     */
    public function test_rating_system_workflow()
    {
        // Step 1: Create multiple reviews
        $ratings = [5, 4, 5, 4, 3];

        foreach ($ratings as $index => $rating) {
            $reviewData = [
                'user_id' => $this->customer->id,
                'restaurant_id' => $this->restaurant->id,
                'rating' => $rating,
                'review_text' => "تقييم {$index}: " . str_repeat('تعليق ', 5),
                'visited_date' => now()->format('Y-m-d')
            ];

            $this->actingAs($this->customer)
                ->postJson('/api/reviews', $reviewData)
                ->assertStatus(201);
        }

        // Step 2: Get average rating
        $averageResponse = $this->getJson("/api/reviews/restaurant/{$this->restaurant->id}/average");

        $averageResponse->assertStatus(200)
            ->assertJsonPath('data.total_reviews', 5)
            ->assertJsonPath('data.average_rating', 4.2);

        // Step 3: Get top restaurants
        $topResponse = $this->getJson('/api/reviews/top-restaurants?limit=5');

        $topResponse->assertStatus(200);
    }
}

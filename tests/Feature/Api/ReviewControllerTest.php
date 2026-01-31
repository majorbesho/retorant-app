<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Review;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create();
    }

    // ========================================
    // Create Review Tests
    // ========================================

    public function test_can_create_review()
    {
        $data = [
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'rating' => 5,
            'review_text' => 'مطعم ممتاز! الطعام طازج والخدمة سريعة جداً',
            'food_quality' => 5,
            'service_quality' => 5,
            'cleanliness' => 5,
            'ambiance' => 4,
            'value_for_money' => 4,
            'would_recommend' => true,
            'visited_date' => now()->subDay()->format('Y-m-d')
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/reviews', $data);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.rating', 5);

        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'rating' => 5
        ]);
    }

    public function test_cannot_create_review_without_auth()
    {
        $response = $this->postJson('/api/reviews', []);

        $response->assertStatus(401);
    }

    public function test_review_text_minimum_length()
    {
        $data = [
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'rating' => 4,
            'review_text' => 'قصير',  // Less than 10 characters
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/reviews', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('review_text');
    }

    public function test_rating_must_be_between_1_and_5()
    {
        $data = [
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'rating' => 10,  // Invalid
            'review_text' => 'تقييم مع رقم غير صحيح',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/reviews', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('rating');
    }

    // ========================================
    // Get Reviews Tests
    // ========================================

    public function test_can_get_all_reviews()
    {
        Review::factory(5)
            ->for($this->user, 'user')
            ->for($this->restaurant, 'restaurant')
            ->create();

        $response = $this->getJson('/api/reviews');

        $response->assertStatus(200)
            ->assertJsonPath('count', 5);
    }

    public function test_can_get_reviews_by_restaurant()
    {
        $restaurant1 = $this->restaurant;
        $restaurant2 = Restaurant::factory()->create();

        Review::factory(4)
            ->for($this->user, 'user')
            ->for($restaurant1, 'restaurant')
            ->create();

        Review::factory(2)
            ->for($this->user, 'user')
            ->for($restaurant2, 'restaurant')
            ->create();

        $response = $this->getJson("/api/reviews/restaurant/{$restaurant1->id}");

        $response->assertStatus(200)
            ->assertJsonPath('count', 4);
    }

    public function test_can_get_reviews_by_user()
    {
        $user1 = $this->user;
        $user2 = User::factory()->create();

        Review::factory(3)
            ->for($user1, 'user')
            ->for($this->restaurant, 'restaurant')
            ->create();

        Review::factory(2)
            ->for($user2, 'user')
            ->for($this->restaurant, 'restaurant')
            ->create();

        $response = $this->getJson("/api/reviews/user/{$user1->id}");

        $response->assertStatus(200)
            ->assertJsonPath('count', 3);
    }

    public function test_can_filter_reviews_by_rating()
    {
        Review::factory(3)
            ->for($this->user, 'user')
            ->for($this->restaurant, 'restaurant')
            ->create(['rating' => 5]);

        Review::factory(2)
            ->for($this->user, 'user')
            ->for($this->restaurant, 'restaurant')
            ->create(['rating' => 4]);

        $response = $this->getJson('/api/reviews/rating/5');

        $response->assertStatus(200)
            ->assertJsonPath('count', 3);
    }

    // ========================================
    // Average Rating Tests
    // ========================================

    public function test_can_calculate_average_rating()
    {
        Review::factory()->for($this->user, 'user')
            ->for($this->restaurant, 'restaurant')
            ->create(['rating' => 5]);

        Review::factory()->for($this->user, 'user')
            ->for($this->restaurant, 'restaurant')
            ->create(['rating' => 4]);

        Review::factory()->for($this->user, 'user')
            ->for($this->restaurant, 'restaurant')
            ->create(['rating' => 3]);

        $response = $this->getJson("/api/reviews/restaurant/{$this->restaurant->id}/average");

        $response->assertStatus(200)
            ->assertJsonPath('data.average_rating', 4.0)
            ->assertJsonPath('data.total_reviews', 3);
    }

    public function test_average_rating_includes_rating_distribution()
    {
        Review::factory(2)->for($this->user, 'user')
            ->for($this->restaurant, 'restaurant')
            ->create(['rating' => 5]);

        Review::factory(1)->for($this->user, 'user')
            ->for($this->restaurant, 'restaurant')
            ->create(['rating' => 4]);

        $response = $this->getJson("/api/reviews/restaurant/{$this->restaurant->id}/average");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'average_rating',
                    'total_reviews',
                    'rating_distribution'
                ]
            ]);
    }

    // ========================================
    // Update Review Tests
    // ========================================

    public function test_can_update_review()
    {
        $review = Review::factory()
            ->for($this->user, 'user')
            ->for($this->restaurant, 'restaurant')
            ->create(['rating' => 3]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/reviews/{$review->id}", [
                'rating' => 5,
                'review_text' => 'تم تحديث التقييم - المطعم ممتاز جداً!'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.rating', 5);
    }

    // ========================================
    // Delete Review Tests
    // ========================================

    public function test_can_delete_review()
    {
        $review = Review::factory()
            ->for($this->user, 'user')
            ->for($this->restaurant, 'restaurant')
            ->create();

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/reviews/{$review->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    // ========================================
    // Verified Reviews Tests
    // ========================================

    public function test_can_get_verified_reviews()
    {
        Review::factory(3)
            ->for($this->user, 'user')
            ->for($this->restaurant, 'restaurant')
            ->create(['is_verified' => true]);

        Review::factory(2)
            ->for($this->user, 'user')
            ->for($this->restaurant, 'restaurant')
            ->create(['is_verified' => false]);

        $response = $this->getJson('/api/reviews/verified');

        $response->assertStatus(200)
            ->assertJsonPath('count', 3);
    }

    // ========================================
    // Top Restaurants Tests
    // ========================================

    public function test_can_get_top_restaurants()
    {
        $restaurant1 = $this->restaurant;
        $restaurant2 = Restaurant::factory()->create();

        // High ratings for restaurant1
        Review::factory(5)
            ->for($this->user, 'user')
            ->for($restaurant1, 'restaurant')
            ->create(['rating' => 5]);

        // Low ratings for restaurant2
        Review::factory(3)
            ->for($this->user, 'user')
            ->for($restaurant2, 'restaurant')
            ->create(['rating' => 2]);

        $response = $this->getJson('/api/reviews/top-restaurants?limit=1');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}

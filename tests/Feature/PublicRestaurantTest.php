<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PublicRestaurantTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_restaurant_page_loads_with_seo_tags()
    {
        $restaurant = Restaurant::factory()->create([
            'name' => 'SEO Tasty Burger',
            'name_translations' => ['en' => 'SEO Tasty Burger', 'ar' => 'SEO Tasty Burger Ar'],
            'slug' => null, // Auto-generate
            'description' => 'Best burgers in town with fresh ingredients.',
            'description_translations' => ['en' => 'Best burgers in town with fresh ingredients.', 'ar' => 'Best burgers'],
            'is_active' => true,
            'status' => 'active'
        ]);

        // Refresh to get generated slug
        $restaurant->refresh();

        $response = $this->get(route('restaurant.show', $restaurant->slug));

        $response->assertStatus(200);

        // Assert Title
        // Note: App Name comes from config/trans, let's assume 'Restaurant AI' or similar.
        // We can just check if restaurant name is in title.
        $response->assertSee('<title>' . $restaurant->name . ' - ', false);

        // Assert Meta Description
        $response->assertSee('<meta name="description" content="' . $restaurant->description . '">', false);

        // Assert OG Tags
        $response->assertSee('<meta property="og:title" content="' . $restaurant->name . '">', false);
        $response->assertSee('<meta property="og:description" content="' . $restaurant->description . '">', false);
        $response->assertSee('<meta property="og:url" content="' . route('restaurant.show', $restaurant->slug) . '">', false);
    }
}

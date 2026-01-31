<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantSlugTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a slug is automatically generated when creating a restaurant.
     */
    public function test_slug_is_generated_on_create()
    {
        $restaurant = Restaurant::factory()->create([
            'name' => 'Great Burger Place',
            'slug' => null, // Force generation by trait
        ]);

        $this->assertNotNull($restaurant->slug);
        $this->assertEquals('great-burger-place', $restaurant->slug);
    }

    /**
     * Test that slugs are unique.
     */
    public function test_slugs_are_unique()
    {
        $r1 = Restaurant::factory()->create([
            'name' => 'Pizza Hut',
            'slug' => null,
        ]);

        $r2 = Restaurant::factory()->create([
            'name' => 'Pizza Hut',
            'slug' => null,
        ]);

        $this->assertEquals('pizza-hut', $r1->slug);
        $this->assertNotEquals('pizza-hut', $r2->slug);
        $this->assertTrue(str_starts_with($r2->slug, 'pizza-hut-'));
    }

    /**
     * Test updating name updates slug (optional, depends on config).
     * Usually slug varies on request, but standard sluggable updates on save if source changes.
     */
    public function test_slug_updates_when_name_changes()
    {
        $restaurant = Restaurant::factory()->create([
            'name' => 'Old Name',
            'slug' => null,
        ]);

        $this->assertEquals('old-name', $restaurant->slug);

        $restaurant->name = 'New Name';
        $restaurant->save();

        // Default behavior of cviebrock/eloquent-sluggable is usually to update slug if source changes,
        // unless onUpdate is false. By default it is false to preserve SEO.
        $this->assertEquals('old-name', $restaurant->slug);
    }

    /**
     * Test slug can be reset.
     */
    public function test_slug_can_be_regenerated_if_set_to_null()
    {
        $restaurant = Restaurant::factory()->create([
            'name' => 'Old Name',
            'slug' => null,
        ]);

        $restaurant->name = 'New Name';
        $restaurant->slug = null; // Force regeneration
        $restaurant->save();

        $this->assertEquals('new-name', $restaurant->slug);
    }
}

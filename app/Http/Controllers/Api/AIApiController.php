<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Menu;
use App\Models\FAQ;
use App\Models\Conversation;
use Illuminate\Support\Facades\DB;

class AIApiController extends Controller
{
    /**
     * GET /api/v1/external/restaurant/{slug}/context
     * Single call to get everything needed for the AI Agent context.
     * Cached for performance.
     */
    public function getContext($slug)
    {
        $cacheKey = "restaurant_context_{$slug}_" . app()->getLocale();

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($slug) {
            $restaurant = Restaurant::where('slug', $slug)
                ->active()
                ->with(['phones', 'aiAgents' => function ($q) {
                    $q->where('type', 'whatsapp')->where('status', 'active');
                }])
                ->firstOrFail();

            // 1. Basic Info
            $info = [
                'id' => $restaurant->id,
                'name' => $restaurant->getTranslated('name'),
                'description' => $restaurant->getTranslated('description'),
                'cuisine' => $restaurant->cuisine_type,
                'city' => $restaurant->city,
                'phone' => $restaurant->phone,
                'location' => [
                    'lat' => $restaurant->latitude,
                    'lng' => $restaurant->longitude,
                    'address' => $restaurant->getTranslated('address'),
                ],
                'working_hours' => $restaurant->working_hours ?? [],
                'settings' => [
                    'min_order' => $restaurant->min_order_amount,
                    'delivery_radius' => $restaurant->delivery_radius,
                ]
            ];

            // 2. AI Agent Settings
            $agent = $restaurant->aiAgents->first();
            $agentSettings = $agent ? [
                'name' => $agent->name,
                'tone' => $agent->voice_tone,
                'system_prompt' => $agent->system_prompt,
            ] : null;

            // 3. FAQs
            $faqs = FAQ::where('restaurant_id', $restaurant->id)
                ->active()
                ->get()
                ->map(function ($f) {
                    return [
                        'Q' => $f->getTranslated('question'),
                        'A' => $f->getTranslated('answer')
                    ];
                });

            // 4. Simplified Menu (Token Optimized)
            $menu = $this->getSimplifiedMenu($restaurant->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'info' => $info,
                    'agent' => $agentSettings,
                    'faqs' => $faqs,
                    'menu' => $menu
                ]
            ]);
        });
    }

    /**
     * Helper to get simplified menu structure
     */
    private function getSimplifiedMenu($restaurantId)
    {
        $menus = Menu::where('restaurant_id', $restaurantId)
            ->active()
            ->with(['categories' => function ($q) {
                $q->active()->orderBy('sort_order')
                    ->with(['products' => function ($p) {
                        $p->active()->select('id', 'category_id', 'name', 'description', 'price', 'is_available');
                    }]);
            }])
            ->get();

        return $menus->map(function ($menu) {
            return [
                'name' => $menu->getTranslated('name'),
                'categories' => $menu->categories->map(function ($cat) {
                    return [
                        'name' => $cat->getTranslated('name'),
                        'items' => $cat->products->map(function ($prod) {
                            return [
                                'id' => $prod->id,
                                'name' => $prod->getTranslated('name'),
                                'price' => $prod->price,
                                'desc' => $prod->getTranslated('description'), // Short desc
                            ];
                        })
                    ];
                })
            ];
        });
    }

    /**
     * GET /api/restaurant/{id}/info (Legacy/Specific)
     */
    public function getRestaurantInfo($id)
    {
        // Using cache for legacy endpoint too
        return \Illuminate\Support\Facades\Cache::remember("restaurant_info_{$id}_" . app()->getLocale(), 3600, function () use ($id) {
            $restaurant = Restaurant::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $restaurant->id,
                    'name' => $restaurant->getTranslated('name'),
                    // ... (rest of simple info)
                    'context_str' => "Restaurant {$restaurant->name}. Cuisine: {$restaurant->cuisine_type}."
                ]
            ]);
        });
    }

    // Keep getMenu / getFaqs for granular access if needed, but wrapped in cache logic...
    // For brevity in this refactor, I'll rely on getContext as the primary one, 
    // but keep storeConversation as it writes data.

    /**
     * POST /api/conversations
     * Secure Logging for n8n
     */
    public function storeConversation(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required', // Can be ID or Slug in future, for now ID
            'customer_phone_number' => 'required|string',
            'message_text' => 'required|string',
            'response_text' => 'nullable|string',
            'direction' => 'required|in:inbound,outbound',
            'session_id' => 'nullable|string',
            'sentiment' => 'nullable|string',
        ]);

        // If restaurant_id is a slug, resolve it
        if (!is_numeric($validated['restaurant_id'])) {
            $r = Restaurant::where('slug', $validated['restaurant_id'])->first();
            if ($r) $validated['restaurant_id'] = $r->id;
        }

        $conversation = Conversation::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'restaurant_id' => $validated['restaurant_id'],
            'customer_phone_number' => $validated['customer_phone_number'],
            'customer_identifier' => $validated['customer_phone_number'],
            'message_text' => $validated['message_text'],
            'response_text' => $validated['response_text'] ?? null,
            'message_direction' => $validated['direction'],
            'session_id' => $validated['session_id'] ?? null,
            'sentiment' => $validated['sentiment'] ?? null,
            'channel' => 'whatsapp',
            'started_at' => now(),
            'last_message_at' => now(),
        ]);

        return response()->json(['success' => true, 'id' => $conversation->id], 201);
    }
}

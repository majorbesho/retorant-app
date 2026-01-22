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
     * GET /api/restaurant/{id}/info
     */
    public function getRestaurantInfo($id)
    {
        $restaurant = Restaurant::with('phones')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $restaurant->id,
                'name' => $restaurant->getTranslated('name'),
                'address' => $restaurant->address, // Assuming address exists or use combined city/area
                'city' => $restaurant->city,
                'area' => $restaurant->area,
                'working_hours' => $restaurant->working_hours,
                'phones' => $restaurant->phones->map(function ($phone) {
                    return [
                        'whatsapp' => $phone->whatsapp_number,
                    ];
                }),
                'ai_enabled' => $restaurant->settings['ai_enabled'] ?? false,
                'settings' => $restaurant->settings,
            ]
        ]);
    }

    /**
     * GET /api/restaurant/{id}/menu
     */
    public function getMenu($id)
    {
        $menus = Menu::where('restaurant_id', $id)
            ->where('is_active', true)
            ->with(['categories' => function ($query) {
                $query->where('is_active', true)
                    ->with(['products' => function ($q) {
                        $q->where('is_active', true)
                            ->with(['variations.options', 'addonGroups.addons']);
                    }]);
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $menus
        ]);
    }

    /**
     * GET /api/restaurant/{id}/faqs
     */
    public function getFaqs($id)
    {
        $faqs = FAQ::where('restaurant_id', $id)
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $faqs
        ]);
    }

    /**
     * POST /api/conversations
     */
    public function storeConversation(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'customer_phone_number' => 'required|string',
            'message_text' => 'required|string',
            'response_text' => 'nullable|string',
            'direction' => 'required|in:inbound,outbound',
            'ai_agent_id' => 'nullable|exists:ai_agents,id',
            'session_id' => 'nullable|string',
            'sentiment' => 'nullable|string',
            'escalation_status' => 'nullable|string'
        ]);

        $conversation = Conversation::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'restaurant_id' => $validated['restaurant_id'],
            'ai_agent_id' => $validated['ai_agent_id'] ?? null,
            'customer_phone_number' => $validated['customer_phone_number'],
            'customer_identifier' => $validated['customer_phone_number'],
            'message_text' => $validated['message_text'],
            'response_text' => $validated['response_text'] ?? null,
            'message_direction' => $validated['direction'],
            'session_id' => $validated['session_id'] ?? null,
            'sentiment' => $validated['sentiment'] ?? null,
            'escalation_status' => $validated['escalation_status'] ?? null,
            'channel' => 'whatsapp', // Default for now
            'status' => 'completed',
            'started_at' => now(),
            'last_message_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $conversation
        ], 201);
    }

    /**
     * GET /api/restaurant/{id}/ai-agent-settings
     */
    public function getAiAgentSettings($id)
    {
        $restaurant = Restaurant::findOrFail($id);

        // Fetch the active WhatsApp agent or the first available one
        $agent = $restaurant->aiAgents()
            ->where('type', 'whatsapp')
            ->where('status', 'active')
            ->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'AI Agent settings not found for this restaurant.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $agent
        ]);
    }
}

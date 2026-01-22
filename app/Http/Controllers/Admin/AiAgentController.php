<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIAgent;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AiAgentController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(AIAgent::class, 'ai_agent');
    }

    public function index()
    {
        $restaurant = $this->getAuthenticatedRestaurant();
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $aiAgents = AIAgent::with('restaurant')->paginate(10);
        } else {
            if (!$restaurant) {
                abort(403, 'No restaurant context found.');
            }
            $aiAgents = $restaurant->aiAgents()->paginate(10);
        }

        return view('admin.ai_agents.index', compact('aiAgents', 'restaurant'));
    }

    public function create()
    {
        $restaurant = $this->getAuthenticatedRestaurant();
        return view('admin.ai_agents.create', compact('restaurant'));
    }

    public function store(Request $request)
    {
        $restaurant = $this->getAuthenticatedRestaurant();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['whatsapp', 'voice', 'web_chat', 'phone'])],
            'status' => ['required', Rule::in(['active', 'inactive', 'training', 'maintenance'])],
            'ai_provider' => 'required|string|max:255',
            'ai_model' => 'required|string|max:255',
            'temperature' => 'required|numeric|min:0|max:1',
            'greeting_ar' => 'nullable|string',
            'greeting_en' => 'nullable|string',
            'fallback_ar' => 'nullable|string',
            'fallback_en' => 'nullable|string',
            'working_hours_ar' => 'nullable|string',
            'working_hours_en' => 'nullable|string',
            'ai_config' => 'nullable|string', // JSON string from textarea
            'voice_provider' => 'nullable|string|max:255',
            'voice_id' => 'nullable|string|max:255',
            'voice_settings' => 'nullable|string', // JSON string from textarea
        ]);

        $aiAgent = new AIAgent();
        $aiAgent->restaurant_id = $restaurant->id;
        $aiAgent->name = $validatedData['name'];
        $aiAgent->type = $validatedData['type'];
        $aiAgent->status = $validatedData['status'];
        $aiAgent->ai_provider = $validatedData['ai_provider'];
        $aiAgent->ai_model = $validatedData['ai_model'];
        $aiAgent->temperature = $validatedData['temperature'];

        // Multi-language JSON fields
        $aiAgent->greeting_message = ['ar' => $request->greeting_ar, 'en' => $request->greeting_en];
        $aiAgent->fallback_message = ['ar' => $request->fallback_ar, 'en' => $request->fallback_en];
        $aiAgent->working_hours = ['ar' => $request->working_hours_ar, 'en' => $request->working_hours_en];

        // JSON config fields (decode if validation passed and it's a valid JSON string, or store as array if cast handles it)
        // Since input is string textarea, we should decode it.
        $aiAgent->ai_config = $request->ai_config ? json_decode($request->ai_config, true) : null;
        $aiAgent->voice_settings = $request->voice_settings ? json_decode($request->voice_settings, true) : null;

        $aiAgent->voice_provider = $validatedData['voice_provider'] ?? null;
        $aiAgent->voice_id = $validatedData['voice_id'] ?? null;

        $aiAgent->save();

        return redirect()->route('admin.ai-agents.index')->with('success', 'AI Agent created successfully!');
    }

    public function edit(AIAgent $aiAgent)
    {
        $restaurant = $this->getAuthenticatedRestaurant();
        return view('admin.ai_agents.edit', compact('aiAgent', 'restaurant'));
    }

    public function update(Request $request, AIAgent $aiAgent)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['whatsapp', 'voice', 'web_chat', 'phone'])],
            'status' => ['required', Rule::in(['active', 'inactive', 'training', 'maintenance'])],
            'ai_provider' => 'required|string|max:255',
            'ai_model' => 'required|string|max:255',
            'temperature' => 'required|numeric|min:0|max:1',
            'greeting_ar' => 'nullable|string',
            'greeting_en' => 'nullable|string',
            'fallback_ar' => 'nullable|string',
            'fallback_en' => 'nullable|string',
            'working_hours_ar' => 'nullable|string',
            'working_hours_en' => 'nullable|string',
            'ai_config' => 'nullable|string',
            'voice_provider' => 'nullable|string|max:255',
            'voice_id' => 'nullable|string|max:255',
            'voice_settings' => 'nullable|string',
        ]);

        $aiAgent->name = $validatedData['name'];
        $aiAgent->type = $validatedData['type'];
        $aiAgent->status = $validatedData['status'];
        $aiAgent->ai_provider = $validatedData['ai_provider'];
        $aiAgent->ai_model = $validatedData['ai_model'];
        $aiAgent->temperature = $validatedData['temperature'];

        $aiAgent->greeting_message = ['ar' => $request->greeting_ar, 'en' => $request->greeting_en];
        $aiAgent->fallback_message = ['ar' => $request->fallback_ar, 'en' => $request->fallback_en];
        $aiAgent->working_hours = ['ar' => $request->working_hours_ar, 'en' => $request->working_hours_en];

        $aiAgent->ai_config = $request->ai_config ? json_decode($request->ai_config, true) : null;
        $aiAgent->voice_settings = $request->voice_settings ? json_decode($request->voice_settings, true) : null;

        $aiAgent->voice_provider = $validatedData['voice_provider'] ?? null;
        $aiAgent->voice_id = $validatedData['voice_id'] ?? null;

        $aiAgent->save();

        return redirect()->route('admin.ai-agents.index')->with('success', 'AI Agent updated successfully!');
    }

    public function destroy(AIAgent $aiAgent)
    {
        $aiAgent->delete();
        return redirect()->route('admin.ai-agents.index')->with('success', 'AI Agent deleted successfully!');
    }

    protected function getAuthenticatedRestaurant()
    {
        $user = auth()->user();
        if ($user->restaurant_id) {
            return Restaurant::find($user->restaurant_id);
        }

        // If user is super admin and not linked to a specific restaurant, 
        // they might need to select one, or we handle it in index.
        // For now, removing the invalid query.
        return null;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    /**
     * Show the restaurant settings page.
     */
    public function settings()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            abort(404, 'No restaurant found for this user.');
        }

        return view('restaurant.settings', compact('restaurant'));
    }

    /**
     * Update restaurant settings.
     */
    public function updateSettings(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'cuisine_type' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'ai_whatsapp' => 'sometimes',
            'ai_calls' => 'sometimes',
            'ai_automation' => 'sometimes',
            'offers' => 'nullable|string',
        ]);

        // Handle Logo
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('restaurants/logos', 'public');
            $restaurant->logo = $path;
        }

        // Handle Gallery
        if ($request->hasFile('gallery')) {
            $galleryPaths = $restaurant->gallery ?? [];
            foreach ($request->file('gallery') as $image) {
                $galleryPaths[] = $image->store('restaurants/gallery', 'public');
            }
            $restaurant->gallery = $galleryPaths;
        }

        // Update Base Fields
        $restaurant->name = $validated['name'];
        $restaurant->name_translations = ['ar' => $validated['name'], 'en' => $validated['name']];
        $restaurant->email = $validated['email'];
        $restaurant->phone = $validated['phone'];
        $restaurant->whatsapp_number = $validated['whatsapp_number'];
        $restaurant->website = $validated['website'];
        $restaurant->cuisine_type = $validated['cuisine_type'];

        // Update Settings JSON
        $settings = $restaurant->settings ?? [];
        $settings['ai_whatsapp'] = $request->boolean('ai_whatsapp');
        $settings['ai_calls'] = $request->boolean('ai_calls');
        $settings['ai_automation'] = $request->boolean('ai_automation');
        $settings['offers_text'] = $validated['offers'];
        $restaurant->settings = $settings;

        $restaurant->save();

        return back()->with('success', 'تم تحديث البيانات بنجاح.');
    }
}

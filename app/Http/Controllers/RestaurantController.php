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

        // Auto-create restaurant if user doesn't have one (Dev/Fix)
        if (!$restaurant) {
            $restaurantName = $user->name . "'s Restaurant";
            $restaurant = \App\Models\Restaurant::create([
                'name' => $restaurantName,
                'name_translations' => [
                    'ar' => $restaurantName,
                    'en' => $restaurantName,
                ],
                'description_translations' => [
                    'ar' => 'مطعم جديد',
                    'en' => 'New Restaurant',
                ],
                'address_translations' => [
                    'ar' => '',
                    'en' => '',
                ],
                'slug' => \Illuminate\Support\Str::slug($user->name . '-' . time()),
                'email' => $user->email,
                'phone' => $user->phone,
                'type' => 'restaurant',
                'cuisine_type' => 'International',
                'cuisine_tags' => [],
                'city' => 'Dubai',
                'area' => 'Not Set',
                'is_active' => true,
            ]);

            $user->restaurant_id = $restaurant->id;
            $user->save();

            return redirect()->route('restaurant.settings')->with('success', 'تم إنشاء ملف مطعم جديد لك.');
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

    /**
     * Show WhatsApp setup page
     */
    public function setupWhatsApp()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            // Redirect to settings to create it automatically
            return redirect()->route('restaurant.settings');
        }

        return view('restaurant.whatsapp-setup', compact('restaurant'));
    }

    /**
     * Create WhatsApp instance
     */
    public function createWhatsAppInstance(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }

        // Check if already has an instance
        if ($restaurant->instance_name && $restaurant->whatsapp_status !== 'failed') {
            return response()->json([
                'error' => 'Instance already exists',
                'instance_name' => $restaurant->instance_name,
            ], 400);
        }

        // Set status to pending immediately
        $restaurant->update([
            'whatsapp_status' => 'pending',
        ]);

        // Dispatch job to create instance
        \App\Jobs\CreateWhatsAppInstanceJob::dispatch($restaurant);

        return response()->json([
            'success' => true,
            'message' => 'جاري إنشاء الاتصال...',
        ]);
    }

    /**
     * Get WhatsApp QR code
     */
    public function getWhatsAppQRCode(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $restaurant = $user->restaurant;

        if (!$restaurant || !$restaurant->instance_name) {
            return response()->json(['error' => 'No instance found'], 404);
        }

        // Return cached QR code if available
        if ($restaurant->whatsapp_qr_code) {
            return response()->json([
                'success' => true,
                'qrcode' => $restaurant->whatsapp_qr_code,
                'status' => $restaurant->whatsapp_status,
            ]);
        }

        // Fetch fresh QR code
        $evolutionService = app(\App\Services\WhatsAppEvolutionService::class);
        $qrData = $evolutionService->getInstanceQRCode($restaurant->instance_name);

        if ($qrData && isset($qrData['qrcode'])) {
            $restaurant->update([
                'whatsapp_qr_code' => $qrData['qrcode'],
            ]);

            return response()->json([
                'success' => true,
                'qrcode' => $qrData['qrcode'],
                'status' => $restaurant->whatsapp_status,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'QR code not available yet',
            'status' => $restaurant->whatsapp_status,
        ]);
    }

    /**
     * Check WhatsApp connection status
     */
    public function checkWhatsAppStatus(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $restaurant = $user->restaurant;

        if (!$restaurant || !$restaurant->instance_name) {
            return response()->json(['error' => 'No instance found'], 404);
        }

        // Optimization: Read directly from DB instead of calling API
        // Webhooks will update the status automatically

        return response()->json([
            'success' => true,
            'status' => $restaurant->whatsapp_status,
            'connected' => $restaurant->hasWhatsAppConnected(),
            'whatsapp_number' => $restaurant->whatsapp_number,
            'connected_at' => $restaurant->whatsapp_connected_at?->diffForHumans(),
        ]);
    }

    /**
     * Disconnect WhatsApp
     */
    public function disconnectWhatsApp(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $restaurant = $user->restaurant;

        if (!$restaurant || !$restaurant->instance_name) {
            return back()->with('error', 'لا يوجد اتصال WhatsApp');
        }

        // Logout from Evolution
        $evolutionService = app(\App\Services\WhatsAppEvolutionService::class);
        $success = $evolutionService->logoutInstance($restaurant->instance_name);

        if ($success) {
            $restaurant->update([
                'whatsapp_status' => 'disconnected',
                'whatsapp_qr_code' => null,
            ]);

            return back()->with('success', 'تم قطع الاتصال بنجاح');
        }

        return back()->with('error', 'فشل قطع الاتصال');
    }
}

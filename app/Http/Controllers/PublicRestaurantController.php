<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\RestaurantRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PublicRestaurantController extends Controller
{
    public function show($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->active()->firstOrFail();

        // Load menu or other public data if needed
        $restaurant->load(['menus.categories.products']);

        return view('public.restaurant.show', compact('restaurant'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'role' => 'required|string|in:restaurant_owner,restaurant_staff,delivery_driver',
            'password' => 'required|string|min:8|confirmed',
            'restaurant_name' => 'required_if:role,restaurant_owner|nullable|string|max:255',
            'restaurant_id' => 'required_if:role,restaurant_staff|nullable|exists:restaurants,id',
            'cuisine_type' => 'nullable|string|max:100',
            'message' => 'nullable|string|max:1000',
        ]);

        // 1. Create Inactive User
        $user = User::create([
            'name' => $validated['name'],
            'name_translations' => ['ar' => $validated['name'], 'en' => $validated['name']],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'is_active' => false,
            'status' => 'pending',
        ]);

        $restaurantId = $validated['restaurant_id'] ?? null;

        // 2. If Owner, Create Inactive Restaurant
        if ($validated['role'] === 'restaurant_owner') {
            $restaurant = Restaurant::create([
                'uuid' => (string) Str::uuid(),
                'name' => $validated['restaurant_name'],
                'name_translations' => ['ar' => $validated['restaurant_name'], 'en' => $validated['restaurant_name']],
                'slug' => Str::slug($validated['restaurant_name']) . '-' . Str::random(5),
                'type' => 'restaurant',
                'cuisine_type' => $validated['cuisine_type'] ?? 'Other',
                'cuisine_tags' => [],
                'city' => 'N/A',
                'area' => 'N/A',
                'is_active' => false,
                'is_verified' => false,
            ]);
            $restaurantId = $restaurant->id;

            $user->update(['restaurant_id' => $restaurantId]);
        } elseif ($validated['role'] === 'restaurant_staff') {
            $user->update(['restaurant_id' => $restaurantId]);
        }

        // 3. Create Restaurant Request
        RestaurantRequest::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'restaurant_name' => $validated['restaurant_name'] ?? ($restaurantId ? Restaurant::find($restaurantId)->name : null),
            'cuisine_type' => $validated['cuisine_type'],
            'message' => $validated['message'],
            'role' => $validated['role'],
            'user_id' => $user->id,
            'restaurant_id' => $restaurantId,
            'status' => 'pending',
        ]);

        // 4. Log the user in but redirect to waiting page
        auth()->login($user);

        return redirect()->route('auth.waiting')->with('success', __('تم استلام طلبك بنجاح! حسابك قيد المراجعة الآن.'));
    }
}

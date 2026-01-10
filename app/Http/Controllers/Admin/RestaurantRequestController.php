<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantRequest;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RestaurantRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = RestaurantRequest::latest();

        if ($user->hasRole('restaurant_owner')) {
            // Owners only see staff requests for their restaurant
            $query->where('role', 'restaurant_staff')
                ->where('restaurant_id', $user->restaurant_id);
        } elseif ($user->is_super_admin || $user->hasRole('super_admin')) {
            // Super admin sees all, or you could filter to Owners/Drivers
            // $query->whereIn('role', ['restaurant_owner', 'delivery_driver']);
        } else {
            // Force empty results for anyone else
            $query->where('id', 0);
        }

        $requests = $query->paginate(20);
        return view('admin.restaurant_requests.index', compact('requests'));
    }

    public function show(RestaurantRequest $restaurantRequest)
    {
        // Authorization check
        $user = auth()->user();
        if (
            $user->hasRole('restaurant_owner') &&
            ($restaurantRequest->role !== 'restaurant_staff' || $restaurantRequest->restaurant_id !== $user->restaurant_id)
        ) {
            abort(403);
        }

        return view('admin.restaurant_requests.show', compact('restaurantRequest'));
    }

    public function approve(Request $request, RestaurantRequest $restaurantRequest)
    {
        if ($restaurantRequest->status !== 'pending') {
            return back()->with('error', 'هذا الطلب تم التعامل معه مسبقاً.');
        }

        // Authorization check
        $admin = auth()->user();
        if (
            $admin->hasRole('restaurant_owner') &&
            ($restaurantRequest->role !== 'restaurant_staff' || $restaurantRequest->restaurant_id !== $admin->restaurant_id)
        ) {
            abort(403);
        }

        // 1. Find or Create User
        $user = $restaurantRequest->user_id ? User::find($restaurantRequest->user_id) : null;

        if (!$user) {
            // Check if user already exists by email or phone (fallback for older requests)
            $user = User::where('email', $restaurantRequest->email)
                ->orWhere('phone', $restaurantRequest->phone)
                ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $restaurantRequest->name,
                    'name_translations' => ['ar' => $restaurantRequest->name, 'en' => $restaurantRequest->name],
                    'email' => $restaurantRequest->email,
                    'phone' => $restaurantRequest->phone,
                    'password' => Hash::make(Str::random(10)), // In old flow they didn't set password
                    'is_active' => true,
                    'status' => 'active',
                ]);
            }
        }

        // Activate user if they were inactive
        $user->update([
            'is_active' => true,
            'status' => 'active',
        ]);

        // 2. Assign Role
        $user->assignRole($restaurantRequest->role);

        // 3. Handle Restaurant Activation (if relevant)
        if ($restaurantRequest->role === 'restaurant_owner') {
            $restaurant = $restaurantRequest->restaurant_id ? Restaurant::find($restaurantRequest->restaurant_id) : null;

            if (!$restaurant) {
                $restaurant = Restaurant::create([
                    'uuid' => (string) Str::uuid(),
                    'name' => $restaurantRequest->restaurant_name,
                    'name_translations' => ['ar' => $restaurantRequest->restaurant_name, 'en' => $restaurantRequest->restaurant_name],
                    'slug' => Str::slug($restaurantRequest->restaurant_name) . '-' . Str::random(5),
                    'type' => 'restaurant',
                    'email' => $restaurantRequest->email,
                    'phone' => $restaurantRequest->phone,
                    'cuisine_type' => $restaurantRequest->cuisine_type ?? 'Other',
                    'cuisine_tags' => [],
                    'city' => 'N/A',
                    'area' => 'N/A',
                    'is_active' => true,
                    'is_verified' => true,
                    'status' => 'active',
                ]);
            } else {
                $restaurant->update([
                    'is_active' => true,
                    'is_verified' => true,
                ]);
            }
            $user->update(['restaurant_id' => $restaurant->id]);
        }

        // 4. Update Request Status
        $restaurantRequest->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.restaurant_requests.index')
            ->with('success', "تم الموافقة على الطلب بنجاح وتفعيل الحساب.");
    }

    public function reject(Request $request, RestaurantRequest $restaurantRequest)
    {
        $restaurantRequest->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.restaurant_requests.index')
            ->with('success', 'تم رفض الطلب بنجاح.');
    }
}

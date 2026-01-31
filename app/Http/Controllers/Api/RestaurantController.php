<?php

namespace App\Http\Controllers\Api;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class RestaurantController extends Controller
{
    /**
     * Get all restaurants (paginated)
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $restaurants = Restaurant::query()
                ->with(['phones', 'menu', 'reservations', 'orders'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $restaurants,
                'message' => __('messages.restaurants_retrieved_successfully'),
                'count' => $restaurants->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_restaurants'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single restaurant
     */
    public function show(Restaurant $restaurant)
    {
        try {
            $restaurant->load(['phones', 'menu', 'categories', 'products', 'reservations', 'orders', 'aiAgent']);

            return response()->json([
                'success' => true,
                'data' => $restaurant,
                'message' => __('messages.restaurant_retrieved_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_restaurant'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create restaurant
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'name_translations' => 'nullable|array',
                'name_translations.ar' => 'nullable|string|max:255',
                'name_translations.en' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'description_translations' => 'nullable|array',
                'description_translations.ar' => 'nullable|string',
                'description_translations.en' => 'nullable|string',
                'cuisine_type' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'logo_url' => 'nullable|url',
                'cover_image_url' => 'nullable|url',
                'rating' => 'nullable|numeric|min:0|max:5',
                'owner_id' => 'nullable|integer',
            ]);

            $restaurant = Restaurant::create($validated);

            return response()->json([
                'success' => true,
                'data' => $restaurant,
                'message' => __('messages.restaurant_created_successfully')
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation_error'),
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_creating_restaurant'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update restaurant
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'name_translations' => 'nullable|array',
                'name_translations.ar' => 'nullable|string|max:255',
                'name_translations.en' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'description_translations' => 'nullable|array',
                'description_translations.ar' => 'nullable|string',
                'description_translations.en' => 'nullable|string',
                'cuisine_type' => 'sometimes|string|max:255',
                'country' => 'sometimes|string|max:255',
                'city' => 'sometimes|string|max:255',
                'address' => 'sometimes|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'logo_url' => 'nullable|url',
                'cover_image_url' => 'nullable|url',
                'rating' => 'nullable|numeric|min:0|max:5',
            ]);

            $restaurant->update($validated);

            return response()->json([
                'success' => true,
                'data' => $restaurant->fresh(),
                'message' => __('messages.restaurant_updated_successfully')
            ], Response::HTTP_OK);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation_error'),
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_updating_restaurant'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete restaurant
     */
    public function destroy(Restaurant $restaurant)
    {
        try {
            $restaurant->delete();

            return response()->json([
                'success' => true,
                'message' => __('messages.restaurant_deleted_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_deleting_restaurant'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get active restaurants only
     */
    public function active(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $restaurants = Restaurant::query()
                ->where('is_active', true)
                ->with(['phones', 'menu', 'categories', 'products'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $restaurants,
                'message' => __('messages.active_restaurants_retrieved_successfully'),
                'count' => $restaurants->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_restaurants'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get restaurants by city
     */
    public function byCity($city, Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $restaurants = Restaurant::query()
                ->where('city', $city)
                ->with(['phones', 'menu', 'categories'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $restaurants,
                'message' => __('messages.restaurants_retrieved_successfully'),
                'count' => $restaurants->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_restaurants'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get restaurants by cuisine type
     */
    public function byCuisine($cuisineType, Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $restaurants = Restaurant::query()
                ->where('cuisine_type', $cuisineType)
                ->with(['phones', 'menu', 'categories'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $restaurants,
                'message' => __('messages.restaurants_retrieved_successfully'),
                'count' => $restaurants->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_restaurants'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get top rated restaurants
     */
    public function topRated(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            $restaurants = Restaurant::query()
                ->orderByDesc('rating')
                ->with(['phones', 'menu', 'categories'])
                ->take($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $restaurants,
                'message' => __('messages.top_rated_restaurants_retrieved_successfully'),
                'count' => count($restaurants)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_restaurants'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Search restaurants
     */
    public function search(Request $request)
    {
        try {
            $query = $request->query('q');
            if (!$query) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.search_query_required'),
                    'errors' => ['q' => 'Search query is required']
                ], Response::HTTP_BAD_REQUEST);
            }

            $perPage = $request->query('per_page', 15);
            $restaurants = Restaurant::query()
                ->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('cuisine_type', 'like', "%{$query}%")
                ->with(['phones', 'menu', 'categories'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $restaurants,
                'message' => __('messages.search_completed_successfully'),
                'count' => $restaurants->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_searching_restaurants'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

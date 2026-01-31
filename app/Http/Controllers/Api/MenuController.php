<?php

namespace App\Http\Controllers\Api;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    /**
     * Get all menus (paginated)
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $menus = Menu::query()
                ->with(['restaurant', 'categories', 'products'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $menus,
                'message' => __('messages.menus_retrieved_successfully'),
                'count' => $menus->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_menus'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single menu
     */
    public function show(Menu $menu)
    {
        try {
            $menu->load(['restaurant', 'categories', 'products']);

            return response()->json([
                'success' => true,
                'data' => $menu,
                'message' => __('messages.menu_retrieved_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_menu'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create menu
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'restaurant_id' => 'required|integer|exists:restaurants,id',
                'name' => 'required|string|max:255',
                'name_translations' => 'nullable|array',
                'name_translations.ar' => 'nullable|string|max:255',
                'name_translations.en' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'description_translations' => 'nullable|array',
                'description_translations.ar' => 'nullable|string',
                'description_translations.en' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            $menu = Menu::create($validated);

            return response()->json([
                'success' => true,
                'data' => $menu->load(['restaurant']),
                'message' => __('messages.menu_created_successfully')
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
                'message' => __('messages.error_creating_menu'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update menu
     */
    public function update(Request $request, Menu $menu)
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
                'is_active' => 'nullable|boolean',
            ]);

            $menu->update($validated);

            return response()->json([
                'success' => true,
                'data' => $menu->fresh()->load(['restaurant']),
                'message' => __('messages.menu_updated_successfully')
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
                'message' => __('messages.error_updating_menu'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete menu
     */
    public function destroy(Menu $menu)
    {
        try {
            $menu->delete();

            return response()->json([
                'success' => true,
                'message' => __('messages.menu_deleted_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_deleting_menu'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get menus by restaurant
     */
    public function byRestaurant($restaurantId, Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $menus = Menu::query()
                ->where('restaurant_id', $restaurantId)
                ->with(['categories', 'products'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $menus,
                'message' => __('messages.menus_retrieved_successfully'),
                'count' => $menus->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_menus'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get active menus only
     */
    public function active(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $menus = Menu::query()
                ->where('is_active', true)
                ->with(['restaurant', 'categories', 'products'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $menus,
                'message' => __('messages.active_menus_retrieved_successfully'),
                'count' => $menus->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_menus'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get menu with all products and categories
     */
    public function detailed(Menu $menu)
    {
        try {
            $menu->load([
                'restaurant',
                'categories' => function ($query) {
                    $query->with('products');
                },
                'products'
            ]);

            return response()->json([
                'success' => true,
                'data' => $menu,
                'message' => __('messages.menu_retrieved_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_menu'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Toggle menu active status
     */
    public function toggle(Menu $menu)
    {
        try {
            $menu->update(['is_active' => !$menu->is_active]);

            return response()->json([
                'success' => true,
                'data' => $menu->fresh(),
                'message' => __('messages.menu_updated_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_updating_menu'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

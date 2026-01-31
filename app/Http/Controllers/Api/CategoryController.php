<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Get all categories (paginated)
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $categories = Category::query()
                ->with(['restaurant', 'products'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => __('messages.categories_retrieved_successfully'),
                'count' => $categories->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_categories'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single category
     */
    public function show(Category $category)
    {
        try {
            $category->load(['restaurant', 'products']);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => __('messages.category_retrieved_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_category'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create category
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
                'image_url' => 'nullable|url',
                'display_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            $category = Category::create($validated);

            return response()->json([
                'success' => true,
                'data' => $category->load(['restaurant']),
                'message' => __('messages.category_created_successfully')
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
                'message' => __('messages.error_creating_category'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update category
     */
    public function update(Request $request, Category $category)
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
                'image_url' => 'nullable|url',
                'display_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            $category->update($validated);

            return response()->json([
                'success' => true,
                'data' => $category->fresh()->load(['restaurant']),
                'message' => __('messages.category_updated_successfully')
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
                'message' => __('messages.error_updating_category'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete category
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => __('messages.category_deleted_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_deleting_category'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get categories by restaurant
     */
    public function byRestaurant($restaurantId, Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $categories = Category::query()
                ->where('restaurant_id', $restaurantId)
                ->with(['products'])
                ->orderBy('display_order')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => __('messages.categories_retrieved_successfully'),
                'count' => $categories->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_categories'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get active categories only
     */
    public function active(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $categories = Category::query()
                ->where('is_active', true)
                ->with(['restaurant', 'products'])
                ->orderBy('display_order')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => __('messages.active_categories_retrieved_successfully'),
                'count' => $categories->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_categories'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get category with products
     */
    public function withProducts(Category $category)
    {
        try {
            $category->load([
                'restaurant',
                'products' => function ($query) {
                    $query->where('is_active', true);
                }
            ]);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => __('messages.category_retrieved_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_category'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Toggle category active status
     */
    public function toggle(Category $category)
    {
        try {
            $category->update(['is_active' => !$category->is_active]);

            return response()->json([
                'success' => true,
                'data' => $category->fresh(),
                'message' => __('messages.category_updated_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_updating_category'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Reorder categories
     */
    public function reorder(Request $request)
    {
        try {
            $validated = $request->validate([
                'categories' => 'required|array',
                'categories.*.id' => 'required|integer|exists:categories,id',
                'categories.*.display_order' => 'required|integer|min:0',
            ]);

            foreach ($validated['categories'] as $categoryData) {
                Category::find($categoryData['id'])->update([
                    'display_order' => $categoryData['display_order']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => __('messages.categories_reordered_successfully')
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
                'message' => __('messages.error_reordering_categories'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

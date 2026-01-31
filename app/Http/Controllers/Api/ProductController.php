<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Get all products (paginated)
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $products = Product::query()
                ->with(['restaurant', 'category'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => __('messages.products_retrieved_successfully'),
                'count' => $products->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_products'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single product
     */
    public function show(Product $product)
    {
        try {
            $product->load(['restaurant', 'category']);

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => __('messages.product_retrieved_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_product'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create product
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'restaurant_id' => 'required|integer|exists:restaurants,id',
                'category_id' => 'required|integer|exists:categories,id',
                'name' => 'required|string|max:255',
                'name_translations' => 'nullable|array',
                'name_translations.ar' => 'nullable|string|max:255',
                'name_translations.en' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'description_translations' => 'nullable|array',
                'description_translations.ar' => 'nullable|string',
                'description_translations.en' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'image_url' => 'nullable|url',
                'calories' => 'nullable|integer|min:0',
                'protein' => 'nullable|numeric|min:0',
                'carbohydrates' => 'nullable|numeric|min:0',
                'fat' => 'nullable|numeric|min:0',
                'dietary_restrictions' => 'nullable|array',
                'allergens' => 'nullable|array',
                'preparation_time' => 'nullable|integer|min:0',
                'stock_quantity' => 'nullable|integer|min:0',
                'is_available' => 'nullable|boolean',
                'is_active' => 'nullable|boolean',
                'rating' => 'nullable|numeric|min:0|max:5',
            ]);

            $product = Product::create($validated);

            return response()->json([
                'success' => true,
                'data' => $product->load(['restaurant', 'category']),
                'message' => __('messages.product_created_successfully')
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
                'message' => __('messages.error_creating_product'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update product
     */
    public function update(Request $request, Product $product)
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
                'category_id' => 'sometimes|integer|exists:categories,id',
                'price' => 'sometimes|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'image_url' => 'nullable|url',
                'calories' => 'nullable|integer|min:0',
                'protein' => 'nullable|numeric|min:0',
                'carbohydrates' => 'nullable|numeric|min:0',
                'fat' => 'nullable|numeric|min:0',
                'dietary_restrictions' => 'nullable|array',
                'allergens' => 'nullable|array',
                'preparation_time' => 'nullable|integer|min:0',
                'stock_quantity' => 'nullable|integer|min:0',
                'is_available' => 'nullable|boolean',
                'is_active' => 'nullable|boolean',
                'rating' => 'nullable|numeric|min:0|max:5',
            ]);

            $product->update($validated);

            return response()->json([
                'success' => true,
                'data' => $product->fresh()->load(['restaurant', 'category']),
                'message' => __('messages.product_updated_successfully')
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
                'message' => __('messages.error_updating_product'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete product
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => __('messages.product_deleted_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_deleting_product'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get products by restaurant
     */
    public function byRestaurant($restaurantId, Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $products = Product::query()
                ->where('restaurant_id', $restaurantId)
                ->with(['category'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => __('messages.products_retrieved_successfully'),
                'count' => $products->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_products'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get products by category
     */
    public function byCategory($categoryId, Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $products = Product::query()
                ->where('category_id', $categoryId)
                ->with(['restaurant'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => __('messages.products_retrieved_successfully'),
                'count' => $products->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_products'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get available products only
     */
    public function available(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $products = Product::query()
                ->where('is_available', true)
                ->where('is_active', true)
                ->with(['restaurant', 'category'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => __('messages.available_products_retrieved_successfully'),
                'count' => $products->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_products'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get products with discount
     */
    public function onDiscount(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $products = Product::query()
                ->whereNotNull('discount_price')
                ->orWhereNotNull('discount_percentage')
                ->where('is_active', true)
                ->with(['restaurant', 'category'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => __('messages.discounted_products_retrieved_successfully'),
                'count' => $products->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_products'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Search products
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
            $products = Product::query()
                ->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->where('is_active', true)
                ->with(['restaurant', 'category'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => __('messages.search_completed_successfully'),
                'count' => $products->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_searching_products'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get top rated products
     */
    public function topRated(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            $products = Product::query()
                ->where('is_active', true)
                ->orderByDesc('rating')
                ->with(['restaurant', 'category'])
                ->take($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => __('messages.top_rated_products_retrieved_successfully'),
                'count' => count($products)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_products'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get products by dietary restriction
     */
    public function byDietaryRestriction($restriction, Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $products = Product::query()
                ->where('is_active', true)
                ->where('dietary_restrictions', 'like', "%{$restriction}%")
                ->with(['restaurant', 'category'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => __('messages.products_retrieved_successfully'),
                'count' => $products->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_products'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Toggle product availability
     */
    public function toggleAvailability(Product $product)
    {
        try {
            $product->update(['is_available' => !$product->is_available]);

            return response()->json([
                'success' => true,
                'data' => $product->fresh(),
                'message' => __('messages.product_updated_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_updating_product'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update stock quantity
     */
    public function updateStock(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:0',
                'action' => 'required|in:set,add,subtract',
            ]);

            $currentStock = $product->stock_quantity ?? 0;

            if ($validated['action'] === 'set') {
                $newStock = $validated['quantity'];
            } elseif ($validated['action'] === 'add') {
                $newStock = $currentStock + $validated['quantity'];
            } else {
                $newStock = max(0, $currentStock - $validated['quantity']);
            }

            $product->update(['stock_quantity' => $newStock]);

            return response()->json([
                'success' => true,
                'data' => $product->fresh(),
                'message' => __('messages.product_stock_updated_successfully')
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
                'message' => __('messages.error_updating_product'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

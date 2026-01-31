<?php

namespace App\Http\Controllers\Api;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    /**
     * Get all reviews (paginated)
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $reviews = Review::query()
                ->with(['user', 'restaurant'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $reviews,
                'message' => __('messages.reviews_retrieved_successfully'),
                'count' => $reviews->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_reviews'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single review
     */
    public function show(Review $review)
    {
        try {
            $review->load(['user', 'restaurant']);

            return response()->json([
                'success' => true,
                'data' => $review,
                'message' => __('messages.review_retrieved_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_review'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create review
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'restaurant_id' => 'required|integer|exists:restaurants,id',
                'rating' => 'required|integer|min:1|max:5',
                'review_text' => 'required|string|min:10|max:1000',
                'food_quality' => 'nullable|integer|min:1|max:5',
                'service_quality' => 'nullable|integer|min:1|max:5',
                'cleanliness' => 'nullable|integer|min:1|max:5',
                'ambiance' => 'nullable|integer|min:1|max:5',
                'value_for_money' => 'nullable|integer|min:1|max:5',
                'would_recommend' => 'nullable|boolean',
                'visited_date' => 'nullable|date_format:Y-m-d|before_or_equal:today',
            ]);

            $review = Review::create($validated);

            return response()->json([
                'success' => true,
                'data' => $review->load(['user', 'restaurant']),
                'message' => __('messages.review_created_successfully')
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
                'message' => __('messages.error_creating_review'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update review
     */
    public function update(Request $request, Review $review)
    {
        try {
            $validated = $request->validate([
                'rating' => 'sometimes|integer|min:1|max:5',
                'review_text' => 'sometimes|string|min:10|max:1000',
                'food_quality' => 'nullable|integer|min:1|max:5',
                'service_quality' => 'nullable|integer|min:1|max:5',
                'cleanliness' => 'nullable|integer|min:1|max:5',
                'ambiance' => 'nullable|integer|min:1|max:5',
                'value_for_money' => 'nullable|integer|min:1|max:5',
                'would_recommend' => 'nullable|boolean',
            ]);

            $review->update($validated);

            return response()->json([
                'success' => true,
                'data' => $review->fresh()->load(['user', 'restaurant']),
                'message' => __('messages.review_updated_successfully')
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
                'message' => __('messages.error_updating_review'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete review
     */
    public function destroy(Review $review)
    {
        try {
            $review->delete();

            return response()->json([
                'success' => true,
                'message' => __('messages.review_deleted_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_deleting_review'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get reviews by restaurant
     */
    public function byRestaurant($restaurantId, Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $reviews = Review::query()
                ->where('restaurant_id', $restaurantId)
                ->with(['user'])
                ->orderByDesc('created_at')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $reviews,
                'message' => __('messages.reviews_retrieved_successfully'),
                'count' => $reviews->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_reviews'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get reviews by user
     */
    public function byUser($userId, Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $reviews = Review::query()
                ->where('user_id', $userId)
                ->with(['restaurant'])
                ->orderByDesc('created_at')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $reviews,
                'message' => __('messages.reviews_retrieved_successfully'),
                'count' => $reviews->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_reviews'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get reviews by rating
     */
    public function byRating($rating, Request $request)
    {
        try {
            if ($rating < 1 || $rating > 5) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.validation_error'),
                    'errors' => ['rating' => 'Rating must be between 1 and 5']
                ], Response::HTTP_BAD_REQUEST);
            }

            $perPage = $request->query('per_page', 15);
            $reviews = Review::query()
                ->where('rating', $rating)
                ->with(['user', 'restaurant'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $reviews,
                'message' => __('messages.reviews_retrieved_successfully'),
                'count' => $reviews->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_reviews'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get restaurant average rating
     */
    public function averageRating($restaurantId)
    {
        try {
            $averageRating = Review::query()
                ->where('restaurant_id', $restaurantId)
                ->avg('rating');

            $totalReviews = Review::query()
                ->where('restaurant_id', $restaurantId)
                ->count();

            $ratingDistribution = Review::query()
                ->where('restaurant_id', $restaurantId)
                ->selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'average_rating' => round($averageRating, 2),
                    'total_reviews' => $totalReviews,
                    'rating_distribution' => $ratingDistribution
                ],
                'message' => __('messages.rating_retrieved_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_ratings'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get top restaurants by rating
     */
    public function topRestaurants(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            $restaurants = Review::query()
                ->selectRaw('restaurant_id, AVG(rating) as avg_rating, COUNT(*) as review_count')
                ->groupBy('restaurant_id')
                ->orderByDesc('avg_rating')
                ->take($limit)
                ->with('restaurant')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $restaurants,
                'message' => __('messages.top_restaurants_retrieved_successfully')
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
     * Get verified reviews
     */
    public function verified(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $reviews = Review::query()
                ->where('is_verified', true)
                ->with(['user', 'restaurant'])
                ->orderByDesc('created_at')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $reviews,
                'message' => __('messages.verified_reviews_retrieved_successfully'),
                'count' => $reviews->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_reviews'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

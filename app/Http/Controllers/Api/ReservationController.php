<?php

namespace App\Http\Controllers\Api;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class ReservationController extends Controller
{
    /**
     * Get all reservations (paginated, protected)
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $reservations = Reservation::query()
                ->with(['customer', 'restaurant', 'table'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $reservations,
                'message' => __('messages.reservations_retrieved_successfully'),
                'count' => $reservations->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_reservations'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single reservation
     */
    public function show(Reservation $reservation)
    {
        try {
            $reservation->load(['customer', 'restaurant', 'table']);

            return response()->json([
                'success' => true,
                'data' => $reservation,
                'message' => __('messages.reservation_retrieved_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_reservation'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create reservation
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|integer|exists:users,id',
                'restaurant_id' => 'required|integer|exists:restaurants,id',
                'reservation_date' => 'required|date_format:Y-m-d H:i:s|after:now',
                'party_size' => 'required|integer|min:1|max:50',
                'special_requests' => 'nullable|string|max:500',
                'guest_name' => 'required|string|max:255',
                'guest_phone' => 'required|string|max:20',
                'guest_email' => 'required|email|max:255',
                'status' => 'required|in:pending,confirmed,checked_in,completed,cancelled',
            ]);

            $reservation = Reservation::create($validated);

            return response()->json([
                'success' => true,
                'data' => $reservation->load(['customer', 'restaurant']),
                'message' => __('messages.reservation_created_successfully')
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
                'message' => __('messages.error_creating_reservation'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update reservation
     */
    public function update(Request $request, Reservation $reservation)
    {
        try {
            $validated = $request->validate([
                'reservation_date' => 'sometimes|date_format:Y-m-d H:i:s|after:now',
                'party_size' => 'sometimes|integer|min:1|max:50',
                'special_requests' => 'nullable|string|max:500',
                'guest_name' => 'sometimes|string|max:255',
                'guest_phone' => 'sometimes|string|max:20',
                'guest_email' => 'sometimes|email|max:255',
                'status' => 'sometimes|in:pending,confirmed,checked_in,completed,cancelled',
            ]);

            $reservation->update($validated);

            return response()->json([
                'success' => true,
                'data' => $reservation->fresh()->load(['customer', 'restaurant']),
                'message' => __('messages.reservation_updated_successfully')
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
                'message' => __('messages.error_updating_reservation'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete reservation
     */
    public function destroy(Reservation $reservation)
    {
        try {
            $reservation->delete();

            return response()->json([
                'success' => true,
                'message' => __('messages.reservation_deleted_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_deleting_reservation'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get reservations by status
     */
    public function byStatus($status, Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $reservations = Reservation::query()
                ->where('status', $status)
                ->with(['customer', 'restaurant'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $reservations,
                'message' => __('messages.reservations_retrieved_successfully'),
                'count' => $reservations->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_reservations'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get customer reservations
     */
    public function customerReservations($customerId, Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $reservations = Reservation::query()
                ->where('customer_id', $customerId)
                ->with(['restaurant'])
                ->orderByDesc('reservation_date')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $reservations,
                'message' => __('messages.reservations_retrieved_successfully'),
                'count' => $reservations->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_reservations'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get restaurant reservations
     */
    public function restaurantReservations($restaurantId, Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $reservations = Reservation::query()
                ->where('restaurant_id', $restaurantId)
                ->with(['customer'])
                ->orderByDesc('reservation_date')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $reservations,
                'message' => __('messages.reservations_retrieved_successfully'),
                'count' => $reservations->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_reservations'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Confirm reservation
     */
    public function confirm(Reservation $reservation)
    {
        try {
            $reservation->update(['status' => 'confirmed']);

            return response()->json([
                'success' => true,
                'data' => $reservation->fresh(),
                'message' => __('messages.reservation_confirmed_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_confirming_reservation'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Cancel reservation
     */
    public function cancel(Request $request, Reservation $reservation)
    {
        try {
            $validated = $request->validate([
                'cancellation_reason' => 'required|string|max:255',
            ]);

            $reservation->update([
                'status' => 'cancelled',
                'cancellation_reason' => $validated['cancellation_reason'],
                'cancelled_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => $reservation->fresh(),
                'message' => __('messages.reservation_cancelled_successfully')
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
                'message' => __('messages.error_cancelling_reservation'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Check in reservation
     */
    public function checkIn(Reservation $reservation)
    {
        try {
            $reservation->update([
                'status' => 'checked_in',
                'checked_in_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => $reservation->fresh(),
                'message' => __('messages.reservation_checked_in_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_checking_in_reservation'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Complete reservation
     */
    public function complete(Reservation $reservation)
    {
        try {
            $reservation->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => $reservation->fresh(),
                'message' => __('messages.reservation_completed_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_completing_reservation'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get pending reservations
     */
    public function pending(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $reservations = Reservation::query()
                ->where('status', 'pending')
                ->with(['customer', 'restaurant'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $reservations,
                'message' => __('messages.pending_reservations_retrieved_successfully'),
                'count' => $reservations->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_reservations'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

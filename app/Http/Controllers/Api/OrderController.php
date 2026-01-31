<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * Get all orders (paginated, protected)
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $orders = Order::query()
                ->with(['customer', 'restaurant', 'items', 'paymentMethod'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $orders,
                'message' => __('messages.orders_retrieved_successfully'),
                'count' => $orders->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_orders'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single order
     */
    public function show(Order $order)
    {
        try {
            $order->load(['customer', 'restaurant', 'items', 'paymentMethod']);

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => __('messages.order_retrieved_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_order'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create order
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|integer|exists:users,id',
                'restaurant_id' => 'required|integer|exists:restaurants,id',
                'total_amount' => 'required|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'delivery_fee' => 'nullable|numeric|min:0',
                'delivery_address' => 'required|string|max:255',
                'delivery_notes' => 'nullable|string',
                'payment_method_id' => 'required|integer|exists:payment_methods,id',
                'status' => 'required|in:pending,confirmed,preparing,ready,on_delivery,delivered,cancelled',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
            ]);

            $order = Order::create($validated);

            // Create order items
            foreach ($validated['items'] as $item) {
                $order->items()->create($item);
            }

            $order->load(['customer', 'restaurant', 'items', 'paymentMethod']);

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => __('messages.order_created_successfully')
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
                'message' => __('messages.error_creating_order'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update order
     */
    public function update(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
                'status' => 'sometimes|in:pending,confirmed,preparing,ready,on_delivery,delivered,cancelled',
                'delivery_address' => 'sometimes|string|max:255',
                'delivery_notes' => 'nullable|string',
                'total_amount' => 'sometimes|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'delivery_fee' => 'nullable|numeric|min:0',
            ]);

            $order->update($validated);

            return response()->json([
                'success' => true,
                'data' => $order->fresh()->load(['customer', 'restaurant', 'items']),
                'message' => __('messages.order_updated_successfully')
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
                'message' => __('messages.error_updating_order'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete order
     */
    public function destroy(Order $order)
    {
        try {
            $order->delete();

            return response()->json([
                'success' => true,
                'message' => __('messages.order_deleted_successfully')
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_deleting_order'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get orders by status
     */
    public function byStatus($status, Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $orders = Order::query()
                ->where('status', $status)
                ->with(['customer', 'restaurant', 'items'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $orders,
                'message' => __('messages.orders_retrieved_successfully'),
                'count' => $orders->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_orders'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get customer orders
     */
    public function customerOrders($customerId, Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $orders = Order::query()
                ->where('customer_id', $customerId)
                ->with(['restaurant', 'items', 'paymentMethod'])
                ->orderByDesc('created_at')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $orders,
                'message' => __('messages.orders_retrieved_successfully'),
                'count' => $orders->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_orders'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get restaurant orders
     */
    public function restaurantOrders($restaurantId, Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $orders = Order::query()
                ->where('restaurant_id', $restaurantId)
                ->with(['customer', 'items', 'paymentMethod'])
                ->orderByDesc('created_at')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $orders,
                'message' => __('messages.orders_retrieved_successfully'),
                'count' => $orders->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_orders'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
                'cancellation_reason' => 'required|string|max:255',
            ]);

            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $validated['cancellation_reason'],
                'cancelled_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => $order->fresh(),
                'message' => __('messages.order_cancelled_successfully')
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
                'message' => __('messages.error_cancelling_order'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get pending orders
     */
    public function pending(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $orders = Order::query()
                ->where('status', 'pending')
                ->with(['customer', 'restaurant', 'items'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $orders,
                'message' => __('messages.pending_orders_retrieved_successfully'),
                'count' => $orders->total()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_orders'),
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

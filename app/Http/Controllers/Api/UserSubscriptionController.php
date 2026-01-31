<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserSubscriptionController extends Controller
{
    /**
     * الحصول على جميع الاشتراكات
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $subscriptions = UserSubscription::with(['user', 'subscriptionPlan'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $subscriptions,
                'message' => 'تم جلب الاشتراكات بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * الحصول على الاشتراكات النشطة
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function active()
    {
        try {
            $subscriptions = UserSubscription::where('status', 'active')
                ->with(['user', 'subscriptionPlan'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $subscriptions,
                'message' => 'تم جلب الاشتراكات النشطة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * الحصول على اشتراكات في فترة التجربة
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function trials()
    {
        try {
            $subscriptions = UserSubscription::where('status', 'trial')
                ->with(['user', 'subscriptionPlan'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $subscriptions,
                'message' => 'تم جلب الاشتراكات في فترة التجربة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * عرض اشتراك محدد
     *
     * @param UserSubscription $subscription
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(UserSubscription $subscription)
    {
        try {
            $subscription->load(['user', 'subscriptionPlan', 'paymentMethod']);

            return response()->json([
                'success' => true,
                'data' => $subscription,
                'message' => 'تم جلب الاشتراك بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * إنشاء اشتراك جديد
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'subscription_plan_id' => 'required|exists:subscription_plans,id',
                'billing_cycle' => 'required|in:monthly,yearly',
                'payment_method_id' => 'nullable|exists:payment_methods,id',
                'stripe_subscription_id' => 'nullable|string|unique:user_subscriptions',
                'stripe_customer_id' => 'nullable|string',
            ]);

            $subscription = UserSubscription::create($validated);
            $subscription->load(['user', 'subscriptionPlan']);

            return response()->json([
                'success' => true,
                'data' => $subscription,
                'message' => 'تم إنشاء الاشتراك بنجاح'
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'بيانات غير صحيحة'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * تحديث اشتراك
     *
     * @param Request $request
     * @param UserSubscription $subscription
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, UserSubscription $subscription)
    {
        try {
            $validated = $request->validate([
                'status' => 'sometimes|in:active,trial,paused,past_due,canceled',
                'current_price' => 'sometimes|numeric|min:0',
                'payment_method_id' => 'sometimes|nullable|exists:payment_methods,id',
                'auto_renew' => 'sometimes|boolean',
                'active_features' => 'sometimes|array',
                'notes' => 'sometimes|nullable|string',
            ]);

            $subscription->update($validated);

            return response()->json([
                'success' => true,
                'data' => $subscription,
                'message' => 'تم تحديث الاشتراك بنجاح'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'بيانات غير صحيحة'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * إلغاء اشتراك
     *
     * @param Request $request
     * @param UserSubscription $subscription
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request, UserSubscription $subscription)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
                'cancel_reason' => $validated['reason'],
            ]);

            return response()->json([
                'success' => true,
                'data' => $subscription,
                'message' => 'تم إلغاء الاشتراك بنجاح'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'بيانات غير صحيحة'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * تجديد الاشتراك
     *
     * @param UserSubscription $subscription
     * @return \Illuminate\Http\JsonResponse
     */
    public function renew(UserSubscription $subscription)
    {
        try {
            if ($subscription->status === 'canceled') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن تجديد اشتراك ملغى'
                ], Response::HTTP_CONFLICT);
            }

            $subscription->update([
                'status' => 'active',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
                'next_billing_date' => now()->addMonth(),
                'last_subscription_renewal_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $subscription,
                'message' => 'تم تجديد الاشتراك بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * حذف اشتراك
     *
     * @param UserSubscription $subscription
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(UserSubscription $subscription)
    {
        try {
            $subscription->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الاشتراك بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

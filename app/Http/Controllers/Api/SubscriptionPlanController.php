<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubscriptionPlanController extends Controller
{
    /**
     * الحصول على جميع خطط الاشتراك
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $plans = SubscriptionPlan::orderBy('price_monthly', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $plans,
                'count' => $plans->count(),
                'message' => 'تم جلب خطط الاشتراك بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * عرض خطة اشتراك محددة
     *
     * @param SubscriptionPlan $plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(SubscriptionPlan $plan)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $plan,
                'message' => 'تم جلب الخطة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * إنشاء خطة اشتراك جديدة (للمسؤولين فقط)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'name_translations' => 'required|array',
                'name_translations.ar' => 'required|string',
                'name_translations.en' => 'required|string',
                'price_monthly' => 'required|numeric|min:0',
                'price_yearly' => 'required|numeric|min:0',
                'features' => 'required|array',
                'limits' => 'required|array',
                'stripe_price_ids' => 'required|array',
                'trial_days' => 'nullable|integer|min:0',
            ]);

            $plan = SubscriptionPlan::create($validated);

            return response()->json([
                'success' => true,
                'data' => $plan,
                'message' => 'تم إنشاء الخطة بنجاح'
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
     * تحديث خطة اشتراك (للمسؤولين فقط)
     *
     * @param Request $request
     * @param SubscriptionPlan $plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, SubscriptionPlan $plan)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'name_translations' => 'sometimes|array',
                'price_monthly' => 'sometimes|numeric|min:0',
                'price_yearly' => 'sometimes|numeric|min:0',
                'features' => 'sometimes|array',
                'limits' => 'sometimes|array',
            ]);

            $plan->update($validated);

            return response()->json([
                'success' => true,
                'data' => $plan,
                'message' => 'تم تحديث الخطة بنجاح'
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
     * حذف خطة اشتراك (للمسؤولين فقط)
     *
     * @param SubscriptionPlan $plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(SubscriptionPlan $plan)
    {
        try {
            $plan->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الخطة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * الحصول على أفضل الخطط الموصى بها
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommended()
    {
        try {
            $plans = SubscriptionPlan::where('slug', 'professional')
                ->orWhere('slug', 'starter')
                ->orderBy('price_monthly')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $plans,
                'message' => 'تم جلب الخطط الموصى بها بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

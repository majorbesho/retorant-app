<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentMethodController extends Controller
{
    /**
     * الحصول على جميع طرق الدفع
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $methods = PaymentMethod::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $methods,
                'count' => $methods->count(),
                'message' => 'تم جلب طرق الدفع بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * الحصول على طرق الدفع حسب النوع
     *
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function byType($type)
    {
        try {
            $methods = PaymentMethod::where('type', $type)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $methods,
                'count' => $methods->count(),
                'type' => $type,
                'message' => "تم جلب طرق الدفع من نوع $type بنجاح"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * عرض طريقة دفع محددة
     *
     * @param PaymentMethod $method
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(PaymentMethod $method)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $method,
                'message' => 'تم جلب طريقة الدفع بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * إنشاء طريقة دفع جديدة
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:card,wallet,bank_transfer,gift_card',
                'name' => 'required|string|max:255',
                'provider' => 'nullable|string|max:255',
                'stripe_id' => 'nullable|string|unique:payment_methods',
                'metadata' => 'nullable|array',
                'is_active' => 'boolean',
            ]);

            $method = PaymentMethod::create($validated);

            return response()->json([
                'success' => true,
                'data' => $method,
                'message' => 'تم إنشاء طريقة الدفع بنجاح'
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
     * تحديث طريقة دفع
     *
     * @param Request $request
     * @param PaymentMethod $method
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, PaymentMethod $method)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'provider' => 'sometimes|string|max:255',
                'metadata' => 'sometimes|array',
                'is_active' => 'sometimes|boolean',
            ]);

            $method->update($validated);

            return response()->json([
                'success' => true,
                'data' => $method,
                'message' => 'تم تحديث طريقة الدفع بنجاح'
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
     * حذف طريقة دفع
     *
     * @param PaymentMethod $method
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(PaymentMethod $method)
    {
        try {
            $method->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف طريقة الدفع بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * الحصول على الطرق النشطة فقط
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function active()
    {
        try {
            $methods = PaymentMethod::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $methods,
                'count' => $methods->count(),
                'message' => 'تم جلب طرق الدفع النشطة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

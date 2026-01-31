<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StaffMemberController extends Controller
{
    /**
     * الحصول على جميع الموظفين
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $staff = StaffMember::with(['user', 'restaurant'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $staff,
                'message' => 'تم جلب قائمة الموظفين بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * الحصول على موظفي مطعم معين
     *
     * @param int $restaurantId
     * @return \Illuminate\Http\JsonResponse
     */
    public function byRestaurant($restaurantId)
    {
        try {
            $staff = StaffMember::where('restaurant_id', $restaurantId)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $staff,
                'count' => $staff->count(),
                'message' => 'تم جلب موظفي المطعم بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * الحصول على موظفي دور معين
     *
     * @param string $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function byRole($role)
    {
        try {
            $validRoles = ['admin', 'manager', 'chef', 'cashier', 'delivery_driver', 'support_agent'];

            if (!in_array($role, $validRoles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'الدور غير موجود',
                    'valid_roles' => $validRoles
                ], Response::HTTP_BAD_REQUEST);
            }

            $staff = StaffMember::where('role', $role)
                ->with(['user', 'restaurant'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $staff,
                'count' => $staff->count(),
                'role' => $role,
                'message' => "تم جلب الموظفين من دور $role بنجاح"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * عرض موظف محدد
     *
     * @param StaffMember $staff
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(StaffMember $staff)
    {
        try {
            $staff->load(['user', 'restaurant']);

            return response()->json([
                'success' => true,
                'data' => $staff,
                'message' => 'تم جلب بيانات الموظف بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * إنشاء موظف جديد
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'restaurant_id' => 'required|exists:restaurants,id',
                'role' => 'required|in:admin,manager,chef,cashier,delivery_driver,support_agent',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'permissions' => 'nullable|array',
                'allowed_channels' => 'nullable|array',
            ]);

            $staff = StaffMember::create($validated);
            $staff->load(['user', 'restaurant']);

            return response()->json([
                'success' => true,
                'data' => $staff,
                'message' => 'تم إنشاء الموظف بنجاح'
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
     * تحديث بيانات موظف
     *
     * @param Request $request
     * @param StaffMember $staff
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, StaffMember $staff)
    {
        try {
            $validated = $request->validate([
                'role' => 'sometimes|in:admin,manager,chef,cashier,delivery_driver,support_agent',
                'phone' => 'sometimes|nullable|string|max:20',
                'address' => 'sometimes|nullable|string|max:255',
                'permissions' => 'sometimes|array',
                'allowed_channels' => 'sometimes|array',
                'is_active' => 'sometimes|boolean',
            ]);

            $staff->update($validated);

            return response()->json([
                'success' => true,
                'data' => $staff,
                'message' => 'تم تحديث بيانات الموظف بنجاح'
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
     * تعطيل/تفعيل موظف
     *
     * @param StaffMember $staff
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(StaffMember $staff)
    {
        try {
            $staff->update(['is_active' => !$staff->is_active]);

            $status = $staff->is_active ? 'مُفعّل' : 'معطّل';
            return response()->json([
                'success' => true,
                'data' => $staff,
                'message' => "تم ${status} الموظف بنجاح"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * حذف موظف
     *
     * @param StaffMember $staff
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(StaffMember $staff)
    {
        try {
            $staff->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الموظف بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * الحصول على الموظفين النشطين
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function active()
    {
        try {
            $staff = StaffMember::where('is_active', true)
                ->with(['user', 'restaurant'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $staff,
                'count' => $staff->count(),
                'message' => 'تم جلب الموظفين النشطين بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

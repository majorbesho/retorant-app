<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AIApiController;
use App\Http\Controllers\Api\SubscriptionPlanController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\UserSubscriptionController;
use App\Http\Controllers\Api\StaffMemberController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;

// المستخدم الحالي
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ========================================
// WhatsApp Evolution Webhook
// ========================================
Route::post('/whatsapp/webhook', [App\Http\Controllers\Api\WhatsAppWebhookController::class, 'handle'])
    ->name('whatsapp.webhook');

// ========================================
// Subscription Plans Routes
// ========================================
Route::prefix('subscription-plans')->group(function () {
    Route::get('/', [SubscriptionPlanController::class, 'index']); // جميع الخطط
    Route::get('/recommended', [SubscriptionPlanController::class, 'recommended']); // الخطط الموصى بها
    Route::get('/{plan}', [SubscriptionPlanController::class, 'show']); // خطة محددة

    // المسؤولون فقط
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [SubscriptionPlanController::class, 'store']);
        Route::put('/{plan}', [SubscriptionPlanController::class, 'update']);
        Route::delete('/{plan}', [SubscriptionPlanController::class, 'destroy']);
    });
});

// ========================================
// Payment Methods Routes
// ========================================
Route::prefix('payment-methods')->group(function () {
    Route::get('/', [PaymentMethodController::class, 'index']); // جميع الطرق
    Route::get('/active', [PaymentMethodController::class, 'active']); // الطرق النشطة فقط
    Route::get('/type/{type}', [PaymentMethodController::class, 'byType']); // حسب النوع
    Route::get('/{method}', [PaymentMethodController::class, 'show']); // طريقة محددة

    // المسؤولون فقط
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [PaymentMethodController::class, 'store']);
        Route::put('/{method}', [PaymentMethodController::class, 'update']);
        Route::delete('/{method}', [PaymentMethodController::class, 'destroy']);
    });
});

// ========================================
// User Subscriptions Routes
// ========================================
Route::prefix('user-subscriptions')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserSubscriptionController::class, 'index']); // جميع الاشتراكات
    Route::get('/active', [UserSubscriptionController::class, 'active']); // الاشتراكات النشطة
    Route::get('/trials', [UserSubscriptionController::class, 'trials']); // اشتراكات التجربة
    Route::get('/{subscription}', [UserSubscriptionController::class, 'show']); // اشتراك محدد

    Route::post('/', [UserSubscriptionController::class, 'store']); // إنشاء اشتراك
    Route::put('/{subscription}', [UserSubscriptionController::class, 'update']); // تحديث
    Route::post('/{subscription}/cancel', [UserSubscriptionController::class, 'cancel']); // إلغاء
    Route::post('/{subscription}/renew', [UserSubscriptionController::class, 'renew']); // تجديد
    Route::delete('/{subscription}', [UserSubscriptionController::class, 'destroy']); // حذف
});

// ========================================
// Staff Members Routes
// ========================================
Route::prefix('staff-members')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [StaffMemberController::class, 'index']); // جميع الموظفين
    Route::get('/active', [StaffMemberController::class, 'active']); // الموظفون النشطون
    Route::get('/restaurant/{restaurantId}', [StaffMemberController::class, 'byRestaurant']); // موظفو مطعم
    Route::get('/role/{role}', [StaffMemberController::class, 'byRole']); // حسب الدور
    Route::get('/{staff}', [StaffMemberController::class, 'show']); // موظف محدد

    Route::post('/', [StaffMemberController::class, 'store']); // إنشاء
    Route::put('/{staff}', [StaffMemberController::class, 'update']); // تحديث
    Route::post('/{staff}/toggle', [StaffMemberController::class, 'toggle']); // تفعيل/تعطيل
    Route::delete('/{staff}', [StaffMemberController::class, 'destroy']); // حذف
});

// ========================================
// Restaurants Routes
// ========================================
Route::prefix('restaurants')->group(function () {
    // Public routes
    Route::get('/', [RestaurantController::class, 'index']); // جميع المطاعم
    Route::get('/active', [RestaurantController::class, 'active']); // المطاعم النشطة
    Route::get('/city/{city}', [RestaurantController::class, 'byCity']); // حسب المدينة
    Route::get('/cuisine/{cuisineType}', [RestaurantController::class, 'byCuisine']); // حسب نوع الطعام
    Route::get('/top-rated', [RestaurantController::class, 'topRated']); // الأفضل تقييماً
    Route::get('/search', [RestaurantController::class, 'search']); // البحث
    Route::get('/{restaurant}', [RestaurantController::class, 'show']); // مطعم محدد

    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [RestaurantController::class, 'store']); // إنشاء
        Route::put('/{restaurant}', [RestaurantController::class, 'update']); // تحديث
        Route::delete('/{restaurant}', [RestaurantController::class, 'destroy']); // حذف
    });
});

// ========================================
// Orders Routes
// ========================================
Route::prefix('orders')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [OrderController::class, 'index']); // جميع الطلبات
    Route::get('/status/{status}', [OrderController::class, 'byStatus']); // حسب الحالة
    Route::get('/customer/{customerId}', [OrderController::class, 'customerOrders']); // طلبات العميل
    Route::get('/restaurant/{restaurantId}', [OrderController::class, 'restaurantOrders']); // طلبات المطعم
    Route::get('/pending', [OrderController::class, 'pending']); // الطلبات المعلقة
    Route::get('/{order}', [OrderController::class, 'show']); // طلب محدد

    Route::post('/', [OrderController::class, 'store']); // إنشاء طلب
    Route::put('/{order}', [OrderController::class, 'update']); // تحديث
    Route::post('/{order}/cancel', [OrderController::class, 'cancel']); // إلغاء
    Route::delete('/{order}', [OrderController::class, 'destroy']); // حذف
});

// ========================================
// Reservations Routes
// ========================================
Route::prefix('reservations')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ReservationController::class, 'index']); // جميع الحجوزات
    Route::get('/status/{status}', [ReservationController::class, 'byStatus']); // حسب الحالة
    Route::get('/customer/{customerId}', [ReservationController::class, 'customerReservations']); // حجوزات العميل
    Route::get('/restaurant/{restaurantId}', [ReservationController::class, 'restaurantReservations']); // حجوزات المطعم
    Route::get('/pending', [ReservationController::class, 'pending']); // الحجوزات المعلقة
    Route::get('/{reservation}', [ReservationController::class, 'show']); // حجز محدد

    Route::post('/', [ReservationController::class, 'store']); // إنشاء حجز
    Route::put('/{reservation}', [ReservationController::class, 'update']); // تحديث
    Route::post('/{reservation}/confirm', [ReservationController::class, 'confirm']); // تأكيد
    Route::post('/{reservation}/cancel', [ReservationController::class, 'cancel']); // إلغاء
    Route::post('/{reservation}/check-in', [ReservationController::class, 'checkIn']); // تسجيل الوصول
    Route::post('/{reservation}/complete', [ReservationController::class, 'complete']); // إنهاء
    Route::delete('/{reservation}', [ReservationController::class, 'destroy']); // حذف
});

// ========================================
// Reviews Routes
// ========================================
Route::prefix('reviews')->group(function () {
    // Public routes
    Route::get('/', [ReviewController::class, 'index']); // جميع التقييمات
    Route::get('/restaurant/{restaurantId}', [ReviewController::class, 'byRestaurant']); // تقييمات المطعم
    Route::get('/user/{userId}', [ReviewController::class, 'byUser']); // تقييمات المستخدم
    Route::get('/rating/{rating}', [ReviewController::class, 'byRating']); // حسب التقييم
    Route::get('/verified', [ReviewController::class, 'verified']); // التقييمات المتحققة
    Route::get('/restaurant/{restaurantId}/average', [ReviewController::class, 'averageRating']); // متوسط التقييم
    Route::get('/top-restaurants', [ReviewController::class, 'topRestaurants']); // أفضل المطاعم
    Route::get('/{review}', [ReviewController::class, 'show']); // تقييم محدد

    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [ReviewController::class, 'store']); // إنشاء
        Route::put('/{review}', [ReviewController::class, 'update']); // تحديث
        Route::delete('/{review}', [ReviewController::class, 'destroy']); // حذف
    });
});

// ========================================
// Menus Routes
// ========================================
Route::prefix('menus')->group(function () {
    // Public routes
    Route::get('/', [MenuController::class, 'index']); // جميع القوائم
    Route::get('/active', [MenuController::class, 'active']); // القوائم النشطة
    Route::get('/restaurant/{restaurantId}', [MenuController::class, 'byRestaurant']); // قوائم المطعم
    Route::get('/{menu}/detailed', [MenuController::class, 'detailed']); // قائمة مع التفاصيل
    Route::get('/{menu}', [MenuController::class, 'show']); // قائمة محددة

    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [MenuController::class, 'store']); // إنشاء
        Route::put('/{menu}', [MenuController::class, 'update']); // تحديث
        Route::post('/{menu}/toggle', [MenuController::class, 'toggle']); // تفعيل/تعطيل
        Route::delete('/{menu}', [MenuController::class, 'destroy']); // حذف
    });
});

// ========================================
// Categories Routes
// ========================================
Route::prefix('categories')->group(function () {
    // Public routes
    Route::get('/', [CategoryController::class, 'index']); // جميع الفئات
    Route::get('/active', [CategoryController::class, 'active']); // الفئات النشطة
    Route::get('/restaurant/{restaurantId}', [CategoryController::class, 'byRestaurant']); // فئات المطعم
    Route::get('/{category}/with-products', [CategoryController::class, 'withProducts']); // فئة مع المنتجات
    Route::get('/{category}', [CategoryController::class, 'show']); // فئة محددة

    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [CategoryController::class, 'store']); // إنشاء
        Route::put('/{category}', [CategoryController::class, 'update']); // تحديث
        Route::post('/{category}/toggle', [CategoryController::class, 'toggle']); // تفعيل/تعطيل
        Route::post('/reorder', [CategoryController::class, 'reorder']); // إعادة ترتيب
        Route::delete('/{category}', [CategoryController::class, 'destroy']); // حذف
    });
});

// ========================================
// Products Routes
// ========================================
Route::prefix('products')->group(function () {
    // Public routes
    Route::get('/', [ProductController::class, 'index']); // جميع المنتجات
    Route::get('/available', [ProductController::class, 'available']); // المنتجات المتاحة
    Route::get('/on-discount', [ProductController::class, 'onDiscount']); // المنتجات بخصم
    Route::get('/top-rated', [ProductController::class, 'topRated']); // الأعلى تقييماً
    Route::get('/search', [ProductController::class, 'search']); // البحث
    Route::get('/restaurant/{restaurantId}', [ProductController::class, 'byRestaurant']); // منتجات المطعم
    Route::get('/category/{categoryId}', [ProductController::class, 'byCategory']); // منتجات الفئة
    Route::get('/dietary/{restriction}', [ProductController::class, 'byDietaryRestriction']); // حسب النظام الغذائي
    Route::get('/{product}', [ProductController::class, 'show']); // منتج محدد

    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [ProductController::class, 'store']); // إنشاء
        Route::put('/{product}', [ProductController::class, 'update']); // تحديث
        Route::post('/{product}/toggle-availability', [ProductController::class, 'toggleAvailability']); // تفعيل/تعطيل
        Route::post('/{product}/update-stock', [ProductController::class, 'updateStock']); // تحديث المخزون
        Route::delete('/{product}', [ProductController::class, 'destroy']); // حذف
    });
});

// ========================================
// AI API Routes (الموجودة سابقاً)
// ========================================
Route::prefix('restaurant/{restaurant}')->group(function () {
    Route::get('/info', [AIApiController::class, 'getRestaurantInfo']);
    Route::get('/menu', [AIApiController::class, 'getMenu']);
    Route::get('/faqs', [AIApiController::class, 'getFaqs']);
    Route::get('/ai-agent-settings', [AIApiController::class, 'getAiAgentSettings']);
});

Route::post('/conversations', [AIApiController::class, 'storeConversation'])->middleware('auth:sanctum');

// ========================================
// External / n8n / AI Agent Routes
// ========================================
Route::prefix('v1/external')->middleware([
    \App\Http\Middleware\EnsureApiKey::class,
    \App\Http\Middleware\SetLocale::class
])->group(function () {
    // Single context call (Info + Menu + FAQs + Settings)
    Route::get('/restaurant/{slug}/context', [AIApiController::class, 'getContext']);

    // Store conversation/leads from n8n
    Route::post('/conversations', [AIApiController::class, 'storeConversation']);
});

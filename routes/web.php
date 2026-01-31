<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AddonGroupController;
use App\Http\Controllers\VariationController;
use App\Http\Controllers\PublicRestaurantController;

Route::get('/', function () {
    $restaurants = \App\Models\Restaurant::where('is_active', true)->get();
    return view('welcome', compact('restaurants'));
});

Route::post('/join-us', [PublicRestaurantController::class, 'store'])->name('restaurant.join');
// Route::get('/restaurant/{slug}', [PublicRestaurantController::class, 'show'])->name('restaurant.show'); // Moved to bottom

// Language Switcher
Route::get('/locale/{locale}', [App\Http\Controllers\LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/waiting', function () {
    return view('auth.waiting');
})->name('auth.waiting');

// Auth routes are handled by Laravel Fortify
// Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::resource('menus', MenuController::class)->middleware('permission:menu-management');
    Route::resource('categories', CategoryController::class)->middleware('permission:category-management');
    Route::resource('products', ProductController::class)->middleware('permission:product-management');
    Route::resource('addon_groups', AddonGroupController::class);
    Route::post('addon_groups/{addonGroup}/addons', [AddonGroupController::class, 'storeAddon'])->name('addon_groups.store_addon');
    Route::delete('addons/{addon}', [AddonGroupController::class, 'destroyAddon'])->name('addons.destroy');

    Route::resource('variations', VariationController::class)->middleware('permission:variation-management');
    Route::post('variations/{variation}/options', [VariationController::class, 'storeOption'])->name('variations.store_option')->middleware('permission:variation-management');
    Route::delete('variation_options/{option}', [VariationController::class, 'destroyOption'])->name('variation_options.destroy')->middleware('permission:variation-management');

    Route::post('products/{product}/delete-image', [ProductController::class, 'deleteImage'])->name('products.delete_image');

    // Restaurant Settings
    Route::get('restaurant/settings', [App\Http\Controllers\RestaurantController::class, 'settings'])->name('restaurant.settings');
    Route::put('restaurant/settings', [App\Http\Controllers\RestaurantController::class, 'updateSettings'])->name('restaurant.settings.update');

    // WhatsApp Setup Routes
    Route::prefix('restaurant/whatsapp')->name('restaurant.whatsapp.')->group(function () {
        Route::get('/setup', [App\Http\Controllers\RestaurantController::class, 'setupWhatsApp'])->name('setup');
        Route::post('/create-instance', [App\Http\Controllers\RestaurantController::class, 'createWhatsAppInstance'])->name('create');
        Route::get('/qr-code', [App\Http\Controllers\RestaurantController::class, 'getWhatsAppQRCode'])->name('qr');
        Route::get('/status', [App\Http\Controllers\RestaurantController::class, 'checkWhatsAppStatus'])->name('status');
        Route::post('/disconnect', [App\Http\Controllers\RestaurantController::class, 'disconnectWhatsApp'])->name('disconnect');
    });

    // Admin Routes
    Route::middleware(['role:super_admin|restaurant_owner'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('restaurant-requests', [App\Http\Controllers\Admin\RestaurantRequestController::class, 'index'])->name('restaurant_requests.index');
        Route::get('restaurant-requests/{restaurantRequest}', [App\Http\Controllers\Admin\RestaurantRequestController::class, 'show'])->name('restaurant_requests.show');
        Route::post('restaurant-requests/{restaurantRequest}/approve', [App\Http\Controllers\Admin\RestaurantRequestController::class, 'approve'])->name('restaurant_requests.approve');
        Route::post('restaurant-requests/{restaurantRequest}/reject', [App\Http\Controllers\Admin\RestaurantRequestController::class, 'reject'])->name('restaurant_requests.reject');

        Route::resource('ai-agents', App\Http\Controllers\Admin\AiAgentController::class);
        Route::get('audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
    });
});


Route::post('/whatsapp/webhook', [WhatsAppController::class, 'handleIncoming']);

// Public Restaurant Route - Defines at the end to avoid conflicts with specific routes like /settings
Route::get('/restaurant/{slug}', [PublicRestaurantController::class, 'show'])->name('restaurant.show');

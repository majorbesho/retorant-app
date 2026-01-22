<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AIApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('restaurant/{restaurant}')->group(function () {
    Route::get('/info', [AIApiController::class, 'getRestaurantInfo']);
    Route::get('/menu', [AIApiController::class, 'getMenu']);
    Route::get('/faqs', [AIApiController::class, 'getFaqs']);
    Route::get('/ai-agent-settings', [AIApiController::class, 'getAiAgentSettings']);
});

Route::post('/conversations', [AIApiController::class, 'storeConversation'])->middleware('auth:sanctum');

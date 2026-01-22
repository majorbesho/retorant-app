<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Restaurant;
use App\Models\FAQ;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

echo "--- AI API Verification Start ---\n";

// 1. Setup Admin & Token
$admin = User::where('is_super_admin', true)->first();
if ($admin) {
    $token = $admin->createToken('test-token')->plainTextToken;
    echo "Sanctum Token Generated: $token\n";
} else {
    die("No admin user found for token generation!\n");
}

// 2. Setup Test Data
$r = Restaurant::first();
if (!$r) die("No restaurant found!\n");

// Create Dummy FAQ
FAQ::create([
    'restaurant_id' => $r->id,
    'question_translations' => ['en' => 'What are your hours?', 'ar' => 'ما هي ساعات العمل؟'],
    'answer_translations' => ['en' => '9 AM to 10 PM', 'ar' => 'من 9 صباحاً حتى 10 مساءً'],
    'is_active' => true
]);
echo "Test FAQ created.\n";

// 3. Test Info Endpoint
$controller = app(\App\Http\Controllers\Api\AIApiController::class);
$response = $controller->getRestaurantInfo($r->id);
echo "Info Response: " . $response->getContent() . "\n";

// 4. Test Menu Endpoint
$response = $controller->getMenu($r->id);
echo "Menu Elements Count: " . count(json_decode($response->getContent(), true)['data']) . "\n";

// 5. Test FAQ Endpoint
$response = $controller->getFaqs($r->id);
echo "FAQs Response: " . $response->getContent() . "\n";

// 6. Test storeConversation Endpoint
$request = Request::create('/api/conversations', 'POST', [
    'restaurant_id' => $r->id,
    'customer_phone_number' => '+971500000000',
    'message_text' => 'Hello',
    'response_text' => 'Hi there!',
    'direction' => 'inbound'
]);

// Authenticate the request manually for the controller check
Auth::login($admin);
$response = $controller->storeConversation($request);
echo "Store Conversation Response: " . $response->getContent() . "\n";

echo "--- AI API Verification End ---\n";

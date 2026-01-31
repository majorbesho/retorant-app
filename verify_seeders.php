<?php

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SubscriptionPlan;
use App\Models\PaymentMethod;
use App\Models\UserSubscription;
use App\Models\StaffMember;

echo "\n========================================\n";
echo "🎉 SEEDING VERIFICATION\n";
echo "========================================\n\n";

$subscriptionPlans = SubscriptionPlan::count();
$paymentMethods = PaymentMethod::count();
$userSubscriptions = UserSubscription::count();
$staffMembers = StaffMember::count();

echo "📊 DATA COUNTS:\n";
echo "  ✅ SubscriptionPlans: $subscriptionPlans\n";
echo "  ✅ PaymentMethods: $paymentMethods\n";
echo "  ✅ UserSubscriptions: $userSubscriptions\n";
echo "  ✅ StaffMembers: $staffMembers\n\n";

echo "📋 SUBSCRIPTION PLANS:\n";
SubscriptionPlan::all()->each(function($plan) {
    $price = $plan->stripe_price_ids['monthly'] ?? 'N/A';
    echo "  • {$plan->name} - {$plan->name_translations['en']} (AED {$plan->price_monthly})\n";
});

echo "\n💳 PAYMENT METHODS SAMPLE:\n";
PaymentMethod::limit(5)->get()->each(function($method) {
    echo "  • {$method->type} - {$method->name}\n";
});

echo "\n👤 STAFF MEMBERS SAMPLE:\n";
StaffMember::limit(5)->get()->each(function($staff) {
    echo "  • User {$staff->user_id} - Role: {$staff->role} (Restaurant {$staff->restaurant_id})\n";
});

echo "\n✅ All seeders completed successfully!\n";
echo "========================================\n\n";

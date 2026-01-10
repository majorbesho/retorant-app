<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('plan_id'); // basic, pro, enterprise
            $table->string('plan_name');
            $table->json('plan_features');

            // الفترة
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly']);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // الدفع
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('AED');
            $table->enum('status', ['active', 'canceled', 'expired', 'past_due', 'pending'])->default('pending');
            $table->string('stripe_subscription_id')->nullable();
            $table->string('stripe_customer_id')->nullable();

            // الاستخدام
            $table->json('usage_limits')->nullable(); // {ai_calls: 1000, users: 5}
            $table->json('current_usage')->nullable();

            // التحديثات
            $table->boolean('auto_renew')->default(true);
            $table->json('upcoming_changes')->nullable(); // التغييرات المقررة

            $table->timestamps();
            $table->softDeletes();

            $table->index(['restaurant_id', 'status', 'ends_at']);
            $table->index('stripe_subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

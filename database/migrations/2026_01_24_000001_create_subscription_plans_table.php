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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('name_translations'); // {ar: "...", en: "..."}
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('description_translations')->nullable();
            
            // الأسعار
            $table->decimal('monthly_price', 10, 2);
            $table->decimal('yearly_price', 10, 2)->nullable();
            $table->decimal('setup_fee', 10, 2)->default(0);
            
            // الفترة الزمنية
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->integer('trial_days')->default(14); // عدد أيام النسخة التجريبية
            
            // الحالة
            $table->enum('status', ['active', 'archived', 'draft'])->default('active');
            
            // الميزات
            $table->json('features'); // {ai_chat: true, order_management: true, analytics: true}
            
            // الحدود
            $table->json('limits'); // {max_restaurants: 1, max_conversations: 1000, max_orders: 10000}
            
            // التكامل مع Stripe
            $table->string('stripe_price_id_monthly')->nullable();
            $table->string('stripe_price_id_yearly')->nullable();
            $table->string('stripe_product_id')->nullable();
            
            // الخصائص الإضافية
            $table->boolean('is_recommended')->default(false); // خطة موصى بها
            $table->integer('sort_order')->default(0);
            $table->json('included_features')->nullable(); // قائمة مفصلة للميزات
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'billing_cycle']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};

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
        Schema::table('restaurants', function (Blueprint $table) {
            // إضافة العلاقة مع User Subscriptions
            $table->foreignId('subscription_id')->nullable()->after('id')->constrained('user_subscriptions')->nullOnDelete();
            
            // إضافة حقول خطة الاشتراك
            $table->string('subscription_plan_name')->nullable()->after('subscription_id');
            $table->json('subscription_features')->nullable()->comment('الميزات المتاحة بناءً على الخطة');
            $table->json('subscription_limits')->nullable()->comment('حدود الاستخدام');
            
            // إحصائيات الاستخدام
            $table->integer('current_conversations_count')->default(0)->after('subscription_limits');
            $table->integer('current_orders_count')->default(0);
            $table->integer('current_api_calls')->default(0);
            $table->integer('max_api_calls_monthly')->default(0)->comment('الحد الأقصى لاستدعاءات API الشهرية');
            
            // تاريخ انتهاء الاشتراك
            $table->timestamp('subscription_expires_at')->nullable()->comment('تاريخ انتهاء صلاحية الاشتراك الحالي');
            $table->timestamp('last_subscription_renewal_at')->nullable();
            
            // حالة الاشتراك
            $table->enum('subscription_status', ['active', 'paused', 'expired', 'canceled', 'pending'])->default('pending');
            
            // المؤشرات (Indexes)
            $table->index(['subscription_id', 'subscription_status']);
            $table->index('subscription_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['subscription_id']);
            $table->dropIndex(['subscription_id', 'subscription_status']);
            $table->dropIndex(['subscription_expires_at']);
            $table->dropColumn([
                'subscription_id',
                'subscription_plan_name',
                'subscription_features',
                'subscription_limits',
                'current_conversations_count',
                'current_orders_count',
                'current_api_calls',
                'max_api_calls_monthly',
                'subscription_expires_at',
                'last_subscription_renewal_at',
                'subscription_status',
            ]);
        });
    }
};

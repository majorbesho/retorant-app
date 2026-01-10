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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('order_number')->unique(); // ORD-2024-001
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->json('customer_address')->nullable(); // {street: '', building: '', apartment: ''}

            // معلومات الطلب
            $table->enum('order_type', ['delivery', 'pickup', 'dine_in']);
            $table->enum('source', ['web', 'mobile', 'whatsapp', 'phone', 'walk_in']);
            $table->json('delivery_address')->nullable();
            $table->timestamp('preferred_delivery_time')->nullable();
            $table->text('special_instructions')->nullable();

            // التسعير
            $table->decimal('subtotal', 12, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('service_charge', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('discount_code')->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);

            // الدفع
            $table->enum('payment_method', ['cash', 'card', 'apple_pay', 'stc_pay', 'tabby', 'tamara']);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded', 'partially_refunded'])->default('pending');
            $table->string('payment_reference')->nullable();
            $table->json('payment_details')->nullable();

            // الحالة
            $table->enum('status', [
                'pending',           // في انتظار التأكيد
                'confirmed',         // مؤكد
                'preparing',         // قيد التحضير
                'ready',             // جاهز
                'out_for_delivery',  // قيد التوصيل
                'delivered',         // تم التسليم
                'picked_up',         // تم الاستلام
                'cancelled',         // ملغي
                'rejected',          // مرفوض
                'on_hold'            // معلق
            ])->default('pending');

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('preparing_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // التوصيل
            $table->foreignId('delivery_driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('delivery_notes')->nullable();
            $table->decimal('delivery_distance', 8, 2)->nullable(); // بالكيلومتر
            $table->integer('estimated_delivery_time')->nullable(); // بالدقائق

            // التقييم
            $table->integer('rating')->nullable();
            $table->text('review')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            // بيانات النظام
            $table->json('metadata')->nullable(); // أي بيانات إضافية
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['restaurant_id', 'status', 'created_at']);
            $table->index(['order_number', 'customer_phone']);
            $table->index(['payment_status', 'status']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

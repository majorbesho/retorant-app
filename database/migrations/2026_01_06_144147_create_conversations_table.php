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
        Schema::create('conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('ai_agent_id')->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();

            // بيانات العميل
            $table->string('customer_identifier'); // رقم الهاتف، البريد، المعرف
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();

            // تفاصيل المحادثة
            $table->enum('channel', ['whatsapp', 'voice', 'web', 'sms', 'email']);
            $table->string('session_id')->nullable();
            $table->enum('status', ['active', 'completed', 'transferred', 'abandoned', 'failed'])->default('active');
            $table->enum('intent', ['inquiry', 'order', 'reservation', 'complaint', 'feedback', 'general'])->default('inquiry');

            // المحتوى
            $table->json('messages')->nullable(); // جميع الرسائل
            $table->json('context')->nullable(); // سياق المحادثة
            $table->json('entities')->nullable(); // الكيانات المستخرجة

            // التتبع
            $table->integer('message_count')->default(0);
            $table->integer('token_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('last_message_at')->nullable();

            // النتائج
            $table->json('outcome')->nullable(); // {type: 'order_placed', order_id: 123}
            $table->boolean('was_successful')->default(false);
            $table->integer('customer_satisfaction')->nullable(); // 1-5

            // بيانات تقنية
            $table->json('metadata')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();

            $table->index(['restaurant_id', 'customer_phone']);
            $table->index(['channel', 'status', 'created_at']);
            $table->index('last_message_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};

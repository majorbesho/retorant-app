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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('restaurant_id')->nullable()->constrained('restaurants')->nullOnDelete();

            // نوع الإشعار
            $table->enum('type', [
                'order_placed',
                'order_confirmed',
                'order_ready',
                'order_out_for_delivery',
                'order_delivered',
                'reservation_made',
                'reservation_confirmed',
                'ai_escalation',
                'payment_received',
                'subscription_expiring',
                'subscription_expired',
                'system_alert',
                'general_notification',
            ])->default('general_notification');

            // المحتوى
            $table->string('title');
            $table->json('title_translations')->nullable();
            $table->text('message');
            $table->json('message_translations')->nullable();

            // البيانات والعمل
            $table->string('action_url')->nullable();
            $table->enum('action_type', ['url', 'order', 'reservation', 'conversation'])->nullable();
            $table->json('data')->nullable()->comment('بيانات إضافية للإشعار');

            // الحالة
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            // الخيارات
            $table->boolean('send_email')->default(true);
            $table->boolean('send_push')->default(true);
            $table->boolean('send_sms')->default(false);
            $table->string('email_sent_at')->nullable();
            $table->string('push_sent_at')->nullable();

            // الأولوية
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');

            // التفاعل
            $table->integer('click_count')->default(0);
            $table->timestamp('last_clicked_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'is_read', 'created_at']);
            $table->index(['restaurant_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

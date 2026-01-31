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
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();

            // نوع الحدث
            $table->enum('event_type', [
                'conversation_started',
                'conversation_ended',
                'conversation_escalated',
                'order_placed',
                'order_confirmed',
                'order_delivered',
                'reservation_made',
                'reservation_confirmed',
                'reservation_completed',
                'ai_escalation',
                'human_agent_involved',
                'customer_feedback',
                'payment_received',
                'user_signup',
                'subscription_created',
                'subscription_upgraded',
                'subscription_downgraded',
                'subscription_canceled',
            ]);

            // البيانات والسياق
            $table->json('event_data')->nullable();
            $table->unsignedBigInteger('ai_agent_id')->nullable();
            $table->unsignedBigInteger('conversation_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('reservation_id')->nullable();

            // معلومات المستخدم
            $table->string('customer_identifier')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // معلومات الطلب
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();

            // القيم والمقاييس
            $table->decimal('value', 10, 2)->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->index(['restaurant_id', 'event_type', 'created_at']);
            $table->index(['ai_agent_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['event_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};

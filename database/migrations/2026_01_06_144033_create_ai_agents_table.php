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
        Schema::create('ai_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['whatsapp', 'voice', 'web_chat', 'phone']);
            $table->enum('status', ['active', 'inactive', 'training', 'maintenance'])->default('active');

            // الإعدادات
            $table->json('settings')->nullable(); // جميع إعدادات الوكيل
            $table->json('greeting_message')->nullable();
            $table->json('fallback_message')->nullable();
            $table->json('working_hours')->nullable();

            // الذكاء الاصطناعي
            $table->string('ai_provider')->default('openai'); // openai, anthropic, gemini
            $table->string('ai_model')->default('gpt-4');
            $table->json('ai_config')->nullable();
            $table->decimal('temperature', 3, 2)->default(0.7);

            // الصوت
            $table->string('voice_provider')->default('elevenlabs');
            $table->string('voice_id')->nullable();
            $table->json('voice_settings')->nullable();

            // التوكنات والاستخدام
            $table->integer('total_tokens_used')->default(0);
            $table->integer('total_calls')->default(0);
            $table->timestamp('last_active_at')->nullable();

            // الإحصائيات
            $table->json('conversation_stats')->nullable(); // {total: 100, resolved: 85, transferred: 15}
            $table->json('performance_metrics')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['restaurant_id', 'type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a_i_agents');
    }
};

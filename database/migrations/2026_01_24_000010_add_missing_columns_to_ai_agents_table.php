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
        Schema::table('ai_agents', function (Blueprint $table) {
            // إضافة الأعمدة الناقصة
            if (!Schema::hasColumn('ai_agents', 'channel_type')) {
                $table->enum('channel_type', ['whatsapp', 'web_chat', 'phone', 'social_media'])->default('web_chat')->after('type');
            }

            if (!Schema::hasColumn('ai_agents', 'voice_language')) {
                $table->string('voice_language')->default('ar')->after('voice_settings');
            }

            if (!Schema::hasColumn('ai_agents', 'voice_gender')) {
                $table->string('voice_gender')->nullable()->after('voice_language');
            }

            if (!Schema::hasColumn('ai_agents', 'speaking_rate')) {
                $table->decimal('speaking_rate', 3, 2)->default(1)->after('voice_gender');
            }

            if (!Schema::hasColumn('ai_agents', 'pitch')) {
                $table->decimal('pitch', 3, 2)->default(1)->after('speaking_rate');
            }

            if (!Schema::hasColumn('ai_agents', 'system_prompt')) {
                $table->text('system_prompt')->nullable()->after('temperature');
            }

            if (!Schema::hasColumn('ai_agents', 'custom_instructions')) {
                $table->json('custom_instructions')->nullable()->after('system_prompt');
            }

            if (!Schema::hasColumn('ai_agents', 'personality')) {
                $table->string('personality')->nullable()->after('custom_instructions');
            }

            if (!Schema::hasColumn('ai_agents', 'response_tone')) {
                $table->string('response_tone')->default('friendly')->after('personality');
            }

            if (!Schema::hasColumn('ai_agents', 'max_response_length')) {
                $table->integer('max_response_length')->default(500)->after('response_tone');
            }

            if (!Schema::hasColumn('ai_agents', 'enable_context_learning')) {
                $table->boolean('enable_context_learning')->default(true)->after('max_response_length');
            }

            if (!Schema::hasColumn('ai_agents', 'enable_sentiment_analysis')) {
                $table->boolean('enable_sentiment_analysis')->default(true)->after('enable_context_learning');
            }

            if (!Schema::hasColumn('ai_agents', 'enable_intent_detection')) {
                $table->boolean('enable_intent_detection')->default(true)->after('enable_sentiment_analysis');
            }

            if (!Schema::hasColumn('ai_agents', 'enable_auto_escalation')) {
                $table->boolean('enable_auto_escalation')->default(true)->after('enable_intent_detection');
            }

            if (!Schema::hasColumn('ai_agents', 'escalation_keywords')) {
                $table->json('escalation_keywords')->nullable()->after('enable_auto_escalation');
            }

            if (!Schema::hasColumn('ai_agents', 'language_detection')) {
                $table->string('language_detection')->default('auto')->after('escalation_keywords');
            }

            if (!Schema::hasColumn('ai_agents', 'enable_translation')) {
                $table->boolean('enable_translation')->default(true)->after('language_detection');
            }

            if (!Schema::hasColumn('ai_agents', 'escalation_message')) {
                $table->json('escalation_message')->nullable()->after('enable_translation');
            }

            if (!Schema::hasColumn('ai_agents', 'error_message')) {
                $table->json('error_message')->nullable()->after('escalation_message');
            }

            if (!Schema::hasColumn('ai_agents', 'enable_outside_hours_message')) {
                $table->boolean('enable_outside_hours_message')->default(false)->after('error_message');
            }

            if (!Schema::hasColumn('ai_agents', 'outside_hours_message')) {
                $table->json('outside_hours_message')->nullable()->after('enable_outside_hours_message');
            }

            if (!Schema::hasColumn('ai_agents', 'auto_reply_enabled')) {
                $table->boolean('auto_reply_enabled')->default(false)->after('outside_hours_message');
            }

            if (!Schema::hasColumn('ai_agents', 'auto_reply_message')) {
                $table->json('auto_reply_message')->nullable()->after('auto_reply_enabled');
            }

            if (!Schema::hasColumn('ai_agents', 'rate_limit_per_minute')) {
                $table->integer('rate_limit_per_minute')->default(10)->after('auto_reply_message');
            }

            if (!Schema::hasColumn('ai_agents', 'rate_limit_per_hour')) {
                $table->integer('rate_limit_per_hour')->default(100)->after('rate_limit_per_minute');
            }

            if (!Schema::hasColumn('ai_agents', 'webhook_url')) {
                $table->string('webhook_url')->nullable()->after('rate_limit_per_hour');
            }

            if (!Schema::hasColumn('ai_agents', 'webhook_secret')) {
                $table->string('webhook_secret')->nullable()->after('webhook_url');
            }

            if (!Schema::hasColumn('ai_agents', 'enable_webhook_logs')) {
                $table->boolean('enable_webhook_logs')->default(false)->after('webhook_secret');
            }

            if (!Schema::hasColumn('ai_agents', 'total_conversations')) {
                $table->integer('total_conversations')->default(0)->after('last_active_at');
            }

            if (!Schema::hasColumn('ai_agents', 'average_response_time')) {
                $table->decimal('average_response_time', 8, 2)->default(0)->after('performance_metrics');
            }

            if (!Schema::hasColumn('ai_agents', 'resolution_rate')) {
                $table->decimal('resolution_rate', 5, 2)->default(0)->after('average_response_time');
            }

            if (!Schema::hasColumn('ai_agents', 'escalation_rate')) {
                $table->decimal('escalation_rate', 5, 2)->default(0)->after('resolution_rate');
            }

            if (!Schema::hasColumn('ai_agents', 'customer_satisfaction_score')) {
                $table->decimal('customer_satisfaction_score', 3, 2)->default(0)->after('escalation_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_agents', function (Blueprint $table) {
            $columns = [
                'channel_type',
                'voice_language',
                'voice_gender',
                'speaking_rate',
                'pitch',
                'system_prompt',
                'custom_instructions',
                'personality',
                'response_tone',
                'max_response_length',
                'enable_context_learning',
                'enable_sentiment_analysis',
                'enable_intent_detection',
                'enable_auto_escalation',
                'escalation_keywords',
                'language_detection',
                'enable_translation',
                'escalation_message',
                'error_message',
                'enable_outside_hours_message',
                'outside_hours_message',
                'auto_reply_enabled',
                'auto_reply_message',
                'rate_limit_per_minute',
                'rate_limit_per_hour',
                'webhook_url',
                'webhook_secret',
                'enable_webhook_logs',
                'total_conversations',
                'average_response_time',
                'resolution_rate',
                'escalation_rate',
                'customer_satisfaction_score',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('ai_agents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

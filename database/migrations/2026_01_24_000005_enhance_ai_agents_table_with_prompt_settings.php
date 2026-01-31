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
            // إعدادات الـ Prompt والسلوك
            $table->text('system_prompt')->nullable()->after('working_hours')->comment('نص النظام الموسع للـ AI');
            $table->json('custom_instructions')->nullable()->comment('تعليمات مخصصة إضافية');
            $table->enum('personality', ['friendly', 'professional', 'casual', 'formal'])->default('professional')->comment('شخصية الـ Agent');

            // إعدادات اللغة والنبرة
            $table->enum('response_tone', ['ar', 'en', 'bilingual'])->default('ar')->comment('لغة وطريقة الرد');
            $table->integer('max_response_length')->default(500)->comment('الحد الأقصى لعدد أحرف الرد');

            // الإعدادات المتقدمة للـ AI
            $table->boolean('enable_context_learning')->default(true)->comment('تفعيل التعلم من السياق');
            $table->boolean('enable_sentiment_analysis')->default(true)->comment('تحليل المشاعر');
            $table->boolean('enable_intent_detection')->default(true)->comment('كشف النية');
            $table->boolean('enable_auto_escalation')->default(true)->comment('تصعيد تلقائي عند الحاجة');
            $table->json('escalation_keywords')->nullable()->comment('كلمات تحتاج إلى تصعيد فوري');

            // كشف اللغة والترجمة
            $table->enum('language_detection', ['auto', 'ar', 'en', 'bilingual'])->default('auto');
            $table->boolean('enable_translation')->default(true)->comment('تفعيل الترجمة التلقائية');

            // الرسائل الخاصة
            $table->json('escalation_message')->nullable()->comment('رسالة التصعيد عند تحويل للعامل البشري');
            $table->json('error_message')->nullable()->comment('رسالة الخطأ عند فشل النظام');

            // الصوت والفيديو
            $table->string('voice_language')->default('ar')->after('voice_settings');
            $table->enum('voice_gender', ['male', 'female', 'neutral'])->default('male')->after('voice_language');
            $table->decimal('speaking_rate', 3, 2)->default(1.0)->comment('سرعة الكلام (0.5 - 2.0)');
            $table->decimal('pitch', 3, 2)->default(1.0)->comment('طبقة الصوت (0.5 - 2.0)');

            // ساعات العمل والرسائل التلقائية
            $table->boolean('enable_outside_hours_message')->default(true);
            $table->json('outside_hours_message')->nullable()->comment('رسالة خارج ساعات العمل');
            $table->boolean('auto_reply_enabled')->default(false);
            $table->json('auto_reply_message')->nullable();

            // معدلات التحديد (Rate Limiting)
            $table->integer('rate_limit_per_minute')->default(10);
            $table->integer('rate_limit_per_hour')->default(100);

            // التكامل والـ Webhooks
            $table->string('webhook_url')->nullable()->comment('رابط الـ webhook لإرسال الإشعارات');
            $table->string('webhook_secret')->nullable()->comment('مفتاح سري للتحقق من الـ webhook');
            $table->boolean('enable_webhook_logs')->default(false);

            // الأداء والإحصائيات المحسنة
            $table->integer('total_conversations')->default(0)->after('last_active_at');
            $table->decimal('average_response_time', 10, 2)->default(0)->comment('متوسط وقت الرد بالميلي ثانية');
            $table->decimal('resolution_rate', 5, 2)->default(0)->comment('نسبة الحل (%)');
            $table->decimal('escalation_rate', 5, 2)->default(0)->comment('نسبة التصعيد (%)');
            $table->decimal('customer_satisfaction_score', 3, 2)->default(0)->comment('تقييم رضا العملاء من 5');

            // لا نضيف index هنا لأنه موجود بالفعل في الـ migration الأصلية
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_agents', function (Blueprint $table) {
            // لا نحذف index لأنه من الـ migration الأصلية
            $table->dropColumn([
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
                'voice_language',
                'voice_gender',
                'speaking_rate',
                'pitch',
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
            ]);
        });
    }
};

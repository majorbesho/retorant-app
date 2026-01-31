<?php

namespace Database\Factories;

use App\Models\AIAgent;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class AIAgentFactory extends Factory
{
    protected $model = AIAgent::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => $this->faker->word . ' Agent',
            'channel_type' => $this->faker->randomElement(['whatsapp', 'web_chat', 'phone', 'social_media']),
            'type' => $this->faker->randomElement(['whatsapp', 'voice', 'web_chat', 'phone']),
            'status' => 'active',

            // الإعدادات الأساسية
            'settings' => [
                'language' => 'ar',
                'timezone' => 'Asia/Dubai',
            ],
            'greeting_message' => [
                'ar' => 'السلام عليكم ورحمة الله وبركاته! كيف أساعدك؟',
                'en' => 'Hello! How can I help you?',
            ],
            'fallback_message' => [
                'ar' => 'آسف، لم أفهم. هل يمكنك إعادة الصياغة؟',
                'en' => 'Sorry, I didn\'t understand. Can you rephrase?',
            ],
            'working_hours' => [
                'monday' => ['open' => '09:00', 'close' => '23:00'],
                'tuesday' => ['open' => '09:00', 'close' => '23:00'],
                'wednesday' => ['open' => '09:00', 'close' => '23:00'],
                'thursday' => ['open' => '09:00', 'close' => '00:00'],
                'friday' => ['open' => '09:00', 'close' => '00:00'],
                'saturday' => ['open' => '09:00', 'close' => '23:00'],
                'sunday' => ['open' => '09:00', 'close' => '23:00'],
            ],

            // إعدادات الـ AI
            'ai_provider' => 'openai',
            'ai_model' => 'gpt-4',
            'ai_config' => [
                'max_tokens' => 500,
                'frequency_penalty' => 0.2,
                'presence_penalty' => 0.2,
            ],
            'temperature' => 0.7,

            // الصوت
            'voice_provider' => null,
            'voice_id' => null,
            'voice_settings' => [],
            'voice_language' => 'ar',
            'voice_gender' => 'male',
            'speaking_rate' => 1.0,
            'pitch' => 1.0,

            // إعدادات الـ Prompt
            'system_prompt' => 'أنت مساعد ذكي للعملاء. كن مهذباً وودياً.',
            'custom_instructions' => [
                'tone' => 'friendly',
            ],
            'personality' => 'friendly',
            'response_tone' => 'ar',
            'max_response_length' => 500,

            // الميزات المتقدمة
            'enable_context_learning' => true,
            'enable_sentiment_analysis' => true,
            'enable_intent_detection' => true,
            'enable_auto_escalation' => true,
            'escalation_keywords' => ['مشكلة', 'شكوى'],
            'language_detection' => 'auto',
            'enable_translation' => true,

            // الرسائل
            'escalation_message' => [
                'ar' => 'سيتم تحويلك إلى موظف متخصص...',
                'en' => 'Transferring you to a specialist...',
            ],
            'error_message' => [
                'ar' => 'حدث خطأ. يرجى المحاولة لاحقاً.',
                'en' => 'Error occurred. Please try again.',
            ],
            'enable_outside_hours_message' => true,
            'outside_hours_message' => [
                'ar' => 'نحن مغلقون حالياً.',
                'en' => 'We are closed now.',
            ],
            'auto_reply_enabled' => false,
            'auto_reply_message' => [
                'ar' => 'شكراً على رسالتك.',
                'en' => 'Thank you for your message.',
            ],

            // معدلات
            'rate_limit_per_minute' => 10,
            'rate_limit_per_hour' => 100,

            // Webhooks
            'webhook_url' => null,
            'webhook_secret' => null,
            'enable_webhook_logs' => false,

            // الإحصائيات
            'total_tokens_used' => 0,
            'total_calls' => 0,
            'last_active_at' => now(),
            'total_conversations' => 0,
            'conversation_stats' => [
                'total' => 0,
                'resolved' => 0,
                'escalated' => 0,
            ],
            'performance_metrics' => [],
            'average_response_time' => 0,
            'resolution_rate' => 0,
            'escalation_rate' => 0,
            'customer_satisfaction_score' => 0,
        ];
    }

    public function whatsapp()
    {
        return $this->state([
            'channel_type' => 'whatsapp',
            'type' => 'whatsapp',
            'voice_provider' => null,
        ]);
    }

    public function webChat()
    {
        return $this->state([
            'channel_type' => 'web_chat',
            'type' => 'web_chat',
            'voice_provider' => null,
        ]);
    }

    public function phone()
    {
        return $this->state([
            'channel_type' => 'phone',
            'type' => 'phone',
            'voice_provider' => 'elevenlabs',
        ]);
    }

    public function highPerformance()
    {
        return $this->state([
            'resolution_rate' => 95,
            'escalation_rate' => 5,
            'customer_satisfaction_score' => 4.8,
            'total_conversations' => 5000,
        ]);
    }
}

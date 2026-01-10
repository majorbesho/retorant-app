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
        Schema::create('users', function (Blueprint $table) {
            // الأساسيات
            $table->id();
            $table->string('name');
            $table->json('name_translations')->nullable(); // دعم تعدد اللغات
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable()->unique(); // للتواصل عبر واتساب/رسائل
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('profile_image')->nullable();

            // التفضيلات واللغات
            $table->string('locale', 10)->default('ar'); // ar, en
            $table->string('timezone')->default('Asia/Dubai');
            $table->json('notification_preferences')->nullable(); // email, sms, whatsapp, push

            // العلاقة مع المطاعم (Multi-tenancy)
            $table->foreignId('restaurant_id')->nullable();
            $table->boolean('is_super_admin')->default(false); // مدير النظام (يملك كل المطاعم)
            $table->json('restaurant_access')->nullable(); // قائمة IDs للمطاعم المسموح الوصول لها (للمسؤولين المتعددي المطاعم)

            // حالة المستخدم
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['active', 'suspended', 'pending'])->default('active');
            $table->timestamp('last_login_at')->nullable();
            $table->ipAddress('last_login_ip')->nullable();

            // المراجع الخارجية للتكامل (مثل stripe للفواتير)
            $table->string('stripe_customer_id')->nullable()->index();
            $table->string('stripe_subscription_id')->nullable();
            // البيانات الوصفية
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // للحذف المرن

            // فهارس إضافية
            $table->index(['restaurant_id', 'status']);
            $table->index('last_login_at');
        });

        // جدول خاص بالمصادقة الاجتماعية (Social Logins)
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider'); // google, apple, facebook
            $table->string('provider_id'); // المعرف من مزود الخدمة
            $table->json('provider_data')->nullable(); // البيانات الإضافية من المزود
            $table->timestamps();

            $table->unique(['provider', 'provider_id']);
        });

        // جدول جلسات المستخدم المتقدمة (للتتبع والإدارة)
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // session_id
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('device_type')->nullable(); // web, mobile, tablet
            $table->string('platform')->nullable(); // windows, ios, android
            $table->string('browser')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('location')->nullable(); // Country/City من IP
            $table->boolean('is_current')->default(false); // هل هذه الجلسة نشطة الآن؟
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();
        });

        // جدول توكنات إعادة تعيين كلمة المرور (بديل عن password_reset_tokens الافتراضي)
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('token');
            $table->string('type')->default('email')->nullable(); // email, sms
            $table->timestamp('created_at')->nullable();
            $table->timestamp('expires_at')->nullable();
        });

        // جدول إشعارات المستخدم (لتخزين الإشعارات المرسلة)
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // order_confirmation, reservation_reminder, etc.
            $table->string('channel'); // email, sms, whatsapp, push, in_app
            $table->string('subject')->nullable();
            $table->text('message');
            $table->json('data')->nullable(); // بيانات إضافية للقالب
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->json('delivery_status')->nullable(); // نجاح/فشل + تفاصيل
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists('users');
    }
};

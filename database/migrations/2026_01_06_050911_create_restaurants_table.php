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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // للتعريف العام الآمن
            $table->string('name');
            $table->json('name_translations'); // متعدد اللغات
            $table->string('slug')->unique(); // للـ SEO
            $table->text('description')->nullable();
            $table->json('description_translations')->nullable();
            $table->enum('type', ['restaurant', 'cafe', 'fast_food', 'fine_dining', 'buffet']);
            $table->string('cuisine_type'); // عربي، هندي، إيطالي
            $table->json('cuisine_tags'); // [halal, vegetarian, vegan, gluten_free]
            $table->string('phone')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // الموقع
            $table->string('address')->nullable();
            $table->json('address_translations')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('city'); // دبي، أبوظبي، الشارقة
            $table->string('area'); // داون تاون، ديرة، الخ...

            // وسائل التواصل الاجتماعي
            $table->json('social_links')->nullable(); // {facebook: url, instagram: url}

            // الخصائص
            $table->json('features')->nullable(); // {delivery: true, pickup: true, reservation: true}
            $table->json('payment_methods')->nullable(); // [cash, card, apple_pay, stc_pay]

            // الوسائط
            $table->string('logo')->nullable();
            $table->json('gallery')->nullable(); // [image1, image2, ...]
            $table->string('cover_image')->nullable();

            // الإعدادات
            $table->json('settings')->nullable(); // جميع الإعدادات
            $table->json('working_hours')->nullable(); // {monday: {open: 09:00, close: 23:00}}
            $table->json('holidays')->nullable(); // تواريخ الإجازات
            $table->integer('preparation_time')->default(30); // وقت التحضير بالدقائق

            // التقييمات والإحصائيات
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('order_count')->default(0);

            // الحالة
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->enum('status', ['active', 'suspended', 'closed', 'maintenance'])->default('active');

            // الفوترة
            $table->string('stripe_account_id')->nullable(); // لـ Connect
            $table->json('subscription_details')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index(['city', 'status', 'is_active']);
            $table->index(['cuisine_type', 'rating']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};

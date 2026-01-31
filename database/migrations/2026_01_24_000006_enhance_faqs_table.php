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
        Schema::table('faqs', function (Blueprint $table) {
            // إضافة الأعمدة الناقصة إذا لم تكن موجودة
            if (!Schema::hasColumn('faqs', 'uuid')) {
                $table->uuid('uuid')->unique()->after('id');
            }
            if (!Schema::hasColumn('faqs', 'category')) {
                $table->enum('category', ['order', 'reservation', 'payment', 'menu', 'delivery', 'account', 'general', 'technical'])->default('general')->after('restaurant_id');
            }
            if (!Schema::hasColumn('faqs', 'keywords')) {
                $table->json('keywords')->nullable()->comment('كلمات للبحث والتطابق')->after('answer_translations');
            }
            if (!Schema::hasColumn('faqs', 'priority')) {
                $table->enum('priority', ['high', 'medium', 'low'])->default('medium')->after('keywords');
            }
            if (!Schema::hasColumn('faqs', 'display_order')) {
                $table->integer('display_order')->default(0)->after('priority');
            }
            if (!Schema::hasColumn('faqs', 'ai_context')) {
                $table->boolean('ai_context')->default(true)->comment('هل تُستخدم لتدريب الـ AI')->after('display_order');
            }
            if (!Schema::hasColumn('faqs', 'usage_count')) {
                $table->integer('usage_count')->default(0)->comment('عدد مرات استخدامها')->after('ai_context');
            }
            if (!Schema::hasColumn('faqs', 'last_used_at')) {
                $table->timestamp('last_used_at')->nullable()->after('usage_count');
            }
            if (!Schema::hasColumn('faqs', 'helpful_count')) {
                $table->integer('helpful_count')->default(0)->comment('عدد مرات تقييمها بـ مفيد')->after('last_used_at');
            }
            if (!Schema::hasColumn('faqs', 'not_helpful_count')) {
                $table->integer('not_helpful_count')->default(0)->after('helpful_count');
            }
            if (!Schema::hasColumn('faqs', 'helpfulness_score')) {
                $table->decimal('helpfulness_score', 3, 2)->default(0)->after('not_helpful_count');
            }
            if (!Schema::hasColumn('faqs', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->comment('ملاحظات داخلية للموظفين')->after('helpfulness_score');
            }
            if (!Schema::hasColumn('faqs', 'related_faqs')) {
                $table->json('related_faqs')->nullable()->comment('أسئلة شائعة ذات صلة')->after('internal_notes');
            }
            if (!Schema::hasColumn('faqs', 'tags')) {
                $table->json('tags')->nullable()->after('related_faqs');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            // لن نحذف الأعمدة - نتركها للأمان
        });
    }
};

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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();

            // بيانات المراجع
            $table->string('reviewer_name');
            $table->string('reviewer_email')->nullable();
            $table->string('reviewer_phone')->nullable();

            // التقييم
            $table->integer('rating'); // 1-5
            $table->json('aspect_ratings')->nullable(); // {food: 5, service: 4, ambiance: 3}
            $table->text('comment');
            $table->json('comment_translations')->nullable();

            // الوسائط
            $table->json('images')->nullable();
            $table->json('videos')->nullable();

            // الحالة
            $table->boolean('is_verified')->default(false); // عميل حقيقي
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_helpful')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->integer('report_count')->default(0);

            // رد المطعم
            $table->text('owner_response')->nullable();
            $table->timestamp('responded_at')->nullable();

            // الإعدادات
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_public')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['restaurant_id', 'rating', 'created_at']);
            $table->index(['is_verified', 'is_approved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

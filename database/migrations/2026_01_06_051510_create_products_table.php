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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->uuid('sku')->unique(); // Stock Keeping Unit
            $table->string('name');
            $table->json('name_translations');
            $table->text('description')->nullable();
            $table->json('description_translations')->nullable();
            $table->text('ingredients')->nullable();
            $table->json('ingredients_translations')->nullable();
            $table->json('allergens')->nullable(); // [nuts, gluten, dairy]
            $table->json('nutritional_info')->nullable(); // {calories: 350, protein: 20g}

            // التسعير
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable(); // سعر التكلفة
            $table->enum('discount_type', ['percentage', 'fixed', 'none'])->default('none');
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->timestamp('discount_start')->nullable();
            $table->timestamp('discount_end')->nullable();

            // المخزون
            $table->integer('stock_quantity')->default(-1); // -1 تعني غير محدود
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'pre_order', 'limited'])->default('in_stock');
            $table->integer('low_stock_threshold')->default(10);

            // الوسائط
            $table->json('images')->nullable(); // صور متعددة
            $table->string('video_url')->nullable(); // فيديو توضيحي

            // الخصائص
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_vegan')->default(false);
            $table->boolean('is_gluten_free')->default(false);
            $table->boolean('is_halal')->default(true);
            $table->boolean('has_spices')->default(false);
            $table->enum('spice_level', ['mild', 'medium', 'hot', 'extra_hot'])->nullable();

            // التخصيصات
            $table->boolean('has_variations')->default(false);
            $table->boolean('has_addons')->default(false);
            $table->json('customization_options')->nullable(); // خيارات مخصصة

            // الإحصائيات
            $table->integer('sales_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0);

            // الحالة
            $table->boolean('is_active')->default(true);
            $table->boolean('is_available')->default(true);
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_to')->nullable();
            $table->json('availability_schedule')->nullable(); // جدول التوفر

            $table->timestamps();
            $table->softDeletes();

            $table->index(['restaurant_id', 'category_id', 'is_active']);
            $table->index(['is_featured', 'is_recommended']);
            $table->index(['price', 'discount_price']);
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

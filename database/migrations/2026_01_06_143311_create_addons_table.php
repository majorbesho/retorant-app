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
        Schema::create('addon_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // الصلصات، الإضافات، المشروبات الجانبية
            $table->json('name_translations');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(false);
            $table->integer('min_selections')->default(0);
            $table->integer('max_selections')->default(99);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('addon_group_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // كاتشب، مايونيز، جبن إضافي
            $table->json('name_translations');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity')->default(-1);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('nutritional_info')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('product_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('addon_group_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);

            $table->unique(['product_id', 'addon_group_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_addons');
        Schema::dropIfExists('addons');
        Schema::dropIfExists('addon_groups');
    }
};
